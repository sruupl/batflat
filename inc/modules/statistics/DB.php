<?php

namespace Inc\Modules\Statistics;

use Inc\Core\Lib\QueryBuilder;

class DB extends QueryBuilder
{
    /**
     * @var \PDO
     */
    protected static $db;
    public function __construct($table = null)
    {
        parent::__construct($table);
        $database = BASE_DIR . '/inc/data/statistics.sdb';
        self::connect("sqlite:{$database}");
    }
}
