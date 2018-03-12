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

namespace Inc\Modules\Carousel;

use Inc\Core\SiteModule;

class Site extends SiteModule
{
    public function init()
    {
        $this->tpl->set('carousel', $this->_insertCarousels());
    }

    private function _insertCarousels()
    {
        $assign = [];
        $tempAssign = [];
        $galleries = $this->db('galleries')->toArray();

        if (!empty($galleries)) {
            foreach ($galleries as $gallery) {
                $items = $this->db('galleries_items')->where('gallery', $gallery['id'])->toArray();
                $tempAssign = $gallery;

                if (count($items)) {
                    foreach ($items as &$item) {
                        $item['src'] = unserialize($item['src']);
                    }

                    $tempAssign['items'] = $items;

                    $assign[$gallery['slug']] = $this->draw('carousel.html', ['carousel' => $tempAssign]);
                }
            }
        }

        return $assign;
    }
}
