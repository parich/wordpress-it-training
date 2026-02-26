# WordPress REST API

WordPress มี REST API ในตัว (built-in ตั้งแต่ WordPress 4.7) ใช้ดึงข้อมูลได้ผ่าน URL เลยโดยไม่ต้องเขียนโค้ด

---

## REST API คืออะไร

```
Browser / App
     │
     │  GET https://example.com/wp-json/wp/v2/posts
     ▼
WordPress REST API
     │
     ▼
ตอบกลับเป็น JSON
[
  { "id": 1, "title": {...}, "content": {...} },
  { "id": 2, "title": {...}, "content": {...} }
]
```

ใช้สำหรับ:
- ดึงข้อมูล Posts, Pages, Users, Categories ผ่าน URL
- สร้าง Mobile App หรือ JavaScript App ที่ดึงข้อมูลจาก WordPress
- เชื่อมต่อ WordPress กับระบบภายนอก
- ทดสอบว่าเว็บ WordPress เปิดเผยข้อมูลอะไรบ้าง (มุมมองความปลอดภัย)

---

## 2 รูปแบบ URL ที่ใช้งาน

### รูปแบบที่ 1 — Pretty URL (ต้องเปิด Permalink)

```
https://example.com/wp-json/wp/v2/posts
```

ใช้ได้เมื่อ **Settings → Permalinks** ไม่ได้ตั้งเป็น "Plain"

### รูปแบบที่ 2 — `?rest_route=` (ใช้ได้เสมอ)

```
https://example.com/?rest_route=/wp/v2/posts
```

ใช้ได้แม้ Permalink ตั้งเป็น "Plain" หรือ server ไม่รองรับ mod_rewrite

> **เมื่อไหรควรใช้ `?rest_route=`**
> - `/wp-json/wp/v2/posts` ขึ้น 404
> - เว็บอยู่บน server ที่ไม่รองรับ `.htaccess` rewrite
> - ทดสอบเว็บที่ยังไม่ได้ตั้ง Permalink

---

## Endpoints หลักที่ใช้บ่อย

| ข้อมูล | Pretty URL | ?rest_route |
|--------|-----------|-------------|
| Posts ทั้งหมด | `/wp-json/wp/v2/posts` | `/?rest_route=/wp/v2/posts` |
| Post เดี่ยว | `/wp-json/wp/v2/posts/5` | `/?rest_route=/wp/v2/posts/5` |
| Pages | `/wp-json/wp/v2/pages` | `/?rest_route=/wp/v2/pages` |
| Categories | `/wp-json/wp/v2/categories` | `/?rest_route=/wp/v2/categories` |
| Tags | `/wp-json/wp/v2/tags` | `/?rest_route=/wp/v2/tags` |
| Users | `/wp-json/wp/v2/users` | `/?rest_route=/wp/v2/users` |
| Media | `/wp-json/wp/v2/media` | `/?rest_route=/wp/v2/media` |
| เช็ค API | `/wp-json` | `/?rest_route=/` |

---

## ตัวอย่างจริง: ค้นหาด้วย `?search=`

### Pretty URL

```
https://cc.rmu.ac.th/wp-json/wp/v2/posts?search=ข่าว
```

### ?rest_route (ใช้แทนถ้า pretty URL ไม่ทำงาน)

```
https://cc.rmu.ac.th/?rest_route=/wp/v2/posts&search=ข่าว
```

**ผลลัพธ์ (JSON):**

```json
[
  {
    "id": 42,
    "date": "2025-01-15T10:00:00",
    "slug": "news-january",
    "status": "publish",
    "title": {
      "rendered": "ข่าวประจำเดือนมกราคม"
    },
    "content": {
      "rendered": "<p>เนื้อหาข่าว...</p>"
    },
    "excerpt": {
      "rendered": "<p>สรุปข่าว...</p>"
    },
    "link": "https://cc.rmu.ac.th/news-january/",
    "categories": [3],
    "tags": [7, 12]
  }
]
```

---

## Parameters ที่ใช้บ่อย

### ค้นหาและกรอง

```
# ค้นหาคำ
?search=ข่าว
?rest_route=/wp/v2/posts&search=ข่าว

# กรองตาม Category ID
?categories=3
?rest_route=/wp/v2/posts&categories=3

# กรองตาม Tag ID
?tags=7

# กรองตาม Author ID
?author=1

# กรองตามสถานะ (ต้อง login)
?status=draft
```

### จำนวนและการเรียงลำดับ

```
# จำนวนผลลัพธ์ (default=10, max=100)
?per_page=5
?per_page=100

# หน้าที่ต้องการ (pagination)
?page=2

# เรียงลำดับ
?orderby=date&order=asc    ← เก่า → ใหม่
?orderby=date&order=desc   ← ใหม่ → เก่า (default)
?orderby=title&order=asc   ← เรียงตามชื่อ A-Z
?orderby=id&order=desc     ← เรียงตาม ID

# เลือกเฉพาะ field ที่ต้องการ (ลด data size)
?_fields=id,title,link,date
```

