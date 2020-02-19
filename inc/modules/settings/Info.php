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
    'name'          =>  $core->lang['settings']['module_name'],
    'description'   =>  $core->lang['settings']['module_desc'],
    'author'        =>  'Sruu.pl',
    'version'       =>  '1.3',
    'compatibility' =>  '1.3.*',
    'icon'          =>  'wrench',

    'install'       =>  function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `settings` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `module` text NOT NULL,
            `field` text NOT NULL,
            `value` text
        )");

        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'title', 'Batflat')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'description', 'Gotham’s time has come.')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'keywords', 'key, words')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'footer', 'Copyright {?=date(\"Y\")?} &copy; by Company Name. All rights reserved.')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'homepage', 'blog')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'timezone', '".date_default_timezone_get()."')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'theme', 'batblog')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'editor', 'wysiwyg')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'lang_site', 'en_english')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'lang_admin', 'en_english')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'version', '1.3.6')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'update_check', '0')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'update_changelog', '')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'update_version', '0')");
        $core->db()->pdo()->exec("INSERT INTO `settings` (`module`, `field`, `value`) VALUES ('settings', 'license', '')");
    },
    'uninstall'     =>  function () use ($core) {
        $core->db()->pdo()->exec("DROP TABLE `settings`");
    }
];
