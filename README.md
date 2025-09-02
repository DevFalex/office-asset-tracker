# 🖥️ Office Asset Tracker

## 📌 Introduction
The **Office Asset Tracker** is a web-based system designed to help organizations manage office assets effectively.  
It supports two roles: **Admin** and **Staff**, each with specific permissions.

---

## ⚙️ System Requirements
- **Server**: XAMPP / WAMP / LAMP (PHP 7.4+)  
- **Database**: MySQL 5.7+  
- **Browser**: Chrome / Edge / Firefox  

---

## 🚀 Installation & Setup
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
