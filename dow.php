<?php
/**
 * سكربت د-سيرفس المطور - دعم تيك توك وسناب شات بدقة عالية
 */

$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    if (strpos($url, 'tiktok.com') !== false) {
        // --- تيك توك (بدون علامة مائية عبر API خارجي) ---
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
        // --- سناب شات (استخراج الفيديو الأصلي بدقة) ---
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1");
        $html = curl_exec($ch);
        curl_close($ch);

        // محاولة استخراج الرابط من داخل سكريبت البيانات لضمان دقة الفيديو المطلوب
        // نبحث عن الرابط الذي يحتوي على كلمة media.snapchat.com
        preg_match('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
        
        // إذا لم ينجح، نبحث عن الرابط في الوسوم البديلة
        if (empty($matches)) {
            preg_match('/property="og:video" content="(.*?)"/', $html, $matches);
        }

        $videoUrl = !empty($matches[1]) ? str_replace('\\u002F', '/', $matches[1]) : null;
        
        // استخراج صورة الغلاف
        preg_match('/property="og:image" content="(.*?)"/', $html, $imgMatches);
        $imgUrl = $imgMatches[1] ?? '';

        if ($videoUrl) {
            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $imgUrl,
                'title' => 'Snapchat Video'
            ];
        }
    }

    if (!$videoData) {
        $error = "عذراً، لم نتمكن من جلب الفيديو المطلوب. تأكد من أن الحساب عام والرابط صحيح.";
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
        body { background: #0f0f0f; color: #fff; font-family: 'Segoe UI', system-ui, sans-serif; text-align: center; padding: 20px; }
        .main-card { background: #1a1a1a; border: 1px solid #333; border-radius: 25px; padding: 25px; max-width: 450px; margin: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.7); }
        .input-box { background: #262626; border: 1px solid #444; color: #fff; border-radius: 15px !important; padding: 12px; text-align: center; margin-bottom: 10px; }
        .btn-fetch { background: linear-gradient(45deg, #fe2c55, #ff5050); border: none; color: #fff; font-weight: bold; border-radius: 15px; width: 100%; padding: 12px; transition: 0.3s; }
        .btn-fetch:active { transform: scale(0.98); }
        video { width: 100%; border-radius: 20px; border: 2px solid #fe2c55; margin-top: 20px; background: #000; box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
        .action-area { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
        .btn-ios { background: #007bff; color: white; font-weight: bold; padding: 15px; border-radius: 15px; text-decoration: none; border: none; }
        .btn-files { background: #28a745; color: white; font-weight: bold; padding: 15px; border-radius: 15px; text-decoration: none; }
        .footer-note { background: #222; padding: 10px; border-radius: 12px; font-size: 0.8em; color: #aaa; margin-top: 20px; border-right: 3px solid #fe2c55; }
    </style>
</head>
<body>

<div class="container">
    <div class="main-card">
        <h4 class="mb-1">د-سيرفس</h4>
        <p class="text-muted small">تحميل فيديوهات تيك توك وسناب شات</p>
        
        <form method="POST">
            <input type="text" name="video_url" class="form-control input-box" placeholder="ضع رابط الفيديو هنا..." required autocomplete="off">
            <button class="btn btn-fetch" type="submit">جلب محتوى الفيديو</button>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-3 small p-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline webkit-playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="action-area">
                <button id="shareBtn" onclick="shareToIphone('<?php echo $videoData['play']; ?>')" class="btn-ios">
                    📤 حفظ في استوديو الصور
                </button>

                <a href="<?php echo $videoData['play']; ?>" download="D-Service-Video.mp4" class="btn-files">
                    📥 تحميل (تطبيق الملفات)
                </a>
            </div>
            
            <div class="footer-note text-start">
                <b>ملاحظة للأيفون:</b> بعد الضغط على "حفظ في الاستوديو"، انتظر التحميل ثم اختر <b>Save Video</b> من القائمة.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function shareToIphone(url) {
    const btn = document.getElementById('shareBtn');
    if (!navigator.share) {
        alert("متصفحك لا يدعم هذه الميزة، يرجى استخدام متصفح سفاري.");
        return;
    }

    try {
        const originalText = btn.innerText;
        btn.innerText = "⏳ جاري التحميل... انتظر ثواني";
        btn.disabled = true;

        const response = await fetch(url);
        const blob = await response.blob();
        const file = new File([blob], "video.mp4", { type: "video/mp4" });

        await navigator.share({
            files: [file],
            title: 'D-Service Video'
        });
        
        btn.innerText = originalText;
        btn.disabled = false;
    } catch (error) {
        btn.innerText = "📤 حفظ في استوديو الصور";
        btn.disabled = false;
        if(error.name !== 'AbortError') {
            alert("حدث خطأ، تأكد من فتح الموقع في سفاري.");
        }
    }
}
</script>

</body>
</html>
