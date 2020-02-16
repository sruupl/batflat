<?php

namespace Inc\Modules\Statistics\Src;

use Inc\Modules\Statistics\DB;

class Chart
{
    public function getVisitors($days = 14, $offset = 0, $url = null, $referrer = null)
    {
        $time = strtotime(date("Ymd000000", strtotime("-".$days + $offset." days")));

        $query = $this->db('statistics')
            ->select([
                'count'        => 'COUNT(*)',
                'count_unique' => 'COUNT(DISTINCT uniqhash)',
                'formatedDate' => "strftime('%Y-%m-%d', datetime(created_at, 'unixepoch', 'localtime'))",
            ])
            ->where('bot', 0)
            ->where('created_at', '>=', $time)
            ->group(['formatedDate'])
            ->asc('formatedDate');

        if (!empty($url)) {
            $query->where('url', $url);
        }

        if (!empty($referrer)) {
            $query->where('referrer', $referrer);
        }

        $data = $query->toArray();

        $return = [
            'labels'  => [],
            'uniques' => [],
            'visits'  => [],
        ];

        while ($time < (time() - ($offset * 86400))) {
            $return['labels'][] = '"'.date("Y-m-d", $time).'"';
            $return['readable'][] = '"'.date("d M Y", $time).'"';
            $return['uniques'][] = 0;
            $return['visits'][] = 0;

            $time = strtotime('+1 day', $time);
        }

        foreach ($data as $day) {
            $index = array_search('"'.$day['formatedDate'].'"', $return['labels']);
            if ($index === false) {
                continue;
            }

            $return['uniques'][$index] = $day['count_unique'];
            $return['visits'][$index] = $day['count'];
        }

        return $return;
    }

    public function getOperatingSystems($url = null, $referrer = null)
    {
        return $this->getPopularBy('platform', $url, $referrer);
    }

    public function getBrowsers($url = null, $referrer = null)
    {
        return $this->getPopularBy('browser', $url, $referrer);
    }

    public function getCountries($url = null, $referrer = null)
    {
        return $this->getPopularBy('country', $url, $referrer);
    }

    public function getPages($url = null, $referrer = null)
    {
        return $this->getPopularBy('url', $url, $referrer, 'desc');
    }

    public function getReferrers($url = null, $referrer = null)
    {
        return $this->getPopularBy('referrer', $url, $referrer);
    }

    protected function getPopularBy($group, $url = null, $referrer = null, $order = 'asc')
    {
        $data = $this->db('statistics')
            ->select([
                $group,
                'count' => 'COUNT(DISTINCT uniqhash)',
            ])
            ->where('bot', 0)
            ->group([$group])
            ->asc('count');

        if (!empty($url)) {
            $data->where('url', $url);
        }

        if (!empty($referrer)) {
            $data->where('referrer', $referrer);
        }

        $data = $data->toArray();


        if ($order == 'desc') {
            $data = array_reverse($data);
        }

        $labels = array_map(function (&$value) {
            $value = preg_replace('/[^a-zA-Z0-9\/]/', '', $value);
            return '"'.$value.'"';
        }, array_column($data, $group));

        return [
            'labels' => $labels,
            'data'   => array_column($data, 'count'),
        ];
    }

    protected function db($table)
    {
        return new DB($table);
    }
}
