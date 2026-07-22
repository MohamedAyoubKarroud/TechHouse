# TechHouse — Music & Audio E-Commerce Platform

MVC-structured PHP/MySQL e-commerce app for music and audio equipment (instruments, DJ gear, studio gear, vinyl). Runs on XAMPP.

## Stack
- **Frontend:** HTML5, CSS3, vanilla JavaScript (responsive)
- **Backend:** PHP 8+ (MVC), PDO with prepared statements
- **Database:** MySQL / MariaDB
- **Server:** Apache (XAMPP)

## Setup

1. **Clone into XAMPP htdocs**
   ```
   C:\xampp\htdocs\techhouse
   ```
2. **Create the database**
   - Open phpMyAdmin → import `database/techhouse.sql`
   - This creates the schema, seeds 4 rubrics (Instruments, DJ Equipment, Studio Gear, Vinyl Records), sample products, and an admin account.
3. **Configure**
   - Edit `config/database.php` if your MySQL credentials differ from defaults (`root` / no password).
4. **Apache rewrite**
   - Ensure `mod_rewrite` is enabled (XAMPP: enabled by default).
   - `.htaccess` at project root routes everything through `index.php`.
5. **Open in browser**
   ```
   http://localhost/techhouse/
   ```

## Default Accounts
- **Admin:** `admin@techhouse.local` / `admin123`
- **Client:** create one via Register, or seed user `client@techhouse.local` / `client123`

## Feature Map
- **Routing:** `app/core/App.php` — `/{controller}/{action}/{param}`
- **Auth & sessions:** `app/core/Auth.php`, `AuthController`
- **CSRF + input sanitization:** `app/core/Security.php`
- **Cart:** session-based, `CartController` + `Cart` model
- **Orders & tracking:** `OrderController`, statuses (pending/paid/shipped/delivered/cancelled)
- **Filtering & search:** `ProductController::index` (price, brand, color, new), `SearchController`
- **Admin dashboard:** product CRUD, user management, order supervision, analytics, top products, geo distribution
- **AI categorization stub:** `app/services/AiCategorizer.php` — pluggable for OpenAI Vision / Anthropic API
- **Geolocation:** `app/core/Geolocation.php` — ip-api.com (free, no key); logged on each visit
- **Visit analytics:** `analytics_visits` table, recorded per page load

```
## Structure du projet
TechHouse
├─ .htaccess
├─ app
│  ├─ controllers
│  │  ├─ AdminController.php
│  │  ├─ AuthController.php
│  │  ├─ CartController.php
│  │  ├─ HomeController.php
│  │  ├─ OrderController.php
│  │  ├─ ProductController.php
│  │  └─ SearchController.php
│  ├─ core
│  │  ├─ App.php
│  │  ├─ Auth.php
│  │  ├─ Controller.php
│  │  ├─ Geolocation.php
│  │  ├─ Model.php
│  │  └─ Security.php
│  ├─ models
│  │  ├─ Analytics.php
│  │  ├─ Cart.php
│  │  ├─ Category.php
│  │  ├─ Order.php
│  │  ├─ Product.php
│  │  ├─ Promo.php
│  │  └─ User.php
│  ├─ services
│  │  ├─ AiCategorizer.php
│  │  └─ OAuth.php
│  └─ views
│     ├─ admin
│     │  ├─ dashboard.php
│     │  ├─ orders.php
│     │  ├─ products.php
│     │  ├─ product_form.php
│     │  └─ users.php
│     ├─ auth
│     │  ├─ login.php
│     │  ├─ register.php
│     │  └─ _social.php
│     ├─ cart
│     │  └─ index.php
│     ├─ home
│     │  └─ index.php
│     ├─ layouts
│     │  ├─ footer.php
│     │  └─ header.php
│     ├─ orders
│     │  ├─ checkout.php
│     │  ├─ confirmation.php
│     │  ├─ history.php
│     │  └─ track.php
│     ├─ products
│     │  ├─ index.php
│     │  └─ show.php
│     └─ search
│        └─ results.php
├─ config
│  ├─ config.php
│  └─ database.php
├─ database
│  ├─ fr_categories.sql
│  ├─ migration_oauth.sql
│  └─ techhouse.sql
├─ index.php
├─ public
│  ├─ css
│  │  └─ style.css
│  ├─ js
│  │  └─ app.js
│  └─ uploads
│     └─ 6a86eae0722c5b20.jpg
├─ README.md
└─ tools
   └─ seed_passwords.php

```