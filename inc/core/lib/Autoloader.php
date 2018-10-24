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
 * Batflat autoloader
 */

class Autoloader
{
    /**
     * Autoload initialization
     *
     * @param string $className
     * @return void
     */
    public static function init($className)
    {
        // Convert directories to lowercase and process uppercase for class files
        $className = explode('\\', $className);
        $file = array_pop($className);
        $file = strtolower(implode('/', $className)) . '/' . $file . '.php';

        if (strpos($_SERVER['SCRIPT_NAME'], '/' . ADMIN . '/') !== false) {
            $file = '../' . $file;
        }
        if (is_readable($file)) {
            require_once($file);
        }
    }
}
