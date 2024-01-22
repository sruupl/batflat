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

return [
    'name'          =>  $core->lang['embededvideoplayer']['module_name'],
    'description'   =>  $core->lang['embededvideoplayer']['module_desc'],
    'author'        =>  'p.dev',
    'version'       =>  '1.0.1',
    'compatibility'    =>    '1.3.*',                                // Compatibility with Batflat version
    'icon'          =>  'film',                                 // Icon from http://fontawesome.io/icons/

    'install'       =>  function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `pdev_evp` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` text NOT NULL,
            `slug` text NOT NULL,
            `file` text NOT NULL,
            `path` text NOT NULL
        )");

        if (!file_exists(UPLOADS.'/pdev_evp')) {
            mkdir(UPLOADS.'/pdev_evp', 0755, true);
        }
    },
    'uninstall'     =>  function () use ($core) {
        // If you uncomment line bellow then you lost all data but not files
        $core->db()->pdo()->exec("DROP TABLE `pdev_evp`");
        // If you uncomment block bellow then you lost all files but not data
        deleteDir(UPLOADS.'/pdev_evp');
    }
];
