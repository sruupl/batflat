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
    'name'          =>  $core->lang['langswitcher']['module_name'],
    'description'   =>  $core->lang['langswitcher']['module_desc'],
    'author'        =>  'Sruu.pl',
    'version'       =>  '1.2',
    'compatibility' =>  '1.3.*',
    'icon'          =>  'flag',
    'install'       =>  function () use ($core) {
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'autodetectlang', 0)");
    },
    'uninstall'     =>  function () use ($core) {
        $core->db()->pdo()->exec("DELETE FROM `settings` WHERE `field` = 'autodetectlang'");
    }
];
