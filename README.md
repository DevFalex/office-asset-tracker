# 🖥️ Office Asset Tracker

## 📌 Introduction
The **Office Asset Tracker** is a web-based system designed to help organizations manage office assets effectively.  
It supports two roles: **Admin** and **Staff**, each with specific permissions.

## ⚙️ System Requirements
- **Server**: XAMPP / WAMP / LAMP (PHP 7.4+)  
- **Database**: MySQL 5.7+  
- **Browser**: Chrome / Edge / Firefox  

## ☁️ Deploy Online (Render + Neon)
The app is container-ready and uses **PostgreSQL** (via a small PDO layer), so it can be hosted for free on [Render](https://render.com) with a [Neon](https://neon.tech) database. Database settings come from a single `DATABASE_URL` environment variable — no code changes needed.

**1. Create the database (Neon):**
1. Sign up at [neon.tech](https://neon.tech) and create a project.
2. Copy the project's **connection string** (looks like `postgresql://user:password@ep-xxx.region.aws.neon.tech/neondb?sslmode=require`).

**2. Deploy the app (Render):**
1. Push this repo to GitHub (already done for this project).
2. In Render: **New → Web Service → Build and deploy from a Git repository**, pick this repo.
3. Render reads `render.yaml` / the `Dockerfile` and builds the PHP image (Runtime: **Docker**).
4. Under **Environment**, add a variable:
   - `DATABASE_URL` = *your Neon connection string*
5. Create the service. On first request the app auto-creates its tables and sample data (from `office-asset-tracker/schema.sql`), then shows the login page at the Render URL.

The database connection lives in `office-asset-tracker/db.php`, which resolves config from `DATABASE_URL`, then discrete `PG*` variables, and finally local defaults. Render injects `$PORT`, which the container's entrypoint binds Apache to.

### Run the full stack locally with Docker
```bash
docker compose up --build
# then open http://localhost:8080
```
This starts the PHP app and a PostgreSQL container together; the schema and sample data load automatically on first run.

## 🚀 Installation & Setup (Local, original MySQL version)
> Note: the online build above uses PostgreSQL. The steps below describe the original XAMPP/MySQL setup; `office_asset_tracker.sql` is the MySQL dump.

1. Copy the project folder (`office_asset_tracker`) into your server’s **htdocs** (XAMPP) or **www** directory.  
2. Import the SQL script: `office_asset_tracker.sql` into **phpMyAdmin**.  
   - This will create required tables and insert sample data (Admin, Staff, Assets).  
3. Update database connection in **`db.php`** if necessary:  

   ```php
   $servername = "localhost";
   $username   = "root";
   $password   = "";
   $dbname     = "office_asset_tracker";

4. Launch in your browser:
👉 http://localhost/office_asset_tracker/login.php

## 👥 User Roles
## 🔹 Admin

Manage Assets (Add, View, Edit, Delete)

Manage Staff (Add, View, Delete)

Assign Assets to staff

Mark Assets as Returned

Generate Reports (Available, In Use, Under Repair, Disposed)

## 🔹 Staff

Login to view assigned assets

See asset details (name, serial number, status, assigned date)

(Optional) Request return or report faulty asset

## 🔑 Login Details (Sample)

Admin

Username: admin
Password: mit_admin123

## 📊 Features
## 🖥 Admin Dashboard

Summary cards for asset status (Available, In Use, Under Repair, Disposed).
Quick links to Assets, Staff, Assignments, Reports.

## 📦 Manage Assets

Add new assets (Laptop, Printer, Router, etc.).
View assets in a searchable table.
Edit or Delete assets.
Status auto-updates (e.g., when assigned).

## 👨‍💼 Manage Staff

Add new staff with username & password.
Staff can log in with these credentials.
View or delete staff records.

## 🔄 Asset Assignment

Assign assets to staff with assigned date.
System updates asset status to In Use.
Mark returned → asset becomes Available again.

## 📊 Reports

View reports by asset status.
See staff assignment history.

## 👩‍💻 Staff Dashboard

Staff sees only their assigned assets.
Asset details: name, serial, date assigned, status.
