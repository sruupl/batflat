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

namespace Inc\Modules\Sample;

use Inc\Core\SiteModule;

/**
 * Sample site class
 */
class Site extends SiteModule
{
    /**
     * Example module variable
     *
     * @var string
     */
    protected $foo;

    /**
     * Module initialization
     * Here everything is done while the module starts
     *
     * @return void
     */
    public function init()
    {
        $this->foo = 'Hello';
    }
    /**
     * Register module routes
     * Call the appropriate method/function based on URL
     *
     * @return void
     */
    public function routes()
    {
        // Simple:
        $this->route('sample', 'getIndex');
        /*
            * Or:
            * $this->route('sample', function() {
            *  $this->getIndex();
            * });
            *
            * or:
            * $this->router->set('sample', $this->getIndex());
            *
            * or:
            * $this->router->set('sample', function() {
            *  $this->getIndex();
            * });
            */
    }

    /**
     * GET: /sample
     * Called method by router
     *
     * @return string
     */
    public function getIndex()
    {
        $page = [
            'title' => $this->lang('title'),
            'desc' => 'Your page description here',
            'content' => $this->draw('hello.html')
        ];

        $this->setTemplate('index.html');
        $this->tpl->set('page', $page);
    }
}
