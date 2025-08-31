<?php
require_once '../config.php';
requireAdmin();

$pdo = getDBConnection();
$success = '';
$error = '';

// Handle form submission
if ($_POST) {
    try {
        $heroTitle = sanitizeInput($_POST['hero_title'] ?? '');
        $heroSubtitle = sanitizeInput($_POST['hero_subtitle'] ?? '');
        $blogContent = $_POST['blog_content'] ?? ''; // Don't sanitize HTML content
        $metaDescription = sanitizeInput($_POST['meta_description'] ?? '');
        $metaKeywords = sanitizeInput($_POST['meta_keywords'] ?? '');
        
        // Update content
        $updates = [
            'hero_title' => $heroTitle,
            'hero_subtitle' => $heroSubtitle,
            'blog_content' => $blogContent,
            'meta_description' => $metaDescription,
            'meta_keywords' => $metaKeywords
        ];
        
        foreach ($updates as $section => $content) {
            $stmt = $pdo->prepare("UPDATE website_content SET content = ?, updated_at = NOW() WHERE section_name = ?");
            $stmt->execute([$content, $section]);
        }
        
        $success = 'İçerik başarıyla güncellendi!';
    } catch (Exception $e) {
        $error = 'Güncelleme sırasında bir hata oluştu: ' . $e->getMessage();
    }
}

// Fetch current content
$contentStmt = $pdo->prepare("SELECT section_name, content FROM website_content");
$contentStmt->execute();
$content = [];
while ($row = $contentStmt->fetch()) {
    $content[$row['section_name']] = $row['content'];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İçerik Yönetimi - Mağusa Taxi Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- TinyMCE for rich text editing -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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
                <li><a href="content.php" class="active"><i class="fas fa-edit"></i> İçerik Yönetimi</a></li>
                <li><a href="images.php"><i class="fas fa-images"></i> Resim Yönetimi</a></li>
                <li><a href="contact.php"><i class="fas fa-phone"></i> İletişim Bilgileri</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Ayarlar</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>İçerik Yönetimi</h1>
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
            
            <form method="POST" action="" class="content-form">
                <!-- Hero Section -->
                <div class="form-section">
                    <h2><i class="fas fa-home"></i> Ana Sayfa Başlık Alanı</h2>
                    
                    <div class="form-group">
                        <label for="hero_title">Ana Başlık</label>
                        <input type="text" id="hero_title" name="hero_title" 
                               value="<?php echo htmlspecialchars($content['hero_title'] ?? ''); ?>" 
                               maxlength="100" required>
                        <small>Ana sayfada görünecek büyük başlık</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="hero_subtitle">Alt Başlık</label>
                        <input type="text" id="hero_subtitle" name="hero_subtitle" 
                               value="<?php echo htmlspecialchars($content['hero_subtitle'] ?? ''); ?>" 
                               maxlength="200">
                        <small>Ana başlığın altında görünecek açıklama</small>
                    </div>
                </div>
                
                <!-- Blog Content -->
                <div class="form-section">
                    <h2><i class="fas fa-blog"></i> Blog İçeriği</h2>
                    
                    <div class="form-group">
                        <label for="blog_content">Blog Yazısı</label>
                        <textarea id="blog_content" name="blog_content" rows="15"><?php echo htmlspecialchars($content['blog_content'] ?? ''); ?></textarea>
                        <small>Resim yanında görünecek blog yazısı. HTML etiketleri kullanabilirsiniz.</small>
                    </div>
                </div>
                
                <!-- SEO Settings -->
                <div class="form-section">
                    <h2><i class="fas fa-search"></i> SEO Ayarları</h2>
                    
                    <div class="form-group">
                        <label for="meta_description">Meta Açıklama</label>
                        <textarea id="meta_description" name="meta_description" rows="3" maxlength="160"><?php echo htmlspecialchars($content['meta_description'] ?? ''); ?></textarea>
                        <small>Google arama sonuçlarında görünecek açıklama (160 karakter)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_keywords">Anahtar Kelimeler</label>
                        <input type="text" id="meta_keywords" name="meta_keywords" 
                               value="<?php echo htmlspecialchars($content['meta_keywords'] ?? ''); ?>">
                        <small>Virgülle ayrılmış anahtar kelimeler (ör: mağusa taxi, kıbrıs taxi)</small>
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
        // Initialize TinyMCE
        tinymce.init({
            selector: '#blog_content',
            height: 400,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
                     alignleft aligncenter alignright alignjustify | \
                     bullist numlist outdent indent | removeformat | help',
            language: 'tr',
            content_style: 'body { font-family: Roboto, sans-serif; font-size:14px }'
        });
        
        // Character counter for meta description
        const metaDesc = document.getElementById('meta_description');
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        metaDesc.parentNode.appendChild(counter);
        
        function updateCounter() {
            const length = metaDesc.value.length;
            counter.textContent = `${length}/160 karakter`;
            counter.style.color = length > 160 ? '#e74c3c' : '#666';
        }
        
        metaDesc.addEventListener('input', updateCounter);
        updateCounter();
    </script>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>
