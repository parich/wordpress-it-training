# Web Control Panel และสิทธิ์ผู้ดูแลระบบ

หลังจากตรวจสอบ IP ของเว็บไซต์ด้วย `nslookup` แล้ว ขั้นตอนถัดไปคือการเข้าถึง **Control Panel** ของ Hosting Server เพื่อจัดการเว็บไซต์

---

## Control Panel คืออะไร

Control Panel คือ **หน้าเว็บสำหรับผู้ดูแลระบบ** ใช้จัดการ Hosting Server ผ่าน Browser
แทนที่จะต้องใช้ Command Line ทุกอย่าง

```
ผู้ดูแลระบบ
     │
     ▼
Control Panel (Web UI)
     │
     ├── จัดการ Domain / Subdomain
     ├── อัปโหลดไฟล์ผ่าน File Manager
     ├── สร้าง Database / User DB
     ├── ตั้งค่า Email
     ├── ดู Error Log
     └── ติดตั้ง WordPress (1-click)
```

---

## Control Panel ที่นิยมใช้

| Control Panel | Default Port | ลักษณะ | ราคา |
|--------------|-------------|--------|------|
| **cPanel** | 2082 / 2083 (SSL) | มาตรฐานอุตสาหกรรม ใช้กว้างที่สุด | มีค่าใช้จ่าย |
| **DirectAdmin** | 2222 | เบา ใช้ทรัพยากรน้อย | มีค่าใช้จ่าย |
| **Plesk** | 8443 / 8880 | รองรับ Windows + Linux | มีค่าใช้จ่าย |
| **CyberPanel** | **8090** | ใช้ OpenLiteSpeed เร็วมาก | **ฟรี** |
| **Webmin** | 10000 | Admin tool สำหรับ Linux | **ฟรี** |
| **HestiaCP** | **8083** | เบา เหมาะ VPS ขนาดเล็ก | **ฟรี** |

---

## ตัวอย่างจริง: cc.rmu.ac.th (IP 202.29.22.64)

จาก `nslookup cc.rmu.ac.th` ได้ IP = **202.29.22.64**

### ตรวจสอบ Control Panel ที่ IP นี้

```
https://202.29.22.64:8090
```

> Port **8090** = **CyberPanel** (OpenLiteSpeed Web Server)

```
┌─────────────────────────────────────┐
│  CyberPanel                         │
│  https://202.29.22.64:8090          │
│                                     │
│  Username: [admin]                  │
│  Password: [**********]             │
│                                     │
│           [ Log in ]                │
└─────────────────────────────────────┘
```

---

## ตัวอย่างจริง: IP 202.29.22.61

```
https://202.29.22.61:8083
```

> Port **8083** = **HestiaCP**

```
┌─────────────────────────────────────┐
│  Hestia Control Panel               │
│  https://202.29.22.61:8083          │
│                                     │
│  Username: [admin]                  │
│  Password: [**********]             │
│                                     │
│           [ Log in ]                │
└─────────────────────────────────────┘
```

---

## วิธีระบุ Control Panel จาก Port

เมื่อรู้ IP แล้ว ลองเปิด port ที่นิยมดูครับ:

```bash
# ลอง port ต่าง ๆ
https://202.29.22.64:2083      # cPanel (SSL)
https://202.29.22.64:8443      # Plesk
https://202.29.22.64:8090      # CyberPanel  ← เจอ
https://202.29.22.64:10000     # Webmin
https://202.29.22.64:8083      # HestiaCP

https://202.29.22.61:8083      # HestiaCP  ← เจอ
```

> Browser จะแสดง **SSL Warning** เพราะใช้ Self-signed Certificate
> กด **Advanced → Proceed** ได้เลยสำหรับระบบ Internal

---

## สิทธิ์ที่ผู้ดูแลระบบต้องมี

### 1. เข้า Control Panel

| ระดับ | สิทธิ์ |
|-------|--------|
| **Admin / Root** | เข้าได้ทุกอย่าง สร้าง/ลบ Account |
| **Reseller** | จัดการ Account ที่ตัวเองดูแล |
| **User** | จัดการ Hosting ของตัวเองเท่านั้น |

---

### 2. FTP / SFTP (อัปโหลดไฟล์)

ใช้อัปโหลด/ดาวน์โหลดไฟล์เว็บไซต์

| โปรโตคอล | Port | ความปลอดภัย |
|----------|------|------------|
| FTP | 21 | ไม่เข้ารหัส (หลีกเลี่ยง) |
| FTPS | 990 | FTP + SSL |
| **SFTP** | **22** | SSH-based เข้ารหัสทั้งหมด (แนะนำ) |

