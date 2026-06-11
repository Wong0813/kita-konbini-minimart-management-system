@php use Illuminate\Support\Facades\Storage; @endphp

@extends('layouts.app')

@section('title', 'Home - Kita Konbini Minimart')

@section('content')

{{-- Hero --}}
<section class="hero" style="position:relative; overflow:hidden; min-height:320px; display:flex; align-items:center; justify-content:center;">
    {{-- Storefront background --}}
    <div style="position:absolute; inset:0;
        background:url('{{ asset('images/store/storefront.jpg') }}') center/cover no-repeat;
        opacity:0.55;">
    </div>
    {{-- Gradient overlay --}}
    <div style="position:absolute; inset:0;
        background:linear-gradient(135deg, rgba(192,57,43,0.75) 0%, rgba(123,36,28,0.85) 100%);">
    </div>
    {{-- Content --}}
    <div class="hero-content" style="position:relative; z-index:1;">
        <h1>Welcome to <span>Kita Konbini</span></h1>
        <p>Your one-stop minimart for snacks, beverages, instant food & stationery.</p>
        <a href="{{ route('products.index') }}" class="btn-primary" style="display:inline-block; width:auto; padding:14px 40px;">Shop Now</a>
    </div>
</section>
{{-- Categories --}}
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

{{-- Featured Products --}}
<section class="section">
    <h2 class="section-title">Featured Products</h2>
    <div class="products-grid">
        @foreach($recommended as $product)
        <div class="product-card" style="position:relative;">

            <button onclick="toggleWishlist({{ $product->id }}, this)"
                style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:20px; cursor:pointer; color:var(--gray-text); z-index:10;">
                ♡
            </button>

            <div class="product-placeholder">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
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
        @endforeach
    </div>
</section>

{{-- Shelf Locator CTA --}}
<section class="section shelf-cta">
    <div class="shelf-cta-content">
        <h2>Can't find what you're looking for?</h2>
        <p>Use our shelf locator to find products by shelf code.</p>
        <a href="{{ route('shelf.index') }}" class="btn-primary">Find on Shelf</a>
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleWishlist(id, btn) {
    fetch('/wishlist/toggle/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.textContent = data.status === 'added' ? '♥' : '♡';
            btn.style.color = data.status === 'added' ? 'var(--red-primary)' : 'var(--gray-text)';
        }
    });
}
</script>
@endpush