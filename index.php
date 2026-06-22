<?php
require_once 'config.php';
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>منصة IPTV</h1>
            <p>استمتع بمئات القنوات التلفزيونية المباشرة</p>
        </div>
    </section>

    <!-- Categories Navigation -->
    <section class="categories-nav">
        <div class="categories-list">
            <a href="?" class="category-btn active" data-category="all">
                📺 الكل
            </a>
            <a href="?category=sports" class="category-btn" data-category="sports">
                ⚽ رياضة
            </a>
            <a href="?category=news" class="category-btn" data-category="news">
                📰 أخبار
            </a>
            <a href="?category=movies" class="category-btn" data-category="movies">
                🎬 أفلام
            </a>
            <a href="?category=kids" class="category-btn" data-category="kids">
                🎨 أطفال
            </a>
        </div>
    </section>

    <!-- Channels Grid -->
    <section class="channels-section">
        <h2 class="section-title">القنوات المتاحة</h2>
        <div id="channelsGrid" class="channels-grid">
            <!-- Channels will be loaded here -->
        </div>
        <div id="noChannels" class="no-data" style="display: none;">
            <p>لا توجد قنوات متاحة</p>
        </div>
    </section>
</div>

<!-- Channel Modal -->
<div id="channelModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="player-container">
            <video id="videoPlayer" class="video-player" controls></video>
        </div>
        <div class="channel-info">
            <div class="info-header">
                <img id="modalLogo" src="" alt="Logo" class="channel-logo-modal">
                <div class="info-text">
                    <h2 id="modalTitle"></h2>
                    <p id="modalCategory"></p>
                </div>
            </div>
            <p id="modalDescription" class="channel-description"></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="<?php echo SITE_URL; ?>/js/hls.min.js"></script>
<script src="<?php echo SITE_URL; ?>/js/main.js"></script>
