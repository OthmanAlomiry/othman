<?php
// الرابط الأصلي الذي تريد إخفاءه
$original_url = "http://135.125.109.73:9000/beinsport4_.m3u8";

// تعيين نوع المحتوى ليكون ملف بث فيديو
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");

// جلب محتوى الرابط وعرضه مباشرة من سيرفرك
echo file_get_contents($original_url);
?>
