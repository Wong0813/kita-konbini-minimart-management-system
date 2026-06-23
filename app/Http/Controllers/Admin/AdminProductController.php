<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductBatch;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'shelf_code'  => 'required|string|max:10',
            'stock'       => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $product = Product::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'cost_price'  => $request->cost_price ?? 0,
            'category_id' => $request->category_id,
            'shelf_code'  => $request->shelf_code,
            'stock'       => $request->stock,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        // Auto-create first batch if stock > 0
        if ($request->stock > 0) {
            ProductBatch::create([
                'product_id'    => $product->id,
                'quantity'      => $request->stock,
                'expiry_date'   => $request->expiry_date,
                'received_date' => now()->toDateString(),
                'notes'         => 'Initial stock',
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'shelf_code'  => 'required|string|max:10',
            'stock'       => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $product->update([
            'name'        => $request->name,
            'price'       => $request->price,
            'cost_price'  => $request->cost_price ?? 0,
            'category_id' => $request->category_id,
            'shelf_code'  => $request->shelf_code,
            'stock'       => $request->stock,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

public function inventory()
{
    $products     = Product::with('category')->get();
    $lowStock     = Product::where('stock', '<=', 3)->with('category')->get();
    $expiringSoon = Product::whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<=', now()->addDays(7))
                    ->whereDate('expiry_date', '>=', now())
                    ->with('category')->get();
    $expired      = Product::whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<', now())
                    ->with('category')->get();

    return view('admin.products.inventory', compact('products', 'lowStock', 'expiringSoon', 'expired'));
}

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate(['stock' => 'required|integer|min:0']);
        $product->update(['stock' => $request->stock]);

        return response()->json(['success' => true, 'stock' => $product->stock]);
    }

    // Add new restock batch
    public function addBatch(Request $request, Product $product)
    {
        $request->validate([
            'quantity'      => 'required|integer|min:1',
            'expiry_date'   => 'nullable|date',
            'received_date' => 'required|date',
            'notes'         => 'nullable|string|max:255',
        ]);

        ProductBatch::create([
            'product_id'    => $product->id,
            'quantity'      => $request->quantity,
            'expiry_date'   => $request->expiry_date,
            'received_date' => $request->received_date,
            'notes'         => $request->notes,
        ]);

        // Update product stock (add new batch quantity)
        $product->increment('stock', $request->quantity);

        // Update product expiry_date to earliest batch expiry
        $earliest = ProductBatch::where('product_id', $product->id)
                        ->whereNotNull('expiry_date')
                        ->where('quantity', '>', 0)
                        ->orderBy('expiry_date')
                        ->first();
        if ($earliest) {
            $product->update(['expiry_date' => $earliest->expiry_date]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Batch added. Stock updated to ' . $product->fresh()->stock,
        ]);
    }

    // Delete a batch
    public function deleteBatch(ProductBatch $batch)
    {
        $product = $batch->product;
        $qty     = $batch->quantity;
        $batch->delete();

        // Reduce stock accordingly
        $product->decrement('stock', $qty);
        if ($product->stock < 0) $product->update(['stock' => 0]);

        return response()->json(['success' => true]);
    }
public function restock(Request $request, Product $product)
{
    $request->validate([
        'cartons'          => 'required|integer|min:1',
        'units_per_carton' => 'required|integer|min:1',
    ]);

    $units = $request->cartons * $request->units_per_carton;

    $updateData = [
        'units_per_carton' => $request->units_per_carton,
        'stock'            => $product->stock + $units,
    ];

    // Update expiry date if provided
    if ($request->filled('expiry_date')) {
        $updateData['expiry_date'] = $request->expiry_date;
    }

    $product->update($updateData);

    // Send notification
    \App\Models\Notification::create([
        'user_id' => null,
        'type'    => 'stock',
        'title'   => 'Restock Done ✅',
        'message' => $product->name . ' restocked with ' . $request->cartons . ' carton(s) × ' . $request->units_per_carton . ' units = +' . $units . ' units added.',
        'icon'    => '📦',
    ]);

    return response()->json([
        'success'   => true,
        'new_stock' => $product->fresh()->stock,
        'units'     => $units,
    ]);
}
}
