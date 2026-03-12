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
        $categories = Category::getTree();
        $categories->load('products');

        $categories->each(function ($category) {
            $category->products_count = $category->products->count();
            unset($category->products);
        });

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories->values(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Categories/Form', [
            'parentCategories' => Category::getTree()->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'depth' => $c->depth,
            ])->values(),
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
            'parent_id' => 'nullable|exists:categories,id',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        if (! empty($validated['parent_id'])) {
            $parentDepth = $this->getCategoryDepth($validated['parent_id']);
            if ($parentDepth + 1 >= Category::MAX_DEPTH) {
                return back()->withErrors(['parent_id' => 'Maximum nesting depth reached.']);
            }
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['position'] = Category::where('parent_id', $validated['parent_id'] ?? null)->max('position') + 1;

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
            'parentCategories' => Category::getTree($category->id)
                ->filter(fn ($c) => $c->depth < Category::MAX_DEPTH - 1)
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'depth' => $c->depth,
                ])->values(),
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
            'parent_id' => 'nullable|exists:categories,id',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        // Prevent circular reference
        if (! empty($validated['parent_id'])) {
            $descendantIds = $this->getDescendantIds($category->id);
            if (in_array($validated['parent_id'], $descendantIds) || $validated['parent_id'] == $category->id) {
                return back()->withErrors(['parent_id' => 'Cannot set a descendant as parent.']);
            }

            $parentDepth = $this->getCategoryDepth($validated['parent_id']);
            $subtreeDepth = $this->getSubtreeDepth($category->id);
            if ($parentDepth + 1 + $subtreeDepth >= Category::MAX_DEPTH) {
                return back()->withErrors(['parent_id' => 'Maximum nesting depth would be exceeded.']);
            }
        }

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
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'Cannot delete a category that has products.']);
        }

        // Promote children to the deleted category's parent
        Category::where('parent_id', $category->id)->update(['parent_id' => $category->parent_id]);

        $category->translations()->delete();
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }

    private function getDescendantIds(int $categoryId): array
    {
        $ids = [];
        $children = Category::where('parent_id', $categoryId)->pluck('id');

        foreach ($children as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getDescendantIds($childId));
        }

        return $ids;
    }

    private function getCategoryDepth(int $categoryId): int
    {
        $depth = 0;
        $category = Category::find($categoryId);

        while ($category && $category->parent_id) {
            $depth++;
            $category = Category::find($category->parent_id);
        }

        return $depth;
    }

    private function getSubtreeDepth(int $categoryId): int
    {
        $children = Category::where('parent_id', $categoryId)->pluck('id');

        if ($children->isEmpty()) {
            return 0;
        }

        $maxChildDepth = 0;
        foreach ($children as $childId) {
            $childDepth = $this->getSubtreeDepth($childId);
            $maxChildDepth = max($maxChildDepth, $childDepth);
        }

        return 1 + $maxChildDepth;
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
