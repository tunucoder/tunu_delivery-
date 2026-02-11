<?php
// Admin Login Page
include '../includes/config.php';

// Redirect if already logged in as admin
if (is_admin()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Tafadhali jaza email na password';
    } else {
        $query = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                redirect('dashboard.php');
            } else {
                $error = 'Email au password sio sahihi';
            }
        } else {
            $error = 'Akaunti ya admin haijapatikana';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TUNU DELIVERY</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 20px;
        }
        
        .admin-login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
        }
        
        .admin-login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark-color);
        }
        
        .admin-badge {
            background: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .logo-center {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-center img {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .logo-center h3 {
            color: var(--primary-color);
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="logo-center">
                <img src="https://i.ibb.co/GQ5tsrNH/grok-image-1770810532027-removebg-preview.webp" alt="TUNU Logo">
                <h3>CBE FOOD DELIVERY</h3>
                <span class="admin-badge">ADMIN PANEL</span>
            </div>
            
            <h2>Admin Login</h2>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email ya Admin</label>
                    <input type="email" name="email" required placeholder="admin@example.com">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 16px;">
                    🔐 Ingia Admin Panel
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                <p style="color: #7f8c8d; font-size: 14px;">
                    <a href="../index.php" style="color: var(--primary-color);">← Rudi kwenye Website</a>
                </p>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 13px; color: #7f8c8d;">
                <strong>Default Admin Account:</strong><br>
                Email: kadiliy17@gmail.com<br>
                Password: admin123<br>
                <small style="color: var(--danger-color);">⚠️ Update password baada ya kuingia!</small>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
