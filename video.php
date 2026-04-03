<?php
// 1. الرابط الأصلي
$remote_m3u8 = "http://apk.arabic-ch.space/live/006900/index.m3u8";
$base_url = "http://apk.arabic-ch.space/live/006900/";

// 2. ترويسات محاكاة متصفح آيفون لتجنب الحظر
$userAgent = "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1";

// 3. معالجة طلبات قطع الفيديو (.ts)
if (isset($_GET['ts'])) {
    $ts_file = $_GET['ts'];
    $full_ts_url = $base_url . $ts_file;
    
    header("Content-Type: video/mp2t");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $full_ts_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    curl_close($ch);
    exit;
}

// 4. جلب ملف الـ m3u8 الأصلي باستخدام cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $remote_m3u8);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
$m3u8_content = curl_exec($ch);
curl_close($ch);

if (!$m3u8_content) {
    header("HTTP/1.1 403 Forbidden");
    die("خطأ: سيرفر البث يرفض اتصال سيرفر Render.");
}

// 5. تعديل روابط الـ .ts لكي تمر عبر هذا الملف نفسه
// سيتحول segment.ts إلى stream.php?ts=segment.ts
$current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$m3u8_content = preg_replace('/([\w\.-]+\.ts)/', $current_url . '?ts=$1', $m3u8_content);

// 6. ترويسات التشغيل لمتصفح سفاري
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache, no-store, must-revalidate");

echo $m3u8_content;
