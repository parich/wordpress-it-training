# phpMyAdmin — Import / Export ข้ามฐานข้อมูลและข้ามเว็บไซต์

---

## phpMyAdmin คืออะไร

phpMyAdmin คือ Web UI สำหรับจัดการ MySQL / MariaDB ผ่าน Browser
ไม่ต้องพิมพ์ SQL Command เอง

```
Browser
  └── phpMyAdmin (Web UI)
          └── MySQL / MariaDB
                  ├── database ของเว็บ A
                  ├── database ของเว็บ B
                  └── database ของเว็บ C
```

**URL ที่พบบ่อย:**

```
http://localhost:8089/           ← Docker (โปรเจกต์นี้)
https://202.29.22.64:8090/phpmyadmin   ← CyberPanel
https://202.29.22.61:8083/phpmyadmin   ← HestiaCP
http://your-domain.com/phpmyadmin      ← cPanel / ทั่วไป
```

---

## หน้าตาหลัก phpMyAdmin

```
┌──────────────────────────────────────────────────────────┐
│  phpMyAdmin                                              │
├─────────────────┬────────────────────────────────────────┤
│ (แถบซ้าย)       │ (แถบขวา — เนื้อหาหลัก)               │
│                 │                                        │
│ ▼ wordpress_db  │  Database: wordpress_db               │
│   wp_comments   │  ┌──────────────────────────────────┐ │
│   wp_links      │  │ Structure │ SQL │Import │ Export  │ │
│   wp_options    │  └──────────────────────────────────┘ │
│   wp_posts      │                                        │
│   wp_postmeta   │  Tables: 12    Size: 2.4 MiB           │
│   wp_terms      │                                        │
│   wp_users      │                                        │
│   wp_usermeta   │                                        │
└─────────────────┴────────────────────────────────────────┘
```

---

## Export (สำรองข้อมูล / ส่งออก)

### วิธี Export Database ทั้งหมด

1. คลิกชื่อ **Database** ในแถบซ้าย (เช่น `wordpress_db`)
2. คลิกแถบ **Export** ด้านบน
3. เลือก Export Method:

| Method | เหมาะกับ |
|--------|---------|
| **Quick** | Export ทั้ง Database ทันที (ใช้บ่อย) |
| **Custom** | เลือกตาราง, encoding, compression เอง |

4. Format: เลือก **SQL** (มาตรฐาน, แก้ไขได้)
5. คลิก **Export** → ได้ไฟล์ `.sql`

---

### Export แบบ Custom (แนะนำสำหรับย้ายเว็บ)

เลือก **Custom** → จะปรากฏตัวเลือกเพิ่มเติม:

```
✅ Add DROP TABLE / VIEW / PROCEDURE / FUNCTION / EVENT / TRIGGER statement
   ← สำคัญ: ป้องกัน Error ถ้ามีตารางเดิมอยู่แล้วตอน Import

✅ Add CREATE DATABASE / USE statement
   ← เพิ่มคำสั่งสร้าง DB ใหม่อัตโนมัติ (ไม่ต้องสร้างก่อน)

Output:
  ◉ Save output to a file
  Compression: gzip  ← บีบอัดถ้าไฟล์ใหญ่
```

**ผลลัพธ์:** ไฟล์ `wordpress_db.sql` หรือ `wordpress_db.sql.gz`

---

### ตัวอย่างเนื้อหาในไฟล์ .sql

```sql
-- phpMyAdmin SQL Dump
-- Host: db

CREATE DATABASE IF NOT EXISTS `wordpress_db`;
USE `wordpress_db`;

DROP TABLE IF EXISTS `wp_posts`;
CREATE TABLE `wp_posts` (
  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_title` text NOT NULL,
  ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `wp_posts` (`ID`, `post_title`, ...) VALUES
