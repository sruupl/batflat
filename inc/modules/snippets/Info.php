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
    'name'          => $core->lang['snippets']['module_name'],
    'description'   => $core->lang['snippets']['module_desc'],
    'author'        => 'Sruu.pl',
    'version'       => '1.2',
    'compatibility' => '1.3.*',
    'icon'          => 'puzzle-piece',
    'install'       => function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `snippets` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` text NOT NULL,
            `slug` text NOT NULL,
            `content` text NOT NULL
            )");
    },
    'uninstall'     => function () use ($core) {
        $core->db()->pdo()->exec("DROP TABLE `snippets`");
    }
];
