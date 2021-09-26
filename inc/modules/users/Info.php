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
    'name'          => $core->lang['users']['module_name'],
    'description'   => $core->lang['users']['module_desc'],
    'author'        => 'Sruu.pl',
    'version'       => '1.2',
    'compatibility' => '1.3.*',
    'icon'          => 'user',
    'install'       => function () use ($core) {
        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `username` text NOT NULL,
            `fullname` text NULL,
            `description` text NULL,
            `password` text NOT NULL,
            `avatar` text NOT NULL,
            `email` text NOT NULL,
            `role` text NOT NULL DEFAULT 'admin',
            `access` text NOT NULL DEFAULT 'all'
        )");

        $core->db()->pdo()->exec("CREATE TABLE `login_attempts` (
            `ip` TEXT NOT NULL,
            `attempts` INTEGER NOT NULL,
            `expires` INTEGER NOT NULL DEFAULT 0
        )");

        $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `remember_me` (
            `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT,
            `token` text NOT NULL,
            `user_id` integer NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            `expiry` integer NOT NULL
        )");

        $avatar = uniqid('avatar').'.png';
        $core->db()->pdo()->exec('INSERT INTO `users` (`username`, `fullname`, `description`, `password`, `avatar`, `email`, `role`, `access`)
            VALUES ("admin", "Selina Kyle", "My name is Selina Kyle but I speak for Catwoman… A mon who can offer you a path. Someone like you is only here by choice. You have been exploring the criminal fraternity but whatever your original intentions you have to become truly lost.", "$2y$10$pgRnDiukCbiYVqsamMM3ROWViSRqbyCCL33N8.ykBKZx0dlplXe9i", "'.$avatar.'", "admin@localhost", "admin", "all")');

        if (!is_dir(UPLOADS."/users")) {
            mkdir(UPLOADS."/users", 0777);
        }

        copy(MODULES.'/users/img/default.png', UPLOADS.'/users/'.$avatar);
    },
    'uninstall' => function () use ($core) {
        $core->db()->pdo()->exec("DROP TABLE `users`");
        $core->db()->pdo()->exec("DROP TABLE `login_attempts`");
        $core->db()->pdo()->exec("DROP TABLE `remember_me`");
        deleteDir(UPLOADS."/users");
    }
];
