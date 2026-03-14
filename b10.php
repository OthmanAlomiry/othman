<?php
/**
 * STARZPLAY Proxy Streamer
 * هذا الملف يعمل كجسر آمن (HTTPS) لجلب البث غير الآمن (HTTP)
 */

header("Access-Control-Allow-Origin: *");

// 1. الرابط الأساسي الذي يعمل لديك
$remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
$base_path  = "http://sportfet.shop/AD1/tracks-v1a1/";

// 2. معالجة طلبات قطع الفيديو (.ts)
if (isset($_GET['ts'])) {
    $ts_file_url = $base_path . $_GET['ts'];
    
    header("Content-Type: video/mp2t");
    
    // استخدام CURL لمحاكاة متصفح حقيقي وتجنب الحظر
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ts_file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    $data = curl_exec($ch);
    curl_close($ch);
    
    echo $data;
    exit;
}

// 3. معالجة طلب ملف البث الأساسي (.m3u8)
// هذا الجزء يتم استدعاؤه عندما يضغط المستخدم "تشغيل" في live.php
if (isset($_GET['proxy'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    $m3u8_data = curl_exec($ch);
    curl_close($ch);

    if ($m3u8_data) {
        // السحر البرمجي: تحويل روابط قطع الفيديو لتمر عبر هذا الملف نفسه بـ HTTPS
        // بدلاً من segment1.ts تصبح b10.php?ts=segment1.ts
        $m3u8_data = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', 'b10.php?ts=$1', $m3u8_data);
        echo $m3u8_data;
    }
    exit;
}

// 4. إذا تم فتح الملف مباشرة (اختياري)
echo "STREAM_SYSTEM_READY";
?>
