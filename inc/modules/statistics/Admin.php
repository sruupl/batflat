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

use Inc\Core\AdminModule;
use Inc\Modules\Statistics\Src\Chart;
use Inc\Modules\Statistics\Src\Statistics;

class Admin extends AdminModule
{
    /**
     * @var string
     */
    protected $moduleDirectory = null;

    /**
     * @var Chart
     */
    protected $chart = null;

    /**
     * @var Statistics
     */
    protected $statistics = null;

    public function init()
    {
        $this->statistics = new Statistics();
        $this->chart = new Chart();

        $this->moduleDirectory = MODULES.'/statistics';
        $this->core->addCSS(url($this->moduleDirectory.'/assets/css/style.css?v={$bat.version}'));
        $this->core->addJS(url($this->moduleDirectory.'/assets/js/Chart.bundle.min.js'));
        $this->core->addJS(url($this->moduleDirectory.'/assets/js/app.js?v={$bat.version}'));
    }

    public function navigation()
    {
        return [
            'Main' => 'main',
        ];
    }

    public function getMain()
    {
        $statistics = $this->statistics;
        $chart = $this->chart;

        // Numbers
        $visitors['unique'] = $statistics->countUniqueVisits();
        $visitors['online'] = $statistics->countCurrentOnline();
        $visitors['visits']['today'] = $statistics->countAllVisits("TODAY");
        $visitors['visits']['yesterday'] = $statistics->countUniqueVisits("YESTERDAY");
        $visitors['visits']['7days'] = $statistics->countUniqueVisits("-7 days", 7);
        $visitors['visits']['30days'] = $statistics->countUniqueVisits("-30 days", 30);
        $visitors['visits']['all'] = $statistics->countUniqueVisits("ALL");

        // Charts
        $visitors['chart'] = $chart->getVisitors(14);
        $visitors['platform'] = $chart->getOperatingSystems();
        $visitors['countries'] = $chart->getCountries();
        $visitors['browsers'] = $chart->getBrowsers();

        // Lists
        $visitors['pages'] = $statistics->getPages();
        $visitors['referrers'] = $statistics->getReferrers();

        return $this->draw('dashboard.html', [
            'visitors' => $visitors
        ]);
    }

    public function getUrl($urlBase64)
    {
        $statistics = $this->statistics;
        $chart = $this->chart;

        $url = base64_decode($urlBase64);

        // Numbers
        $visitors['unique'] = $statistics->countUniqueVisits('ALL', 1, $url);
        $visitors['all'] = $statistics->countAllVisits('ALL', 1, $url);

        // Charts
        $visitors['chart'] = $chart->getVisitors(14, 0, $url);
        $visitors['platform'] = $chart->getOperatingSystems($url);
        $visitors['countries'] = $chart->getCountries($url);
        $visitors['browsers'] = $chart->getBrowsers($url);

        // Lists
        $visitors['referrers'] = $statistics->getReferrers($url, false);

        return $this->draw('url.html', [
            'url' => $url,
            'visitors' => $visitors
        ]);
    }

    public function getreferrer($urlBase64)
    {
        $statistics = $this->statistics;
        $chart = $this->chart;

        $url = base64_decode($urlBase64);

        // Numbers
        $visitors['unique'] = $statistics->countUniqueVisits('ALL', 1, null, $url);
        $visitors['all'] = $statistics->countAllVisits('ALL', 1, null, $url);

        // Charts
        $visitors['chart'] = $chart->getVisitors(14, 0, null, $url);
        $visitors['platform'] = $chart->getOperatingSystems(null, $url);
        $visitors['countries'] = $chart->getCountries(null, $url);
        $visitors['browsers'] = $chart->getBrowsers(null, $url);

        // Lists
        $visitors['referrers'] = $chart->getReferrers($url, false);

        return $this->draw('referrer.html', [
            'url' => $url,
            'visitors' => $visitors
        ]);
    }

    public function getPages()
    {
        $statistics = $this->statistics;
        $chart = $this->chart;

        $pages['chart'] = $chart->getPages();
        $pages['list'] = $statistics->getPages(null, false);

        return $this->draw('pages.html', [
            'pages' => $pages
        ]);
    }

    public function getReferrers()
    {
        $statistics = $this->statistics;
        $chart = $this->chart;

        $referrer['chart'] = $chart->getReferrers();
        $referrer['list'] = $statistics->getReferrers(null, false);

        return $this->draw('referrers.html', [
            'referrers' => $referrer
        ]);
    }
}
