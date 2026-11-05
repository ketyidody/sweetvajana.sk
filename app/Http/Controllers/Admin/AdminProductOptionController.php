<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addition;
use App\Models\Corpus;
use App\Models\CreamFlavor;
use App\Models\Language;
use App\Models\ModelTranslation;
use App\Models\OptionGroup;
use App\Models\OptionGroupValue;
use App\Models\Size;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminProductOptionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ProductOptions/Index', [
            'languages' => Language::getActive(),
            'defaultLocale' => Language::getDefault()?->code ?? 'sk',
            'corpuses' => Corpus::with('translations')->get()->map(fn ($c) => $this->formatOption($c)),
            'creamFlavors' => CreamFlavor::with('translations')->get()->map(fn ($c) => $this->formatOption($c)),
            'additions' => Addition::with('translations')->get()->map(fn ($a) => array_merge($this->formatOption($a), ['price' => $a->price])),
            'sizes' => Size::with('translations')->get()->map(fn ($s) => $this->formatOption($s)),
            'optionGroups' => OptionGroup::with(['translations', 'values.translations'])->get()->map(fn ($g) => array_merge(
                $this->formatOption($g),
                ['values' => $g->values->map(fn ($v) => $this->formatOption($v))],
            )),
        ]);
    }

    // --- Corpus ---

    public function storeCorpus(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $corpus = Corpus::create(['name' => $validated['name']]);
        $this->saveTranslations($corpus, Corpus::class, $validated['translations'] ?? [], Corpus::translatableFields());

        return back()->with('success', 'Corpus added.');
    }

    public function updateCorpus(Request $request, Corpus $corpus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $corpus->update(['name' => $validated['name']]);
        $this->saveTranslations($corpus, Corpus::class, $validated['translations'] ?? [], Corpus::translatableFields());

        return back()->with('success', 'Corpus updated.');
    }

    public function destroyCorpus(Corpus $corpus)
    {
        $corpus->translations()->delete();
        $corpus->delete();

        return back()->with('success', 'Corpus deleted.');
    }

    // --- Cream Flavor ---

    public function storeCreamFlavor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $creamFlavor = CreamFlavor::create(['name' => $validated['name']]);
        $this->saveTranslations($creamFlavor, CreamFlavor::class, $validated['translations'] ?? [], CreamFlavor::translatableFields());

        return back()->with('success', 'Cream flavor added.');
    }

    public function updateCreamFlavor(Request $request, CreamFlavor $creamFlavor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $creamFlavor->update(['name' => $validated['name']]);
        $this->saveTranslations($creamFlavor, CreamFlavor::class, $validated['translations'] ?? [], CreamFlavor::translatableFields());

        return back()->with('success', 'Cream flavor updated.');
    }

    public function destroyCreamFlavor(CreamFlavor $creamFlavor)
    {
        $creamFlavor->translations()->delete();
        $creamFlavor->delete();

        return back()->with('success', 'Cream flavor deleted.');
    }

    // --- Addition ---

    public function storeAddition(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $addition = Addition::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
        ]);
        $this->saveTranslations($addition, Addition::class, $validated['translations'] ?? [], Addition::translatableFields());

        return back()->with('success', 'Addition added.');
    }

    public function updateAddition(Request $request, Addition $addition)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $addition->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
        ]);
        $this->saveTranslations($addition, Addition::class, $validated['translations'] ?? [], Addition::translatableFields());

        return back()->with('success', 'Addition updated.');
    }

    public function destroyAddition(Addition $addition)
    {
        $addition->translations()->delete();
        $addition->delete();

        return back()->with('success', 'Addition deleted.');
    }

    // --- Size ---

    public function storeSize(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $size = Size::create(['name' => $validated['name']]);
        $this->saveTranslations($size, Size::class, $validated['translations'] ?? [], Size::translatableFields());

        return back()->with('success', 'Size added.');
    }

    public function updateSize(Request $request, Size $size)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $size->update(['name' => $validated['name']]);
        $this->saveTranslations($size, Size::class, $validated['translations'] ?? [], Size::translatableFields());

        return back()->with('success', 'Size updated.');
    }

    public function destroySize(Size $size)
    {
        $size->translations()->delete();
        $size->delete();

        return back()->with('success', 'Size deleted.');
    }

    // --- Option Group ---

    public function storeOptionGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $group = OptionGroup::create(['name' => $validated['name']]);
        $this->saveTranslations($group, OptionGroup::class, $validated['translations'] ?? [], OptionGroup::translatableFields());

        return back()->with('success', 'Option group added.');
    }

    public function updateOptionGroup(Request $request, OptionGroup $optionGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $optionGroup->update(['name' => $validated['name']]);
        $this->saveTranslations($optionGroup, OptionGroup::class, $validated['translations'] ?? [], OptionGroup::translatableFields());

        return back()->with('success', 'Option group updated.');
    }

    public function destroyOptionGroup(OptionGroup $optionGroup)
    {
        foreach ($optionGroup->values as $value) {
            $value->translations()->delete();
        }
        $optionGroup->translations()->delete();
        $optionGroup->delete();

        return back()->with('success', 'Option group deleted.');
    }

    // --- Option Group Value ---

    public function storeOptionGroupValue(Request $request, OptionGroup $optionGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $value = $optionGroup->values()->create(['name' => $validated['name']]);
        $this->saveTranslations($value, OptionGroupValue::class, $validated['translations'] ?? [], OptionGroupValue::translatableFields());

        return back()->with('success', 'Option added.');
    }

    public function updateOptionGroupValue(Request $request, OptionGroupValue $optionGroupValue)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $optionGroupValue->update(['name' => $validated['name']]);
        $this->saveTranslations($optionGroupValue, OptionGroupValue::class, $validated['translations'] ?? [], OptionGroupValue::translatableFields());

        return back()->with('success', 'Option updated.');
    }

    public function destroyOptionGroupValue(OptionGroupValue $optionGroupValue)
    {
        $optionGroupValue->translations()->delete();
        $optionGroupValue->delete();

        return back()->with('success', 'Option deleted.');
    }

    // --- Helpers ---

    private function formatOption($option): array
    {
        $translations = [];
        foreach ($option->translations as $t) {
            $translations[$t->locale][$t->field] = $t->value;
        }

        return [
            'id' => $option->id,
            'name' => $option->name,
            'translations' => $translations,
        ];
    }

    private function saveTranslations($model, string $modelClass, array $translations, array $translatableFields): void
    {
        foreach ($translations as $locale => $fields) {
            foreach ($fields as $field => $value) {
                if (in_array($field, $translatableFields)) {
                    ModelTranslation::updateOrCreate(
                        [
                            'translatable_type' => $modelClass,
                            'translatable_id' => $model->id,
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
