<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ModelTranslation;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminPageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Pages/Index', [
            'pages' => Page::latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Pages/Form', [
            'languages' => Language::getActive(),
            'defaultLocale' => Language::getDefault()?->code ?? 'sk',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.content' => 'nullable|string',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);

        $page = Page::create($validated);

        $this->saveTranslations($page, $translations);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created.');
    }

    public function edit(Page $page)
    {
        $page->load('translations');

        $translationData = [];
        foreach ($page->translations as $t) {
            $translationData[$t->locale][$t->field] = $t->value;
        }

        return Inertia::render('Admin/Pages/Form', [
            'page' => $page,
            'pageTranslations' => $translationData,
            'languages' => Language::getActive(),
            'defaultLocale' => Language::getDefault()?->code ?? 'sk',
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,'.$page->id,
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.content' => 'nullable|string',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);

        $page->update($validated);

        $this->saveTranslations($page, $translations);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated.');
    }

    public function destroy(Page $page)
    {
        $page->translations()->delete();
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted.');
    }

    private function saveTranslations(Page $page, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            foreach ($fields as $field => $value) {
                if (in_array($field, Page::translatableFields())) {
                    ModelTranslation::updateOrCreate(
                        [
                            'translatable_type' => Page::class,
                            'translatable_id' => $page->id,
                            'locale' => $locale,
                            'field' => $field,
                        ],
                        ['value' => $value ?? '']
                    );
                }
            }
        }
    }
}
