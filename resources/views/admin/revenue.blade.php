@extends('admin.layouts.app')

@section('title', 'Revenue - Admin')
@section('page-title', 'Revenue & Sales Report')

@section('content')

{{-- Summary --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">RM {{ number_format($totalRevenue, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🧾</div>
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ $totalOrders }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-label">Avg Order Value</div>
        <div class="stat-value">RM {{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-label">This Month</div>
        <div class="stat-value">
            RM {{ number_format($monthlySales->firstWhere('month', now()->month)?->total ?? 0, 2) }}
        </div>
    </div>
</div>

{{-- Monthly Chart --}}
<div class="admin-card">
    <div class="admin-card-title">📈 Monthly Sales ({{ now()->year }})</div>
    @php
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $maxSale = $monthlySales->max('total') ?: 1;
    @endphp
    <div style="display:flex; align-items:flex-end; gap:8px; height:160px; padding:0 4px;">
        @foreach($months as $i => $month)
            @php
                $sale = $monthlySales->firstWhere('month', $i + 1);
                $height = $sale ? ($sale->total / $maxSale) * 140 : 0;
            @endphp
            <div style="flex:1; display:flex; flex-direction:column; align-items:center;">
                <div style="font-size:10px; color:#888; margin-bottom:4px;">
                    RM {{ number_format($sale->total ?? 0, 0) }}
                </div>
                <div style="width:100%; height:{{ max($height, 2) }}px; background:#B30000; border-radius:4px 4px 0 0;"
                    title="{{ $month }}: RM {{ number_format($sale->total ?? 0, 2) }}"></div>
                <div style="font-size:10px; color:#888; margin-top:4px;">{{ $month }}</div>
            </div>
        @endforeach
    </div>
</div>

{{-- Monthly Breakdown --}}
<div class="admin-card">
    <div class="admin-card-title">📋 Monthly Breakdown</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Month</th>
                <th>Orders</th>
                <th>Revenue</th>
                <th>Avg per Order</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlySales as $sale)
            <tr>
                <td style="font-weight:700;">{{ $months[$sale->month - 1] }} {{ now()->year }}</td>
                <td><span class="badge badge-blue">{{ $sale->count }}</span></td>
                <td style="font-weight:800; color:#B30000;">RM {{ number_format($sale->total, 2) }}</td>
                <td>RM {{ number_format($sale->total / $sale->count, 2) }}</td>
            </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; color:#888;">No sales data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Order History --}}
<div class="admin-card">
    <div class="admin-card-title">🧾 Order History</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td style="font-weight:700;">#{{ $order->id }}</td>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->items->count() }} items</td>
                <td style="font-weight:800; color:#B30000;">RM {{ number_format($order->total, 2) }}</td>
                <td><span class="badge badge-green">{{ $order->status }}</span></td>
                <td style="color:#888; font-size:12px;">{{ $order->created_at->format('d M Y H:i') }}</td>
            </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; color:#888;">No orders yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection