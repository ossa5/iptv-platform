# منصة IPTV على الويب
# IPTV Web Platform

منصة بث تلفزيوني عبر الويب تدعم قنوات رياضية وترفيهية مثل beIN Sports مع واجهة حديثة وسهلة الاستخدام.

## الميزات الرئيسية ✨

- ✅ عرض القنوات التلفزيونية والبث المباشر
- ✅ مشغل فيديو يدعم HLS و M3U8 (hls.js)
- ✅ تصنيفات متعددة (رياضة، أخبار، أفلام، أطفال، عام)
- ✅ دليل القنوات مع البحث والتصفية
- ✅ جدول المباريات والأحداث
- ✅ واجهة متجاوبة (Responsive Design)
- ✅ تحديث القنوات من ملفات M3U
- ✅ لوحة إدارة بسيطة
- ✅ نظام Cache لتحسين الأداء
- ✅ قاعدة بيانات JSON

## البنية التكنولوجية 🛠️

- **الواجهة الأمامية:** HTML5 + CSS3 + JavaScript
- **الخلفية:** PHP 7.4+
- **مشغل الفيديو:** hls.js
- **قاعدة البيانات:** ملفات JSON
- **الخادم:** Apache / Nginx + PHP

## المجلدات والملفات

```
iptv-platform/
├── index.php                 # الصفحة الرئيسية
├── admin.php                 # لوحة الإدارة
├── api.php                   # واجهة API
├── config.php                # الإعدادات
├── css/
│   ├── style.css            # التنسيقات الرئيسية
│   └── admin.css            # تنسيقات الإدارة
├── js/
│   ├── main.js              # السكريبت الرئيسي
│   ├── search.js            # البحث والتصفية
│   └── admin.js             # لوحة الإدارة
├── data/
│   ├── channels.json        # قائمة القنوات
│   ├── matches.json         # جدول المباريات
│   └── cache.json           # ملف التخزين المؤقت
├── uploads/                 # رفع ملفات M3U
├── includes/
│   ├── header.php           # رأس الصفحة
│   ├── footer.php           # تذييل الصفحة
│   └── functions.php        # الدوال المساعدة
├── .gitignore
└── README.md
```

## المتطلبات 📋

- PHP 7.4 أو أحدث
- متصفح حديث يدعم HTML5
- خادم ويب (Apache/Nginx)
- تفعيل `allow_url_fopen` في PHP (اختياري للم3u8 الخارجية)

## التثبيت والتشغيل 🚀

### 1. استنساخ المستودع
```bash
git clone https://github.com/ossa5/iptv-platform.git
cd iptv-platform
```

### 2. إعدادات الملفات والصلاحيات
```bash
chmod 755 data/
chmod 755 uploads/
chmod 644 data/*.json
```

### 3. التشغيل المحلي
```bash
# استخدام PHP المدمج
php -S localhost:8000

# أو استخدام Apache
# ضع المشروع في مجلد public_html أو htdocs
```

### 4. الوصول إلى التطبيق
```
الصفحة الرئيسية: http://localhost:8000
لوحة الإدارة: http://localhost:8000/admin.php
```

## API الرئيسي 📡

### الحصول على قائمة القنوات
```
GET api.php?action=channels
GET api.php?action=channels&category=sports
GET api.php?action=search&q=keyword
```

### الحصول على جدول المباريات
```
GET api.php?action=matches
GET api.php?action=matches&date=2026-06-22
```

### إضافة قناة جديدة (الإدارة)
```
POST api.php?action=add_channel
Content-Type: application/json

{
  "name": "beIN Sports 1",
  "url": "http://example.com/stream.m3u8",
  "logo": "http://example.com/logo.png",
  "category": "sports"
}
```

### استيراد ملف M3U
```
POST api.php?action=import_m3u
Content-Type: multipart/form-data
```

## ملفات البيانات 📊

### channels.json
```json
{
  "channels": [
    {
      "id": 1,
      "name": "beIN Sports 1",
      "url": "https://example.com/stream.m3u8",
      "logo": "https://example.com/logo.png",
      "category": "sports",
      "country": "SA",
      "language": "ar",
      "active": true
    }
  ]
}
```

### matches.json
```json
{
  "matches": [
    {
      "id": 1,
      "team1": "الهلال",
      "team2": "النصر",
      "date": "2026-06-22",
      "time": "20:00",
      "league": "دوري روشن السعودي",
      "channel": "beIN Sports 1"
    }
  ]
}
```

## الاستخدام 📖

### للمستخدم النهائي
1. افتح الصفحة الرئيسية
2. اختر فئة أو بحث عن قناة
3. انقر على القناة لبدء البث
4. استمتع بالبث المباشر

### للمسؤول
1. اذهب إلى لوحة الإدارة (admin.php)
2. أضف/عدّل/احذف القنوات
3. استورد ملفات M3U
4. أضف جدول المباريات
5. اعرض الإحصائيات

## نصائح الأداء ⚡

- تفعيل الـ Cache لتسريع الوصول للبيانات
- استخدام CDN للشعارات والصور
- تحسين صيغ الفيديو (استخدام HLS/DASH)
- ضغط الملفات الثابتة (gzip)
- تقليل عدد الطلبات

## الأمان 🔒

- التحقق من صحة البيانات المدخلة
- استخدام SQL escaping (في حالة قاعدة بيانات لاحقاً)
- حماية لوحة الإدارة بكلمة مرور
- فحص ملفات M3U المرفوعة

## المساهمة 🤝

نرحب بالمساهمات! يرجى:
1. عمل Fork للمستودع
2. إنشاء فرع جديد للميزة
3. إرسال Pull Request

## الترخيص 📜

MIT License

## التواصل 📧

- GitHub: [@ossa5](https://github.com/ossa5)

---

**تم التطوير بـ ❤️ من قبل فريق المشروع**