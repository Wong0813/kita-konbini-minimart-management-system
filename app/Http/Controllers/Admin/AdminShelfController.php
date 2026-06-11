<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class AdminShelfController extends Controller
{
    public function index()
    {
        $sections = [
            'A' => ['name' => 'Beverage',    'color' => 'blue'],
            'B' => ['name' => 'Snacks',      'color' => 'orange'],
            'C' => ['name' => 'Instant Food','color' => 'pink'],
            'D' => ['name' => 'Stationery',  'color' => 'green'],
        ];

        $products  = Product::with('category')->get();
        $shelfMap  = Product::all()->keyBy('shelf_code');

        return view('admin.shelf.index', compact('sections', 'products', 'shelfMap'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'shelf_code' => 'required|string|max:10',
        ]);

        // Clear old slot if another product is there
        Product::where('shelf_code', $request->shelf_code)
            ->where('id', '!=', $request->product_id)
            ->update(['shelf_code' => null]);

        // Assign new slot
        $product = Product::findOrFail($request->product_id);
        $product->update(['shelf_code' => $request->shelf_code]);

        return response()->json([
            'success'     => true,
            'product'     => $product->name,
            'shelf_code'  => $request->shelf_code,
        ]);
    }

    public function clear(Request $request)
    {
        $request->validate(['shelf_code' => 'required|string']);

        Product::where('shelf_code', $request->shelf_code)
            ->update(['shelf_code' => null]);

        return response()->json(['success' => true]);
    }
}