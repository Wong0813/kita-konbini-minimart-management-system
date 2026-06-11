@extends('layouts.app')

@section('title', 'My Wishlist - Kita Konbini')

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>My <span>Wishlist</span></h1>
        <p>Products you've saved for later.</p>
    </div>
</section>

<section class="section">

    @forelse($products as $product)
    <div class="wishlist-item" id="wish-{{ $product->id }}">

        {{-- Product Icon --}}
        <div class="wishlist-icon">🛒</div>

        {{-- Product Info --}}
        <div class="wishlist-info">
            <span class="product-category">{{ $product->category->name }}</span>
            <h3 class="product-name" style="font-size:15px;">{{ $product->name }}</h3>
            <p class="product-shelf">Shelf: {{ $product->shelf_code }}</p>
            <span class="product-price">{{ $product->price_formatted }}</span>
        </div>

        {{-- Actions --}}
        <div class="wishlist-actions">
            @if($product->stock > 0)
                <button class="add-btn" data-id="{{ $product->id }}" style="position:relative; bottom:auto; right:auto;">+</button>
            @else
                <span class="out-of-stock">Out of stock</span>
            @endif
            <button onclick="removeWishlist({{ $product->id }})"
                style="background:none; border:none; font-size:22px; cursor:pointer; color:var(--red-primary); display:block; margin-top:6px;"
                title="Remove from wishlist">♥</button>
        </div>

    </div>
    @empty
        <div style="text-align:center; padding:60px 0;">
            <div style="font-size:64px; margin-bottom:16px;">♡</div>
            <h3 style="font-weight:800; margin-bottom:8px;">Your wishlist is empty</h3>
            <p style="color:var(--gray-text); margin-bottom:24px;">Save products you like!</p>
            <a href="{{ route('home') }}" class="btn-primary" style="display:inline-block; width:auto; padding:12px 32px;">← Back to Home</a>
        </div>
    @endforelse

    @if($products->count() > 0)
    <div style="margin-top:24px; display:flex; gap:12px; flex-wrap:wrap;">
        <a href="{{ route('home') }}" class="btn-primary" style="display:inline-block; width:auto; padding:12px 32px;">← Back to Home</a>
        <a href="{{ route('products.index') }}" class="btn-primary" style="display:inline-block; width:auto; padding:12px 32px; background:var(--black);">Browse All Products</a>
    </div>
    @endif

</section>

@endsection

@push('scripts')
<script>
function removeWishlist(id) {
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
            const card = document.getElementById('wish-' + id);
            card.style.opacity = '0';
            card.style.transition = 'opacity 0.3s';
            setTimeout(() => card.remove(), 300);
        }
    });
}
</script>
@endpush