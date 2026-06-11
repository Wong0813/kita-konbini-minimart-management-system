@extends('layouts.auth')

@section('content')
<div class="auth-split">

    {{-- Left Side - Real Store Photo --}}
    <div class="auth-left" style="position:relative; overflow:hidden; padding:0;">

        {{-- Store photo background --}}
        <img src="{{ asset('images/store/shelves1.jpg') }}" alt="Kita Konbini Store"
            style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;">

        {{-- Dark overlay --}}
        <div style="position:absolute; inset:0;
            background:linear-gradient(135deg, rgba(192,57,43,0.85) 0%, rgba(50,10,5,0.92) 100%);">
        </div>

        {{-- Content on top --}}
        <div class="auth-left-content" style="position:relative; z-index:1; padding:48px 40px; height:100%; display:flex; flex-direction:column; justify-content:center;">

            <div class="auth-brand" style="margin-bottom:24px;">
                <p class="brand-sub">Kita コンビニ</p>
                <span class="brand-main">MINIMART</span>
            </div>

            <p class="auth-tagline">Your one-stop campus minimart for snacks, beverages, instant food & stationery.</p>

            <div class="auth-features">
                <div class="auth-feature-item">
                    <span>🛒</span>
                    <p>Shop your favourite products anytime</p>
                </div>
                <div class="auth-feature-item">
                    <span>📍</span>
                    <p>Find products using our shelf locator</p>
                </div>
                <div class="auth-feature-item">
                    <span>♥</span>
                    <p>Save products to your wishlist</p>
                </div>
                <div class="auth-feature-item">
                    <span>⚡</span>
                    <p>Fast checkout, no hassle</p>
                </div>
            </div>

            {{-- Store stats --}}
            <div style="display:flex; gap:24px; margin-top:32px;">
                <div style="text-align:center;">
                    <div style="font-family:'Bebas Neue',cursive; font-size:32px; color:white; line-height:1;">20+</div>
                    <div style="font-size:11px; color:rgba(255,255,255,0.6); font-weight:600; text-transform:uppercase; letter-spacing:1px;">Products</div>
                </div>
                <div style="width:1px; background:rgba(255,255,255,0.2);"></div>
                <div style="text-align:center;">
                    <div style="font-family:'Bebas Neue',cursive; font-size:32px; color:white; line-height:1;">4</div>
                    <div style="font-size:11px; color:rgba(255,255,255,0.6); font-weight:600; text-transform:uppercase; letter-spacing:1px;">Categories</div>
                </div>
                <div style="width:1px; background:rgba(255,255,255,0.2);"></div>
                <div style="text-align:center;">
                    <div style="font-family:'Bebas Neue',cursive; font-size:32px; color:white; line-height:1;">UPSI</div>
                    <div style="font-size:11px; color:rgba(255,255,255,0.6); font-weight:600; text-transform:uppercase; letter-spacing:1px;">Campus</div>
                </div>
            </div>

        </div>
    </div>

    {{-- Right Side - Login Form --}}
    <div class="auth-right">
        <div class="auth-card">

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Logo small --}}
            <div style="margin-bottom:28px;">
                <div style="font-family:'Bebas Neue',cursive; font-size:28px; color:var(--red-primary); line-height:1;">KITA KONBINI</div>
                <div style="font-size:12px; color:var(--gray-text); font-weight:600; letter-spacing:2px; text-transform:uppercase;">Minimart</div>
            </div>

            <h2 style="font-size:24px; font-weight:800; margin-bottom:6px;">Welcome back 👋</h2>
            <p style="color:var(--gray-text); font-size:14px; margin-bottom:28px;">Log in to your account to continue.</p>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label for="matric_id">Matric Number / ID <span class="required">*</span></label>
                    <input type="text" id="matric_id" name="matric_id"
                        value="{{ old('matric_id') }}"
                        class="{{ $errors->has('matric_id') ? 'is-invalid' : '' }}"
                        required autofocus placeholder="e.g. A12345">
                    @error('matric_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                            required placeholder="Enter your password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                            <svg id="eye-off-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg id="eye-on-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">Log In</button>
            </form>

            <p class="auth-footer-text mt-2">
                Don't have an account? <a href="{{ route('register') }}">Sign Up</a>
            </p>

            <p style="text-align:center; margin-top:20px; font-size:11px; color:var(--gray-text);">
                KitaKonbiniMinimart.com
            </p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const input  = document.getElementById(fieldId);
    const eyeOff = document.getElementById('eye-off-' + fieldId);
    const eyeOn  = document.getElementById('eye-on-'  + fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        eyeOff.style.display = 'none';
        eyeOn.style.display  = 'block';
    } else {
        input.type = 'password';
        eyeOff.style.display = 'block';
        eyeOn.style.display  = 'none';
    }
}
</script>
@endpush