# HTTP / HTTPS

> **"HTTP คือภาษาที่ Browser และ Server ใช้คุยกัน — HTTPS คือเวอร์ชันที่เข้ารหัสแล้ว"**

---

## 1. HTTP คืออะไร

**HTTP** = HyperText Transfer Protocol
- โปรโตคอลที่กำหนดรูปแบบการส่งข้อมูลระหว่าง Client กับ Server
- ทำงานบน **Port 80**
- ข้อมูลส่งแบบ **plain text** — ใครดักอ่านได้เลย

---

## 2. HTTPS คืออะไร

**HTTPS** = HTTP + **S**ecure (TLS/SSL Encryption)
- ทำงานบน **Port 443**
- ข้อมูลถูก **เข้ารหัส** ก่อนส่ง — ดักอ่านไม่ออก
- ต้องมี **SSL Certificate** ติดตั้งบน Server

```
HTTP  →  ส่งข้อมูลแบบเปิดเผย  →  อันตราย ❌
HTTPS →  ส่งข้อมูลแบบเข้ารหัส →  ปลอดภัย ✅
```

---

## 3. HTTP vs HTTPS เปรียบเทียบ

| หัวข้อ | HTTP | HTTPS |
|--------|------|-------|
| Port | 80 | 443 |
| การเข้ารหัส | ไม่มี | TLS/SSL |
| URL | `http://` | `https://` |
| SSL Certificate | ไม่ต้องการ | จำเป็น |
| Browser แสดง | ⚠️ Not Secure | 🔒 Secure |
| SEO | ด้อยกว่า | Google ให้คะแนนสูงกว่า |
| ใช้ใน Production | ❌ ไม่ควร | ✅ ต้องใช้ |

---

## 4. TLS/SSL ทำงานอย่างไร

```
Client                          Server
  │                               │
  │── 1. Hello (ขอเชื่อมต่อ) ────▶│
  │                               │
  │◀── 2. ส่ง SSL Certificate ────│
  │                               │
  │── 3. ตรวจสอบ Certificate ─────│
  │    (ออกโดย CA ที่น่าเชื่อถือ?)│
  │                               │
  │── 4. แลก Encryption Key ─────▶│
  │                               │
  │◀──── 5. เชื่อมต่อสำเร็จ 🔒 ───│
  │                               │
  │  ข้อมูลทั้งหมดถูกเข้ารหัสแล้ว  │
```

**CA** = Certificate Authority เช่น Let's Encrypt, DigiCert, Comodo

---

## 5. SSL Certificate

### ประเภท Certificate

| ประเภท | ตรวจสอบอะไร | เหมาะกับ |
|--------|-----------|---------|
| **DV** (Domain Validated) | แค่ domain | เว็บทั่วไป, Blog |
| **OV** (Organization Validated) | domain + องค์กร | เว็บธุรกิจ |
| **EV** (Extended Validation) | ตรวจสอบละเอียดมาก | ธนาคาร, e-commerce |

### ได้ SSL ฟรีจากไหน

- **Let's Encrypt** — ฟรี 100% ต่ออายุทุก 90 วันอัตโนมัติ
- Hosting ส่วนใหญ่ให้ฟรีในตัว (cPanel → Let's Encrypt)
- Cloudflare — SSL ฟรีพร้อม CDN

---

## 6. WordPress กับ HTTPS

### ตั้งค่าใน WordPress Admin

**Settings → General**
```
WordPress Address (URL): https://example.com
Site Address (URL):      https://example.com
```

### Redirect HTTP → HTTPS ใน .htaccess

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Plugin ช่วย

- **Really Simple SSL** — คลิกเดียว redirect ทั้งเว็บ

---

## 7. ดูของจริงใน Browser

1. เปิดเว็บที่มี HTTPS (เช่น `https://wordpress.org`)
2. คลิก **🔒 ไอคอนกุญแจ** ซ้ายของ URL bar
3. คลิก **"Connection is secure"** → **"Certificate is valid"**
4. จะเห็น: ออกโดยใคร, หมดอายุวันไหน, เข้ารหัสแบบไหน

---

## 8. สรุป

```
HTTP  = ไม่ปลอดภัย  | Port 80  | http://
HTTPS = ปลอดภัย     | Port 443 | https:// + 🔒
```

- ปี 2024 เว็บทุกเว็บควรใช้ **HTTPS**
- Google **ลดอันดับ** เว็บที่ยังใช้ HTTP
- Browser แสดง **"Not Secure"** ถ้าไม่มี HTTPS
- WordPress ควรตั้งค่า URL เป็น `https://` เสมอ
