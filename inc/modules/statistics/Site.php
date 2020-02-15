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

namespace Inc\Modules\Statistics;

use Inc\Core\SiteModule;

use Inc\Modules\Statistics\DB;
use Inc\Modules\Statistics\PHPBrowserDetector\Browser;
use Inc\Modules\Statistics\PHPBrowserDetector\Os;
use Inc\Core\Lib\HttpRequest;

class Site extends SiteModule
{
    public function init()
    {
        // Browser
        $browser = new Browser;

        // OS
        $os = new Os;

        // IP and GEO
        $ip = $_SERVER['REMOTE_ADDR'];

        // Get latest country or fetch new
        $country = 'Unknown';
        $latest = $this->db('statistics')->where('ip', $ip)->desc('created_at')->limit(1)->oneArray();

        if (!$latest) {
            $details = json_decode(HttpRequest::get('https://freegeoip.app/json/'.$ip), true);

            if (!empty($details['country_code'])) {
                $country = $details['country_code'];
            }
        } else {
            $country = $latest['country'];
        }

        // referrer
        $referrer = isset_or($_SERVER['HTTP_referrer'], false);
        if ($referrer && $url = parse_url($referrer)) {
            if (strpos($url['host'], domain(false)) !== false) {
                $referrer = null;
            }
        } else {
            $referrer = null;
        }

        // Add visitor record
        $this->db('statistics')->save([
            'ip'          => $ip,
            'browser'     => $browser->getName(),
            'useragent'   => $browser->getUserAgent()->getUserAgentString(),
            'uniqhash'    => md5($ip.$browser->getUserAgent()->getUserAgentString()),
            'country'     => $country,
            'platform'    => $os->getName(),
            'url'         => '/'.implode('/', parseURL()),
            'referrer'    => $referrer,
            'status_code' => http_response_code(),
            'bot'         => ($browser->isRobot() ? 1 : 0),
        ]);
    }

    public function routes()
    {
        //
    }

    protected function db($table = null)
    {
        return new DB($table);
    }
}
