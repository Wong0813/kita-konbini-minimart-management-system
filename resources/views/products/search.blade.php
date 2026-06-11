@extends('layouts.app')

@section('title', 'Search Results - Kita Konbini')

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>Search <span>Results</span></h1>
        <p>Showing results for: <strong>"{{ $q }}"</strong></p>
    </div>
</section>

<section class="section">
    <h2 class="section-title">{{ $products->count() }} result(s) found</h2>

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
            <div style="text-align:center; padding:40px 0; grid-column:span 2;">
                <div style="font-size:64px; margin-bottom:16px;">🔍</div>
                <h3 style="font-weight:800; margin-bottom:8px;">No results found</h3>
                <p style="color:var(--gray-text);">Try searching with a different keyword.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top:24px;">
        <a href="{{ route('home') }}" class="btn-primary" style="display:inline-block; width:auto; padding:12px 32px;">← Back to Home</a>
    </div>
</section>

@endsection