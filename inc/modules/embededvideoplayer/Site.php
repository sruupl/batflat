<?php
/**
* BatFlat - EmbededVideoPlayer Module by pplaczek.
* Allows you to upload a video clip and play it using the html5 player.
*
* @author       Piotr Płaczek <piotr@pplaczek.pl>
* @copyright    2018 Piotr Płaczek <p.dev>
* @license      MIT https://github.com/piotr-placzek/BatFlat-EmbededVideoPlayer/blob/master/LICENSE.md
* @link         https://github.com/piotr-placzek/BatFlat-EmbededVideoPlayer
*/


namespace Inc\Modules\EmbededVideoPlayer;

use Inc\Core\SiteModule;

/**
 * EmbededVideoPlayer site class
 */
class Site extends SiteModule
{
    /**
     * Module initialization
     * Here everything is done while the module starts
     *
     * @return void
     */
    public function init()
    {
        // Add styles
        $this->core->addCss(url('inc/modules/embededvideoplayer/view/pdev_evp.css'));

        // Get db items
        $files = $this->core->db('pdev_evp')->toArray();

        // Create assigns array
        $assign = array();
        dump($files);
        foreach ($files as $file) {
            dump($file);
            $view = $this->draw('embededVideoPlayer.html', ['evp' => $file]);
            $assign[$file['slug']] = $view;
        }

        // Create user tag
        $this->tpl->set('pdev_evp', $assign);
    }
}
