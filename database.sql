-- TUNU DELIVERY Database
-- Modern Food Delivery System
-- Created by Kadili Dev
-- Email: kadiliy17@gmail.com
-- Contact: 0618240534

CREATE DATABASE IF NOT EXISTS tunu_delivery;
USE tunu_delivery;

-- Users Table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    address TEXT,
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    role ENUM('customer', 'admin') DEFAULT 'customer',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    image_url VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menu Items Table
CREATE TABLE menu_items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2) DEFAULT NULL,
    image_url VARCHAR(255),
    preparation_time INT DEFAULT 30,
    is_featured TINYINT(1) DEFAULT 0,
    is_available TINYINT(1) DEFAULT 1,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    total_orders INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 0.00,
    discount_amount DECIMAL(10, 2) DEFAULT 0.00,
    final_amount DECIMAL(10, 2) NOT NULL,
    delivery_address TEXT NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    payment_method ENUM('cash_on_delivery', 'airtel_money', 'tigo_pesa', 'mpesa', 'halopesa') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    order_status ENUM('pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_phone VARCHAR(15),
    order_notes TEXT,
    delivery_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    special_instructions TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- Reviews Table
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- Favorites Table
CREATE TABLE favorites (
    favorite_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, item_id)
);

-- Insert Default Admin
INSERT INTO users (full_name, email, phone, password_hash, role) 
VALUES ('Admin', 'kadiliy17@gmail.com', '0618240534', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Default password: admin123 (CHANGE THIS!)

-- Insert Sample Categories
INSERT INTO categories (category_name, description, icon, display_order) VALUES
('Pilau', 'Pilau ya kupendeza', '🍛', 1),
('Wali na Maharage', 'Wali wa rangi na maharage', '🍚', 2),
('Chips & Chicken', 'Chipsi na kuku', '🍗', 3),
('Ugali & Samaki', 'Ugali na samaki wa kupendeza', '🐟', 4),
('Biriyani', 'Biriyani ya Indian', '🍛', 5),
('Drinks', 'Vinywaji', '🥤', 6);

-- Insert Sample Menu Items
INSERT INTO menu_items (category_id, item_name, description, price, discount_price, preparation_time, is_featured, image_url) VALUES
(1, 'Pilau ya Kuku', 'Pilau ya kuku wenye mchuzi mzuri', 8000.00, 7000.00, 30, 1, 'food1.jpg'),
(2, 'Wali na Maharage', 'Wali wa rangi na maharage ya nazi', 5000.00, NULL, 25, 1, 'food2.jpg'),
(3, 'Chips na Quarter', 'Chipsi za viazi na quarter chicken', 10000.00, 9000.00, 20, 1, 'food3.jpg'),
(4, 'Ugali na Samaki', 'Ugali na samaki wa kukaanga', 12000.00, NULL, 35, 0, 'food4.jpg'),
(5, 'Biriyani ya Nyama', 'Biriyani ya nyama ya ng\'ombe', 15000.00, 13000.00, 40, 1, 'food5.jpg'),
(6, 'Juice ya Maembe', 'Juice fresh ya maembe', 3000.00, NULL, 5, 0, 'food6.jpg');
