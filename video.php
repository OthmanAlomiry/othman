<?php
// 1. إعدادات البث
$remote_m3u8 = "http://apk.arabic-ch.space/live/006900/index.m3u8";
$base_url = "http://apk.arabic-ch.space/live/006900/";

// 2. محاكاة متصفح آيفون حقيقي لتجنب الحظر
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1\r\n" .
                    "Accept: */*\r\n"
    ]
];

// 3. إذا طلب المتصفح ملف .ts (قطعة فيديو)
if (isset($_GET['ts'])) {
    $ts_url = $base_url . $_GET['ts'];
    header("Content-Type: video/mp2t");
    readfile($ts_url); // تحميل القطعة وتمريرها مباشرة للمتصفح
    exit;
}

// 4. جلب ملف الـ m3u8 وتعديله
$context = stream_context_create($options);
$m3u8_content = @file_get_contents($remote_m3u8, false, $context);

if ($m3u8_content === FALSE) {
    header("HTTP/1.1 403 Forbidden");
    die("Error: Source blocked the server.");
}

// 5. تعديل روابط الـ .ts لكي تمر عبر هذا السكربت نفسه (Proxying)
// سيتحول رابط segment1.ts إلى stream.php?ts=segment1.ts
$current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$m3u8_content = preg_replace('/([\w\.-]+\.ts)/', $current_url . '?ts=$1', $m3u8_content);

// 6. ترويسات التشغيل لآيفون
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache");

echo $m3u8_content;
