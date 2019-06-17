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
    
    namespace Inc\Modules\SearchBox;

    use Inc\Core\SiteModule;

    class Site extends SiteModule
    {

        public function init()
        {
            if(isset($_GET['search']))
                redirect(url('search/'.urlencode(strip_tags($_GET['search']))));

            $this->tpl->set('searchBox', $this->_insertSearchBox());
        }
        

        public function routes()
        {
            $this->route('search/(:any)', 'getSearch');
            $this->route('search/(:any)/(:int)', 'getSearch');
        }

        public function getSearch($phrase, $index = 1)
        {
            $phrase = urldecode($phrase);
            $phrase = strip_tags ($phrase);
            $phrase = htmlentities ($phrase);
            $searchTemplate = 'search.html';
            $phraseMinLength = 3;

            $page = [
                'title' => $this->tpl->noParse(sprintf($this->lang('results_for'), $phrase)),
                'desc' => $this->settings('settings.description')
            ];

            // if $searchTemplate exists, use it instead of "index.html"
            if(file_exists(THEMES.'/'.$this->settings('settings.theme').'/'.$searchTemplate))
                $this->setTemplate($searchTemplate);
            else
                $this->setTemplate('index.html');
            
            // check if $phrase is long as value of $phraseMinLength
            if(strlen($phrase) < $phraseMinLength)
                $page['content'] = sprintf($this->lang('too_short_phrase'), $phraseMinLength);
            else 
            {
                // select pages
                $pages = $this->db()->pdo()->prepare("SELECT * FROM pages WHERE lang = ? AND (title LIKE ? OR content LIKE ?)");
                $pages->execute([$this->_currentLanguage(), '%'.$phrase.'%', '%'.$phrase.'%']);
                $pagesArray = $pages->fetchAll();

                // add URL key to pages array
                foreach($pagesArray as &$item)
                {
                    $item['url'] = url($item['slug']);
                }

                // select blog entries
                $blog = $this->db()->pdo()->prepare("SELECT * FROM blog WHERE lang = ? AND status = ? AND (title LIKE ? OR content LIKE ?)");
                $blog->execute([$this->_currentLanguage(), 2, '%'.$phrase.'%', '%'.$phrase.'%']);
                $blogArray = $blog->fetchAll();

                // add URL key to blog array
                foreach($blogArray as &$item)
                {
                    $item['url'] = url('blog/post/'.$item['slug']);
                }

                // merge of pages and blog entries
                $rows = array_merge($pagesArray, $blogArray);

                // display results
                if(!empty($rows) && (count($rows) >= $index))
                { 
                    $pagination = new \Inc\Core\Lib\Pagination($index, count($rows), 10, url('search/'.$phrase.'/%d'));
                    $rows = array_chunk($rows, $pagination->getRecordsPerPage());
                    $page['content'] = $this->_insertResults($rows[$pagination->offset()]) . $pagination->nav();
                }
                else
                    $page['content'] = sprintf($this->lang('no_results'), $phrase);
            }

            $this->tpl->set('page', $page);
        }

        private function _insertSearchBox()
        {
            return $this->draw('input.html');
        }

        private function _insertResults(array $results)
        {
            foreach($results as &$result)
            {
                // remove HTML and Template tags
                $result['content'] = preg_replace('/{(.*?)}/', '', strip_tags($result['content']));
            }
            return $this->draw('results.html', ['results' => $results]);
        }

        private function _currentLanguage()
        {
            if(!isset($_SESSION['lang']))
                return $this->settings('settings', 'lang_site');
            else
                return $_SESSION['lang'];
        }

    }