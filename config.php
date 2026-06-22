<?php
/**
 * إعدادات منصة IPTV
 * IPTV Platform Configuration
 */

// معلومات الموقع
define('SITE_NAME', 'منصة IPTV');
define('SITE_URL', 'http://localhost:8000');
define('ADMIN_PASSWORD', 'admin123'); // غير كلمة المرور هذه!

// المسارات
define('DATA_PATH', __DIR__ . '/data/');
define('UPLOADS_PATH', __DIR__ . '/uploads/');
define('CACHE_PATH', __DIR__ . '/data/cache.json');

// إعدادات قاعدة البيانات (ملفات JSON)
define('CHANNELS_DB', DATA_PATH . 'channels.json');
define('MATCHES_DB', DATA_PATH . 'matches.json');

// الفئات المتاحة
define('CATEGORIES', json_encode([
    'sports' => 'رياضة',
    'news' => 'أخبار',
    'movies' => 'أفلام',
    'kids' => 'أطفال',
    'general' => 'عام'
]));

// إعدادات التخزين المؤقت
define('CACHE_EXPIRY', 3600); // ساعة واحدة
define('ENABLE_CACHE', true);

// إعدادات الفيديو
define('HLS_TIMEOUT', 30); // ثانية
define('VIDEO_PLAYER', 'hls.js');

// إعدادات الأمان
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['m3u', 'txt']);

// إعدادات التطوير
define('DEBUG_MODE', false); // اجعلها false في الإنتاج

// timezone
date_default_timezone_set('Asia/Riyadh');

// رؤوس CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// معالجة الأخطاء
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// دالة تسجيل الأخطاء
function log_error($message) {
    $log_file = DATA_PATH . 'error.log';
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, $log_file);
}

// دالة الرد على JSON
function json_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// دالة للتحقق من الجلسة/الإدارة
function check_admin() {
    if (empty($_SESSION['admin']) || $_SESSION['admin'] !== true) {
        json_response(['error' => 'غير مصرح'], 403);
    }
}

?>