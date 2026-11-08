<?php

namespace App\Models;

use App\Delivery\DeliveryDriver;
use App\Delivery\DeliveryManager;
use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    use HasTranslations;

    protected $fillable = ['key', 'name', 'description', 'price', 'is_active', 'position', 'settings'];

    protected $hidden = ['settings'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'settings' => 'encrypted:array',
    ];

    public static function translatableFields(): array
    {
        return ['name', 'description'];
    }

    public function driver(): ?DeliveryDriver
    {
        $class = app(DeliveryManager::class)->driverClass($this->key);

        return $class ? new $class($this) : null;
    }
}
