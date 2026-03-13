<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $languages = Language::getActive();
        $defaultCode = Language::getDefault()?->code ?? 'sk';

        $urls = [];

        // Static pages
        foreach (['/' => ['changefreq' => 'daily', 'priority' => '1.0'],
                  '/products' => ['changefreq' => 'daily', 'priority' => '0.9'],
                  '/contact' => ['changefreq' => 'monthly', 'priority' => '0.5']] as $path => $meta) {
            foreach ($languages as $lang) {
                $urls[] = array_merge(['loc' => $this->makeUrl($path, $lang->code, $defaultCode)], $meta);
            }
        }

        // Product pages
        Product::where('is_active', true)->get(['slug', 'updated_at'])->each(function ($product) use ($languages, $defaultCode, &$urls) {
            foreach ($languages as $lang) {
                $urls[] = [
                    'loc' => $this->makeUrl('/products/'.$product->slug, $lang->code, $defaultCode),
                    'lastmod' => $product->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        });

        // CMS pages (excluding about — it's served at /about which is the same page)
        Page::where('is_active', true)->get(['slug', 'updated_at'])->each(function ($page) use ($languages, $defaultCode, &$urls) {
            foreach ($languages as $lang) {
                $urls[] = [
                    'loc' => $this->makeUrl('/pages/'.$page->slug, $lang->code, $defaultCode),
                    'lastmod' => $page->updated_at->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            }
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
        foreach ($urls as $url) {
            $xml .= '    <url>'.PHP_EOL;
            $xml .= '        <loc>'.e($url['loc']).'</loc>'.PHP_EOL;
            if (isset($url['lastmod'])) {
                $xml .= '        <lastmod>'.e($url['lastmod']).'</lastmod>'.PHP_EOL;
            }
            $xml .= '        <changefreq>'.e($url['changefreq']).'</changefreq>'.PHP_EOL;
            $xml .= '        <priority>'.e($url['priority']).'</priority>'.PHP_EOL;
            $xml .= '    </url>'.PHP_EOL;
        }
        $xml .= '</urlset>'.PHP_EOL;

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $content = view('robots', ['sitemapUrl' => url('/sitemap.xml')])->render();

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    private function makeUrl(string $path, string $locale, string $defaultCode): string
    {
        if ($locale === $defaultCode) {
            return url($path);
        }

        return url('/'.$locale.$path);
    }
}
