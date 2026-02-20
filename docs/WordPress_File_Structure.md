# WordPress File Structure Overview
[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

> **"รู้จักโครงสร้างไฟล์ = รู้ว่าต้องแก้ไขอะไรที่ไหน"**

---

## 1. ภาพรวมโครงสร้าง

```
wordpress/                      ← root ของ WordPress
│
├── index.php                   ← entry point หลัก
├── wp-config.php               ← ⚙️ config สำคัญที่สุด (DB, keys)
├── .htaccess                   ← Apache rules (permalink, redirect)
├── license.txt
├── readme.html
│
├── wp-admin/                   ← 🔒 Admin Dashboard (ห้ามแก้ไข)
│
├── wp-includes/                ← 🔒 WordPress Core (ห้ามแก้ไข)
│
└── wp-content/                 ← ✅ โซนที่นักพัฒนาทำงาน
    ├── themes/                 ← Themes
    ├── plugins/                ← Plugins
    ├── uploads/                ← รูปภาพ / ไฟล์ที่อัปโหลด
    └── languages/              ← ไฟล์ภาษา
```

---

## 2. ไฟล์ Root Level

### `index.php`
```php
<?php
// ไฟล์นี้สั้นมาก — แค่โหลด wp-blog-header.php
require __DIR__ . '/wp-blog-header.php';
```
- จุดเริ่มต้นของทุก request
- ไม่ต้องแก้ไข

### `wp-config.php` ⭐ สำคัญมาก
```php
// Database Connection
define('DB_NAME',     'wordpress');
define('DB_USER',     'wordpress');
define('DB_PASSWORD', 'wordpress');
define('DB_HOST',     'db');        // docker service name

// Table Prefix (เปลี่ยนเพื่อความปลอดภัย)
$table_prefix = 'wp_';

// Debug Mode
define('WP_DEBUG', false);          // true = เปิด error log

// Security Keys (สร้างที่ api.wordpress.org/secret-key/1.1/salt/)
define('AUTH_KEY',    '...');
define('SECURE_AUTH_KEY', '...');
```

### `.htaccess`
```apache
# WordPress Permalink Rules
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
```
- ถ้าหาย → Permalink จะพัง → แก้ไขที่ **Settings → Permalinks → Save**

---

## 3. wp-admin/ — Admin Dashboard

```
wp-admin/
├── index.php           ← หน้า Dashboard หลัก
├── admin.php           ← entry point ของทุกหน้าใน admin
├── admin-ajax.php      ← รับ AJAX requests
├── install.php         ← ติดตั้ง WordPress ครั้งแรก
├── css/                ← styles ของ admin
├── js/                 ← scripts ของ admin
└── includes/           ← functions ของ admin
```

> ❌ **ห้ามแก้ไขไฟล์ใน `wp-admin/`** — จะถูก overwrite ทุกครั้งที่ update WordPress

---

## 4. wp-includes/ — WordPress Core

```
wp-includes/
├── functions.php       ← functions หลักทั้งหมด (get_posts, wp_mail ฯลฯ)
├── class-wp.php        ← WordPress class หลัก
├── class-wp-query.php  ← การดึงข้อมูลจาก DB
├── class-wp-post.php   ← Post object
├── post.php            ← post functions
├── user.php            ← user functions
├── pluggable.php       ← functions ที่ plugin override ได้
├── rest-api/           ← WordPress REST API
├── blocks/             ← Gutenberg blocks
└── js/, css/           ← scripts และ styles ของ core
```

> ❌ **ห้ามแก้ไขไฟล์ใน `wp-includes/`** — จะถูก overwrite ทุกครั้งที่ update WordPress

---

## 5. wp-content/ — โซนนักพัฒนา ✅

```
wp-content/
│
├── themes/
│   ├── twentytwentyfour/   ← Default theme
│   ├── twentytwentythree/
│   └── my-custom-theme/    ← Theme ที่สร้างเอง
│
├── plugins/
│   ├── akismet/            ← Default plugin
│   ├── woocommerce/
│   └── workppass-contact/  ← Plugin ที่สร้างในคอร์สนี้
│
├── uploads/
│   └── 2024/
│       └── 01/
│           ├── image.jpg
│           └── document.pdf
│
└── languages/
    ├── th_TH.mo
    └── th_TH.po
```

---

## 6. wp-content/themes/ — โครงสร้าง Theme

```
my-theme/
├── style.css           ← ⭐ Theme header + styles (ต้องมี)
├── index.php           ← Template fallback หลัก (ต้องมี)
├── functions.php       ← โหลด scripts, register menus, hooks
├── header.php          ← ส่วนหัวของทุกหน้า
├── footer.php          ← ส่วนท้ายของทุกหน้า
├── sidebar.php         ← Sidebar
├── single.php          ← หน้า Post เดียว
├── page.php            ← หน้า Page
├── archive.php         ← หน้า Category / Tag
├── search.php          ← หน้าผลการค้นหา
├── 404.php             ← หน้า Not Found
└── screenshot.png      ← รูปตัวอย่างใน Appearance → Themes
```

`style.css` ต้องมี header นี้:
```css
/*
Theme Name: My Custom Theme
Theme URI:  https://example.com
Author:     Your Name
Version:    1.0.0
*/
```

---

## 7. wp-content/plugins/ — โครงสร้าง Plugin

```
my-plugin/
├── my-plugin.php       ← ⭐ Main file (ต้องมี Plugin header)
├── includes/           ← PHP classes, functions
├── admin/              ← Admin-specific code
├── public/             ← Frontend code
└── assets/
    ├── css/
    └── js/
```

`my-plugin.php` ต้องมี header:
```php
<?php
/**
 * Plugin Name: My Plugin
 * Description: คำอธิบาย plugin
 * Version:     1.0.0
 * Author:      Your Name
 */
```

---

## 8. สรุป — แก้ไขอะไร ไปที่ไหน

| ต้องการ | ไปที่ |
|--------|-------|
| เปลี่ยน DB / URL / Debug | `wp-config.php` |
| แก้ปัญหา Permalink | `.htaccess` |
| แก้หน้าตาเว็บ | `wp-content/themes/` |
| เพิ่มฟีเจอร์ | `wp-content/plugins/` |
| ดูรูปที่อัปโหลด | `wp-content/uploads/` |
| อัปเดต WP Core | ผ่าน Admin Dashboard (อย่าแก้ไฟล์ตรงๆ) |

```
✅ แก้ได้:   wp-config.php, .htaccess, wp-content/
❌ ห้ามแก้:  wp-admin/, wp-includes/
```
