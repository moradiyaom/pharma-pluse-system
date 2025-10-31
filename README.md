# 💊 Pharma Pluse System

A complete web-based **Pharmaceutical Management System** built using **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript**.  
It provides a centralized platform for **Admin**, **Company**, and **Customer** users to manage medicines, orders, stock, billing, and reports efficiently.

---

## 🚀 Features

### 🧑‍💼 Admin Panel
- Manage companies, customers, and medicines.
- Add, update, or delete medicine records.
- Approve or reject company registrations.
- View total sales, orders, and transaction history.
- Generate reports and monitor low-stock medicines.

### 🏭 Company Panel
- Add and manage their own medicine stock.
- Update pricing, expiry dates, and available quantities.
- View and process customer orders.
- Monitor total sales and product performance.

### 👨‍⚕️ Customer Panel
- Register, log in, and browse available medicines.
- Search medicines by name, category, or company.
- Add medicines to cart and place orders.
- View order history and download bills.
- Receive email or on-screen order confirmations.

---

## 🛠️ Tech Stack

| Component | Technology |
|------------|-------------|
| **Frontend** | HTML5, CSS3, JavaScript |
| **Backend** | PHP (Core PHP / Procedural) |
| **Database** | MySQL |
| **Server** | XAMPP / WAMP / LAMP |
| **Version Control** | Git / GitHub |

---

## 📁 Folder Structure
pharma-pluse-system/
│
├── admin/ # Admin dashboard pages
│ ├── index.php
│ ├── manage_medicines.php
│ ├── manage_companies.php
│ ├── manage_customers.php
│ ├── reports.php
│ └── logout.php
│
├── company/ # Company panel
│ ├── index.php
│ ├── add_medicine.php
│ ├── manage_stock.php
│ ├── view_orders.php
│ └── profile.php
│
├── customer/ # Customer panel
│ ├── index.php
│ ├── view_medicines.php
│ ├── cart.php
│ ├── checkout.php
│ └── order_history.php
│
├── includes/ # Common files
│ ├── db_connect.php
│ ├── header.php
│ ├── footer.php
│ └── auth.php
│
├── assets/
│ ├── css/
│ │ ├── style.css
│ ├── js/
│ │ ├── main.js
│ └── images/
│ ├── logo.png
│ └── ...
│
├── database/
│ └── pharma_pluse.sql # Database dump file
│
├── index.php # Landing page
├── login.php # Common login
├── register.php # Customer registration
└── README.md


---

## ⚙️ Installation & Setup

### Prerequisites
- Install **XAMPP** or **WAMP** server.
- PHP version 7.4+ recommended.
- MySQL service running.

### Steps
1. **Clone or Download Repository**
   ```bash
   git clone https://github.com/your-username/pharma-pluse-system.git


Move to htdocs Folder

C:\xampp\htdocs\pharma-pluse-system


Import Database

Open phpMyAdmin

Create a new database named:

pharma_pluse


Import the file:

database/pharma_pluse.sql


Configure Database Connection

Open includes/db_connect.php

Update your local DB credentials:

$conn = mysqli_connect("localhost", "root", "", "pharma_pluse");


Run the Application

Open browser and visit:

http://localhost/pharma-pluse-system/

🔐 Default Login Credentials
Role	Email	Password
Admin	admin@pharma.com
	admin123
Company	company@pharma.com
	company123
Customer	customer@pharma.com
	customer123

(You can modify these in phpMyAdmin under the users table.)

📊 Database Tables Overview
Table Name	Description
users	Stores all user login credentials (admin, company, customer)
medicines	Stores medicine details (name, price, expiry, stock)
orders	Customer orders and billing details
order_items	Items within each order
companies	Company registration and info
customers	Customer profiles
payments	Payment and transaction details
🧩 Future Enhancements

Email/SMS notifications for orders.

GST and invoice PDF generation.

Role-based access with JWT (optional).

Stock expiry alerts.

REST API support for mobile app integration.

🖋️ Author

Developed by: [Your Name]
📧 youremail@example.com

💻 https://github.com/your-username
