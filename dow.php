<?php
/**
 * د-سيرفس V10 - المعالج الذكي والنهائي
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    // --- معالجة تيك توك ---
    if (strpos($url, 'tiktok.com') !== false) {
        $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        $result = json_decode($res, true);
        curl_close($ch);
        if ($result && isset($result['data'])) {
            $videoData = ['play' => $result['data']['play'], 'cover' => $result['data']['cover']];
        }
    } 
    // --- معالجة سناب شات الذكية ---
    elseif (strpos($url, 'snapchat.com') !== false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1");
        $html = curl_exec($ch);
        curl_close($ch);

        // البحث في كود الصفحة عن الروابط المباشرة
        preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
        
        if (!empty($matches[1])) {
            $latest = end($matches[1]); // جلب أحدث سنابة
            $videoUrl = str_replace('\\u002F', '/', $latest);
            preg_match('/property="og:image" content="(.*?)"/', $html, $img);
            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $img[1] ?? ''
            ];
        }
    }

    if (!$videoData) {
        $error = "عذراً، روابط المشاركة (t/) محمية حالياً. يفضل وضع رابط الملف الشخصي المباشر (مثل snapchat.com/add/username) لجلب السنابات بنجاح.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | محمل السنابات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; }
        .app-box { max-width: 480px; margin: 40px auto; background: #111; border-radius: 30px; padding: 30px; border: 1px solid #333; box-shadow: 0 25px 50px rgba(0,0,0,0.9); }
        .logo-text { color: #FFFC00; font-weight: 900; font-size: 2.3rem; margin-bottom: 5px; }
        .input-group { background: #1a1a1a; border-radius: 20px; padding: 6px; border: 1px solid #444; }
        .form-control { background: transparent; border: none; color: #fff; text-align: center; }
        .btn-yellow { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 10px 25px; }
        video { width: 100%; border-radius: 20px; margin-top: 25px; border: 1px solid #FFFC00; }
        .btn-action { display: block; width: 100%; padding: 16px; margin-top: 10px; border-radius: 18px; font-weight: bold; text-decoration: none; text-align: center; }
        .btn-blue { background: #007bff; color: white; }
        .btn-green { background: #28a745; color: white; }
        .loading { display: none; color: #FFFC00; margin: 15px 0; }
    </style>
</head>
<body>

<div class="container text-center">
    <div class="app-box">
        <h1 class="logo-text">د-سيرفس</h1>
        <p class="text-muted small mb-4">انسخ رابط "البروفايل" أو رابط "السنابة"</p>

        <form id="mainForm" method="POST">
            <div class="input-group">
                <input type="text" name="video_url" class="form-control" placeholder="https://snapchat.com/add/..." required>
                <button type="submit" class="btn-yellow">جلب</button>
            </div>
        </form>

        <div id="loader" class="loading">⏳ جاري البحث عن السنابات...</div>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-4 bg-danger bg-opacity-10 border-0 text-danger small p-3" style="border-radius: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="mt-4">
                <button onclick="shareToIos('<?php echo $videoData['play']; ?>')" class="btn-action btn-blue">📤 حفظ في الاستوديو</button>
                <a href="<?php echo $videoData['play']; ?>" download="D-Service.mp4" class="btn-action btn-green">📥 تحميل للملفات</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('mainForm').onsubmit = function() {
        document.getElementById('loader').style.display = 'block';
    };

    async function shareToIos(url) {
        if (!navigator.share) { alert("افتح الموقع من سفاري"); return; }
        try {
            const response = await fetch(url);
            const blob = await response.blob();
            const file = new File([blob], "video.mp4", { type: "video/mp4" });
            await navigator.share({ files: [file] });
        } catch (e) { console.error(e); }
    }
</script>

</body>
</html>
