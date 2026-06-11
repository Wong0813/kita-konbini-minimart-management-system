@extends('layouts.app')

@section('title', 'My Cart - Kita Konbini')

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>My <span>Cart</span></h1>
        <p>Review your items before checkout.</p>
    </div>
</section>

<section class="section">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(empty($items))
        <div style="text-align:center; padding: 40px 0;">
            <div style="font-size:64px; margin-bottom:16px;">🛒</div>
            <h3 style="font-weight:800; margin-bottom:8px;">Your cart is empty</h3>
            <p style="color:var(--gray-text); margin-bottom:24px;">Add some products first!</p>
            <a href="{{ route('products.index') }}" class="btn-primary" style="display:inline-block; width:auto; padding:12px 32px;">Shop Now</a>
        </div>
    @else
        {{-- Cart Items --}}
        <div class="cart-items">
            @foreach($items as $item)
            <div class="cart-item" id="cart-item-{{ $item['product']->id }}">
                <div class="cart-item-emoji">🛒</div>
                <div class="cart-item-info">
                    <div class="cart-item-name">{{ $item['product']->name }}</div>
                    <div class="cart-item-price">{{ $item['product']->price_formatted }}</div>
                    <div class="cart-item-meta">Shelf: {{ $item['product']->shelf_code }}</div>
                    <div class="qty-control">
                        <span class="qty-label">Qty:</span>
                        <button class="qty-btn" onclick="updateQty({{ $item['product']->id }}, {{ $item['qty'] - 1 }})">−</button>
                        <span class="qty-value" id="qty-{{ $item['product']->id }}">{{ $item['qty'] }}</span>
                        <button class="qty-btn" onclick="updateQty({{ $item['product']->id }}, {{ $item['qty'] + 1 }})">+</button>
                        <button class="qty-btn" style="margin-left:8px; color:red; border-color:red;" onclick="removeItem({{ $item['product']->id }})">🗑</button>
                    </div>
                </div>
                <div style="font-weight:800; font-size:14px; color:var(--red-primary);">
                    RM {{ number_format($item['subtotal'], 2) }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Order Summary --}}
        <div class="order-details">
            <h3>Order Summary</h3>
            <div class="order-row">
                <span>Subtotal</span>
                <span id="cart-total">RM {{ number_format($total, 2) }}</span>
            </div>
            <div class="order-row">
                <span>Delivery</span>
                <span>Free</span>
            </div>
            <div class="order-total">
                <span>Total</span>
                <span id="cart-total-2">RM {{ number_format($total, 2) }}</span>
            </div>

            <form method="POST" action="{{ route('cart.checkout') }}">
                @csrf
                <button type="submit" class="checkout-btn">Checkout</button>
            </form>
        </div>
    @endif
</section>

@endsection

@push('scripts')
<script>
function updateQty(id, qty) {
    fetch('/cart/update/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ qty: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (qty <= 0) {
                document.getElementById('cart-item-' + id).remove();
            } else {
                document.getElementById('qty-' + id).textContent = qty;
            }
            document.getElementById('cart-total').textContent = 'RM ' + data.total;
            document.getElementById('cart-total-2').textContent = 'RM ' + data.total;
        }
    });
}

function removeItem(id) {
    fetch('/cart/remove/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-item-' + id).remove();
        }
    });
}
</script>
@endpush