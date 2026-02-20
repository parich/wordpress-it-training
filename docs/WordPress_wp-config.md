# wp-config.php
[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

> **"wp-config.php คือไฟล์ตั้งค่าหลักของ WordPress — เชื่อมต่อ DB, กำหนด Security Keys, และควบคุม behavior ของเว็บ"**

ไฟล์นี้อยู่ที่ root ของ WordPress และโหลดขึ้นมาทุกครั้งที่มี request

---

## 1. โครงสร้างไฟล์

```
wp-config.php
│
├── Database Settings       ← เชื่อมต่อ MySQL
├── Authentication Keys     ← Security / Cookie encryption
├── Table Prefix            ← ชื่อ prefix ของ tables
├── Debug Mode              ← เปิด/ปิด error display
├── Custom Constants        ← ตั้งค่าเพิ่มเติม
└── require wp-settings.php ← โหลด WordPress Core
```

---

## 2. Database Settings

```php
define( 'DB_NAME',     'wordpress' );   // ชื่อ database
define( 'DB_USER',     'wordpress' );   // database username
define( 'DB_PASSWORD', 'wordpress' );   // database password
define( 'DB_HOST',     'db' );          // hostname (docker: ชื่อ service)
define( 'DB_CHARSET',  'utf8' );        // character encoding
define( 'DB_COLLATE',  '' );            // database collation
```

### Docker version (โปรเจกต์นี้)

ไฟล์จริงในโปรเจกต์ใช้ `getenv_docker()` แทน hardcode เพื่อรับค่าจาก environment variable:

```php
define( 'DB_NAME',     getenv_docker('WORDPRESS_DB_NAME',     'wordpress') );
define( 'DB_USER',     getenv_docker('WORDPRESS_DB_USER',     'wordpress') );
define( 'DB_PASSWORD', getenv_docker('WORDPRESS_DB_PASSWORD', 'wordpress') );
define( 'DB_HOST',     getenv_docker('WORDPRESS_DB_HOST',     'mysql') );
```

ค่าจะถูกส่งมาจาก `docker-compose.yml`:
```yaml
environment:
  - WORDPRESS_DB_HOST=db:3306
  - WORDPRESS_DB_NAME=${DB_NAME}
  - WORDPRESS_DB_USER=${DB_USER}
  - WORDPRESS_DB_PASSWORD=${DB_PASSWORD}
```

---

## 3. Authentication Keys & Salts

```php
define( 'AUTH_KEY',         'random string ยาวๆ ไม่ซ้ำกัน' );
define( 'SECURE_AUTH_KEY',  'random string ยาวๆ ไม่ซ้ำกัน' );
define( 'LOGGED_IN_KEY',    'random string ยาวๆ ไม่ซ้ำกัน' );
define( 'NONCE_KEY',        'random string ยาวๆ ไม่ซ้ำกัน' );
define( 'AUTH_SALT',        'random string ยาวๆ ไม่ซ้ำกัน' );
define( 'SECURE_AUTH_SALT', 'random string ยาวๆ ไม่ซ้ำกัน' );
define( 'LOGGED_IN_SALT',   'random string ยาวๆ ไม่ซ้ำกัน' );
define( 'NONCE_SALT',       'random string ยาวๆ ไม่ซ้ำกัน' );
```

**ทำไมต้องมี?**
- ใช้ encrypt **session cookies** และ **nonces**
- ถ้าเปลี่ยน keys → ผู้ใช้ทุกคนถูก logout อัตโนมัติ
- ควรสร้างใหม่ทุก site

**สร้างได้ที่:** `https://api.wordpress.org/secret-key/1.1/salt/`

---

## 4. Table Prefix

```php
$table_prefix = 'wp_';
```

- กำหนด prefix ของทุก table ใน database
- Default คือ `wp_` → ได้ table `wp_posts`, `wp_users` ฯลฯ
- เปลี่ยนเป็นอื่น เช่น `mysite_` เพื่อความปลอดภัย (กัน SQL injection แบบ generic)
- ถ้าจะรัน **หลาย WordPress** ใน database เดียว → ใช้ prefix ต่างกัน

```php
// ตัวอย่างรัน 2 sites ใน DB เดียว
$table_prefix = 'blog1_';   // site 1
$table_prefix = 'blog2_';   // site 2
```

---

## 5. Debug Mode

```php
define( 'WP_DEBUG', false );   // false = ปิด (production)
                               // true  = เปิด (development)
```

### Debug Constants เพิ่มเติม

```php
// เปิด debug mode
define( 'WP_DEBUG', true );

// บันทึก error ลงไฟล์แทนแสดงบนหน้าจอ
define( 'WP_DEBUG_LOG', true );    // บันทึกลง wp-content/debug.log
define( 'WP_DEBUG_DISPLAY', false ); // ไม่แสดงบนหน้าเว็บ

// แสดง query ที่ใช้
define( 'SAVEQUERIES', true );
```

**ข้อควรระวัง:** ❌ อย่าเปิด `WP_DEBUG` บน production — error message อาจเปิดเผยข้อมูล server

---

## 6. Constants ที่มีประโยชน์

### Memory Limit

```php
define( 'WP_MEMORY_LIMIT', '256M' );        // memory สำหรับ frontend
define( 'WP_MAX_MEMORY_LIMIT', '512M' );    // memory สำหรับ admin
```

### ปิด Automatic Updates

```php
define( 'AUTOMATIC_UPDATER_DISABLED', true );    // ปิดทุก auto-update
define( 'WP_AUTO_UPDATE_CORE', false );          // ปิด core auto-update
```

### ปิด File Editor ใน Admin

```php
// ป้องกันแก้ไข theme/plugin ผ่าน Admin Dashboard
define( 'DISALLOW_FILE_EDIT', true );
```

### Force SSL ใน Admin

```php
define( 'FORCE_SSL_ADMIN', true );
```

### กำหนด URL

```php
define( 'WP_HOME',    'https://example.com' );
define( 'WP_SITEURL', 'https://example.com' );
```

---

## 7. ABSPATH

```php
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
```

- `ABSPATH` = absolute path ไปยัง WordPress root บน server
- บรรทัดสุดท้ายโหลด WordPress Core ทั้งหมด
- **ห้ามแก้ไขหรือลบส่วนนี้**

---

## 8. ลำดับการทำงาน

```
HTTP Request มาถึง
        │
        ▼
index.php
        │
        ▼
wp-blog-header.php
        │
        ▼
wp-load.php
        │  ← ค้นหา wp-config.php
        ▼
wp-config.php       ← โหลด DB settings, keys, constants
        │
        ▼
wp-settings.php     ← โหลด core, plugins, theme
        │
        ▼
WordPress พร้อมใช้งาน
```

---

## 9. สรุปสิ่งที่ควรรู้

| หัวข้อ | ค่า default (โปรเจกต์นี้) | หมายเหตุ |
|--------|--------------------------|---------|
| `DB_NAME` | `wordpress` | จาก `.env` |
| `DB_USER` | `wordpress` | จาก `.env` |
| `DB_PASSWORD` | `wordpress` | จาก `.env` |
| `DB_HOST` | `db` | docker service name |
| `$table_prefix` | `wp_` | เปลี่ยนได้เพื่อความปลอดภัย |
| `WP_DEBUG` | `false` | เปิดตอน dev เท่านั้น |

```
✅ แก้ไขได้:  DB settings, debug mode, custom constants
❌ ห้ามลบ:   Security keys, ABSPATH, require wp-settings.php
```
