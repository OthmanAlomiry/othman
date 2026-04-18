<?php
/**
 * سكربت د-سيرفس المطور V3
 * دعم روابط سناب شات القصيرة والطويلة بدقة عالية
 */

$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    if (strpos($url, 'tiktok.com') !== false) {
        // --- تيك توك ---
        $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($result && isset($result['data'])) {
            $videoData = [
                'play' => $result['data']['play'],
                'cover' => $result['data']['cover'],
                'title' => $result['data']['title'] ?: 'TikTok Video'
            ];
        }
    } elseif (strpos($url, 'snapchat.com') !== false) {
        // --- سناب شات المتطور ---
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1");
        $html = curl_exec($ch);
        curl_close($ch);

        // محاولة استخراج الرابط المباشر (MP4) من سورس الصفحة
        // الطريقة الأولى: البحث عن رابط media.snapchat.com في بيانات الـ JSON
        if (preg_match('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches)) {
            $videoUrl = $matches[1];
        } 
        // الطريقة الثانية: البحث عن رابط og:video
        elseif (preg_match('/property="og:video" content="(.*?)"/', $html, $matches)) {
            $videoUrl = $matches[1];
        }
        // الطريقة الثالثة: البحث عن رابط الفيديوهات القصيرة (Spotlight)
        elseif (preg_match('/"contentUrl":"(.*?)"/', $html, $matches)) {
            $videoUrl = $matches[1];
        } else {
            $videoUrl = null;
        }

        if ($videoUrl) {
            // تنظيف الرابط من أي ترميز يونيكود
            $videoUrl = str_replace('\\u002F', '/', $videoUrl);
            $videoUrl = htmlspecialchars_decode($videoUrl);

            // استخراج صورة الغلاف
            preg_match('/property="og:image" content="(.*?)"/', $html, $imgMatches);
            $imgUrl = $imgMatches[1] ?? '';

            $videoData = [
                'play' => $videoUrl,
                'cover' => $imgUrl,
                'title' => 'Snapchat Content'
            ];
        }
    }

    if (!$videoData) {
        $error = "عذراً، لم نتمكن من جلب الفيديو. تأكد أن الرابط عام (Public) وليس من محادثة خاصة.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | تحميل فيديو</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0b0b0b; color: #f0f0f0; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .main-container { max-width: 480px; margin: auto; background: #161616; border-radius: 30px; padding: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.8); border: 1px solid #222; }
        .header-title { font-weight: 800; color: #fe2c55; letter-spacing: 1px; }
        .input-group { background: #222; border-radius: 15px; padding: 5px; border: 1px solid #333; }
        .form-control { background: transparent; border: none; color: white; text-align: center; }
        .form-control:focus { background: transparent; color: white; box-shadow: none; }
        .btn-fetch { background: #fe2c55; border: none; border-radius: 12px; font-weight: bold; padding: 10px 20px; }
        video { width: 100%; border-radius: 20px; border: 1px solid #333; margin-top: 25px; background: #000; }
        .btn-download { display: block; width: 100%; padding: 15px; margin-top: 15px; border-radius: 15px; font-weight: bold; text-decoration: none; text-align: center; transition: 0.3s; }
        .btn-share { background: #007bff; color: white; border: none; }
        .btn-files { background: #28a745; color: white; }
        .alert { border-radius: 15px; background: rgba(220, 53, 69, 0.1); border: 1px solid #dc3545; color: #dc3545; }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="main-container">
        <h2 class="header-title mb-1">د-سيرفس</h2>
        <p class="text-muted small mb-4">تيك توك • سناب شات</p>

        <form method="POST">
            <div class="input-group mb-3">
                <input type="text" name="video_url" class="form-control" placeholder="ضع الرابط هنا..." required>
                <button class="btn-fetch text-white" type="submit">جلب</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert mt-3 small"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline webkit-playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="d-grid gap-2">
                <button id="shareBtn" onclick="saveToGallery('<?php echo $videoData['play']; ?>')" class="btn-download btn-share">
                    📤 حفظ في استوديو الصور
                </button>
                <a href="<?php echo $videoData['play']; ?>" download="video.mp4" class="btn-download btn-files">
                    📥 تحميل (تطبيق الملفات)
                </a>
            </div>
            
            <p class="mt-3 text-muted small">للأيفون: انتظر التحميل ثم اختر <b>Save Video</b></p>
        <?php endif; ?>
    </div>
</div>

<script>
async function saveToGallery(url) {
    const btn = document.getElementById('shareBtn');
    if (!navigator.share) {
        alert("يرجى استخدام متصفح سفاري للأيفون.");
        return;
    }

    try {
        const originalText = btn.innerText;
        btn.innerText = "⏳ جاري المعالجة...";
        btn.disabled = true;

        const response = await fetch(url);
        const blob = await response.blob();
        const file = new File([blob], "snap_video.mp4", { type: "video/mp4" });

        await navigator.share({
            files: [file],
            title: 'Download Video'
        });
        
        btn.innerText = originalText;
        btn.disabled = false;
    } catch (e) {
        btn.innerText = "📤 حفظ في استوديو الصور";
        btn.disabled = false;
    }
}
</script>

</body>
</html>
