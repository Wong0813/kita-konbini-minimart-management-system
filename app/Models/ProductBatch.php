<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'expiry_date',
        'received_date',
        'notes',
    ];

    protected $casts = [
        'expiry_date'   => 'date',
        'received_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusAttribute(): string
    {
        if (!$this->expiry_date) return 'no-expiry';
        if ($this->expiry_date->isPast()) return 'expired';
        if ($this->expiry_date->diffInDays(now()) <= 7) return 'expiring-soon';
        return 'good';
    }
}
