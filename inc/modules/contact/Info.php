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
    'name'          =>  $core->lang['contact']['module_name'],
    'description'   =>  $core->lang['contact']['module_desc'],
    'author'        =>  'Sruu.pl',
    'version'       =>  '1.2',
    'compatibility' =>  '1.3.*',
    'icon'          =>  'envelope',
    
    'install'   => function () use ($core) {
        $core->db()->pdo()->exec("INSERT INTO `settings`
        (`module`, `field`, `value`)
        VALUES
        ('contact', 'email', 1),
        ('contact', 'driver', 'mail'),
        ('contact', 'phpmailer.server', 'smtp.example.com'),
        ('contact', 'phpmailer.port', '587'),
        ('contact', 'phpmailer.username', 'login@example.com'),
        ('contact', 'phpmailer.name', 'Batflat contact'),
        ('contact', 'phpmailer.password', 'yourpassword'),
        ('contact', 'checkbox.switch', '0'),
        ('contact', 'checkbox.content', 'I agree to the processing of personal data...')");
    },
    'uninstall' => function () use ($core) {
        $core->db()->pdo()->exec("DELETE FROM `settings` WHERE `module` = 'contact'");
    }
];
