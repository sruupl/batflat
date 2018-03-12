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
 * Batflat modules collection
 */
class ModulesCollection
{
    /**
     * List of loaded modules
     *
     * @var array
     */
    protected $modules = [];

    /**
     * ModulesCollection constructor
     *
     * @param \Inc\Core\Main $core
     */
    public function __construct($core)
    {
        $modules = array_column($core->db('modules')->asc('sequence')->toArray(), 'dir');
        if ($core instanceof \Inc\Core\Admin) {
            $clsName = 'Admin';
        } else {
            $clsName = 'Site';
        }
        
        foreach ($modules as $dir) {
            $file = MODULES.'/'.$dir.'/'.$clsName.'.php';
            if (file_exists($file)) {
                $namespace = 'inc\modules\\'.$dir.'\\'.$clsName;
                $this->modules[$dir] = new $namespace($core);
            }
        }

        // Init loop
        $this->initLoop();

        // Routes loop for Site
        if ($clsName != 'Admin') {
            $this->routesLoop();
        }
    }

    /**
     * Executes all init methods
     *
     * @return void
     */
    protected function initLoop()
    {
        foreach ($this->modules as $module) {
            $module->init();
        }
    }

    /**
     * Executes all routes methods
     *
     * @return void
     */
    protected function routesLoop()
    {
        foreach ($this->modules as $module) {
            $module->routes();
        }
    }

    /**
     * Executes all finish methods
     *
     * @return void
     */
    public function finishLoop()
    {
        foreach ($this->modules as $module) {
            $module->finish();
        }
    }

    /**
     * Get list of modules as array
     *
     * @return array
     */
    public function getArray()
    {
        return $this->modules;
    }

    /**
     * Check if collection has loaded module
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->modules);
    }

    /**
     * Get specified module by magic method
     *
     * @param string $module
     * @return \Inc\Core\BaseModule
     */
    public function __get($module)
    {
        if (isset($this->modules[$module])) {
            return $this->modules[$module];
        } else {
            return null;
        }
    }
}
