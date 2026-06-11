<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $recommended = Product::where('is_featured', true)
            ->orWhereIn('id', [1, 2, 3, 4, 5, 6])
            ->take(6)
            ->get();

        return view('home.index', compact('categories', 'recommended'));
    }
}