**เชื่อมต่อด้วย FileZilla:**

```
Host:     sftp://202.29.22.64
Username: ชื่อ user ใน Hosting
Password: รหัสผ่าน
Port:     22
```

ไฟล์ WordPress อยู่ที่: `public_html/` หรือ `/var/www/html/`

---

### 3. phpMyAdmin (จัดการ Database ผ่าน Browser)

phpMyAdmin คือ Web UI สำหรับจัดการ MySQL/MariaDB

**URL ทั่วไป:**

```
https://202.29.22.64/phpmyadmin
https://202.29.22.64:8090/phpmyadmin   # CyberPanel
https://202.29.22.61:8083/phpmyadmin   # HestiaCP (อาจแตกต่างกัน)
```

**สิ่งที่ทำได้:**

```
phpMyAdmin
├── ดู/แก้ไขตาราง Database
├── รัน SQL Query
├── Import .sql (Restore Database)
├── Export .sql (Backup Database)
└── สร้าง/ลบ User Database
```

> **สำคัญ:** phpMyAdmin ไม่ควร expose บน Internet โดยตรง
> ควรจำกัดด้วย IP Whitelist หรือ Basic Auth

---

### 4. Remote Database (เชื่อมต่อ DB โดยตรง)

เชื่อมต่อ MySQL จากเครื่อง Developer โดยตรง (ไม่ผ่าน phpMyAdmin)

**ข้อมูลที่ต้องมี:**

```
Host:     202.29.22.64
Port:     3306 (MySQL default)
Database: ชื่อ Database
Username: ชื่อ DB User
Password: รหัสผ่าน DB
```

**เชื่อมต่อด้วย MySQL Workbench หรือ DBeaver:**

```
MySQL Workbench
→ New Connection
→ Connection Method: Standard TCP/IP
→ Hostname: 202.29.22.64
→ Port: 3306
→ Username: db_user
→ Password: ****
```

> **ข้อควรระวัง:** MySQL ส่วนใหญ่ block Remote Connection โดย default
> ต้องให้ Server Admin เปิด Remote Access และ Whitelist IP ก่อน

---

## สรุป: ช่องทางเข้าถึงเซิร์ฟเวอร์

```
IP Server: 202.29.22.64
│
├─ :8090  → CyberPanel (Web Control Panel)
│           ├── File Manager
│           ├── Database Manager
│           ├── Email Manager
│           └── phpMyAdmin
│
├─ :22    → SSH / SFTP (Command Line / FileZilla)
│
├─ :21    → FTP (ไม่แนะนำ ใช้ SFTP แทน)
│
├─ :3306  → MySQL Remote (ต้องเปิดสิทธิ์พิเศษ)
│
└─ :80 / :443 → เว็บไซต์จริง (HTTP / HTTPS)


IP Server: 202.29.22.61
│
└─ :8083  → HestiaCP (Web Control Panel)
```

---

## เปรียบเทียบ CyberPanel vs HestiaCP

| | CyberPanel | HestiaCP |
|---|---|---|
| Port | 8090 | 8083 |
| Web Server | OpenLiteSpeed | Nginx / Apache |
| ความเร็ว | สูงมาก (LSCache) | ดี |
| ติดตั้ง WordPress | 1-click | 1-click (Softaculous) |
| Let's Encrypt SSL | รองรับ | รองรับ |
| ใช้ RAM | ปานกลาง | น้อยมาก |
| เหมาะกับ | VPS / Dedicated Server | VPS ขนาดเล็ก |

---

## ความปลอดภัยเบื้องต้น

| สิ่งที่ต้องทำ | เหตุผล |
|-------------|--------|
| เปลี่ยน Password เริ่มต้น | Default password เดาง่าย |
| เปิด 2FA | ป้องกัน Brute Force |
| จำกัด IP ที่เข้า Control Panel ได้ | ลด Attack Surface |
| ใช้ SFTP แทน FTP | FTP ส่งข้อมูลแบบไม่เข้ารหัส |
| ปิด Remote MySQL ถ้าไม่ใช้ | ลดความเสี่ยง |
| อัปเดต Control Panel สม่ำเสมอ | แก้ช่องโหว่ |

---

## ดูเพิ่มเติม

- [DNS_Lookup_Guide.md](DNS_Lookup_Guide.md) — ตรวจสอบ IP ด้วย nslookup
- [HTTP_HTTPS.md](HTTP_HTTPS.md) — SSL Certificate และ HTTPS
- [WordPress_Backup_Guide.md](WordPress_Backup_Guide.md) — Backup ผ่าน phpMyAdmin
