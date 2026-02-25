# WordPress การใช้งานเบื้องต้น

---

## 1. เข้าสู่ระบบ WordPress

### Login

เปิด Browser แล้วไปที่:

```
http://your-domain.com/wp-admin
```

ตัวอย่างในเครื่อง (Docker):

```
http://localhost:8088/wp-admin
```

กรอก **Username** และ **Password** แล้วคลิก **Log In**

---

### Dashboard

หลัง Login จะเข้าสู่หน้า **Dashboard** — หน้าแรกของ Admin Panel

```
┌─────────────────────────────────────────────────────┐
│  WordPress Admin                          [ชื่อ user]│
├──────────────┬──────────────────────────────────────┤
│  Dashboard   │   At a Glance                        │
│  Posts       │   ▪ 5 Posts   ▪ 2 Pages              │
│  Media       │   ▪ WordPress 6.x                    │
│  Pages       │                                      │
│  Comments    │   Quick Draft                        │
│  Appearance  │   [กรอกหัวข้อ draft ได้เลย]          │
│  Plugins     │                                      │
│  Users       │   Activity                           │
│  Settings    │   Recently Published...              │
└──────────────┴──────────────────────────────────────┘
```

**เมนูด้านบน (Toolbar):**

| ปุ่ม | หน้าที่ |
|------|---------|
| **Visit Site** | เปิดหน้าเว็บฝั่ง Frontend (ที่ผู้เยี่ยมชมเห็น) |
| **+ New** | สร้าง Post / Page / Media ใหม่ |
| **ชื่อ User** | แก้ไข Profile / Logout |

> **Visit Site vs Dashboard**
> - **Dashboard** = ฝั่ง Admin (จัดการเนื้อหา)
> - **Visit Site** = ฝั่ง Frontend (หน้าเว็บจริงที่คนทั่วไปเห็น)
> กดสลับไปมาได้ตลอด

---

## 2. โครงสร้างเนื้อหา (Content)

WordPress แบ่งเนื้อหาออกเป็น 3 กลุ่มหลัก:

```
Content
├── Pages       ← หน้าคงที่ (About, Contact)
├── Posts       ← บทความ (มี Categories + Tags)
└── Media       ← ไฟล์ทั้งหมด (รูปภาพ, PDF, วิดีโอ)
```

---

### 2.1 Pages (หน้าคงที่)

**Posts → Pages**

| ลักษณะ | รายละเอียด |
|--------|-----------|
| ใช้สำหรับ | เนื้อหาที่ไม่เปลี่ยนบ่อย |
| ตัวอย่าง | หน้า About Us, Contact, Privacy Policy |
| ไม่มี | Categories, Tags |
| มี | Parent Page (จัดลำดับชั้นได้) |
| แสดงใน | เมนู Navigation ส่วนใหญ่ |

**การสร้าง Page ใหม่:**

1. ไปที่ **Pages → Add New**
2. กรอก **Title** (ชื่อหน้า)
3. เพิ่มเนื้อหาใน **Block Editor**
4. ด้านขวา: ตั้งค่า **Status** (Draft / Published)
5. คลิก **Publish**

```
Pages                                Title (ภาษาไทย)        Slug (URL)
├── หน้าแรก                          หน้าแรก               /home
├── เกี่ยวกับเรา                      เกี่ยวกับเรา           /about
│   ├── ประวัติความเป็นมา             ประวัติความเป็นมา      /about/history
│   ├── วิสัยทัศน์และพันธกิจ          วิสัยทัศน์และพันธกิจ   /about/vision
│   └── โครงสร้างองค์กร              โครงสร้างองค์กร        /about/organization
├── หลักสูตร                         หลักสูตร              /programs
│   ├── ปริญญาตรี                    ปริญญาตรี             /programs/bachelor
│   └── ปริญญาโท                    ปริญญาโท              /programs/master
├── บุคลากร                         บุคลากร               /staff
├── ข่าวสาร                         ข่าวสาร               /news        ← Posts page
├── ดาวน์โหลด                       ดาวน์โหลด             /download
├── นโยบายความเป็นส่วนตัว            นโยบายความเป็นส่วนตัว  /privacy-policy
└── ติดต่อเรา                        ติดต่อเรา             /contact
```

---

### 2.2 Posts (บทความ)

**Posts → All Posts**

| ลักษณะ | รายละเอียด |
|--------|-----------|
| ใช้สำหรับ | บทความ, ข่าวสาร, Blog |
| เรียงลำดับ | ตาม วันที่ (ใหม่ → เก่า) |
| มี | Categories, Tags, Featured Image |
| แสดงใน | Blog page, Archives, RSS Feed |

**การสร้าง Post ใหม่:**

1. ไปที่ **Posts → Add New**
2. กรอก **Title**
3. เพิ่มเนื้อหา
4. ตั้งค่า **Category** (ด้านขวา)
5. เพิ่ม **Tags** (ด้านขวา)
6. เลือก **Featured Image**
7. คลิก **Publish**

