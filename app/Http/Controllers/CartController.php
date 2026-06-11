<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Show cart
    public function index()
    {
        $cart     = session('cart', []);
        $items    = [];
        $total    = 0;

        foreach ($cart as $id => $qty) {
            $product = Product::find($id);
            if ($product) {
                $subtotal = $product->price * $qty;
                $total   += $subtotal;
                $items[]  = [
                    'product'  => $product,
                    'qty'      => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        return view('cart.index', compact('items', 'total'));
    }

    // Add to cart (AJAX or redirect)
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart    = session('cart', []);

        if (isset($cart[$id])) {
            // Don't exceed stock
            $cart[$id] = min($cart[$id] + 1, $product->stock);
        } else {
            $cart[$id] = 1;
        }

        session(['cart' => $cart]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count'   => count($cart),
                'message' => $product->name . ' ditambah ke troli.',
            ]);
        }

        return back()->with('success', $product->name . ' ditambah ke troli.');
    }

    // Update quantity
    public function update(Request $request, $id)
    {
        $request->validate(['qty' => 'required|integer|min:0']);

        $cart = session('cart', []);
        $qty  = (int) $request->qty;

        if ($qty <= 0) {
            unset($cart[$id]);
        } else {
            $product  = Product::findOrFail($id);
            $cart[$id] = min($qty, $product->stock);
        }

        session(['cart' => $cart]);

        // Recalculate totals
        $total = 0;
        foreach ($cart as $pid => $q) {
            $p = Product::find($pid);
            if ($p) $total += $p->price * $q;
        }

        return response()->json([
            'success' => true,
            'count'   => count($cart),
            'total'   => number_format($total, 2),
        ]);
    }

    // Remove item
    public function remove(Request $request, $id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'count'   => count($cart),
        ]);
    }

    // Checkout
    public function checkout(Request $request)
{
    $cart = session('cart', []);

    if (empty($cart)) {
        return back()->with('error', 'Troli anda kosong.');
    }

    $total = 0;
    $items = [];

    foreach ($cart as $id => $qty) {
        $product = \App\Models\Product::find($id);
        if ($product) {
            $subtotal = $product->price * $qty;
            $total += $subtotal;
            $items[] = [
                'product_id' => $product->id,
                'quantity'   => $qty,
                'price'      => $product->price,
            ];
            $product->decrement('stock', $qty);
        }
    }

    // Save order to database
    $order = \App\Models\Order::create([
        'user_id' => auth()->id(),
        'total'   => $total,
        'status'  => 'completed',
    ]);

    foreach ($items as $item) {
        $order->items()->create($item);
    }

    session()->forget('cart');

    return redirect()->route('home')
        ->with('success', 'Pesanan berjaya! Terima kasih kerana membeli-belah di Kita Konbini. 🎉');
   }
}
