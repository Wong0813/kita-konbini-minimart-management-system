@extends('layouts.app')

@section('title', 'All Products - Kita Konbini')

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>All <span>Products</span></h1>
        <p>Browse our full range of snacks, beverages, instant food & stationery.</p>
    </div>
</section>

<section class="section">
    <h2 class="section-title">Categories</h2>
    <div class="categories-grid">
        @foreach($categories as $category)
        <a href="{{ route('products.category', $category->slug) }}" class="category-card">
            <span class="category-icon">{{ $category->icon }}</span>
            <span class="category-name">{{ $category->name }}</span>
        </a>
        @endforeach
    </div>
</section>

<section class="section">
    <h2 class="section-title">All Products</h2>
    <div class="products-grid">
        @forelse($products as $product)
        <div class="product-card">
            <div class="product-placeholder">
    @if($product->image)
        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
            style="width:100%; height:100%; object-fit:cover; border-radius:10px;">
    @else
        🛒
    @endif
</div>
            <div class="product-info">
                <span class="product-category">{{ $product->category->name }}</span>
                <h3 class="product-name">{{ $product->name }}</h3>
                <p class="product-shelf">Shelf: {{ $product->shelf_code }}</p>
                <div class="product-footer">
                    <span class="product-price">{{ $product->price_formatted }}</span>
                    @if($product->stock > 0)
                        <button class="add-btn" data-id="{{ $product->id }}">+</button>
                    @else
                        <span class="out-of-stock">Out of stock</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
            <p>No products found.</p>
        @endforelse
    </div>
</section>

@endsection