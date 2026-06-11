@php use Illuminate\Support\Facades\Storage; @endphp

@extends('layouts.app')

@section('title', $category->name . ' - Kita Konbini')

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>{{ $category->icon }} <span>{{ $category->name }}</span></h1>
        <p>Browse all products in this category.</p>
    </div>
</section>

<section class="section">
    <h2 class="section-title">{{ $products->count() }} Products</h2>

    <div class="products-grid">
        @forelse($products as $product)
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
        @empty
            <div style="text-align:center; padding:40px 0; grid-column:span 2;">
                <p style="color:var(--gray-text);">No products in this category.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top:24px;">
        <a href="{{ route('home') }}" class="btn-primary" style="display:inline-block; width:auto; padding:12px 32px;">← Back to Home</a>
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