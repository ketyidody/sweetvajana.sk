<?php

return [

    /*
    | Delivery drivers available to the shop. Each class must extend
    | App\Delivery\DeliveryDriver; admins enable and configure them
    | under Admin → Delivery.
    */

    'drivers' => [
        App\Delivery\StorePickup::class,
    ],

];
