<?php
/**
 * سكربت د-سيرفس المطور V6
 * معالج ذكي لاستخراج السنابات من اسم المستخدم مباشرة
 */

$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

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
                'title' => 'TikTok Video'
            ];
        }
    } elseif (strpos($url, 'snapchat.com') !== false) {
        // --- معالجة سناب شات الذكية ---
        
        // 1. تتبع الرابط لجلب الرابط الحقيقي إذا كان مختصراً
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36");
        $html = curl_exec($ch);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        // 2. البحث عن روابط الفيديو المباشرة في الصفحة النهائية
        // نبحث عن نمط القصص العامة (Story) أو Spotlight
        preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
        
        if (!empty($matches[1])) {
            $videoUrl = str_replace('\\u002F', '/', $matches[1][0]);
            preg_match('/property="og:image" content="(.*?)"/', $html, $imgMatches);
            
            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $imgMatches[1] ?? '',
                'title' => 'Snapchat Content'
            ];
        } else {
            // محاولة أخيرة عبر وسوم الـ Metadata
            preg_match('/property="og:video" content="(.*?)"/', $html, $matches);
            if (!empty($matches[1])) {
                $videoData = [
                    'play' => htmlspecialchars_decode($matches[1]),
                    'cover' => '',
                    'title' => 'Snapchat Video'
                ];
            }
        }
    }

    if (!$videoData) {
        $error = "عذراً، لم نتمكن من جلب الفيديو. تأكد من أن الحساب يحتوي على قصص عامة حالياً وأنك تستخدم متصفح سفاري.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | تحميل ذكي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; text-align: center; padding: 20px; }
        .card-main { background: #111; border: 1px solid #333; border-radius: 30px; padding: 30px; max-width: 450px; margin: auto; box-shadow: 0 20px 50px rgba(0,0,0,0.9); }
        .yellow-text { color: #FFFC00; font-weight: 900; font-size: 2.2rem; }
        .input-field { background: #222; border: 1px solid #444; color: #fff; border-radius: 15px !important; text-align: center; }
        .btn-yellow { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 10px 30px; }
        video { width: 100%; border-radius: 20px; margin-top: 20px; border: 1px solid #FFFC00; }
        .action-btn { display: block; width: 100%; padding: 15px; margin-top: 10px; border-radius: 15px; font-weight: bold; text-decoration: none; text-align: center; }
        .btn-blue { background: #007bff; color: white; }
        .btn-green { background: #28a745; color: white; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card-main">
        <h1 class="yellow-text mb-1">د-سيرفس</h1>
        <p class="text-muted small mb-4">انسخ رابط الملف الشخصي أو السنابة العامة</p>

        <form method="POST">
            <div class="input-group mb-3">
                <input type="text" name="video_url" class="form-control input-field" placeholder="ضع الرابط هنا..." required>
                <button class="btn btn-yellow" type="submit">جلب</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mt-3 small"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="mt-3">
                <button id="shareBtn" onclick="saveIos('<?php echo $videoData['play']; ?>')" class="action-btn btn-blue">
                    📤 حفظ في استوديو الصور (أيفون)
                </button>
                <a href="<?php echo $videoData['play']; ?>" download="D-Service.mp4" class="action-btn btn-green">
                    📥 تحميل للملفات
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function saveIos(url) {
    const btn = document.getElementById('shareBtn');
    if (!navigator.share) { alert("استخدم متصفح سفاري."); return; }
    try {
        btn.innerText = "⏳ جاري التجهيز...";
        btn.disabled = true;
        const res = await fetch(url);
        const blob = await res.blob();
        const file = new File([blob], "video.mp4", { type: "video/mp4" });
        await navigator.share({ files: [file] });
        btn.innerText = "📤 حفظ في استوديو الصور (أيفون)";
        btn.disabled = false;
    } catch (e) {
        btn.innerText = "📤 حفظ في استوديو الصور (أيفون)";
        btn.disabled = false;
    }
}
</script>

</body>
</html>
