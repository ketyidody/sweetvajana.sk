<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(Page $page)
    {
        abort_unless($page->is_active, 404);

        $page->load('translations');

        return Inertia::render('Page/Show', [
            'page' => [
                'title' => $page->translated('title'),
                'content' => $page->translated('content'),
                'slug' => $page->slug,
            ],
        ]);
    }
}
