<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\ModelTranslation;
use App\Models\OptionGroup;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::with('category')->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Form', [
            'categories' => Category::getTree()->filter(fn ($c) => $c->is_active)->values(),
            'languages' => Language::getActive(),
            'defaultLocale' => Language::getDefault()?->code ?? 'sk',
            'optionGroups' => $this->availableOptionGroups(),
        ]);
    }

    public function store(Request $request)
    {
        $isOrderableOnline = filter_var($request->input('is_orderable_online'), FILTER_VALIDATE_BOOLEAN);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => $isOrderableOnline ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:20480',
            'additional_images' => 'nullable|array|max:10',
            'additional_images.*' => 'image|max:20480',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_orderable_online' => 'boolean',
            'is_available_for_collection' => 'boolean',
            'soonest_availability' => 'nullable|integer|min:1|max:365',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'option_groups' => 'nullable|array',
            'option_groups.*.id' => 'required|integer|distinct|exists:option_groups,id',
            'option_groups.*.min_selections' => 'required|integer|min:0|max:50',
            'option_groups.*.max_selections' => 'required|integer|min:1|max:50|gte:option_groups.*.min_selections',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = Storage::url(
                $request->file('image')->store('products', 'public')
            );
        }

        $images = [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $file) {
                $images[] = Storage::url($file->store('products', 'public'));
            }
        }
        $validated['images'] = $images;

        $translations = $validated['translations'] ?? [];
        $optionGroups = $validated['option_groups'] ?? [];
        unset($validated['additional_images'], $validated['translations'], $validated['option_groups']);

        $product = Product::create($validated);

        $this->saveTranslations($product, $translations);
        $this->syncOptionGroups($product, $optionGroups);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load(['translations', 'optionGroups']);

        $translationData = [];
        foreach ($product->translations as $t) {
            $translationData[$t->locale][$t->field] = $t->value;
        }

        $defaultLocale = Language::getDefault()?->code ?? 'sk';

        return Inertia::render('Admin/Products/Form', [
            'product' => $product,
            'categories' => Category::getTree()->filter(fn ($c) => $c->is_active)->values(),
            'productTranslations' => $translationData,
            'productOptionGroups' => $product->optionGroups->map(fn ($g) => [
                'id' => $g->id,
                'min_selections' => $g->pivot->min_selections,
                'max_selections' => $g->pivot->max_selections,
            ]),
            'languages' => Language::getActive(),
            'defaultLocale' => $defaultLocale,
            'optionGroups' => $this->availableOptionGroups(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $isOrderableOnline = filter_var($request->input('is_orderable_online'), FILTER_VALIDATE_BOOLEAN);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => $isOrderableOnline ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:20480',
            'additional_images' => 'nullable|array|max:10',
            'additional_images.*' => 'image|max:20480',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_orderable_online' => 'boolean',
            'is_available_for_collection' => 'boolean',
            'soonest_availability' => 'nullable|integer|min:1|max:365',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
            'option_groups' => 'nullable|array',
            'option_groups.*.id' => 'required|integer|distinct|exists:option_groups,id',
            'option_groups.*.min_selections' => 'required|integer|min:0|max:50',
            'option_groups.*.max_selections' => 'required|integer|min:1|max:50|gte:option_groups.*.min_selections',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = Storage::url(
                $request->file('image')->store('products', 'public')
            );
        } else {
            unset($validated['image']);
        }

        $images = $request->input('existing_images', []) ?? [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $file) {
                $images[] = Storage::url($file->store('products', 'public'));
            }
        }
        $validated['images'] = $images;

        $translations = $validated['translations'] ?? [];
        $optionGroups = $validated['option_groups'] ?? [];
        unset($validated['additional_images'], $validated['existing_images'], $validated['translations'], $validated['option_groups']);

        $product->update($validated);

        $this->saveTranslations($product, $translations);
        $this->syncOptionGroups($product, $optionGroups);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->translations()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    private function availableOptionGroups()
    {
        return OptionGroup::withCount('values')->orderBy('name')->get(['id', 'name']);
    }

    private function syncOptionGroups(Product $product, array $optionGroups): void
    {
        $product->optionGroups()->sync(
            collect($optionGroups)->mapWithKeys(fn ($g) => [
                $g['id'] => [
                    'min_selections' => $g['min_selections'],
                    'max_selections' => $g['max_selections'],
                ],
            ])->all()
        );
    }

    private function saveTranslations(Product $product, array $translations): void
    {
        foreach ($translations as $locale => $fields) {
            foreach ($fields as $field => $value) {
                if (in_array($field, Product::translatableFields())) {
                    ModelTranslation::updateOrCreate(
                        [
                            'translatable_type' => Product::class,
                            'translatable_id' => $product->id,
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
