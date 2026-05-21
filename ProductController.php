<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // All products / featured
    public function index()
    {
        $products   = Product::with('category')->latest()->get();
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    // Search
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $products = Product::with('category')
            ->where('name', 'like', "%{$q}%")
            ->orWhere('shelf_code', 'like', "%{$q}%")
            ->get();

        return view('products.search', compact('products', 'q'));
    }

    // Show single product
    public function show($id)
    {
        $product  = Product::with('category')->findOrFail($id);
        $related  = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }

    // Products by category
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->get();

        return view('products.category', compact('category', 'products'));
    }

    // Wishlist page
    public function wishlist()
    {
        $wishlist = session('wishlist', []);
        $products = Product::whereIn('id', $wishlist)->get();
        return view('products.wishlist', compact('products'));
    }

    // Toggle wishlist (AJAX)
    public function toggleWishlist(Request $request, $id)
    {
        $wishlist = session('wishlist', []);

        if (in_array($id, $wishlist)) {
            $wishlist = array_diff($wishlist, [$id]);
            $status = 'removed';
        } else {
            $wishlist[] = $id;
            $status = 'added';
        }

        session(['wishlist' => array_values($wishlist)]);

        return response()->json([
            'success' => true,
            'status'  => $status,
            'count'   => count($wishlist),
        ]);
    }
}
