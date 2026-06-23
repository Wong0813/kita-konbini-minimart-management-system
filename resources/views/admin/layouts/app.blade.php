<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Kita Konbini')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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

                {{-- Notification Bell --}}
                <div style="position:relative;">
                    <button onclick="toggleNotif()" id="notif-btn"
                        style="width:40px; height:40px; background:none; border:none; cursor:pointer;
                        display:flex; align-items:center; justify-content:center; border-radius:50%;
                        transition:background 0.2s; color:#333;"
                        onmouseover="this.style.background='#f0f0f0'"
                        onmouseout="this.style.background='none'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notif-badge"
                            style="display:none; position:absolute; top:4px; right:4px;
                            background:#C0392B; color:white; font-size:9px; font-weight:800;
                            width:16px; height:16px; border-radius:50%;
                            align-items:center; justify-content:center;">0</span>
                    </button>

                    {{-- Dropdown --}}
                    <div id="notif-dropdown" style="display:none; position:absolute; right:0; top:48px;
                        width:340px; background:white; border-radius:16px;
                        box-shadow:0 8px 32px rgba(0,0,0,0.12); border:1px solid #eee;
                        z-index:999; overflow:hidden;">
                        <div style="padding:14px 16px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-weight:800; font-size:14px;">🔔 Notifications</span>
                            <button onclick="markAllRead()" style="background:none; border:none; cursor:pointer; font-size:11px; color:#C0392B; font-weight:700;">Mark all read</button>
                        </div>
                        <div id="notif-list" style="max-height:360px; overflow-y:auto;">
                            <div style="text-align:center; padding:24px; color:#9B9B9B; font-size:13px;">Loading...</div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('home') }}"
                   target="_blank"
                   style="display:inline-flex; align-items:center; gap:6px;
                          padding:8px 16px; background:#fff; border:1.5px solid #ddd;
                          border-radius:8px; font-size:13px; font-weight:700;
                          color:#333; text-decoration:none; transition:all 0.2s;"
                   onmouseover="this.style.background='#f5f5f5'; this.style.borderColor='#bbb';"
                   onmouseout="this.style.background='#fff'; this.style.borderColor='#ddd';">
                    🏪 View Store
                </a>
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

<script>
let notifOpen = false;

function toggleNotif() {
    notifOpen = !notifOpen;
    const dropdown = document.getElementById('notif-dropdown');
    dropdown.style.display = notifOpen ? 'block' : 'none';
    if (notifOpen) loadNotifications();
}

function loadNotifications() {
    fetch('/notifications')
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('notif-list');
            if (!data.length) {
                list.innerHTML = '<div style="text-align:center; padding:24px; color:#9B9B9B; font-size:13px;">No notifications yet 🎉</div>';
                return;
            }
            list.innerHTML = data.map(n => `
                <div onclick="markRead(${n.id}, this)"
                    style="padding:12px 16px; border-bottom:1px solid #F7F7F7; cursor:pointer;
                    background:${n.is_read ? 'white' : '#FDF2F0'};
                    transition:background 0.2s; display:flex; gap:12px; align-items:flex-start;"
                    onmouseover="this.style.background='#F7F7F7'"
                    onmouseout="this.style.background='${n.is_read ? 'white' : '#FDF2F0'}'">
                    <span style="font-size:20px; flex-shrink:0;">${n.icon ?? '🔔'}</span>
                    <div>
                        <div style="font-size:13px; font-weight:700; margin-bottom:2px;">${n.title}</div>
                        <div style="font-size:12px; color:#9B9B9B; line-height:1.4;">${n.message}</div>
                        <div style="font-size:10px; color:#C0C0C0; margin-top:4px;">${new Date(n.created_at).toLocaleString('en-MY')}</div>
                    </div>
                    ${!n.is_read ? '<div style="width:8px; height:8px; background:#C0392B; border-radius:50%; flex-shrink:0; margin-top:4px;"></div>' : ''}
                </div>
            `).join('');
        });
}

function markRead(id, el) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    });
    el.style.background = 'white';
    updateNotifCount();
}

function markAllRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    }).then(() => {
        loadNotifications();
        updateNotifCount();
    });
}

function updateNotifCount() {
    fetch('/notifications/count')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
}

document.addEventListener('click', (e) => {
    if (!document.getElementById('notif-btn')?.contains(e.target) &&
        !document.getElementById('notif-dropdown')?.contains(e.target)) {
        notifOpen = false;
        const dropdown = document.getElementById('notif-dropdown');
        if (dropdown) dropdown.style.display = 'none';
    }
});

updateNotifCount();
setInterval(updateNotifCount, 30000);
</script>

</body>
</html>