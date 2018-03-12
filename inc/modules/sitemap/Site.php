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
    
    namespace Inc\Modules\Sitemap;

    use Inc\Core\SiteModule;

    class Site extends SiteModule
    {
        public function routes()
        {
            $this->route('sitemap.xml', function() {
                $this->setTemplate(false);
                header('Content-type: application/xml');

                $urls = [];

                // Home Page
                $urls[] = [
                    'url' => url(),
                    'lastmod' => null,
                ];

                // Pages
                $pages = $this->db('pages')->asc('lang')->asc('id')->toArray();
                $lang = $this->settings('settings.lang_site');
                $homepage = $this->settings('settings.homepage');
                foreach($pages as $page)
                {
                    $page['date'] = strtotime($page['date']);

                    $shortLang = strstr($page['lang'], '_', true);
                    if(strpos($page['slug'], '404') !== FALSE)
                        continue;

                    if($lang == $page['lang'] && $homepage == $page['slug'])
                        $urls[0]['lastmod'] = date('c', $page['date']);
                    else if($homepage == $page['slug'])
                        $urls[] = ['url' => url($shortLang), 'lastmod' => date('c', $page['date'])];
                    else if($lang == $page['lang'])
                        $urls[] = ['url' => url($page['slug']), 'lastmod' => date('c', $page['date'])];
                    else
                        $urls[] = ['url' => url([$shortLang, $page['slug']]), 'lastmod' => date('c', $page['date'])];
                }

                // Blog
                $posts = $this->db('blog')->where('status', 2)->desc('published_at')->toArray();
                $tags = $this->db('blog_tags_relationship')->leftJoin('blog_tags', 'blog_tags.id = blog_tags_relationship.tag_id')->leftJoin('blog', 'blog.id', 'blog_tags_relationship.blog_id')->where('blog.status', 2)->group('blog_tags.slug')->select(['slug' => 'blog_tags.slug'])->toArray();
                if($homepage != 'blog')
                {
                    $urls[] = [
                        'url' => url('blog'),
                        'lastmod' => date('c', $posts[0]['published_at'])
                    ];
                }
                else
                {
                    $urls[0]['lastmod'] = date('c', $posts[0]['published_at']);
                }
                foreach($posts as $post)
                {
                    $post['published_at'] = $post['published_at'];
                    $urls[] = [
                        'url' => url(['blog','post',$post['slug']]),
                        'lastmod' => date('c', $post['published_at']),
                    ];
                }
                foreach($tags as $tag)
                {
                    $urls[] = [
                        'url' => url(['blog', 'tag', $tag['slug']]),
                        'lastmod' => null,
                    ];
                }

                echo $this->draw('sitemap.xml', compact('urls'));
            });
        }
    }