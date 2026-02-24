# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A WordPress training environment for IT students, combining:
- A Docker Compose stack for local WordPress development
- A custom WordPress plugin (`workppass-contact`)
- Reference documentation in `docs/` covering a 2-day WordPress training curriculum (Thai language)

## Docker Environment

All Docker commands must be run from the `wp/` directory where `docker-compose.yml` lives.

```bash
# Start WordPress + MariaDB + phpMyAdmin
cd wp && docker compose up -d

# Stop and remove containers (keeps volumes)
cd wp && docker compose down

# Stop and remove containers AND volumes (wipes DB and wp-content)
cd wp && docker compose down -v
```

**Service URLs** (based on defaults in `wp/.env`):
- WordPress: http://localhost:8088
- phpMyAdmin: http://localhost:8089
- MariaDB: localhost:33067

**Credentials** are in `wp/.env`. The `.env` file is committed because this is a training project — the comment in the file notes it should be gitignored for production.

**Volumes**: `wp/www/` is the live WordPress root (bind-mounted, gitignored). `db_data` is a named Docker volume for the database.

**PHP upload limit** is set via `wp/uploads.ini` (64 MB), mounted into the WordPress container.

## Plugin Development

Custom plugin source lives in `code/plugins/workppass-contact/`. To test changes, copy/symlink the plugin folder into the running container's `wp/www/wp-content/plugins/` directory.

### workppass-contact plugin architecture

Single-file plugin (`workppass-contact.php`) with six sections:

1. **Custom Post Type** — registers `contact_submission` (private, admin-only, no REST)
2. **REST endpoint** — `POST /wp-json/workppass/v1/contact` — handles form submission: nonce verification → honeypot check → validation → `wp_insert_post` + `update_post_meta`
3. **Shortcode** `[workppass_contact_form]` — renders the full form (inline CSS + vanilla JS), fetches nonce via `wp_nonce_field`, submits via `fetch()` to the REST endpoint
4. **Admin columns** — custom columns (email, phone, topic) on the submissions list screen
5. **Meta box** — detail view for individual submissions showing all stored meta fields
6. **Read-only notice** — admin notice on the edit screen reminding that submissions are read-only

**Meta fields stored per submission**: `_contact_name`, `_contact_email`, `_contact_phone`, `_contact_company`, `_contact_topic`, `_contact_message`, `_contact_ip`, `_contact_date`

**Allowed topics**: `support`, `sales`, `partnership`, `other`

## Repository Structure

```
wp/                        # Docker Compose stack
  docker-compose.yml
  .env                     # DB credentials and ports
  uploads.ini              # PHP upload size override
  www/                     # WordPress root (gitignored)

code/plugins/
  workppass-contact/
    workppass-contact.php  # Complete plugin, single file

docs/                      # Training reference docs (Markdown, Thai/English)
  WordPress_Training_IT_2Days.md   # Master training index with links
  *.md                     # Topic-specific reference files
```

## Gitignore Notes

- `wp/www/` — WordPress core and all installed content is excluded
- `*.sql` — database dumps are excluded
- `wp/.env` — currently committed (training project exception; normally excluded)
