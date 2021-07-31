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

namespace Inc\Modules\Navigation;

use Inc\Core\SiteModule;

class Site extends SiteModule
{
    public function routes()
    {
        $this->_insertMenu();
    }

    /**
    * get nav data
    */
    private function _insertMenu()
    {
        $assign = [];
        $homepage = $this->settings('settings', 'homepage');

        $lang_prefix = $this->core->lang['name'];

        if ($lang_prefix != $this->settings('settings', 'lang_site')) {
            $lang_prefix = explode('_', $lang_prefix)[0];
        } else {
            $lang_prefix = null;
        }

        // get nav
        $navs = $this->db('navs')->toArray();
        foreach ($navs as $nav) {
            // get nav children
            $items = $this->db('navs_items')->leftJoin('pages', 'pages.id = navs_items.page')->where('navs_items.nav', $nav['id'])->where('navs_items.lang', $this->core->lang['name'])->asc('`order`')->select(['navs_items.*', 'pages.slug'])->toArray();

            if (count($items)) {
                // generate URL
                foreach ($items as &$item) {
                    // if external URL field is empty, it means that it's a batflat page
                    $item['active'] = null;
                    if (!$item['url']) {
                        if ($item['slug'] == $homepage) {
                            $item['url'] = $lang_prefix ? url([$lang_prefix]) : url('');
                        } else {
                            $item['url'] = $lang_prefix ? url([$lang_prefix, $item['slug']]) : url([$item['slug']]);
                        }

                        $url = parseURL();
                        if ($url[0] == $item['slug'] || (preg_match('/^[a-z]{2}$/', $url[0]) && isset_or($url[1], $homepage) == $item['slug']) || $this->_isChildActive($item['id'], $url[0]) || ($url[0] == null && $homepage == $item['slug'])) {
                            $item['active'] = 'active';
                        }
                    } else {
                        $item['url'] = url($item['url']);
                        $page = ['slug' => null];

                        if (url(parseURL(1)) == $item['url'] || $this->_isChildActive($item['id'], parseURL(1)) || (parseURL(1) == null && url($homepage) == $item['url'])) {
                            $item['active'] = 'active';
                        }

                        if ($item['url'] == url($homepage)) {
                            $item['url'] = url('');
                        }
                    }
                }

                $navigation_admin = new Admin($this->core);
                $assign[$nav['name']] = $this->draw('nav.html', ['navigation' => ['list' => $navigation_admin->buildTree($items)]]);
            } else {
                $assign[$nav['name']] = null;
            }
        }

        $this->tpl->set('navigation', $assign);
    }

    /**
    * check if parent's child is active
    */
    private function _isChildActive($itemID, $slug)
    {
        $rows = $this->db('pages')
                ->leftJoin('navs_items', 'pages.id = navs_items.page')
                ->where('navs_items.parent', $itemID)
                ->toArray();

        if (count($rows)) {
            foreach ($rows as $row) {
                if ($slug == $row['slug']) {
                    return true;
                }
            }
        }

        return false;
    }
}
