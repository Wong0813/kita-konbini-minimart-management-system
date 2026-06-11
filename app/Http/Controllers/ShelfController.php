<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ShelfController extends Controller
{
    public function index()
    {
        $sections = [
            'A' => ['name' => 'Beverage',    'color' => 'blue'],
            'B' => ['name' => 'Snacks',      'color' => 'orange'],
            'C' => ['name' => 'Instant Food','color' => 'pink'],
            'D' => ['name' => 'Stationery',  'color' => 'green'],
        ];

        $products = Product::all()->keyBy('shelf_code');

        return view('shelf.index', compact('sections', 'products'));
    }

    public function slotInfo($code)
    {
        $product = Product::with('category')->where('shelf_code', $code)->first();

        if (!$product) {
            return response()->json(['found' => false, 'code' => $code]);
        }

        return response()->json([
            'found'    => true,
            'id'       => $product->id,
            'name'     => $product->name,
            'category' => $product->category->name,
            'price'    => 'RM ' . number_format($product->price, 2),
            'code'     => $product->shelf_code,
            'stock'    => $product->stock,
            'image'    => $product->image ? Storage::url($product->image) : null,
        ]);
    }
}