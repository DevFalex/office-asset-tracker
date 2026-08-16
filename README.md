# 🖥️ Office Asset Tracker

## 📌 Introduction
The **Office Asset Tracker** is a web-based system designed to help organizations manage office assets effectively.  
It supports two roles: **Admin** and **Staff**, each with specific permissions.

## ⚙️ System Requirements
- **Server**: XAMPP / WAMP / LAMP (PHP 7.4+)  
- **Database**: MySQL 5.7+  
- **Browser**: Chrome / Edge / Firefox  

## ☁️ Deploy Online (Railway)
The app is container-ready and reads its database settings from environment variables, so it can be hosted on [Railway](https://railway.app) with a managed MySQL — no code changes needed.

**Steps:**
1. Push this repository to GitHub (already done for this project).
2. In Railway: **New Project → Deploy from GitHub repo** and pick this repo. Railway detects the `Dockerfile` and builds the PHP app.
3. In the same project: **New → Database → Add MySQL**. Railway provisions a managed MySQL instance.
4. Open the **app service → Variables** and add a reference to the database. The simplest option is a single variable:
   - `MYSQL_URL` = `${{MySQL.MYSQL_URL}}`

   (Alternatively add the discrete vars `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, each referencing `${{MySQL.<same name>}}`.)
5. On the app service, open **Settings → Networking → Generate Domain** to get a public URL.
6. Visit the URL. On first load the app auto-creates its tables and sample data (from `office-asset-tracker/schema.sql`), then shows the login page.

The database connection is defined in `office-asset-tracker/db.php`, which resolves config from `MYSQL_URL`/`DATABASE_URL`, then Railway's `MYSQL*` / generic `DB_*` variables, and finally falls back to local `localhost`/`root` defaults for XAMPP.

### Run the full stack locally with Docker
```bash
docker compose up --build
# then open http://localhost:8080
```
This starts the PHP app and a MySQL container together; the schema and sample data load automatically on first run.

## 🚀 Installation & Setup (Local XAMPP/LAMP)
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
