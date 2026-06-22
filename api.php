<?php
/**
 * واجهة API لمنصة IPTV
 * IPTV Platform API
 */

require_once 'config.php';
require_once 'includes/functions.php';

session_start();

$action = $_GET['action'] ?? 'channels';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        // الحصول على القنوات
        case 'channels':
            if ($method === 'GET') {
                $category = $_GET['category'] ?? null;
                
                // محاولة الحصول من Cache
                $cache_key = 'channels_' . ($category ?? 'all');
                $channels = get_cache($cache_key);
                
                if ($channels === null) {
                    if ($category) {
                        $channels = get_channels_by_category($category);
                    } else {
                        $channels = array_filter(get_all_channels(), fn($ch) => $ch['active']);
                    }
                    set_cache($cache_key, $channels);
                }
                
                json_response(['success' => true, 'channels' => $channels]);
            }
            break;
            
        // البحث عن القنوات
        case 'search':
            if ($method === 'GET') {
                $query = $_GET['q'] ?? '';
                
                if (empty($query)) {
                    json_response(['success' => true, 'channels' => []]);
                }
                
                $results = search_channels($query);
                json_response(['success' => true, 'channels' => $results]);
            }
            break;
            
        // الحصول على قناة محددة
        case 'channel':
            if ($method === 'GET') {
                $id = $_GET['id'] ?? null;
                
                if (!$id) {
                    json_response(['success' => false, 'error' => 'معرف القناة مطلوب'], 400);
                }
                
                $channel = get_channel_by_id($id);
                
                if (!$channel) {
                    json_response(['success' => false, 'error' => 'القناة غي�� موجودة'], 404);
                }
                
                json_response(['success' => true, 'channel' => $channel]);
            }
            break;
            
        // الحصول على المباريات
        case 'matches':
            if ($method === 'GET') {
                $date = $_GET['date'] ?? null;
                
                $cache_key = 'matches_' . ($date ?? 'all');
                $matches = get_cache($cache_key);
                
                if ($matches === null) {
                    if ($date) {
                        $matches = get_matches_by_date($date);
                    } else {
                        $matches = get_all_matches();
                    }
                    set_cache($cache_key, $matches);
                }
                
                json_response(['success' => true, 'matches' => $matches]);
            }
            break;
            
        // إضافة قناة (الإدارة)
        case 'add_channel':
            if ($method === 'POST') {
                check_admin();
                
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (empty($data['name']) || empty($data['url']) || empty($data['category'])) {
                    json_response(['success' => false, 'error' => 'بيانات ناقصة'], 400);
                }
                
                add_channel($data);
                clear_cache();
                
                json_response(['success' => true, 'message' => 'تمت إضافة القناة بنجاح']);
            }
            break;
            
        // تحديث قناة
        case 'update_channel':
            if ($method === 'POST') {
                check_admin();
                
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? null;
                
                if (!$id) {
                    json_response(['success' => false, 'error' => 'معرف القناة مطلوب'], 400);
                }
                
                update_channel($id, $data);
                clear_cache();
                
                json_response(['success' => true, 'message' => 'تم تحديث القناة بنجاح']);
            }
            break;
            
        // حذف قناة
        case 'delete_channel':
            if ($method === 'POST') {
                check_admin();
                
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? null;
                
                if (!$id) {
                    json_response(['success' => false, 'error' => 'معرف القناة مطلوب'], 400);
                }
                
                delete_channel($id);
                clear_cache();
                
                json_response(['success' => true, 'message' => 'تم حذف القناة بنجاح']);
            }
            break;
            
        // استيراد ملف M3U
        case 'import_m3u':
            if ($method === 'POST') {
                check_admin();
                
                if (!isset($_FILES['file'])) {
                    json_response(['success' => false, 'error' => 'لم يتم تحميل أي ملف'], 400);
                }
                
                $file = $_FILES['file'];
                
                if (!validate_file_extension($file['name'])) {
                    json_response(['success' => false, 'error' => 'نوع الملف غير صحيح'], 400);
                }
                
                if ($file['size'] > MAX_FILE_SIZE) {
                    json_response(['success' => false, 'error' => 'حجم الملف كبير جداً'], 400);
                }
                
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    json_response(['success' => false, 'error' => 'خطأ في تحميل الملف'], 400);
                }
                
                $upload_path = UPLOADS_PATH . 'temp_' . time() . '_' . basename($file['name']);
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    if (import_m3u_file($upload_path)) {
                        unlink($upload_path);
                        json_response(['success' => true, 'message' => 'تم استيراد القنوات بنجاح']);
                    } else {
                        json_response(['success' => false, 'error' => 'خطأ في معالجة الملف'], 500);
                    }
                } else {
                    json_response(['success' => false, 'error' => 'فشل تحميل الملف'], 500);
                }
            }
            break;
            
        // إضافة مباراة
        case 'add_match':
            if ($method === 'POST') {
                check_admin();
                
                $data = json_decode(file_get_contents('php://input'), true);
                
                if (empty($data['team1']) || empty($data['team2']) || empty($data['date'])) {
                    json_response(['success' => false, 'error' => 'بيانات ناقصة'], 400);
                }
                
                add_match($data);
                clear_cache();
                
                json_response(['success' => true, 'message' => 'تمت إضافة المباراة بنجاح']);
            }
            break;
            
        // حذف مباراة
        case 'delete_match':
            if ($method === 'POST') {
                check_admin();
                
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id'] ?? null;
                
                if (!$id) {
                    json_response(['success' => false, 'error' => 'معرف المباراة مطلوب'], 400);
                }
                
                delete_match($id);
                clear_cache();
                
                json_response(['success' => true, 'message' => 'تم حذف المباراة بنجاح']);
            }
            break;
            
        // الحصول على الإحصائيات
        case 'statistics':
            if ($method === 'GET') {
                check_admin();
                
                $stats = get_statistics();
                json_response(['success' => true, 'statistics' => $stats]);
            }
            break;
            
        // تسجيل الدخول
        case 'login':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $password = $data['password'] ?? '';
                
                if (hash('sha256', $password) === hash('sha256', ADMIN_PASSWORD)) {
                    $_SESSION['admin'] = true;
                    json_response(['success' => true, 'message' => 'تم تسجيل الدخول بنجاح']);
                } else {
                    json_response(['success' => false, 'error' => 'كلمة المرور غير صحيحة'], 401);
                }
            }
            break;
            
        // تسجيل الخروج
        case 'logout':
            $_SESSION['admin'] = false;
            session_destroy();
            json_response(['success' => true, 'message' => 'تم تسجيل الخروج']);
            break;
            
        // الفئات
        case 'categories':
            if ($method === 'GET') {
                $categories = json_decode(CATEGORIES, true);
                json_response(['success' => true, 'categories' => $categories]);
            }
            break;
            
        default:
            json_response(['success' => false, 'error' => 'إجراء غير معروف'], 400);
    }
} catch (Exception $e) {
    log_error($e->getMessage());
    json_response(['success' => false, 'error' => 'حدث خطأ في الخادم'], 500);
}
?>