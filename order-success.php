<?php
// Order Success Page
include 'includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = $_SESSION['user_id'];

// Get order details
$query = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    redirect('index.php');
}

// Get order items
$query = "SELECT oi.*, mi.item_name FROM order_items oi 
          JOIN menu_items mi ON oi.item_id = mi.item_id 
          WHERE oi.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful - TUNU DELIVERY</title>
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
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                
                <button class="menu-toggle">☰</button>
            </nav>
        </div>
    </header>

    <section class="cart-page">
        <div class="container">
            <div style="text-align: center; max-width: 600px; margin: 0 auto;">
                <div style="font-size: 72px; color: var(--success-color);">✓</div>
                <h2 class="section-title">Oda Yako Imefanikiwa!</h2>
                
                <div class="alert alert-success">
                    Asante kwa kuoda kwenye TUNU DELIVERY!<br>
                    Namba ya oda yako ni: <strong>#<?php echo $order_id; ?></strong>
                </div>
                
                <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin: 30px 0; text-align: left;">
                    <h3>Muhtasari wa Oda</h3>
                    
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Chakula</th>
                                <th>Quantity</th>
                                <th>Bei</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($item = $items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>TSH <?php echo number_format($item['price'], 0); ?></td>
                                <td>TSH <?php echo number_format($item['subtotal'], 0); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <div class="cart-total">
                        Total: TSH <?php echo number_format($order['total_amount'], 0); ?>
                    </div>
                    
                    <hr>
                    
                    <div style="margin-top: 20px;">
                        <p><strong>Delivery Address:</strong><br>
                        <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                        
                        <p><strong>Simu:</strong> <?php echo htmlspecialchars($order['phone_number']); ?></p>
                        
                        <p><strong>Payment Method:</strong> 
                        <?php 
                        $payment_methods = [
                            'cash_on_delivery' => 'Lipa Mzigo Unapofika',
                            'airtel_money' => 'Airtel Money',
                            'tigo_pesa' => 'Tigo Pesa',
                            'mpesa' => 'M-Pesa',
                            'halopesa' => 'HaloPesa'
                        ];
                        echo $payment_methods[$order['payment_method']];
                        ?></p>
                        
                        <p><strong>Status ya Oda:</strong> 
                        <span style="color: var(--secondary-color);">
                            <?php 
                            $statuses = [
                                'pending' => 'Inasubiri',
                                'confirmed' => 'Imethibitishwa',
                                'preparing' => 'Inaandaliwa',
                                'out_for_delivery' => 'Njiani',
                                'delivered' => 'Imewasilishwa'
                            ];
                            echo $statuses[$order['order_status']];
                            ?>
                        </span></p>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <p>Tutawasiliana nawe hivi karibuni kuhusu oda yako.</p>
                    <p>Kwa maswali, wasiliana nasi: <strong>0618240534</strong></p>
                </div>
                
                <div style="margin-top: 30px;">
                    <a href="index.php" class="btn btn-primary">Rudi Nyumbani</a>
                    <a href="orders.php" class="btn">View My Orders</a>
                </div>
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
