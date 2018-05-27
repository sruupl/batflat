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

namespace Inc\Modules\LangSwitcher;

use Inc\Core\SiteModule;

class Site extends SiteModule
{
    public function init()
    {
        if ($this->settings('settings', 'autodetectlang') == '1' && empty(parseURL(1)) && !isset($_SESSION['langswitcher']['detected'])) {
            $detedcted = false;
            $languages = $this->_detectBrowserLanguage();
            foreach ($languages as $value => $priority) {
                $value = substr($value, 0, 2);
                if ($detect = glob('inc/lang/'.$value.'_*')) {
                    $this->core->loadLanguage(basename($detect[0]));
                    break;
                }
            }
        }

        $_SESSION['langswitcher']['detected'] = true;
        if (isset($_GET['lang'])) {
            $lang = explode('_', $_GET['lang'])[0];
            $this->_setLanguage($_GET['lang']);

            $dir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');

            $e = parseURL();
            foreach ($this->_getLanguages() as $lng) {
                if ($lng['symbol'] == $e[0]) {
                    array_shift($e);
                    break;
                }
            }
            $slug = implode('/', $e);

            if ($this->db('pages')->where('slug', $slug)->where('lang', $_GET['lang'])->oneArray()) {
                redirect(url($lang.'/'.$slug));
            } else {
                redirect(url($slug));
            }
        }

        $this->tpl->set('langSwitcher', $this->_insertSwitcher());
    }

    private function _insertSwitcher()
    {
        return $this->draw('switcher.html', ['languages' => $this->_getLanguages()]);
    }

    protected function _getLanguages($selected = null, $attr = 'selected', $all = false)
    {
        $langs = glob('inc/lang/*', GLOB_ONLYDIR);

        $result = [];
        foreach ($langs as $lang) {
            if (!$all & file_exists($lang.'/.lock')) {
                continue;
            }
            $lang = basename($lang);
            $result[] = [
                'dir'   => $lang,
                'name'  => mb_strtoupper(preg_replace('/_[a-z]+/', null, $lang)),
                'symbol'=> preg_replace('/_[a-z]+/', null, $lang),
                'attr'  => (($selected ? $selected : $this->core->lang['name']) == $lang) ? $attr : null
            ];
        }
        return $result;
    }

    private function _setLanguage($value)
    {
        if (in_array($value, array_column($this->_getLanguages(), 'dir'))) {
            $_SESSION['lang'] = $value;
            return true;
        }
        return false;
    }

    private function _detectBrowserLanguage()
    {
        $prefLocales = array_reduce(
        explode(',', isset_or($_SERVER['HTTP_ACCEPT_LANGUAGE'], null)),
        function ($res, $el) {
            list($l, $q) = array_merge(explode(';q=', $el), [1]);
            $res[$l] = (float) $q;
            return $res;
        }, []);
        arsort($prefLocales);

        return $prefLocales;
    }
}
