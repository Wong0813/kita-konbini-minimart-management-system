@extends('layouts.app')

@section('title', 'All Products - Kita Konbini')

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="hero-content">
        <h1>All <span>Products</span></h1>
        <p>Browse our full range of snacks, beverages, instant food & stationery.</p>
    </div>
</section>

{{-- Category Filter --}}
<section class="section">
    <h2 class="section-title">Categories</h2>
    <div class="categories-grid">
        @foreach($categories as $category)
        <a href="{{ route('products.category', $category->slug) }}" class="category-card" style="--cat-color: {{ $category->color }}">
            <span class="category-icon">{{ $category->icon }}</span>
            <span class="category-name">{{ $category->name }}</span>
        </a>
        @endforeach
    </div>
</section>

{{-- Products Grid --}}
<section class="section">
    <h2 class="section-title">All Products</h2>
    <div class="products-grid">
        @forelse($products as $product)
        <div class="product-card" style="position:relative;">

            {{-- Wishlist heart --}}
            <button onclick="toggleWishlist({{ $product->id }}, this)"
                style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:20px; cursor:pointer; color:var(--gray-text); z-index:10;">
                ♡
            </button>

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
            <p>No products found.</p>
        @endforelse
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