<?php
require_once '../config.php';
requireAdmin();

$pdo = getDBConnection();

// Get statistics
$contentCount = $pdo->query("SELECT COUNT(*) FROM website_content")->fetchColumn();
$imageCount = $pdo->query("SELECT COUNT(*) FROM slider_images WHERE is_active = 1")->fetchColumn();
$lastUpdate = $pdo->query("SELECT MAX(updated_at) FROM website_content")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mağusa Taxi - Admin Dashboard</title>
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
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="content.php"><i class="fas fa-edit"></i> İçerik Yönetimi</a></li>
                <li><a href="images.php"><i class="fas fa-images"></i> Resim Yönetimi</a></li>
                <li><a href="contact.php"><i class="fas fa-phone"></i> İletişim Bilgileri</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="../index.php" target="_blank" class="view-site-btn">
                        <i class="fas fa-external-link-alt"></i> Siteyi Görüntüle
                    </a>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $contentCount; ?></h3>
                        <p>İçerik Bölümü</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $imageCount; ?></h3>
                        <p>Aktif Resim</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo date('d.m.Y', strtotime($lastUpdate)); ?></h3>
                        <p>Son Güncelleme</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Online</h3>
                        <p>Site Durumu</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Hızlı İşlemler</h2>
                <div class="actions-grid">
                    <a href="content.php" class="action-card">
                        <i class="fas fa-edit"></i>
                        <h3>İçeriği Düzenle</h3>
                        <p>Blog yazısı ve site metinlerini düzenleyin</p>
                    </a>
                    
                    <a href="images.php" class="action-card">
                        <i class="fas fa-upload"></i>
                        <h3>Resim Yükle</h3>
                        <p>Yeni taxi resimleri ekleyin veya mevcut resimleri yönetin</p>
                    </a>
                    
                    <a href="contact.php" class="action-card">
                        <i class="fas fa-phone"></i>
                        <h3>İletişim Güncelle</h3>
                        <p>Telefon numarası ve WhatsApp bilgilerini güncelleyin</p>
                    </a>
                    
                    <a href="../index.php" target="_blank" class="action-card">
                        <i class="fas fa-external-link-alt"></i>
                        <h3>Siteyi Görüntüle</h3>
                        <p>Yaptığınız değişiklikleri canlı sitede kontrol edin</p>
                    </a>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="recent-activity">
                <h2>Son Aktiviteler</h2>
                <div class="activity-list">
                    <div class="activity-item">
                        <i class="fas fa-edit"></i>
                        <div class="activity-info">
                            <p>İçerik güncellendi</p>
                            <small><?php echo date('d.m.Y H:i', strtotime($lastUpdate)); ?></small>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <i class="fas fa-sign-in-alt"></i>
                        <div class="activity-info">
                            <p>Admin paneline giriş yapıldı</p>
                            <small><?php echo date('d.m.Y H:i'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>
