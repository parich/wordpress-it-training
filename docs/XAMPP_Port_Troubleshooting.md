# XAMPP Port Troubleshooting (Windows PowerShell)
[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

## ปัญหา: Port ถูกใช้งานอยู่ (Port Already in Use)

เมื่อเปิด XAMPP แล้ว Apache หรือ MySQL ไม่ start — สาเหตุหลักคือ port ถูก process อื่นใช้อยู่

---

## 1. เช็ค Port ที่ถูกใช้อยู่

### ดู Port ทั้งหมดที่กำลังถูกใช้

```powershell
netstat -ano
```

### ดูเฉพาะ Port ที่ XAMPP ใช้ (80, 443, 3306)

```powershell
netstat -ano | findstr ":80 "
netstat -ano | findstr ":443 "
netstat -ano | findstr ":3306 "
```

### ดูว่า Process ไหน (PID) ใช้ Port นั้น

```powershell
# ตัวอย่าง: ดู PID ที่ใช้ port 80
netstat -ano | findstr ":80 "

# นำ PID ที่ได้มาหาชื่อ process
tasklist /fi "PID eq 1234"
```

---

## 2. Port ที่ XAMPP ใช้

| Service | Default Port |
|---------|-------------|
| Apache HTTP | 80 |
| Apache HTTPS | 443 |
| MySQL | 3306 |
| FileZilla FTP | 21 |

---

## 3. ปิด Process ที่ใช้ Port นั้นอยู่

### วิธีที่ 1 — ปิดด้วย PID (แนะนำ)

```powershell
# หา PID จาก port ก่อน
netstat -ano | findstr ":80 "

# ปิด process ด้วย PID (เปลี่ยน 1234 เป็น PID จริง)
taskkill /PID 1234 /F
```

### วิธีที่ 2 — ปิดด้วยชื่อ Process

```powershell
# ปิด IIS (มักชนกับ Apache port 80)
taskkill /IM iisexpress.exe /F

# ปิด Skype (ชนพอร์ต 443)
taskkill /IM skype.exe /F

# ปิด MySQL อื่นที่ติดตั้งแยก
taskkill /IM mysqld.exe /F
```

---

## 4. ปิด Windows Service ที่ชนกับ XAMPP

### ดู Service ที่รันอยู่

```powershell
Get-Service | Where-Object {$_.Status -eq "Running"}
```

### ปิด IIS (World Wide Web Publishing Service)

```powershell
# หยุด IIS ชั่วคราว
Stop-Service -Name "W3SVC" -Force

# ปิดไม่ให้ start อัตโนมัติ
Set-Service -Name "W3SVC" -StartupType Disabled
```

### ปิด SQL Server (ชนกับ MySQL port 3306)

```powershell
Stop-Service -Name "MSSQLSERVER" -Force
```

### ปิด Web Deployment Agent Service

```powershell
Stop-Service -Name "MsDepSvc" -Force
```

---

## 5. เปิด Service กลับ (หลังใช้ XAMPP เสร็จ)

```powershell
# เปิด IIS กลับ
Start-Service -Name "W3SVC"
Set-Service -Name "W3SVC" -StartupType Automatic
```

---

## 6. แก้ไข Port ใน XAMPP (ถ้าไม่อยากปิด Service อื่น)

แก้ไขที่ **XAMPP Control Panel → Apache → Config → httpd.conf**

```
# เปลี่ยนจาก
Listen 80
# เป็น
Listen 8080
```

แก้ไขที่ **XAMPP Control Panel → Apache → Config → httpd-ssl.conf**

```
# เปลี่ยนจาก
Listen 443
# เป็น
Listen 8443
```

---

## 7. Script ตรวจสอบครบในที คัดลอกใช้ได้เลย

```powershell
Write-Host "=== XAMPP Port Check ===" -ForegroundColor Cyan

Write-Host "`n[Port 80 - Apache HTTP]" -ForegroundColor Yellow
netstat -ano | findstr ":80 "

Write-Host "`n[Port 443 - Apache HTTPS]" -ForegroundColor Yellow
netstat -ano | findstr ":443 "

Write-Host "`n[Port 3306 - MySQL]" -ForegroundColor Yellow
netstat -ano | findstr ":3306 "

Write-Host "`n=== Done ===" -ForegroundColor Cyan
```

รันใน PowerShell (Run as Administrator) แล้วนำ PID ที่ได้ไป `taskkill /PID xxxx /F`
