<?php
/**
 * الدوال المساعدة
 * Helper Functions
 */

/**
 * قراءة ملف JSON
 */
function read_json_file($file_path) {
    if (!file_exists($file_path)) {
        return [];
    }
    
    $content = file_get_contents($file_path);
    return json_decode($content, true) ?? [];
}

/**
 * كتابة ملف JSON
 */
function write_json_file($file_path, $data) {
    $dir = dirname($file_path);
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents($file_path, $json);
}

/**
 * الحصول على جميع القنوات
 */
function get_all_channels() {
    $data = read_json_file(CHANNELS_DB);
    return $data['channels'] ?? [];
}

/**
 * الحصول على قناة من خلال المعرف
 */
function get_channel_by_id($id) {
    $channels = get_all_channels();
    foreach ($channels as $channel) {
        if ($channel['id'] == $id) {
            return $channel;
        }
    }
    return null;
}

/**
 * البحث عن القنوات
 */
function search_channels($query) {
    $channels = get_all_channels();
    $results = [];
    
    $query = strtolower($query);
    
    foreach ($channels as $channel) {
        if (strpos(strtolower($channel['name']), $query) !== false ||
            strpos(strtolower($channel['category']), $query) !== false) {
            $results[] = $channel;
        }
    }
    
    return $results;
}

/**
 * الحصول على القنوات حسب الفئة
 */
function get_channels_by_category($category) {
    $channels = get_all_channels();
    $results = [];
    
    foreach ($channels as $channel) {
        if ($channel['category'] === $category && $channel['active']) {
            $results[] = $channel;
        }
    }
    
    return $results;
}

/**
 * إضافة قناة جديدة
 */
function add_channel($channel_data) {
    $channels = read_json_file(CHANNELS_DB);
    
    if (empty($channels['channels'])) {
        $channels['channels'] = [];
    }
    
    // إيجاد أكبر معرف
    $max_id = 0;
    foreach ($channels['channels'] as $ch) {
        if ($ch['id'] > $max_id) {
            $max_id = $ch['id'];
        }
    }
    
    $channel_data['id'] = $max_id + 1;
    $channel_data['active'] = $channel_data['active'] ?? true;
    $channel_data['created_at'] = date('Y-m-d H:i:s');
    
    $channels['channels'][] = $channel_data;
    
    return write_json_file(CHANNELS_DB, $channels);
}

/**
 * تحديث قناة
 */
function update_channel($id, $channel_data) {
    $channels = read_json_file(CHANNELS_DB);
    
    foreach ($channels['channels'] as &$ch) {
        if ($ch['id'] == $id) {
            $channel_data['id'] = $id;
            $channel_data['updated_at'] = date('Y-m-d H:i:s');
            $ch = array_merge($ch, $channel_data);
            break;
        }
    }
    
    return write_json_file(CHANNELS_DB, $channels);
}

/**
 * حذف قناة
 */
function delete_channel($id) {
    $channels = read_json_file(CHANNELS_DB);
    
    $channels['channels'] = array_filter($channels['channels'], function($ch) use ($id) {
        return $ch['id'] != $id;
    });
    
    return write_json_file(CHANNELS_DB, $channels);
}

/**
 * الحصول على جميع المباريات
 */
function get_all_matches() {
    $data = read_json_file(MATCHES_DB);
    return $data['matches'] ?? [];
}

/**
 * الحصول على مباريات بتاريخ معين
 */
function get_matches_by_date($date) {
    $matches = get_all_matches();
    $results = [];
    
    foreach ($matches as $match) {
        if ($match['date'] === $date) {
            $results[] = $match;
        }
    }
    
    return $results;
}

/**
 * إضافة مباراة
 */
function add_match($match_data) {
    $matches = read_json_file(MATCHES_DB);
    
    if (empty($matches['matches'])) {
        $matches['matches'] = [];
    }
    
    $max_id = 0;
    foreach ($matches['matches'] as $m) {
        if ($m['id'] > $max_id) {
            $max_id = $m['id'];
        }
    }
    
    $match_data['id'] = $max_id + 1;
    $match_data['created_at'] = date('Y-m-d H:i:s');
    
    $matches['matches'][] = $match_data;
    
    return write_json_file(MATCHES_DB, $matches);
}

