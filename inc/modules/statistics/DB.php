<?php

namespace Inc\Modules\Statistics;

use Inc\Core\Lib\QueryBuilder;

class DB extends QueryBuilder
{
    /**
     * @var \PDO
     */
    protected static $db;
}

$database = BASE_DIR.'/inc/data/statistics.sdb';
DB::connect("sqlite:{$database}");