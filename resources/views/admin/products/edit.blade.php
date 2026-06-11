@extends('admin.layouts.app')

@section('title', 'Edit Product - Admin')
@section('page-title', 'Edit Product')

@section('content')

<div style="max-width:800px;">
    <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-gray admin-btn-sm" style="margin-bottom:20px; display:inline-block;">← Back</a>

    <div class="admin-card">
        @if($errors->any())
            <div class="alert-admin alert-admin-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div style="font-size:13px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Basic Info</div>

            <div class="form-grid-2">
                <div class="admin-form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="admin-form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-form-group">
                <label>Description</label>
                <textarea name="description" rows="2">{{ old('description', $product->description) }}</textarea>
            </div>

            {{-- Pricing --}}
            <div style="font-size:13px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:1px; margin:20px 0 12px;">Pricing & Stock</div>

            <div class="form-grid-3">
                <div class="admin-form-group">
                    <label>Selling Price (RM) *</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                </div>
                <div class="admin-form-group">
                    <label>Cost Price (RM)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0">
                </div>
                <div class="admin-form-group">
                    <label>Stock *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                </div>
            </div>

            {{-- Shelf & Expiry --}}
            <div style="font-size:13px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:1px; margin:20px 0 12px;">Location & Expiry</div>

            <div class="form-grid-2">
                <div class="admin-form-group">
                    <label>Shelf Code *</label>
                    <input type="text" name="shelf_code" value="{{ old('shelf_code', $product->shelf_code) }}"
                        required style="text-transform:uppercase;">
                    <p style="font-size:11px; color:#888; margin-top:4px;">
                        A = Beverage, B = Snacks, C = Instant Food, D = Stationery
                    </p>
                </div>
                <div class="admin-form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}">
                </div>
            </div>

            {{-- Shelf Visual Guide --}}
            <div style="background:#F7F7F7; border-radius:10px; padding:14px; margin-bottom:20px;">
                <div style="font-size:12px; font-weight:700; color:#888; margin-bottom:10px;">📍 Shelf Map — Current: <strong style="color:#C0392B;">{{ $product->shelf_code }}</strong></div>
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
                                transition:opacity 0.2s;
                                opacity:{{ $product->shelf_code == $letter.$i ? '1' : '0.5' }};
                                {{ $product->shelf_code == $letter.$i ? 'box-shadow:0 0 0 2px white, 0 0 0 4px '.$info[1].';' : '' }}"
                                onmouseover="this.style.opacity='1'"
                                onmouseout="this.style.opacity='{{ $product->shelf_code == $letter.$i ? '1' : '0.5' }}'"
                                title="{{ $letter.$i }}">
                                {{ $letter.$i }}
                            </div>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
                <p style="font-size:11px; color:#888; margin-top:8px;">👆 Click a slot to change shelf location.</p>
            </div>

            {{-- Image --}}
            <div style="font-size:13px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:1px; margin:20px 0 12px;">Product Image</div>

            <div class="admin-form-group">
                <div style="border:2px dashed #E0E0E0; border-radius:12px; padding:24px; text-align:center; cursor:pointer; transition:border-color 0.2s;"
                    onclick="document.getElementById('image-input').click()"
                    id="image-drop-zone">
                    <div id="image-preview" style="margin-bottom:12px;">
                        @if($product->image)
                            <img id="preview-img" src="{{ Storage::url($product->image) }}"
                                style="max-height:120px; border-radius:8px; object-fit:cover;">
                            <p style="font-size:11px; color:#888; margin-top:6px;">Current image — click to change</p>
                        @else
                            <img id="preview-img" style="max-height:120px; border-radius:8px; object-fit:cover; display:none;">
                            <div id="image-placeholder">
                                <div style="font-size:32px; margin-bottom:8px;">🖼️</div>
                                <div style="font-size:13px; font-weight:700; color:#888;">Click to upload image</div>
                                <div style="font-size:11px; color:#aaa; margin-top:4px;">JPG, PNG, WEBP — max 2MB</div>
                            </div>
                        @endif
                    </div>
                </div>
                <input type="file" id="image-input" name="image" accept="image/*" style="display:none;"
                    onchange="previewImage(this)">
                <p style="font-size:11px; color:#888; margin-top:6px;">Leave empty to keep current image.</p>
            </div>

            {{-- Featured --}}
            <div class="admin-form-group">
                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; background:#F7F7F7; padding:14px; border-radius:10px;">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}
                        style="width:18px; height:18px; accent-color:#C0392B;">
                    <div>
                        <div style="font-weight:700; font-size:14px;">⭐ Mark as Featured</div>
                        <div style="font-size:11px; color:#888; margin-top:2px;">Featured products appear on the home page.</div>
                    </div>
                </label>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="admin-btn admin-btn-primary">Save Changes</button>
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
            const img = document.getElementById('preview-img');
            img.src = e.target.result;
            img.style.display = 'block';
            const placeholder = document.getElementById('image-placeholder');
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('image-drop-zone').style.borderColor = '#C0392B';
    }
}
</script>
@endpush