(1, 'Hello World', ...),
(2, 'About Us', ...);
```

---

## Import (นำเข้าข้อมูล / Restore)

### วิธี Import เข้า Database เดิม (ชื่อเดิม)

1. คลิกชื่อ **Database** ในแถบซ้าย
2. คลิกแถบ **Import**
3. คลิก **Choose File** → เลือกไฟล์ `.sql`
4. Encoding: `utf8mb4` (WordPress)
5. คลิก **Import**

> **ข้อควรระวัง:** ถ้า `.sql` มีคำสั่ง `DROP TABLE` ข้อมูลเดิมจะถูกลบก่อน Import ใหม่

---

## Import / Export ข้ามชื่อ Database

### สถานการณ์: Export จาก `db_siteA` → Import เข้า `db_siteB`

นี่คือปัญหาที่พบบ่อยที่สุดตอนย้ายเว็บไซต์

**ปัญหา:** ไฟล์ `.sql` มีชื่อ Database เดิมฝังอยู่

```sql
-- ในไฟล์ .sql ที่ Export มา
CREATE DATABASE IF NOT EXISTS `db_siteA`;   ← ชื่อเดิม
USE `db_siteA`;
```

ถ้า Import เข้าไปตรง ๆ จะสร้าง `db_siteA` ใหม่ ไม่ใช่ `db_siteB` ที่ต้องการ

---

### วิธีที่ 1 — แก้ไขชื่อใน .sql ก่อน Import (แนะนำ)

เปิดไฟล์ `.sql` ด้วย Text Editor (VS Code, Notepad++) แล้ว Find & Replace:

```
Find:    db_siteA
Replace: db_siteB
```

**ตรวจสอบ 3 จุดนี้:**

```sql
-- ก่อนแก้
CREATE DATABASE IF NOT EXISTS `db_siteA`;
USE `db_siteA`;

