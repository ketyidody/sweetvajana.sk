<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialOrder extends Model
{
    protected $fillable = [
        'product_id',
        'product_name',
        'customer_name',
        'customer_email',
        'customer_phone',
        'message',
        'choices',
        'status',
    ];

    protected $casts = [
        'choices' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
