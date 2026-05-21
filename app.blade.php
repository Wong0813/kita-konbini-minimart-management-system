<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kita Konbini Minimart')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar">
        <a href="{{ route('home') }}" class="navbar-brand">
            <span>Kita Konbini</span>
            MINIMART
        </a>

        <div class="navbar-search">
            <form action="{{ route('products.search') }}" method="GET">
                <input type="text" name="q" placeholder="Search products..."
                    value="{{ request('q') }}" autocomplete="off">
                <button type="submit" class="search-icon" style="background:none;border:none;cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/>
                    </svg>
                </button>
            </form>
        </div>

        <div class="navbar-actions">
            <a href="{{ route('wishlist') }}" title="Wishlist">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </a>

            <a href="{{ route('cart.index') }}" title="Cart">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="cart-badge">{{ count(session('cart')) }}</span>
                @endif
            </a>

            <a href="{{ route('profile') }}" title="Profile">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="site-footer">
        KitaKonbiniMinimart.com
    </footer>

    @stack('scripts')
    <script>
        // Add to cart AJAX
        document.querySelectorAll('.add-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.id;
                fetch('/cart/add/' + productId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Update cart badge
                        const badge = document.querySelector('.cart-badge');
                        if (badge) badge.textContent = data.count;
                        // Quick feedback
                        this.textContent = '✓';
                        setTimeout(() => this.textContent = '+', 1000);
                    }
                });
            });
        });
    </script>
</body>
</html>
