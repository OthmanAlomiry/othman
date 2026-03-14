<?php
header("Access-Control-Allow-Origin: *");

// الرابط الأساسي
$remote_url = "http://sportfet.shop/AD1/tracks-v1a1/mono.m3u8";
$base_path = "http://sportfet.shop/AD1/tracks-v1a1/";

// معالجة قطع الفيديو (ts)
if (isset($_GET['ts'])) {
    header("Content-Type: video/mp2t");
    echo file_get_contents($base_path . $_GET['ts']);
    exit;
}

// معالجة البث (m3u8)
if (isset($_GET['proxy'])) {
    header("Content-Type: application/vnd.apple.mpegurl");
    $content = file_get_contents($remote_url);
    
    // تحويل الروابط الداخلية لتمر عبر هذا الملف لتخطي حماية HTTPS
    $current_file = "b10.php";
    $content = preg_replace('/([a-zA-Z0-9_\-]+\.ts)/', $current_file . '?ts=$1', $content);
    echo $content;
    exit;
}

// إذا تم فتح الملف مباشرة يظهر كود التشفير الذي وضعته أنت
$secret_link = base64_encode($remote_url);
echo "STREAM_READY:" . $secret_link;
?>
