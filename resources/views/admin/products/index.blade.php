@extends('admin.layouts.app')

@section('title', 'Products - Admin')
@section('page-title', 'Products')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <p style="color:#888; font-size:14px;">{{ $products->count() }} total products</p>
    <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn-primary">+ Add Product</a>
</div>

<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Cost</th>
                <th>Shelf</th>
                <th>Stock</th>
                <th>Expiry</th>
                <th>Featured</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td style="font-weight:700;">{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td>RM {{ number_format($product->price, 2) }}</td>
                <td>RM {{ number_format($product->cost_price, 2) }}</td>
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
                    @if($product->expiry_date)
                        @if($product->expiry_date->isPast())
                            <span class="badge badge-red">{{ $product->expiry_date->format('d M Y') }}</span>
                        @elseif($product->expiry_date->diffInDays(now()) <= 7)
                            <span class="badge badge-orange">{{ $product->expiry_date->format('d M Y') }}</span>
                        @else
                            <span class="badge badge-green">{{ $product->expiry_date->format('d M Y') }}</span>
                        @endif
                    @else
                        <span style="color:#888; font-size:12px;">—</span>
                    @endif
                </td>
                <td>
                    @if($product->is_featured)
                        <span class="badge badge-blue">⭐ Yes</span>
                    @else
                        <span class="badge badge-gray">No</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn admin-btn-gray admin-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                            onsubmit="return confirm('Delete {{ $product->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
                <tr><td colspan="9" style="text-align:center; color:#888;">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection