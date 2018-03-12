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

use Inc\Modules\Statistics\DB;

return [
    'name'          => $core->lang['statistics']['module_name'],
    'description'   => $core->lang['statistics']['module_desc'],
    'author'        => 'Sruu.pl',
    'version'       => '1.0',
    'compatibility' => '1.3.*',
    'icon'          => 'pie-chart',

    'install'   => function () use ($core) {
        if (file_exists(BASE_DIR.'/inc/data/statistics.sdb')) {
            return;
        }

        DB::pdo()->exec("CREATE TABLE IF NOT EXISTS `statistics` (
            `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `ip`	TEXT NOT NULL,
            `useragent`	TEXT,
            `uniqhash` TEXT,
            `browser`	TEXT,
            `country`	TEXT,
            `platform` TEXT,
            `url` TEXT,
            `referrer` TEXT,
            `status_code` INTEGER NOT NULL,
            `bot` INTEGER NOT NULL,
            `created_at`	INTEGER NOT NULL
        );");

        DB::pdo()->exec("CREATE INDEX statistics_idx1 ON statistics(ip,useragent,country,platform,url,referrer,status_code,bot)");
    },
    'uninstall' => function () use ($core) {
        unlink(BASE_DIR.'/inc/data/statistics.sdb');
    },
];
