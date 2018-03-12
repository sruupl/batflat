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
    'name'          =>  $core->lang['modules']['module_name'],
    'description'   =>  $core->lang['modules']['module_desc'],
    'author'        =>  'Sruu.pl',
    'version'       =>  '1.1',
    'compatibility'    =>    '1.3.*',
    'icon'          =>  'plug',

    'install'       =>  function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `modules` (
                `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                `dir` text NOT NULL,
                `sequence` integer DEFAULT 0
                )");
    },
    'uninstall'     =>  function () use ($core) {
        $core->db()->pdo()->exec("DROP TABLE `modules`");
    }
];
