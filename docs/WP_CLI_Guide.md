# WP-CLI — WordPress Command Line Interface
[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

> **"WP-CLI คือเครื่องมือที่ให้เราควบคุม WordPress ผ่าน terminal แทนการคลิกใน Admin Dashboard"**

---

## 1. WP-CLI คืออะไร

- Command Line tool สำหรับจัดการ WordPress
- ทำได้ทุกอย่างที่ทำผ่าน Admin Dashboard — แต่เร็วกว่า
- ใช้ได้กับ: Local WP, Docker, Server จริง (SSH)
- ประหยัดเวลามากเมื่อต้องจัดการหลายเว็บพร้อมกัน

---

## 2. เข้าใช้งาน WP-CLI

### ผ่าน Docker (โปรเจกต์นี้)

```bash
# เข้าไปใน WordPress container ก่อน
docker compose exec wordpress bash

# แล้วใช้ wp command ได้เลย
wp --info
```

### ผ่าน Local WP

```
Site → Open Site Shell → พิมพ์ wp command ได้เลย
```

### ตรวจสอบว่าใช้งานได้

```bash
wp --info
```

ผลลัพธ์:
```
OS:     Linux
Shell:  /bin/bash
PHP:    8.2.x
WP-CLI: 2.x.x
```

---

## 3. คำสั่งพื้นฐาน

### Core WordPress

```bash
# ดู WP version
wp core version

# อัปเดต WordPress core
wp core update

# ติดตั้ง WordPress ใหม่ (ถ้ายังไม่ได้ install)
wp core install \
  --url="http://localhost:8088" \
  --title="My Site" \
  --admin_user="admin" \
  --admin_password="admin" \
  --admin_email="admin@example.com"
```

---

## 4. Plugin Commands

```bash
# ดู plugins ทั้งหมด
wp plugin list

# ติดตั้ง plugin
wp plugin install woocommerce
wp plugin install contact-form-7

# เปิดใช้งาน
wp plugin activate woocommerce

# ปิดการใช้งาน
wp plugin deactivate woocommerce

# ลบ plugin
wp plugin delete woocommerce

# อัปเดต plugin ทั้งหมด
wp plugin update --all

# ติดตั้ง + เปิดใช้งานในคำสั่งเดียว
wp plugin install woocommerce --activate
```

---

## 5. Theme Commands

```bash
# ดู themes ทั้งหมด
wp theme list

# ติดตั้ง theme
wp theme install astra

# เปิดใช้งาน theme
wp theme activate astra

# อัปเดต themes ทั้งหมด
wp theme update --all

# ลบ theme
wp theme delete twentytwentyone
```

---

## 6. User Commands

```bash
# ดู users ทั้งหมด
wp user list

# สร้าง user ใหม่
wp user create john john@example.com \
  --role=editor \
  --user_pass=StrongPass123

# เปลี่ยน password
wp user update admin --user_pass=NewPassword123

# ดู roles ทั้งหมด
wp role list

# ลบ user
wp user delete 2 --reassign=1
```

---

## 7. Post & Content Commands

```bash
# ดู posts ทั้งหมด
wp post list

# ดู posts เฉพาะ type
wp post list --post_type=page --post_status=publish

# สร้าง post ใหม่
wp post create \
  --post_title="Hello World" \
  --post_content="Content here" \
  --post_status=publish \
  --post_type=post

# ลบ post
wp post delete 1

# ลบ posts ทั้งหมดใน type นั้น
wp post delete $(wp post list --post_type=post --format=ids)
```

---

## 8. Options / Settings Commands

```bash
# ดู option
wp option get siteurl
wp option get blogname

# แก้ไข option
wp option update blogname "ชื่อเว็บใหม่"
wp option update blogdescription "คำอธิบายเว็บ"

# ดู options หลายตัวพร้อมกัน
wp option get home
wp option get siteurl
```

---

## 9. Database Commands

```bash
# Export database (backup)
wp db export backup.sql

# Import database
wp db import backup.sql

# เปิด MySQL shell
wp db cli

# รัน SQL query
wp db query "SELECT ID, post_title FROM wp_posts WHERE post_status='publish';"

# เช็ค database
wp db check

# Optimize tables
wp db optimize
```

---

## 10. Cache Commands

```bash
# ล้าง cache ทั้งหมด
wp cache flush

# ล้าง transients
wp transient delete --all

# ล้าง rewrite rules (แก้ปัญหา permalink)
wp rewrite flush
```

---

## 11. Search & Replace

```bash
# เปลี่ยน URL (เช่น ย้ายจาก local ไป production)
wp search-replace \
  'http://localhost:8088' \
  'https://example.com' \
  --all-tables

# Preview ก่อน (ไม่ได้แก้จริง)
wp search-replace \
  'http://localhost:8088' \
  'https://example.com' \
  --all-tables \
  --dry-run
```

> ใช้บ่อยมากตอน **migrate** เว็บจาก local ไป server จริง

---

## 12. ใช้กับ Docker โปรเจกต์นี้

```bash
# เข้า container
docker compose exec wordpress bash

# คำสั่งที่ใช้บ่อยใน training นี้

# เช็ค WordPress พร้อมใช้ไหม
wp core is-installed && echo "WordPress is installed"

# ดู plugins ที่ active
wp plugin list --status=active

# ล้าง cache หลังแก้ไขอะไร
wp cache flush && wp rewrite flush

# export DB ก่อน down -v
wp db export /var/www/html/backup-$(date +%Y%m%d).sql
```

---

## 13. สรุปคำสั่งที่ใช้บ่อย

| หมวด | คำสั่ง | ทำอะไร |
|------|--------|--------|
| Core | `wp core version` | ดู WP version |
| Plugin | `wp plugin list` | ดู plugins ทั้งหมด |
| Plugin | `wp plugin install X --activate` | ติดตั้ง + เปิด |
| Plugin | `wp plugin update --all` | อัปเดตทุก plugin |
| Theme | `wp theme activate X` | เปลี่ยน theme |
| User | `wp user list` | ดู users |
| DB | `wp db export backup.sql` | Backup DB |
| DB | `wp search-replace old new` | เปลี่ยน URL ทั้งเว็บ |
| Cache | `wp cache flush` | ล้าง cache |
| Cache | `wp rewrite flush` | แก้ปัญหา permalink |
