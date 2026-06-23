@extends('admin.layouts.app')

@section('title', 'Inventory - Admin')
@section('page-title', 'Inventory Manager')

@section('content')

{{-- Alerts --}}
@if($expired->count() > 0)
<div class="alert-admin alert-admin-danger">
    ⚠️ {{ $expired->count() }} product(s) have <strong>expired</strong>!
</div>
@endif

@if($expiringSoon->count() > 0)
<div class="alert-admin alert-admin-warning">
    🕐 {{ $expiringSoon->count() }} product(s) expiring within 7 days!
</div>
@endif

@if($lowStock->count() > 0)
<div class="alert-admin alert-admin-warning">
    📉 {{ $lowStock->count() }} product(s) with low stock (≤ 3)!
</div>
@endif

{{-- Expired Products --}}
@if($expired->count() > 0)
<div class="admin-card">
    <div class="admin-card-title">🔴 Expired Products</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Shelf</th>
                <th>Stock</th>
                <th>Expiry Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expired as $product)
            <tr>
                <td style="font-weight:700;">{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td><span class="badge badge-blue">{{ $product->shelf_code }}</span></td>
                <td><span class="badge badge-red">{{ $product->stock }}</span></td>
                <td><span class="badge badge-red">{{ $product->expiry_date->format('d M Y') }}</span></td>
                <td>
                    <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn admin-btn-gray admin-btn-sm">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Expiring Soon --}}
@if($expiringSoon->count() > 0)
<div class="admin-card">
    <div class="admin-card-title">🟡 Expiring Soon (within 7 days)</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Shelf</th>
                <th>Stock</th>
                <th>Expiry Date</th>
                <th>Days Left</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expiringSoon as $product)
            <tr>
                <td style="font-weight:700;">{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td><span class="badge badge-blue">{{ $product->shelf_code }}</span></td>
                <td><span class="badge badge-orange">{{ $product->stock }}</span></td>
                <td>{{ $product->expiry_date->format('d M Y') }}</td>
                <td><span class="badge badge-orange">{{ now()->diffInDays($product->expiry_date) }} days</span></td>
                <td>
                    <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn admin-btn-gray admin-btn-sm">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Low Stock --}}
@if($lowStock->count() > 0)
<div class="admin-card">
    <div class="admin-card-title">📉 Low Stock Products</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Shelf</th>
                <th>Current Stock</th>
                <th>Adjust Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStock as $product)
            <tr>
                <td style="font-weight:700;">{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td><span class="badge badge-blue">{{ $product->shelf_code }}</span></td>
                <td><span class="badge badge-red">{{ $product->stock }}</span></td>
                <td>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <input type="number" class="stock-input" id="stock-{{ $product->id }}"
                            value="{{ $product->stock }}" min="0">
                        <button onclick="adjustStock({{ $product->id }})"
                            class="admin-btn admin-btn-primary admin-btn-sm">Update</button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- All Products --}}
<div class="admin-card">
    <div class="admin-card-title">📦 All Products — Stock & Restock by Carton</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Shelf</th>
                <th>Expiry</th>
                <th>Stock</th>
                <th>Adjust / Restock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td style="font-weight:700;">{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td><span class="badge badge-blue">{{ $product->shelf_code }}</span></td>
                <td>
                    @if($product->expiry_date)
                        @if($product->expiry_date->isPast())
                            <span class="badge badge-red">{{ $product->expiry_date->format('d M Y') }}</span>
                        @elseif($product->expiry_date->diffInDays(now()) <= 7)
                            <span class="badge badge-orange">{{ $product->expiry_date->format('d M Y') }}</span>
                        @else
                            <span class="badge badge-green">{{ $product->expiry_date->format('d M Y') }}</span>
                        @endif
                    @else
                        <span style="color:#aaa; font-size:12px;">—</span>
                    @endif
                </td>
                <td>
                    @if($product->stock <= 3)
                        <span class="badge badge-red">{{ $product->stock }}</span>
                    @elseif($product->stock <= 10)
                        <span class="badge badge-orange">{{ $product->stock }}</span>
                    @else
                        <span class="badge badge-green">{{ $product->stock }}</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                        {{-- Normal adjust --}}
                        <input type="number" class="stock-input" id="stock-{{ $product->id }}"
                            value="{{ $product->stock }}" min="0">
                        <button onclick="adjustStock({{ $product->id }})"
                            class="admin-btn admin-btn-primary admin-btn-sm">Update</button>
                        {{-- Carton restock --}}
                        <button onclick="openRestockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->units_per_carton }})"
                            class="admin-btn admin-btn-gray admin-btn-sm">📦 Carton</button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Restock by Carton Modal --}}
