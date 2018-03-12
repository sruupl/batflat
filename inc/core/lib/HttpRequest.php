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

namespace Inc\Core\Lib;

/**
 * HttpRequest Class
 */
class HttpRequest
{
    /**
     * Status of latest request
     *
     * @var string
     */
    protected static $lastStatus = null;

    /**
     * GET method request
     *
     * @param string $url
     * @param array $datafields
     * @param array $headers
     * @return string Output
     */
    public static function get($url, $datafields = [], $headers = [])
    {
        return self::request('GET', $url, $datafields, $headers);
    }

    /**
     * POST method request
     *
     * @param string $url
     * @param array $datafields
     * @param array $headers
     * @return string Output
     */
    public static function post($url, $datafields = [], $headers = [])
    {
        return self::request('POST', $url, $datafields, $headers);
    }

    /**
     * Get last request status
     *
     * @return string
     */
    public static function getStatus()
    {
        return self::$lastStatus;
    }

    /**
     * Universal request method
     *
     * @param string $type GET, POST, PUT, DELETE, UPDATE
     * @param string $url
     * @param array $datafields
     * @param array $headers
     * @return string
     */
    protected static function request($type, $url, $datafields, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

        if (!empty($datafields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datafields));
        }
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        self::$lastStatus = curl_error($ch);
        curl_close($ch);

        return $output;
    }
}
