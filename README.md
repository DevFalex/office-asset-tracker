# 🖥️ Office Asset Tracker

A full-stack web application for managing an organization's office assets — tracking equipment, assigning it to staff, recording returns, and reporting on inventory. Built with a role-based access model (**Admin** and **Staff**) and a clean, responsive dashboard UI.

**🔗 Live demo:** https://office-asset-tracker-1nva.onrender.com

> Try it with the demo accounts below — no signup required.

| Role | Username | Password | What you can do |
|------|----------|----------|-----------------|
| **Admin** | `admin` | `admin123` | Full access — manage assets & staff, assign/return equipment, view reports |
| **Staff** | `Falex` | `staff123` | View only the assets currently assigned to you |

---

## 🧰 Tech Stack

| Layer | Technology |
|-------|-----------|
| **Language** | PHP 8.2 |
| **Database** | PostgreSQL (hosted on [Neon](https://neon.tech)) |
| **DB access** | PDO with prepared statements |
| **Frontend** | Bootstrap 5, Bootstrap Icons, vanilla JS |
| **Web server** | Apache (`php:8.2-apache`) |
| **Containerization** | Docker + Docker Compose |
| **Hosting** | [Render](https://render.com) (Docker web service) + Neon (managed Postgres) |
| **Config** | Environment-driven (`DATABASE_URL`) — 12‑factor style |

---

## ✨ Features

- **Role-based access control** — Admins manage everything; Staff see only their own assigned assets. Server-side guards protect every page.
- **Admin dashboard** — status stat tiles (Available / In Use / Under Repair / Disposed), summary counts, and recent assignments at a glance.
- **Asset management** — add, edit, delete and search assets; status updates automatically on assignment/return.
- **Staff management** — create staff accounts (with hashed passwords), edit and remove them.
- **Assignments** — assign available assets to staff, and mark them returned (which frees the asset again).
- **Reports & analytics** — assets by status (progress bars), assets in use by department, and full assignment history.
- **Responsive UI** — a shared dashboard shell with a collapsible sidebar that works on mobile and desktop.

---

## 🔐 Security

This project was hardened beyond the typical demo app:

- **SQL injection** — every database query uses **parameterized prepared statements**; no user input is concatenated into SQL.
- **Password storage** — passwords are hashed with PHP's `password_hash()` / `password_verify()` (**bcrypt**). Legacy accounts are transparently re-hashed on next login.
- **Cross-site scripting (XSS)** — all user-supplied output is HTML-escaped before rendering.
- **Session safety** — the session ID is regenerated on login to prevent fixation.
- **Access control** — management pages require an Admin session; the staff dashboard requires a Staff session.

> ⚠️ The demo credentials above are intentionally public for portfolio viewing. For a real deployment, use private passwords and don't store production data behind shared logins.

---

## 🗂️ Project Structure

```
office-asset-tracker/
├── Dockerfile              # PHP 8.2 + Apache + pdo_pgsql image
├── docker-compose.yml      # App + PostgreSQL for local development
├── render.yaml             # Render deployment blueprint
├── docker/entrypoint.sh    # Binds Apache to the platform's $PORT
└── office-asset-tracker/
    ├── db.php              # PDO connection + helpers (auth, flash, escaping)
    ├── schema.sql          # Tables + seed data (auto-loaded on first run)
    ├── login.php           # Authentication + landing page
    ├── index.php           # Admin dashboard
    ├── assets.php          # Manage assets
    ├── staff.php           # Manage staff
    ├── assign.php          # Assign / return assets
    ├── reports.php         # Reports & analytics
    ├── staff-dashboard.php # Staff view (own assets)
    ├── edit_asset.php      # Edit an asset
    ├── edit_staff.php      # Edit a staff member
    └── partials/           # Shared layout (sidebar + topbar + footer)
```

---

## 🚀 Run Locally

The quickest way is Docker — it starts the app and a PostgreSQL database together, and loads the schema + sample data automatically on first run:

```bash
docker compose up --build
# then open http://localhost:8080
```

Log in with `admin` / `admin123`.

---

## ☁️ Deploy Online (Render + Neon)

The app is container-ready and reads its database settings from a single `DATABASE_URL` environment variable, so no code changes are needed to host it.

**1. Create the database (Neon):**
1. Sign up at [neon.tech](https://neon.tech) and create a project.
2. Copy the **connection string** (e.g. `postgresql://user:password@ep-xxx.region.aws.neon.tech/neondb?sslmode=require`).

**2. Deploy the app (Render):**
1. In Render: **New → Web Service → Build and deploy from a Git repository**, and pick this repo.
2. Render detects the `Dockerfile` / `render.yaml` and builds the image (Runtime: **Docker**).
3. Under **Environment**, add `DATABASE_URL` = *your Neon connection string*.
4. Create the service. On the first request the app auto-creates its tables and sample data, then shows the login page.

`db.php` resolves its config from `DATABASE_URL`, then discrete `PG*` variables, then local defaults; Render injects `$PORT`, which the container's entrypoint binds Apache to.

---

## 👥 User Roles

**Admin**
- Manage assets (add, view, edit, delete)
- Manage staff (add, view, edit, delete)
- Assign assets to staff and mark them returned
- View reports by status, department, and full history

**Staff**
- Log in to view their own assigned assets (name, serial number, status, assigned date)

---

<p align="center"><sub>Built by DevFalex.</sub></p>
