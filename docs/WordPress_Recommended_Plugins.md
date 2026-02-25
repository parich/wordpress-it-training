# WordPress Plugins แนะนำ แยกตามหมวด

> ติดตั้งเฉพาะที่ **จำเป็นจริง ๆ** — Plugin มากเกินไปทำให้เว็บช้าและเสี่ยงด้านความปลอดภัย

---

## 1. SEO

| Plugin | ฟรี/จ่าย | แนะนำ |
|--------|---------|-------|
| **Yoast SEO** | ฟรี (มี Pro) | ยอดนิยมที่สุด ครอบคลุมทุกด้าน |
| **Rank Math** | ฟรี (มี Pro) | ฟีเจอร์เยอะกว่า Yoast ในเวอร์ชันฟรี |
| **The SEO Framework** | ฟรี | เบา ไม่มี upsell |

**ฟีเจอร์หลัก:** Meta title/description, Sitemap XML, Open Graph, Schema markup

> เลือกใช้แค่ **1 ตัว** เท่านั้น ไม่ควรใช้พร้อมกัน

---

## 2. ความปลอดภัย (Security)

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Wordfence Security** | ฟรี (มี Pro) | Firewall + Malware scan + Login protection |
| **WP Cerber Security** | ฟรี (มี Pro) | Brute force + Spam + Anti-bot |
| **iThemes Security** | ฟรี (มี Pro) | Hardening + 2FA + File change detection |
| **All In One WP Security** | ฟรี | ครอบคลุม เหมาะมือใหม่ |

**สิ่งที่ควรได้จาก Security Plugin:**
```
✅ จำกัดครั้งที่ Login ผิด (Brute Force)
✅ แจ้งเตือนเมื่อมีการ Login ผิดปกติ
✅ ซ่อน wp-admin / wp-login.php
✅ สแกน Malware
✅ Firewall กรอง request อันตราย
```

---

## 3. Backup

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **UpdraftPlus** | ฟรี (มี Pro) | Backup ไปที่ Google Drive, Dropbox, S3 |
| **All-in-One WP Migration** | ฟรี (จำกัด 512MB) | ย้ายเว็บง่ายมาก ไฟล์เดียว |
| **BackWPup** | ฟรี | Schedule backup, หลาย destination |
| **Duplicator** | ฟรี (มี Pro) | Clone / Migrate เว็บ |

**ตั้งค่า UpdraftPlus แนะนำ:**
```
Backup files every:    Weekly
Backup DB every:       Daily
Retain:                4 copies
Remote storage:        Google Drive / Dropbox
```

---

## 4. Cache & Performance

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **WP Super Cache** | ฟรี | เบา ใช้ง่าย (จาก Automattic) |
| **W3 Total Cache** | ฟรี (มี Pro) | ปรับแต่งละเอียดที่สุด |
| **LiteSpeed Cache** | ฟรี | เร็วมาก ถ้าใช้ LiteSpeed Server |
| **WP Rocket** | จ่าย $59/ปี | ดีที่สุด ตั้งค่าง่าย |
| **Autoptimize** | ฟรี | Minify CSS/JS/HTML |

> ถ้าใช้ **CyberPanel** (OpenLiteSpeed) ให้ใช้ **LiteSpeed Cache** ได้เลย ฟรีและเร็วมาก

---

## 5. รูปภาพ (Image Optimization)

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Smush** | ฟรี (มี Pro) | Compress รูปอัตโนมัติตอนอัปโหลด |
| **ShortPixel** | ฟรี 100 รูป/เดือน | คุณภาพดี รองรับ WebP |
| **Imagify** | ฟรี 25MB/เดือน | จาก WP Rocket team |
| **WebP Express** | ฟรี | แปลงรูปเป็น WebP อัตโนมัติ |

---

## 6. Contact Form (ฟอร์มติดต่อ)

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Contact Form 7** | ฟรี | เบา ยืดหยุ่น ยอดนิยมมากที่สุด |
| **WPForms** | ฟรี (มี Pro) | Drag & Drop ใช้ง่าย |
| **Ninja Forms** | ฟรี (มี Pro) | Builder สวย |
| **Gravity Forms** | จ่าย $59/ปี | ฟีเจอร์สูง conditional logic |

---

## 7. Anti-Spam

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Akismet Anti-Spam** | ฟรี (ไม่เชิงพาณิชย์) | ติดมากับ WordPress ใช้ AI ตรวจ spam |
| **Antispam Bee** | ฟรี | ไม่ต้องใช้ API key เลย |
| **CleanTalk** | จ่าย $8/ปี | ไม่มี CAPTCHA แต่กรอง spam ดีมาก |

---

