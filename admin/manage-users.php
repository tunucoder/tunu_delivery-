<?php
// Admin - Manage Users
include '../includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if (!is_admin()) {
    redirect('../index.php');
}

// Get all customers
$users = $conn->query("SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simamia Customers - TUNU DELIVERY</title>
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
            <h2 class="section-title">Customers Wote</h2>
            
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jina</th>
                        <th>Email</th>
                        <th>Simu</th>
                        <th>Anwani</th>
                        <th>Alisajiliwa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($users->num_rows === 0): ?>
                        <tr><td colspan="6" style="text-align: center;">Hakuna wateja</td></tr>
                    <?php else: ?>
                        <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><?php echo htmlspecialchars($user['address']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
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
