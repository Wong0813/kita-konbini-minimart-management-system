<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ShelfController extends Controller
{
    public function index()
    {
        // Build shelf grid: A=Beverage, B=Snacks, C=Instant Food, D=Stationery
        $sections = [
            'A' => ['name' => 'Beverage',     'color' => 'blue'],
            'B' => ['name' => 'Snacks',        'color' => 'orange'],
            'C' => ['name' => 'Instant Food',  'color' => 'pink'],
            'D' => ['name' => 'Stationery',    'color' => 'green'],
        ];

        // Get all products mapped by shelf_code
        $products = Product::all()->keyBy('shelf_code');

        return view('shelf.index', compact('sections', 'products'));
    }

    // AJAX: get product info for a shelf slot
    public function slotInfo($code)
    {
        $product = Product::where('shelf_code', $code)->first();

        if (!$product) {
            return response()->json(['found' => false, 'code' => $code]);
        }

        return response()->json([
            'found'   => true,
            'id'      => $product->id,
            'name'    => $product->name,
            'price'   => 'RM ' . number_format($product->price, 2),
            'code'    => $product->shelf_code,
            'stock'   => $product->stock,
        ]);
    }
}
