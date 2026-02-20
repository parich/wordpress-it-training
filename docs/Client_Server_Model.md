# Client–Server Model

> **"Client ถาม → Server ตอบ — ทุกหน้าเว็บที่เห็นคือผลของการสนทนานี้"**

---

## 1. แนวคิดหลัก

| บทบาท | คืออะไร | ตัวอย่าง |
|-------|---------|---------|
| **Client** | ฝั่งที่ขอข้อมูล | Browser (Chrome, Firefox), Mobile App |
| **Server** | ฝั่งที่เก็บและตอบข้อมูล | เครื่องที่รัน Apache + PHP + MySQL |

- Client **ส่ง Request** → Server **ตอบ Response**
- ทุกครั้งที่พิมพ์ URL แล้วกด Enter = Client ส่ง Request 1 ครั้ง

---

## 2. วงจร Request–Response

```
Browser (Client)                    Web Server
        │                               │
        │── GET /index.php ────────────▶│
        │                               │  ประมวลผล PHP
        │                               │  ดึงข้อมูลจาก MySQL
        │◀── 200 OK + HTML ─────────────│
        │                               │
   แสดงผลหน้าเว็บ
```

### HTTP Method ที่พบบ่อย

| Method | ใช้ทำอะไร | ตัวอย่าง |
|--------|---------|---------|
| `GET` | ขอดูข้อมูล | เปิดหน้าเว็บ |
| `POST` | ส่งข้อมูล | กรอก form / login |
| `PUT` | อัปเดตข้อมูล | แก้ไข profile |
| `DELETE` | ลบข้อมูล | ลบโพสต์ |

### HTTP Status Code ที่ควรรู้

| Code | ความหมาย |
|------|---------|
| `200 OK` | สำเร็จ |
| `301 Moved Permanently` | เปลี่ยน URL ถาวร |
| `404 Not Found` | ไม่พบหน้านั้น |
| `403 Forbidden` | ไม่มีสิทธิ์เข้าถึง |
| `500 Internal Server Error` | Server เกิดข้อผิดพลาด |

---

## 3. DNS — แปลง Domain → IP

```
พิมพ์ www.example.com
        │
        ▼
   DNS Server
   แปลงเป็น → 93.184.216.34 (IP Address)
        │
        ▼
   เชื่อมต่อไปยัง Web Server
```

- Domain คือชื่อที่จำง่าย เช่น `wordpress.org`
- IP Address คือที่อยู่จริงของ Server บนอินเทอร์เน็ต

---

## 4. เชื่อมกับ WordPress

```
ผู้ใช้เปิด Browser
        │
        │  GET /about/
        ▼
   Apache (Web Server)
        │
        │  ส่งต่อให้ PHP
        ▼
   WordPress (PHP)
        │
        │  SELECT * FROM wp_posts WHERE ...
        ▼
   MySQL (Database)
        │
        │  ส่งข้อมูลกลับ
        ▼
   WordPress สร้าง HTML
        │
        ▼
   Browser แสดงหน้าเว็บ
```

| Layer | เทคโนโลยี | หน้าที่ |
|-------|---------|---------|
| Client | Browser | แสดงผล HTML / CSS / JS |
| Web Server | Apache / Nginx | รับ Request, ส่งต่อให้ PHP |
| App | PHP + WordPress | สร้าง HTML จากข้อมูล |
| Database | MySQL / MariaDB | เก็บ posts, users, settings |

---

## 5. Demo — ดูของจริงใน Browser

1. เปิด **Chrome** ไปที่เว็บใดก็ได้
2. กด `F12` หรือ `Ctrl + Shift + I` → เปิด DevTools
3. คลิก tab **Network**
4. กด `F5` reload หน้า
5. คลิกที่ request แรก (ชื่อ domain)

สิ่งที่จะเห็น:
- **Headers** → Request Headers (browser ส่งอะไรไป) / Response Headers (server ตอบอะไรกลับ)
- **Status Code** → `200 OK`
- **Response** → HTML ที่ server ส่งกลับมา

---

## 6. สรุป

```
Client  ──── Request (GET/POST) ────▶  Server
Client  ◀─── Response (HTML/JSON) ───  Server
```

- **URL** = ที่อยู่ของ resource บน Server
- **DNS** = แปลง domain → IP
- **HTTP Method** = บอก Server ว่าจะทำอะไร
- **Status Code** = Server บอกผลลัพธ์กลับมา
- **WordPress** = PHP ทำงานบน Server สร้าง HTML ส่งกลับ Client
