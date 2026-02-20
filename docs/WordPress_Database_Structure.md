# WordPress Database Structure Overview
[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

> **"WordPress เก็บทุกอย่างใน MySQL — รู้จัก table = เข้าใจว่าข้อมูลอยู่ที่ไหน"**

ข้อมูลนี้ได้จาก database จริงของโปรเจกต์นี้ (MariaDB 10.11)

---

## 1. ภาพรวม — 12 Tables

```
wordpress (database)
│
├── wp_posts              ← Posts, Pages, Custom Post Types
├── wp_postmeta           ← ข้อมูลเพิ่มเติมของ post (custom fields)
│
├── wp_users              ← Users ทุกคน
├── wp_usermeta           ← ข้อมูลเพิ่มเติมของ user (role, preferences)
│
├── wp_options            ← Settings ทั้งหมดของเว็บ
│
├── wp_terms              ← Categories, Tags, ชื่อ taxonomy
├── wp_term_taxonomy      ← ประเภทของ term (category/tag/custom)
├── wp_term_relationships ← ความสัมพันธ์ post ↔ term
├── wp_termmeta           ← ข้อมูลเพิ่มเติมของ term
│
├── wp_comments           ← Comments ทั้งหมด
├── wp_commentmeta        ← ข้อมูลเพิ่มเติมของ comment
│
└── wp_links              ← Blogroll links (เก่า ไม่ค่อยได้ใช้)
```

---

## 2. wp_posts — หัวใจของ WordPress

เก็บ **Posts, Pages, Custom Post Types, Revisions, Attachments** ทั้งหมด

| Column | Type | คืออะไร |
|--------|------|---------|
| `ID` | bigint PK | ID ของ post |
| `post_author` | bigint | FK → wp_users.ID |
| `post_date` | datetime | วันที่เผยแพร่ (local time) |
| `post_date_gmt` | datetime | วันที่เผยแพร่ (UTC) |
| `post_content` | longtext | เนื้อหาทั้งหมด |
| `post_title` | text | หัวข้อ |
| `post_excerpt` | text | บทสรุปย่อ |
| `post_status` | varchar(20) | `publish`, `draft`, `private`, `trash` |
| `post_name` | varchar(200) | slug ใน URL เช่น `hello-world` |
| `post_type` | varchar(20) | `post`, `page`, `attachment`, หรือ custom |
| `post_parent` | bigint | ID ของ parent post (สำหรับ page ลูก) |
| `menu_order` | int | ลำดับการแสดงผล |
| `comment_count` | bigint | จำนวน comment |

### post_status ที่พบบ่อย

| status | ความหมาย |
|--------|---------|
| `publish` | เผยแพร่แล้ว เห็นได้สาธารณะ |
| `draft` | แบบร่าง ยังไม่เผยแพร่ |
| `private` | เผยแพร่แต่เห็นเฉพาะ admin |
| `trash` | ในถังขยะ |
| `auto-draft` | บันทึกอัตโนมัติ |
| `revision` | ประวัติการแก้ไข |

### ดูข้อมูลจริง

```sql
-- ดู posts ที่ publish แล้ว
SELECT ID, post_title, post_type, post_status, post_date
FROM wp_posts
WHERE post_status = 'publish'
ORDER BY post_date DESC;

-- ดูเฉพาะ pages
SELECT ID, post_title, post_name
FROM wp_posts
WHERE post_type = 'page' AND post_status = 'publish';
```

---

## 3. wp_postmeta — Custom Fields

เก็บข้อมูลเพิ่มเติมของ post ในรูปแบบ **key-value**

| Column | Type | คืออะไร |
|--------|------|---------|
| `meta_id` | bigint PK | ID |
| `post_id` | bigint | FK → wp_posts.ID |
| `meta_key` | varchar(255) | ชื่อ field เช่น `_contact_email` |
| `meta_value` | longtext | ค่าของ field |

### ตัวอย่างจาก Plugin ในคอร์สนี้

```sql
-- ดู meta ของ contact submission
SELECT meta_key, meta_value
FROM wp_postmeta
WHERE post_id = 1
  AND meta_key LIKE '_contact_%';
```

ผลลัพธ์:
```
_contact_name    → สมชาย ใจดี
_contact_email   → somchai@example.com
_contact_topic   → support
_contact_message → ต้องการความช่วยเหลือ...
```

---

## 4. wp_users — ผู้ใช้งาน

| Column | Type | คืออะไร |
|--------|------|---------|
| `ID` | bigint PK | ID ของ user |
| `user_login` | varchar(60) | username สำหรับ login |
| `user_pass` | varchar(255) | password (hashed ด้วย bcrypt) |
| `user_email` | varchar(100) | อีเมล (unique) |
| `user_registered` | datetime | วันที่สมัคร |
| `display_name` | varchar(250) | ชื่อที่แสดงบนเว็บ |

> ❌ **password เก็บเป็น hash** — ไม่สามารถอ่านได้โดยตรง

```sql
-- ดู users ทั้งหมด (ไม่แสดง password)
SELECT ID, user_login, user_email, user_registered, display_name
FROM wp_users;
```

---

## 5. wp_usermeta — ข้อมูลเพิ่มเติม User

| Column | Type | คืออะไร |
|--------|------|---------|
| `umeta_id` | bigint PK | ID |
| `user_id` | bigint | FK → wp_users.ID |
| `meta_key` | varchar(255) | ชื่อ key |
| `meta_value` | longtext | ค่า |

### Meta Keys ที่สำคัญ

| meta_key | เก็บอะไร |
|---------|---------|
| `wp_capabilities` | Role เช่น `{"administrator":true}` |
| `wp_user_level` | Level ของ user (เก่า) |
| `session_tokens` | Login sessions |

```sql
-- ดู role ของ users ทั้งหมด
SELECT u.user_login, m.meta_value AS role
FROM wp_users u
JOIN wp_usermeta m ON u.ID = m.user_id
WHERE m.meta_key = 'wp_capabilities';
```

---

## 6. wp_options — Settings ทั้งหมด

| Column | Type | คืออะไร |
|--------|------|---------|
| `option_id` | bigint PK | ID |
| `option_name` | varchar(191) | ชื่อ setting (unique) |
| `option_value` | longtext | ค่า |
| `autoload` | varchar(20) | `yes` = โหลดทุกครั้ง, `no` = โหลดเมื่อต้องการ |

### Options ที่สำคัญ

```sql
SELECT option_name, option_value
FROM wp_options
WHERE option_name IN (
  'siteurl',          -- URL ของเว็บ
  'home',             -- Home URL
  'blogname',         -- ชื่อเว็บ
  'blogdescription',  -- คำอธิบาย
  'admin_email',      -- อีเมล admin
  'active_plugins',   -- plugins ที่เปิดอยู่
  'template',         -- theme ที่ใช้
  'permalink_structure' -- รูปแบบ URL
);
```

---

## 7. wp_terms, wp_term_taxonomy, wp_term_relationships

สามตารางนี้ทำงานร่วมกันเพื่อจัดการ **Categories, Tags, และ Custom Taxonomies**

```
wp_terms
  term_id | name        | slug
  ------  | ----------- | -------
  1       | Technology  | technology
  2       | WordPress   | wordpress

wp_term_taxonomy
  term_taxonomy_id | term_id | taxonomy   | count
  ---------------- | ------- | ---------- | -----
  1                | 1       | category   | 5
  2                | 2       | post_tag   | 3

wp_term_relationships
  object_id (post_id) | term_taxonomy_id
  ------------------- | ----------------
  10 (post ID)        | 1 (Technology category)
  10 (post ID)        | 2 (WordPress tag)
```

```sql
-- ดู categories ทั้งหมด
SELECT t.name, t.slug, tt.count
FROM wp_terms t
JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
WHERE tt.taxonomy = 'category';

-- ดูว่า post ID 10 อยู่ใน category อะไร
SELECT t.name
FROM wp_terms t
JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
JOIN wp_term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
WHERE tr.object_id = 10 AND tt.taxonomy = 'category';
```

---

## 8. wp_comments — Comments

| Column | Type | คืออะไร |
|--------|------|---------|
| `comment_ID` | bigint PK | ID |
| `comment_post_ID` | bigint | FK → wp_posts.ID |
| `comment_author` | tinytext | ชื่อผู้ comment |
| `comment_author_email` | varchar(100) | อีเมล |
| `comment_author_IP` | varchar(100) | IP Address |
| `comment_content` | text | เนื้อหา comment |
| `comment_approved` | varchar(20) | `1`=อนุมัติ, `0`=รอ, `spam` |
| `comment_parent` | bigint | reply ต่อ comment ID ไหน |

---

## 9. ความสัมพันธ์ระหว่าง Tables

```
wp_users ──────────────────────── wp_usermeta
  │ (ID)                            (user_id)
  │
  └──▶ wp_posts ─────────────────── wp_postmeta
         │ (ID)                       (post_id)
         │
         ├──▶ wp_comments ────────── wp_commentmeta
         │      (comment_post_ID)      (comment_id)
         │
         └──▶ wp_term_relationships
                (object_id)
                    │
                    └──▶ wp_term_taxonomy
                               │
                               └──▶ wp_terms
```

---

## 10. เข้าดูผ่าน phpMyAdmin

เปิดที่ `http://localhost:8089` แล้ว:

1. คลิก **wordpress** database ซ้ายมือ
2. คลิกชื่อ table ที่ต้องการ
3. คลิก **Browse** — ดูข้อมูล
4. คลิก **SQL** — รัน query เอง
5. คลิก **Structure** — ดู schema

---

## 11. สรุป

| Table | เก็บอะไร | ใช้บ่อยแค่ไหน |
|-------|---------|-------------|
| `wp_posts` | Posts, Pages, Media | ⭐⭐⭐ |
| `wp_postmeta` | Custom fields | ⭐⭐⭐ |
| `wp_options` | Settings ทั้งหมด | ⭐⭐⭐ |
| `wp_users` | Users | ⭐⭐ |
| `wp_usermeta` | User roles/data | ⭐⭐ |
| `wp_terms` | Categories/Tags | ⭐⭐ |
| `wp_term_taxonomy` | ประเภท taxonomy | ⭐⭐ |
| `wp_term_relationships` | post ↔ category | ⭐⭐ |
| `wp_comments` | Comments | ⭐ |
| `wp_commentmeta` | Comment data | ⭐ |
| `wp_termmeta` | Term extra data | ⭐ |
| `wp_links` | Blogroll (เก่า) | — |
