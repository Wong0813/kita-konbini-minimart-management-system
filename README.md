# 🏪 Kita Konbini Minimart Management System

A Laravel-based minimart management system with a customer-facing storefront and an admin panel for managing products, inventory, and sales.

---

## 🛠 Tech Stack

- **Framework** — Laravel (PHP)
- **Frontend** — Blade Templates + Vanilla CSS/JS
- **Database** — MySQL (via Laragon)
- **Storage** — Laravel Storage (local disk)

---

## ✨ Features

**Customer Side**
- Register & Login (Matric ID + Email)
- Browse & search products by category
- Shopping cart & checkout
- Wishlist
- Shelf locator — find product by shelf code
- Profile management

**Admin Panel**
- Dashboard with stats, alerts & monthly sales chart
- Product management (CRUD + image upload)
- Inventory manager with batch tracking & expiry alerts
- Shelf management
- Category & user management
- Revenue report

---

## ⚙️ Setup

See **SETUP_GUIDE.txt** in this repo for full step-by-step setup instructions.

Quick start:
```bash
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

---

## 🔑 Default Login

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@kitakonbini.com | admin123 |
| Demo User | demo@student.edu.my | password |

Admin panel: `/admin`

---

## 👥 Contributors

- d20231109113-art
- Yukariii123
