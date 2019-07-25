<?php
/**
* This file is part of Batflat ~ the lightweight, fast and easy CMS
*
* @author       Paweł Klockiewicz <klockiewicz@sruu.pl>
* @author       Wojciech Król <krol@sruu.pl>
* @copyright    2017 Paweł Klockiewicz, Wojciech Król <Sruu.pl>
* @license      https://batflat.org/license
* @link         https://batflat.org
*/

namespace Inc\Modules\Contact;

use Inc\Core\SiteModule;

class Site extends SiteModule
{
    private $_headers;
    private $_params;
    private $_error = null;

    private $mail = [];

    public function init()
    {
        $this->tpl->set('contact', function () {
            if (isset($_POST['send-email'])) {
                if ($this->_initDriver()) {
                    if ($this->_sendEmail()) {
                        $this->notify('success', $this->lang('send_success'));
                    } else {
                        $this->notify('failure', $this->_error);
                    }
                } else {
                    $this->notify('failure', $this->_error);
                }

                redirect(currentURL());
            }
            return ['form' => $this->_insertForm()];
        });
    }

    private function _insertForm()
    {
        return $this->draw('form.html', [
            'checkbox' => [
                'switch'    => $this->settings('contact', 'checkbox.switch'),
                'content'   => $this->settings('contact', 'checkbox.content'),
            ]
        ]);
    }

    private function _initDriver()
    {
        $settings = $this->settings('contact');

        $this->email['driver'] = $settings['driver'];

        $data = $_POST;
        htmlspecialchars_array($data);

        if ($this->_checkErrors($data)) {
            return false;
        }

        $this->email['subject'] = $data['subject'];
        $this->email['from'] = $data['from'];

        if ($settings['driver'] == 'mail') {
            $this->email['sender'] = $this->settings('settings', 'title')." <no-reply@{$_SERVER['HTTP_HOST']}>";
        } elseif ($settings['driver'] == 'phpmailer' && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $this->email['sender'] = [
                $this->settings('contact', 'phpmailer.username'),
                $this->settings('contact', 'phpmailer.name'),
            ];
        }

        if (!is_numeric($settings['email'])) {
            $this->email['to'] = $settings['email'];
        } else {
            $user = $this->db('users')->where($settings['email'])->oneArray();
            $this->email['to'] = $user['email'];
        }

        $this->email['message'] = $this->draw('mail.html', ['mail' => $data]);

        return true;
    }

    private function _checkErrors($array)
    {
        if (!filter_var($array['from'], FILTER_VALIDATE_EMAIL)) {
            $this->_error = $this->lang('wrong_email');
        }

        if (checkEmptyFields(['name', 'subject', 'from', 'message'], $array)) {
            $this->_error = $this->lang('empty_inputs');
        }

        // antibot field
        if (!empty($array['title'])) {
            exit();
        }

        if (isset($_COOKIE['MailWasSend'])) {
            $this->_error = $this->lang('antiflood');
        }

        if ($this->_error) {
            return true;
        }

        return false;
    }

    private function _sendEmail()
    {
        if ($this->email['driver'] == 'mail') {
            $headers  = "From: {$this->email['sender']}\n";
            $headers .= "Reply-To: {$this->email['from']}\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-type: text/html; charset=utf-8\n";

            if (@mail($this->email['to'], '=?UTF-8?B?'.base64_encode($this->email['subject']).'?=', $this->email['message'], $headers)) {
                // cookies antiflood
                $cookieParams = session_get_cookie_params();
                setcookie("MailWasSend", 'BATFLAT', time()+360, $cookieParams["path"], $cookieParams["domain"], null, true);
                return true;
            } else {
                $this->_error = $this->lang('send_failure');
                return false;
            }
        } elseif ($this->email['driver'] == 'phpmailer') {
            $settings = $this->settings('contact');

            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();                                            // Set mailer to use SMTP
                $mail->Host = $settings['phpmailer.server'];                // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                                     // Enable SMTP authentication
                $mail->Username = $settings['phpmailer.username'];          // SMTP username
                $mail->Password = $settings['phpmailer.password'];          // SMTP password
                $mail->SMTPSecure = 'TLS';                                  // Enable TLS encryption, `ssl` also accepted
                $mail->Port = $settings['phpmailer.port'];                  // TCP port to connect to
                $mail->CharSet = 'UTF-8';

                $mail->Subject = $this->email['subject'];
                $mail->Body = $this->email['message'];

                $mail->addReplyTo($this->email['from']);
                $mail->setFrom($this->email['sender'][0], $this->email['sender'][1]);
                $mail->addAddress($this->email['to']);

                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->isHTML(true);

                if ($mail->send()) {
                    $cookieParams = session_get_cookie_params();
                    setcookie("MailWasSend", 'BATFLAT', time()+360, $cookieParams["path"], $cookieParams["domain"], null, true);
                }
            } catch (\Exception $e) {
                $this->_error = $e->errorMessage();
            } catch (\Exception $e) {
                $this->_error = $e->getMessage();
            }

            if ($this->_error) {
                return false;
            }

            return true;
        }
    }
}
