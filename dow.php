<?php
/**
 * سكربت د-سيرفس V9 - النسخة المستقرة
 * معالجة مشاكل عدم استجابة زر الجلب
 */

error_reporting(0); // إخفاء الأخطاء التقنية عن المستخدم
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    // --- محرك جلب تيك توك ---
    if (strpos($url, 'tiktok.com') !== false) {
        $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        $result = json_decode($res, true);
        curl_close($ch);

        if ($result && isset($result['data'])) {
            $videoData = [
                'play' => $result['data']['play'],
                'cover' => $result['data']['cover']
            ];
        }
    } 
    // --- محرك جلب سناب شات ---
    elseif (strpos($url, 'snapchat.com') !== false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1");
        $html = curl_exec($ch);
        curl_close($ch);

        // جلب أحدث فيديو MP4
        preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
        
        if (!empty($matches[1])) {
            $latest = end($matches[1]);
            $videoUrl = str_replace('\\u002F', '/', $latest);
            preg_match('/property="og:image" content="(.*?)"/', $html, $img);
            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $img[1] ?? ''
            ];
        }
    }

    if (!$videoData) {
        $error = "عذراً، تعذر جلب المحتوى. تأكد من أن الرابط صحيح وعام.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | جلب الفيديو</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #000; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .app-container { max-width: 500px; margin: 50px auto; padding: 25px; background: #111; border-radius: 25px; border: 1px solid #333; }
        .brand { color: #FFFC00; font-weight: 900; font-size: 2.2rem; margin-bottom: 20px; }
        .input-group { background: #1a1a1a; border-radius: 15px; padding: 5px; border: 1px solid #444; }
        .form-control { background: transparent; border: none; color: #fff; text-align: center; }
        .form-control::placeholder { color: #666; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 12px; border: none; padding: 10px 25px; transition: 0.3s; }
        .btn-fetch:active { transform: scale(0.95); }
        video { width: 100%; border-radius: 15px; margin-top: 20px; border: 1px solid #222; max-height: 70vh; }
        .action-area { margin-top: 20px; }
        .btn-custom { display: block; width: 100%; padding: 15px; border-radius: 15px; font-weight: bold; text-decoration: none; text-align: center; margin-bottom: 10px; border: none; }
        .btn-ios { background: #007bff; color: #fff; }
        .btn-files { background: #28a745; color: #fff; }
        .loading-spinner { display: none; margin: 20px auto; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #FFFC00; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="container text-center">
    <div class="app-container">
        <h1 class="brand">د-سيرفس</h1>
        
        <form id="fetchForm" method="POST">
            <div class="input-group">
                <input type="text" name="video_url" class="form-control" placeholder="ضع رابط سناب أو تيك توك هنا" required>
                <button type="submit" class="btn-fetch" id="submitBtn">جلب</button>
            </div>
        </form>

        <div id="loader" class="loading-spinner"></div>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-3 small border-0 bg-danger bg-opacity-10 text-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="action-area">
                <button onclick="saveToIos('<?php echo $videoData['play']; ?>')" class="btn-custom btn-ios">📤 حفظ في الاستوديو</button>
                <a href="<?php echo $videoData['play']; ?>" download="D-Service_Video.mp4" class="btn-custom btn-files">📥 تحميل كملف</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // إظهار مؤشر التحميل عند الضغط لمنع تكرار الضغط
    document.getElementById('fetchForm').onsubmit = function() {
        document.getElementById('submitBtn').style.display = 'none';
        document.getElementById('loader').style.display = 'block';
    };

    async function saveToIos(url) {
        if (!navigator.share) { alert("يرجى فتح الموقع من متصفح سفاري"); return; }
        try {
            const res = await fetch(url);
            const blob = await res.blob();
            const file = new File([blob], "video.mp4", { type: "video/mp4" });
            await navigator.share({ files: [file] });
        } catch (e) {
            console.error(e);
        }
    }
</script>

</body>
</html>
