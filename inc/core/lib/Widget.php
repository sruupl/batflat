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
 * Widgets class
 */
class Widget
{
    /** @var array Widgets collection */
    protected static $widgets = [];

    /**
     * Add widget to collection
     *
     * @param string $name
     * @param callable $callback
     * @return void
     */
    public static function add($name, callable $callback)
    {
        static::$widgets[$name][] = $callback;
    }

    /**
     * Execute all widgets and get content
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    public static function call($name, $params = [])
    {
        $result = [];
        foreach (isset_or(static::$widgets[$name], []) as $widget) {
            $content = call_user_func_array($widget, $params);
            if (is_string($content)) {
                $result[] = $content;
            }
        }
        
        return implode("\n", $result);
    }
}
