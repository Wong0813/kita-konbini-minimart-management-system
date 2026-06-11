<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Kita Konbini')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <a href="{{ route('admin.shelf.manage') }}" class="{{ request()->routeIs('admin.shelf.*') ? 'active' : '' }}">
    
</a>
</head>
<body>
<div class="admin-wrapper">

    {{-- Sidebar --}}
    <aside class="admin-sidebar">
        <div class="admin-sidebar-brand">
            <h2>KITA KONBINI</h2>
            <p>Admin Panel</p>
        </div>
        <nav class="admin-nav">
            <div class="nav-section">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                📊 Dashboard
            </a>

            <div class="nav-section">Catalogue</div>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                📦 Products
            </a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                🏷️ Categories
            </a>

            <div class="nav-section">Operations</div>
            <a href="{{ route('admin.inventory') }}" class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
                🗄️ Inventory
            </a>
            <a href="{{ route('admin.revenue') }}" class="{{ request()->routeIs('admin.revenue') ? 'active' : '' }}">
                💰 Revenue
            </a>

            <div class="nav-section">Users</div>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                👥 Users
            </a>

            <div class="nav-section">System</div>
            <a href="{{ route('home') }}">🏪 View Store</a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="width:100%; text-align:left; padding:12px 20px; background:none; border:none; color:#ccc; font-family:Nunito,sans-serif; font-size:14px; font-weight:600; cursor:pointer;">
                    🚪 Logout
                </button>
            </form>
        </nav>
    </aside>

    {{-- Main --}}
    <main class="admin-main">
        <div class="admin-topbar">
            <h1>@yield('page-title', 'Dashboard')</h1>
            <div class="admin-topbar-actions">
                <span style="font-size:13px; color:#888;">👤 {{ auth()->user()->name }}</span>
            </div>
        </div>
        <div class="admin-content">
            @if(session('success'))
                <div class="alert-admin alert-admin-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-admin alert-admin-danger">❌ {{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

</div>
@stack('scripts')
</body>
</html>