<?php

namespace App\Delivery;

use App\Models\DeliveryMethod;
use Illuminate\Support\Collection;

class DeliveryManager
{
    /** @var Collection<string, class-string<DeliveryDriver>> */
    private Collection $drivers;

    public function __construct(array $driverClasses)
    {
        $this->drivers = collect($driverClasses)->keyBy(fn (string $class) => $class::key());
    }

    /** @return class-string<DeliveryDriver>|null */
    public function driverClass(string $key): ?string
    {
        return $this->drivers->get($key);
    }

    /**
     * Creates a disabled record for every registered driver that has none yet.
     */
    public function sync(): void
    {
        $position = (int) DeliveryMethod::max('position');

        foreach ($this->drivers as $key => $class) {
            DeliveryMethod::firstOrCreate(['key' => $key], [
                'name' => $class::defaultName(),
                'price' => 0,
                'is_active' => false,
                'position' => ++$position,
            ]);
        }
    }

    /** @return Collection<int, DeliveryDriver> */
    public function available(): Collection
    {
        return DeliveryMethod::query()
            ->where('is_active', true)
            ->whereIn('key', $this->drivers->keys())
            ->withTranslations()
            ->orderBy('position')
            ->get()
            ->map(fn (DeliveryMethod $method) => $method->driver())
            ->filter(fn (DeliveryDriver $driver) => $driver->isConfigured())
            ->values();
    }

    public function findAvailable(?string $key): ?DeliveryDriver
    {
        return $this->available()->first(fn (DeliveryDriver $driver) => $driver::key() === $key);
    }

    public function find(?string $key): ?DeliveryDriver
    {
        if (! $key || ! $this->drivers->has($key)) {
            return null;
        }

        return DeliveryMethod::where('key', $key)->withTranslations()->first()?->driver();
    }
}
