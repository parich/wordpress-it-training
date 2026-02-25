# WordPress Discussion Settings — ตั้งค่าให้ปลอดภัย ลดสแปม ควบคุมคุณภาพ

**Settings → Discussion**

---

## กรณี: ไม่ต้องการ Comment และไม่ต้องการให้ลงทะเบียน (เว็บองค์กร / เว็บข้อมูล)

เหมาะกับเว็บที่ต้องการแสดงข้อมูลอย่างเดียว ไม่มี community

### ขั้นที่ 1 — ปิด Comment และ Registration (Settings)

**Settings → General:**

```
☐ Anyone can register    ← ปิด (ไม่ให้ลงทะเบียน)
```

**Settings → Discussion:**

```
☐ Attempt to notify blogs linked to the post
☐ Allow link notifications from other blogs (pingbacks & trackbacks)
☐ Allow people to submit comments on new posts
```

### ขั้นที่ 2 — ปิด Comment บทความเก่าทั้งหมด (SQL)

Settings ด้านบนป้องกันเฉพาะบทความ **ใหม่** บทความเก่าต้องปิดด้วย SQL:

**phpMyAdmin → เลือก Database → แถบ SQL → วางคำสั่งนี้:**

```sql
-- ปิด comment ทุก post และ page ที่มีอยู่แล้ว
UPDATE wp_posts
SET comment_status = 'closed',
    ping_status    = 'closed'
WHERE post_status = 'publish'
  AND post_type IN ('post', 'page');
```

คลิก **Go** → เสร็จสิ้น

### ขั้นที่ 3 — ตรวจสอบว่าปิดแล้ว

**phpMyAdmin → SQL:**

```sql
-- ตรวจว่ายังมี post ที่เปิด comment อยู่ไหม
SELECT ID, post_title, comment_status, ping_status
FROM wp_posts
WHERE post_status  = 'publish'
  AND post_type   IN ('post', 'page')
  AND (comment_status = 'open' OR ping_status = 'open');
```

ถ้าไม่มีผลลัพธ์ = ปิดครบแล้ว

### ผลที่ได้

```
ผู้เยี่ยมชม → เข้าเว็บ
               ↓
    ไม่มีช่อง Comment บนหน้าใด ๆ
    ไม่มีปุ่ม Register
    Spambot ส่ง comment ไม่ได้
    Pingback ไม่ทำงาน
```

---

## ทำไมต้องตั้งค่า Discussion

Comment section ที่ไม่ได้ตั้งค่า คือช่องทางที่ Spambot ใช้บ่อยที่สุด:

```
ปัญหาที่เกิดขึ้นถ้าไม่ตั้งค่า
├── Spam comment (ลิงก์ขาย, โฆษณา, ยา, การพนัน)
├── ลิงก์อันตราย (Phishing, Malware)
├── Comment ไม่เหมาะสม (offensive content)
├── SEO เสีย (Google เห็นลิงก์เสียบน page ของเรา)
└── Server load สูง (Spam bot ยิง request ต่อเนื่อง)
```

---

## ภาพรวม Discussion Settings ทั้งหมด

```
Settings → Discussion
│
├── Default post settings
│   ├── Attempt to notify blogs linked from the post
│   ├── Allow link notifications from other blogs (pingbacks & trackbacks)
│   └── Allow people to submit comments on new posts
│
├── Other comment settings
│   ├── Comment author must fill out name and email
│   ├── Users must be registered and logged in to comment
│   ├── Automatically close comments on posts older than X days
│   ├── Enable threaded (nested) comments X levels deep
│   ├── Break comments into pages with X top level comments per page
│   └── Comments should be displayed with older/newer comments at top
│
├── Email me whenever
│   ├── Anyone posts a comment
│   └── A comment is held for moderation
│
├── Before a comment appears
│   ├── Comment must be manually approved
│   └── Comment author must have a previously approved comment
│
├── Comment Moderation
│   └── Hold a comment in queue if it contains X or more links
│       [Keyword list]
│
└── Disallowed Comment Keys
    [Keyword / IP / Email / URL blacklist]
```

