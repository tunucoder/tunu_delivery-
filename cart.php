<?php
// Cart Page
include 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - TUNU DELIVERY</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo-section">
                    <img src="https://i.ibb.co/GQ5tsrNH/grok-image-1770810532027-removebg-preview.webp" alt="TUNU Logo">
                    <span class="logo-text">TUNU</span>
                </div>
                
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#menu">Menu</a></li>
                    <?php if(is_logged_in()): ?>
                        <li><a href="orders.php">My Orders</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a href="cart.php">🛒 Cart
                            <span class="cart-count">0</span>
                        </a>
                    </li>
                </ul>
                
                <button class="menu-toggle">☰</button>
            </nav>
        </div>
    </header>

    <section class="cart-page">
        <div class="container">
            <h2 class="section-title">Cart Yako</h2>
            
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Chakula</th>
                        <th>Bei</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Futa</th>
                    </tr>
                </thead>
                <tbody id="cart-items">
                    <tr>
                        <td colspan="5" style="text-align: center;">Cart yako ni tupu</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="cart-total">
                Total: <span id="cart-total">TSH 0</span>
            </div>
            
            <div style="text-align: right;">
                <a href="index.php#menu" class="btn">Endelea Kununua</a>
                <a href="checkout.php" class="btn btn-success">Maliza Oda</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 TUNU DELIVERY. Created by Kadili Dev.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
