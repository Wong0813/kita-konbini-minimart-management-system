@extends('layouts.auth')

@section('content')
<div class="auth-page">

    {{-- Logo --}}
    <div class="auth-logo">
        <p class="brand-sub">Kita Konbini</p>
        <span class="brand-main">MINIMART</span>
    </div>

    {{-- Login Card --}}
    <div class="auth-card">
        <div class="back-btn"></div>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label for="matric_id">
                    matric Number/ ID <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="matric_id"
                    name="matric_id"
                    value="{{ old('matric_id') }}"
                    class="{{ $errors->has('matric_id') ? 'is-invalid' : '' }}"
                    required
                    autofocus
                >
                @error('matric_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">
                    Password <span class="required">*</span>
                </label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        required
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                        {{-- Eye crossed (hidden) --}}
                        <svg id="eye-off-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        {{-- Eye (visible) --}}
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
    </div>

    <p class="auth-domain">KitaKonbiniMinimart.com</p>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
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
