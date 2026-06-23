@extends('layouts.app')

@section('title', 'My Cart - Kita Konbini')

@section('content')

<style>
/* ── Variables ── */
:root {
    --red: #C0392B;
    --red-dark: #922B21;
    --green: #16A34A;
    --border: #EFEFEF;
    --muted: #9CA3AF;
    --sans: 'Inter', system-ui, sans-serif;
    --mono: 'JetBrains Mono', 'Fira Mono', monospace;
}

/* ── Payment method tabs ── */
.pay-methods { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:16px; }
.pm { border:1.5px solid var(--border); border-radius:10px; padding:10px 6px; text-align:center; cursor:pointer; transition:all .15s; }
.pm:hover { border-color:var(--red); background:#FDF2F0; }
.pm.active { border-color:var(--red); background:#FDF2F0; }
.pm-icon { font-size:22px; display:block; margin-bottom:4px; }
.pm-label { font-size:11px; font-weight:600; }

/* ── Card visual preview ── */
.card-vis {
    background: linear-gradient(135deg,#1a1a2e 0%,#16213e 60%,#0f3460 100%);
    border-radius:14px; padding:18px; margin-bottom:16px; color:white; position:relative; min-height:130px;
}
.cv-chip { width:32px; height:24px; background:linear-gradient(135deg,#d4a843,#f0c040); border-radius:5px; margin-bottom:14px; }
.cv-num { font-family:var(--mono); font-size:15px; letter-spacing:.18em; margin-bottom:14px; }
.cv-bottom { display:flex; justify-content:space-between; font-size:10px; opacity:.75; }
.cv-holder, .cv-exp { display:flex; flex-direction:column; gap:2px; }
.cv-holder span, .cv-exp span { font-size:12px; font-weight:600; opacity:1; color:white; font-family:var(--mono); letter-spacing:.05em; }
.cv-brand { position:absolute; top:16px; right:16px; font-size:18px; font-weight:900; letter-spacing:-.02em; }

/* ── Card type badges ── */
.card-types { display:flex; gap:6px; margin-bottom:12px; }
.ct { font-size:10px; font-weight:800; border:1.5px solid var(--border); border-radius:5px; padding:3px 8px; cursor:pointer; color:var(--muted); transition:all .15s; }
.ct.active { border-color:var(--red); color:var(--red); background:#FDF2F0; }

/* ── Form fields ── */
.field { margin-bottom:12px; }
.field label { display:block; font-size:11px; font-weight:600; color:var(--muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:.06em; }
.field input, .field select {
    width:100%; height:40px; border:1.5px solid var(--border); border-radius:8px;
    padding:0 12px; font-size:13px; font-family:var(--sans); box-sizing:border-box;
    transition:border-color .15s; outline:none; background:white;
}
.field input:focus, .field select:focus { border-color:var(--red); }
.field-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.errmsg { font-size:11px; color:var(--red); margin-top:4px; display:none; }
.errmsg.show { display:block; }

/* ── Bank grid (FPX style) ── */
.bank-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:14px; }
.bank-tile {
    border:1.5px solid var(--border); border-radius:10px; padding:10px 6px;
    text-align:center; cursor:pointer; transition:all .15s; background:white;
}
.bank-tile:hover  { border-color:#1A6CB5; background:#F0F6FF; }
.bank-tile.selected { border-color:#1A6CB5; background:#EBF3FF; box-shadow:0 0 0 2px #1A6CB5; }
.bank-tile img  { width:44px; height:28px; object-fit:contain; display:block; margin:0 auto 5px; }
.bank-tile span { font-size:9.5px; font-weight:600; color:#555; line-height:1.2; display:block; }
.fpx-badge {
    display:flex; align-items:center; gap:6px; background:#F7F8FA;
    border:1px solid var(--border); border-radius:8px; padding:7px 10px; margin-bottom:12px; font-size:11px; color:var(--muted);
}
.fpx-badge strong { color:#1A6CB5; }
.bank-info { background:#F7F7F7; border-radius:10px; padding:12px; margin:10px 0; }
.bank-row { display:flex; justify-content:space-between; font-size:12px; padding:4px 0; border-bottom:1px solid var(--border); }
.bank-row:last-child { border-bottom:none; }
.bank-row span:last-child { font-weight:700; }

/* ── E-wallet grid ── */
.ewallet-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:14px; }
.ew-item {
    border:1.5px solid var(--border); border-radius:12px; padding:12px 8px;
    text-align:center; cursor:pointer; font-size:12px; font-weight:600; transition:all .15s; background:white;
}
.ew-item:hover  { border-color:#444; }
.ew-item.selected { border-color:#444; box-shadow:0 0 0 2px rgba(0,0,0,.12); }
.ew-logo { width:52px; height:32px; object-fit:contain; display:block; margin:0 auto 5px; }
.ew-name { font-size:10px; font-weight:700; color:#333; }

/* ── Coupon ── */
.coupon-row { display:flex; gap:8px; margin-top:12px; }
.coupon-row input { flex:1; height:36px; border:1.5px solid var(--border); border-radius:8px; padding:0 10px; font-size:13px; outline:none; font-family:var(--sans); }
.coupon-row input:focus { border-color:var(--red); }
.coupon-row button { height:36px; padding:0 14px; background:var(--red); color:white; border:none; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; }
.coupon-msg { font-size:11px; margin-top:4px; min-height:16px; }
.coupon-msg.ok { color:var(--green); }
.coupon-msg.err { color:var(--red); }

/* ── Summary rows ── */
.s-row { display:flex; justify-content:space-between; font-size:13px; padding:5px 0; }
.s-row span:first-child { color:var(--muted); }
.s-row.discount span:last-child { color:var(--green); font-weight:700; }
.s-divider { border:none; border-top:1px solid var(--border); margin:10px 0; }
.s-total { display:flex; justify-content:space-between; font-size:16px; font-weight:800; padding:4px 0; }
.s-total span:last-child { color:var(--red); font-family:var(--mono); }

/* ── Buttons ── */
.checkout-btn {
    display:flex; align-items:center; justify-content:center; gap:8px;
    width:100%; height:50px; background:var(--red); color:white; border:none;
    border-radius:10px; font-size:15px; font-weight:700; cursor:pointer; margin-top:16px;
    transition:background .15s; font-family:var(--sans);
}
.checkout-btn:hover { background:var(--red-dark); }
.checkout-btn:disabled { background:#ccc; cursor:not-allowed; }
.checkout-btn svg, .pay-final-btn svg, .back-btn svg { width:18px; height:18px; fill:none; stroke:currentColor; stroke-width:2.2; stroke-linecap:round; stroke-linejoin:round; }

.pay-final-btn {
    display:flex; align-items:center; justify-content:center; gap:8px;
    width:100%; height:50px; background:var(--red); color:white; border:none;
    border-radius:10px; font-size:15px; font-weight:700; cursor:pointer; margin-top:14px;
    transition:background .15s; font-family:var(--sans);
}
.pay-final-btn:hover { background:var(--red-dark); }

.back-btn {
    width:100%; height:38px; background:none; border:1.5px solid var(--border);
    border-radius:8px; font-size:13px; font-weight:600; color:var(--muted);
    cursor:pointer; margin-top:12px; font-family:var(--sans); transition:all .15s;
}
.back-btn:hover { border-color:var(--red); color:var(--red); }

.secure-note { display:flex; align-items:center; justify-content:center; gap:5px; font-size:11px; color:var(--muted); margin-top:8px; }
.secure-note svg { width:13px; height:13px; fill:none; stroke:var(--muted); stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }

/* ── Processing animation ── */
.proc-view { text-align:center; padding:32px 0; display:none; }
.spin { width:48px; height:48px; border:4px solid var(--border); border-top-color:var(--red); border-radius:50%; animation:spin .8s linear infinite; margin:0 auto 16px; }
@keyframes spin { to { transform:rotate(360deg); } }
.proc-title { font-size:15px; font-weight:700; margin-bottom:6px; }
.proc-step { font-size:12px; color:var(--muted); margin-bottom:16px; }
.proc-bar { height:4px; background:var(--border); border-radius:4px; overflow:hidden; }
.proc-bar-fill { height:100%; background:var(--red); width:0%; transition:width .4s ease; }

/* ── Receipt ── */
.receipt-view { display:none; }
.receipt-hero { text-align:center; padding:20px 0 16px; border-bottom:1px solid var(--border); }
.receipt-check { width:52px; height:52px; background:#EAFAF1; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; }
.receipt-check svg { width:26px; height:26px; stroke:var(--green); fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }
.receipt-hero h2 { font-size:18px; font-weight:800; margin-bottom:4px; }
.receipt-hero p { font-size:12px; color:var(--muted); }
.receipt-body { padding-top:14px; }
.receipt-items { background:#F7F7F7; border-radius:10px; padding:10px 12px; margin-bottom:12px; }
.receipt-item { display:flex; justify-content:space-between; font-size:12px; padding:4px 0; border-bottom:1px solid var(--border); }
.receipt-item:last-child { border-bottom:none; }
.receipt-meta { margin-bottom:10px; }
.rm-row { display:flex; justify-content:space-between; font-size:12px; padding:4px 0; border-bottom:1px solid var(--border); }
.rm-row:last-child { border-bottom:none; }
.receipt-total-bar { display:flex; justify-content:space-between; font-size:15px; font-weight:800; background:var(--red); color:white; border-radius:10px; padding:12px 14px; margin:10px 0; }
.receipt-ref { text-align:center; font-size:10px; color:var(--muted); font-family:var(--mono); margin-bottom:14px; }
.btn-new-order { width:100%; height:44px; background:var(--red); color:white; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; margin-bottom:8px; font-family:var(--sans); }
.btn-print { width:100%; height:40px; background:none; border:1.5px solid var(--border); border-radius:10px; font-size:13px; font-weight:600; color:var(--muted); cursor:pointer; font-family:var(--sans); }

/* ── Fail ── */
.fail-view { text-align:center; padding:28px 0; display:none; }
.fail-icon { width:52px; height:52px; background:#FEF2F2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; }
.fail-icon svg { width:26px; height:26px; stroke:#DC2626; fill:none; stroke-width:2.5; stroke-linecap:round; }
.fail-title { font-size:16px; font-weight:800; margin-bottom:6px; color:#DC2626; }
.fail-msg { font-size:13px; color:var(--muted); margin-bottom:16px; }

/* ── Card ── */
.panel { background:white; border:1px solid var(--border); border-radius:14px; overflow:hidden; }
.panel-head { padding:1.1rem 1.5rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; }
.panel-head h2 { font-size:14px; font-weight:700; }
.panel-body { padding:1.25rem 1.5rem; }

@media print {
    #cart-items-panel, .hero { display:none !important; }
    #right-col { grid-column:1/-1; }
    .back-btn, .pay-final-btn, .btn-new-order, .btn-print, .checkout-btn { display:none !important; }
}
</style>

<section class="hero" style="padding:24px 32px; min-height:auto;">
    <div class="hero-content">
        <h1 style="font-size:32px;">My <span>Cart</span></h1>
    </div>
</section>

<div id="cart-section" style="max-width:1080px; margin:0 auto; padding:2rem 1.5rem; display:grid; grid-template-columns:1fr 360px; gap:2rem; align-items:start;">

    {{-- ══════════════════════════════════════ --}}
    {{-- LEFT: Cart Items --}}
    {{-- ══════════════════════════════════════ --}}
    <div id="cart-items-panel">
        <div class="panel">
            <div class="panel-head">
                <h2>Cart Items</h2>
                <span style="font-size:12px; color:#9B9B9B;" id="item-count">{{ count($items) }} items</span>
            </div>

            <div class="panel-body">
                @if(empty($items))
                    <div style="text-align:center; padding:3rem 1.5rem; color:#9B9B9B;">
                        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
                        <h3 style="font-weight:800; margin-bottom:8px;">Your cart is empty</h3>
                        <p style="margin-bottom:24px;">Add some products first!</p>
                        <a href="{{ route('products.index') }}" style="display:inline-block; padding:12px 32px; background:#C0392B; color:white; border-radius:50px; font-weight:700; text-decoration:none;">Shop Now</a>
                    </div>
                @else
                    @if(session('success'))
                        <div style="background:#EAFAF1; color:#1E8449; border:1px solid #A9DFBF; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:600; margin-bottom:16px;">
                            ✅ {{ session('success') }}
                        </div>
                    @endif

                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="font-size:11px; text-transform:uppercase; letter-spacing:0.07em; color:#9CA3AF; font-weight:500; padding:0 0 10px; text-align:left; border-bottom:1px solid #EFEFEF;">Product</th>
                                <th style="font-size:11px; text-transform:uppercase; letter-spacing:0.07em; color:#9CA3AF; font-weight:500; padding:0 0 10px; text-align:center; border-bottom:1px solid #EFEFEF;">Qty</th>
                                <th style="font-size:11px; text-transform:uppercase; letter-spacing:0.07em; color:#9CA3AF; font-weight:500; padding:0 0 10px; text-align:right; border-bottom:1px solid #EFEFEF;">Price</th>
                            </tr>
                        </thead>
                        <tbody id="cart-body">
                            @foreach($items as $item)
                            <tr id="cart-row-{{ $item['product']->id }}">
                                <td style="padding:14px 0; border-bottom:1px solid #EFEFEF; vertical-align:middle;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div style="width:52px; height:52px; border-radius:10px; border:1px solid #EFEFEF; display:flex; align-items:center; justify-content:center; background:#F7F7F7; overflow:hidden; flex-shrink:0;">
                                            @if($item['product']->image)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($item['product']->image) }}"
                                                    style="width:100%; height:100%; object-fit:contain; padding:4px;">
                                            @else
                                                <span style="font-size:24px;">🛒</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-size:14px; font-weight:600; margin-bottom:2px;">{{ $item['product']->name }}</div>
                                            <div style="font-size:11px; color:#9CA3AF;">Shelf: {{ $item['product']->shelf_code }}</div>
                                            <div style="font-size:11px; color:#C0392B; font-weight:600;">{{ $item['product']->category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:14px 0; border-bottom:1px solid #EFEFEF; vertical-align:middle; text-align:center;">
                                    <div style="display:flex; align-items:center; justify-content:center; gap:0; border:1px solid #EFEFEF; border-radius:8px; overflow:hidden; width:fit-content; margin:0 auto;">
                                        <button onclick="updateQty({{ $item['product']->id }}, {{ $item['qty'] - 1 }})"
                                            style="width:30px; height:30px; background:none; border:none; cursor:pointer; font-size:16px; color:#9B9B9B; display:flex; align-items:center; justify-content:center;">−</button>
                                        <span id="qty-{{ $item['product']->id }}"
                                            style="width:32px; text-align:center; font-size:13px; font-weight:600; border-left:1px solid #EFEFEF; border-right:1px solid #EFEFEF; line-height:30px;">
                                            {{ $item['qty'] }}
                                        </span>
                                        <button onclick="updateQty({{ $item['product']->id }}, {{ $item['qty'] + 1 }})"
                                            style="width:30px; height:30px; background:none; border:none; cursor:pointer; font-size:16px; color:#9B9B9B; display:flex; align-items:center; justify-content:center;">+</button>
                                    </div>
                                </td>
                                <td style="padding:14px 0; border-bottom:1px solid #EFEFEF; vertical-align:middle; text-align:right;">
                                    <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                                        <span id="subtotal-{{ $item['product']->id }}" style="font-size:14px; font-weight:700; font-family:monospace;">
                                            RM {{ number_format($item['subtotal'], 2) }}
                                        </span>
                                        <button onclick="removeItem({{ $item['product']->id }})"
                                            style="background:none; border:none; cursor:pointer; color:#9CA3AF; padding:4px; border-radius:5px; display:flex; align-items:center; justify-content:center; transition:color 0.15s;"
                                            onmouseover="this.style.color='#DC2626'" onmouseout="this.style.color='#9CA3AF'">
                                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Add More Products --}}
                    <div style="margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid #EFEFEF;">
                        <div style="font-size:12px; font-weight:600; color:#9B9B9B; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.06em;">Add More Products</div>
                        <div id="catalogue" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(120px,1fr)); gap:10px;">
                            {{-- Loaded via JS --}}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════ --}}
    {{-- RIGHT: Summary + Payment (merged) --}}
    {{-- ══════════════════════════════════════ --}}
    @if(!empty($items))
    <div id="right-col">

        {{-- ── ORDER SUMMARY PANEL ── --}}
        <div class="panel" id="summary-card" style="margin-bottom:1rem;">
            <div class="panel-head"><h2>Order Summary</h2></div>
            <div class="panel-body">

                <div class="s-row">
                    <span>Subtotal (<span id="s-count">{{ count($items) }}</span> items)</span>
                    <span id="s-sub" style="font-family:monospace; font-weight:500;">RM {{ number_format($total, 2) }}</span>
                </div>
                <div class="s-row discount" id="s-disc-row" style="display:none;">
                    <span>Discount</span>
                    <span id="s-disc">– RM 0.00</span>
                </div>
                <div class="s-row">
                    <span>Delivery</span>
                    <span id="s-ship" style="color:#16A34A; font-weight:600;">Free</span>
                </div>
                <div class="s-row">
                    <span>SST (8%)</span>
                    <span id="s-tax" style="font-family:monospace;">RM {{ number_format($total * 0.08, 2) }}</span>
                </div>

                <hr class="s-divider">

                <div class="s-total">
                    <span>Total</span>
                    <span id="s-total">RM {{ number_format($total * 1.08, 2) }}</span>
                </div>

                {{-- Coupon --}}
                <div class="coupon-row">
                    <input type="text" id="coupon-input" placeholder="Coupon code">
                    <button onclick="applyCoupon()">Apply</button>
                </div>
                <div class="coupon-msg" id="coupon-msg"></div>

                <button class="checkout-btn" id="checkout-btn" onclick="goToPayment()">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Proceed to Payment
                </button>

                <p class="secure-note">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Secure checkout · Demo mode
                </p>

                <a href="{{ route('products.index') }}"
                    style="display:block; text-align:center; margin-top:12px; font-size:13px; color:#C0392B; font-weight:700; text-decoration:none;">
                    ← Continue Shopping
                </a>
            </div>
        </div>

        {{-- ── PAYMENT PANEL (hidden initially) ── --}}
        <div class="panel" id="payment-card" style="display:none;">
            <div class="panel-head">
                <h2>Payment</h2>
                <span id="pay-total-label" style="font-size:13px; font-weight:700; color:#C0392B; font-family:monospace;">RM 0.00</span>
            </div>
            <div class="panel-body">

                {{-- PAYMENT FORM --}}
                <div id="pay-form-view">
                    <div style="font-size:11px; font-weight:600; color:#9B9B9B; text-transform:uppercase; letter-spacing:.06em; margin-bottom:10px;">Choose payment method</div>
                    <div class="pay-methods">
                        <div class="pm active" onclick="setMethod('card')" id="pm-card">
                            <span class="pm-icon">💳</span>
                            <div class="pm-label">Credit / Debit</div>
                        </div>
                        <div class="pm" onclick="setMethod('bank')" id="pm-bank">
                            <span class="pm-icon">🏦</span>
                            <div class="pm-label">Bank Transfer</div>
                        </div>
                        <div class="pm" onclick="setMethod('ewallet')" id="pm-ewallet">
                            <span class="pm-icon">📱</span>
                            <div class="pm-label">e-Wallet</div>
                        </div>
                    </div>

                    {{-- CARD METHOD --}}
                    <div id="method-card">
                        <div class="card-types">
                            <div class="ct active" id="ct-visa">VISA</div>
                            <div class="ct" id="ct-mc">MC</div>
                            <div class="ct" id="ct-amex">AMEX</div>
                            <div class="ct" id="ct-jcb">JCB</div>
                        </div>
                        <div class="card-vis">
                            <div class="cv-chip"></div>
                            <div class="cv-num" id="prev-num">•••• •••• •••• ••••</div>
                            <div class="cv-bottom">
                                <div class="cv-holder">Card holder<span id="prev-name">YOUR NAME</span></div>
                                <div class="cv-exp">Expires<span id="prev-exp">MM/YY</span></div>
                            </div>
                            <div class="cv-brand" id="prev-brand">VISA</div>
                        </div>
                        <div class="field">
                            <label>Name on card</label>
                            <input type="text" id="f-name" placeholder="e.g. Ahmad bin Ali" autocomplete="cc-name"
                                oninput="document.getElementById('prev-name').textContent = this.value || 'YOUR NAME'">
                            <div class="errmsg" id="e-name">Enter cardholder name</div>
                        </div>
                        <div class="field">
                            <label>Card number</label>
                            <input type="text" id="f-num" placeholder="0000 0000 0000 0000" maxlength="19" inputmode="numeric" autocomplete="cc-number">
                            <div class="errmsg" id="e-num">Enter a valid card number</div>
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label>Expiry</label>
                                <input type="text" id="f-exp" placeholder="MM / YY" maxlength="7" inputmode="numeric" autocomplete="cc-exp">
                                <div class="errmsg" id="e-exp">Invalid expiry</div>
                            </div>
                            <div class="field">
                                <label>CVV</label>
                                <input type="text" id="f-cvv" placeholder="•••" maxlength="4" inputmode="numeric" autocomplete="cc-csc">
                                <div class="errmsg" id="e-cvv">Invalid CVV</div>
                            </div>
                        </div>
                    </div>

                    {{-- BANK TRANSFER METHOD --}}
                    <div id="method-bank" style="display:none;">

                        <div class="fpx-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1A6CB5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            Powered by <strong>FPX Online Banking</strong> · Secure redirect
                        </div>

                        <div style="font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px;">Select your bank</div>
                        <div class="bank-grid" id="bank-grid">
                            <div class="bank-tile" onclick="selectBank(this,'Maybank2U')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Maybank_logo.svg/120px-Maybank_logo.svg.png" alt="Maybank" onerror="this.style.display='none'">
                                <span>Maybank2U</span>
                            </div>
                            <div class="bank-tile" onclick="selectBank(this,'CIMB Clicks')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/CIMB_logo.svg/120px-CIMB_logo.svg.png" alt="CIMB" onerror="this.style.display='none'">
                                <span>CIMB Clicks</span>
                            </div>
                            <div class="bank-tile" onclick="selectBank(this,'Public Bank')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a3/Public_Bank_Berhad_logo.svg/120px-Public_Bank_Berhad_logo.svg.png" alt="PBB" onerror="this.style.display='none'">
                                <span>Public Bank</span>
                            </div>
                            <div class="bank-tile" onclick="selectBank(this,'RHB Bank')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/RHB_Bank_logo.svg/120px-RHB_Bank_logo.svg.png" alt="RHB" onerror="this.style.display='none'">
                                <span>RHB Bank</span>
                            </div>
                            <div class="bank-tile" onclick="selectBank(this,'Hong Leong Bank')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Hong_Leong_Bank_logo.svg/120px-Hong_Leong_Bank_logo.svg.png" alt="HLB" onerror="this.style.display='none'">
                                <span>Hong Leong</span>
                            </div>
                            <div class="bank-tile" onclick="selectBank(this,'AmBank')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/90/AmBank_logo.svg/120px-AmBank_logo.svg.png" alt="AmBank" onerror="this.style.display='none'">
                                <span>AmBank</span>
                            </div>
                            <div class="bank-tile" onclick="selectBank(this,'Bank Islam')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/Bank_Islam_Malaysia_logo.svg/120px-Bank_Islam_Malaysia_logo.svg.png" alt="Bank Islam" onerror="this.style.display='none'">
                                <span>Bank Islam</span>
                            </div>
                            <div class="bank-tile" onclick="selectBank(this,'BSN')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/39/BSN_logo.svg/120px-BSN_logo.svg.png" alt="BSN" onerror="this.style.display='none'">
                                <span>BSN</span>
                            </div>
                            <div class="bank-tile" onclick="selectBank(this,'Bank Rakyat')">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Bank_Rakyat_Malaysia_logo.svg/120px-Bank_Rakyat_Malaysia_logo.svg.png" alt="Bank Rakyat" onerror="this.style.display='none'">
                                <span>Bank Rakyat</span>
                            </div>
                        </div>
                        <div class="errmsg" id="e-bank" style="margin-bottom:8px;">Please select a bank</div>

                        {{-- Transfer details shown after bank selected --}}
                        <div id="bank-details-box" style="display:none;">
                            <div class="bank-info">
                                <div class="bank-row"><span>Account name</span><span>Kita Konbini Sdn Bhd</span></div>
                                <div class="bank-row"><span>Account no.</span><span>5641 2233 4455</span></div>
                                <div class="bank-row"><span>Bank</span><span>Maybank</span></div>
                                <div class="bank-row"><span>Reference</span><span id="bank-ref">ORD-000000</span></div>
                            </div>
                            <p style="font-size:11px; color:var(--muted); margin-bottom:10px;">
                                You will be redirected to <strong id="bank-redirect-name">your bank</strong> to complete payment securely.
                                Upload receipt if paying via manual transfer.
                            </p>
                            <div class="field">
                                <label>Receipt image (optional)</label>
                                <input type="file" id="f-receipt" style="height:auto; border:none; padding:8px 0; font-size:12px;">
                            </div>
                        </div>

                        <input type="hidden" id="f-bank" value="">
                    </div>

                    {{-- E-WALLET METHOD --}}
                    <div id="method-ewallet" style="display:none;">
                        <p style="font-size:12px; color:var(--muted); margin-bottom:12px;">Select your e-wallet. You will be redirected to complete payment.</p>
                        <div class="ewallet-grid">

                            {{-- Touch 'n Go --}}
                            <div class="ew-item" onclick="selectWallet(this)" data-wallet="Touch 'n Go eWallet">
                                <svg class="ew-logo" viewBox="0 0 120 50" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="50" rx="8" fill="#00AED6"/>
                                    <text x="60" y="20" text-anchor="middle" font-family="Arial Black,Arial" font-weight="900" font-size="10" fill="white">Touch 'n Go</text>
                                    <text x="60" y="36" text-anchor="middle" font-family="Arial" font-size="9" fill="white" font-weight="600">eWallet</text>
                                </svg>
                                <div class="ew-name">TNG eWallet</div>
                            </div>

                            {{-- GrabPay --}}
                            <div class="ew-item" onclick="selectWallet(this)" data-wallet="GrabPay">
                                <svg class="ew-logo" viewBox="0 0 120 50" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="50" rx="8" fill="#00B14F"/>
                                    <text x="60" y="32" text-anchor="middle" font-family="Arial Black,Arial" font-weight="900" font-size="18" fill="white">Grab</text>
                                    <text x="96" y="38" text-anchor="middle" font-family="Arial" font-size="8" fill="white" opacity=".85">Pay</text>
                                </svg>
                                <div class="ew-name">GrabPay</div>
                            </div>

                            {{-- Boost --}}
                            <div class="ew-item" onclick="selectWallet(this)" data-wallet="Boost">
                                <svg class="ew-logo" viewBox="0 0 120 50" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="50" rx="8" fill="#E8192C"/>
                                    <text x="60" y="34" text-anchor="middle" font-family="Arial Black,Arial" font-weight="900" font-size="20" fill="white">boost</text>
                                </svg>
                                <div class="ew-name">Boost</div>
                            </div>

                            {{-- MAE by Maybank --}}
                            <div class="ew-item" onclick="selectWallet(this)" data-wallet="MAE by Maybank">
                                <svg class="ew-logo" viewBox="0 0 120 50" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="50" rx="8" fill="#FFCC00"/>
                                    <text x="60" y="30" text-anchor="middle" font-family="Arial Black,Arial" font-weight="900" font-size="22" fill="#1A1A1A">MAE</text>
                                    <text x="60" y="43" text-anchor="middle" font-family="Arial" font-size="8" fill="#1A1A1A" opacity=".7">by Maybank</text>
                                </svg>
                                <div class="ew-name">MAE</div>
                            </div>

                            {{-- ShopeePay --}}
                            <div class="ew-item" onclick="selectWallet(this)" data-wallet="ShopeePay">
                                <svg class="ew-logo" viewBox="0 0 120 50" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="50" rx="8" fill="#EE4D2D"/>
                                    <text x="60" y="22" text-anchor="middle" font-family="Arial Black,Arial" font-weight="900" font-size="10" fill="white">Shopee</text>
                                    <text x="60" y="37" text-anchor="middle" font-family="Arial Black,Arial" font-weight="900" font-size="11" fill="white">Pay</text>
                                </svg>
                                <div class="ew-name">ShopeePay</div>
                            </div>

                            {{-- BigPay --}}
                            <div class="ew-item" onclick="selectWallet(this)" data-wallet="BigPay">
                                <svg class="ew-logo" viewBox="0 0 120 50" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="50" rx="8" fill="#0E1D6A"/>
                                    <text x="60" y="34" text-anchor="middle" font-family="Arial Black,Arial" font-weight="900" font-size="20" fill="white">BigPay</text>
                                </svg>
                                <div class="ew-name">BigPay</div>
                            </div>

                        </div>
                        <div class="errmsg" id="e-wallet-sel" style="margin-bottom:8px;">Please select an e-wallet</div>
                        <div class="field">
                            <label>Registered phone no.</label>
                            <input type="text" id="f-phone" placeholder="+60 1X-XXX XXXX" inputmode="tel">
                            <div class="errmsg" id="e-phone">Enter your registered number</div>
                        </div>
                    </div>

                    <div class="field" style="margin-top:14px;">
                        <label>Billing email</label>
                        <input type="email" id="f-email" placeholder="you@example.com" autocomplete="email">
                        <div class="errmsg" id="e-email">Enter a valid email</div>
                    </div>

                    <button class="back-btn" onclick="backToCart()">← Back to cart</button>
                    <button class="pay-final-btn" onclick="submitPayment()">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        <span id="pay-btn-label">Pay RM 0.00</span>
                    </button>
                    <p class="secure-note" style="margin-top:10px;">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        Demo — no real charges made
                    </p>
                </div>

                {{-- PROCESSING --}}
                <div class="proc-view" id="proc-view">
                    <div class="spin"></div>
                    <div class="proc-title">Processing payment…</div>
                    <div class="proc-step" id="proc-step">Connecting to payment network</div>
                    <div class="proc-bar"><div class="proc-bar-fill" id="proc-fill"></div></div>
                </div>

                {{-- RECEIPT --}}
                <div class="receipt-view" id="receipt-view">
                    <div class="receipt-hero">
                        <div class="receipt-check">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <h2>Order confirmed!</h2>
                        <p>Payment successful. Thank you for your purchase.</p>
                    </div>
                    <div class="receipt-body">
                        <div class="receipt-items" id="r-items"></div>
                        <div class="receipt-meta">
                            <div class="rm-row"><span>Payment method</span><span id="r-method">—</span></div>
                            <div class="rm-row"><span>Email</span><span id="r-email">—</span></div>
                            <div class="rm-row"><span>Date</span><span id="r-date">—</span></div>
                            <div class="rm-row"><span>Subtotal</span><span id="r-sub">—</span></div>
                            <div class="rm-row"><span>Shipping</span><span id="r-ship">—</span></div>
                            <div class="rm-row" id="r-disc-row" style="display:none;"><span>Discount</span><span id="r-disc">—</span></div>
                            <div class="rm-row"><span>Tax (8%)</span><span id="r-tax">—</span></div>
                        </div>
                        <div class="receipt-total-bar">
                            <span>Total charged</span>
                            <span id="r-total">RM 0.00</span>
                        </div>
                        <div class="receipt-ref" id="r-ref">REF: —</div>
                        <button class="btn-new-order" onclick="resetAll()">Place a new order</button>
                        <button class="btn-print" onclick="window.print()">🖨 Print receipt</button>
                    </div>
                </div>

                {{-- FAILED --}}
                <div class="fail-view" id="fail-view">
                    <div class="fail-icon">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </div>
                    <div class="fail-title">Payment declined</div>
                    <p class="fail-msg" id="fail-msg">Your card was declined. Please try a different card.</p>
                    <button class="pay-final-btn" onclick="backToPayForm()" style="max-width:260px; margin:0 auto;">Try again</button>
                </div>

            </div>
        </div>

    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
// ════════════════════════════════════════════════════════
// EXISTING FUNCTIONS — TIDAK DIUBAH
// ════════════════════════════════════════════════════════

function updateQty(id, qty) {
    if (qty < 0) return;
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
                document.getElementById('cart-row-' + id)?.remove();
            } else {
                const qtyEl = document.getElementById('qty-' + id);
                if (qtyEl) qtyEl.textContent = qty;
            }
            const totalEl = document.getElementById('summary-total');
            const subEl   = document.getElementById('summary-sub');
            if (totalEl) totalEl.textContent = 'RM ' + data.total;
            if (subEl)   subEl.textContent   = 'RM ' + data.total;

            const badge = document.querySelector('.cart-badge');
            if (badge) badge.textContent = data.count;
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
            document.getElementById('cart-row-' + id)?.remove();
            const badge = document.querySelector('.cart-badge');
            if (badge) badge.textContent = data.count;
        }
    });
}

fetch('/products/catalogue')
    .then(r => r.json())
    .then(products => {
        const grid = document.getElementById('catalogue');
        if (!grid) return;
        grid.innerHTML = products.map(p => `
            <div onclick="addToCartQuick(${p.id}, this)"
                style="border:1.5px solid #EFEFEF; border-radius:10px; padding:10px; cursor:pointer;
                text-align:center; background:white; transition:all 0.15s;"
                onmouseover="this.style.borderColor='#C0392B'; this.style.background='#FDF2F0';"
                onmouseout="this.style.borderColor='#EFEFEF'; this.style.background='white';"
                data-id="${p.id}">
                <div style="font-size:26px; margin-bottom:6px;">
                    ${p.image ? `<img src="${p.image}" style="width:40px; height:40px; object-fit:contain;">` : '🛒'}
                </div>
                <div style="font-size:11px; font-weight:600; margin-bottom:2px; line-height:1.3;">${p.name}</div>
                <div style="font-size:11px; color:#9B9B9B;">RM ${p.price}</div>
            </div>
        `).join('');
    })
    .catch(() => {});

function addToCartQuick(id, el) {
    fetch('/cart/add/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            el.style.borderColor = '#16A34A';
            el.style.background  = '#F0FDF4';
            const badge = document.querySelector('.cart-badge');
            if (badge) badge.textContent = data.count;
            setTimeout(() => location.reload(), 600);
        }
    });
}

// ════════════════════════════════════════════════════════
// PAYMENT SYSTEM — NEW FUNCTIONS
// ════════════════════════════════════════════════════════

// ── State ──
let currentMethod   = 'card';
let selectedWallet  = null;
let discountAmount  = 0;
let discountCode    = null;

const COUPONS = { 'KONBINI10': 10, 'SAVE20': 20, 'FIRSTORDER': 15 };

// Read base subtotal from server-rendered value
function getSubtotal() {
    const el = document.getElementById('s-sub');
    if (!el) return 0;
    return parseFloat(el.textContent.replace('RM', '').trim()) || 0;
}

function calcTotals() {
    const sub      = getSubtotal();
    const disc     = discountAmount;
    const afterDisc = Math.max(0, sub - disc);
    const tax      = afterDisc * 0.08;
    const total    = afterDisc + tax;
    return { sub, disc, afterDisc, tax, total };
}

function refreshSummaryUI() {
    const t = calcTotals();
    const fmt = v => 'RM ' + v.toFixed(2);

    // Discount row
    const discRow = document.getElementById('s-disc-row');
    const discEl  = document.getElementById('s-disc');
    if (discRow && discEl) {
        if (t.disc > 0) {
            discRow.style.display = 'flex';
            discEl.textContent    = '– ' + fmt(t.disc);
        } else {
            discRow.style.display = 'none';
        }
    }

    const taxEl   = document.getElementById('s-tax');
    const totEl   = document.getElementById('s-total');
    const btnLbl  = document.getElementById('pay-btn-label');
    const payLbl  = document.getElementById('pay-total-label');

    if (taxEl)  taxEl.textContent  = fmt(t.tax);
    if (totEl)  totEl.textContent  = fmt(t.total);
    if (btnLbl) btnLbl.textContent = 'Pay ' + fmt(t.total);
    if (payLbl) payLbl.textContent = fmt(t.total);
}

// Init on page load
document.addEventListener('DOMContentLoaded', refreshSummaryUI);

// ── Coupon ──
function applyCoupon() {
    const code  = (document.getElementById('coupon-input')?.value || '').trim().toUpperCase();
    const msgEl = document.getElementById('coupon-msg');
    if (!code) { showMsg(msgEl, 'Enter a coupon code.', false); return; }

    if (COUPONS[code]) {
        const pct      = COUPONS[code];
        discountAmount = getSubtotal() * pct / 100;
        discountCode   = code;
        refreshSummaryUI();
        showMsg(msgEl, `✅ "${code}" applied — ${pct}% off!`, true);
    } else {
        discountAmount = 0;
        discountCode   = null;
        refreshSummaryUI();
        showMsg(msgEl, '❌ Invalid coupon code.', false);
    }
}

function showMsg(el, text, ok) {
    if (!el) return;
    el.textContent  = text;
    el.className    = 'coupon-msg ' + (ok ? 'ok' : 'err');
}

// ── Navigation ──
function goToPayment() {
    document.getElementById('summary-card').style.display  = 'none';
    document.getElementById('payment-card').style.display  = 'block';
    refreshSummaryUI();

    // Generate order ref
    const ref = 'ORD-' + Math.random().toString(36).slice(2,8).toUpperCase();
    const refEl = document.getElementById('bank-ref');
    if (refEl) refEl.textContent = ref;
}

function backToCart() {
    document.getElementById('payment-card').style.display = 'none';
    document.getElementById('summary-card').style.display = 'block';
    showView('pay-form-view');
}

function backToPayForm() {
    showView('pay-form-view');
}

function showView(id) {
    ['pay-form-view','proc-view','receipt-view','fail-view'].forEach(v => {
        const el = document.getElementById(v);
        if (el) el.style.display = (v === id) ? 'block' : 'none';
    });
}

// ── Payment method tabs ──
function setMethod(method) {
    currentMethod  = method;
    ['card','bank','ewallet'].forEach(m => {
        const tab = document.getElementById('pm-' + m);
        const div = document.getElementById('method-' + m);
        if (tab) tab.classList.toggle('active', m === method);
        if (div) div.style.display = (m === method) ? 'block' : 'none';
    });
}

function selectWallet(el) {
    document.querySelectorAll('.ew-item').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    selectedWallet = el.getAttribute('data-wallet') || el.querySelector('.ew-name')?.textContent?.trim() || '';
    showErr('e-wallet-sel', false);
}

function selectBank(el, name) {
    document.querySelectorAll('.bank-tile').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    const bankInput = document.getElementById('f-bank');
    if (bankInput) bankInput.value = name;
    // Show details box
    const box = document.getElementById('bank-details-box');
    if (box) box.style.display = 'block';
    const redir = document.getElementById('bank-redirect-name');
    if (redir) redir.textContent = name;
    showErr('e-bank', false);
}

// ── Card number live format ──
const numInput = document.getElementById('f-num');
if (numInput) {
    numInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'').slice(0,16);
        this.value = v.replace(/(.{4})/g,'$1 ').trim();
        const prev = document.getElementById('prev-num');
        if (prev) prev.textContent = this.value || '•••• •••• •••• ••••';
        detectCardBrand(v);
    });
}

const expInput = document.getElementById('f-exp');
if (expInput) {
    expInput.addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'');
        if (v.length >= 3) v = v.slice(0,2) + ' / ' + v.slice(2,4);
        this.value = v;
        const prev = document.getElementById('prev-exp');
        if (prev) prev.textContent = this.value || 'MM/YY';
    });
}

function detectCardBrand(num) {
    let brand = '';
    if (/^4/.test(num))          brand = 'VISA';
    else if (/^5[1-5]/.test(num)) brand = 'MC';
    else if (/^3[47]/.test(num))  brand = 'AMEX';
    else if (/^35/.test(num))     brand = 'JCB';

    ['visa','mc','amex','jcb'].forEach(b => {
        const el = document.getElementById('ct-' + b);
        if (el) el.classList.toggle('active', b === brand.toLowerCase());
    });
    const prev = document.getElementById('prev-brand');
    if (prev) prev.textContent = brand;
}

// ── Validation ──
function validate() {
    let ok = true;
    const email = document.getElementById('f-email')?.value || '';
    const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    showErr('e-email', !emailOk); if (!emailOk) ok = false;

    if (currentMethod === 'card') {
        const name = (document.getElementById('f-name')?.value || '').trim();
        const num  = (document.getElementById('f-num')?.value || '').replace(/\s/g,'');
        const exp  = (document.getElementById('f-exp')?.value || '').trim();
        const cvv  = (document.getElementById('f-cvv')?.value || '').trim();

        if (!name)          { showErr('e-name', true); ok = false; } else showErr('e-name', false);
        if (num.length < 13){ showErr('e-num',  true); ok = false; } else showErr('e-num',  false);
        if (!/^\d{2}\s*\/\s*\d{2}$/.test(exp)) { showErr('e-exp', true); ok = false; } else showErr('e-exp', false);
        if (cvv.length < 3) { showErr('e-cvv',  true); ok = false; } else showErr('e-cvv',  false);
    }

    if (currentMethod === 'bank') {
        const bank = document.getElementById('f-bank')?.value || '';
        if (!bank) { showErr('e-bank', true); ok = false; } else showErr('e-bank', false);
    }

    if (currentMethod === 'ewallet') {
        if (!selectedWallet) { showErr('e-wallet-sel', true); ok = false; } else showErr('e-wallet-sel', false);
        const phone = (document.getElementById('f-phone')?.value || '').trim();
        if (!phone) { showErr('e-phone', true); ok = false; } else showErr('e-phone', false);
    }

    return ok;
}

function showErr(id, show) {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('show', show);
}

// ── Submit payment ──
function submitPayment() {
    if (!validate()) return;

    showView('proc-view');
    const steps = currentMethod === 'card'
        ? ['Connecting to payment network', 'Verifying card details', 'Authorising transaction', 'Finalising order']
        : currentMethod === 'bank'
        ? ['Connecting to FPX network', 'Redirecting to ' + (document.getElementById('f-bank')?.value || 'bank'), 'Verifying transaction', 'Confirming order']
        : ['Connecting to ' + (selectedWallet || 'e-Wallet'), 'Sending payment request', 'Awaiting confirmation', 'Finalising order'];

    let i = 0;
    const fill = document.getElementById('proc-fill');
    const step = document.getElementById('proc-step');

    const tick = setInterval(() => {
        i++;
        if (fill) fill.style.width = (i / steps.length * 100) + '%';
        if (step && steps[i]) step.textContent = steps[i];
        if (i >= steps.length - 1) {
            clearInterval(tick);
            setTimeout(() => {
                // ~15% chance of failure for realism
                if (Math.random() < 0.15) {
                    showView('fail-view');
                    const failMsg = document.getElementById('fail-msg');
                    if (failMsg) failMsg.textContent = currentMethod === 'card'
                        ? 'Your card was declined. Please try a different card.'
                        : 'Transaction could not be completed. Please try again.';
                } else {
                    buildReceipt();
                    showView('receipt-view');
                }
            }, 600);
        }
    }, 700);
}

// ── Build receipt ──
function buildReceipt() {
    const t = calcTotals();
    const fmt = v => 'RM ' + v.toFixed(2);

    // Items from DOM
    const rows = document.querySelectorAll('#cart-body tr');
    let itemsHtml = '';
    rows.forEach(row => {
        const name = row.querySelector('div[style*="font-weight:600"]')?.textContent?.trim();
        const qty  = row.querySelector('[id^="qty-"]')?.textContent?.trim();
        const sub  = row.querySelector('[id^="subtotal-"]')?.textContent?.trim();
        if (name) {
            itemsHtml += `<div class="receipt-item">
                <span>${name} × ${qty || 1}</span>
                <span style="font-family:monospace;">${sub || '—'}</span>
            </div>`;
        }
    });
    const rItems = document.getElementById('r-items');
    if (rItems) rItems.innerHTML = itemsHtml || '<div class="receipt-item"><span>Items</span><span>See cart</span></div>';

    // Method label
    let methodLabel = '';
    if (currentMethod === 'card') {
        const num = (document.getElementById('f-num')?.value || '').slice(-4);
        methodLabel = 'Card ending ' + (num || '••••');
    } else if (currentMethod === 'bank') {
        methodLabel = document.getElementById('f-bank')?.value || 'Bank transfer';
    } else {
        methodLabel = selectedWallet || 'e-Wallet';
    }

    const setTxt = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };

    setTxt('r-method', methodLabel);
    setTxt('r-email',  document.getElementById('f-email')?.value || '—');
    setTxt('r-date',   new Date().toLocaleString('en-MY', { dateStyle:'medium', timeStyle:'short' }));
    setTxt('r-sub',    fmt(t.sub));
    setTxt('r-ship',   'Free');
    setTxt('r-tax',    fmt(t.tax));
    setTxt('r-total',  fmt(t.total));
    setTxt('r-ref',    'REF: ' + Math.random().toString(36).slice(2,10).toUpperCase());

    const discRow = document.getElementById('r-disc-row');
    const discEl  = document.getElementById('r-disc');
    if (t.disc > 0 && discRow && discEl) {
        discRow.style.display = 'flex';
        discEl.textContent    = '– ' + fmt(t.disc) + (discountCode ? ' (' + discountCode + ')' : '');
    }
}

// ── Reset ──
function resetAll() {
    location.reload();
}
</script>
@endpush