# docker-compose.yml Basics
[← กลับหน้าหลัก](WordPress_Training_IT_2Days.md)

> **"docker-compose.yml คือไฟล์ที่บอก Docker ว่าจะสร้าง container อะไรบ้าง และแต่ละตัวทำงานอย่างไร"**

---

## 1. docker-compose.yml คืออะไร

- ไฟล์ **YAML** ที่กำหนดค่า services (containers) ทั้งหมดในโปรเจกต์
- รันคำสั่งเดียว `docker compose up` → Docker สร้างทุกอย่างให้อัตโนมัติ
- แทนการพิมพ์คำสั่ง `docker run` ยาวๆ ทีละ container

---

## 2. โครงสร้าง YAML เบื้องต้น

```yaml
# YAML ใช้ indent (space) แทน {} ของ JSON
# ห้ามใช้ Tab — ใช้ Space เท่านั้น

key: value
list:
  - item1
  - item2
nested:
  child_key: child_value
```

---

## 3. ไฟล์ docker-compose.yml ของโปรเจกต์นี้

```yaml
version: "3.8"

services:
  db:
  wordpress:
  phpmyadmin:

volumes:
  db_data:
```

มี **3 services** และ **1 volume** — อธิบายทีละส่วน

---

## 4. Service: db (MariaDB)

```yaml
db:
  image: mariadb:10.11          # ใช้ MariaDB version 10.11
  restart: always               # restart อัตโนมัติถ้า crash
  env_file: .env                # โหลดตัวแปรจากไฟล์ .env
  environment:
    - MYSQL_DATABASE=${DB_NAME}         # ชื่อ database
    - MYSQL_USER=${DB_USER}             # user ที่ WordPress ใช้
    - MYSQL_PASSWORD=${DB_PASSWORD}     # password
    - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
  ports:
    - "${DB_PORT}:3306"         # เปิด port ให้เข้าจากเครื่อง host
  volumes:
    - db_data:/var/lib/mysql    # เก็บข้อมูล DB ไว้ใน volume
```

### ports อธิบาย

```
"${DB_PORT}:3306"
 └──────┬──────┘└──┬──┘
    Host port   Container port
    (33067)       (3306)

เข้าถึง DB จากเครื่องได้ที่: localhost:33067
```

### ค่าจาก .env

```ini
DB_NAME=wordpress
DB_USER=wordpress
DB_PASSWORD=wordpress
DB_ROOT_PASSWORD=change_me_root_pw
DB_PORT=33067
```

---

## 5. Service: wordpress

```yaml
wordpress:
  image: wordpress:6.4-php8.2-apache  # WP 6.4 + PHP 8.2 + Apache
  depends_on:
    - db                        # รอ db start ก่อน
  restart: always
  ports:
    - "${WP_PORT}:80"           # WP_PORT=8088 → เปิดที่ localhost:8088
  env_file: .env
  environment:
    - WORDPRESS_DB_HOST=db:3306      # ชื่อ service "db" + port
    - WORDPRESS_DB_NAME=${DB_NAME}
    - WORDPRESS_DB_USER=${DB_USER}
    - WORDPRESS_DB_PASSWORD=${DB_PASSWORD}
  volumes:
    - ./wordpress:/var/www/html           # โฟลเดอร์ local ↔ container
    - ./uploads.ini:/usr/local/etc/php/conf.d/uploads.ini
```

### depends_on

```
docker compose up
      │
      ├── เริ่ม db ก่อน
      │
      └── รอ db พร้อม → เริ่ม wordpress
```

### volumes อธิบาย

```
./wordpress  :  /var/www/html
└────┬─────┘   └──────┬──────┘
เครื่อง local    ข้างใน container

แก้ไขไฟล์บนเครื่อง → เห็นผลทันทีใน container
```

---

## 6. Service: phpmyadmin

```yaml
phpmyadmin:
  image: phpmyadmin:latest
  depends_on:
    - db                        # รอ db ก่อน
  restart: always
  ports:
    - "${PMA_PORT}:80"          # PMA_PORT=8089 → localhost:8089
  environment:
    - PMA_HOST=db               # ชื่อ service ที่เป็น MySQL
    - PMA_USER=${DB_USER}
    - PMA_PASSWORD=${DB_PASSWORD}
```

เข้าใช้งาน phpMyAdmin ที่ `http://localhost:8089`

---

## 7. Volumes

```yaml
volumes:
  db_data:    # Named volume — Docker จัดการเอง
```

