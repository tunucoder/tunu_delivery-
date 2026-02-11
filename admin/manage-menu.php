<?php
// Admin - Manage Menu
include '../includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if (!is_admin()) {
    redirect('../index.php');
}

$success = '';
$error = '';

// Add new menu item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $category_id = intval($_POST['category_id']);
    $item_name = sanitize_input($_POST['item_name']);
    $description = sanitize_input($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = sanitize_input($_POST['image_url']);
    
    $insert = "INSERT INTO menu_items (category_id, item_name, description, price, image_url) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("issds", $category_id, $item_name, $description, $price, $image_url);
    
    if ($stmt->execute()) {
        $success = 'Chakula kimeongezwa!';
    } else {
        $error = 'Kuna tatizo. Jaribu tena.';
    }
}

// Delete menu item
if (isset($_GET['delete'])) {
    $item_id = intval($_GET['delete']);
    $conn->query("DELETE FROM menu_items WHERE item_id = $item_id");
    $success = 'Chakula kimefutwa!';
}

// Toggle availability
if (isset($_GET['toggle'])) {
    $item_id = intval($_GET['toggle']);
    $conn->query("UPDATE menu_items SET is_available = NOT is_available WHERE item_id = $item_id");
    $success = 'Status imebadilishwa!';
}

// Get categories
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");

// Get all menu items
$menu_items = $conn->query("SELECT m.*, c.category_name FROM menu_items m 
                            JOIN categories c ON m.category_id = c.category_id 
                            ORDER BY c.category_name, m.item_name");
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu - TUNU DELIVERY</title>
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
            <h2 class="section-title">Manage Menu</h2>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
                <h3>Ongeza Chakula Kipya</h3>
                <form method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Aina ya Chakula</label>
                            <select name="category_id" required>
                                <option value="">Chagua aina...</option>
                                <?php 
                                $categories->data_seek(0);
                                while($cat = $categories->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $cat['category_id']; ?>">
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Jina la Chakula</label>
                            <input type="text" name="item_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Maelezo</label>
                            <textarea name="description" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Bei (TSH)</label>
                            <input type="number" name="price" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Jina la Picha (mfano: food1.jpg)</label>
                            <input type="text" name="image_url" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_item" class="btn btn-primary">Ongeza Chakula</button>
                </form>
            </div>
            
            <h3>Menu Yote</h3>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Picha</th>
                        <th>Jina</th>
                        <th>Aina</th>
                        <th>Bei</th>
                        <th>Inapatikana</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $menu_items->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="../images/<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                                 onerror="this.src='../images/placeholder.jpg'">
                        </td>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                        <td>TSH <?php echo number_format($item['price'], 0); ?></td>
                        <td>
                            <?php echo $item['is_available'] ? 
                                '<span style="color: var(--success-color);">✓ Ndio</span>' : 
                                '<span style="color: var(--danger-color);">✗ Hapana</span>'; ?>
                        </td>
                        <td>
                            <a href="?toggle=<?php echo $item['item_id']; ?>" class="btn">
                                <?php echo $item['is_available'] ? 'Funga' : 'Fungua'; ?>
                            </a>
                            <a href="?delete=<?php echo $item['item_id']; ?>" 
                               class="btn" 
                               style="background: var(--danger-color); color: white;"
                               onclick="return confirm('Una uhakika?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
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
