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

namespace Inc\Core;

/**
 * Core Site class
 */
class Site extends Main
{
    /**
     * Current site template file
     * Does not use template if set to false
     *
     * @var mixed
     */
    public $template = 'index.html';

    /**
     * Site constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadLanguage();
        $this->loadModules();

        $return = $this->router->execute();

        if (is_string($this->template)) {
            $this->drawTheme($this->template);
        } elseif ($this->template === false) {
            if (strpos(get_headers_list('Content-Type'), 'text/html') !== false) {
                header("Content-type: text/plain");
            }

            echo $return;
        }

        $this->module->finishLoop();
    }

    /**
    * set variables to template core and display them
    * @param string $file
    * @return void
    */
    private function drawTheme($file)
    {
        $assign = [];
        $assign['notify']   = $this->getNotify();
        $assign['powered']  = 'Powered by <a href="https://batflat.org/">Batflat</a>';
        $assign['path']     = url();
        $assign['theme']    = url(THEMES.'/'.$this->settings->get('settings.theme'));
        $assign['lang']     = $this->lang['name'];

        $assign['header']   = isset_or($this->appends['header'], ['']);
        $assign['footer']   = isset_or($this->appends['footer'], ['']);

        $this->tpl->set('bat', $assign);
        echo $this->tpl->draw(THEMES.'/'.$this->settings->get('settings.theme').'/'.$file, true);
    }

    /**
    * load files with language
    * @param string $lang
    * @return void
    */
    public function loadLanguage($lang = null)
    {
        $this->lang = [];

        if ($lang != null && is_dir('inc/lang/'.$lang)) {
            $_SESSION['lang'] = $lang;
        }

        if (!isset($_SESSION['lang']) || !is_dir('inc/lang/'.$_SESSION['lang'])) {
            $this->lang['name'] = $this->settings->get('settings.lang_site');
            $_SESSION['lang'] = $this->lang['name'];
        } else {
            $this->lang['name'] = $_SESSION['lang'];
        }
        

        foreach (glob(MODULES.'/*/lang/'.$this->lang['name'].'.ini') as $file) {
            $base = str_replace($this->lang['name'], 'en_english', $file);
            $module = str_replace([MODULES.'/', '/lang/'.$this->lang['name'].'.ini'], null, $file);
            $this->lang[$module] = array_merge(parse_ini_file($base), parse_ini_file($file));
        }
        foreach (glob('inc/lang/'.$this->lang['name'].'/*.ini') as $file) {
            $base = str_replace($this->lang['name'], 'en_english', $file);
            $pathInfo = pathinfo($file);
            $this->lang[$pathInfo['filename']] = array_merge(parse_ini_file($base), parse_ini_file($file));
        }

        $this->tpl->set('lang', $this->lang);
    }

    /**
    * check if user is login
    * @return bool
    */
    public function loginCheck()
    {
        if (isset($_SESSION['bat_user']) && isset($_SESSION['token']) && isset($_SESSION['userAgent']) && isset($_SESSION['IPaddress'])) {
            if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR']) {
                return false;
            }
            if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}
