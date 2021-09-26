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

return [
    'name'          => $core->lang['galleries']['module_name'],
    'description'   => $core->lang['galleries']['module_desc'],
    'author'        => 'Sruu.pl',
    'version'       => '1.1',
    'compatibility' => '1.3.*',
    'icon'          => 'camera',
    'install'       =>  function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `galleries` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` text NOT NULL,
            `slug` text NOT NULL,
            `img_per_page` integer NOT NULL DEFAULT 0,
            `sort` text NOT NULL DEFAULT 'DESC'
        )");

        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `galleries_items` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `gallery` integer NOT NULL,
            `src` text NOT NULL,
            `title` text NULL,
            `desc` text NULL
        )");

        if (!file_exists(UPLOADS.'/galleries')) {
            mkdir(UPLOADS.'/galleries', 0755, true);
        }
    },
    'uninstall'     => function () use ($core) {
        $core->db()->pdo()->exec("DROP TABLE `galleries`");
        $core->db()->pdo()->exec("DROP TABLE `galleries_items`");
        deleteDir(UPLOADS.'/galleries');
    }
];
