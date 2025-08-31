<?php
require_once 'config.php';

// Handle language selection
$available_languages = ['tr', 'en', 'ru'];
$default_language = 'tr';

// Check if language is set in URL
if(isset($_GET['lang']) && in_array($_GET['lang'], $available_languages)) {
    $current_language = $_GET['lang'];
    // Set cookie to remember language preference
    setcookie('lang', $current_language, time() + (86400 * 30), "/"); // 30 days
} 
// Check if language cookie is set
elseif(isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $available_languages)) {
    $current_language = $_COOKIE['lang'];
} 
// Use default language
else {
    $current_language = $default_language;
}

// Load language file
$translations = include "assets/lang/{$current_language}.php";

// Get database connection
$pdo = getDBConnection();

// Fetch website content
$contentStmt = $pdo->prepare("SELECT section_name, content FROM website_content");
$contentStmt->execute();
$content = [];
while ($row = $contentStmt->fetch()) {
    $content[$row['section_name']] = $row['content'];
}

// Helper to resolve localized content from DB with graceful fallbacks
function getLocalizedContent(string $key, array $content, array $translations, string $lang) {
    $langKey = $key . '_' . $lang; // e.g., hero_title_en
    if (!empty($content[$langKey])) {
        return $content[$langKey];
    }
    if (!empty($content[$key])) {
        return $content[$key];
    }
    // Fallback to translation file entries when available
    return $translations[$key] ?? '';
}

// Fetch contact information
$contactStmt = $pdo->prepare("SELECT * FROM contact_info ORDER BY id DESC LIMIT 1");
$contactStmt->execute();
$contact = $contactStmt->fetch();

// Fetch slider images
$imagesStmt = $pdo->prepare("SELECT * FROM slider_images WHERE is_active = 1 ORDER BY display_order ASC");
$imagesStmt->execute();
$images = $imagesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($translations['site_title']); ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars(getLocalizedContent('meta_description', $content, $translations, $current_language)); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(getLocalizedContent('meta_keywords', $content, $translations, $current_language)); ?>">
    <meta name="author" content="Mağusa Taxi">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Mağusa Taxi - Güvenilir Kıbrıs Taxi Hizmeti">
    <meta property="og:description" content="<?php echo htmlspecialchars($content['meta_description'] ?? ''); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">
    <meta property="og:locale" content="tr_TR">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Mağusa Taxi - Güvenilir Kıbrıs Taxi Hizmeti">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($content['meta_description'] ?? ''); ?>">
    <meta name="twitter:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">
    
    <!-- Schema.org structured data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Mağusa Taxi",
        "description": "Kıbrıs'ta güvenilir taxi hizmeti",
        "url": "<?php echo SITE_URL; ?>",
        "telephone": "<?php echo $contact['phone_number'] ?? ''; ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Mağusa",
            "addressCountry": "Kıbrıs"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "35.1264",
            "longitude": "33.9369"
        },
        "openingHours": "Mo-Su 00:00-23:59",
        "serviceArea": {
            "@type": "Place",
            "name": "Kıbrıs"
        }
    }
    </script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/background.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="index.php">
                <i class="fas fa-taxi"></i>
                <span><?php echo $current_language === 'tr' ? 'Mağusa Taxi' : ($current_language === 'ru' ? 'Фамагуста Такси' : 'Famagusta Taxi'); ?></span>
            </a>
            <nav class="topbar-actions">
                <div class="language-selector inline">
                    <a href="?lang=tr" class="language-btn <?php echo $current_language === 'tr' ? 'active' : ''; ?>">TR</a>
                    <a href="?lang=en" class="language-btn <?php echo $current_language === 'en' ? 'active' : ''; ?>">EN</a>
                    <a href="?lang=ru" class="language-btn <?php echo $current_language === 'ru' ? 'active' : ''; ?>">RU</a>
                </div>
                <div class="cta-buttons">
                    <a class="cta btn-outline" href="https://wa.me/<?php echo str_replace('+', '', $contact['whatsapp_number'] ?? ''); ?>?text=Merhaba, taxi hizmeti almak istiyorum." target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                    <a class="cta btn-solid" href="tel:<?php echo $contact['phone_number'] ?? ''; ?>">
                        <i class="fas fa-phone"></i>
                        <span><?php echo $translations['phone_label']; ?></span>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section with Direct Text on Background -->
    <section class="hero-section">
        <div class="hero-inner">
            <h1><?php echo htmlspecialchars(getLocalizedContent('hero_title', $content, $translations, $current_language)); ?></h1>
            <p class="subtitle"><?php echo htmlspecialchars(getLocalizedContent('hero_subtitle', $content, $translations, $current_language)); ?></p>

            <div class="hero-cta">
                <a class="cta btn-solid" href="tel:<?php echo $contact['phone_number'] ?? ''; ?>">
                    <i class="fas fa-phone"></i>
                    <span><?php echo $translations['phone_label']; ?></span>
                </a>
                <a class="cta btn-outline" href="https://wa.me/<?php echo str_replace('+', '', $contact['whatsapp_number'] ?? ''); ?>?text=Merhaba, taxi hizmeti almak istiyorum." target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
            </div>

            <ul class="hero-chips">
                <li>Lefkoşa</li>
                <li>Girne</li>
                <li>Gazimağusa</li>
                <li>İskele</li>
                <li>Lefke</li>
                <li>Güzelyurt</li>
            </ul>
        </div>
    </section>
    
    <!-- Main Container -->
    <div class="container">

        <!-- Main Content -->
        <main class="main-content">
            <!-- Image Slider Section -->
            <section class="slider-section">
                <div class="slider-container">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $index => $image): ?>
                            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($image['alt_text']); ?>"
                                     loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Slider Navigation -->
                        <div class="slider-nav">
                            <?php foreach ($images as $index => $image): ?>
                                <button class="nav-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                        onclick="goToSlide(<?php echo $index; ?>)"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="slide active">
                            <img src="assets/images/default-taxi.jpg" alt="Mağusa Taxi" loading="eager">
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Blog Content Section -->
            <section class="blog-section">
                <div class="blog-content">
                    <?php
                        $blogHtml = $content['blog_content_' . $current_language] ?? $content['blog_content'] ?? null;
                        if (!$blogHtml) {
                            $blogHtml = '<h2>' . $translations['welcome_title'] . '</h2><p>' . $translations['welcome_content'] . '</p>';
                        }
                        echo $blogHtml;
                    ?>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $current_language === 'tr' ? 'Mağusa Taxi' : ($current_language === 'ru' ? 'Фамагуста Такси' : 'Famagusta Taxi'); ?>. <?php echo $translations['copyright_text']; ?></p>
                <div class="footer-contact">
                    <p><i class="fas fa-phone"></i> <?php echo $translations['phone_label']; ?>: <?php echo htmlspecialchars($contact['phone_number'] ?? ''); ?></p>
                    <p><i class="fab fa-whatsapp"></i> <?php echo $translations['whatsapp_label']; ?>: <?php echo htmlspecialchars($contact['whatsapp_number'] ?? ''); ?></p>
                </div>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/script.js"></script>
    <script src="assets/js/background-slideshow.js"></script>
</body>
</html>