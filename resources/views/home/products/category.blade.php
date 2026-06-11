@extends('layouts.app')

@section('title', $category->name . ' - Kita Konbini')

@section('content')

<section class="section">
    <h2 class="section-title">
        {{ $category->icon }} {{ $category->name }}
    </h2>

    <div class="products-grid">
        @forelse($products as $product)
        <div class="product-card">
            <div class="product-image">
                @if($product->image)
                    <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <div class="product-placeholder">🛒</div>
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
            <p>No products in this category.</p>
        @endforelse
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('products.index') }}" class="btn-primary">← Back to All Products</a>
    </div>
</section>

@endsection 