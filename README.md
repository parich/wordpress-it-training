# WordPress with Docker Compose

Quick setup to run WordPress + MariaDB locally.

Files added:

- docker-compose.yml
- .env

Usage:

1. Edit `.env` to customize passwords and port.
2. Start services:

```powershell
docker compose up -d
```

3. Open http://localhost:8080 (or the port set in `WP_PORT`).

Stopping and removing containers:

```powershell
docker compose down -v
```

Notes:

- Change default passwords in `.env` before exposing to the public.
- Volumes `db_data` and `wp_data` persist database and uploads.
