<<<<<<< HEAD
# Elyzia - ONLINE SHOPPING Website (PHP + MySQL)

## FEATURES
- Customer side: Home page (products), search, category filter, product detail, cart, checkout, order tracking, login/signup
- Admin side: Dashboard, add/edit/delete products, order status update
- Database: MySQL (XAMPP)

---
##TECHNOLOGY USED
-PHP
-MYSQL
-HTML
-CSS
-JAVASCRIPT

-----
## SETUP STEPS (XAMPP + VS Code)

### 1. XAMPP install pannunga (already illa na)
https://www.apachefriends.org/download.html -ல irundhu download pannunga.

### 2. Project folder-ah correct place-la podunga
Indha `elyzia` folder muzhusa copy panni idhula podunga:

**Windows:**
```
C:\xampp\htdocs\elyzia\
```

**Mac:**
```
/Applications/XAMPP/xamppfiles/htdocs/elyzia/
```

So `C:\xampp\htdocs\elyzia\index.php`, `C:\xampp\htdocs\elyzia\config\db.php` mathiri irukanum.

### 3. VS Code-la open pannunga
VS Code open panni, File > Open Folder > `htdocs/elyzia` select pannunga. Idhu la irundhu edit pannalam.

### 4. XAMPP Control Panel start pannunga
XAMPP Control Panel open panni:
- **Apache** → Start button click pannunga
- **MySQL** → Start button click pannunga

Rendu um pachai (green) color-la "Running" nu kaatanum.

### 5. Database create pannunga
1. Browser-la open pannunga: `http://localhost/phpmyadmin`
2. Mela irukra **Import** tab click pannunga
3. **Choose File** click panni, project-la irukra `database.sql` file select pannunga
4. Kீழe **Go** button click pannunga

Idhu `elyzia_db` database-ah automatic-a create pannidum, tables + sample products + admin login um create aagidum.

### 6. Website open pannunga
Browser-la idha type pannunga:
```
http://localhost/elyzia/index.php
```

**Important:** `file:///...index.php` nu direct-a double click panni open pannadinga — adhu vela seiyathu. Always `http://localhost/...` nu than open pannanum, apparam than PHP code run aagum.

---

## LOGIN DETAILS

**Admin login:**
- URL: `http://localhost/elyzia/login.php`
- Email: `admin@elyzia.com`
- Password: `admin123`
- Login pannadhum automatic-a Admin Dashboard-ku pogum (`http://localhost/elyzia/admin/dashboard.php`)

**Customer:** Puthusa "Sign Up" panni account create pannikonga.

---

## Product images add panna
`assets/images/` folder-la image podunga (e.g., `headphones.png`). Admin panel-la irundhu new product add pannumbodhu neenga image upload panna mudiyum, adhu automatic-a idhe folder-la save aagum.

---

## Common Errors & Fixes

| Problem | Fix |
|---|---|
| "Connection failed" error | MySQL start pannala. XAMPP Control Panel-la MySQL start pannunga |
| Blank white page | `php.ini` la error display off irukalam, illa PHP syntax error irukalam. Terminal-la XAMPP Apache error log paarunga |
| 404 Not Found | Folder correct-a `htdocs/elyzia` la illa. Path check pannunga |
| Images kaanama irukku | `assets/images/` folder-la andha image file illa. Adhukku pathila product name text kaamikkum (that's normal fallback) |
| "Table doesn't exist" | `database.sql` import pannala. Step 5 mela paarunga |

---

## Folder Structure
```
elyzia/
├── config/db.php          -> database connection settings
├── includes/               -> header.php, footer.php (shared)
├── assets/css/style.css    -> all styling
├── assets/images/          -> product images
├── admin/                  -> admin panel (dashboard, add/edit product, orders)
├── index.php                -> homepage
├── product.php               -> product detail page
├── cart.php, checkout.php    -> shopping flow
├── login.php, register.php   -> auth
├── orders.php                 -> customer order history
└── database.sql             -> import this into phpMyAdmin first
```
##AUTHOR
BLESSY GNANAM B
=======
# ELYZIA
online shopping website
>>>>>>> 84d0567febc6bb6dd8580ce3741509276b8cdf79
