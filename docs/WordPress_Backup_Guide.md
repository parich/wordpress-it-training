# WordPress Backup Guide

[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

---

## ทำไมต้อง Backup?

WordPress ประกอบด้วย 2 ส่วนหลักที่ต้อง backup:

| ส่วน | เนื้อหา | ตำแหน่ง |
| ---- | ------- | ------- |
| **Database** | Posts, Pages, Settings, Users, Comments | MySQL / MariaDB |
| **Files** | Themes, Plugins, Uploads (รูปภาพ) | `wp-content/` |

> การ backup ที่สมบูรณ์ต้องได้ทั้งสองส่วน

---

## 1. Manual Backup (ไม่ใช้ Plugin)

### 1.1 Backup Database ด้วย phpMyAdmin

1. เปิด phpMyAdmin → เลือก database ของ WordPress
2. คลิกแท็บ **Export**
3. เลือก Format: `SQL`
4. คลิก **Export** → ได้ไฟล์ `.sql`

```
ไฟล์ที่ได้: wordpress_backup_2024-01-01.sql
```

### 1.2 Backup Files ด้วย File Manager / FTP

ไฟล์ที่ต้อง backup คือโฟลเดอร์ `wp-content/`:

```
wp-content/
├── themes/       ← theme ที่ใช้งาน
├── plugins/      ← plugin ทั้งหมด
└── uploads/      ← รูปภาพและไฟล์ที่อัปโหลด
```

**วิธี zip ผ่าน Terminal:**

```bash
# Linux / Mac
zip -r wp-content-backup.zip wp-content/

# Windows PowerShell
Compress-Archive -Path wp-content -DestinationPath wp-content-backup.zip
```

### 1.3 Restore Manual Backup

**Restore Database:**

```sql
-- ผ่าน phpMyAdmin: Import → เลือกไฟล์ .sql
-- หรือผ่าน WP-CLI:
wp db import wordpress_backup.sql
```

**Restore Files:**

```bash
# แตกไฟล์ zip ทับ wp-content/ เดิม
unzip wp-content-backup.zip
```

---

## 2. Plugin: UpdraftPlus

> UpdraftPlus เป็น plugin backup ยอดนิยมอันดับ 1 มีผู้ใช้กว่า 3 ล้านเว็บ

### ติดตั้ง

**Plugins → Add New → ค้นหา "UpdraftPlus" → Install → Activate**

### การตั้งค่า (Settings → UpdraftPlus Backups)

| ตัวเลือก | แนะนำ |
| ------- | ----- |
| Files backup schedule | Weekly |
| Database backup schedule | Daily |
| Retain this many backups | 2–3 ชุด |
| Remote Storage | Google Drive / Dropbox (แนะนำ) |

### สร้าง Backup ทันที

1. ไปที่ **Settings → UpdraftPlus Backups**
2. คลิก **Backup Now**
3. เลือก ✅ Include your database และ ✅ Include your files
4. คลิก **Backup Now** อีกครั้ง

### Restore ด้วย UpdraftPlus

1. ไปที่แท็บ **Existing Backups**
2. เลือกชุด backup ที่ต้องการ
3. คลิก **Restore** → เลือกส่วนที่ต้องการ (Database / Files)
4. คลิก **Restore** → รอจนเสร็จ

### Remote Storage (Free)

- **Google Drive** — ต้องเชื่อมต่อผ่าน OAuth
- **Dropbox** — ฟรีถึง 2 GB
- **Email** — ส่งไฟล์ backup ทาง email (ขนาดเล็กเท่านั้น)

> **Premium:** Amazon S3, Azure, WebDAV, SFTP

---

## 3. Plugin: All-in-One WP Migration

> เหมาะสำหรับ **ย้ายเว็บ** (Migrate) หรือ clone เว็บระหว่าง environment เช่น Local → Production

### ติดตั้ง

**Plugins → Add New → ค้นหา "All-in-One WP Migration" → Install → Activate**

### Export (Backup / ย้ายออก)

1. ไปที่ **All-in-One WP Migration → Export**
2. เลือก **Export To → File**
3. รอ export → ดาวน์โหลดไฟล์ `.wpress`

```
ไฟล์ที่ได้: your-site.wpress  (รวม DB + Files ในไฟล์เดียว)
```

### Import (Restore / ย้ายเข้า)

1. ติดตั้ง WordPress ใหม่ (เปล่า)
2. ติดตั้ง Plugin: All-in-One WP Migration
3. ไปที่ **All-in-One WP Migration → Import**
4. ลากไฟล์ `.wpress` หรือคลิก **Import From → File**
5. กด **Proceed** เพื่อยืนยัน

> ⚠️ การ Import จะ **ลบข้อมูลเดิมทั้งหมด** และแทนที่ด้วยข้อมูลจากไฟล์ `.wpress`

### ข้อจำกัด Free Version

| ข้อจำกัด | รายละเอียด |
| -------- | ---------- |
| ขนาดไฟล์ Import | สูงสุด **512 MB** (Free) |
| Storage | เฉพาะ Local File (ไม่มี Cloud ใน Free) |
| Multisite | ไม่รองรับ (ต้องซื้อ Premium) |

> หากเว็บมีขนาดใหญ่กว่า 512 MB ให้ใช้ UpdraftPlus แทน

---

## 4. เปรียบเทียบวิธี Backup

| วิธี | ง่าย | อัตโนมัติ | Cloud Storage | Migrate | เหมาะกับ |
| ---- | ---- | --------- | ------------- | ------- | --------- |
| Manual | ❌ ยาก | ❌ | ❌ | ✅ | Dev / Docker |
| UpdraftPlus | ✅ | ✅ | ✅ | ❌ | Production |
| All-in-One WP Migration | ✅ | ❌ | ❌ (Free) | ✅ | Local → Server |

---

## 5. แนวทางปฏิบัติที่ดี (Best Practices)

- [ ] Backup **ก่อน** อัปเดต Core / Plugin / Theme ทุกครั้ง
- [ ] เก็บ backup ไว้ **นอกเซิร์ฟเวอร์** (Google Drive / Local PC)
- [ ] ทดสอบ Restore อย่างน้อยปีละครั้ง
- [ ] ตั้งค่า Backup อัตโนมัติ (Daily DB + Weekly Files)
- [ ] เก็บ backup อย่างน้อย **3 ชุดล่าสุด**
