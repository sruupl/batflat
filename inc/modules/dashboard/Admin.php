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

namespace Inc\Modules\Dashboard;

use Inc\Core\AdminModule;

class Admin extends AdminModule
{
    public function navigation()
    {
        return [
            'Main' => 'main'
        ];
    }

    public function getMain()
    {
        $this->core->addCSS(url(MODULES.'/dashboard/css/style.css?v={$bat.version}'));
        return $this->draw('dashboard.html', ['modules' => $this->_modulesList(), 'news' => $this->_fetchNews()]);
    }

    private function _modulesList()
    {
        $modules = array_column($this->db('modules')->toArray(), 'dir');
        $result = [];

        if ($this->core->getUserInfo('access') != 'all') {
            $modules = array_intersect($modules, explode(',', $this->core->getUserInfo('access')));
        }

        foreach ($modules as $name) {
            $files = [
                'info'  => MODULES.'/'.$name.'/Info.php',
                'admin' => MODULES.'/'.$name.'/Admin.php',
            ];

            if (file_exists($files['info']) && file_exists($files['admin'])) {
                $details        = $this->core->getModuleInfo($name);
                $features       = $this->core->getModuleNav($name);

                if (empty($features)) {
                    continue;
                }

                $details['url'] = url([ADMIN, $name, array_shift($features)]);

                $result[] = $details;
            }
        }
        return $result;
    }

    private function _fetchNews()
    {
        \libxml_use_internal_errors(true);
        $assign = [];
        $lang = $this->settings('settings.lang_admin');
        if (!in_array($lang, ['en_english', 'pl_polski'])) {
            $lang = 'en_english';
        }

        $xml = \Inc\Core\Lib\HttpRequest::get('https://batflat.org/blog/feed/'.$lang);
        if (!empty($xml) && ($rss = simplexml_load_string($xml))) {
            $counter = 0;
            foreach ($rss->channel->item as $item) {
                if ($counter >= 3) {
                    break;
                }
                
                $assign[$counter]['title'] = (string) $item->title;
                $assign[$counter]['link'] = (string) $item->link;
                $assign[$counter]['desc'] = (string) $item->description;
                $assign[$counter]['date'] = date('Y-m-d', strtotime($item->pubDate));
                if (isset($item->image)) {
                    $assign[$counter]['image'] = (string) $item->image->url;
                }

                $counter++;
            }
        } else {
            $assign[] = [
                'title' => $this->lang('rss_fail_title'),
                'link' => '#',
                'desc' => $this->lang('rss_fail_desc'),
                'date' => null
            ];
        }

        return $assign;
    }
}