---

## การตั้งค่าแนะนำ (ทีละหัวข้อ)

### 1. Default post settings

```
☐ Attempt to notify blogs linked to the post    ← ปิด (ลด outbound request)
☐ Allow link notifications from other blogs      ← ปิด (ปิด Pingback/Trackback)
☑ Allow people to submit comments on new posts  ← เปิดถ้าต้องการ Comment
```

> **Pingbacks & Trackbacks คืออะไร?**
> เมื่อเว็บอื่น link มาหาเรา WordPress จะส่ง/รับการแจ้งเตือน
> Spambot ใช้ช่องทางนี้ยิง fake pingback เข้ามาจำนวนมาก
> **แนะนำ: ปิดทั้งสองข้อ**

---

### 2. Other comment settings

```
☑ Comment author must fill out name and email
   ← บังคับใส่ชื่อ + อีเมล ลด anonymous spam

☐ Users must be registered and logged in to comment
   ← เปิดถ้าต้องการให้เฉพาะสมาชิก comment ได้
   ← ปิดถ้าอยากให้ทุกคน comment ได้ (แต่ต้องใช้ moderation)

☑ Automatically close comments on posts older than  90  days
   ← ปิด comment บทความเก่าอัตโนมัติ
   ← บทความเก่ามักถูก spam มากกว่าบทความใหม่
```

---

### 3. Email notifications

```
☑ Anyone posts a comment        ← แจ้งเตือน Admin ทุกครั้ง (ถ้า volume น้อย)
☑ A comment is held for moderation  ← แจ้งเตือนเมื่อมี comment รออนุมัติ
```

> ถ้าเว็บมี traffic สูง แจ้งเตือนทุก comment อาจ inbox ล้น
> แนะนำเปิดเฉพาะ "held for moderation"

---

### 4. Before a comment appears ← สำคัญที่สุด

```
☑ Comment must be manually approved
   ← comment ทุกอันต้องผ่าน Admin อนุมัติก่อนแสดง
   ← ป้องกัน spam และลิงก์อันตรายแสดงบนหน้าเว็บ

☑ Comment author must have a previously approved comment
   ← คนที่เคยได้รับการอนุมัติแล้ว comment ใหม่จะผ่านอัตโนมัติ
   ← ลดภาระ moderation สำหรับ regular commenter
```

**ผลที่ได้:**

```
Comment ใหม่จาก user ที่ไม่รู้จัก
    ↓
[รอ Moderation Queue]
    ↓
Admin ตรวจสอบ → Approve / Spam / Trash
    ↓
แสดงบนหน้าเว็บ (เฉพาะที่ Approve)
```

---

### 5. Comment Moderation — กรองด้วย Keywords

```
Hold a comment in the moderation queue if it contains  2  or more links

Words in the moderation queue:
[พื้นที่ใส่ keyword ทีละบรรทัด]
```

**แนะนำตั้งค่า links = 1 หรือ 2:**
- `1` = comment ที่มีลิงก์ใด ๆ ต้องรอ approve
- `2` = ยืดหยุ่นกว่า อนุญาต 1 ลิงก์ผ่านได้

**ตัวอย่าง keyword ที่ควรเพิ่มใน moderation queue:**

```
casino
poker
viagra
cialis
loan
forex
crypto
buy now
click here
```

---

### 6. Disallowed Comment Keys — บล็อกถาวร

Comment ที่มี keyword, IP, อีเมล หรือ URL ในรายการนี้ จะถูก **ทิ้งทันที** ไม่เข้า queue

```
[กรอกทีละบรรทัด — keyword / IP / email / URL]
```

**ตัวอย่าง:**

