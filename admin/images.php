<?php
require_once '../config.php';
requireAdmin();

$pdo = getDBConnection();
$success = '';
$error = '';

// Handle image upload
if (isset($_POST['upload_image'])) {
    $uploadDir = '../assets/images/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'Sadece JPEG, PNG ve WebP formatları desteklenir.';
        } elseif ($file['size'] > $maxSize) {
            $error = 'Dosya boyutu 5MB\'dan küçük olmalıdır.';
        } else {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'taxi_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $targetPath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $altText = sanitizeInput($_POST['alt_text'] ?? 'Mağusa Taxi');
                $displayOrder = (int)($_POST['display_order'] ?? 0);
                
                $stmt = $pdo->prepare("INSERT INTO slider_images (image_path, alt_text, display_order) VALUES (?, ?, ?)");
                $stmt->execute(['assets/images/' . $filename, $altText, $displayOrder]);
                
                $success = 'Resim başarıyla yüklendi!';
            } else {
                $error = 'Dosya yüklenirken bir hata oluştu.';
            }
        }
    } else {
        $error = 'Lütfen bir resim dosyası seçin.';
    }
}

// Handle image deletion
if (isset($_POST['delete_image'])) {
    $imageId = (int)$_POST['image_id'];
    
    // Get image path before deletion
    $stmt = $pdo->prepare("SELECT image_path FROM slider_images WHERE id = ?");
    $stmt->execute([$imageId]);
    $image = $stmt->fetch();
    
    if ($image) {
        // Delete from database
        $deleteStmt = $pdo->prepare("DELETE FROM slider_images WHERE id = ?");
        $deleteStmt->execute([$imageId]);
        
        // Delete file
        $filePath = '../' . $image['image_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $success = 'Resim başarıyla silindi!';
    }
}

// Handle image status toggle
if (isset($_POST['toggle_status'])) {
    $imageId = (int)$_POST['image_id'];
    $newStatus = $_POST['new_status'] === '1' ? 1 : 0;
    
    $stmt = $pdo->prepare("UPDATE slider_images SET is_active = ? WHERE id = ?");
    $stmt->execute([$newStatus, $imageId]);
    
    $success = 'Resim durumu güncellendi!';
}

// Handle order update
if (isset($_POST['update_order'])) {
    $orders = $_POST['order'] ?? [];
    
    foreach ($orders as $imageId => $order) {
        $stmt = $pdo->prepare("UPDATE slider_images SET display_order = ? WHERE id = ?");
        $stmt->execute([(int)$order, (int)$imageId]);
    }
    
    $success = 'Sıralama güncellendi!';
}

// Fetch all images
$imagesStmt = $pdo->prepare("SELECT * FROM slider_images ORDER BY display_order ASC, id DESC");
$imagesStmt->execute();
$images = $imagesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resim Yönetimi - Mağusa Taxi Admin</title>
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
                <li><a href="images.php" class="active"><i class="fas fa-images"></i> Resim Yönetimi</a></li>
                <li><a href="contact.php"><i class="fas fa-phone"></i> İletişim Bilgileri</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Resim Yönetimi</h1>
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
            
            <!-- Upload Form -->
            <div class="form-section">
                <h2><i class="fas fa-upload"></i> Yeni Resim Yükle</h2>
                
                <form method="POST" enctype="multipart/form-data" class="upload-form">
                    <div class="form-group">
                        <label for="image">Resim Dosyası</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                        <small>Desteklenen formatlar: JPEG, PNG, WebP (Maksimum: 5MB)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="alt_text">Resim Açıklaması</label>
                        <input type="text" id="alt_text" name="alt_text" value="Mağusa Taxi" maxlength="255">
                        <small>SEO için önemli olan resim açıklaması</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Sıralama</label>
                        <input type="number" id="display_order" name="display_order" value="0" min="0">
                        <small>Küçük sayılar önce görünür (0 = en önce)</small>
                    </div>
                    
                    <button type="submit" name="upload_image" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Resmi Yükle
                    </button>
                </form>
            </div>
            
            <!-- Images List -->
            <div class="form-section">
                <h2><i class="fas fa-images"></i> Mevcut Resimler</h2>
                
                <?php if (empty($images)): ?>
                    <div class="empty-state">
                        <i class="fas fa-image"></i>
                        <p>Henüz resim yüklenmemiş.</p>
                    </div>
                <?php else: ?>
                    <form method="POST" class="order-form">
                        <div class="images-grid">
                            <?php foreach ($images as $image): ?>
                                <div class="image-card <?php echo $image['is_active'] ? '' : 'inactive'; ?>">
                                    <div class="image-preview">
                                        <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($image['alt_text']); ?>">
                                        
                                        <?php if (!$image['is_active']): ?>
                                            <div class="inactive-overlay">
                                                <i class="fas fa-eye-slash"></i>
                                                <span>Pasif</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="image-info">
                                        <p><strong>Açıklama:</strong> <?php echo htmlspecialchars($image['alt_text']); ?></p>
                                        <p><strong>Yükleme:</strong> <?php echo date('d.m.Y H:i', strtotime($image['uploaded_at'])); ?></p>
                                        
                                        <div class="image-controls">
                                            <div class="order-control">
                                                <label>Sıra:</label>
                                                <input type="number" name="order[<?php echo $image['id']; ?>]" 
                                                       value="<?php echo $image['display_order']; ?>" min="0" max="999">
                                            </div>
                                            
                                            <div class="action-buttons">
                                                <!-- Toggle Status -->
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                                    <input type="hidden" name="new_status" value="<?php echo $image['is_active'] ? '0' : '1'; ?>">
                                                    <button type="submit" name="toggle_status" 
                                                            class="btn <?php echo $image['is_active'] ? 'btn-warning' : 'btn-success'; ?> btn-sm"
                                                            title="<?php echo $image['is_active'] ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                                        <i class="fas <?php echo $image['is_active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                                    </button>
                                                </form>
                                                
                                                <!-- Delete -->
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Bu resmi silmek istediğinizden emin misiniz?');">
                                                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                                    <button type="submit" name="delete_image" class="btn btn-danger btn-sm" title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="update_order" class="btn btn-primary">
                                <i class="fas fa-sort"></i> Sıralamayı Güncelle
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        // Preview uploaded image
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('.image-preview-temp');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'image-preview-temp';
                        preview.innerHTML = '<img style="max-width: 200px; max-height: 150px; border-radius: 8px; margin-top: 10px;">';
                        document.querySelector('.upload-form').appendChild(preview);
                    }
                    preview.querySelector('img').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Drag and drop reordering
        let draggedElement = null;
        
        document.querySelectorAll('.image-card').forEach(card => {
            card.draggable = true;
            
            card.addEventListener('dragstart', function(e) {
                draggedElement = this;
                this.style.opacity = '0.5';
            });
            
            card.addEventListener('dragend', function(e) {
                this.style.opacity = '';
                draggedElement = null;
            });
            
            card.addEventListener('dragover', function(e) {
                e.preventDefault();
            });
            
            card.addEventListener('drop', function(e) {
                e.preventDefault();
                if (draggedElement && draggedElement !== this) {
                    const draggedOrder = draggedElement.querySelector('input[name*="order"]');
                    const targetOrder = this.querySelector('input[name*="order"]');
                    
                    const temp = draggedOrder.value;
                    draggedOrder.value = targetOrder.value;
                    targetOrder.value = temp;
                }
            });
        });
    </script>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>
