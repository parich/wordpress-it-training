# Analyze Other Websites

[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

> **"ก่อนสร้างเว็บ — เรียนรู้จากเว็บที่มีอยู่แล้ว เครื่องมือช่วยให้รู้ว่าเขาใช้อะไร"**

---

## 1. Browser Extensions (แนะนำติดตั้ง)

### Wappalyzer

- **ดาวน์โหลด:** [Chrome](https://chrome.google.com/webstore/detail/wappalyzer) / [Firefox](https://addons.mozilla.org/en-US/firefox/addon/wappalyzer/)
- บอก technology stack ของเว็บ — CMS, Framework, Server, Analytics, CDN
- คลิกไอคอนที่ toolbar → เห็นผลทันที

**สิ่งที่ Wappalyzer บอกได้:**

| หมวด | ตัวอย่างที่เห็น |
| ---- | -------------- |
| CMS | WordPress 6.4, Shopify, Wix |
| Web Server | Apache, Nginx, Cloudflare |
| JavaScript | jQuery, React, Vue.js |
| Analytics | Google Analytics, Facebook Pixel |
| CDN | Cloudflare, jsDelivr, AWS CloudFront |
| Email | Mailchimp, SendGrid |
| Hosting | AWS, DigitalOcean, WP Engine |

**วิธีใช้:**
1. ไปที่เว็บที่ต้องการวิเคราะห์
2. คลิกไอคอน Wappalyzer บน toolbar
3. อ่านรายการ technology ที่ตรวจพบ

---

### WP Detector

- **ดาวน์โหลด:** [Chrome Web Store — WP Detector](https://chrome.google.com/webstore/search/wp%20detector)
- เน้นเฉพาะ WordPress — ตรวจ theme, plugins, WordPress version
- แม่นยำกว่า Wappalyzer สำหรับ WordPress โดยเฉพาะ

**สิ่งที่ WP Detector บอกได้:**
- ชื่อ **Theme** + version + ผู้พัฒนา
- รายการ **Plugins** ที่ตรวจพบ (จาก source code)
- **WordPress version** (ถ้าไม่ถูกซ่อน)
- Child theme หรือ Parent theme

---

## 2. View Page Source

กด `Ctrl + U` (Windows) หรือ `Cmd + Option + U` (Mac)

### หา WordPress signature

```html
<!-- ค้นหา wp-content ใน source -->
<link rel='stylesheet' href='https://example.com/wp-content/themes/astra/...' />

<!-- หา generator tag -->
<meta name="generator" content="WordPress 6.4.2" />

<!-- หา theme ใน link tag -->
href="/wp-content/themes/THEME-NAME/style.css"

<!-- หา plugins -->
src="/wp-content/plugins/PLUGIN-NAME/..."
```

**Keyboard shortcut ค้นหา:**
- กด `Ctrl + F` → พิมพ์ `wp-content` → เห็นทุก asset ที่โหลด

---

## 3. whatwpthemeisthat.com

1. ไปที่ [whatwpthemeisthat.com](https://www.whatwpthemeisthat.com)
2. วาง URL ของเว็บที่ต้องการตรวจ
3. กด **Analyze**

**ผลที่ได้:**
- ชื่อ theme + ลิงก์ดาวน์โหลด
- Child theme (ถ้ามี)
- Plugin บางส่วนที่ตรวจพบ

---

## 4. builtwith.com

1. ไปที่ [builtwith.com](https://builtwith.com)
2. พิมพ์ domain ที่ต้องการ
3. ดู Technology Profile

**ละเอียดกว่าเครื่องมืออื่น:**
- ประวัติ technology ที่เคยใช้
- Hosting provider
- SSL certificate provider
- Advertising networks
- Email service provider

---

## 5. Chrome DevTools — Network Tab

1. กด `F12` → คลิก **Network**
2. Reload หน้า (`F5`)
3. ดู files ที่โหลด

### หา theme/plugin จาก URL

```
wp-content/themes/THEME-NAME/     ← ชื่อ theme
wp-content/plugins/PLUGIN-NAME/   ← ชื่อ plugin
```

### Filter ให้ง่ายขึ้น

- คลิก **JS** → ดูเฉพาะ JavaScript files
- คลิก **CSS** → ดูเฉพาะ stylesheet
- ช่อง Filter พิมพ์ `wp-content` → กรองเฉพาะ WP files

---

## 6. เปรียบเทียบเครื่องมือ

| เครื่องมือ | ใช้งาน | เห็น Theme | เห็น Plugins | ข้อดี |
| --------- | ------ | ---------- | ------------ | ----- |
| **Wappalyzer** | Extension | ✅ | บางส่วน | เร็ว ครอบคลุม technology อื่นด้วย |
| **WP Detector** | Extension | ✅ | ✅ | เน้น WP โดยเฉพาะ แม่นกว่า |
| **View Source** | Browser | ✅ | ✅ | ไม่ต้องติดตั้ง แต่ต้องอ่านเอง |
| **whatwpthemeisthat** | เว็บไซต์ | ✅ | บางส่วน | ง่าย เห็นลิงก์ดาวน์โหลด theme |
| **builtwith** | เว็บไซต์ | ✅ | ✅ | ละเอียดมาก มีประวัติ |
| **DevTools Network** | Browser | ✅ | ✅ | แม่นที่สุด เห็น request จริง |

---

## 7. ข้อจำกัด

- บางเว็บ **ซ่อน WordPress version** ด้วยเหตุผลด้านความปลอดภัย
- บางเว็บ **เปลี่ยนชื่อ `wp-content`** ทำให้ตรวจยากขึ้น
- Plugin ที่โหลดแบบ **inline** หรือ concatenate แล้ว → ตรวจยากกว่า
- เครื่องมือทุกตัว **ไม่ 100% แม่นยำ** — ใช้หลายตัวประกอบกัน

---

## สรุป Workflow

```
เปิดเว็บที่ต้องการวิเคราะห์
        │
        ├── 1. ดู Wappalyzer icon → technology stack ภาพรวม
        ├── 2. ดู WP Detector → theme + plugins (เฉพาะ WP)
        ├── 3. Ctrl+U → ค้นหา wp-content → ชื่อ theme/plugin จริง
        └── 4. F12 Network → ดู assets ที่โหลดจริงๆ
```
