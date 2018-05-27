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

use Inc\Core\AdminModule;

class Admin extends AdminModule
{
    public function navigation()
    {
        return [
            $this->lang('settings', 'general') => 'settings',
        ];
    }

    public function getSettings()
    {
        $value = $this->settings('contact');

        if (is_numeric($value['email'])) {
            $assign['users'] = $this->_getUsers($value['email']);
            $assign['email'] = null;
        } else {
            $assign['users'] = $this->_getUsers();
            $assign['email'] = $value['email'];
        }
        
        $assign['checkbox'] = [
            'switch' => $value['checkbox.switch'],
            'content' => $this->tpl->noParse($value['checkbox.content']),
        ];

        $assign['driver'] = $value['driver'];
        $assign['phpmailer'] = [
            'server' => $value['phpmailer.server'],
            'port' => $value['phpmailer.port'],
            'username' => $value['phpmailer.username'],
            'password' => $value['phpmailer.password'],
            'name' => $value['phpmailer.name'],
        ];

        return $this->draw('settings.html', ['contact' => $assign]);
    }

    public function postSave()
    {
        $update = [
            'email' => ($_POST['user'] > 0 ? $_POST['user'] : $_POST['email']),
            'checkbox.switch' => $_POST['checkbox']['switch'],
            'checkbox.content' => $_POST['checkbox']['content'],
            'driver' => $_POST['driver'],
            'phpmailer.server' => $_POST['phpmailer']['server'],
            'phpmailer.port' => $_POST['phpmailer']['port'],
            'phpmailer.username' => $_POST['phpmailer']['username'],
            'phpmailer.password' => $_POST['phpmailer']['password'],
            'phpmailer.name' => $_POST['phpmailer']['name'],
        ];

        $errors = 0;
        foreach ($update as $field => $value) {
            if (!$this->db('settings')->where('module', 'contact')->where('field', $field)->save(['value' => $value])) {
                $errors++;
            }
        }

        if (!$errors) {
            $this->notify('success', $this->lang('save_success'));
        } else {
            $this->notify('failure', $this->lang('save_failure'));
        }

        redirect(url([ADMIN, 'contact', 'settings']));
    }

    private function _getUsers($id = null)
    {
        $rows = $this->db('users')->where('role', 'admin')->toArray();
        if (count($rows)) {
            foreach ($rows as $row) {
                if ($id == $row['id']) {
                    $attr = 'selected';
                } else {
                    $attr = null;
                }
                $result[] = $row + ['attr' => $attr];
            }
        }
        return $result;
    }
}
