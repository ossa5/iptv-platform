<?php
require_once 'config.php';
require_once 'includes/functions.php';

session_start();

// تحديد إذا كان المسؤول مسجل دخول
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة الإدارة - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php if (!$is_admin): ?>
            <!-- Login Form -->
            <div class="login-form">
                <div class="login-card">
                    <h1>لوحة الإدارة</h1>
                    <p>أدخل كلمة المرور للمتابعة</p>
                    <form id="loginForm">
                        <div class="form-group">
                            <input type="password" id="password" placeholder="كلمة المرور" required>
                        </div>
                        <button type="submit" class="btn btn-primary">دخول</button>
                    </form>
                    <div id="loginError" class="error-message" style="display: none;"></div>
                </div>
            </div>
        <?php else: ?>
            <!-- Admin Dashboard -->
            <nav class="admin-nav">
                <div class="nav-brand">
                    <h1>لوحة الإدارة</h1>
                </div>
                <ul class="nav-menu">
                    <li><a href="#" data-tab="dashboard" class="nav-link active">لوحة التحكم</a></li>
                    <li><a href="#" data-tab="channels" class="nav-link">إدارة القنوات</a></li>
                    <li><a href="#" data-tab="matches" class="nav-link">إدارة المباريات</a></li>
                    <li><a href="#" data-tab="import" class="nav-link">استيراد M3U</a></li>
                    <li><a href="#" id="logoutBtn" class="nav-link logout">خروج</a></li>
                </ul>
            </nav>

            <div class="admin-content">
                <!-- Dashboard Tab -->
                <div id="dashboard" class="tab-content active">
                    <h2>نظرة عامة</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3 id="totalChannels">0</h3>
                            <p>إجمالي القنوات</p>
                        </div>
                        <div class="stat-card">
                            <h3 id="activeChannels">0</h3>
                            <p>قنوات نشطة</p>
                        </div>
                        <div class="stat-card">
                            <h3 id="totalMatches">0</h3>
                            <p>إجمالي المباريات</p>
                        </div>
                    </div>
                </div>

                <!-- Channels Management Tab -->
                <div id="channels" class="tab-content">
                    <div class="section-header">
                        <h2>إدارة القنوات</h2>
                        <button class="btn btn-primary" onclick="openChannelForm()">+ إضافة قناة</button>
                    </div>
                    <div id="channelsList" class="channels-table">
                        <!-- Channels will be loaded here -->
                    </div>
                </div>

                <!-- Matches Management Tab -->
                <div id="matches" class="tab-content">
                    <div class="section-header">
                        <h2>إدارة المباريات</h2>
                        <button class="btn btn-primary" onclick="openMatchForm()">+ إضافة مباراة</button>
                    </div>
                    <div id="matchesList" class="matches-table">
                        <!-- Matches will be loaded here -->
                    </div>
                </div>

                <!-- Import Tab -->
                <div id="import" class="tab-content">
                    <h2>استيراد ملف M3U</h2>
                    <div class="import-form">
                        <p>اختر ملف M3U لاستيراد القنوات</p>
                        <input type="file" id="m3uFile" accept=".m3u,.m3u8,.txt">
                        <button class="btn btn-primary" onclick="importM3U()">استيراد</button>
                        <div id="importStatus" class="status-message" style="display: none;"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Channel Form Modal -->
    <div id="channelModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modalTitle">إضافة قناة</h2>
            <form id="channelForm">
                <input type="hidden" id="channelId">
                <div class="form-group">
                    <label>اسم القناة</label>
                    <input type="text" id="channelName" required>
                </div>
                <div class="form-group">
                    <label>رابط البث (M3U8/HLS)</label>
                    <input type="url" id="channelUrl" required>
                </div>
                <div class="form-group">
                    <label>شعار القناة</label>
                    <input type="url" id="channelLogo">
                </div>
                <div class="form-group">
                    <label>الفئة</label>
                    <select id="channelCategory" required>
                        <option value="sports">رياضة</option>
                        <option value="news">أخبار</option>
                        <option value="movies">أفلام</option>
                        <option value="kids">أطفال</option>
                        <option value="general">عام</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>الدولة</label>
                    <input type="text" id="channelCountry" value="SA">
                </div>
                <div class="form-group">
                    <label>اللغة</label>
                    <input type="text" id="channelLanguage" value="ar">
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="channelActive" checked>
                        نشط
                    </label>
                </div>
                <button type="submit" class="btn btn-success">حفظ</button>
            </form>
        </div>
    </div>

    <!-- Match Form Modal -->
    <div id="matchModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="matchModalTitle">إضافة مباراة</h2>
            <form id="matchForm">
                <input type="hidden" id="matchId">
                <div class="form-group">
                    <label>الفريق الأول</label>
                    <input type="text" id="team1" required>
                </div>
                <div class="form-group">
                    <label>الفريق الثاني</label>
                    <input type="text" id="team2" required>
                </div>
                <div class="form-group">
                    <label>التاريخ</label>
                    <input type="date" id="matchDate" required>
                </div>
                <div class="form-group">
                    <label>الوقت</label>
                    <input type="time" id="matchTime">
                </div>
                <div class="form-group">
                    <label>البطولة</label>
                    <input type="text" id="league">
                </div>
                <div class="form-group">
                    <label>القناة الناقلة</label>
                    <select id="channel">
                        <option value="">-- اختر قناة --</option>
                        <!-- Channels will be loaded here -->
                    </select>
                </div>
                <button type="submit" class="btn btn-success">حفظ</button>
            </form>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/js/admin.js"></script>
</body>
</html>
