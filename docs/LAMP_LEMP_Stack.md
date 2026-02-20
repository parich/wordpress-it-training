# LAMP / LEMP Stack

> **"Stack คือชุดซอฟต์แวร์ที่ทำงานร่วมกันเพื่อรันเว็บไซต์"**

---

## 1. LAMP Stack คืออะไร

**LAMP** = ตัวย่อจาก 4 ซอฟต์แวร์

| ตัวอักษร | ซอฟต์แวร์ | หน้าที่ |
|---------|---------|---------|
| **L** | Linux | Operating System — รองรับทุกอย่าง |
| **A** | Apache | Web Server — รับ HTTP Request |
| **M** | MySQL / MariaDB | Database — เก็บข้อมูล |
| **P** | PHP | Programming Language — สร้าง HTML แบบ dynamic |

```
Linux OS
  └── Apache (รับ Request)
        └── PHP (ประมวลผล)
              └── MySQL (ดึงข้อมูล)
```

---

## 2. LEMP Stack คืออะไร

**LEMP** = เปลี่ยน Apache → **Nginx** (อ่านว่า "Engine-X")

| ตัวอักษร | ซอฟต์แวร์ | หน้าที่ |
|---------|---------|---------|
| **L** | Linux | Operating System |
| **E** | Nginx | Web Server — เร็วกว่า Apache สำหรับ traffic สูง |
| **M** | MySQL / MariaDB | Database |
| **P** | PHP | Programming Language |

> ทำไมเขียน E แต่ออกเสียง Nginx? → เพราะ "Engine-X" → **E**nginX

---

## 3. LAMP vs LEMP เปรียบเทียบ

| หัวข้อ | LAMP (Apache) | LEMP (Nginx) |
|--------|--------------|-------------|
| Web Server | Apache | Nginx |
| จัดการ traffic สูง | ปานกลาง | ดีกว่า |
| Static files | ปานกลาง | เร็วกว่ามาก |
| Config ง่าย? | ง่าย (.htaccess) | ยากกว่าเล็กน้อย |
| WordPress รองรับ? | ✅ | ✅ |
| ใช้ RAM | มากกว่า | น้อยกว่า |
| นิยมใช้กับ | Shared Hosting, XAMPP | VPS, Cloud Server |

---

## 4. Request ไหลผ่าน Stack อย่างไร

```
Browser ส่ง Request
        │
        ▼
  ┌─────────────┐
  │   Apache    │  ← รับ Request, เช็ค .htaccess
  │   / Nginx   │    ถ้าเป็น static file (.jpg, .css) → ส่งเลย
  └──────┬──────┘    ถ้าเป็น .php → ส่งต่อ
         │
         ▼
  ┌─────────────┐
  │     PHP     │  ← ประมวลผล WordPress
  │  (PHP-FPM)  │    โหลด wp-load.php, query database
  └──────┬──────┘
         │
         ▼
  ┌─────────────┐
  │    MySQL    │  ← SELECT posts, options, users...
  └──────┬──────┘
         │
         ▼
  PHP สร้าง HTML
         │
         ▼
  Browser แสดงผล
```

---

## 5. เชื่อมกับ WordPress

WordPress ต้องการ Stack ครบทั้ง 4 ชั้น:

| Layer | WordPress ใช้ทำอะไร |
|-------|-------------------|
| Linux | รัน services ทั้งหมด |
| Apache/Nginx | รับ URL เช่น `/about/` ส่งให้ WordPress |
| MySQL | เก็บ posts, pages, users, settings, plugins |
| PHP | รัน WordPress core code ทั้งหมด |

### WordPress กับ .htaccess (Apache)

```apache
# Permalink ของ WordPress ต้องการบรรทัดนี้
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
```

> ถ้าไม่มีไฟล์ `.htaccess` → URL เช่น `/about/` จะ 404

---

## 6. Stack ในโลกจริง

| เครื่องมือ | Stack ที่ใช้ |
|---------|-----------|
| **XAMPP** | Windows/Mac + Apache + MySQL + PHP |
| **Local WP** | Nginx + MySQL + PHP (Docker-based) |
| **Docker (course นี้)** | Alpine Linux + Apache/Nginx + MySQL + PHP |
| **Shared Hosting** | Linux + Apache + MySQL + PHP |
| **DigitalOcean / AWS** | Linux + Nginx + MySQL + PHP (ตั้งเอง) |

---

## 7. เช็ค Version ใน XAMPP

เปิด **XAMPP Control Panel → Shell** แล้วพิมพ์:

```bash
# เช็ค PHP version
php -v

# เช็ค MySQL version
mysql --version

# เช็ค Apache version
httpd -v
```

---

## 8. สรุป

```
LAMP = Linux + Apache  + MySQL + PHP  → นิยมใน Shared Hosting
LEMP = Linux + Nginx   + MySQL + PHP  → นิยมใน VPS / Cloud
```

- WordPress ทำงานได้ทั้ง LAMP และ LEMP
- **XAMPP** ใช้ LAMP บนเครื่อง local
- **Docker** ในคอร์สนี้จำลอง stack เดียวกับ production server
