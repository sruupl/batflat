<?php
/**
* BatFlat - FilesToDownload Module by pplaczek.
* Allows you to add to the page or post a list of files ready to be downloaded directly from the server.
*
* @author       Piotr Płaczek <piotr@pplaczek.pl>
* @copyright    2018 Piotr Płaczek <p.dev>
* @license      MIT https://github.com/piotr-placzek/BatFlat-FilesToDownload/blob/master/LICENSE.md
* @link         https://github.com/piotr-placzek/BatFlat-FilesToDownload/
*/


namespace Inc\Modules\FilesToDownload;

use Inc\Core\SiteModule;

/**
 * FilesToDownload site class
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
        $this->core->addCss('https://use.fontawesome.com/releases/v5.3.1/css/all.css');
        $this->core->addCss(url('inc/modules/filestodownload/view/pdev_ftd.css'));

        // Get db items
        $files = $this->core->db('pdev_ftd')->toArray();

        // Create assigns array
        $assign = array();
        dump($files);
        foreach ($files as $file) {
            dump($file);
            $view = $this->draw('fileToDownload.html', ['ftd' => $file]);
            $assign[$file['slug']] = $view;
        }

        // Create user tag
        $this->tpl->set('pdev_ftd', $assign);
    }
}
