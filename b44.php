<?php
// رابط القناة الأصلي
$remote_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/67341.m3u8";

if (isset($_GET['ts'])) {
    // جلب قطع الفيديو مباشرة
    $ts_url = urldecode($_GET['ts']);
    header("Content-Type: video/mp2t");
    $opts = ["http" => ["header" => "User-Agent: VLC/3.0.18\r\n"]];
    $context = stream_context_create($opts);
    readfile($ts_url, false, $context);
    exit;
}

// جلب ملف القائمة وتعديل الروابط لتمر عبر هذا الملف نفسه
$opts = ["http" => ["header" => "User-Agent: VLC/3.0.18\r\n"]];
$context = stream_context_create($opts);
$content = file_get_contents($remote_url, false, $context);

if ($content) {
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    
    $base_url = "http://ibo.lynxiptv.com/live/276983819492/Dm00SSnT73/";
    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && $line[0] !== '#') {
            $full_ts = (strpos($line, 'http') === 0) ? $line : $base_url . $line;
            echo "b44.php?ts=" . urlencode($full_ts) . "\n";
        } else {
            echo $line . "\n";
        }
    }
} else {
    // إذا لم يعمل، سنعرض مشغل فيديو احتياطي يحاول الاتصال المباشر
    include_once 'fallback_player.html'; 
}
exit;
