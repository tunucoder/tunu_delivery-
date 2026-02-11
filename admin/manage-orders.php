<?php
// Admin - Manage Orders
include '../includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if (!is_admin()) {
    redirect('../index.php');
}

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = sanitize_input($_POST['new_status']);
    
    $update = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        $success = 'Status ya oda imebadilishwa!';
    }
}

// Get all orders
$filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : 'all';

if ($filter === 'all') {
    $query = "SELECT o.*, u.full_name FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              ORDER BY o.created_at DESC";
    $orders = $conn->query($query);
} else {
    $query = "SELECT o.*, u.full_name FROM orders o 
              JOIN users u ON o.user_id = u.user_id 
              WHERE o.order_status = ? 
              ORDER BY o.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $orders = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - TUNU DELIVERY</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo-section">
                    <img src="https://i.ibb.co/GQ5tsrNH/grok-image-1770810532027-removebg-preview.webp" alt="TUNU Logo">
                    <span class="logo-text">TUNU Admin</span>
                </div>
                
                <ul class="nav-links">
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="manage-orders.php">Oda</a></li>
                    <li><a href="manage-menu.php">Menu</a></li>
                    <li><a href="manage-users.php">Customers</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
                
                <button class="menu-toggle">☰</button>
            </nav>
        </div>
    </header>

    <section class="admin-panel">
        <div class="container">
            <h2 class="section-title">Manage Orders</h2>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="admin-nav">
                <a href="manage-orders.php">Zote</a>
                <a href="manage-orders.php?status=pending">Zinazosubiri</a>
                <a href="manage-orders.php?status=confirmed">Zimethibitishwa</a>
                <a href="manage-orders.php?status=preparing">Zinaandaliwa</a>
                <a href="manage-orders.php?status=out_for_delivery">Njiani</a>
                <a href="manage-orders.php?status=delivered">Zimewasilishwa</a>
            </div>
            
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Simu</th>
                        <th>Tarehe</th>
                        <th>Jumla</th>
                        <th>Malipo</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($orders->num_rows === 0): ?>
                        <tr><td colspan="8" style="text-align: center;">Hakuna oda</td></tr>
                    <?php else: ?>
                        <?php while($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="order-details.php?order_id=<?php echo $order['order_id']; ?>">
                                    #<?php echo $order['order_id']; ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['phone_number']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>TSH <?php echo number_format($order['total_amount'], 0); ?></td>
                            <td>
                                <?php 
                                $payment_methods = [
                                    'cash_on_delivery' => 'Lipa Mzigo',
                                    'airtel_money' => 'Airtel',
                                    'tigo_pesa' => 'Tigo',
                                    'mpesa' => 'M-Pesa',
                                    'halopesa' => 'Halo'
                                ];
                                echo $payment_methods[$order['payment_method']];
                                ?>
                            </td>
                            <td><?php echo $order['order_status']; ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <select name="new_status" class="quantity-input" onchange="this.form.submit()">
                                        <option value="">Chagua...</option>
                                        <option value="pending">Inasubiri</option>
                                        <option value="confirmed">Imethibitishwa</option>
                                        <option value="preparing">Inaandaliwa</option>
                                        <option value="out_for_delivery">Njiani</option>
                                        <option value="delivered">Imewasilishwa</option>
                                        <option value="cancelled">Imesitishwa</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 TUNU DELIVERY. Created by Kadili Dev.</p>
            </div>
        </div>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>
