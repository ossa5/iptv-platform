<?php
require_once 'config.php';
require_once 'includes/functions.php';

$channel_id = $_GET['id'] ?? null;

if (!$channel_id) {
    header('Location: ' . SITE_URL);
    exit;
}

$channel = get_channel_by_id($channel_id);

if (!$channel) {
    header('Location: ' . SITE_URL);
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="watch-container">
        <!-- Player Section -->
        <div class="player-section">
            <video id="videoPlayer" class="video-player" controls>
                <source src="<?php echo htmlspecialchars($channel['url']); ?>" type="application/x-mpegURL">
            </video>
            <div class="player-overlay" id="playerOverlay" style="display: none;">
                <div class="overlay-message">جاري تحميل المشغل...</div>
            </div>
        </div>

        <!-- Channel Info Section -->
        <div class="info-section">
            <div class="channel-header">
                <img src="<?php echo htmlspecialchars($channel['logo']); ?>" alt="<?php echo htmlspecialchars($channel['name']); ?>" class="channel-logo-large">
                <div class="channel-meta">
                    <h1><?php echo htmlspecialchars($channel['name']); ?></h1>
                    <p class="category-badge"><?php echo get_category_icon($channel['category']); ?> <?php echo htmlspecialchars($channel['category']); ?></p>
                    <p class="channel-country">🌍 <?php echo htmlspecialchars($channel['country']); ?></p>
                </div>
            </div>

            <!-- Related Matches -->
            <div class="matches-widget">
                <h3>مباريات قادمة</h3>
                <div id="relatedMatches" class="matches-list">
                    <!-- Matches will be loaded here -->
                </div>
                <div id="noMatches" class="no-data" style="display: none;">
                    <p>لا توجد مباريات قادمة</p>
                </div>
            </div>

            <!-- Share Section -->
            <div class="share-section">
                <h3>شارك هذه القناة</h3>
                <div class="share-buttons">
                    <button class="share-btn" onclick="shareChannel()">
                        📤 شارك
                    </button>
                    <button class="share-btn" onclick="copyLink()">
                        📋 انسخ الرابط
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const channelId = <?php echo json_encode($channel_id); ?>;
const channelData = <?php echo json_encode($channel); ?>;
</script>
<script src="<?php echo SITE_URL; ?>/js/hls.min.js"></script>
<script src="<?php echo SITE_URL; ?>/js/watch.js"></script>
