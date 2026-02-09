<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\ModelTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => Category::withCount('products')->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Categories/Form', [
            'languages' => Language::getActive(),
            'defaultLocale' => Language::getDefault()?->code ?? 'sk',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = Storage::url(
                $request->file('image')->store('categories', 'public')
            );
        }

        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);

        $category = Category::create($validated);

        $this->saveTranslations($category, $translations);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        $category->load('translations');

        $translationData = [];
        foreach ($category->translations as $t) {
            $translationData[$t->locale][$t->field] = $t->value;
        }

        return Inertia::render('Admin/Categories/Form', [
            'category' => $category,
            'categoryTranslations' => $translationData,
            'languages' => Language::getActive(),
            'defaultLocale' => Language::getDefault()?->code ?? 'sk',
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = Storage::url(
                $request->file('image')->store('categories', 'public')
            );
        }

        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);

        $category->update($validated);

        $this->saveTranslations($category, $translations);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->translations()->delete();
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }

    private function saveTranslations(Category $category, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            foreach ($fields as $field => $value) {
                if (in_array($field, Category::translatableFields())) {
                    ModelTranslation::updateOrCreate(
                        [
                            'translatable_type' => Category::class,
                            'translatable_id' => $category->id,
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
