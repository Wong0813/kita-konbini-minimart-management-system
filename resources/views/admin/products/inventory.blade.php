@extends('admin.layouts.app')

@section('title', 'Inventory - Admin')
@section('page-title', 'Inventory Manager')

@section('content')

{{-- Alerts --}}
@if($batchesExpired->count() > 0)
<div class="alert-admin alert-admin-danger">
    ⚠️ {{ $batchesExpired->count() }} batch(es) have expired and still have remaining stock!
</div>
@endif

@if($batchesExpiringSoon->count() > 0)
<div class="alert-admin alert-admin-warning">
    🕐 {{ $batchesExpiringSoon->count() }} batch(es) expiring within 7 days!
</div>
@endif

@if($lowStock->count() > 0)
<div class="alert-admin alert-admin-warning">
    📉 {{ $lowStock->count() }} product(s) with low stock (≤ 3)!
</div>
@endif

{{-- Expired Batches --}}
@if($batchesExpired->count() > 0)
<div class="admin-card">
    <div class="admin-card-title">🔴 Expired Batches (Still In Stock)</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Shelf</th>
                <th>Batch Qty</th>
                <th>Expiry Date</th>
                <th>Received</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($batchesExpired as $batch)
            <tr>
                <td style="font-weight:700;">{{ $batch->product->name }}</td>
                <td>{{ $batch->product->category->name }}</td>
                <td><span class="badge badge-blue">{{ $batch->product->shelf_code }}</span></td>
                <td><span class="badge badge-red">{{ $batch->quantity }} units</span></td>
                <td><span class="badge badge-red">{{ $batch->expiry_date->format('d M Y') }}</span></td>
                <td>{{ $batch->received_date->format('d M Y') }}</td>
                <td style="display:flex; gap:6px;">
                    <a href="{{ route('admin.products.edit', $batch->product) }}" class="admin-btn admin-btn-gray admin-btn-sm">Edit</a>
                    <button onclick="deleteBatch({{ $batch->id }}, this)" class="admin-btn admin-btn-sm" style="background:#E74C3C; color:white;">Remove</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Expiring Soon Batches --}}
@if($batchesExpiringSoon->count() > 0)
<div class="admin-card">
    <div class="admin-card-title">🟡 Batches Expiring Soon (within 7 days)</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Shelf</th>
                <th>Batch Qty</th>
                <th>Expiry Date</th>
                <th>Days Left</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($batchesExpiringSoon as $batch)
            <tr>
                <td style="font-weight:700;">{{ $batch->product->name }}</td>
                <td>{{ $batch->product->category->name }}</td>
                <td><span class="badge badge-blue">{{ $batch->product->shelf_code }}</span></td>
                <td><span class="badge badge-orange">{{ $batch->quantity }} units</span></td>
                <td>{{ $batch->expiry_date->format('d M Y') }}</td>
                <td><span class="badge badge-orange">{{ now()->diffInDays($batch->expiry_date) }} days</span></td>
                <td>
                    <a href="{{ route('admin.products.edit', $batch->product) }}" class="admin-btn admin-btn-gray admin-btn-sm">Edit</a>
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

{{-- All Products + Batch Tracking --}}
<div class="admin-card">
    <div class="admin-card-title">📦 All Products — Stock & Batch Tracker</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Shelf</th>
                <th>Total Stock</th>
                <th>Batches</th>
                <th>Restock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td style="font-weight:700;">{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td><span class="badge badge-blue">{{ $product->shelf_code }}</span></td>
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
                    @if($product->batches->count() > 0)
                        <button onclick="toggleBatches({{ $product->id }})"
                            class="admin-btn admin-btn-gray admin-btn-sm">
                            📋 {{ $product->batches->count() }} batch(es)
                        </button>
                    @else
                        <span style="color:#aaa; font-size:12px;">No batches</span>
                    @endif
                </td>
                <td>
                    <button onclick="openRestockModal({{ $product->id }}, '{{ addslashes($product->name) }}')"
                        class="admin-btn admin-btn-primary admin-btn-sm">+ Restock</button>
                </td>
            </tr>

            {{-- Batch detail row (hidden by default) --}}
            @if($product->batches->count() > 0)
            <tr id="batches-{{ $product->id }}" style="display:none;">
                <td colspan="6" style="padding:0 12px 12px; background:#FAFAFA;">
                    <table style="width:100%; border-collapse:collapse; font-size:12px;">
                        <thead>
                            <tr style="color:#888; font-weight:700;">
                                <td style="padding:6px;">Received</td>
                                <td style="padding:6px;">Qty</td>
                                <td style="padding:6px;">Expiry Date</td>
                                <td style="padding:6px;">Status</td>
                                <td style="padding:6px;">Notes</td>
                                <td style="padding:6px;">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->batches as $batch)
                            <tr style="border-top:1px solid #eee;" id="batch-row-{{ $batch->id }}">
                                <td style="padding:6px;">{{ $batch->received_date->format('d M Y') }}</td>
                                <td style="padding:6px;"><strong>{{ $batch->quantity }}</strong> units</td>
                                <td style="padding:6px;">
                                    @if($batch->expiry_date)
                                        {{ $batch->expiry_date->format('d M Y') }}
                                    @else
                                        <span style="color:#aaa;">—</span>
                                    @endif
                                </td>
                                <td style="padding:6px;">
                                    @if(!$batch->expiry_date)
                                        <span class="badge badge-blue">No Expiry</span>
                                    @elseif($batch->expiry_date->isPast())
                                        <span class="badge badge-red">Expired</span>
                                    @elseif($batch->expiry_date->diffInDays(now()) <= 7)
                                        <span class="badge badge-orange">Expiring Soon</span>
                                    @else
                                        <span class="badge badge-green">Good</span>
                                    @endif
                                </td>
                                <td style="padding:6px; color:#888;">{{ $batch->notes ?? '—' }}</td>
                                <td style="padding:6px;">
                                    <button onclick="deleteBatch({{ $batch->id }}, this)"
                                        class="admin-btn admin-btn-sm" style="background:#E74C3C; color:white; font-size:11px;">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endif

            @endforeach
        </tbody>
    </table>
