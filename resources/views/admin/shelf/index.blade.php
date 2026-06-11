@extends('admin.layouts.app')

@section('title', 'Shelf Manager - Admin')
@section('page-title', 'Shelf Manager')

@section('content')

<div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start;">

    {{-- Shelf Grid --}}
    <div class="admin-card">
        <div class="admin-card-title">🗺️ Shelf Layout — Click a slot to assign a product</div>

        <div style="display:flex; gap:20px; flex-wrap:wrap;">
            @foreach($sections as $letter => $info)
            <div>
                <div style="font-size:12px; font-weight:700; color:#888; margin-bottom:8px; text-transform:uppercase;">
                    {{ $letter }} — {{ $info['name'] }}
                </div>
                <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:6px;">
                    @for($i = 1; $i <= 16; $i++)
                        @php
                            $code    = $letter . $i;
                            $product = $shelfMap->get($code);
                        @endphp
                        <div class="shelf-admin-slot slot-{{ $info['color'] }} {{ $product ? 'occupied' : 'empty' }}"
                            data-code="{{ $code }}"
                            onclick="selectSlot('{{ $code }}', '{{ $product?->name ?? '' }}', {{ $product?->id ?? 'null' }})"
                            title="{{ $code }}: {{ $product?->name ?? 'Empty' }}">
                            <span class="slot-code">{{ $code }}</span>
                            @if($product)
                                <span class="slot-product">{{ Str::limit($product->name, 8) }}</span>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Assignment Panel --}}
    <div>
        {{-- Selected Slot Info --}}
        <div class="admin-card" id="slot-panel" style="display:none;">
            <div class="admin-card-title">📍 Selected Slot: <span id="selected-code" style="color:#B30000;"></span></div>

            <div id="current-product" style="margin-bottom:16px; padding:12px; background:#F7F7F7; border-radius:8px; font-size:13px;">
                Loading...
            </div>

            <div class="admin-form-group">
                <label>Assign Product</label>
                <select id="product-select" style="width:100%; padding:10px; border:1.5px solid #E0E0E0; border-radius:8px; font-family:Nunito,sans-serif; font-size:13px; outline:none;">
                    <option value="">— Select product —</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-shelf="{{ $product->shelf_code }}">
                            {{ $product->name }}
                            @if($product->shelf_code)
                                (currently: {{ $product->shelf_code }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex; gap:8px;">
                <button onclick="assignProduct()" class="admin-btn admin-btn-primary" style="flex:1;">
                    ✓ Assign
                </button>
                <button onclick="clearSlot()" class="admin-btn admin-btn-danger">
                    🗑 Clear
                </button>
            </div>
        </div>

        {{-- Legend --}}
        <div class="admin-card">
            <div class="admin-card-title">Legend</div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:24px; height:24px; background:#2980B9; border-radius:6px;"></div>
                    <span style="font-size:13px;">A — Beverage</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:24px; height:24px; background:#E67E22; border-radius:6px;"></div>
                    <span style="font-size:13px;">B — Snacks</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:24px; height:24px; background:#E74C3C; border-radius:6px;"></div>
                    <span style="font-size:13px;">C — Instant Food</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:24px; height:24px; background:#27AE60; border-radius:6px;"></div>
                    <span style="font-size:13px;">D — Stationery</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px; margin-top:4px;">
                    <div style="width:24px; height:24px; background:#E0E0E0; border-radius:6px; border:2px dashed #999;"></div>
                    <span style="font-size:13px; color:#888;">Empty slot</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:24px; height:24px; background:#27AE60; border-radius:6px; box-shadow:0 0 0 3px rgba(39,174,96,0.3);"></div>
                    <span style="font-size:13px;">Occupied slot</span>
                </div>
            </div>
        </div>

        {{-- Unassigned Products --}}
        <div class="admin-card">
            <div class="admin-card-title">📦 Unassigned Products</div>
            @php $unassigned = $products->whereNull('shelf_code'); @endphp
            @if($unassigned->isEmpty())
                <p style="color:#888; font-size:13px;">All products are assigned! ✅</p>
            @else
                <div style="display:flex; flex-direction:column; gap:6px;">
                    @foreach($unassigned as $p)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 12px; background:#FDF2F0; border-radius:8px; font-size:13px;">
                        <span style="font-weight:700;">{{ $p->name }}</span>
                        <span style="color:#888; font-size:11px;">{{ $p->category->name }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
.shelf-admin-slot {
    width: 52px; height: 52px;
    border-radius: 8px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    cursor: pointer; color: white;
    transition: all 0.2s;
    font-size: 10px; font-weight: 800;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.shelf-admin-slot:hover { transform: scale(1.08); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }

.shelf-admin-slot.selected { border-color: white; box-shadow: 0 0 0 3px rgba(0,0,0,0.3); transform: scale(1.08); }

.slot-blue   { background: #2980B9; }
.slot-orange { background: #E67E22; }
.slot-pink   { background: #E74C3C; }
.slot-green  { background: #27AE60; }

.shelf-admin-slot.empty { opacity: 0.4; border: 2px dashed rgba(255,255,255,0.5); }
.shelf-admin-slot.occupied { opacity: 1; }

.slot-code    { font-size: 11px; font-weight: 800; }
.slot-product { font-size: 7px; opacity: 0.85; text-align: center; line-height: 1.2; max-width: 48px; overflow: hidden; }
</style>
@endpush

@push('scripts')
<script>
let selectedCode = null;

function selectSlot(code, productName, productId) {
    selectedCode = code;

    // Highlight selected
    document.querySelectorAll('.shelf-admin-slot').forEach(s => s.classList.remove('selected'));
    document.querySelector(`[data-code="${code}"]`).classList.add('selected');

    // Show panel
    document.getElementById('slot-panel').style.display = 'block';
    document.getElementById('selected-code').textContent = code;

    // Show current product
    const current = document.getElementById('current-product');
    if (productId) {
        current.innerHTML = `<strong>Current:</strong> ${productName} <span style="color:#27AE60; font-weight:700;">✓ Occupied</span>`;
        // Pre-select the product
        document.getElementById('product-select').value = productId;
    } else {
        current.innerHTML = `<span style="color:#888;">Empty slot — no product assigned</span>`;
        document.getElementById('product-select').value = '';
    }
}

function assignProduct() {
    const productId = document.getElementById('product-select').value;
    if (!productId) { alert('Please select a product!'); return; }
    if (!selectedCode) { alert('Please select a slot first!'); return; }

    fetch('/admin/shelf/assign', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, shelf_code: selectedCode })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAdminToast(`✓ ${data.product} assigned to ${data.shelf_code}`);
            setTimeout(() => location.reload(), 800);
        }
    });
}

function clearSlot() {
    if (!selectedCode) return;
    if (!confirm(`Clear slot ${selectedCode}?`)) return;

    fetch('/admin/shelf/clear', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ shelf_code: selectedCode })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAdminToast(`Slot ${selectedCode} cleared`);
            setTimeout(() => location.reload(), 800);
        }
    });
}

function showAdminToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = `position:fixed; bottom:24px; right:24px; background:#1A1A1A; color:white;
        padding:12px 20px; border-radius:50px; font-size:13px; font-weight:700;
        z-index:9999; box-shadow:0 4px 16px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease forwards;`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2000);
}
</script>
@endpush