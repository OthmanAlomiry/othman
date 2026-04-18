<?php
/**
 * سكربت د-سيرفس V7 - النسخة الأكثر استقراراً
 * دعم شامل لتيك توك وسناب شات (Profile & Spotlight)
 */

$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    if (strpos($url, 'tiktok.com') !== false) {
        // --- معالجة تيك توك (يعمل بامتياز) ---
        $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($result && isset($result['data'])) {
            $videoData = [
                'play' => $result['data']['play'],
                'cover' => $result['data']['cover']
            ];
        }
    } elseif (strpos($url, 'snapchat.com') !== false) {
        // --- معالجة سناب شات (تتبع العمق) ---
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // تتبع التحويلات
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        // التظاهر بأننا متصفح أيفون حقيقي
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1");
        
        $html = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        // محاولة استخراج الرابط من 4 أنماط مختلفة
        // 1. نمط الميديا المباشر
        preg_match('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $m1);
        // 2. نمط og:video
        preg_match('/property="og:video" content="(.*?)"/', $html, $m2);
        // 3. نمط الـ Spotlight JSON
        preg_match('/"contentUrl":"(.*?)"/', $html, $m3);
        // 4. نمط التنزيل المباشر
        preg_match('/<video.*?src="(.*?)"/', $html, $m4);

        $rawUrl = $m1[1] ?? $m2[1] ?? $m3[1] ?? $m4[1] ?? null;

        if ($rawUrl) {
            $videoUrl = str_replace('\\u002F', '/', $rawUrl);
            $videoUrl = htmlspecialchars_decode($videoUrl);
            
            preg_match('/property="og:image" content="(.*?)"/', $html, $img);

            $videoData = [
                'play' => $videoUrl,
                'cover' => $img[1] ?? ''
            ];
        }
    }

    if (!$videoData) {
        $error = "للأسف، سناب شات تحجب جلب هذا الرابط آلياً. جرب نسخ 'رابط الملف الشخصي' المباشر بدلاً من رابط المشاركة.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | التحميل الذكي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --snap-color: #FFFC00; --tt-color: #fe2c55; }
        body { background: #000; color: #fff; font-family: system-ui, -apple-system, sans-serif; padding: 15px; }
        .main-card { max-width: 450px; margin: 40px auto; background: #121212; border-radius: 30px; padding: 25px; border: 1px solid #222; box-shadow: 0 20px 50px rgba(0,0,0,0.8); }
        .logo { font-weight: 900; font-size: 2.2rem; color: var(--snap-color); text-shadow: 0 0 15px rgba(255, 252, 0, 0.2); }
        .input-group { background: #1a1a1a; border-radius: 20px; padding: 5px; border: 1px solid #333; }
        .form-control { background: transparent; border: none; color: #fff; text-align: center; }
        .form-control:focus { background: transparent; color: #fff; box-shadow: none; }
        .btn-main { background: var(--snap-color); color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 10px 25px; }
        video { width: 100%; border-radius: 20px; margin-top: 20px; border: 1px solid #333; }
        .btn-action { display: block; width: 100%; padding: 16px; margin-top: 10px; border-radius: 18px; font-weight: bold; text-decoration: none; text-align: center; border: none; transition: 0.3s; }
        .btn-share { background: #007bff; color: white; }
        .btn-files { background: #28a745; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="main-card text-center">
        <h1 class="logo mb-2">د-سيرفس</h1>
        <p class="text-muted small mb-4">أدخل رابط سناب شات أو تيك توك</p>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="video_url" class="form-control" placeholder="ضع الرابط هنا..." required>
                <button class="btn btn-main" type="submit">جلب</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert mt-4 border-0 bg-danger bg-opacity-10 text-danger small p-3" style="border-radius: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="mt-3">
                <button id="shareBtn" onclick="iosSave('<?php echo $videoData['play']; ?>')" class="btn-action btn-share">
                    📤 حفظ في الاستوديو (أيفون)
                </button>
                <a href="<?php echo $videoData['play']; ?>" download="Video-D-Service.mp4" class="btn-action btn-files">
                    📥 تحميل للملفات
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function iosSave(url) {
    const btn = document.getElementById('shareBtn');
    if (!navigator.share) { alert("استخدم متصفح سفاري."); return; }
    try {
        const text = btn.innerText;
        btn.innerText = "⏳ جاري التحميل...";
        btn.disabled = true;
        const res = await fetch(url);
        const blob = await res.blob();
        const file = new File([blob], "video.mp4", { type: "video/mp4" });
        await navigator.share({ files: [file] });
        btn.innerText = text; btn.disabled = false;
    } catch (e) {
        btn.innerText = "📤 حفظ في الاستوديو (أيفون)"; btn.disabled = false;
    }
}
</script>

</body>
</html>