/**
 * حذف مباراة
 */
function delete_match($id) {
    $matches = read_json_file(MATCHES_DB);
    
    $matches['matches'] = array_filter($matches['matches'], function($m) use ($id) {
        return $m['id'] != $id;
    });
    
    return write_json_file(MATCHES_DB, $matches);
}

/**
 * إعادة تعيين Cache
 */
function clear_cache() {
    if (file_exists(CACHE_PATH)) {
        unlink(CACHE_PATH);
    }
}

/**
 * الحصول من Cache
 */
function get_cache($key) {
    if (!ENABLE_CACHE || !file_exists(CACHE_PATH)) {
        return null;
    }
    
    $cache = json_decode(file_get_contents(CACHE_PATH), true);
    
    if (isset($cache[$key])) {
        $item = $cache[$key];
        
        if (isset($item['expires_at']) && time() > $item['expires_at']) {
            return null;
        }
        
        return $item['data'] ?? null;
    }
    
    return null;
}

/**
 * حفظ في Cache
 */
function set_cache($key, $data, $expiry = null) {
    if (!ENABLE_CACHE) {
        return false;
    }
    
    $cache = [];
    if (file_exists(CACHE_PATH)) {
        $cache = json_decode(file_get_contents(CACHE_PATH), true) ?? [];
    }
    
    $cache[$key] = [
        'data' => $data,
        'expires_at' => time() + ($expiry ?? CACHE_EXPIRY)
    ];
    
    return write_json_file(CACHE_PATH, $cache);
}

/**
 * فحص صيغة الملف
 */
function validate_file_extension($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ALLOWED_EXTENSIONS);
}

/**
 * معالجة ملف M3U
 */
function parse_m3u($content) {
    $channels = [];
    $lines = explode("\n", $content);
    
    $current_channel = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line)) {
            continue;
        }
        
        if (strpos($line, '#EXTINF:') === 0) {
            // استخراج البيانات من السطر
            preg_match('/tvg-name="([^"]*)"/', $line, $name);
            preg_match('/tvg-logo="([^"]*)"/', $line, $logo);
            preg_match('/group-title="([^"]*)"/', $line, $group);
            
            $current_channel = [
                'name' => $name[1] ?? 'Unknown',
                'logo' => $logo[1] ?? '',
                'category' => strtolower(str_replace(' ', '_', $group[1] ?? 'general')),
                'country' => 'SA',
                'language' => 'ar'
            ];
        } elseif (!empty($line) && !strpos($line, '#')) {
            $current_channel['url'] = $line;
            $channels[] = $current_channel;
            $current_channel = [];
        }
    }
    
    return $channels;
}

/**
 * استيراد ملف M3U
 */
function import_m3u_file($file_path) {
    if (!file_exists($file_path)) {
        return false;
    }
    
    $content = file_get_contents($file_path);
    $channels = parse_m3u($content);
    
    foreach ($channels as $channel) {
        add_channel($channel);
    }
    
    clear_cache();
    return true;
}

/**
 * الحصول على إحصائيات
 */
function get_statistics() {
    $channels = get_all_channels();
    $matches = get_all_matches();
    
    $categories = [];
    foreach ($channels as $ch) {
        if (!isset($categories[$ch['category']])) {
            $categories[$ch['category']] = 0;
        }
        $categories[$ch['category']]++;
    }
    
    return [
        'total_channels' => count($channels),
        'active_channels' => count(array_filter($channels, fn($ch) => $ch['active'])),
        'total_matches' => count($matches),
        'by_category' => $categories
    ];
}

/**
 * تعيين رمز الفئة
 */
function get_category_icon($category) {
    $icons = [
        'sports' => '⚽',
        'news' => '📰',
        'movies' => '🎬',
        'kids' => '🎨',
        'general' => '📺'
    ];
    
    return $icons[$category] ?? '📺';
}

?>