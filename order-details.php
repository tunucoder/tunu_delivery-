<?php
// Order Details Page
include 'includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = $_SESSION['user_id'];

// Check if admin or order owner
if (is_admin()) {
    $query = "SELECT o.*, u.full_name, u.email FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              WHERE o.order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
} else {
    $query = "SELECT o.*, u.full_name, u.email FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              WHERE o.order_id = ? AND o.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $order_id, $user_id);
}

$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    redirect('index.php');
}

// Get order items
$query = "SELECT oi.*, mi.item_name, mi.image_url FROM order_items oi 
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
    <title>Maelezo ya Oda #<?php echo $order_id; ?> - TUNU DELIVERY</title>
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
                    <?php if(is_admin()): ?>
                        <li><a href="admin/dashboard.php">Admin</a></li>
                    <?php else: ?>
                        <li><a href="orders.php">My Orders</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                
                <button class="menu-toggle">☰</button>
            </nav>
        </div>
    </header>

    <section class="cart-page">
        <div class="container">
            <h2 class="section-title">Maelezo ya Oda #<?php echo $order_id; ?></h2>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <div>
                    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
                        <h3>Vitu Vilivyoodwa</h3>
                        
                        <?php while($item = $items->fetch_assoc()): ?>
                        <div style="display: flex; align-items: center; padding: 15px; border-bottom: 1px solid #ddd;">
                            <img src="images/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px; margin-right: 20px;"
                                 onerror="this.src='images/placeholder.jpg'">
                            <div style="flex: 1;">
                                <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
                                <p style="color: #7f8c8d;">
                                    Quantity: <?php echo $item['quantity']; ?> × 
                                    TSH <?php echo number_format($item['price'], 0); ?>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <strong style="color: var(--primary-color); font-size: 18px;">
                                    TSH <?php echo number_format($item['subtotal'], 0); ?>
                                </strong>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        
                        <div class="cart-total" style="margin-top: 20px;">
                            Total: TSH <?php echo number_format($order['total_amount'], 0); ?>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                        <h3>Taarifa za Oda</h3>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>Namba ya Oda:</strong><br>
                            #<?php echo $order['order_id']; ?>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>Date:</strong><br>
                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                        </div>
                        
                        <?php if(is_admin()): ?>
                        <div style="margin-bottom: 15px;">
                            <strong>Mteja:</strong><br>
                            <?php echo htmlspecialchars($order['full_name']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>Simu:</strong><br>
                            <?php echo htmlspecialchars($order['phone_number']); ?>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>Anwani:</strong><br>
                            <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?>
                        </div>
                        
                        <?php if($order['order_notes']): ?>
                        <div style="margin-bottom: 15px;">
                            <strong>Maelezo:</strong><br>
                            <?php echo nl2br(htmlspecialchars($order['order_notes'])); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>Payment Method:</strong><br>
                            <?php 
                            $payment_methods = [
                                'cash_on_delivery' => 'Lipa Mzigo Unapofika',
                                'airtel_money' => 'Airtel Money',
                                'tigo_pesa' => 'Tigo Pesa',
                                'mpesa' => 'M-Pesa',
                                'halopesa' => 'HaloPesa'
                            ];
                            echo $payment_methods[$order['payment_method']];
                            ?>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <strong>Status ya Oda:</strong><br>
                            <?php 
                            $statuses = [
                                'pending' => 'Inasubiri',
                                'confirmed' => 'Imethibitishwa',
                                'preparing' => 'Inaandaliwa',
                                'out_for_delivery' => 'Njiani',
                                'delivered' => 'Imewasilishwa',
                                'cancelled' => 'Imesitishwa'
                            ];
                            
                            $status_colors = [
                                'pending' => '#f39c12',
                                'confirmed' => '#3498db',
                                'preparing' => '#9b59b6',
                                'out_for_delivery' => '#e67e22',
                                'delivered' => '#27ae60',
                                'cancelled' => '#e74c3c'
                            ];
                            
                            echo '<span style="color: ' . $status_colors[$order['order_status']] . '; font-weight: bold; font-size: 18px;">' . 
                                 $statuses[$order['order_status']] . '</span>';
                            ?>
                        </div>
                        
                        <div style="margin-top: 30px;">
                            <?php if(is_admin()): ?>
                                <a href="admin/manage-orders.php" class="btn btn-primary" style="width: 100%;">
                                    Rudi kwenye Oda
                                </a>
                            <?php else: ?>
                                <a href="orders.php" class="btn btn-primary" style="width: 100%;">
                                    Rudi kwenye My Orders
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
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