### ตัวอย่างรวม Parameters

```
# ดึงข่าว 5 อันล่าสุด เฉพาะ field ที่ต้องการ
/?rest_route=/wp/v2/posts&per_page=5&orderby=date&order=desc&_fields=id,title,link,date

# ค้นหา "ประกาศ" ใน Category ID 3
/?rest_route=/wp/v2/posts&search=ประกาศ&categories=3&per_page=10
```

---

## ดู Category ID และ Tag ID

ก่อนกรองด้วย `?categories=` ต้องรู้ ID ก่อน:

```
# ดู Categories ทั้งหมดพร้อม ID
/?rest_route=/wp/v2/categories

# ผลลัพธ์
[
  { "id": 1, "name": "Uncategorized", "slug": "uncategorized" },
  { "id": 3, "name": "ข่าวสาร",       "slug": "news" },
  { "id": 5, "name": "ประกาศ",         "slug": "announcement" }
]
```

แล้วนำ ID มาใช้กรอง:

```
/?rest_route=/wp/v2/posts&categories=3
```

---

## Custom Endpoint ของ Plugin (workppass-contact)

นอกจาก built-in endpoints WordPress ยังรองรับ **Custom Endpoint** ที่ Plugin สร้างเอง:

```
# Custom endpoint จาก workppass-contact plugin
POST https://example.com/wp-json/workppass/v1/contact

# หรือ
POST https://example.com/?rest_route=/workppass/v1/contact
```

```
Namespace: workppass/v1   ← กำหนดใน register_rest_route()
Route:     /contact
Method:    POST           ← รับข้อมูลฟอร์ม
```

---

## ความปลอดภัย — ข้อมูลที่รั่วออกได้

REST API เปิดเผยข้อมูลต่อไปนี้โดย default **โดยไม่ต้อง login:**

```
⚠️  /wp/v2/users   → username, name, avatar ของ users ทุกคน
⚠️  /wp/v2/posts   → เนื้อหาทุก post ที่ publish
⚠️  /wp/v2/pages   → เนื้อหาทุก page ที่ publish
⚠️  /wp/v2/media   → URL ไฟล์ทั้งหมดที่อัปโหลด
```

**ตัวอย่างที่น่ากังวล:**

```
# ดู username ทุกคน → ใช้ Brute Force Login ได้
/?rest_route=/wp/v2/users

# ผลลัพธ์
[
  { "id": 1, "name": "admin", "slug": "admin" }
]
```

---

## ปิดหรือจำกัด REST API (Security)

### วิธีที่ 1 — ปิด Users endpoint (แนะนำเสมอ)

เพิ่มใน `functions.php` ของ Theme หรือ Plugin:

```php
// ซ่อน Users จาก REST API (ต้องไม่ได้ login)
add_filter('rest_endpoints', function($endpoints) {
    if (!is_user_logged_in()) {
        unset($endpoints['/wp/v2/users']);
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});
```

### วิธีที่ 2 — บังคับ login ก่อนเข้า REST API ทั้งหมด

```php
// ต้อง login ถึงจะใช้ REST API ได้
add_filter('rest_authentication_errors', function($result) {
    if (!is_user_logged_in()) {
        return new WP_Error(
            'rest_not_logged_in',
            'กรุณา Login ก่อนใช้งาน API',
            ['status' => 401]
        );
    }
    return $result;
});
```

### วิธีที่ 3 — ใช้ Plugin

| Plugin | วิธีจำกัด |
|--------|----------|
| **Wordfence** | ปิด REST API ใน Firewall settings |
| **Disable REST API** | ปิดทั้งหมดสำหรับ non-logged-in users |
| **WP Hide & Security Enhancer** | ซ่อน endpoint ทั้งหมด |

---

## สรุปเปรียบเทียบ 2 รูปแบบ URL

| | Pretty URL | ?rest_route |
|---|---|---|
| รูปแบบ | `/wp-json/wp/v2/posts` | `/?rest_route=/wp/v2/posts` |
| ต้องการ Permalink | ใช่ (ไม่ใช่ Plain) | ไม่ — ทำงานได้เสมอ |
| อ่านง่าย | ✅ สวยกว่า | ❌ ยาวกว่า |
| ใช้เมื่อ | ทั่วไป | เมื่อ pretty URL ขึ้น 404 |
| Parameters | ต่อด้วย `?param=value` | ต่อด้วย `&param=value` |

---

## ดูเพิ่มเติม

- [Contact_Form_to_DB.md](Contact_Form_to_DB.md) — Custom REST endpoint ใน workppass plugin
- [WordPress_Discussion_Settings.md](WordPress_Discussion_Settings.md) — ความปลอดภัยทั่วไป
- [WordPress_Recommended_Plugins.md](WordPress_Recommended_Plugins.md#2-ความปลอดภัย-security) — Security plugins
