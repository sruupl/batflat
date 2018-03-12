<?php

namespace Inc\Modules\Statistics\Src;

use Inc\Modules\Statistics\DB;

class Statistics
{
    public function getReferrers($url = null, $limit = 15, $bot = false)
    {
        $query = $this->db('statistics')
            ->select([
                'referrer',
                'count_unique' => 'COUNT(DISTINCT uniqhash)',
                'count'        => 'COUNT(uniqhash)',
            ])
            ->where('bot', $bot ? 1 : 0)
            ->group(['referrer'])
            ->desc('count');

        if (!empty($url)) {
            $query->where('url', $url);
        }
        if ($limit !== false) {
            $query->limit($limit);
        }

        $urls = $query->toArray();

        return $urls;
    }

    public function getPages($referrer = null, $limit = 15)
    {
        $query = $this->db('statistics')
            ->select([
                'url',
                'count_unique' => 'COUNT(DISTINCT uniqhash)',
                'count'        => 'COUNT(uniqhash)',
            ])
            ->group(['url'])
            ->desc('count');

        if ($limit !== false) {
            $query->limit($limit);
        }

        if (!empty($referrer)) {
            $query->where('referrer', $referrer);
        }

        $urls = $query->toArray();

        return $urls;
    }

    public function countCurrentOnline($margin = "-5 minutes")
    {
        $online = $this->db('statistics')
            ->select([
                'count' => 'COUNT(DISTINCT uniqhash)',
            ])
            ->where('bot', 0)
            ->where('created_at', '>', strtotime($margin))
            ->oneArray();

        return $online['count'];
    }

    public function countAllVisits($date = 'TODAY', $days = 1, $url = null, $referrer = null)
    {
        $query = $this->db('statistics')
            ->select([
                'count' => 'COUNT(uniqhash)',
            ])
            ->where('bot', 0);

        if ($date != 'ALL') {
            $date = strtotime($date);
            $query->where('created_at', '>=', $date)->where('created_at', '<', $date + $days * 86400);
        }

        if (!empty($url)) {
            $query->where('url', $url);
        }
        if (!empty($referrer)) {
            $query->where('referrer', $referrer);
        }

        $all = $query->oneArray();

        return $all['count'];
    }

    public function countUniqueVisits($date = 'TODAY', $days = 1, $url = null, $referrer = null)
    {
        $query = $this->db('statistics')
            ->select([
                'count' => 'COUNT(DISTINCT uniqhash)',
            ])
            ->where('bot', 0);

        if ($date != 'ALL') {
            $date = strtotime($date);
            $query->where('created_at', '>=', $date)->where('created_at', '<', $date + $days * 86400);
        }

        if (!empty($url)) {
            $query->where('url', $url);
        }
        if (!empty($referrer)) {
            $query->where('referrer', $referrer);
        }

        $record = $query->oneArray();

        return $record['count'];
    }

    protected function db($table)
    {
        return new DB($table);
    }
}
