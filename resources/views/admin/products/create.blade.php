@extends('admin.layouts.app')

@section('title', 'Add Product - Admin')
@section('page-title', 'Add Product')

@section('content')

<div style="max-width:800px;">
    <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-gray admin-btn-sm" style="margin-bottom:20px; display:inline-block;">← Back</a>

    <div class="admin-card">
        @if($errors->any())
            <div class="alert-admin alert-admin-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Basic Info --}}
            <div style="font-size:13px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Basic Info</div>

            <div class="form-grid-2">
                <div class="admin-form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Milo Can 240ml">
                </div>
                <div class="admin-form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-form-group">
                <label>Description</label>
                <textarea name="description" rows="2" placeholder="Short product description (optional)">{{ old('description') }}</textarea>
            </div>

            {{-- Pricing --}}
            <div style="font-size:13px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:1px; margin:20px 0 12px;">Pricing & Stock</div>

            <div class="form-grid-3">
                <div class="admin-form-group">
                    <label>Selling Price (RM) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required placeholder="0.00">
                </div>
                <div class="admin-form-group">
                    <label>Cost Price (RM)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', 0) }}" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="admin-form-group">
                    <label>Stock *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required placeholder="0">
                </div>
            </div>

            {{-- Shelf & Expiry --}}
            <div style="font-size:13px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:1px; margin:20px 0 12px;">Location & Expiry</div>

            <div class="form-grid-2">
                <div class="admin-form-group">
                    <label>Shelf Code *</label>
                    <input type="text" name="shelf_code" value="{{ old('shelf_code') }}"
                        placeholder="e.g. A1, B3, C2" required
                        style="text-transform:uppercase;">
                    <p style="font-size:11px; color:#888; margin-top:4px;">
                        A = Beverage, B = Snacks, C = Instant Food, D = Stationery
                    </p>
                </div>
                <div class="admin-form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}">
                    <p style="font-size:11px; color:#888; margin-top:4px;">Leave empty if no expiry date.</p>
                </div>
            </div>

            {{-- Shelf Visual Guide --}}
            <div style="background:#F7F7F7; border-radius:10px; padding:14px; margin-bottom:20px;">
                <div style="font-size:12px; font-weight:700; color:#888; margin-bottom:10px;">📍 Shelf Map</div>
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    @foreach(['A' => ['Beverage','#2980B9'], 'B' => ['Snacks','#E67E22'], 'C' => ['Instant Food','#E74C3C'], 'D' => ['Stationery','#27AE60']] as $letter => $info)
                    <div>
                        <div style="font-size:10px; font-weight:700; color:#888; margin-bottom:4px;">{{ $letter }} — {{ $info[0] }}</div>
                        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:3px;">
                            @for($i = 1; $i <= 8; $i++)
                            <div onclick="document.querySelector('[name=shelf_code]').value='{{ $letter.$i }}'"
                                style="width:32px; height:32px; background:{{ $info[1] }}; border-radius:6px;
                                display:flex; align-items:center; justify-content:center;
                                font-size:9px; font-weight:800; color:white; cursor:pointer;
                                transition:opacity 0.2s; opacity:0.7;"
                                onmouseover="this.style.opacity='1'"
                                onmouseout="this.style.opacity='0.7'"
                                title="Click to select {{ $letter.$i }}">
                                {{ $letter.$i }}
                            </div>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
                <p style="font-size:11px; color:#888; margin-top:8px;">👆 Click a slot to auto-fill the shelf code above.</p>
            </div>

            {{-- Image --}}
            <div style="font-size:13px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:1px; margin:20px 0 12px;">Product Image</div>

            <div class="admin-form-group">
                <div style="border:2px dashed #E0E0E0; border-radius:12px; padding:24px; text-align:center; cursor:pointer; transition:border-color 0.2s;"
                    onclick="document.getElementById('image-input').click()"
                    id="image-drop-zone">
                    <div id="image-preview" style="display:none; margin-bottom:12px;">
                        <img id="preview-img" style="max-height:120px; border-radius:8px; object-fit:cover;">
                    </div>
                    <div id="image-placeholder">
                        <div style="font-size:32px; margin-bottom:8px;">🖼️</div>
                        <div style="font-size:13px; font-weight:700; color:#888;">Click to upload image</div>
                        <div style="font-size:11px; color:#aaa; margin-top:4px;">JPG, PNG, WEBP — max 2MB</div>
                    </div>
                </div>
                <input type="file" id="image-input" name="image" accept="image/*" style="display:none;"
                    onchange="previewImage(this)">
            </div>

            {{-- Featured --}}
            <div class="admin-form-group">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; background:#F7F7F7; padding:14px; border-radius:10px;">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                        style="width:18px; height:18px; accent-color:#C0392B;">
                    <div>
                        <div style="font-weight:700; font-size:14px;">⭐ Mark as Featured</div>
                        <div style="font-size:11px; color:#888; margin-top:2px;">Featured products appear on the home page.</div>
                    </div>
                </label>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="admin-btn admin-btn-primary">Add Product</button>
                <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-gray">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
            document.getElementById('image-placeholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('image-drop-zone').style.borderColor = '#C0392B';
    }
}
</script>
@endpush