<div id="restock-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:28px; width:100%; max-width:460px; margin:20px; box-shadow:0 8px 40px rgba(0,0,0,0.2);">

        <div style="font-size:16px; font-weight:800; margin-bottom:4px;">
            📦 Restock by Carton
        </div>
        <div style="font-size:13px; color:#C0392B; font-weight:700; margin-bottom:20px;" id="modal-product-name"></div>

        <div class="admin-form-group">
            <label>Units Per Carton *</label>
            <input type="number" id="modal-upc" min="1" placeholder="e.g. 12"
                style="width:100%; padding:10px; border:1.5px solid #E0E0E0; border-radius:8px; font-size:14px;"
                oninput="updateModalPreview()">
            <p style="font-size:11px; color:#888; margin-top:4px;">How many units in 1 carton?</p>
        </div>

        <div class="admin-form-group">
            <label>Number of Cartons *</label>
            <input type="number" id="modal-cartons" min="1" value="1" placeholder="e.g. 2"
                style="width:100%; padding:10px; border:1.5px solid #E0E0E0; border-radius:8px; font-size:14px;"
                oninput="updateModalPreview()">
        </div>

        {{-- Preview --}}
        <div id="modal-preview" style="background:#F0FDF4; border:1px solid #A9DFBF; border-radius:8px; padding:12px; margin-bottom:16px; display:none;">
            <div style="font-size:13px; font-weight:700; color:#1E8449;" id="modal-preview-text"></div>
        </div>

        <div class="admin-form-group">
            <label>Expiry Date <span style="color:#888; font-weight:400;">(optional)</span></label>
            <input type="date" id="modal-expiry"
                style="width:100%; padding:10px; border:1.5px solid #E0E0E0; border-radius:8px; font-size:14px;">
            <p style="font-size:11px; color:#888; margin-top:4px;">Leave empty if no expiry (e.g. stationery).</p>
        </div>

        <div id="modal-error" style="display:none; color:#E74C3C; font-size:13px; margin-bottom:12px; background:#FEE2E2; padding:10px; border-radius:8px;"></div>

        <div style="display:flex; gap:12px;">
            <button onclick="submitRestock()" class="admin-btn admin-btn-primary" style="flex:1;">✓ Restock</button>
            <button onclick="closeRestockModal()" class="admin-btn admin-btn-gray">Cancel</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentProductId = null;

function openRestockModal(id, name, upc) {
    currentProductId = id;
    document.getElementById('modal-product-name').textContent = name;
    document.getElementById('modal-upc').value = upc || 12;
    document.getElementById('modal-cartons').value = 1;
    document.getElementById('modal-expiry').value = '';
    document.getElementById('modal-error').style.display = 'none';
    document.getElementById('restock-modal').style.display = 'flex';
    updateModalPreview();
}

function closeRestockModal() {
    document.getElementById('restock-modal').style.display = 'none';
    currentProductId = null;
}

function updateModalPreview() {
    const upc     = parseInt(document.getElementById('modal-upc').value) || 0;
    const cartons = parseInt(document.getElementById('modal-cartons').value) || 0;
    const total   = upc * cartons;
    const preview = document.getElementById('modal-preview');
    const text    = document.getElementById('modal-preview-text');

    if (upc > 0 && cartons > 0) {
        text.textContent = cartons + ' carton(s) × ' + upc + ' units = ' + total + ' units will be added to stock';
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function submitRestock() {
    const upc     = document.getElementById('modal-upc').value;
    const cartons = document.getElementById('modal-cartons').value;
    const expiry  = document.getElementById('modal-expiry').value;

    if (!upc || upc < 1) { showModalError('Please enter units per carton.'); return; }
    if (!cartons || cartons < 1) { showModalError('Please enter number of cartons.'); return; }

    fetch('/admin/products/' + currentProductId + '/restock', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            units_per_carton: upc,
            cartons: cartons,
            expiry_date: expiry
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeRestockModal();
            showAdminToast('📦 Restocked! +' + data.units + ' units. New stock: ' + data.new_stock);
            setTimeout(() => location.reload(), 1000);
        } else {
            showModalError('Something went wrong. Please try again.');
        }
    });
}

function showModalError(msg) {
    const el = document.getElementById('modal-error');
    el.textContent = msg;
    el.style.display = 'block';
}

function adjustStock(id) {
    const stock = document.getElementById('stock-' + id).value;
    fetch('/admin/inventory/' + id + '/stock', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ stock: stock })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAdminToast('✓ Stock updated to ' + data.stock);
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
        animation:slideIn 0.3s ease;`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}

// Close modal when click outside
document.getElementById('restock-modal').addEventListener('click', function(e) {
    if (e.target === this) closeRestockModal();
});
</script>
@endpush