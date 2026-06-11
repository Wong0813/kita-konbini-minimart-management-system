<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|unique:categories,slug',
            'icon'  => 'nullable|string|max:10',
            'color' => 'nullable|string|max:20',
        ]);

        Category::create($request->only('name', 'slug', 'icon', 'color'));

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category added.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }
}