-- หลังแก้
CREATE DATABASE IF NOT EXISTS `db_siteB`;
USE `db_siteB`;
```

แล้ว Import ไฟล์ที่แก้แล้วตามปกติ

---

### วิธีที่ 2 — Import เข้า Database ที่สร้างไว้แล้ว (ไม่มี CREATE DATABASE)

1. **สร้าง Database ใหม่ก่อน** ใน phpMyAdmin:
   - คลิก **New** ในแถบซ้าย
   - ชื่อ: `db_siteB`
   - Collation: `utf8mb4_unicode_ci`
   - คลิก **Create**

2. **Export ใหม่** โดยไม่ติ๊ก "Add CREATE DATABASE":
   - Export Method: **Custom**
   - ยกเลิก ✅ `Add CREATE DATABASE / USE statement`
   - Export → ได้ไฟล์ที่ไม่มีชื่อ DB

3. คลิกชื่อ `db_siteB` ในแถบซ้าย → **Import** → เลือกไฟล์

---

### วิธีที่ 3 — Import ผ่าน phpMyAdmin โดยเลือก Target Database

1. คลิก **db_siteB** ในแถบซ้าย (ต้องสร้างไว้ก่อน)
2. คลิก **Import**
3. เลือกไฟล์ `.sql` (แม้มีชื่อ DB เดิม phpMyAdmin จะใช้ DB ที่เลือกอยู่)

> **หมายเหตุ:** วิธีนี้ได้ผลเฉพาะเมื่อไฟล์ `.sql` **ไม่มี** `CREATE DATABASE` และ `USE`
> ถ้ามี ต้องแก้ตามวิธีที่ 1

---

## ย้ายเว็บไซต์ WordPress ข้ามเซิร์ฟเวอร์ (ต่างชื่อ DB + ต่าง URL)

### ขั้นตอนครบวงจร

```
เว็บเก่า (Server A)              เว็บใหม่ (Server B)
─────────────────────            ─────────────────────
DB: db_siteA                     DB: db_siteB
URL: https://old-site.com        URL: https://new-site.com
```

#### ขั้นที่ 1 — Export จาก Server A

phpMyAdmin → `db_siteA` → **Export** → Custom:
- ✅ DROP TABLE
- ✅ CREATE DATABASE (หรือไม่ก็ได้ แล้วแก้ทีหลัง)
- บันทึกเป็น `db_siteA.sql`

#### ขั้นที่ 2 — แก้ไขไฟล์ .sql

Find & Replace ใน Text Editor:

```
db_siteA  →  db_siteB
```

#### ขั้นที่ 3 — แก้ไข URL ใน .sql (สำคัญมาก)

WordPress เก็บ URL ไว้ในตาราง `wp_options` แบบ Serialized
**ห้ามแก้ด้วย Find & Replace ธรรมดา** เพราะจะทำให้ Serialized string พัง

ใช้ **Search-Replace-DB** หรือ **WP-CLI** หลัง Import แทน (ดูขั้นที่ 5)

#### ขั้นที่ 4 — Import เข้า Server B

phpMyAdmin → `db_siteB` → **Import** → เลือก `db_siteA.sql` ที่แก้แล้ว

#### ขั้นที่ 5 — แก้ไข URL ใน wp_options

ใน phpMyAdmin → `db_siteB` → แถบ **SQL** → รันคำสั่ง:

```sql
-- ตรวจสอบ URL เดิม
SELECT option_name, option_value
FROM wp_options
WHERE option_name IN ('siteurl', 'home');
```

ผลลัพธ์:

```
option_name  │ option_value
─────────────┼───────────────────────────
siteurl      │ https://old-site.com
home         │ https://old-site.com
```

อัปเดต URL ใหม่:

```sql
UPDATE wp_options
SET option_value = 'https://new-site.com'
WHERE option_name IN ('siteurl', 'home');
```

#### ขั้นที่ 6 — แก้ไข wp-config.php บน Server B

เปิดไฟล์ `wp-config.php` แล้วแก้:

```php
define( 'DB_NAME',     'db_siteB' );
define( 'DB_USER',     'user_siteB' );
define( 'DB_PASSWORD', 'password_siteB' );
define( 'DB_HOST',     'localhost' );
```

#### ขั้นที่ 7 — แก้ URL ที่ฝังในเนื้อหา (Post Content)

URL ที่ฝังในเนื้อหาบทความ, รูปภาพ ต้องแก้เพิ่ม ใช้ WP-CLI:

```bash
wp search-replace 'https://old-site.com' 'https://new-site.com' --all-tables
```

---

## ตารางสรุป: Export ด้วยตัวเลือกไหน ใช้เมื่อไหร่

| สถานการณ์ | Export Method | ตัวเลือกสำคัญ |
|----------|--------------|--------------|
| Backup ปกติ | Quick | — |
| ย้ายไป DB ชื่อเดิม | Quick | — |
| ย้ายไป DB ชื่อใหม่ | Custom | ไม่ติ๊ก CREATE DATABASE |
| ย้ายข้ามเซิร์ฟเวอร์ | Custom | DROP TABLE + ไม่ใส่ charset ผิด |
| ไฟล์ใหญ่ > 50 MB | Custom | เลือก gzip compression |

---

## ปัญหาที่พบบ่อยและวิธีแก้

| ปัญหา | สาเหตุ | วิธีแก้ |
|-------|--------|---------|
| `#1044 Access denied` | User ไม่มีสิทธิ์ DB | ให้ Admin สร้าง DB User ใหม่ |
| `#1046 No database selected` | ไม่ได้คลิก DB ก่อน Import | คลิกชื่อ DB ในแถบซ้ายก่อน |
| `#1050 Table already exists` | ไม่มี DROP TABLE ใน .sql | Export ใหม่พร้อม DROP TABLE |
| ไฟล์ใหญ่เกิน อัปโหลดไม่ได้ | `upload_max_filesize` เล็กเกิน | Import ผ่าน SSH แทน หรือเพิ่ม limit |
| หน้าเว็บขาว (White Screen) | URL ใน wp_options ยังเป็นเดิม | UPDATE wp_options siteurl/home |
| รูปภาพหาย | Path รูปยังชี้ไป URL เดิม | wp search-replace URL เดิม → ใหม่ |

---

## ขีดจำกัดขนาดไฟล์ Import

phpMyAdmin อ่านค่าจาก PHP:

```
upload_max_filesize   = 64M   ← โปรเจกต์นี้ (wp/uploads.ini)
post_max_size         = 64M
memory_limit          = 128M
max_execution_time    = 300
```

ถ้า Database ใหญ่กว่า 64 MB ให้ใช้วิธีอื่น:

```bash
# Import ผ่าน MySQL command line (ไม่มี size limit)
mysql -u root -p db_siteB < db_siteA.sql

# Docker (โปรเจกต์นี้)
docker exec -i wp-db-1 mysql -u root -pYOUR_PASSWORD wordpress < backup.sql
```

---

## ดูเพิ่มเติม

- [Web_Control_Panel_Guide.md](Web_Control_Panel_Guide.md) — วิธีเข้า phpMyAdmin บน Control Panel ต่าง ๆ
- [WordPress_Database_Structure.md](WordPress_Database_Structure.md) — โครงสร้างตาราง WordPress
- [WordPress_Backup_Guide.md](WordPress_Backup_Guide.md) — Backup ครบทั้ง Files + Database
- [WP_CLI_Guide.md](WP_CLI_Guide.md) — search-replace URL ด้วย WP-CLI
