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
    'name'          => $core->lang['navigation']['module_name'],
    'description'   => $core->lang['navigation']['module_desc'],
    'author'        => 'Sruu.pl',
    'version'       => '1.3',
    'compatibility' => '1.3.*',
    'icon'          => 'list-ul',
    'install'       => function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `navs` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` text NOT NULL
        )");
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `navs_items` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` text NOT NULL,
            `url` text NULL,
            `page` integer NULL,
            `lang` text NOT NULL,
            `parent` integer NOT NULL DEFAULT 0,
            `nav` integer NOT NULL,
            `order` integer NOT NULL,
            `class` text NULL
        )");
        $core->db()->pdo()->exec("INSERT INTO `navs` (`name`) VALUES ('main')");
        $core->db()->pdo()->exec("INSERT INTO `navs_items` (`name`, `url`, `page`, `lang`, `nav`, `order`)
            VALUES ('Home', 'blog', 0, 'en_english', 1, 1)");
        $core->db()->pdo()->exec("INSERT INTO `navs_items` (`name`, `url`, `page`, `lang`, `nav`, `order`)
            VALUES ('Strona główna', 'blog', 0, 'pl_polski', 1, 1)");
        $core->db()->pdo()->exec("INSERT INTO `navs_items` (`name`, `page`, `lang`, `nav`, `order`)
            VALUES ('About me', 1, 'en_english', 1, 2)");
        $core->db()->pdo()->exec("INSERT INTO `navs_items` (`name`, `page`, `lang`, `nav`, `order`)
            VALUES ('O mnie', 2, 'pl_polski', 1, 2)");
        $core->db()->pdo()->exec("INSERT INTO `navs_items` (`name`, `page`, `lang`, `nav`, `order`)
            VALUES ('Contact', 3, 'en_english', 1, 3)");
        $core->db()->pdo()->exec("INSERT INTO `navs_items` (`name`, `page`, `lang`, `nav`, `order`)
            VALUES ('Kontakt', 4, 'pl_polski', 1, 3)");
    },
    'uninstall'     => function () use ($core) {
        $core->db()->pdo()->exec("DROP TABLE `navs`");
        $core->db()->pdo()->exec("DROP TABLE `navs_items`");
    }
];
