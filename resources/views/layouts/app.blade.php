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

    {{-- Admin Bar (only visible to admins) --}}
    @auth
        @if(auth()->user()->is_admin)
        <div style="background:#1a1a1a; color:#fff; padding:8px 24px;
                    display:flex; align-items:center; justify-content:space-between;
                    font-size:13px; font-family:'Nunito',sans-serif;
                    position:sticky; top:0; z-index:200;">
            <span style="display:flex; align-items:center; gap:8px;">
                <span style="background:#C0392B; color:#fff; font-size:11px; font-weight:800;
                              padding:2px 8px; border-radius:4px; letter-spacing:0.5px;">ADMIN</span>
                You are viewing the store as an admin.
            </span>
            <a href="{{ route('admin.dashboard') }}"
               style="display:inline-flex; align-items:center; gap:6px;
                      background:#C0392B; color:#fff; padding:6px 14px;
                      border-radius:6px; font-size:12px; font-weight:800;
                      text-decoration:none; transition:background 0.2s;"
               onmouseover="this.style.background='#a93226';"
               onmouseout="this.style.background='#C0392B';">
                ⚙️ Back to Admin Panel
            </a>
        </div>
        @endif
    @endauth

    {{-- Navbar --}}
    <nav class="navbar" @auth @if(auth()->user()->is_admin) style="top:37px;" @endif @endauth>
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

    <style>
    @keyframes ripple {
        to { transform: translate(-50%,-50%) scale(3); opacity: 0; }
    }

    .combo-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 998;
        backdrop-filter: blur(4px);
    }

    .combo-box {
        position: fixed;
        bottom: -100%; left: 50%;
        transform: translateX(-50%);
        width: 90%; max-width: 380px;
        background: white;
        border-radius: 24px 24px 0 0;
        padding: 28px 24px 32px;
        z-index: 999;
        transition: bottom 0.4s cubic-bezier(0.34,1.56,0.64,1);
        box-shadow: 0 -8px 40px rgba(0,0,0,0.15);
    }

    .combo-show .combo-box { bottom: 0; }
    .combo-show .combo-overlay { opacity: 1; }
    #combo-popup:not(.combo-show) .combo-overlay { opacity: 0; transition: opacity 0.3s; }

    .combo-close {
        position: absolute; top: 16px; right: 16px;
        background: #F0F0F0; border: none;
        width: 32px; height: 32px; border-radius: 50%;
        cursor: pointer; font-size: 14px;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s;
    }

    .combo-close:hover { background: #E0E0E0; }

    .combo-tag {
        display: inline-block;
        background: var(--red-bg); color: var(--red-primary);
        font-size: 12px; font-weight: 800;
        padding: 4px 12px; border-radius: 50px; margin-bottom: 12px;
    }

    .combo-title { font-size: 20px; font-weight: 800; margin-bottom: 16px; color: var(--black); }

    .combo-product {
        display: flex; align-items: center; gap: 16px;
        background: var(--gray-light); border-radius: var(--radius-sm);
        padding: 14px; margin-bottom: 20px;
    }

    .combo-emoji {
        font-size: 40px; width: 60px; height: 60px;
        background: white; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .combo-name { font-size: 15px; font-weight: 800; color: var(--black); margin-bottom: 3px; }
    .combo-cat  { font-size: 11px; color: var(--gray-text); text-transform: uppercase; font-weight: 600; margin-bottom: 4px; }
    .combo-price { font-size: 16px; font-weight: 800; color: var(--red-primary); }
    .combo-actions { display: flex; gap: 10px; }

    .combo-btn-yes {
        flex: 1; padding: 14px;
        background: var(--red-primary); color: white;
        border: none; border-radius: var(--radius-sm);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px; font-weight: 800; cursor: pointer;
        transition: background 0.2s, transform 0.1s;
    }

    .combo-btn-yes:hover { background: var(--red-dark); transform: translateY(-1px); }

    .combo-btn-no {
        padding: 14px 20px;
        background: var(--gray-border); color: var(--black);
        border: none; border-radius: var(--radius-sm);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px; font-weight: 700; cursor: pointer;
        transition: background 0.2s;
    }

    .combo-btn-no:hover { background: #D5D5D5; }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.4s ease';
        setTimeout(() => document.body.style.opacity = '1', 50);

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.product-card, .category-card').forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = `opacity 0.4s ease ${i * 0.06}s, transform 0.4s ease ${i * 0.06}s`;
            observer.observe(el);
        });

        document.querySelectorAll('.add-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.id;

                const ripple = document.createElement('span');
                ripple.style.cssText = `
                    position:absolute; border-radius:50%;
                    background:rgba(255,255,255,0.5);
                    width:40px; height:40px;
                    top:50%; left:50%;
                    transform:translate(-50%,-50%) scale(0);
                    animation:ripple 0.4s ease-out forwards;
                    pointer-events:none;
                `;
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 400);

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
                        const badge = document.querySelector('.cart-badge');
                        if (badge) {
                            badge.textContent = data.count;
                            badge.style.transform = 'scale(1.4)';
                            setTimeout(() => badge.style.transform = 'scale(1)', 200);
                        }
                        this.textContent = '✓';
                        this.style.background = '#27AE60';
                        setTimeout(() => {
                            this.textContent = '+';
                            this.style.background = '';
                        }, 1200);
                        showComboPopup(productId);
                    }
                });
            });
        });
    });

    function toggleWishlist(id, btn) {
        fetch('/wishlist/toggle/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.textContent = data.status === 'added' ? '♥' : '♡';
                btn.style.color = data.status === 'added' ? 'var(--red-primary)' : 'var(--gray-text)';
            }
        });
    }

    function showComboPopup(productId) {
        const existing = document.getElementById('combo-popup');
        if (existing) existing.remove();

        fetch('/products/combo/' + productId)
            .then(r => r.json())
            .then(data => {
                if (!data.combo) return;

                const popup = document.createElement('div');
                popup.id = 'combo-popup';
                popup.innerHTML = `
                    <div class="combo-overlay" onclick="closeCombo()"></div>
                    <div class="combo-box">
                        <button class="combo-close" onclick="closeCombo()">✕</button>
                        <div class="combo-tag">🎁 Grab a Combo!</div>
                        <h3 class="combo-title">Pair it with</h3>
                        <div class="combo-product">
                            <div class="combo-emoji">🛒</div>
                            <div class="combo-info">
                                <div class="combo-name">${data.combo.name}</div>
                                <div class="combo-cat">${data.combo.category}</div>
                                <div class="combo-price">${data.combo.price}</div>
                            </div>
                        </div>
                        <div class="combo-actions">
                            <button class="combo-btn-yes" onclick="addCombo(${data.combo.id})">+ Add to Cart</button>
                            <button class="combo-btn-no" onclick="closeCombo()">No thanks</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(popup);
                setTimeout(() => popup.classList.add('combo-show'), 10);
            })
            .catch(() => {});
    }

    function addCombo(id) {
        fetch('/cart/add/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const badge = document.querySelector('.cart-badge');
                if (badge) badge.textContent = data.count;
                closeCombo();
                showToast('Combo added to cart! 🎉');
            }
        });
    }

    function closeCombo() {
        const popup = document.getElementById('combo-popup');
        if (popup) {
            popup.classList.remove('combo-show');
            setTimeout(() => popup.remove(), 300);
        }
    }

    function showToast(message) {
        const existing = document.getElementById('toast-notif');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'toast-notif';
        toast.textContent = message;
        toast.style.cssText = `
            position:fixed; bottom:24px; left:50%;
            transform:translateX(-50%) translateY(80px);
            background:#1C1C1C; color:white;
            padding:12px 24px; border-radius:50px;
            font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; font-weight:700;
            z-index:9999; box-shadow:0 8px 24px rgba(0,0,0,0.2);
            transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.style.transform = 'translateX(-50%) translateY(0)', 10);
        setTimeout(() => {
            toast.style.transform = 'translateX(-50%) translateY(80px)';
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }
    </script>

</body>
</html>