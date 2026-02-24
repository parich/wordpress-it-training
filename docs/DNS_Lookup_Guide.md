# การตรวจสอบ Domain และ IP ด้วย nslookup

การใช้ `nslookup` เพื่อตรวจสอบว่าเว็บไซต์ตั้งอยู่ที่ IP ไหน DNS ชี้ไปที่ใด และมีปัญหา DNS หรือไม่

---

## พื้นฐาน DNS คืออะไร

```
ผู้ใช้พิมพ์ → cc.rmu.ac.th
        ↓
   DNS Server  (แปลงชื่อ → IP)
        ↓
  202.29.22.64  (เซิร์ฟเวอร์จริง)
        ↓
   Browser โหลดหน้าเว็บ
```

- **Domain** = ชื่อที่จำง่าย เช่น `cc.rmu.ac.th`
- **IP Address** = ที่อยู่จริงของเซิร์ฟเวอร์บนอินเทอร์เน็ต เช่น `202.29.22.64`
- **DNS Server** = สมุดโทรศัพท์ที่แปลง Domain → IP

---

## nslookup คืออะไร

`nslookup` (Name Server Lookup) คือ command-line tool สำหรับ **สอบถาม DNS Server**
ใช้ได้บน Windows, macOS, Linux โดยไม่ต้องติดตั้งเพิ่ม

---

## ตัวอย่างที่ 1 — nslookup พื้นฐาน

```
nslookup cc.rmu.ac.th
```

**ผลลัพธ์จริง:**

```
Server:  UnKnown
Address:  2001:3c8:a702:0:202:29:22:3

Name:    culture.rmu.ac.th
Address:  202.29.22.64
Aliases:  cc.rmu.ac.th
```

**อ่านผลลัพธ์:**

| บรรทัด | ความหมาย |
|--------|----------|
| `Server: UnKnown` | DNS Server ที่ใช้ตอบ (ของ ISP หรือ router) |
| `Address: 2001:3c8:...` | IP ของ DNS Server (IPv6) |
| `Name: culture.rmu.ac.th` | ชื่อจริงของเซิร์ฟเวอร์ (Canonical Name) |
| `Address: 202.29.22.64` | **IP จริงของเว็บไซต์** |
| `Aliases: cc.rmu.ac.th` | `cc.rmu.ac.th` เป็นแค่ชื่อสมญา (CNAME) ชี้ไปที่ `culture.rmu.ac.th` |

> **สรุป:** `cc.rmu.ac.th` เป็น **CNAME** (alias) ของ `culture.rmu.ac.th`
> เซิร์ฟเวอร์จริงอยู่ที่ IP **202.29.22.64** ซึ่งเป็น IP ของ มหาวิทยาลัยราชมงคลมหานคร

---

## ตัวอย่างที่ 2 — nslookup ระบุ DNS Server (Google DNS)

```
nslookup cc.rmu.ac.th 8.8.8.8
```

บอกให้ถาม **Google Public DNS** (8.8.8.8) แทน DNS ของ ISP

**ผลลัพธ์จริง:**

```
Server:  dns.google
Address:  8.8.8.8

Non-authoritative answer:
Name:    culture.rmu.ac.th
Address:  202.29.22.64
Aliases:  cc.rmu.ac.th
```

**อ่านผลลัพธ์:**

| บรรทัด | ความหมาย |
|--------|----------|
| `Server: dns.google` | ใช้ Google DNS ตอบ |
| `Address: 8.8.8.8` | IP ของ Google DNS |
| `Non-authoritative answer` | ข้อมูลจาก cache ของ Google (ไม่ใช่จากเจ้าของ domain โดยตรง) |
| `Name: culture.rmu.ac.th` | ชื่อจริงของเซิร์ฟเวอร์ |
| `Address: 202.29.22.64` | IP จริง (ตรงกับตัวอย่างที่ 1) |

> **สรุป:** ผลเหมือนกัน = DNS ทั้งสองแหล่งให้ข้อมูลตรงกัน แสดงว่า DNS ทำงานปกติ

---

## เปรียบเทียบ: DNS ของ ISP vs Google DNS