```
Named Volume (db_data)
  └── ข้อมูล MySQL เก็บอยู่ใน Docker internal storage
  └── ลบ container แล้ว → ข้อมูลยังอยู่
  └── ต้องการลบจริงๆ → docker compose down -v
```

### Bind Mount vs Named Volume

| | Bind Mount | Named Volume |
|--|-----------|-------------|
| ตัวอย่าง | `./wordpress:/var/www/html` | `db_data:/var/lib/mysql` |
| เก็บที่ | โฟลเดอร์บนเครื่อง | Docker internal |
| แก้ไขได้จาก host | ✅ | ❌ |
| ใช้กับ | Source code, config | Database data |

---

## 8. Network ระหว่าง Services

Docker สร้าง **internal network** ให้อัตโนมัติ:

```
┌──────────────────────────────────────────┐
│          Docker Internal Network         │
│                                          │
│  ┌──────────┐    db:3306    ┌─────────┐  │
│  │wordpress │──────────────▶│   db    │  │
│  └──────────┘               └─────────┘  │
│                                    ▲     │
│  ┌──────────┐    db:3306           │     │
│  │phpmyadmin│────────────────────── │    │
│  └──────────┘                            │
└──────────────────────────────────────────┘
         │                │            │
    localhost:8088   localhost:8089  localhost:33067
      WordPress        phpMyAdmin      MySQL
```

> Services คุยกันด้วย **ชื่อ service** (`db`, `wordpress`) ไม่ต้องใช้ IP

---

## 9. คำสั่งที่ใช้บ่อย

### Start / Stop

```bash
# เปิด services ทั้งหมด (background)
docker compose up -d

# เปิดพร้อมดู logs (foreground)
docker compose up

# หยุดทุก service (container ยังอยู่, ข้อมูลยังอยู่)
docker compose stop

# หยุด + ลบ container (ข้อมูลใน volume ยังอยู่)
docker compose down

# restart service เดียว
docker compose restart wordpress
```

### Down + ลบ Volume (Clear ทุกอย่าง)

```bash
# หยุด + ลบ container + ลบ named volumes (db_data หายด้วย)
docker compose down -v

# หยุด + ลบ container + ลบ volumes + ลบ images ที่ build เอง
docker compose down -v --rmi local

# ลบทุกอย่างรวม images ที่ pull มาด้วย
docker compose down -v --rmi all
```

> ⚠️ `down -v` จะลบข้อมูล MySQL ทั้งหมด — WordPress ต้องติดตั้งใหม่

### Logs

```bash
# ดู logs แบบ real-time ทุก service
docker compose logs -f

# ดู logs เฉพาะ service
docker compose logs -f wordpress
docker compose logs -f db
```

### ดู Status / เข้า Container

```bash
# ดู container ที่รันอยู่
docker compose ps

# เข้าไปใน container (bash shell)
docker compose exec wordpress bash
docker compose exec db bash

# รัน command เดี่ยวใน container
docker compose exec db mysql -u wordpress -pwordpress wordpress
```

### Clear ระบบ Docker ทั้งหมด (ระวัง)

```bash
# ลบ container ที่หยุดแล้วทั้งหมด
docker container prune

# ลบ volume ที่ไม่มี container ใช้
docker volume prune

# ลบ image ที่ไม่ได้ใช้
docker image prune

# ลบทุกอย่างในครั้งเดียว (container + network + image + cache)
docker system prune -a

# ลบทุกอย่าง รวม volumes (nuclear option ⚠️)
docker system prune -a --volumes
```

### สรุประดับความรุนแรง

| คำสั่ง | ลบอะไร | ข้อมูล DB |
| ------ | ------ | ------- |
| `docker compose stop` | — | ✅ ยังอยู่ |
| `docker compose down` | containers | ✅ ยังอยู่ |
| `docker compose down -v` | containers + volumes | ❌ หายหมด |
| `docker system prune -a --volumes` | ทุกอย่างในเครื่อง | ❌ หายหมด |

---

## 10. สรุป

```
docker-compose.yml
  ├── version: "3.8"
  ├── services:
  │     ├── db          → MariaDB  → localhost:33067
  │     ├── wordpress   → WP+PHP   → localhost:8088
  │     └── phpmyadmin  → GUI DB   → localhost:8089
  └── volumes:
        └── db_data     → เก็บข้อมูล MySQL ถาวร
```

| URL | เข้าถึงอะไร |
|-----|-----------|
| `http://localhost:8088` | WordPress |
| `http://localhost:8089` | phpMyAdmin |
| `localhost:33067` | MySQL (จาก client เช่น DBeaver) |
