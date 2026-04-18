<?php
/**
 * د-سيرفس V13 - جلب أحدث سنابة وحيدة باليوزر
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['snap_username'])) {
    $username = trim($_POST['snap_username']);
    $username = ltrim($username, '@');
    
    // بناء رابط البروفايل
    $url = "https://www.snapchat.com/add/" . $username;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    // محاكاة متصفح أيفون حديث جداً
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1");
    
    $html = curl_exec($ch);
    curl_close($ch);

    // استخراج جميع روابط الـ MP4
    preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
    
    if (!empty($matches[1])) {
        // التعديل: نأخذ آخر فيديو في القائمة (غالباً هو الأحدث في صفحة البروفايل)
        $latestVideo = end($matches[1]); 
        $videoUrl = str_replace('\\u002F', '/', $latestVideo);
        
        // جلب صورة الغلاف (Thumbnail)
        preg_match('/property="og:image" content="(.*?)"/', $html, $img);
        
        $videoData = [
            'play' => htmlspecialchars_decode($videoUrl),
            'cover' => $img[1] ?? ''
        ];
    } else {
        $error = "لم نجد مقاطع فيديو عامة لهذا اليوزر. تأكد أن الحساب يحتوي على قصص (Public Stories) حالياً.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | أحدث سنابة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0b0b0b; color: #fff; font-family: 'Segoe UI', sans-serif; }
        .app-card { max-width: 480px; margin: 40px auto; background: #161616; border-radius: 40px; padding: 35px; border: 1px solid #333; }
        .logo { color: #FFFC00; font-weight: 900; font-size: 2.5rem; letter-spacing: -1px; }
        .input-group { background: #222; border-radius: 20px; padding: 6px; border: 1px solid #444; }
        .form-control { background: transparent !important; border: none !important; color: #fff !important; text-align: center; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 12px 30px; }
        video { width: 100%; border-radius: 25px; margin-top: 30px; border: 2px solid #FFFC00; box-shadow: 0 0 20px rgba(255, 252, 0, 0.1); }
        .btn-save { display: block; width: 100%; padding: 18px; margin-top: 20px; border-radius: 20px; font-weight: bold; text-decoration: none; text-align: center; background: #007bff; color: #fff; }
        .loader { display: none; margin-top: 20px; color: #FFFC00; }
    </style>
</head>
<body>

<div class="container">
    <div class="app-card text-center">
        <h1 class="logo">د-سيرفس</h1>
        <p class="text-muted small mb-4">جلب أحدث مقطع فيديو للمستخدم</p>

        <form method="POST" id="snapForm">
            <div class="input-group">
                <input type="text" name="snap_username" class="form-control" placeholder="اكتب اليوزر هنا" required autocomplete="off">
                <button type="submit" class="btn-fetch">جلب</button>
            </div>
        </form>

        <div id="loader" class="loader">🔍 جاري استخراج أحدث سنابة...</div>

        <?php if ($error): ?>
            <div class="alert mt-4 bg-danger bg-opacity-10 text-danger border-0 small" style="border-radius: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>
            
            <button onclick="iosDownload('<?php echo $videoData['play']; ?>')" class="btn-save">
                📥 حفظ الفيديو (أيفون)
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('snapForm').onsubmit = function() {
        document.querySelector('.btn-fetch').style.display = 'none';
        document.getElementById('loader').style.display = 'block';
    };

    async function iosDownload(url) {
        if (!navigator.share) { alert("استخدم متصفح سفاري للحفظ"); return; }
        try {
            const res = await fetch(url);
            const blob = await res.blob();
            const file = new File([blob], "snap_video.mp4", { type: "video/mp4" });
            await navigator.share({ files: [file] });
        } catch (e) { console.error(e); }
    }
</script>

</body>
</html>
