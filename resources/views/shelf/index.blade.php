@extends('layouts.app')

@section('title', 'Shelf Locator - Kita Konbini')

@push('styles')
<style>
/* ── Fix: prevent white gap below last section ── */
html, body {
    background: var(--gray-light) !important;
}

main {
    background: var(--gray-light) !important;
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 57px); /* 57px = navbar */
}

.shelf-page {
    background: var(--gray-light);
    padding-bottom: 40px;
    flex: 1;
    min-height: 100%;
}

/* ── Section grid card ── */
.shelf-section-card {
    background: white;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1.5px solid transparent;
    transition: border-color 0.2s;
}

.shelf-section-card:hover {
    border-color: var(--gray-border);
}

.shelf-section-label {
    font-size: 11px;
    font-weight: 800;
    color: var(--gray-text);
    margin-bottom: 10px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.shelf-slots-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
}

/* ── Popup overlay ── */
.slot-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    backdrop-filter: blur(3px);
    z-index: 997;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.slot-overlay.active {
    opacity: 1;
    pointer-events: auto;
}

/* ── Popup sheet ── */
.slot-sheet {
    position: fixed;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%) translateY(100%);
    width: 90%;
    max-width: 400px;
    background: white;
    border-radius: 24px 24px 0 0;
    padding: 28px 24px 40px;
    box-shadow: 0 -8px 40px rgba(0,0,0,0.12);
    z-index: 998;
    transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);
}

.slot-sheet.active {
    transform: translateX(-50%) translateY(0);
}

/* ── Drag handle ── */
.sheet-handle {
    width: 40px;
    height: 4px;
    background: var(--gray-border);
    border-radius: 2px;
    margin: 0 auto 20px;
}

/* ── Popup tags ── */
.slot-tags {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.slot-tag {
    font-size: 11px;
    font-weight: 800;
    padding: 4px 12px;
    border-radius: 50px;
}

.slot-tag-code {
    background: var(--red-bg);
    color: var(--red-primary);
}

.slot-tag-cat {
    background: var(--gray-light);
    color: var(--gray-text);
}

/* ── Product info row ── */
.slot-product-row {
    display: flex;
    gap: 14px;
    align-items: center;
    background: var(--gray-light);
    border-radius: 14px;
    padding: 14px;
    margin-bottom: 16px;
}

.slot-product-img {
    width: 72px;
    height: 72px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}

.slot-product-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 4px;
}

.slot-product-name {
    font-size: 16px;
    font-weight: 800;
    margin-bottom: 4px;
    color: var(--black);
}

.slot-product-price {
    font-size: 18px;
    font-weight: 800;
    color: var(--red-primary);
}

.slot-product-stock {
    font-size: 12px;
    margin-top: 4px;
    color: var(--gray-text);
}

/* ── Add to cart btn inside popup ── */
.slot-add-btn {
    width: 100%;
    padding: 14px;
    background: var(--red-primary);
    color: white;
    border: none;
    border-radius: 50px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 15px;
    font-weight: 800;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
}

.slot-add-btn:hover {
    background: var(--red-dark);
    transform: translateY(-1px);
}

.slot-close-btn {
    margin-top: 10px;
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 50px;
    background: var(--gray-border);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 700;
    cursor: pointer;
    font-size: 14px;
    color: var(--black);
    transition: background 0.2s;
}

.slot-close-btn:hover {
    background: #D5D5D5;
}

/* ── Empty slot state ── */
.slot-empty {
    text-align: center;
    padding: 20px 0;
}

.slot-empty-icon {
    font-size: 48px;
    margin-bottom: 12px;
}

.slot-empty h3 {
    font-weight: 800;
    margin-bottom: 6px;
    font-size: 16px;
}

.slot-empty p {
    color: var(--gray-text);
    font-size: 13px;
}
</style>
@endpush

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>Shelf <span>Locator</span></h1>
        <p>Click on a slot to see what's there and add it to your cart.</p>
    </div>
</section>

<div class="shelf-page">
    <div class="section">
        <div style="display:flex; gap:20px; flex-wrap:wrap; justify-content:center;">
            @foreach($sections as $letter => $info)
            <div class="shelf-section-card">
                <div class="shelf-section-label">
                    {{ $letter }} — {{ $info['name'] }}
                </div>
                <div class="shelf-slots-grid">
                    @for($i = 1; $i <= 16; $i++)
                        @php
                            $code    = $letter . $i;
                            $product = $products->get($code);
                        @endphp
                        <div class="shelf-slot {{ $info['color'] }}"
                            onclick="showSlot('{{ $code }}')"
                            title="{{ $code }}: {{ $product?->name ?? 'Empty' }}"
                            style="{{ !$product ? 'opacity:0.35;' : '' }}">
                            {{ $code }}
                        </div>
                    @endfor
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Overlay --}}
<div id="slot-overlay" class="slot-overlay" onclick="closePopup()"></div>

{{-- Bottom Sheet --}}
<div id="slot-sheet" class="slot-sheet">
    <div class="sheet-handle"></div>
    <div id="slot-content"></div>
    <button class="slot-close-btn" onclick="closePopup()">Close</button>
</div>

@endsection

@push('scripts')
<script>
function showSlot(code) {
    fetch('/shelf/slot/' + code)
        .then(r => r.json())
        .then(data => {
            const content = document.getElementById('slot-content');

            if (!data.found) {
                content.innerHTML = `
                    <div class="slot-empty">
                        <div class="slot-empty-icon">📭</div>
                        <h3>Slot ${code} is Empty</h3>
                        <p>No product assigned here.</p>
                    </div>`;
            } else {
                const stockColor = data.stock <= 3
                    ? '#E74C3C'
                    : data.stock <= 10 ? '#E67E22' : '#27AE60';

                const imageHtml = data.image
                    ? `<img src="${data.image}" alt="${data.name}">`
                    : `<span>🛒</span>`;

                content.innerHTML = `
                    <div class="slot-tags">
                        <span class="slot-tag slot-tag-code">📍 ${data.code}</span>
                        <span class="slot-tag slot-tag-cat">${data.category}</span>
                    </div>
                    <div class="slot-product-row">
                        <div class="slot-product-img">${imageHtml}</div>
                        <div>
                            <div class="slot-product-name">${data.name}</div>
                            <div class="slot-product-price">${data.price}</div>
                            <div class="slot-product-stock">
                                Stock: <span style="color:${stockColor}; font-weight:700;">${data.stock}</span>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="/cart/add/${data.id}">
                        <input type="hidden" name="_token"
                            value="${document.querySelector('meta[name=csrf-token]').content}">
                        <button type="submit" class="slot-add-btn">🛒 Add to Cart</button>
                    </form>`;
            }

            document.getElementById('slot-overlay').classList.add('active');
            document.getElementById('slot-sheet').classList.add('active');
            document.body.style.overflow = 'hidden';
        });
}

function closePopup() {
    document.getElementById('slot-overlay').classList.remove('active');
    document.getElementById('slot-sheet').classList.remove('active');
    document.body.style.overflow = '';
}

// Close on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePopup();
});
</script>
@endpush