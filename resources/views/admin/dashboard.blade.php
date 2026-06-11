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

{{-- Alerts --}}
@if($expired > 0)
<div class="alert-admin alert-admin-danger">
    ⚠️ {{ $expired }} product(s) have <strong>expired</strong>!
    <a href="{{ route('admin.inventory') }}" style="color:#991B1B; font-weight:800; margin-left:8px;">View Inventory →</a>
</div>
@endif

@if($expiringSoon > 0)
<div class="alert-admin alert-admin-warning">
    🕐 {{ $expiringSoon }} product(s) are <strong>expiring within 7 days</strong>.
    <a href="{{ route('admin.inventory') }}" style="color:#92400E; font-weight:800; margin-left:8px;">View Inventory →</a>
</div>
@endif

@if($lowStock > 0)
<div class="alert-admin alert-admin-warning">
    📉 {{ $lowStock }} product(s) have <strong>low stock (≤ 3)</strong>.
    <a href="{{ route('admin.inventory') }}" style="color:#92400E; font-weight:800; margin-left:8px;">View Inventory →</a>
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
                    $sale = $monthlySales->firstWhere('month', $i + 1);
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