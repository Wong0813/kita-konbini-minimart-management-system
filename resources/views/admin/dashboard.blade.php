@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stat Cards --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-label">Total Products</div>
        <div class="stat-value">{{ $totalProducts }}</div>
        <div class="stat-sub">{{ $lowStock }} low stock</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏷️</div>
        <div class="stat-label">Categories</div>
        <div class="stat-value">{{ $totalCategories }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-label">Total Users</div>
        <div class="stat-value">{{ $totalUsers }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">RM {{ number_format($totalRevenue, 2) }}</div>
        <div class="stat-sub">{{ $totalOrders }} orders</div>
    </div>
</div>

{{-- Alert Popup Modal --}}
@if($expired > 0 || $expiringSoon > 0 || $lowStock > 0)
<div id="alert-overlay" style="position:fixed; inset:0; background:rgba(0,0,0,0.5);
     z-index:1000; display:flex; align-items:center; justify-content:center;
     backdrop-filter:blur(3px);">
    <div style="background:white; border-radius:20px; padding:32px; width:100%;
                max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,0.2); position:relative;">

        {{-- Header --}}
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
            <div style="background:#FEF3C7; width:48px; height:48px; border-radius:12px;
                        display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                ⚠️
            </div>
            <div>
                <div style="font-size:18px; font-weight:800; color:#1a1a1a;">Action Required</div>
                <div style="font-size:13px; color:#888; margin-top:2px;">Please review the following alerts</div>
            </div>
            <button onclick="closeAlertPopup()"
                style="position:absolute; top:16px; right:16px; background:#f0f0f0;
                       border:none; width:32px; height:32px; border-radius:50%;
                       cursor:pointer; font-size:14px; display:flex;
                       align-items:center; justify-content:center;">✕</button>
        </div>

        {{-- Alert Items --}}
        <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:24px;">

            @if($expired > 0)
            <div style="background:#FEF2F2; border:1.5px solid #FECACA; border-radius:12px; padding:14px 16px;
                        display:flex; align-items:center; gap:12px;">
                <span style="font-size:20px;">🚨</span>
                <div>
                    <div style="font-size:13px; font-weight:800; color:#991B1B;">{{ $expired }} product(s) EXPIRED</div>
                    <div style="font-size:12px; color:#B91C1C; margin-top:2px;">Remove or replace these items immediately</div>
                </div>
            </div>
            @endif

            @if($expiringSoon > 0)
            <div style="background:#FFFBEB; border:1.5px solid #FDE68A; border-radius:12px; padding:14px 16px;
                        display:flex; align-items:center; gap:12px;">
                <span style="font-size:20px;">🕐</span>
                <div>
                    <div style="font-size:13px; font-weight:800; color:#92400E;">{{ $expiringSoon }} product(s) expiring within 7 days</div>
                    <div style="font-size:12px; color:#B45309; margin-top:2px;">Check inventory and plan promotions</div>
                </div>
            </div>
            @endif

            @if($lowStock > 0)
            <div style="background:#FFFBEB; border:1.5px solid #FDE68A; border-radius:12px; padding:14px 16px;
                        display:flex; align-items:center; gap:12px;">
                <span style="font-size:20px;">📉</span>
                <div>
                    <div style="font-size:13px; font-weight:800; color:#92400E;">{{ $lowStock }} product(s) low stock (≤ 3)</div>
                    <div style="font-size:12px; color:#B45309; margin-top:2px;">Restock soon to avoid running out</div>
                </div>
            </div>
            @endif

        </div>

        {{-- Actions --}}
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.inventory') }}"
               style="flex:1; padding:12px; background:#C0392B; color:white;
                      border-radius:10px; font-size:14px; font-weight:800;
                      text-decoration:none; text-align:center;
                      transition:background 0.2s;"
               onmouseover="this.style.background='#a93226'"
               onmouseout="this.style.background='#C0392B'">
                🗄️ View Inventory
            </a>
            <button onclick="closeAlertPopup()"
                style="padding:12px 20px; background:#f0f0f0; color:#333;
                       border:none; border-radius:10px; font-size:14px;
                       font-weight:700; cursor:pointer; transition:background 0.2s;"
                onmouseover="this.style.background='#e0e0e0'"
                onmouseout="this.style.background='#f0f0f0'">
                Dismiss
            </button>
        </div>

    </div>
</div>
@endif

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    {{-- Monthly Sales Chart --}}
    <div class="admin-card">
        <div class="admin-card-title">📈 Monthly Sales ({{ now()->year }})</div>
        @php
            $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $maxSale = $monthlySales->max('total') ?: 1;
        @endphp
        <div class="month-bar-wrapper">
            @foreach($months as $i => $month)
                @php
                    $sale = $monthlySales->get($i + 1);
                    $height = $sale ? ($sale->total / $maxSale) * 100 : 0;
                @endphp
                <div style="flex:1; display:flex; flex-direction:column; align-items:center;">
                    <div class="month-bar" style="width:100%; height:{{ max($height, 2) }}px;"
                        title="{{ $month }}: RM {{ number_format($sale->total ?? 0, 2) }}"></div>
                    <div class="month-bar-label">{{ $month }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="admin-card">
        <div class="admin-card-title">🧾 Recent Orders</div>
        @if($recentOrders->isEmpty())
            <p style="color:#888; font-size:13px;">No orders yet.</p>
        @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr>
                    <td>{{ $order->user->name }}</td>
                    <td>RM {{ number_format($order->total, 2) }}</td>
                    <td><span class="badge badge-green">{{ $order->status }}</span></td>
                    <td style="color:#888; font-size:12px;">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

{{-- Quick Actions --}}
<div class="admin-card">
    <div class="admin-card-title">⚡ Quick Actions</div>
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn-primary">+ Add Product</a>
        <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn-gray">+ Add Category</a>
        <a href="{{ route('admin.inventory') }}" class="admin-btn admin-btn-gray">🗄️ Inventory</a>
        <a href="{{ route('admin.revenue') }}" class="admin-btn admin-btn-gray">💰 Revenue Report</a>
        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-gray">👥 View Users</a>
    </div>
</div>

@endsection

@push('scripts')
<script>
function closeAlertPopup() {
    const overlay = document.getElementById('alert-overlay');
    if (overlay) {
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.3s';
        setTimeout(() => overlay.remove(), 300);
    }
}
</script>
@endpush