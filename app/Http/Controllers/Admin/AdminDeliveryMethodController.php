<?php

namespace App\Http\Controllers\Admin;

use App\Delivery\DeliveryManager;
use App\Http\Controllers\Controller;
use App\Models\DeliveryMethod;
use App\Models\Language;
use App\Models\ModelTranslation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AdminDeliveryMethodController extends Controller
{
    public function index(DeliveryManager $delivery)
    {
        $delivery->sync();

        $methods = DeliveryMethod::orderBy('position')->get()
            ->filter(fn (DeliveryMethod $method) => $method->driver())
            ->map(fn (DeliveryMethod $method) => [
                'id' => $method->id,
                'key' => $method->key,
                'name' => $method->name,
                'price' => $method->price,
                'is_active' => $method->is_active,
                'is_configured' => $method->driver()->isConfigured(),
                'requires_address' => $method->driver()::requiresAddress(),
            ])
            ->values();

        return Inertia::render('Admin/DeliveryMethods/Index', ['methods' => $methods]);
    }

    public function edit(DeliveryMethod $deliveryMethod)
    {
        $driver = $deliveryMethod->driver() ?? abort(404);
        $deliveryMethod->load('translations');

        $translations = [];
        foreach ($deliveryMethod->translations as $t) {
            $translations[$t->locale][$t->field] = $t->value;
        }

        $fields = collect($driver::settingsFields())->map(fn ($field) => [
            ...$field,
            // Secrets are never sent back to the browser.
            'value' => $field['type'] === 'password' ? '' : ($driver->setting($field['key']) ?? ''),
            'has_value' => filled($driver->setting($field['key'])),
        ]);

        return Inertia::render('Admin/DeliveryMethods/Form', [
            'method' => [
                'id' => $deliveryMethod->id,
                'key' => $deliveryMethod->key,
                'name' => $deliveryMethod->name,
                'description' => $deliveryMethod->description,
                'price' => $deliveryMethod->price,
                'is_active' => $deliveryMethod->is_active,
                'requires_address' => $driver::requiresAddress(),
            ],
            'methodTranslations' => $translations,
            'settingsFields' => $fields,
            'languages' => Language::getActive(),
            'defaultLocale' => Language::getDefault()?->code ?? 'sk',
        ]);
    }

    public function update(Request $request, DeliveryMethod $deliveryMethod)
    {
        $driver = $deliveryMethod->driver() ?? abort(404);
        $fields = collect($driver::settingsFields());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0|max:99999',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string|max:2000',
            'settings' => 'nullable|array',
            'settings.*' => 'nullable|string|max:2000',
        ]);

        $settings = $deliveryMethod->settings ?? [];
        foreach ($fields as $field) {
            $value = $validated['settings'][$field['key']] ?? null;

            // An empty password field keeps the stored secret.
            if ($field['type'] === 'password' && blank($value)) {
                continue;
            }

            $settings[$field['key']] = $value;
        }

        if ($validated['is_active'] ?? false) {
            $missing = $fields
                ->filter(fn ($field) => ($field['required'] ?? false) && blank($settings[$field['key']] ?? null))
                ->mapWithKeys(fn ($field) => ["settings.{$field['key']}" => "{$field['label']} is required to enable this delivery method."]);

            if ($missing->isNotEmpty()) {
                throw ValidationException::withMessages($missing->all());
            }
        }

        $deliveryMethod->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? false,
            'settings' => $settings,
        ]);

        foreach ($validated['translations'] ?? [] as $locale => $values) {
            foreach (DeliveryMethod::translatableFields() as $field) {
                if (array_key_exists($field, $values)) {
                    ModelTranslation::updateOrCreate(
                        [
                            'translatable_type' => DeliveryMethod::class,
                            'translatable_id' => $deliveryMethod->id,
                            'locale' => $locale,
                            'field' => $field,
                        ],
                        ['value' => $values[$field] ?? '']
                    );
                }
            }
        }

        return redirect()->route('admin.delivery-methods.index')->with('success', 'Delivery method updated.');
    }

    public function toggle(DeliveryMethod $deliveryMethod)
    {
        $driver = $deliveryMethod->driver() ?? abort(404);

        if (! $deliveryMethod->is_active && ! $driver->isConfigured()) {
            return back()->with('error', 'Fill in the required settings before enabling this delivery method.');
        }

        $deliveryMethod->update(['is_active' => ! $deliveryMethod->is_active]);

        return back()->with('success', $deliveryMethod->is_active ? 'Delivery method enabled.' : 'Delivery method disabled.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:delivery_methods,id',
        ]);

        foreach ($validated['ids'] as $position => $id) {
            DeliveryMethod::where('id', $id)->update(['position' => $position + 1]);
        }

        return back();
    }
}
