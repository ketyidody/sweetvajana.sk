<?php

namespace App\Delivery;

use App\Models\SiteSetting;

class StorePickup extends DeliveryDriver
{
    public static function key(): string
    {
        return 'store_pickup';
    }

    public static function defaultName(): string
    {
        return 'Osobný odber';
    }

    public static function settingsFields(): array
    {
        return [
            [
                'key' => 'address',
                'label' => 'Pickup address',
                'type' => 'textarea',
                'help' => 'Shown to customers at checkout. Falls back to the invoice seller address when empty.',
            ],
        ];
    }

    public function details(): ?string
    {
        return $this->setting('address') ?: SiteSetting::get('invoice_seller_address') ?: null;
    }
}
