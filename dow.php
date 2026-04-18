<?php
/**
 * د-سيرفس V11 - معالج الملفات الشخصية (@username)
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    // 1. تنظيف الرابط من الفراغات والرموز الزائدة
    if (preg_match('/(https:\/\/www\.snapchat\.com\/@[^\s?]+)/', $url, $cleanUrl)) {
        $url = $cleanUrl[1]; 
    }

    if (strpos($url, 'snapchat.com') !== false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        // استخدام User-Agent يحاكي أحدث نظام أيفون بدقة لتجاوز الحجب
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Mobile/15E148 Safari/604.1");
        
        $html = curl_exec($ch);
        curl_close($ch);

        // البحث عن روابط الميديا (نبحث عن أحدث فيديو بصيغة MP4)
        preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
        
        if (!empty($matches[1])) {
            // جلب آخر فيديو تمت إضافته (الأحدث)
            $latestVideo = end($matches[1]);
            $videoUrl = str_replace('\\u002F', '/', $latestVideo);
            
            // جلب صورة الغلاف
            preg_match('/property="og:image" content="(.*?)"/', $html, $img);
            
            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $img[1] ?? '',
                'title' => 'Snapchat Story'
            ];
        } else {
            // محاولة جلب رابط الفيديو من وسم og:video في حال كان Spotlight
            preg_match('/property="og:video" content="(.*?)"/', $html, $ogMatch);
            if (!empty($ogMatch[1])) {
                $videoData = [
                    'play' => htmlspecialchars_decode($ogMatch[1]),
                    'cover' => '',
                    'title' => 'Snapchat Video'
                ];
            }
        }
    }

    if (!$videoData) {
        $error = "لم نجد سنابات عامة نشطة حالياً لهذا البروفايل. تأكد أن الحساب يحتوي على 'قصة عامة' (Public Story) منشورة الآن.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | محمل البروفايلات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; padding: 10px; }
        .main-card { max-width: 460px; margin: 30px auto; background: #121212; border-radius: 35px; padding: 25px; border: 1px solid #222; box-shadow: 0 30px 60px rgba(0,0,0,0.9); }
        .logo { color: #FFFC00; font-weight: 900; font-size: 2.1rem; text-shadow: 0 0 10px rgba(255, 252, 0, 0.2); }
        .input-area { background: #1d1d1d; border-radius: 20px; padding: 8px; border: 1px solid #333; display: flex; align-items: center; }
        .form-control { background: transparent; border: none; color: #fff; text-align: center; font-size: 0.9rem; }
        .form-control:focus { background: transparent; color: #fff; box-shadow: none; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 12px 25px; transition: 0.3s; }
        .btn-fetch:hover { transform: scale(1.05); background: #fffb00; }
        video { width: 100%; border-radius: 25px; margin-top: 20px; border: 1px solid #333; background: #000; }
        .action-btn { display: block; width: 100%; padding: 16px; margin-top: 12px; border-radius: 20px; font-weight: bold; text-decoration: none; text-align: center; border: none; font-size: 1rem; }
        .btn-download { background: #007bff; color: white; }
        .alert-custom { background: rgba(255, 0, 0, 0.05); border: 1px solid rgba(255,0,0,0.2); color: #ff6b6b; border-radius: 15px; padding: 15px; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="main-card text-center">
        <h1 class="logo mb-2">د-سيرفس</h1>
        <p class="text-muted small mb-4">جلب أحدث القصص من رابط البروفايل</p>

        <form method="POST" id="fetchForm">
            <div class="input-area">
                <input type="text" name="video_url" class="form-control" placeholder="انسخ رابط البروفايل هنا..." required>
                <button type="submit" class="btn-fetch" id="btnText">جلب</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert-custom mt-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="snapVideo" controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="mt-4">
                <button onclick="saveIos('<?php echo $videoData['play']; ?>')" class="action-btn btn-download">
                    📥 حفظ في الاستوديو (أيفون)
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // تغيير نص الزر عند الجلب لمنع التكرار
    document.getElementById('fetchForm').onsubmit = function() {
        document.getElementById('btnText').innerText = "⏳...";
    };

    async function saveIos(url) {
        if (!navigator.share) { alert("افتح الموقع من متصفح سفاري"); return; }
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
