<?php
require_once '../config.php';
requireAdmin();

$pdo = getDBConnection();
$success = '';
$error = '';

// Handle form submission
if ($_POST) {
    try {
        $whatsappNumber = sanitizeInput($_POST['whatsapp_number'] ?? '');
        $phoneNumber = sanitizeInput($_POST['phone_number'] ?? '');
        
        // Validate phone numbers
        if ($whatsappNumber && !preg_match('/^\+?[1-9]\d{1,14}$/', $whatsappNumber)) {
            $error = 'Geçersiz WhatsApp numarası formatı.';
        } elseif ($phoneNumber && !preg_match('/^\+?[1-9]\d{1,14}$/', $phoneNumber)) {
            $error = 'Geçersiz telefon numarası formatı.';
        } else {
            // Update contact info
            $stmt = $pdo->prepare("UPDATE contact_info SET whatsapp_number = ?, phone_number = ?, updated_at = NOW() WHERE id = 1");
            $stmt->execute([$whatsappNumber, $phoneNumber]);
            
            $success = 'İletişim bilgileri başarıyla güncellendi!';
        }
    } catch (Exception $e) {
        $error = 'Güncelleme sırasında bir hata oluştu: ' . $e->getMessage();
    }
}

// Fetch current contact info
$contactStmt = $pdo->prepare("SELECT * FROM contact_info ORDER BY id DESC LIMIT 1");
$contactStmt->execute();
$contact = $contactStmt->fetch();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim Bilgileri - Mağusa Taxi Admin</title>
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
                <li><a href="contact.php" class="active"><i class="fas fa-phone"></i> İletişim Bilgileri</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>İletişim Bilgileri</h1>
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
            
            <form method="POST" action="" class="contact-form">
                <div class="form-section">
                    <h2><i class="fas fa-phone"></i> Telefon Bilgileri</h2>
                    
                    <div class="form-group">
                        <label for="whatsapp_number">WhatsApp Numarası</label>
                        <div class="input-group">
                            <i class="fab fa-whatsapp"></i>
                            <input type="tel" id="whatsapp_number" name="whatsapp_number" 
                                   value="<?php echo htmlspecialchars($contact['whatsapp_number'] ?? ''); ?>" 
                                   placeholder="+905331234567">
                        </div>
                        <small>Uluslararası format kullanın (ör: +905331234567)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone_number">Telefon Numarası</label>
                        <div class="input-group">
                            <i class="fas fa-phone"></i>
                            <input type="tel" id="phone_number" name="phone_number" 
                                   value="<?php echo htmlspecialchars($contact['phone_number'] ?? ''); ?>" 
                                   placeholder="+903921234567">
                        </div>
                        <small>Normal arama için telefon numarası</small>
                    </div>
                </div>
                
                <!-- Preview Section -->
                <div class="form-section">
                    <h2><i class="fas fa-eye"></i> Önizleme</h2>
                    
                    <div class="preview-buttons">
                        <div class="preview-button whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            <span><?php echo htmlspecialchars($contact['whatsapp_number'] ?? 'Numara girilmedi'); ?></span>
                        </div>
                        
                        <div class="preview-button phone">
                            <i class="fas fa-phone"></i>
                            <span><?php echo htmlspecialchars($contact['phone_number'] ?? 'Numara girilmedi'); ?></span>
                        </div>
                    </div>
                    
                    <p class="preview-note">
                        <i class="fas fa-info-circle"></i>
                        Bu butonlar ana sayfanın sağ üst köşesinde görünecektir.
                    </p>
                </div>
                
                <!-- Test Section -->
                <div class="form-section">
                    <h2><i class="fas fa-test-tube"></i> Test Et</h2>
                    
                    <div class="test-buttons">
                        <?php if ($contact['whatsapp_number']): ?>
                            <a href="https://wa.me/<?php echo str_replace('+', '', $contact['whatsapp_number']); ?>?text=Test mesajı" 
                               target="_blank" class="btn btn-success">
                                <i class="fab fa-whatsapp"></i> WhatsApp Test Et
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($contact['phone_number']): ?>
                            <a href="tel:<?php echo $contact['phone_number']; ?>" class="btn btn-primary">
                                <i class="fas fa-phone"></i> Telefonu Test Et
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Değişiklikleri Kaydet
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri Dön
                    </a>
                </div>
            </form>
        </main>
    </div>
    
    <script>
        // Real-time preview update
        document.getElementById('whatsapp_number').addEventListener('input', function() {
            document.querySelector('.preview-button.whatsapp span').textContent = 
                this.value || 'Numara girilmedi';
        });
        
        document.getElementById('phone_number').addEventListener('input', function() {
            document.querySelector('.preview-button.phone span').textContent = 
                this.value || 'Numara girilmedi';
        });
        
        // Phone number formatting
        function formatPhoneNumber(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.startsWith('90')) {
                value = '+' + value;
            } else if (value.startsWith('5') || value.startsWith('3')) {
                value = '+90' + value;
            }
            
            input.value = value;
        }
        
        document.getElementById('whatsapp_number').addEventListener('blur', function() {
            formatPhoneNumber(this);
        });
        
        document.getElementById('phone_number').addEventListener('blur', function() {
            formatPhoneNumber(this);
        });
    </script>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>
