<?php
/**
 * سكربت د-سيرفس V5 - جلب القصص من الملف الشخصي العام
 */

$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    // معالجة تيك توك (TikTok)
    if (strpos($url, 'tiktok.com') !== false) {
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
    } 
    // معالجة سناب شات (Snapchat Profile & Stories)
    elseif (strpos($url, 'snapchat.com') !== false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // التظاهر بأن الطلب من متصفح ديسكتوب حقيقي لتجنب الحجب
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36");
        $html = curl_exec($ch);
        curl_close($ch);

        // محاولة البحث عن روابط الميديا في بيانات JSON المدمجة
        // سنبحث عن روابط media.snapchat.com التي تنتهي بـ .mp4
        preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
        
        if (!empty($matches[1])) {
            // جلب أول فيديو متاح في الملف الشخصي (عادة يكون أحدث ستوري)
            $videoUrl = str_replace('\\u002F', '/', $matches[1][0]);
            
            // جلب صورة الغلاف
            preg_match('/property="og:image" content="(.*?)"/', $html, $imgMatches);
            $imgUrl = $imgMatches[1] ?? '';

            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $imgUrl,
                'title' => 'Snapchat Story'
            ];
        }
    }

    if (!$videoData) {
        $error = "لم نتمكن من العثور على سنابات عامة حالياً. يرجى التأكد من أن الحساب يحتوي على 'قصة عامة' (Public Story).";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | جلب السنابات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .main-card { max-width: 450px; margin: auto; background: #111; border-radius: 30px; padding: 25px; border: 1px solid #333; box-shadow: 0 20px 50px rgba(0,0,0,0.8); }
        .logo { font-weight: 900; color: #FFFC00; font-size: 2rem; }
        .input-box { background: #222; border: 1px solid #444; color: #fff; border-radius: 15px !important; text-align: center; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 10px 25px; }
        video { width: 100%; border-radius: 20px; margin-top: 20px; background: #000; }
        .btn-action { display: block; width: 100%; padding: 15px; margin-top: 10px; border-radius: 15px; font-weight: bold; text-decoration: none; text-align: center; border: none; }
        .btn-gallery { background: #007bff; color: white; }
        .btn-files { background: #28a745; color: white; }
        .alert { background: rgba(255, 0, 0, 0.1); border: 1px solid #f00; color: #f88; border-radius: 15px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="main-card text-center">
        <h1 class="logo mb-1">د-سيرفس</h1>
        <p class="text-muted small mb-4">انسخ رابط الملف الشخصي وضعه هنا</p>

        <form method="POST">
            <div class="input-group mb-3">
                <input type="text" name="video_url" class="form-control input-box" placeholder="https://www.snapchat.com/add/اسم_المستخدم" required>
                <button class="btn btn-fetch" type="submit">جلب</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert mt-4 small p-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="mt-3">
                <button id="shareBtn" onclick="shareToIos('<?php echo $videoData['play']; ?>')" class="btn-action btn-gallery">
                    📤 حفظ في استوديو الصور (أيفون)
                </button>
                <a href="<?php echo $videoData['play']; ?>" download="snap_story.mp4" class="btn-action btn-files">
                    📥 تحميل للملفات
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function shareToIos(url) {
    const btn = document.getElementById('shareBtn');
    if (!navigator.share) {
        alert("يرجى استخدام سفاري.");
        return;
    }
    try {
        const originalText = btn.innerText;
        btn.innerText = "⏳ جاري التحميل...";
        btn.disabled = true;

        const response = await fetch(url);
        const blob = await response.blob();
        const file = new File([blob], "video.mp4", { type: "video/mp4" });

        await navigator.share({ files: [file] });
        
        btn.innerText = originalText;
        btn.disabled = false;
    } catch (e) {
        btn.innerText = "📤 حفظ في استوديو الصور (أيفون)";
        btn.disabled = false;
    }
}
</script>

</body>
</html>
