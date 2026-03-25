<?php
/**
 * ملف تشغيل قناة SHOOF المطور - متجر الخدمة الرقمية
 * يقوم هذا الملف بجلب البث الخارجي وتجاوز حماية الـ Referer والـ CORS
 */

// 1. رابط البث المباشر الخارجي
$remote_url = "https://liveeu-gcp.alkassdigital.net/shooflive/main.m3u8";

// 2. إعدادات محاكاة متصفح حقيقي لتجاوز حماية السيرفر
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36\r\n" .
                    "Referer: https://alkass.net/\r\n" .
                    "Origin: https://alkass.net\r\n"
    ]
];

// 3. إنشاء سياق الاتصال وجلب المحتوى
$context = stream_context_create($options);
$content = @file_get_contents($remote_url, false, $context);

// 4. فحص هل تم جلب البيانات بنجاح
if ($content === false) {
    // في حال فشل السيرفر في القراءة، نقوم بتحويل المتصفح للرابط مباشرة كحل أخير
    header("Location: $remote_url");
    exit;
}

// 5. إعداد رؤوس الاستجابة لمتصفح المستخدم (مهم جداً للتشغيل)
header("Content-Type: application/vnd.apple.mpegurl"); // تعريف الملف كبث فيديو
header("Access-Control-Allow-Origin: *");             // السماح لموقعك (index.php) بفتح البث
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 6. إصلاح الروابط الداخلية (Relative Links)
// ملفات الـ M3U8 غالباً تحتوي على روابط ملفات فرعية (segments) تحتاج لتكملة المسار
$base_path = "https://liveeu-gcp.alkassdigital.net/shooflive/";

// استبدال كلمة index برابطها الكامل إذا كانت موجودة لضمان استمرار البث
$content = str_replace("index", $base_path . "index", $content);

// 7. طباعة المحتوى للمشغل
echo $content;
?>
