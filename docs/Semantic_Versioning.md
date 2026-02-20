# Semantic Versioning (MAJOR.MINOR.PATCH)
[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

> **"Version number ไม่ใช่แค่ตัวเลข — มันบอกว่า 'เปลี่ยนอะไรไป' และ 'ปลอดภัยที่จะอัปเดตไหม'"**

---

## 1. รูปแบบ

```
    6   .   4   .   2
    │       │       │
  MAJOR   MINOR   PATCH
    │       │       │
    │       │       └── Bug fix, แก้ปัญหาเล็กน้อย
    │       └────────── เพิ่มฟีเจอร์ใหม่ (ยังใช้งานร่วมกันได้)
    └────────────────── เปลี่ยนแปลงใหญ่ (อาจ breaking)
```

---

## 2. แต่ละส่วนหมายความว่าอะไร

### PATCH — แก้ bug เล็กน้อย

```
6.4.2  →  6.4.3
```

- แก้ไข bug
- แก้ security vulnerability เล็กน้อย
- ไม่เพิ่มฟีเจอร์ใหม่
- **อัปเดตได้เลย — ปลอดภัย 100%**

### MINOR — เพิ่มฟีเจอร์ใหม่

```
6.4.x  →  6.5.0
```

- เพิ่มฟีเจอร์ใหม่
- **Backward Compatible** — code เดิมยังทำงานได้
- อาจ deprecate feature เก่า (แต่ยังใช้ได้อยู่)
- **อัปเดตได้ — ทดสอบก่อนถ้าเป็น production**

### MAJOR — เปลี่ยนแปลงใหญ่

```
6.x.x  →  7.0.0
```

- **Breaking Changes** — code เดิมอาจใช้ไม่ได้
- API เปลี่ยน, function ถูกลบ, behavior เปลี่ยน
- **ต้องทดสอบละเอียด ก่อนอัปเดต**

---

## 3. กฎการเพิ่ม version

```
มี bug fix          →  PATCH +1    6.4.1 → 6.4.2
เพิ่มฟีเจอร์ใหม่   →  MINOR +1    6.4.2 → 6.5.0  (PATCH reset เป็น 0)
Breaking change    →  MAJOR +1    6.5.0 → 7.0.0  (MINOR, PATCH reset เป็น 0)
```

---

## 4. WordPress ใช้ Semantic Versioning อย่างไร

### WordPress Core

```
WordPress 6.4.2
             │
         MAJOR.MINOR.PATCH

6.4.1  →  6.4.2   = Security/bug fix (อัปเดตได้เลย)
6.4.x  →  6.5.0   = Feature release (Full Site Editing, new blocks)
6.x.x  →  7.0.0   = Major rewrite (เปลี่ยน architecture)
```

WordPress release cycle ปกติ:
- **Major/Minor**: ทุก ~4 เดือน
- **Patch**: ทันทีเมื่อพบ security issue

### ดู WordPress version ปัจจุบัน

```bash
# ผ่าน WP-CLI
wp core version

# ผ่าน Admin
Dashboard → Updates → WordPress x.x.x
```

---

## 5. Plugin & Theme Version

### ตัวอย่าง WooCommerce

```
WooCommerce 8.5.2
             │
         MAJOR.MINOR.PATCH

8.4.0  →  8.5.0  = เพิ่ม payment gateway ใหม่
8.5.1  →  8.5.2  = แก้ bug checkout
7.x.x  →  8.0.0  = เปลี่ยน database schema (ต้องระวัง!)
```

### ดู version ใน Admin

```
Plugins → ดูคอลัมน์ Version / Update Available
```

---

## 6. PHP Version กับ Compatibility

```
PHP  8.0  →  8.1   = Minor (features ใหม่, deprecated บางอย่าง)
PHP  8.1  →  8.2   = Minor
PHP  7.x  →  8.0   = Major (Breaking! function หลายตัวถูกลบ)
```

### WordPress กับ PHP

| PHP Version | WordPress Support |
|------------|------------------|
| 7.4 | ใช้ได้ แต่ End of Life แล้ว |
| 8.0 | ใช้ได้ |
| 8.1 | ✅ แนะนำ |
| 8.2 | ✅ แนะนำ (โปรเจกต์นี้ใช้) |
| 8.3 | ✅ ล่าสุด |

---

## 7. Breaking Changes คืออะไร

```
PHP 7.x:
  ereg()          ← function นี้ยังมีอยู่
  mysql_connect() ← ยังใช้ได้

PHP 8.0:
  ereg()          ← ถูกลบ! → Fatal Error
  mysql_connect() ← ถูกลบ! → Fatal Error
```

Plugin ที่เขียนสำหรับ PHP 7.x → อาจพังทันทีเมื่อ upgrade เป็น PHP 8.0

---

## 8. When to Update — แนวทางปฏิบัติ

| Version Change | ความเสี่ยง | ควรทำอะไรก่อน |
|---------------|-----------|--------------|
| PATCH (x.x.**1**) | ต่ำมาก | อัปเดตได้เลย |
| MINOR (x.**1**.0) | ต่ำ-ปานกลาง | อ่าน changelog |
| MAJOR (**2**.0.0) | สูง | ทดสอบใน staging ก่อน |

### Staging คืออะไร

```
Production (เว็บจริง)
      │  copy ไป
      ▼
Staging (เว็บทดสอบ) ← ทดสอบ update ที่นี่ก่อน
      │  ถ้าโอเค
      ▼
Production ← อัปเดตจริง
```

---

## 9. อ่าน Changelog ก่อน Update

ก่อนอัปเดต plugin หรือ theme ดูที่:

```
Plugin page → "View version x.x.x details" → Changelog tab
```

สิ่งที่ต้องดู:
- มี **Breaking Changes** ไหม?
- PHP version ขั้นต่ำเปลี่ยนไหม?
- WordPress version ขั้นต่ำเปลี่ยนไหม?

---

## 10. สรุป

```
MAJOR.MINOR.PATCH
  │      │     │
  │      │     └── Bug fix → อัปเดตได้เลย ✅
  │      └────────── Feature ใหม่ → อ่าน changelog ก่อน ⚠️
  └───────────────── Breaking change → ทดสอบ staging ก่อน ❌

6.4.2 = WordPress 6, Feature release 4, Bug fix 2
```

> กฎง่ายๆ: **"PATCH อัปเดตเลย, MINOR อ่านก่อน, MAJOR ระวัง"**
