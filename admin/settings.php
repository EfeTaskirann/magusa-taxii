<?php
require_once '../config.php';
requireAdmin();

$pdo = getDBConnection();
$success = '';
$error = '';

// Handle password change
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if ($newPassword !== $confirmPassword) {
        $error = 'Yeni şifreler eşleşmiyor.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Şifre en az 6 karakter olmalıdır.';
    } else {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($currentPassword, $user['password'])) {
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $updateStmt->execute([$hashedPassword, $_SESSION['admin_id']]);
            
            $success = 'Şifre başarıyla güncellendi!';
        } else {
            $error = 'Mevcut şifre yanlış.';
        }
    }
}

// Get current admin info
$adminStmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
$adminStmt->execute([$_SESSION['admin_id']]);
$admin = $adminStmt->fetch();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Mağusa Taxi Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-taxi"></i>
                <h3>Mağusa Taxi</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="content.php"><i class="fas fa-edit"></i> İçerik Yönetimi</a></li>
                <li><a href="images.php"><i class="fas fa-images"></i> Resim Yönetimi</a></li>
                <li><a href="contact.php"><i class="fas fa-phone"></i> İletişim Bilgileri</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Ayarlar</h1>
                <a href="../index.php" target="_blank" class="view-site-btn">
                    <i class="fas fa-external-link-alt"></i> Siteyi Görüntüle
                </a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- Account Info -->
            <div class="form-section">
                <h2><i class="fas fa-user"></i> Hesap Bilgileri</h2>
                
                <div class="account-info">
                    <div class="info-item">
                        <label>Kullanıcı Adı:</label>
                        <span><?php echo htmlspecialchars($admin['username']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>E-posta:</label>
                        <span><?php echo htmlspecialchars($admin['email'] ?? 'Belirtilmemiş'); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Hesap Oluşturma:</label>
                        <span><?php echo date('d.m.Y H:i', strtotime($admin['created_at'])); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Son Giriş:</label>
                        <span><?php echo $admin['last_login'] ? date('d.m.Y H:i', strtotime($admin['last_login'])) : 'İlk giriş'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Password Change -->
            <div class="form-section">
                <h2><i class="fas fa-key"></i> Şifre Değiştir</h2>
                
                <form method="POST" action="" class="password-form">
                    <div class="form-group">
                        <label for="current_password">Mevcut Şifre</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Yeni Şifre</label>
                        <div class="input-group">
                            <i class="fas fa-key"></i>
                            <input type="password" id="new_password" name="new_password" required minlength="6">
                        </div>
                        <small>En az 6 karakter olmalıdır</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Yeni Şifre (Tekrar)</label>
                        <div class="input-group">
                            <i class="fas fa-key"></i>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="fas fa-save"></i> Şifreyi Güncelle
                    </button>
                </form>
            </div>
            
            <!-- System Info -->
            <div class="form-section">
                <h2><i class="fas fa-info-circle"></i> Sistem Bilgileri</h2>
                
                <div class="system-info">
                    <div class="info-item">
                        <label>PHP Sürümü:</label>
                        <span><?php echo PHP_VERSION; ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Sunucu:</label>
                        <span><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'; ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Maksimum Dosya Boyutu:</label>
                        <span><?php echo ini_get('upload_max_filesize'); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <label>Bellek Limiti:</label>
                        <span><?php echo ini_get('memory_limit'); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Backup & Maintenance -->
            <div class="form-section">
                <h2><i class="fas fa-tools"></i> Bakım İşlemleri</h2>
                
                <div class="maintenance-actions">
                    <div class="action-item">
                        <div class="action-info">
                            <h4>Veritabanı Yedekleme</h4>
                            <p>Site verilerinin yedeğini alın</p>
                        </div>
                        <button class="btn btn-secondary" onclick="alert('Bu özellik geliştirme aşamasındadır.')">
                            <i class="fas fa-download"></i> Yedek Al
                        </button>
                    </div>
                    
                    <div class="action-item">
                        <div class="action-info">
                            <h4>Önbellek Temizleme</h4>
                            <p>Site önbelleğini temizleyin</p>
                        </div>
                        <button class="btn btn-warning" onclick="clearCache()">
                            <i class="fas fa-broom"></i> Önbellek Temizle
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Password confirmation check
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Şifreler eşleşmiyor');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Clear cache function
        function clearCache() {
            if (confirm('Önbelleği temizlemek istediğinizden emin misiniz?')) {
                // In a real implementation, this would make an AJAX call
                alert('Önbellek temizlendi!');
            }
        }
        
        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            // Remove existing indicator
            let indicator = document.querySelector('.password-strength');
            if (indicator) {
                indicator.remove();
            }
            
            // Add new indicator
            if (password.length > 0) {
                indicator = document.createElement('div');
                indicator.className = `password-strength strength-${strength.level}`;
                indicator.textContent = strength.text;
                this.parentNode.parentNode.appendChild(indicator);
            }
        });
        
        function calculatePasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 6) score++;
            if (password.length >= 8) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            
            if (score < 3) return { level: 'weak', text: 'Zayıf' };
            if (score < 5) return { level: 'medium', text: 'Orta' };
            return { level: 'strong', text: 'Güçlü' };
        }
    </script>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>
