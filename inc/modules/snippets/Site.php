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

namespace Inc\Modules\Snippets;

use Inc\Core\SiteModule;

class Site extends SiteModule
{
    public function init()
    {
        $this->_importSnippets();
    }

    private function _importSnippets()
    {
        $rows = $this->db('snippets')->toArray();

        $snippets = [];
        foreach ($rows as $row) {
            $snippets[$row['slug']] = $row['content'];
        }

        return $this->tpl->set('snippet', $snippets);
    }
}
