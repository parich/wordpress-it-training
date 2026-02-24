# WordPress Training for IT Students (Year 1)

สภาพแวดล้อมการเรียนรู้ WordPress สำหรับนักศึกษา IT — ครอบคลุม Web Architecture, Docker, Plugin Development, Analytics และ Security

---

## 📚 Training Index (2 วัน)

> **[→ เปิด Training Index แบบเต็ม](docs/WordPress_Training_IT_2Days.md)**

### 🗓 DAY 1: Environment, Architecture & Core WordPress

| # | หัวข้อ | เอกสาร |
|---|--------|--------|
| 1 | Web Architecture, DNS, HTTP/HTTPS | [Client-Server Model](docs/Client_Server_Model.md) · [HTTP/HTTPS](docs/HTTP_HTTPS.md) · [LAMP/LEMP](docs/LAMP_LEMP_Stack.md) |
| 2 | ตรวจสอบ Domain / IP | [nslookup Guide](docs/DNS_Lookup_Guide.md) |
| 3 | Web Control Panel (cPanel, CyberPanel, HestiaCP) | [Control Panel Guide](docs/Web_Control_Panel_Guide.md) |
| 4 | Docker + WordPress + phpMyAdmin | [Docker Compose](docs/Docker_Compose_Basics.md) · [phpMyAdmin Import/Export](docs/phpMyAdmin_Guide.md) |
| 5 | WP-CLI | [WP-CLI Guide](docs/WP_CLI_Guide.md) |
| 6 | WordPress Structure | [File Structure](docs/WordPress_File_Structure.md) · [Database](docs/WordPress_Database_Structure.md) · [wp-config.php](docs/WordPress_wp-config.md) |
| 7 | Semantic Versioning | [SemVer Guide](docs/Semantic_Versioning.md) |
| 8 | WordPress Basic Usage | [Dashboard, Posts, Pages, Media, Users, Plugins](docs/WordPress_Basic_Usage.md) |
| 9 | General Settings, Permalinks | [Settings Guide](docs/WordPress_General_Settings.md) |
| 10 | Analyze Other Websites | [Wappalyzer, DevTools, WhatWPTheme](docs/Analyze_Other_Websites.md) |

### 🗓 DAY 2: Analytics, SEO & Performance

| # | หัวข้อ |
|---|--------|
| 1 | Google Analytics (GA4) — Page Views, Events, PDPA |
| 2 | Google Tag Manager |
| 3 | Google Search Console — Sitemap, Index, Errors |
| 4 | Performance — PageSpeed, GTmetrix, LCP/CLS/TTFB |
| 5 | Optimization — Image, Cache, Minify, CDN |
| 6 | Security — Site Health, Hardening, [Backup](docs/WordPress_Backup_Guide.md) |
| 7 | Final Project |

---

## 🐳 Docker Environment

```bash
# Start (WordPress + MariaDB + phpMyAdmin)
cd wp && docker compose up -d

# Stop
cd wp && docker compose down
```

| Service | URL |
|---------|-----|
| WordPress | http://localhost:8088 |
| phpMyAdmin | http://localhost:8089 |

---

## 🔌 Custom Plugin

Plugin `workppass-contact` — Contact Form ที่บันทึกข้อมูลลง Custom Post Type

```
code/plugins/workppass-contact/
└── workppass-contact.php   ← Single-file plugin
```

Features: REST API endpoint · Shortcode `[workppass_contact_form]` · Admin columns · Meta box

---

## 📁 Repository Structure

```
wp/                        ← Docker Compose stack
  docker-compose.yml
  .env
  uploads.ini
  www/                     ← WordPress root (gitignored)

code/plugins/
  workppass-contact/       ← Custom plugin source

docs/                      ← Training reference docs (Thai/English)
  WordPress_Training_IT_2Days.md   ← Training Index (หน้าหลัก)
  *.md                             ← Topic-specific guides
```
