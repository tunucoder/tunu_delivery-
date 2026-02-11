<?php
// Checkout Page
include 'includes/config.php';

// Check if user is logged in
if (!is_logged_in()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get user details
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_address = sanitize_input($_POST['delivery_address']);
    $phone_number = sanitize_input($_POST['phone_number']);
    $payment_method = sanitize_input($_POST['payment_method']);
    $payment_phone = sanitize_input($_POST['payment_phone'] ?? '');
    $order_notes = sanitize_input($_POST['order_notes'] ?? '');
    
    // Get cart from POST (sent via JavaScript)
    $cart_json = $_POST['cart_data'];
    $cart = json_decode($cart_json, true);
    
    if (empty($cart)) {
        $error = 'Cart yako ni tupu!';
    } else {
        // Calculate total
        $total_amount = 0;
        foreach ($cart as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert order
            $insert_order = "INSERT INTO orders (user_id, total_amount, delivery_address, phone_number, payment_method, payment_phone, order_notes) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_order);
            $stmt->bind_param("idsssss", $user_id, $total_amount, $delivery_address, $phone_number, $payment_method, $payment_phone, $order_notes);
            $stmt->execute();
            
            $order_id = $conn->insert_id;
            
            // Insert order items
            $insert_item = "INSERT INTO order_items (order_id, item_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_item);
            
            foreach ($cart as $item) {
                $item_id = $item['id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $subtotal = $price * $quantity;
                
                $stmt->bind_param("iiidd", $order_id, $item_id, $quantity, $price, $subtotal);
                $stmt->execute();
            }
            
            $conn->commit();
            
            // Clear cart and redirect
            $success = 'Oda yako imefanikiwa! Namba ya oda: #' . $order_id;
            echo "<script>localStorage.removeItem('cart'); setTimeout(() => { window.location.href = 'order-success.php?order_id=" . $order_id . "'; }, 2000);</script>";
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Kuna tatizo. Jaribu tena.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TUNU DELIVERY</title>
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
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                
                <button class="menu-toggle">☰</button>
            </nav>
        </div>
    </header>

    <section class="cart-page">
        <div class="container">
            <h2 class="section-title">Maliza Oda Yako</h2>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="checkout-form" onsubmit="return validateCheckoutForm()">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <h3>Taarifa za Uwasilishaji</h3>
                        
                        <div class="form-group">
                            <label>Delivery Address</label>
                            <textarea name="delivery_address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone_number" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Maelezo Zaidi (Optional)</label>
                            <textarea name="order_notes" placeholder="Mfano: Piga simu ukifika..."></textarea>
                        </div>
                    </div>
                    
                    <div>
                        <h3>Chagua Payment Method</h3>
                        
                        <input type="hidden" name="payment_method" id="payment-method" required>
                        
                        <div class="payment-methods">
                            <div class="payment-option" data-method="cash_on_delivery" onclick="selectPaymentMethod('cash_on_delivery')">
                                <div style="font-size: 48px;">💵</div>
                                <div><strong>Lipa Mzigo Unapofika</strong></div>
                            </div>
                            
                            <div class="payment-option" data-method="airtel_money" onclick="selectPaymentMethod('airtel_money')">
                                <img src="https://i.ibb.co/zVJrmYn1/images-removebg-preview.webp" alt="Airtel Money">
                                <div><strong>Airtel Money</strong></div>
                                <span class="coming-soon-badge">Coming Soon</span>
                            </div>
                            
                            <div class="payment-option" data-method="tigo_pesa" onclick="selectPaymentMethod('tigo_pesa')">
                                <img src="https://i.ibb.co/FLQ2MVxQ/mixx-logo-removebg-preview.webp" alt="Tigo Pesa">
                                <div><strong>Tigo Pesa</strong></div>
                                <span class="coming-soon-badge">Coming Soon</span>
                            </div>
                            
                            <div class="payment-option" data-method="mpesa" onclick="selectPaymentMethod('mpesa')">
                                <img src="https://i.ibb.co/5Xmzv2kq/M-pesa-logo-removebg-preview.webp" alt="M-Pesa">
                                <div><strong>M-Pesa</strong></div>
                                <span class="coming-soon-badge">Coming Soon</span>
                            </div>
                            
                            <div class="payment-option" data-method="halopesa" onclick="selectPaymentMethod('halopesa')">
                                <img src="https://i.ibb.co/S4mp6TbX/applications-system-removebg-preview.webp" alt="HaloPesa">
                                <div><strong>HaloPesa</strong></div>
                                <span class="coming-soon-badge">Coming Soon</span>
                            </div>
                        </div>
                        
                        <div class="form-group" id="payment-phone-group" style="display: none;">
                            <label>Phone Number ya Malipo</label>
                            <input type="tel" name="payment_phone" id="payment-phone" placeholder="0712345678">
                            <small>Tutakutumia USSD push ili ukamilishe malipo</small>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="cart_data" id="cart-data">
                
                <div style="margin-top: 30px; text-align: right;">
                    <div class="cart-total" style="margin-bottom: 20px;">
                        Total ya Kulipa: <span id="checkout-total">TSH 0</span>
                    </div>
                    <button type="submit" class="btn btn-success" style="font-size: 18px; padding: 15px 40px;">
                        Thibitisha Oda
                    </button>
                </div>
            </form>
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
    <script>
        // Populate cart data for form submission
        window.addEventListener('load', function() {
            const cart = getCart();
            document.getElementById('cart-data').value = JSON.stringify(cart);
            
            // Calculate and display total
            let total = 0;
            cart.forEach(item => {
                total += item.price * item.quantity;
            });
            
            document.getElementById('checkout-total').textContent = 'TSH ' + total.toLocaleString();
            
            // Check if cart is empty
            if (cart.length === 0) {
                alert('Cart yako ni tupu!');
                window.location.href = 'index.php#menu';
            }
        });
    </script>
</body>
</html>
