# WordPress General Settings
[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

> **"Settings → General คือจุดแรกที่ควรตั้งค่าหลังติดตั้ง WordPress เสร็จ"**

---

## เข้าถึงได้ที่

```
Admin Dashboard → Settings → General
```

---

## 1. Site Title & Tagline

```
Site Title:   Workppass
Tagline:      ระบบจัดการพนักงานออนไลน์
```

**Site Title** ปรากฏที่:
- Browser tab title
- `<title>` tag ใน `<head>`
- Schema markup (SEO)
- หน้า login

**Tagline** ปรากฏที่:
- `<meta name="description">` (ถ้า theme ใช้)
- บางส่วนของ theme header

ดึงค่าด้วย PHP:
```php
echo get_bloginfo('name');        // Site Title
echo get_bloginfo('description'); // Tagline
```

---

## 2. WordPress Address & Site Address

| Field | คืออะไร |
|-------|---------|
| **WordPress Address (URL)** | URL ที่ไฟล์ WordPress อยู่จริง |
| **Site Address (URL)** | URL ที่ผู้ใช้เข้าถึงเว็บ |

ปกติทั้งสองค่าเหมือนกัน:
```
WordPress Address: https://example.com
Site Address:      https://example.com
```

กรณีแยก (WordPress อยู่ใน subfolder):
```
WordPress Address: https://example.com/wp    ← ไฟล์อยู่ที่นี่
Site Address:      https://example.com       ← แต่เข้าเว็บที่นี่
```

> ⚠️ แก้ไขผิดพลาด → เว็บเข้าไม่ได้ทันที — แก้ไขผ่าน `wp-config.php` หรือ WP-CLI แทน

```bash
# แก้ไขผ่าน WP-CLI ปลอดภัยกว่า
wp option update siteurl 'https://example.com'
wp option update home 'https://example.com'
```

---

## 3. Administration Email Address

```
admin@example.com
```

ใช้สำหรับ:
- รับการแจ้งเตือนจากระบบ
- รับอีเมล reset password ของ admin
- WordPress ส่งอีเมลแจ้งเมื่อมี user ใหม่สมัคร
- Plugin ใช้เป็น default "From" address

> ควรใช้อีเมลที่เปิดอ่านจริงๆ ไม่ใช่ noreply

---

## 4. Membership & New User Default Role

```
☐ Anyone can register    ← ปิดถ้าไม่ต้องการให้สมัครสมาชิกเอง

New User Default Role:   Subscriber ▼
```

### WordPress Roles

| Role | สิทธิ์ |
|------|--------|
| **Administrator** | ทุกอย่าง |
| **Editor** | จัดการ posts/pages ทุกชิ้น |
| **Author** | จัดการเฉพาะ posts ของตัวเอง |
| **Contributor** | เขียน post แต่ publish ไม่ได้ |
| **Subscriber** | แค่อ่าน + แก้ profile ตัวเอง |

> ควรตั้ง Default Role เป็น **Subscriber** เสมอ — อย่าให้ใครได้ Editor/Admin โดยอัตโนมัติ

---

## 5. Site Language & Timezone

```
Site Language:  ภาษาไทย (th)
Timezone:       Bangkok  (UTC+7)
```

**Timezone** กระทบ:
- วันที่ publish post
- เวลาใน admin dashboard
- Scheduled posts (ตั้งเวลาเผยแพร่)
- Plugin ที่ใช้เวลา เช่น WooCommerce

```bash
# ตั้งค่าผ่าน WP-CLI
wp option update timezone_string 'Asia/Bangkok'
wp option update WPLANG 'th'
```

---

## 6. Date Format & Time Format

```
Date Format:   d/m/Y    →  20/02/2026
Time Format:   H:i      →  14:30
```

Format ที่ใช้บ่อย:

| Format | ผลลัพธ์ |
|--------|---------|
| `d/m/Y` | 20/02/2026 |
| `Y-m-d` | 2026-02-20 |
| `F j, Y` | February 20, 2026 |
| `j F Y` | 20 February 2026 |
| `H:i` | 14:30 |
| `g:i a` | 2:30 pm |

---

## 7. Week Starts On

```
Week Starts On:  Monday ▼
```

กระทบ Calendar widget และ plugin ที่แสดง calendar

---

---

## 8. Reading Settings

```
Settings → Reading
```

| ตัวเลือก | คำอธิบาย |
| -------- | -------- |
| **Your homepage displays** | เลือกว่าหน้าแรกแสดงอะไร |
| A static page | ใช้ Page ที่สร้างไว้เป็นหน้าแรก (แนะนำ) |
| Your latest posts | แสดง blog posts ล่าสุด (default) |

### ตั้งค่า Front Page / Blog Page

```
Homepage:     [Home]      ← Page ชื่อ "Home"
Posts page:   [Blog]      ← Page ชื่อ "Blog"
```

สิ่งที่ต้องทำก่อน:

1. สร้าง Page ชื่อ **Home** (เนื้อหาว่างก็ได้)
2. สร้าง Page ชื่อ **Blog**
3. ไปที่ Settings → Reading → เลือกทั้งสองหน้า

```bash
# ตั้งค่าผ่าน WP-CLI
wp option update show_on_front 'page'
wp option update page_on_front <HOME_PAGE_ID>
wp option update page_for_posts <BLOG_PAGE_ID>
```

---

## 9. Discussion Settings (Disable Comments)

```
Settings → Discussion
```

### ปิด Comments ทั้งเว็บ

```
☐ Allow people to submit comments on new posts   ← uncheck นี้
```

> ปิดที่นี่จะกระทบเฉพาะ **posts ใหม่** — posts เก่ายังคง setting เดิม

### ปิด Comments ทุก post พร้อมกัน (WP-CLI)

```bash
# ปิด comment ทุก post ที่มีอยู่
wp post list --format=ids | xargs -d ' ' -I {} wp post update {} --comment_status=closed

# หรือผ่าน SQL
wp db query "UPDATE wp_posts SET comment_status='closed' WHERE post_type='post';"
```

### การตั้งค่า Discussion ที่แนะนำสำหรับเว็บองค์กร

```
☐ Allow link notifications from other blogs (pingbacks and trackbacks)
☐ Allow people to submit comments on new posts
☑ Comment must be manually approved
☑ Comment author must have a previously approved comment
```

---

## 10. Permalink Structure

```
Settings → Permalinks
```

### รูปแบบ Permalink

| ตัวเลือก | ตัวอย่าง URL | SEO |
| -------- | ----------- | --- |
| Plain | `/?p=123` | ❌ แย่ |
| Day and name | `/2026/02/20/post-name/` | ปานกลาง |
| Month and name | `/2026/02/post-name/` | ปานกลาง |
| Numeric | `/archives/123` | ❌ แย่ |
| **Post name** | `/post-name/` | ✅ ดีที่สุด |
| Custom | กำหนดเอง | ขึ้นอยู่กับ pattern |

> แนะนำ **Post name** — URL สั้น สะอาด จำง่าย และ Google ชอบ

### ตั้งค่า

```
⦿ Post name    →   /%postname%/
```

กด **Save Changes** → WordPress สร้าง `.htaccess` ใหม่อัตโนมัติ

```bash
# ตั้งค่าผ่าน WP-CLI
wp option update permalink_structure '/%postname%/'
wp rewrite flush
```

### ข้อควรระวัง

> ❌ อย่าเปลี่ยน Permalink หลังเว็บ live แล้ว — URL เก่าทั้งหมดจะพัง และ SEO ranking หาย

---

## การตั้งค่าที่ควรทำทันทีหลังติดตั้ง

```
✅ Site Title      → ชื่อจริงของเว็บ
✅ Tagline         → คำอธิบายสั้นๆ หรือลบออก
✅ Site URL        → ตรวจสอบว่าถูกต้อง (http vs https)
✅ Admin Email     → อีเมลที่เปิดอ่านจริง
✅ Membership      → ปิด "Anyone can register"
✅ Timezone        → Asia/Bangkok
✅ Date Format     → รูปแบบที่เหมาะกับกลุ่มเป้าหมาย
✅ Reading         → ตั้ง Front page / Posts page
✅ Discussion      → ปิด Comments (ถ้าไม่ต้องการ)
✅ Permalinks      → Post name (/%postname%/)
```

---

## สรุป

```
Settings → General
  ├── Site Title & Tagline     → ชื่อและคำอธิบายเว็บ
  ├── WordPress/Site Address   → URL หลักของเว็บ
  ├── Admin Email              → อีเมลรับแจ้งเตือน
  ├── Membership               → ปิด registration ถ้าไม่ต้องการ
  ├── Default Role             → Subscriber (ปลอดภัยที่สุด)
  ├── Language & Timezone      → ภาษาไทย / Asia/Bangkok
  └── Date/Time Format         → ตามกลุ่มเป้าหมาย

Settings → Reading
  ├── Front page               → Static page (Home)
  └── Posts page               → Blog page

Settings → Discussion
  └── Disable comments         → uncheck "Allow comments"

Settings → Permalinks
  └── Post name                → /%postname%/
```