---

#### 2.2.1 Categories (หมวดหมู่)

**Posts → Categories**

- จัดกลุ่มบทความแบบ **ต้นไม้** (มี Parent-Child ได้)
- บทความ **ต้องมี** Category อย่างน้อย 1 อัน (ถ้าไม่เลือก = Uncategorized)
- URL: `example.com/category/ชื่อ-category/`

```
ตัวอย่าง Categories:       Slug (URL)
├── ข่าวสาร               /category/news
│   ├── ข่าวมหาวิทยาลัย   /category/news/university
│   └── ข่าวกิจกรรม       /category/news/activity
├── บทความวิชาการ         /category/academic
└── ประกาศ               /category/announcement
```

**เพิ่ม Category:**

1. **Posts → Categories**
2. กรอก **Name** และ **Slug** (URL-friendly)
3. เลือก **Parent Category** (ถ้าต้องการจัดเป็นหมวดย่อย)
4. คลิก **Add New Category**

---

#### 2.2.2 Tags (ป้ายกำกับ)

**Posts → Tags**

- ติดป้ายบทความแบบ **แบน** (ไม่มี Parent-Child)
- บทความหนึ่งมีได้หลาย Tag
- URL: `example.com/tag/ชื่อ-tag/`

| | Categories | Tags |
|---|---|---|
| โครงสร้าง | ลำดับชั้น (Parent-Child) | แบน |
| บังคับ | ใช่ (มี Uncategorized) | ไม่บังคับ |
| จำนวนต่อ Post | 1–2 อัน (แนะนำ) | ไม่จำกัด |
| ใช้สำหรับ | จัดกลุ่มหลัก | คำค้นหาเพิ่มเติม |

**ตัวอย่างการใช้:**

```
Post: "วิธีติดตั้ง WordPress บน Docker"
  Category: บทความวิชาการ
  Tags: wordpress, docker, tutorial, linux
```

---

### 2.3 Media (ไฟล์มีเดีย)

**Media → Library**

WordPress เก็บไฟล์ทุกอย่างที่อัปโหลดไว้ใน **Media Library**
เส้นทางจริงในเซิร์ฟเวอร์: `wp-content/uploads/YYYY/MM/`

---

#### 2.3.1 Image (รูปภาพ)

รองรับไฟล์: `.jpg` `.jpeg` `.png` `.gif` `.webp` `.svg`

**อัปโหลดรูป:**

1. **Media → Add New**
2. ลาก-วางไฟล์ หรือคลิก **Select Files**
3. WordPress สร้างขนาดย่อให้อัตโนมัติ:

| ขนาด | Default (px) |
|------|-------------|
| Thumbnail | 150 × 150 |
| Medium | 300 × 300 |
| Large | 1024 × 1024 |
| Full | ขนาดต้นฉบับ |

**แก้ไขข้อมูลรูป (Attachment Details):**

- **Alt Text** — ข้อความ accessibility / SEO (สำคัญมาก)
- **Caption** — คำบรรยายใต้รูป
- **Title** — ชื่อไฟล์
- **Description** — รายละเอียดเพิ่มเติม

---

#### 2.3.2 File (ไฟล์อื่น ๆ)

รองรับไฟล์: `.pdf` `.doc` `.docx` `.xls` `.xlsx` `.ppt` `.zip` `.mp4` `.mp3`

**อัปโหลดและแทรกไฟล์ใน Post/Page:**

1. ใน Block Editor คลิก **+** → เลือก **File**
2. อัปโหลดไฟล์ หรือเลือกจาก Media Library
3. ผู้เยี่ยมชมสามารถ Download ได้จาก link

> **ขีดจำกัดขนาดไฟล์** ขึ้นกับ `upload_max_filesize` ใน PHP
> ในโปรเจกต์นี้ตั้งไว้ที่ **64 MB** ผ่าน `wp/uploads.ini`

---

## 3. Users (ผู้ใช้งาน)

**Users → All Users**

WordPress มีระบบ Role ควบคุมสิทธิ์การเข้าถึง:

| Role | สิทธิ์ |
|------|--------|
| **Administrator** | ทำได้ทุกอย่าง |
| **Editor** | จัดการ Posts/Pages ทั้งหมด รวมของคนอื่น |
| **Author** | เขียนและ Publish Post ของตัวเองได้ |
| **Contributor** | เขียน Post ได้ แต่ Publish ไม่ได้ |
| **Subscriber** | Login ได้ แก้ Profile ตัวเองได้เท่านั้น |

**เพิ่ม User ใหม่:**

1. **Users → Add New**
2. กรอก **Username**, **Email**, **Password**
3. เลือก **Role**
4. คลิก **Add New User**

**แก้ไข Profile ตัวเอง:**

- คลิกชื่อ User มุมขวาบน → **Edit Profile**
- เปลี่ยน Display Name, Password, Email

---

## 4. Plugins (ปลั๊กอิน)

**Plugins → Installed Plugins**

