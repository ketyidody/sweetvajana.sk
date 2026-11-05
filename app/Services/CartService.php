<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Normalize and validate the customer's option picks against the product's assigned groups.
     *
     * @return array<int, int[]> group id => sorted value ids
     *
     * @throws ValidationException
     */
    public function validateOptions(Product $product, array $input): array
    {
        $product->loadMissing('optionGroups.values');

        $normalized = [];
        $errors = [];

        foreach ($product->optionGroups as $group) {
            $validIds = $group->values->pluck('id')->all();
            $picked = collect($input[$group->id] ?? [])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values()
                ->all();

            if (array_diff($picked, $validIds)) {
                $errors["options.{$group->id}"] = 'Invalid option selected.';

                continue;
            }

            $min = min($group->pivot->min_selections, count($validIds));
            $max = $group->pivot->max_selections;

            if (count($picked) < $min || count($picked) > $max) {
                $errors["options.{$group->id}"] = $min === $max
                    ? "Please select {$max} option(s)."
                    : "Please select between {$min} and {$max} options.";

                continue;
            }

            if ($picked) {
                $normalized[$group->id] = $picked;
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return $normalized;
    }

    public function lineKey(int $productId, array $options): string
    {
        $valueIds = collect($options)->flatten()->sort()->values();

        return $valueIds->isEmpty() ? (string) $productId : $productId.'-'.$valueIds->implode('.');
    }

    /**
     * Resolve session cart entries into priced lines, dropping any that are no longer valid.
     *
     * @return Collection<int, array{key: string, product: Product, quantity: int, options: array, subtotal: float}>
     */
    public function lines(array $cart, bool $lockForUpdate = false): Collection
    {
        if (empty($cart)) {
            return collect();
        }

        $productIds = collect($cart)->map(fn ($item, $key) => $item['product_id'] ?? (int) $key)->unique();

        $query = Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->with(['translations', 'optionGroups.translations', 'optionGroups.values.translations']);

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $products = $query->get()->keyBy('id');

        $lines = collect();

        foreach ($cart as $key => $item) {
            // Legacy cart entries were keyed by product id without a product_id field.
            $product = $products->get($item['product_id'] ?? (int) $key);

            if (! $product) {
                continue;
            }

            try {
                $options = $this->validateOptions($product, $item['options'] ?? []);
            } catch (ValidationException) {
                continue;
            }

            $quantity = (int) $item['quantity'];

            $lines->push([
                'key' => (string) $key,
                'product' => $product,
                'quantity' => $quantity,
                'options' => $options,
                'subtotal' => $product->price * $quantity,
            ]);
        }

        return $lines;
    }

    /**
     * @return array<int, array{group: string, values: string[]}>
     */
    public function describeOptions(Product $product, array $options, ?string $locale = null): array
    {
        return $product->optionGroups
            ->filter(fn ($group) => isset($options[$group->id]))
            ->map(fn ($group) => [
                'group' => $group->translated('name', $locale),
                'values' => $group->values
                    ->whereIn('id', $options[$group->id])
                    ->map(fn ($value) => $value->translated('name', $locale))
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }
}
