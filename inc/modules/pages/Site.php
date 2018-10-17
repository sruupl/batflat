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

namespace Inc\Modules\Pages;

use Inc\Core\SiteModule;

class Site extends SiteModule
{
    public function init()
    {
        $slug = parseURL();
        $lang = $this->getLanguageBySlug($slug[0]);
        if ($lang !== false) {
            $this->core->loadLanguage($lang);
        }

        if (empty($slug[0]) || ($lang !== false && empty($slug[1]))) {
            $this->core->router->changeRoute($this->settings('settings', 'homepage'));
        }

        \Inc\Core\Lib\Event::add('router.notfound', function () {
            $this->get404();
        });
    }

    public function routes()
    {
        // Load pages from default language
        $this->route('(:str)', function ($slug) {
            $this->importPage($slug);
        });

        // Load pages from specified language prefix
        $this->route('(:str)/(:str)', function ($lang, $slug) {
            // get current language by slug
            $lang = $this->getLanguageBySlug($lang);

            // Set current language to specified or if not exists to default
            if ($lang) {
                $this->core->loadLanguage($lang);
            } else {
                $slug = null;
            }

            $this->importPage($slug);
        });

        $this->importAllPages();
    }

    /**
     * get a specific page
     */
    private function importPage($slug = null)
    {
        if (!empty($slug)) {
            $row = $this->db('pages')->where('slug', $slug)->where('lang', $this->getCurrentLang())->oneArray();

            if (empty($row)) {
                return $this->get404();
            }
        } else {
            return $this->get404();
        }

        if (intval($row['markdown'])) {
            $parsedown = new \Inc\Core\Lib\Parsedown();
            $row['content'] = $parsedown->text($row['content']);
        }

        $this->filterRecord($row);
        $this->setTemplate($row['template']);
        $this->tpl->set('page', $row);
    }

    /**
     * get array with all pages
     */
    private function importAllPages()
    {
        $this->tpl->set('pages', function () {
            $rows = $this->db('pages')->where('lang', $this->getCurrentLang())->toArray();

            $assign = [];
            foreach ($rows as $row) {
                $this->filterRecord($row);
                $assign[$row['id']] = $row;
            }

            return $assign;
        });
    }

    public function get404()
    {
        http_response_code(404);
        if (!($row = $this->db('pages')->like('slug', '404%')->where('lang', $this->getCurrentLang())->oneArray())) {
            echo '<h1>404 Not Found</h1>';
            echo $this->lang('not_found');
            exit;
        }

        $this->setTemplate($row['template']);
        $this->tpl->set('page', $row);
    }

    private function getCurrentLang()
    {
        if (!isset($_SESSION['lang'])) {
            return $this->settings('settings', 'lang_site');
        } else {
            return $_SESSION['lang'];
        }
    }

    protected function getLanguageBySlug($slug)
    {
        $langs = parent::getLanguages();
        foreach ($langs as $lang) {
            preg_match_all('/([a-z]{2})_([a-z]+)/', $lang['name'], $matches);
            if ($slug == $matches[1][0]) {
                return $matches[0][0];
            }
        }

        return false;
    }

    protected function filterRecord(array &$page)
    {
        if (isset($page['title'])) {
            $page['title'] = htmlspecialchars($page['title']);
        }
    }
}
