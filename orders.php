<?php
// User Orders Page
include 'includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get all user orders
$query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - TUNU DELIVERY</title>
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
                    <li><a href="orders.php">My Orders</a></li>
                    <li><a href="logout.php">Logout</a></li>
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
            <h2 class="section-title">My Orders</h2>
            
            <?php if($orders->num_rows === 0): ?>
                <div class="alert alert-info">
                    Bado hujawahi kuoda. <a href="index.php#menu">Anza kuoda sasa!</a>
                </div>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Namba ya Oda</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Malipo</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>TSH <?php echo number_format($order['total_amount'], 0); ?></td>
                            <td>
                                <?php 
                                $payment_methods = [
                                    'cash_on_delivery' => 'Lipa Mzigo',
                                    'airtel_money' => 'Airtel Money',
                                    'tigo_pesa' => 'Tigo Pesa',
                                    'mpesa' => 'M-Pesa',
                                    'halopesa' => 'HaloPesa'
                                ];
                                echo $payment_methods[$order['payment_method']];
                                ?>
                            </td>
                            <td>
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
                                
                                echo '<span style="color: ' . $status_colors[$order['order_status']] . '; font-weight: bold;">' . 
                                     $statuses[$order['order_status']] . '</span>';
                                ?>
                            </td>
                            <td>
                                <a href="order-details.php?order_id=<?php echo $order['order_id']; ?>" class="btn">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
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