Plugin คือโค้ดที่เพิ่มความสามารถให้ WordPress โดยไม่ต้องแก้ Core

```
wp-content/
└── plugins/
    ├── workppass-contact/   ← Plugin ที่สร้างเอง
    ├── akismet/             ← Anti-spam
    └── hello/               ← ตัวอย่าง
```

**การจัดการ Plugin:**

| การกระทำ | วิธี |
|----------|------|
| เปิด/ปิด | คลิก **Activate** / **Deactivate** |
| ติดตั้งใหม่ | **Add New** → ค้นหาชื่อ → **Install Now** |
| อัปเดต | **Plugins → Updates** หรือ Dashboard notification |
| ลบ | Deactivate ก่อน → คลิก **Delete** |

> ต้อง **Deactivate ก่อนเสมอ** ถึงจะลบได้

---

## 5. Appearance (รูปลักษณ์)

**Appearance**

ควบคุมหน้าตาของเว็บฝั่ง Frontend

### 5.1 Themes

**Appearance → Themes**

- Theme ควบคุม Layout, สี, Font, โครงสร้างหน้าเว็บ
- เปลี่ยน Theme ได้โดยไม่กระทบเนื้อหา

| การกระทำ | วิธี |
|----------|------|
| เปลี่ยน Theme | Hover บน Theme → **Activate** |
| ติดตั้ง Theme ใหม่ | **Add New** → ค้นหา → **Install** → **Activate** |
| แก้ไข Theme | **Appearance → Theme File Editor** (ไม่แนะนำ ควรใช้ Child Theme) |

### 5.2 Customize

**Appearance → Customize**

ปรับแต่งแบบ Live Preview:
- Site Identity (Logo, Site Name)
- Colors, Typography
- Header, Footer
- Menus, Widgets

### 5.3 Menus

**Appearance → Menus**

สร้างและจัดการเมนู Navigation:

1. สร้าง Menu ใหม่ → ตั้งชื่อ
2. ลาก Pages / Posts / Custom Links มาใส่
3. จัดลำดับและ Submenu ด้วยการลาก
4. เลือก **Display Location** (Primary Menu, Footer Menu ฯลฯ)
5. คลิก **Save Menu**

### 5.4 Widgets

**Appearance → Widgets**

Block ที่แสดงในพื้นที่พิเศษ (Sidebar, Footer):
- Recent Posts
- Categories
- Search
- Text/HTML custom

---

## 6. Settings (การตั้งค่า)

**Settings**

### 6.1 General

| การตั้งค่า | ความหมาย |
|-----------|----------|
| Site Title | ชื่อเว็บไซต์ |
| Tagline | คำอธิบายสั้น |
| WordPress Address (URL) | URL ของ WordPress core |
| Site Address (URL) | URL ที่ผู้เยี่ยมชมเข้าถึง |
| Admin Email | อีเมลของ Admin |
| Timezone | โซนเวลา (Asia/Bangkok สำหรับไทย) |
| Date/Time Format | รูปแบบแสดงวันที่ |

### 6.2 Reading

| การตั้งค่า | ความหมาย |
|-----------|----------|
| Your homepage displays | เลือกว่าหน้าแรกเป็น Posts หรือ Static Page |
| Blog posts show at most | จำนวนบทความต่อหน้า |
| Search engine visibility | ติ๊ก = ซ่อนจาก Google (ใช้ตอน Dev) |

> **สำคัญ:** ตอน Dev ให้ติ๊ก "Discourage search engines" ไว้
> ก่อน Go Live ให้ **ปิด** ออก

### 6.3 Discussion

ตั้งค่าระบบ Comment:
- เปิด/ปิด Comment ทั้งเว็บ
- Moderation (รอ Approve ก่อนแสดง)
- การแจ้งเตือนทางอีเมล

### 6.4 Permalinks

**Settings → Permalinks**

ตั้งรูปแบบ URL ของ Post/Page:

| รูปแบบ | ตัวอย่าง URL |
|--------|-------------|
| Plain | `/?p=123` |
| Day and name | `/2025/01/15/ชื่อ-post/` |
| Month and name | `/2025/01/ชื่อ-post/` |
| **Post name** *(แนะนำ)* | `/ชื่อ-post/` |
| Custom | กำหนดเอง |

> แนะนำใช้ **Post name** — URL สั้น อ่านง่าย ดี SEO

---

## สรุปภาพรวม Admin Menu

```
WordPress Admin
├── Dashboard       ← ภาพรวมเว็บ
├── Posts           ← บทความ + Categories + Tags
├── Media           ← รูปภาพและไฟล์ทั้งหมด
├── Pages           ← หน้าคงที่
├── Comments        ← จัดการ Comment
├── Appearance      ← Theme, Menu, Widget, Customize
├── Plugins         ← เพิ่ม/ลบ/เปิด-ปิด Plugin
├── Users           ← จัดการ User และ Role
├── Tools           ← Import/Export, Site Health
└── Settings        ← ตั้งค่าทั้งหมด
```