## 8. Multilingual (หลายภาษา)

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Polylang** | ฟรี (มี Pro) | ยืดหยุ่น เบา |
| **WPML** | จ่าย $39/ปี | มาตรฐานอุตสาหกรรม ครบที่สุด |
| **TranslatePress** | ฟรี (มี Pro) | แปลผ่าน Frontend เห็นผลทันที |
| **GTranslate** | ฟรี (มี Pro) | ใช้ Google Translate แปลอัตโนมัติ เพิ่มปุ่มเลือกภาษาได้ทันที |

> **GTranslate vs Polylang/WPML**
>
> - **GTranslate** — แปลโดย Google Translate อัตโนมัติ ตั้งค่าง่าย แต่คุณภาพขึ้นกับ AI
> - **Polylang / WPML** — แปลด้วยมือ คุณภาพสูงกว่า แต่ต้องใส่เนื้อหาเองทุกภาษา

---

## 9. Social Media & Sharing

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Social Snap** | ฟรี (มี Pro) | ปุ่ม Share สวย |
| **Monarch** (Elegant Themes) | จ่าย | สวยมาก |
| **AddToAny** | ฟรี | เบา รองรับหลาย platform |
| **Chaty** | ฟรี (มี Pro) | ปุ่มลอย (Floating Widget) รวม Chat ทุก platform ไว้ปุ่มเดียว |

**Chaty รองรับ Channel:**

```text
WhatsApp · Facebook Messenger · LINE · Telegram
Instagram DM · TikTok · Email · Phone Call
Viber · WeChat · Slack · Discord · ...
```

**การทำงาน:**

```text
ผู้เยี่ยมชมเห็นปุ่มลอยมุมขวาล่าง
         ↓
     คลิกเปิด
         ↓
  เลือก Channel ที่ต้องการ
  (WhatsApp / LINE / Messenger)
         ↓
  เปิด Chat กับเจ้าของเว็บโดยตรง
```

> เหมาะสำหรับเว็บที่ต้องการให้ผู้เยี่ยมชมติดต่อได้ทันทีโดยไม่ต้องกรอกฟอร์ม

---

## 9.1 Auto Post to Social Media (โพสต์อัตโนมัติไปยัง Social)

เมื่อ **Publish บทความใหม่** หรือตามตารางเวลา → ส่งไปยัง Facebook, X (Twitter), Instagram ฯลฯ อัตโนมัติ

| Plugin | ฟรี/จ่าย | รองรับ | เด่น |
| --- | --- | --- | --- |
| **Jetpack Social** | ฟรี (30 โพสต์/เดือน) | Facebook, X, LinkedIn, Tumblr | กด Publish ส่งอัตโนมัติทันที |
| **Blog2Social** | ฟรี (มี Pro) | Facebook, X, Instagram, LinkedIn, Pinterest | Schedule โพสต์ + ปรับข้อความแต่ละ platform |
| **FS Poster** | จ่าย $45 | Facebook, X, Instagram, TikTok, Telegram, YouTube | ครบที่สุด auto-post + scheduler |
| **Revive Old Posts** | ฟรี (มี Pro) | Facebook, X, LinkedIn | ดึงบทความเก่ามาโพสต์ใหม่ตามตาราง |
| **WP Social Ninja** | ฟรี (มี Pro) | Facebook, X, Instagram, YouTube | Social Feed + Auto Post |

**การทำงาน:**

```text
WordPress Post → Publish
       ↓
  Auto Post Plugin
       ↓
  ┌────┬────┬──────────┬──────────┐
  FB   X   Instagram  LinkedIn  ...
```

**ข้อจำกัดของแต่ละ Platform:**

| Platform | ข้อจำกัด |
|----------|---------|
| **Facebook Page** | ต้องเชื่อม Facebook App (ฟรี) |
| **X (Twitter)** | ต้องใช้ Developer Account (ฟรี, มี rate limit) |
| **Instagram** | ต้องเป็น **Business/Creator Account** + เชื่อมผ่าน Facebook |
| **TikTok** | ต้องเป็น Business Account |
| **LINE Official** | ไม่รองรับ auto-post โดยตรง ต้องใช้ LINE Notify API เอง |

> **Instagram สำคัญ:** Instagram ไม่อนุญาตให้ post รูปภาพอัตโนมัติแบบ Personal Account
> ต้องอัปเกรดเป็น **Instagram Business Account** และเชื่อมกับ Facebook Page ก่อน

**แนะนำสำหรับเว็บมหาวิทยาลัย:**

```text
ฟรี:  Jetpack Social    ← ง่ายสุด เหมาะเริ่มต้น
Pro:  Blog2Social Pro   ← ถ้าต้องการ schedule + Instagram
```

---

