<?php
// تصفية الرابط
$id = "006900";
$url = "http://apk.arabic-ch.space/live/$id/index.m3u8";

$options = [
    "http" => [
        "header" => "User-Agent: VLC/3.0.18\r\n"
    ]
];
$context = stream_context_create($options);
$data = file_get_contents($url, false, $context);

if($data){
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    // تغيير روابط القطع لتمر عبر هذا السكربت أيضاً (تحتاج برمجة متقدمة)
    echo str_replace("index", "http://apk.arabic-ch.space/live/$id/index", $data);
} else {
    echo "سيرفر البث يرفض الاتصال بسيرفرك الشخصي.";
}
