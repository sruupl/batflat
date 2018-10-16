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
        $this->route('sitemap.xml', function () {
            $this->setTemplate(false);
            header('Content-type: application/xml');

            $urlset = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
            $urlset->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $urlset->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $urlset->addAttribute(
                'xsi:schemaLocation',
                'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'
            );

            $home = $urlset->addChild('url');
            $home->addChild('loc', url());

            // Pages
            $pages = $this->db('pages')->asc('lang')->asc('id')->toArray();
            $lang = $this->settings('settings.lang_site');
            $homepage = $this->settings('settings.homepage');
            $node = null;
            foreach ($pages as $page) {
                $page['date'] = strtotime($page['date']);

                $shortLang = strstr($page['lang'], '_', true);
                if (strpos($page['slug'], '404') !== false) {
                    continue;
                }

                if ($lang == $page['lang'] && $homepage == $page['slug']) {
                    $node = $home;
                } elseif ($homepage == $page['slug']) {
                    $node = $urlset->addChild('url');
                    $node->addChild('loc', url($shortLang));
                } elseif ($lang == $page['lang']) {
                    $node = $urlset->addChild('url');
                    $node->addChild('loc', url($page['slug']));
                } else {
                    $node = $urlset->addChild('url');
                    $node->addChild('loc', url([$shortLang, $page['slug']]));
                }

                $node->addChild('lastmod', date('c', $page['date']));
            }

            // Blog
            $posts = $this->db('blog')->where('status', 2)->desc('published_at')->toArray();
            $tags = $this->db('blog_tags_relationship')
                ->leftJoin('blog_tags', 'blog_tags.id = blog_tags_relationship.tag_id')
                ->leftJoin('blog', 'blog.id', 'blog_tags_relationship.blog_id')
                ->where('blog.status', 2)->group('blog_tags.slug')
                ->select(['slug' => 'blog_tags.slug'])->toArray();
            if ($homepage != 'blog') {
                $node = $urlset->addChild('url');
                $node->addChild('loc', url('blog'));
                $node->addChild('lastmod', date('c', $posts[0]['published_at']));
            } else {
                $home->addChild('lastmod', date('c', $posts[0]['published_at']));
            }
            foreach ($posts as $post) {
                $node = $urlset->addChild('url');
                $node->addChild('loc', url(['blog', 'post', $post['slug']]));
                $node->addChild('lastmod', date('c', $post['published_at']));
            }

            foreach ($tags as $tag) {
                $node = $urlset->addChild('url');
                $node->addChild('loc', url(['blog', 'tag', $tag['slug']]));
            }

            echo $urlset->asXML();
        });
    }
}
