<?php
require_once 'config.php';
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <!-- Matches Section -->
    <section class="matches-section">
        <div class="section-header">
            <h1 class="section-title">جدول المباريات</h1>
            <div class="filter-controls">
                <select id="dateFilter" class="filter-select">
                    <option value="">جميع التواريخ</option>
                    <option value="today">اليوم</option>
                    <option value="tomorrow">غداً</option>
                    <option value="week">هذا الأسبوع</option>
                </select>
            </div>
        </div>

        <div id="matchesContainer" class="matches-grid">
            <!-- Matches will be loaded here -->
        </div>
        <div id="noMatches" class="no-data" style="display: none;">
            <p>لا توجد مباريات متاحة</p>
        </div>
    </section>
</div>

<!-- Match Details Modal -->
<div id="matchModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="match-details">
            <div class="match-header">
                <h2 id="matchTitle"></h2>
                <p id="matchLeague"></p>
            </div>
            <div class="match-info">
                <div class="team">
                    <p class="team-name" id="team1"></p>
                </div>
                <div class="match-time">
                    <p id="matchDate"></p>
                    <p id="matchTime"></p>
                </div>
                <div class="team">
                    <p class="team-name" id="team2"></p>
                </div>
            </div>
            <div class="match-channel">
                <p>البث على: <strong id="broadcastChannel"></strong></p>
                <a id="watchButton" href="#" class="btn btn-primary">مشاهدة</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="<?php echo SITE_URL; ?>/js/matches.js"></script>
