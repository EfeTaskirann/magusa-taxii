<?php
require_once '../config.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_POST) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION[ADMIN_SESSION_NAME] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre.';
        }
    } else {
        $error = 'Lütfen tüm alanları doldurun.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mağusa Taxi - Admin Girişi</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-form">
            <div class="login-header">
                <i class="fas fa-taxi"></i>
                <h2>Mağusa Taxi Admin</h2>
                <p>Yönetim paneline giriş yapın</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Giriş Yap
                </button>
            </form>
            
            <div class="login-footer">
                <a href="../index.php">← Ana Sayfaya Dön</a>
            </div>
        </div>
    </div>
    
    <script>
        // Focus on username field
        document.getElementById('username').focus();
        
        // Add enter key support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.login-btn').click();
            }
        });
    </script>
</body>
</html>
