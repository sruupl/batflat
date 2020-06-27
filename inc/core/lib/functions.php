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

/**
 * check if array have an empty values
 *
 * @param array $keys
 * @param array $array
 *
 * @return boolean
 */
function checkEmptyFields(array $keys, array $array)
{
    foreach ($keys as $field) {
        if (empty($array[$field])) {
            return true;
        }
    }

    return false;
}

/**
 * delte dir with files
 *
 * @param string $path
 *
 * @return boolean
 */
function deleteDir($path)
{
    return !empty($path) && is_file($path)
        ? @unlink($path)
        : (array_reduce(glob($path.'/*'),
            function ($r, $i) {
                return $r && deleteDir($i);
            }, true))
        && @rmdir($path);
}

/**
 * remove special chars from string
 *
 * @param string $text
 *
 * @return string
 */
function createSlug($text)
{
    setlocale(LC_ALL, 'pl_PL');
    $text = str_replace(' ', '-', trim($text));
    $text = str_replace('.', '-', trim($text));
    $text = iconv('utf-8', 'ascii//translit', $text);
    $text = preg_replace('#[^a-z0-9\-]#si', '', $text);
    $text = str_replace('\'', '', $text);

    return strtolower($text ? $text : '-');
}

/**
 * convert special chars from array
 *
 * @param array $array
 *
 * @return array
 */
function htmlspecialchars_array(array $array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = htmlspecialchars_array($value);
        } else {
            $array[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }

    return $array;
}

/**
 * convert all characters to HTML entities from array
 *
 * @param array $array
 *
 * @return array
 */
function htmlentities_array(array $array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = htmlentities_array($value);
        } else {
            $array[$key] = htmlentities($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }

    return $array;
}

/**
 * redirect to URL
 *
 * @param string $url
 * @param array  $data
 *
 * @return void
 */
function redirect($url, array $data = [])
{
    if ($data) {
        $_SESSION['REDIRECT_DATA'] = $data;
    }

    header("Location: $url");
    exit();
}

/**
 * get data from session
 *
 * @return array or null
 */
function getRedirectData()
{
    if (isset($_SESSION['REDIRECT_DATA'])) {
        $tmp = $_SESSION['REDIRECT_DATA'];
        unset($_SESSION['REDIRECT_DATA']);

        return $tmp;
    }

    return null;
}

/**
 * Returns current url
 *
 * @param boolean $query
 *
 * @return string
 */
function currentURL($query = false)
{
    if (isset_or($GLOBALS['core'], null) instanceof \Inc\Core\Admin) {
        $url = url(ADMIN.'/'.implode('/', parseURL()));
    } else {
        $url = url(implode('/', parseURL()));
    }

    if ($query) {
        return $url.'?'.$_SERVER['QUERY_STRING'];
    } else {
        return $url;
    }
}

/**
 * parse URL
 *
 * @param int $key
 *
 * @return mixed array, string or false
 */
function parseURL($key = null)
{
    $url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $url = trim(str_replace($url, '', $_SERVER['REQUEST_URI']), '/');
    $url = explode('?', $url);
    $array = explode('/', $url[0]);

    if ($key) {
        return isset_or($array[$key - 1], false);
    } else {
        return $array;
    }
}

/**
 * add token to URL
 *
 * @param string $url
 *
 * @return string
 */
function addToken($url)
{
    if (isset($_SESSION['token'])) {
        if (parse_url($url, PHP_URL_QUERY)) {
            return $url.'&t='.$_SESSION['token'];
        } else {
            return $url.'?t='.$_SESSION['token'];
        }
    }

    return $url;
}

/**
 * create URL
 *
 * @param string / array $data
 *
 * @return string
 */
function url($data = null)
{
    if (filter_var($data, FILTER_VALIDATE_URL) !== false) {
        return $data;
    }

    if (!is_array($data) && strpos($data, '#') === 0) {
        return $data;
    }

    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
        || isset_or($_SERVER['SERVER_PORT'], null) == 443
        || isset_or($_SERVER['HTTP_X_FORWARDED_PORT'], null) == 443
        || isset_or($_SERVER['HTTP_X_FORWARDED_PROTO'], null) == 'https'
    ) {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }

    $url = trim($protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $url = str_replace('/'.ADMIN, '', $url);

    if (is_array($data)) {
        $url = $url.'/'.implode('/', $data);
    } elseif ($data) {
        $data = str_replace(BASE_DIR.'/', null, $data);
        $url = $url.'/'.trim($data, '/');
    }

    if (strpos($url, '/'.ADMIN.'/') !== false) {
        $url = addToken($url);
    }

    return $url;
}