## 10. Page Builder

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Elementor** | ฟรี (มี Pro $59/ปี) | ยอดนิยมที่สุด Drag & Drop |
| **Beaver Builder** | จ่าย $99/ปี | เสถียร โค้ดสะอาด |
| **Divi** | จ่าย $89/ปี | All-in-one Theme + Builder |
| **Gutenberg (built-in)** | ฟรี | Block Editor ในตัว WordPress |

> **แนะนำ:** ลองใช้ **Gutenberg** ก่อน ถ้าไม่พอค่อยติดตั้ง Elementor

### Elementor Add-ons (ต้องติดตั้ง Elementor ก่อน)

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Dynific Addons for Elementor** | ฟรี (มี Pro) | Widget เพิ่มเติม เช่น Timeline, Advanced Tabs, Tooltip |
| **Happy Addons for Elementor** | ฟรี (มี Pro) | Widget สวย 400+ พร้อม Happy Effects และ Floating Effects |
| **AnWP Post Grid & Post Carousel Slider for Elementor** | ฟรี | แสดง Posts เป็น Grid / Carousel พร้อม filter Category |

**การใช้งาน AnWP Post Grid — เหมาะสำหรับ:**

```text
หน้าข่าวสาร  → แสดง Posts แบบ Grid กรองตาม Category
หน้าแรก      → Carousel แสดงบทความล่าสุดแบบสไลด์
```

---

## 11. Analytics & Tracking

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **Site Kit by Google** | ฟรี | รวม GA4 + Search Console + PageSpeed ในที่เดียว |
| **MonsterInsights** | ฟรี (มี Pro) | แสดง GA4 ใน Dashboard |
| **Insert Headers and Footers** | ฟรี | ใส่ script GA / GTM โดยไม่แก้ theme |

---

## 12. Privacy & Cookie (GDPR / PDPA)

| Plugin | ฟรี/จ่าย | เด่น |
| --- | --- | --- |
| **Cookie Notice & Compliance for GDPR / CCPA** | ฟรี (มี Pro) | แถบแจ้งเตือน Cookie ปรับแต่งได้ รองรับ GDPR, CCPA, PDPA |

**ฟีเจอร์หลัก:**

```text
✅ แถบแจ้งเตือว Cookie (Cookie Banner) ด้านล่างหน้าจอ
✅ ปุ่ม Accept / Reject / Customize
✅ บล็อก Script (GA, GTM) จนกว่าผู้ใช้จะ Accept
✅ บันทึก Consent Log
✅ หน้า Privacy Policy อัตโนมัติ
```

**ตั้งค่าที่แนะนำ:**

```text
Settings → Cookie Notice
  Position:       Bottom        ← แถบล่างหน้าจอ
  Message:        เว็บไซต์นี้ใช้คุกกี้เพื่อพัฒนาประสบการณ์การใช้งาน
  Accept button:  ยอมรับ
  Refuse button:  ปฏิเสธ  (เปิดให้แสดงปุ่มนี้ด้วย)
  Privacy policy: ลิงก์ไปหน้า /privacy-policy
```

> **กฎหมายที่เกี่ยวข้องในไทย:** พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล (PDPA) พ.ศ. 2562
> เว็บที่เก็บข้อมูลผู้ใช้หรือใช้ Tracking cookie ต้องขอ Consent ก่อน

---

## 13. Membership & User Management

| Plugin | ฟรี/จ่าย | เด่น |
|--------|---------|------|
| **MemberPress** | จ่าย $179/ปี | ครบที่สุด |
| **Paid Memberships Pro** | ฟรี (มี Pro) | ยืดหยุ่น |
| **BuddyPress** | ฟรี | Social network บน WordPress |

---

## สรุป: Plugin ขั้นต่ำที่ควรมีสำหรับเว็บมหาวิทยาลัย

```
ต้องมี (Must Have)
├── Yoast SEO หรือ Rank Math        ← SEO
├── Wordfence หรือ WP Cerber        ← Security
├── UpdraftPlus                     ← Backup
└── Akismet หรือ Antispam Bee       ← Anti-Spam

ควรมี (Should Have)
├── WP Super Cache หรือ LiteSpeed Cache  ← Performance
├── Smush หรือ ShortPixel                ← Image Optimization
└── Site Kit by Google                   ← Analytics

ไม่บังคับ (Optional)
├── Contact Form 7     ← ถ้าต้องการฟอร์ม
├── Polylang           ← ถ้าต้องการหลายภาษา
└── Elementor          ← ถ้า Gutenberg ไม่พอ
```

---

## ดูเพิ่มเติม

- [WordPress_Basic_Usage.md](WordPress_Basic_Usage.md#4-plugins-ปลั๊กอิน) — วิธีติดตั้ง Plugin
- [WordPress_Discussion_Settings.md](WordPress_Discussion_Settings.md) — ลดสแปม Comment
- [WordPress_Backup_Guide.md](WordPress_Backup_Guide.md) — Backup ด้วย UpdraftPlus
