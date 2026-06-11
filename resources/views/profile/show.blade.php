@extends('layouts.app')

@section('title', 'My Profile - Kita Konbini')

@section('content')

<section class="hero">
    <div class="hero-content">
        <h1>My <span>Profile</span></h1>
        <p>Manage your account details.</p>
    </div>
</section>

<section class="section">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    {{-- Avatar --}}
    <div class="profile-avatar">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 style="font-size:18px; font-weight:800; margin-top:12px;">{{ $user->name }}</h3>
        <p style="color:var(--gray-text); font-size:13px;">{{ $user->matric_id }}</p>
    </div>

    {{-- Profile Form --}}
    <div style="background:var(--white); border-radius:var(--radius); padding:24px; box-shadow:var(--shadow-card);">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf

            <div class="form-group">
                <label>Name <span class="required">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label>Matric Number / ID <span class="required">*</span></label>
                <input type="text" name="matric_id" value="{{ old('matric_id', $user->matric_id) }}" required>
            </div>

            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label>New Password <span style="color:var(--gray-text); font-weight:400;">(leave blank to keep current)</span></label>
                <input type="password" name="password">
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation">
            </div>

            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
    </div>

    {{-- Logout --}}
    <div style="margin-top:20px; text-align:center;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none; border:none; color:var(--red-primary); font-family:Nunito,sans-serif; font-size:14px; font-weight:700; cursor:pointer; text-decoration:underline;">
                Log Out
            </button>
        </form>
    </div>

</section>

@endsection