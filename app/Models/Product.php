<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'cost_price',
        'category_id',
        'shelf_code',
        'stock',
        'units_per_carton',
        'is_featured',
        'image',
        'description',
        'expiry_date',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'cost_price'       => 'decimal:2',
        'is_featured'      => 'boolean',
        'expiry_date'      => 'date',
        'units_per_carton' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'RM ' . number_format($this->price, 2);
    }
}