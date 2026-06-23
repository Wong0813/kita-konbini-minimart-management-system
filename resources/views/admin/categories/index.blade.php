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
                    <label>Color</label>

                    <div style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap; margin-bottom:8px;">

                        {{-- Wheel canvas --}}
                        <div style="position:relative; width:160px; height:160px; flex-shrink:0;">
                            <canvas id="color-wheel" width="160" height="160"
                                style="border-radius:50%; cursor:crosshair; display:block;"></canvas>
                            <div id="wheel-cursor"
                                style="position:absolute; width:14px; height:14px; border-radius:50%;
                                       border:2.5px solid #fff; box-shadow:0 0 0 1.5px rgba(0,0,0,.4);
                                       pointer-events:none; transform:translate(-50%,-50%);
                                       top:50%; left:50%;">
                            </div>
                        </div>

                        {{-- Right panel --}}
                        <div style="flex:1; min-width:140px; display:flex; flex-direction:column; gap:12px;">

                            {{-- Preview --}}
                            <div id="color-preview"
                                style="height:48px; border-radius:8px; border:1.5px solid #E0E0E0;
                                       background:{{ old('color','#2563EB') }}; transition:background .2s;">
                            </div>

                            {{-- Brightness --}}
                            <div>
                                <p style="font-size:11px; color:#888; margin:0 0 4px;">Brightness</p>
                                <input type="range" id="sl-bright" min="0" max="100" value="85"
                                    style="width:100%; accent-color:#2563EB;">
                            </div>

                            {{-- Saturation --}}
                            <div>
                                <p style="font-size:11px; color:#888; margin:0 0 4px;">Saturation</p>
                                <input type="range" id="sl-sat" min="0" max="100" value="80"
                                    style="width:100%; accent-color:#2563EB;">
                            </div>

                            {{-- Hidden real input + visible hex --}}
                            <input type="hidden" name="color" id="color-input" value="{{ old('color','#2563EB') }}">
                            <input type="text" id="color-hex" maxlength="7"
                                value="{{ old('color','#2563EB') }}"
                                placeholder="#2563EB"
                                style="padding:8px 10px; border:1.5px solid #E0E0E0; border-radius:8px;
                                       font-family:monospace; font-size:13px; width:100%; box-sizing:border-box;">
                        </div>
                    </div>

                    <p style="font-size:11px; color:#888; margin:0;">
                        Click the wheel to pick a hue, then adjust brightness &amp; saturation.
                    </p>
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

@push('scripts')
<script>
(function(){
    const canvas = document.getElementById('color-wheel');
    const ctx    = canvas.getContext('2d');
    const cur    = document.getElementById('wheel-cursor');
    const prev   = document.getElementById('color-preview');
    const inp    = document.getElementById('color-input');
    const hexEl  = document.getElementById('color-hex');
    const slB    = document.getElementById('sl-bright');
    const slS    = document.getElementById('sl-sat');
    const W = 160, R = W / 2;
    let hue = 220, sat = 80, bri = 85;

    function drawWheel() {
        ctx.clearRect(0, 0, W, W);
        for (let a = 0; a < 360; a++) {
            ctx.beginPath();
            ctx.moveTo(R, R);
            ctx.arc(R, R, R - 1, (a - 0.5) * Math.PI / 180, (a + 1.5) * Math.PI / 180);
            ctx.closePath();
            ctx.fillStyle = `hsl(${a},${sat}%,${bri}%)`;
            ctx.fill();
        }
        ctx.beginPath();
        ctx.arc(R, R, 20, 0, Math.PI * 2);
        ctx.fillStyle = `hsl(${hue},${sat}%,${bri}%)`;
        ctx.fill();
        ctx.strokeStyle = 'rgba(255,255,255,0.65)';
        ctx.lineWidth = 2;
        ctx.stroke();
    }

    function hslToHex(h, s, l) {
        s /= 100; l /= 100;
        const a = s * Math.min(l, 1 - l);
        const f = n => {
            const k = (n + h / 30) % 12;
            return Math.round(255 * (l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1)))
                .toString(16).padStart(2, '0');
        };
        return `#${f(0)}${f(8)}${f(4)}`;
    }

    function hexToHsl(hex) {
        let r = parseInt(hex.slice(1,3),16)/255,
            g = parseInt(hex.slice(3,5),16)/255,
            b = parseInt(hex.slice(5,7),16)/255;
        const max = Math.max(r,g,b), min = Math.min(r,g,b);
        let h, s, l = (max + min) / 2;
        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h *= 60;
        }
        return [Math.round(h), Math.round(s * 100), Math.round(l * 100)];
    }

    function placeCursor() {
        const rad  = hue * Math.PI / 180;
        const dist = R * 0.72;
        cur.style.left = (R + Math.cos(rad) * dist) + 'px';
        cur.style.top  = (R + Math.sin(rad) * dist) + 'px';
        cur.style.background = `hsl(${hue},${sat}%,${bri}%)`;
    }

    function updateAll() {
        drawWheel();
        placeCursor();
        const hex = hslToHex(hue, sat, bri);
        prev.style.background = hex;
        inp.value   = hex;
        hexEl.value = hex;
        slS.style.setProperty('accent-color', hex);
        slB.style.setProperty('accent-color', hex);
    }

    function pickFromWheel(e) {
        const rect = canvas.getBoundingClientRect();
        const cx = (e.clientX ?? e.touches[0].clientX) - rect.left - R;
        const cy = (e.clientY ?? e.touches[0].clientY) - rect.top  - R;
        if (Math.sqrt(cx*cx + cy*cy) > R) return;
        hue = ((Math.atan2(cy, cx) * 180 / Math.PI) + 360) % 360;
        updateAll();
    }

    let drag = false;
    canvas.addEventListener('mousedown',   e => { drag = true; pickFromWheel(e); });
    document.addEventListener('mousemove', e => { if (drag) pickFromWheel(e); });
    document.addEventListener('mouseup',   () => drag = false);
    canvas.addEventListener('touchstart',  e => { e.preventDefault(); pickFromWheel(e); }, { passive: false });
    canvas.addEventListener('touchmove',   e => { e.preventDefault(); pickFromWheel(e); }, { passive: false });

    slB.addEventListener('input', () => { bri = +slB.value; updateAll(); });
    slS.addEventListener('input', () => { sat = +slS.value; updateAll(); });

    hexEl.addEventListener('input', () => {
        if (/^#[0-9a-fA-F]{6}$/.test(hexEl.value)) {
            [hue, sat, bri] = hexToHsl(hexEl.value);
            slB.value = bri;
            slS.value = sat;
            updateAll();
        }
    });

    // Initialise from old() value if validation failed and page reloaded
    const initHex = '{{ old('color', '#2563EB') }}';
    [hue, sat, bri] = hexToHsl(initHex);
    slB.value = bri;
    slS.value = sat;
    updateAll();
})();
</script>
@endpush