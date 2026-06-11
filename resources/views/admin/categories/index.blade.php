@extends('admin.layouts.app')

@section('title', 'Categories - Admin')
@section('page-title', 'Categories')

@section('content')

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    {{-- Add Category Form --}}
    <div class="admin-card">
        <div class="admin-card-title">+ Add New Category</div>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert-admin alert-admin-danger">{{ $errors->first() }}</div>
            @endif
            <div class="admin-form-group">
                <label>Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="admin-form-group">
                <label>Slug * (e.g. instant-food)</label>
                <input type="text" name="slug" value="{{ old('slug') }}" required>
            </div>
            <div class="form-grid-2">
                <div class="admin-form-group">
                    <label>Icon (emoji)</label>
                    <input type="text" name="icon" value="{{ old('icon') }}" placeholder="🍜">
                </div>
                <div class="admin-form-group">
                    <label>Color (hex)</label>
                    <input type="text" name="color" value="{{ old('color') }}" placeholder="#EF4444">
                </div>
            </div>
            <button type="submit" class="admin-btn admin-btn-primary">Add Category</button>
        </form>
    </div>

    {{-- Categories List --}}
    <div class="admin-card">
        <div class="admin-card-title">All Categories</div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Products</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td style="font-size:24px;">{{ $category->icon }}</td>
                    <td style="font-weight:700;">{{ $category->name }}</td>
                    <td><span class="badge badge-gray">{{ $category->slug }}</span></td>
                    <td><span class="badge badge-blue">{{ $category->products_count }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                            onsubmit="return confirm('Delete {{ $category->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection