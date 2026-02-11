<?php
// Homepage - TUNU DELIVERY (English Version)
// Created by Kadili Dev | kadiliy17@gmail.com | 0618240534

include 'includes/config.php';

if (!$conn) {
    die("Database connection failed. Please import database.sql!");
}

$menu_query = "SELECT m.*, c.category_name FROM menu_items m 
               JOIN categories c ON m.category_id = c.category_id 
               WHERE m.is_available = 1 
               ORDER BY c.category_id, m.item_name";

$menu_items = $conn->query($menu_query);

if (!$menu_items) {
    die("Error: " . $conn->error . "<br>Please import database.sql!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TUNU DELIVERY - Delicious Food, Fast Delivery!</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo-section">
                    <img src="https://i.ibb.co/GQ5tsrNH/grok-image-1770810532027-removebg-preview.webp" alt="TUNU">
                    <span class="logo-text">TUNU</span>
                </div>
                
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#menu">Menu</a></li>
                    <li><a href="#about">About</a></li>
                    <?php if(is_logged_in()): ?>
                        <?php if(is_admin()): ?>
                            <li><a href="admin/dashboard.php">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="orders.php">My Orders</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a href="cart.php">🛒 <span class="cart-count">0</span></a>
                    </li>
                </ul>
                <button class="menu-toggle">☰</button>
            </nav>
        </div>
    </header>

    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to TUNU DELIVERY!</h1>
                <p>Delicious food, fast delivery - Right to your doorstep!</p>
                <a href="#menu" class="btn">Order Now</a>
            </div>
        </div>
    </section>

    <section class="menu-section" id="menu">
        <div class="container">
            <h2 class="section-title">Our Menu</h2>
            
            <div class="menu-grid">
                <?php while($item = $menu_items->fetch_assoc()): ?>
                <div class="menu-item">
                    <img src="images/<?php echo htmlspecialchars($item['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                         onerror="this.src='images/placeholder.jpg'">
                    <div class="menu-item-content">
                        <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="price">TSH <?php echo number_format($item['price'], 0); ?></div>
                        
                        <div class="quantity-selector">
                            <button class="quantity-btn" onclick="decreaseQuantity(<?php echo $item['item_id']; ?>)">-</button>
                            <input type="number" id="quantity-<?php echo $item['item_id']; ?>" class="quantity-input" value="1" min="1" readonly>
                            <button class="quantity-btn" onclick="increaseQuantity(<?php echo $item['item_id']; ?>)">+</button>
                        </div>
                        
                        <button class="btn btn-primary add-to-cart" 
                                data-id="<?php echo $item['item_id']; ?>"
                                data-name="<?php echo htmlspecialchars($item['item_name']); ?>"
                                data-price="<?php echo $item['price']; ?>"
                                style="width: 100%;">
                            Add to Cart
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section class="about-section" id="about">
        <div class="container">
            <h2 class="section-title">About Us</h2>
            <div class="about-content">
                <p>TUNU DELIVERY is a food delivery service that brings delicious, high-quality food right to your doorstep.</p>
                <p>We strive to provide the best service at affordable prices and convenient times.</p>
                <p>Order from us today and experience the difference!</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>TUNU DELIVERY</h3>
                    <p>Delicious food, fast delivery!</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="#menu">Menu</a>
                    <a href="#about">About</a>
                    <a href="admin/login.php">Admin</a>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>Phone: 0618240534</p>
                    <p>Email: kadiliy17@gmail.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 TUNU DELIVERY. Created by Kadili Dev.</p>
            </div>
        </div>
    </footer>
    <script src="js/script.js"></script>
    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>
</html>
