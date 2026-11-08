<?php

namespace App\Delivery;

use App\Models\DeliveryMethod;

abstract class DeliveryDriver
{
    public function __construct(protected DeliveryMethod $method) {}

    abstract public static function key(): string;

    abstract public static function defaultName(): string;

    public static function requiresAddress(): bool
    {
        return false;
    }

    /**
     * Driver-specific settings shown in the admin, e.g. API credentials.
     *
     * @return array<int, array{key: string, label: string, type: 'text'|'textarea'|'password', required?: bool, help?: string}>
     */
    public static function settingsFields(): array
    {
        return [];
    }

    public function method(): DeliveryMethod
    {
        return $this->method;
    }

    public function name(?string $locale = null): string
    {
        return $this->method->translated('name', $locale) ?: static::defaultName();
    }

    public function description(?string $locale = null): ?string
    {
        return $this->method->translated('description', $locale) ?: null;
    }

    public function price(float $subtotal): float
    {
        return (float) $this->method->price;
    }

    public function details(): ?string
    {
        return null;
    }

    public function setting(string $key): mixed
    {
        return $this->method->settings[$key] ?? null;
    }

    public function isConfigured(): bool
    {
        return collect(static::settingsFields())
            ->filter(fn ($field) => $field['required'] ?? false)
            ->every(fn ($field) => filled($this->setting($field['key'])));
    }

    public function toArray(float $subtotal): array
    {
        return [
            'key' => static::key(),
            'name' => $this->name(),
            'description' => $this->description(),
            'details' => $this->details(),
            'requires_address' => static::requiresAddress(),
            'price' => number_format($this->price($subtotal), 2, '.', ''),
        ];
    }
}
