TUNU FOOD DELIVERY SYSTEM (CBE Dar es Salaam)

![alt text](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)


![alt text](https://img.shields.io/badge/License-MIT-green.svg)


![alt text](https://img.shields.io/badge/Hosted-InfinityFree-orange.svg)

TUNU FOOD DELIVERY is a full-stack digital solution developed specifically for the College of Business Education (CBE) Dar es Salaam campus. The system allows students and staff to order meals from the campus kitchen through their smartphones and laptops, eliminating long queues and ensuring fast delivery.

🌐 Live Demo: tunudelivery.ct.ws

📱 Mobile-Driven Development

What makes this project unique is that it was developed 100% on a mobile device.

IDE: A-CODE (Primary mobile code editor).

Web Server: KSWEB (Local PHP/MySQL server on Android).

FTP/Deployment: Deployed directly from mobile to InfinityFree.

✨ Features
👤 User Side

Mobile-First Interface: Dynamic layout with a floating bottom navigation dock for mobile and a traditional top menu for desktop.

Menu Management: Browse various meal categories (Main Dish, Drinks).

Asynchronous Basket: Uses Vanilla JavaScript Fetch API to add items to the basket without reloading the page.

Secure Payments:

ZenoPay API: Real-time USSD push for M-Pesa, Tigo Pesa, Airtel Money, and Halopesa.

COD: Cash on Delivery option.

Order History: Track the status of your meals (Received, Cooking, Delivered).

🔑 Admin Side

Dashboard: Overview of total orders, customers, and successful revenue.

Product CRUD: Fully functional Create, Read, Update, and Delete operations for menu items via Image URLs.

Order Management: Real-time order tracking and status updating.

Access Control: Role-based authentication ensures only admins can access the backend.

🛠️ Tech Stack

Frontend: HTML5, CSS3, Native (Vanilla) JavaScript.

Backend: PHP (Server-side processing).

Database: MySQL (Relational database management).

Integration: ZenoPay API (Mobile Money Tanzania).

📊 Database Design (3NF)

The system uses a normalized database consisting of 6 related tables:

users - Stores credentials and roles (Admin/User).

categories - Groups food items.

products - Stores meal details, prices, and image URLs.

orders - Tracks order numbers and totals.

order_items - Links products to specific orders (Many-to-Many).

payments - Logs transaction references from ZenoPay.

🚀 Installation & Setup
Prerequisites

Local: XAMPP (PC) or KSWEB (Mobile).

Remote: Any cPanel/Apache hosting (e.g., InfinityFree).

Steps

Clone the Repository:

code
Bash
download
content_copy
expand_less
git clone https://github.com/tunucoder/tunu_delivery-

Database Setup:

Open PHPMyAdmin.

Create a database named kadili_food_db.

Import the kadili_food_db.sql file provided in the repository.

Configuration:

Open config.php.

Update your database credentials:

code
PHP
download
content_copy
expand_less
$conn = mysqli_connect("localhost", "root", "", "kadili_food_db");

ZenoPay API:

Replace YOUR_API_KEY in config.php with your actual ZenoPay credential.

🛡️ Security Implementation

Password Hashing: Uses password_hash() with BCRYPT for user data protection.

SQL Injection Prevention: All inputs are sanitized using mysqli_real_escape_string.

Session Protection: Critical pages are restricted based on user roles and session states.

👨‍💻 Author
TUNU JULIUS GEDO

Role: Full-Stack Developer

Email: tunucoder@gmail.com

WhatsApp: 0618665029

College: College of Business Education (CBE)

Created for the Full-Stack Website Development (From Scratch) assignment at CBE. All rights reserved. © 2026.
