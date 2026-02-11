<?php
// Admin Dashboard
include '../includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if (!is_admin()) {
    redirect('../index.php');
}

// Get statistics
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid' OR payment_method = 'cash_on_delivery'")->fetch_assoc()['total'] ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'pending'")->fetch_assoc()['count'];

// Get recent orders
$recent_orders = $conn->query("SELECT o.*, u.full_name FROM orders o 
                               JOIN users u ON o.user_id = u.user_id 
                               ORDER BY o.created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TUNU DELIVERY</title>
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
                    <li><a href="../index.php">Website</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
                
                <button class="menu-toggle">☰</button>
            </nav>
        </div>
    </header>

    <section class="admin-panel">
        <div class="container">
            <h2 class="section-title">Admin Dashboard</h2>
            
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Total Orders</p>
                </div>
                
                <div class="dashboard-card">
                    <h3><?php echo $pending_orders; ?></h3>
                    <p>Pending Orders</p>
                </div>
                
                <div class="dashboard-card">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Customers</p>
                </div>
                
                <div class="dashboard-card">
                    <h3>TSH <?php echo number_format($total_revenue, 0); ?></h3>
                    <p>Jumla ya Revenue</p>
                </div>
            </div>
            
            <h3 style="margin-top: 40px;">Oda za Hivi Karibuni</h3>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Tarehe</th>
                        <th>Jumla</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($recent_orders->num_rows === 0): ?>
                        <tr><td colspan="6" style="text-align: center;">Hakuna oda</td></tr>
                    <?php else: ?>
                        <?php while($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>TSH <?php echo number_format($order['total_amount'], 0); ?></td>
                            <td>
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
                            </td>
                            <td>
                                <a href="order-details.php?order_id=<?php echo $order['order_id']; ?>" class="btn">Angalia</a>
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