```
gambling
adult
xxx
192.168.1.100       ← IP ที่รู้ว่าเป็น spammer
spammer@evil.com    ← อีเมล spammer
spam-site.com       ← URL เว็บ spam
```

**ความแตกต่าง Moderation vs Disallowed:**

| | Moderation | Disallowed |
|---|---|---|
| ผลลัพธ์ | รอ Admin อนุมัติ | ทิ้งทันที ไม่เข้า queue |
| ใช้กับ | คำต้องสงสัย | คำที่รู้แน่ว่า spam |
| กู้คืนได้ | ได้ (ยังอยู่ใน queue) | ไม่ได้ (ถูกลบทิ้ง) |

---

## สรุป: การตั้งค่าที่แนะนำสำหรับเว็บองค์กร / มหาวิทยาลัย

```
Default post settings
  ☐ Pingbacks & Trackbacks        ← ปิด
  ☑ Allow comments                ← เปิด (ถ้าต้องการ) / ☐ ปิดถ้าไม่ต้องการ

Other comment settings
  ☑ Require name and email        ← เปิด
  ☐ Registered users only         ← ขึ้นกับนโยบาย
  ☑ Close after 90 days           ← เปิด

Email
  ☑ Held for moderation           ← เปิด

Before a comment appears
  ☑ Must be manually approved     ← เปิด
  ☑ Previously approved author    ← เปิด

Comment Moderation
  Links ≥ 1 → hold for moderation ← ตั้งเป็น 1 หรือ 2

Disallowed Keys
  [ใส่ keyword spam ที่รู้จัก]
```

---

## ปิด Comment ทั้งเว็บ (ถ้าไม่ต้องการเลย)

ถ้าเว็บไม่ต้องการ Comment เลย วิธีที่ครอบคลุมที่สุด:

**ขั้นที่ 1 — Settings → Discussion:**

```
☐ Allow people to submit comments on new posts   ← uncheck
```

**ขั้นที่ 2 — ปิด Comment บทความเก่าทั้งหมด (WP-CLI):**

```bash
# ปิด comment ทุก post ที่มีอยู่แล้ว
wp db query "UPDATE wp_posts SET comment_status='closed' WHERE post_type IN ('post','page');"

# หรือทีละ post
wp post list --format=ids | xargs -d ' ' -I {} wp post update {} --comment_status=closed
```

**ขั้นที่ 3 — ปิด Pingback ระดับ functions.php (theme):**

```php
// เพิ่มใน functions.php ของ theme
add_filter('xmlrpc_methods', function($methods) {
    unset($methods['pingback.ping']);
    return $methods;
});
```

---

## Plugin ที่ช่วยลดสแปม

| Plugin | หน้าที่ |
|--------|---------|
| **Akismet Anti-Spam** | ตรวจ spam comment ด้วย AI (มาติดมากับ WordPress) |
| **WP Cerber Security** | ป้องกัน spam + brute force + malware |
| **Antispam Bee** | ฟรี ไม่ต้องใช้ API key |
| **reCAPTCHA** | Google CAPTCHA บนฟอร์ม comment |

**เปิดใช้ Akismet (มีอยู่แล้ว):**

1. Plugins → Akismet Anti-Spam → **Activate**
2. คลิก **Set up your Akismet account**
3. สร้าง API Key ฟรีที่ akismet.com (เว็บไม่ใช่เชิงพาณิชย์)
4. ใส่ API Key → **Connect**

---

## ดูเพิ่มเติม

- [WordPress_General_Settings.md](WordPress_General_Settings.md#9-discussion-settings-disable-comments) — ภาพรวม Settings
- [WordPress_Basic_Usage.md](WordPress_Basic_Usage.md#6-settings-การตั้งค่า) — Settings เบื้องต้น
- [WordPress_Training_IT_2Days.md](WordPress_Training_IT_2Days.md#6-security--best-practices) — Security Best Practices