/**
 * Current domain name
 *
 * @return string
 */
function domain($with_protocol = true, $cut_www = false)
{
    $url = parse_url(url());

    if ($cut_www && strpos($url['host'], 'www.') === 0) {
        $host = str_replace('www.', null, $url['host']);
    } else {
        $host = $url['host'];
    }

    if ($with_protocol) {
        return $url['scheme'].'://'.$host;
    }

    return $host;
}

/**
 * Batflat dir name
 *
 * @return string
 */
function batflat_dir() {
    return dirname(str_replace(ADMIN, null, $_SERVER['SCRIPT_NAME']));
}

/**
 * toggle empty variable
 *
 * @param mixed $var
 * @param mixed $alternate
 *
 * @return mixed
 */
function isset_or(&$var, $alternate = null)
{
    return (isset($var)) ? $var : $alternate;
}

/**
 * compares two version number strings
 *
 * @param string $a
 * @param string $b
 *
 * @return int
 */
function cmpver($a, $b)
{
    $a = explode(".", $a);
    $b = explode(".", $b);
    foreach ($a as $depth => $aVal) {
        if (isset($b[$depth])) {
            $bVal = $b[$depth];
        } else {
            $bVal = "0";
        }

        list($aLen, $bLen) = [strlen($aVal), strlen($bVal)];

        if ($aLen > $bLen) {
            $bVal = str_pad($bVal, $aLen, "0");
        } elseif ($bLen > $aLen) {
            $aVal = str_pad($aVal, $bLen, "0");
        }

        if ($aVal == $bVal) {
            continue;
        }

        if ($aVal > $bVal) {
            return 1;
        }

        if ($aVal < $bVal) {
            return -1;
        }
    }

    return 0;
}

/**
 * Limits string to specified length and appends with $end value
 *
 * @param  string  $text  Input text
 * @param  integer $limit String max length
 * @param  string  $end   Appending variable if text is longer than limit
 *
 * @return string         Limited string
 */
function str_limit($text, $limit = 100, $end = '...')
{
    if (mb_strlen($text, 'UTF-8') > $limit) {
        return mb_substr($text, 0, $limit, 'UTF-8').$end;
    }

    return $text;
}

/**
 * Get response headers list
 *
 * @param string $key
 *
 * @return mixed Array of headers or specified header by $key
 */
function get_headers_list($key = null)
{
    $headers_list = headers_list();
    $headers = [];
    foreach ($headers_list as $header) {
        $e = explode(":", $header);
        $headers[strtolower(array_shift($e))] = trim(implode(":", $e));
    }

    if ($key) {
        return isset_or($headers[strtolower($key)], false);
    }

    return $headers;
}

/**
 * Generating random hash from specified characters
 *
 * @param  int    $length     Hash length
 * @param  string $characters Characters for hash
 *
 * @return string             Generated random string
 */
function str_gen($length, $characters = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM")
{
    $return = null;

    if (is_string($characters)) {
        $characters = str_split($characters);
    }

    for ($i = 0; $i < $length; $i++) {
        $return .= $characters[rand(0, count($characters) - 1)];
    }

    return $return;
}

/**
 * Compressed base64_encode
 *
 * @param strin $string
 *
 * @return string
 */
function gz64_encode($string)
{
    return str_replace(['+', '/'], ['_', '-'], trim(base64_encode(gzcompress($string, 9)), "="));
}

/**
 * Decompress base64_decode
 *
 * @param string $string
 *
 * @return string
 */
function gz64_decode($string)
{
    return gzuncompress(base64_decode(str_replace(['_', '-'], ['+', '/'], $string)));
}

/**
 * Call variable which can be callback or other type.
 * If it is anonymous function it will be executed, otherwise $variable will be returned.
 *
 * @param mixed $variable
 *
 * @return mixed
 */
function cv($variable)
{
    if (!is_string($variable) && is_callable($variable)) {
        return $variable();
    }

    return $variable;
}
