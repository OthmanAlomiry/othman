<?php
// إعدادات المتغيرات
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = $_POST['video_url'];

    // فحص نوع الرابط (تيك توك أم سناب شات)
    if (strpos($url, 'tiktok.com') !== false) {
        // --- معالجة تيك توك ---
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
        // --- معالجة سناب شات ---
        // ملاحظة: روابط سناب شات تتطلب أحياناً استخراج يدوي أو استخدام API وسيط
        // سنستخدم هنا طريقة البحث في سورس الصفحة لجلب رابط m3u8 أو mp4 المباشر
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1");
        $html = curl_exec($ch);
        curl_close($ch);

        // محاولة استخراج رابط الفيديو المباشر من الميتا تاغ في سناب
        preg_match('/<meta property="og:video" content="(.*?)"/', $html, $matches);
        $videoUrl = $matches[1] ?? null;
        
        preg_match('/<meta property="og:image" content="(.*?)"/', $html, $imgMatches);
        $imgUrl = $imgMatches[1] ?? '';

        if ($videoUrl) {
            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $imgUrl,
                'title' => 'Snapchat Spotlight'
            ];
        }
    }

    if (!$videoData) {
        $error = "عذراً، لم نتمكن من جلب الفيديو. تأكد من صحة الرابط.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محمل فيديوهات د-سيرفس الشامل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #121212; color: white; font-family: 'Segoe UI', sans-serif; text-align: center; padding-top: 30px; }
        .card { background: #1e1e1e; border: none; border-radius: 25px; padding: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.6); }
        .input-group input { background: #2c2c2c; border: 1px solid #444; color: white; border-radius: 12px 0 0 12px !important; }
        .btn-fetch { background: #fe2c55; border: none; color: white; border-radius: 0 12px 12px 0 !important; font-weight: bold; }
        video { width: 100%; max-width: 320px; border-radius: 20px; border: 2px solid #333; margin-bottom: 20px; background: #000; }
        .btn-action { font-weight: bold; padding: 15px; border-radius: 15px; width: 100%; margin-bottom: 12px; border: none; transition: 0.3s; }
        .btn-files { background: #28a745; color: white; }
        .btn-save-ios { background: #007bff; color: white; }
        .platform-icons { font-size: 0.8em; color: #888; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card col-md-6 mx-auto">
        <h4 class="mb-2">محمل الفيديوهات الذكي</h4>
        <p class="platform-icons">يدعم: تيك توك | سناب شات (Spotlight & Stories)</p>
        
        <form method="POST">
            <div class="input-group mb-4 mt-3">
                <input type="text" name="video_url" class="form-control" placeholder="انسخ الرابط وضعه هنا..." required>
                <button class="btn btn-fetch" type="submit">جلب الفيديو</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline webkit-playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="d-grid">
                <a href="<?php echo $videoData['play']; ?>" download="video.mp4" class="btn-action btn-files text-decoration-none">
                    📥 تحميل إلى "الملفات"
                </a>

                <button onclick="shareVideo('<?php echo $videoData['play']; ?>', '<?php echo htmlspecialchars($videoData['title']); ?>')" class="btn-action btn-save-ios">
                    📤 مشاركة وحفظ في الاستوديو
                </button>
            </div>
            
            <p class="mt-2 text-muted small">اضغط "مشاركة" ثم اختر "Save Video" للحفظ في الصور.</p>
        <?php endif; ?>
    </div>
</div>

<script>
async function shareVideo(url, title) {
    if (navigator.share) {
        try {
            const btn = event.target;
            btn.innerText = "⏳ جاري المعالجة...";
            
            const response = await fetch(url);
            const blob = await response.blob();
            const file = new File([blob], "video.mp4", { type: "video/mp4" });

            await navigator.share({
                files: [file],
                title: title
            });
            btn.innerText = "📤 مشاركة وحفظ في الاستوديو";
        } catch (error) {
            alert("فشل في فتح قائمة المشاركة. تأكد من فتح الموقع في متصفح سفاري.");
            btn.innerText = "📤 مشاركة وحفظ في الاستوديو";
        }
    } else {
        alert("متصفحك لا يدعم خاصية المشاركة المباشرة.");
    }
}
</script>

</body>
</html>