</div>

{{-- Restock Modal --}}
<div id="restock-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:28px; width:100%; max-width:460px; margin:20px;">
        <div style="font-size:16px; font-weight:800; margin-bottom:20px;">
            ➕ Restock — <span id="modal-product-name" style="color:#C0392B;"></span>
        </div>

        <div class="admin-form-group">
            <label>Quantity Received *</label>
            <input type="number" id="batch-quantity" min="1" placeholder="e.g. 24" style="width:100%; padding:10px; border:1.5px solid #E0E0E0; border-radius:8px; font-size:14px;">
        </div>

        <div class="admin-form-group">
            <label>Expiry Date</label>
            <input type="date" id="batch-expiry" style="width:100%; padding:10px; border:1.5px solid #E0E0E0; border-radius:8px; font-size:14px;">
            <p style="font-size:11px; color:#888; margin-top:4px;">Leave empty if product has no expiry (e.g. stationery).</p>
        </div>

        <div class="admin-form-group">
            <label>Date Received *</label>
            <input type="date" id="batch-received" style="width:100%; padding:10px; border:1.5px solid #E0E0E0; border-radius:8px; font-size:14px;">
        </div>

        <div class="admin-form-group">
            <label>Notes (optional)</label>
            <input type="text" id="batch-notes" placeholder="e.g. Supplier: ABC Trading" style="width:100%; padding:10px; border:1.5px solid #E0E0E0; border-radius:8px; font-size:14px;">
        </div>

        <div id="modal-error" style="display:none; color:#E74C3C; font-size:13px; margin-bottom:12px;"></div>

        <div style="display:flex; gap:12px;">
            <button onclick="submitRestock()" class="admin-btn admin-btn-primary" style="flex:1;">Add Batch</button>
            <button onclick="closeRestockModal()" class="admin-btn admin-btn-gray">Cancel</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentProductId = null;

// Set today as default received date
document.getElementById('batch-received').value = new Date().toISOString().split('T')[0];

function openRestockModal(id, name) {
    currentProductId = id;
    document.getElementById('modal-product-name').textContent = name;
    document.getElementById('batch-quantity').value = '';
    document.getElementById('batch-expiry').value = '';
    document.getElementById('batch-received').value = new Date().toISOString().split('T')[0];
    document.getElementById('batch-notes').value = '';
    document.getElementById('modal-error').style.display = 'none';
    document.getElementById('restock-modal').style.display = 'flex';
}

function closeRestockModal() {
    document.getElementById('restock-modal').style.display = 'none';
    currentProductId = null;
}

function submitRestock() {
    const qty      = document.getElementById('batch-quantity').value;
    const expiry   = document.getElementById('batch-expiry').value;
    const received = document.getElementById('batch-received').value;
    const notes    = document.getElementById('batch-notes').value;

    if (!qty || qty < 1) {
        showModalError('Please enter a valid quantity.');
        return;
    }
    if (!received) {
        showModalError('Please enter received date.');
        return;
    }

    fetch(`/admin/inventory/${currentProductId}/batch`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ quantity: qty, expiry_date: expiry, received_date: received, notes: notes })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeRestockModal();
            location.reload();
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

function toggleBatches(id) {
    const row = document.getElementById('batches-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

function deleteBatch(id, btn) {
    if (!confirm('Remove this batch? Stock will be reduced accordingly.')) return;
    fetch(`/admin/inventory/batch/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
    });
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
            alert('Stock updated to ' + data.stock);
            location.reload();
        }
    });
}
</script>
@endpush
