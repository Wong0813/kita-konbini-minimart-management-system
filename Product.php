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
        'category_id',
        'shelf_code',
        'stock',
        'is_featured',
        'image',
        'description',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    // Relationship: belongs to Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Format price as RM string
    public function getPriceFormattedAttribute(): string
    {
        return 'RM ' . number_format($this->price, 2);
    }
}
