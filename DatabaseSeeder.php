<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Demo User ──────────────────────────────────────────────────────
        User::create([
            'name'      => 'Demo User',
            'matric_id' => 'A12345',
            'email'     => 'demo@student.edu.my',
            'password'  => Hash::make('password'),
        ]);

        // ── Categories ─────────────────────────────────────────────────────
        $beverage   = Category::create(['name' => 'Beverage',     'slug' => 'beverage',     'icon' => '🥤', 'color' => '#2563EB']);
        $snacks     = Category::create(['name' => 'Snacks',       'slug' => 'snacks',       'icon' => '🍟', 'color' => '#F59E0B']);
        $instantFood= Category::create(['name' => 'Instant Food', 'slug' => 'instant-food', 'icon' => '🍜', 'color' => '#EF4444']);
        $stationery = Category::create(['name' => 'Stationery',   'slug' => 'stationery',   'icon' => '✏️', 'color' => '#10B981']);

        // ── Products ───────────────────────────────────────────────────────

        // Beverages
        Product::create(['name' => '100 Plus Can Drink',         'price' => 2.00, 'category_id' => $beverage->id,    'shelf_code' => 'A1',  'stock' => 9,  'is_featured' => true]);
        Product::create(['name' => 'Oishi Green Tea Honey Lemon','price' => 2.30, 'category_id' => $beverage->id,    'shelf_code' => 'A3',  'stock' => 3,  'is_featured' => true]);
        Product::create(['name' => 'Milo Can 240ml',             'price' => 2.50, 'category_id' => $beverage->id,    'shelf_code' => 'A2',  'stock' => 7]);
        Product::create(['name' => 'Teh Botol Sosro',            'price' => 1.80, 'category_id' => $beverage->id,    'shelf_code' => 'A4',  'stock' => 5]);
        Product::create(['name' => 'Mineral Water 500ml',        'price' => 0.90, 'category_id' => $beverage->id,    'shelf_code' => 'A5',  'stock' => 20]);
        Product::create(['name' => 'Pokka Green Tea',            'price' => 2.00, 'category_id' => $beverage->id,    'shelf_code' => 'A6',  'stock' => 6]);

        // Snacks
        Product::create(['name' => 'Corntoz Chilli Cheez',       'price' => 2.50, 'category_id' => $snacks->id,      'shelf_code' => 'B3',  'stock' => 5,  'is_featured' => true]);
        Product::create(['name' => 'Cadbury Dairy Milk Choc 160g','price'=> 3.50, 'category_id' => $snacks->id,      'shelf_code' => 'B4',  'stock' => 4,  'is_featured' => true]);
        Product::create(['name' => 'Roller Coaster Cheese',      'price' => 2.50, 'category_id' => $snacks->id,      'shelf_code' => 'B7',  'stock' => 3,  'is_featured' => true]);
        Product::create(['name' => "Lay's Classic Chips",        'price' => 3.20, 'category_id' => $snacks->id,      'shelf_code' => 'B1',  'stock' => 8]);
        Product::create(['name' => 'Oreo Original',              'price' => 2.90, 'category_id' => $snacks->id,      'shelf_code' => 'B2',  'stock' => 6]);
        Product::create(['name' => 'KitKat 2 Fingers',           'price' => 1.50, 'category_id' => $snacks->id,      'shelf_code' => 'B5',  'stock' => 10]);

        // Instant Food
        Product::create(['name' => 'Samyang Buldak Spicy Ramen', 'price' => 4.50, 'category_id' => $instantFood->id, 'shelf_code' => 'C1',  'stock' => 12, 'is_featured' => true]);
        Product::create(['name' => 'Maggi Kari 5-pack',          'price' => 5.90, 'category_id' => $instantFood->id, 'shelf_code' => 'C2',  'stock' => 8]);
        Product::create(['name' => 'Indomie Goreng',             'price' => 1.20, 'category_id' => $instantFood->id, 'shelf_code' => 'C3',  'stock' => 15]);
        Product::create(['name' => 'Cup Noodles Seafood',        'price' => 2.80, 'category_id' => $instantFood->id, 'shelf_code' => 'C4',  'stock' => 7]);

        // Stationery
        Product::create(['name' => 'Small Stapler',              'price' => 4.90, 'category_id' => $stationery->id,  'shelf_code' => 'D2',  'stock' => 2,  'is_featured' => true]);
        Product::create(['name' => 'Paper Clip Binder',          'price' => 1.00, 'category_id' => $stationery->id,  'shelf_code' => 'D1',  'stock' => 7,  'is_featured' => true]);
        Product::create(['name' => 'Adhesive Tape Transparent',  'price' => 1.90, 'category_id' => $stationery->id,  'shelf_code' => 'D4',  'stock' => 4,  'is_featured' => true]);
        Product::create(['name' => 'Pen Blue (box of 10)',       'price' => 5.50, 'category_id' => $stationery->id,  'shelf_code' => 'D3',  'stock' => 5]);
        Product::create(['name' => 'A4 Paper 80gsm (100 sheets)','price' => 4.00, 'category_id' => $stationery->id,  'shelf_code' => 'D5',  'stock' => 6]);
        Product::create(['name' => 'Pencil 2B (12 pcs)',         'price' => 3.00, 'category_id' => $stationery->id,  'shelf_code' => 'D6',  'stock' => 9]);
    }
}