| | DNS ของ ISP / Router | Google DNS (8.8.8.8) |
|---|---|---|
| ความเร็ว | เร็ว (ใกล้กว่า) | อาจช้ากว่าเล็กน้อย |
| ความน่าเชื่อถือ | ขึ้นกับ ISP | เสถียรมาก |
| ใช้ตรวจสอบ | ผลที่ผู้ใช้ในเครือข่ายนั้นเห็น | ผลที่โลกภายนอกเห็น |
| **เมื่อผลต่างกัน** | DNS ของ ISP อาจ cache เก่า | Google DNS อัปเดตล่าสุด |

---

## CNAME คืออะไร

```
cc.rmu.ac.th  →  CNAME  →  culture.rmu.ac.th  →  A  →  202.29.22.64
   (alias)                    (ชื่อจริง)               (IP จริง)
```

- **A Record** = Domain ชี้ตรงไปที่ IP
- **CNAME Record** = Domain ชี้ไปที่ Domain อื่น (alias)
- `cc.rmu.ac.th` เป็น CNAME หมายความว่า ถ้าเปลี่ยน IP ของ `culture.rmu.ac.th` ทุก alias จะตามโดยอัตโนมัติ

---

## DNS Server สาธารณะที่ใช้บ่อย

| DNS Server | IP | เจ้าของ |
|---|---|---|
| Google DNS | `8.8.8.8` / `8.8.4.4` | Google |
| Cloudflare | `1.1.1.1` / `1.0.0.1` | Cloudflare |
| OpenDNS | `208.67.222.222` | Cisco |

ตัวอย่างการใช้:

```bash
nslookup cc.rmu.ac.th 8.8.8.8       # ถาม Google DNS
nslookup cc.rmu.ac.th 1.1.1.1       # ถาม Cloudflare DNS
nslookup cc.rmu.ac.th 208.67.222.222 # ถาม OpenDNS
```

---

## Use Cases — ใช้ nslookup เมื่อไหร่

### 1. เว็บเข้าไม่ได้ — ตรวจว่า DNS ทำงานอยู่ไหม

```
nslookup cc.rmu.ac.th
```

- **มี IP** = DNS ทำงาน ปัญหาอยู่ที่เซิร์ฟเวอร์หรือ network
- **ไม่มี IP / "can't find"** = DNS มีปัญหา ลอง `nslookup cc.rmu.ac.th 8.8.8.8`

### 2. เพิ่งย้าย Hosting — ตรวจว่า DNS อัปเดตแล้วหรือยัง

```bash
nslookup cc.rmu.ac.th              # DNS ที่ใช้อยู่ → ได้ IP เดิม (ยังไม่อัปเดต)
nslookup cc.rmu.ac.th 8.8.8.8     # Google DNS → ได้ IP ใหม่ (อัปเดตแล้ว)
```

ถ้าผลต่างกัน = DNS ยังไม่ propagate ถึงทุกที่ (ปกติใช้เวลา 24–48 ชั่วโมง)

### 3. ตรวจว่าเว็บใช้ CDN หรือเปล่า

```
nslookup cc.rmu.ac.th
```

ถ้า IP เป็นของ Cloudflare (`104.x.x.x`) หรือ Akamai แสดงว่าเว็บนั้นใช้ CDN

---

## สรุปคำสั่ง nslookup ที่ใช้บ่อย

```bash
# ตรวจ IP ของ domain
nslookup cc.rmu.ac.th

# ตรวจด้วย DNS server ที่กำหนด
nslookup cc.rmu.ac.th 8.8.8.8

# ตรวจ domain อื่น ๆ
nslookup wordpress.org
nslookup google.com 1.1.1.1
```

---

## ดูเพิ่มเติม

- [Client_Server_Model.md](Client_Server_Model.md) — DNS ทำงานอย่างไร
- [HTTP_HTTPS.md](HTTP_HTTPS.md) — SSL Certificate และ HTTPS
- [Analyze_Other_Websites.md](Analyze_Other_Websites.md) — วิเคราะห์เว็บไซต์อื่น
