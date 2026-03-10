<?php
// الرابط الذي تريد إخفاءه
$secret_url = "http://135.125.109.73:9000/beinsport4_.m3u8";

// توجيه المتصفح للرابط تلقائياً دون إظهاره كـ نص
header("Location: $secret_url");
exit;
?>
