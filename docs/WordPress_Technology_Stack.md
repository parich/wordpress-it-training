# WordPress Technology Stack (PHP + MySQL)

> **"WordPress คือ PHP code ที่ดึงข้อมูลจาก MySQL มาสร้างเป็น HTML ส่งให้ Browser"**

---

## 1. ภาพรวม Stack

```
┌─────────────────────────────────────┐
│           Browser / Client          │  HTML, CSS, JavaScript
└─────────────────┬───────────────────┘
                  │ HTTP Request
┌─────────────────▼───────────────────┐
│         Web Server (Apache/Nginx)   │  รับ Request, ส่งต่อ PHP
└─────────────────┬───────────────────┘
                  │
┌─────────────────▼───────────────────┐
│           PHP + WordPress           │  ตัวประมวลผลหลัก
│  ┌─────────────────────────────┐   │
│  │  WordPress Core (PHP Code)  │   │
│  │  - Functions                │   │
│  │  - Hooks (Actions/Filters)  │   │
│  │  - Template Hierarchy       │   │
│  └─────────────────────────────┘   │
└─────────────────┬───────────────────┘
                  │ SQL Query
┌─────────────────▼───────────────────┐
│        MySQL / MariaDB              │  เก็บข้อมูลทั้งหมด
└─────────────────────────────────────┘
```

---

## 2. PHP — ตัวขับเคลื่อน WordPress

### PHP คืออะไร
- **Server-side scripting language** — รันบน server ไม่ใช่ browser
- WordPress core เขียนด้วย PHP ทั้งหมด (~1 ล้านบรรทัด)
- PHP แปลง template + ข้อมูลจาก DB → HTML

### PHP ทำงานอย่างไรใน WordPress

```php
// ตัวอย่างง่ายๆ ที่ WordPress ทำอยู่เบื้องหลัง

// 1. รับ URL request
$slug = 'about';

// 2. ดึงข้อมูลจาก MySQL
$post = $wpdb->get_row(
    "SELECT * FROM wp_posts WHERE post_name = '$slug'"
);

// 3. สร้าง HTML ส่งกลับ Browser
echo '<h1>' . $post->post_title . '</h1>';
echo '<div>' . $post->post_content . '</div>';
```

### PHP Version กับ WordPress

| PHP Version | WordPress รองรับ? | แนะนำ? |
|------------|-----------------|-------|
| 7.4 | ✅ | ❌ หมด support แล้ว |
| 8.0 | ✅ | ❌ |
| 8.1 | ✅ | ✅ |
| 8.2 | ✅ | ✅ (แนะนำ) |
| 8.3 | ✅ | ✅ (ล่าสุด) |

> เช็ค PHP version: **Admin → Tools → Site Health → Info → Server**

---

## 3. MySQL — ฐานข้อมูลของ WordPress

### ตารางหลักใน WordPress Database

| ตาราง | เก็บอะไร |
|-------|---------|
| `wp_posts` | Posts, Pages, Custom Post Types ทั้งหมด |
| `wp_postmeta` | ข้อมูลเพิ่มเติมของ post (custom fields) |
| `wp_users` | ข้อมูล user ทุกคน |
| `wp_usermeta` | ข้อมูลเพิ่มเติมของ user (role, preferences) |
| `wp_options` | Settings ทุกอย่าง (site URL, theme, plugins) |
| `wp_terms` | Categories, Tags |
| `wp_term_relationships` | ความสัมพันธ์ post ↔ category/tag |
| `wp_comments` | Comments ทั้งหมด |

### ดู Database ผ่าน phpMyAdmin

```
http://localhost:8080  (Docker setup ในคอร์สนี้)
```

ลอง Query ดูข้อมูลจริง:
```sql
-- ดู posts ทั้งหมด
SELECT ID, post_title, post_status, post_type
FROM wp_posts
WHERE post_status = 'publish';

-- ดู settings ของเว็บ
SELECT option_name, option_value
FROM wp_options
WHERE option_name IN ('siteurl', 'blogname', 'admin_email');
```

---

## 4. WordPress Core ทำงานอย่างไร

### ลำดับการโหลด (Loading Order)

```
1. index.php
      │
2. wp-blog-header.php
      │
3. wp-load.php
      │
4. wp-config.php       ← DB credentials, settings
      │
5. wp-settings.php     ← โหลด core functions, plugins, theme
      │
6. Plugins โหลด
      │
7. Theme โหลด
      │
8. WordPress Query     ← ดึงข้อมูลจาก MySQL ตาม URL
      │
9. Template Hierarchy  ← เลือก template ที่จะใช้แสดงผล
      │
10. HTML Output        ← ส่งกลับ Browser
```

### wp-config.php — ไฟล์ที่สำคัญที่สุด

```php
// เชื่อมต่อ Database
define('DB_NAME',     'wordpress');
define('DB_USER',     'root');
define('DB_PASSWORD', 'secret');
define('DB_HOST',     'db');        // localhost หรือ docker service name

// Security Keys (ใช้ encrypt cookies)
define('AUTH_KEY',    'ใส่ random string ยาวๆ');

// Debug mode (เปิดตอน dev, ปิดตอน production)
define('WP_DEBUG', true);
```

---

## 5. Hooks System — หัวใจของ WordPress

WordPress ใช้ระบบ **Hooks** ให้ plugin/theme แทรก code ได้โดยไม่แก้ core

```php
// Action Hook — เพิ่ม code ที่จุดนั้น
add_action('wp_head', function() {
    echo '<meta name="author" content="Workppass">';
});

// Filter Hook — แก้ไขค่าก่อนแสดงผล
add_filter('the_title', function($title) {
    return '🔥 ' . $title;
});
```

```
WordPress Core รัน
      │
      ├── do_action('wp_head')      ← Plugin/Theme แทรก code ได้
      ├── apply_filters('the_title') ← แก้ไขค่าได้
      └── do_action('wp_footer')    ← Plugin/Theme แทรก code ได้
```

---

## 6. Template Hierarchy

WordPress เลือก template ตาม URL อัตโนมัติ

```
URL: /about/          → page.php หรือ single.php
URL: /               → front-page.php หรือ home.php
URL: /category/news/ → category.php หรือ archive.php
URL: /search?s=foo   → search.php
ไม่พบ template       → index.php (fallback)
```

---

## 7. สรุป

| เทคโนโลยี | บทบาทใน WordPress |
|---------|-----------------|
| **PHP** | รัน WordPress core, theme, plugin code |
| **MySQL** | เก็บ content, settings, users ทั้งหมด |
| **HTML** | ผลลัพธ์ที่ PHP สร้างส่งให้ Browser |
| **CSS** | จัด style หน้าเว็บ (จาก theme) |
| **JavaScript** | Interactivity (เมนู, slider, AJAX) |

```
PHP อ่าน Template + ดึง MySQL → สร้าง HTML → Browser แสดงผล
```

- แก้ไข **เนื้อหา** → ผ่าน MySQL (Admin Dashboard)
- แก้ไข **หน้าตา** → ผ่าน PHP Template (Theme)
- เพิ่ม **ฟีเจอร์** → ผ่าน PHP Hooks (Plugin)
