<?php
/**
 * رأس الصفحة
 * Header Component
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="منصة IPTV للبث المباشر للقنوات التلفزيونية">
    <meta name="theme-color" content="#1a1a2e">
    <title><?php echo SITE_NAME; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    
    <!-- hls.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="<?php echo SITE_URL; ?>">
                    <span class="logo-icon">📺</span>
                    <span class="logo-text"><?php echo SITE_NAME; ?></span>
                </a>
            </div>
            
            <div class="navbar-menu">
                <ul class="navbar-list">
                    <li><a href="<?php echo SITE_URL; ?>" class="nav-link">الرئيسية</a></li>
                    <li><a href="<?php echo SITE_URL; ?>?category=sports" class="nav-link">رياضة</a></li>
                    <li><a href="<?php echo SITE_URL; ?>?category=news" class="nav-link">أخبار</a></li>
                    <li><a href="<?php echo SITE_URL; ?>?category=movies" class="nav-link">أفلام</a></li>
                    <li><a href="<?php echo SITE_URL; ?>?category=kids" class="nav-link">أطفال</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/matches.php" class="nav-link">المباريات</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/admin.php" class="nav-link admin-link">الإدارة</a></li>
                </ul>
            </div>
            
            <div class="navbar-search">
                <input type="text" id="searchInput" placeholder="ابحث عن قناة..." class="search-box">
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">