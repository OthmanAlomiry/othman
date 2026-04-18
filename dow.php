<?php
/**
 * سكربت د-سيرفس للتحميل الذكي (تيك توك & سناب شات)
 * مبرمج ليعمل بكفاءة على الأيفون والأندرويد
 */

$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    if (strpos($url, 'tiktok.com') !== false) {
        // --- معالجة تيك توك (TikTok) ---
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
        // --- معالجة سناب شات المتطورة (Snapchat) ---
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // تتبع الروابط المختصرة
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1");
        $html = curl_exec($ch);
        curl_close($ch);

        // محاولة استخراج الرابط المباشر من JSON أو Meta Tags
        preg_match('/"contentUrl":"(.*?)"/', $html, $matches);
        if (empty($matches)) {
            preg_match('/property="og:video" content="(.*?)"/', $html, $matches);
        }

        $videoUrl = !empty($matches[1]) ? str_replace('\\u002F', '/', $matches[1]) : null;
        
        preg_match('/property="og:image" content="(.*?)"/', $html, $imgMatches);
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
        $error = "عذراً، لم نتمكن من جلب الفيديو. تأكد من أن الرابط عام وصحيح.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محمل فيديوهات د-سيرفس</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #121212; color: white; font-family: 'Segoe UI', Tahoma, sans-serif; text-align: center; padding-top: 30px; }
        .card { background: #1e1e1e; border: none; border-radius: 25px; padding: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.6); max-width: 500px; margin: auto; }
        .form-control { background: #2c2c2c; border: 1px solid #444; color: white; border-radius: 12px !important; text-align: center; }
        .form-control:focus { background: #333; color: white; border-color: #fe2c55; box-shadow: none; }
        .btn-fetch { background: #fe2c55; border: none; color: white; font-weight: bold; padding: 12px 30px; border-radius: 12px; margin-top: 10px; width: 100%; }
        video { width: 100%; border-radius: 20px; border: 2px solid #333; margin-top: 20px; background: #000; }
        .btn-action { font-weight: bold; padding: 15px; border-radius: 15px; width: 100%; margin-top: 15px; border: none; transition: 0.3s; display: block; text-decoration: none; }
        .btn-files { background: #28a745; color: white; }
        .btn-save-ios { background: #007bff; color: white; }
        .info-box { background: #2c2c2c; padding: 12px; border-radius: 12px; font-size: 0.85em; color: #bbb; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h4 class="mb-3">محمل الفيديوهات الذكي</h4>
        <p class="text-muted small mb-4">تيك توك | سناب شات</p>
        
        <form method="POST">
            <input type="text" name="video_url" class="form-control mb-2" placeholder="ضع الرابط هنا (TikTok أو Snapchat)" required autocomplete="off">
            <button class="btn btn-fetch" type="submit">جلب الفيديو</button>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-3 py-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline webkit-playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="mt-2">
                <a href="<?php echo $videoData['play']; ?>" download="video.mp4" class="btn-action btn-files">
                    📥 تحميل (تطبيق الملفات)
                </a>

                <button id="shareBtn" onclick="shareVideo('<?php echo $videoData['play']; ?>')" class="btn-action btn-save-ios">
                    📤 مشاركة وحفظ في الاستوديو
                </button>
            </div>
            
            <div class="info-box">
                <b>للأيفون:</b> اضغط "مشاركة" ثم اختر <b>Save Video</b> ليتم حفظه في الصور فوراً.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function shareVideo(url) {
    const btn = document.getElementById('shareBtn');
    if (navigator.share) {
        try {
            const originalText = btn.innerText;
            btn.innerText = "⏳ جاري التحميل للمعالجة...";
            btn.disabled = true;

            const response = await fetch(url);
            const blob = await response.blob();
            const file = new File([blob], "video.mp4", { type: "video/mp4" });

            await navigator.share({
                files: [file],
                title: 'Video Download'
            });
            
            btn.innerText = originalText;
            btn.disabled = false;
        } catch (error) {
            btn.innerText = "📤 مشاركة وحفظ في الاستوديو";
            btn.disabled = false;
            if(error.name !== 'AbortError') {
                alert("يرجى استخدام متصفح سفاري على الأيفون لضمان الحفظ.");
            }
        }
    } else {
        alert("خاصية المشاركة غير مدعومة في هذا المتصفح.");
    }
}
</script>

</body>
</html>
