<?php
/**
 * سكربت د-سيرفس V4 - جلب السنابات من الملف الشخصي والروابط العامة
 */

$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    if (strpos($url, 'tiktok.com') !== false) {
        // --- تيك توك كما هو (يعمل بامتياز) ---
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
        // --- معالجة سناب شات (قصص الملف الشخصي + الروابط العامة) ---
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // استخدام User-Agent مكثف لجعل سناب تظهر المحتوى بالكامل
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
        $html = curl_exec($ch);
        curl_close($ch);

        // محاولة جلب رابط الفيديو من ملف الـ JSON المدمج في الصفحة (Snapchat Stores data)
        // نبحث عن أول فيديو بصيغة mp4 متاح في الستوري العام
        if (preg_match('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches)) {
            $videoUrl = $matches[1];
        } elseif (preg_match('/property="og:video" content="(.*?)"/', $html, $matches)) {
            $videoUrl = $matches[1];
        } else {
            $videoUrl = null;
        }

        if ($videoUrl) {
            $videoUrl = str_replace('\\u002F', '/', $videoUrl);
            $videoUrl = htmlspecialchars_decode($videoUrl);

            preg_match('/property="og:image" content="(.*?)"/', $html, $imgMatches);
            $imgUrl = $imgMatches[1] ?? '';

            $videoData = [
                'play' => $videoUrl,
                'cover' => $imgUrl,
                'title' => 'Snapchat Story'
            ];
        }
    }

    if (!$videoData) {
        $error = "عذراً، لم نجد سنابات عامة حالياً في هذا الرابط. تأكد أن " . (strpos($url, 'add') ? "الحساب لديه ستوري عام" : "الرابط صحيح");
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
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .main-card { max-width: 450px; margin: auto; background: #111; border-radius: 35px; padding: 30px; border: 1px solid #333; box-shadow: 0 30px 60px rgba(0,0,0,0.9); }
        .logo { font-weight: 900; color: #FFFC00; text-shadow: 0 0 10px rgba(255, 252, 0, 0.3); } /* لون سناب شات */
        .input-box { background: #222; border: 1px solid #444; color: #fff; border-radius: 20px !important; text-align: center; height: 55px; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 20px; border: none; padding: 0 25px; transition: 0.3s; }
        .btn-fetch:hover { background: #e6e300; }
        video { width: 100%; border-radius: 25px; margin-top: 25px; background: #1a1a1a; box-shadow: 0 15px 30px rgba(0,0,0,0.5); }
        .btn-action { display: block; width: 100%; padding: 18px; margin-top: 15px; border-radius: 20px; font-weight: bold; text-decoration: none; text-align: center; border: none; }
        .btn-gallery { background: #007bff; color: white; }
        .btn-files { background: #28a745; color: white; }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="main-card text-center">
        <h2 class="logo mb-2">د-سيرفس</h2>
        <p class="text-muted small mb-4">ضع رابط الملف الشخصي أو رابط السنابة</p>

        <form method="POST">
            <div class="input-group">
                <input type="text" name="video_url" class="form-control input-box" placeholder="https://www.snapchat.com/add/اسم-المستخدم" required>
                <button class="btn btn-fetch" type="submit">جلب</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-4 small border-0 bg-danger bg-opacity-10 text-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline webkit-playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <div class="mt-4">
                <button id="shareBtn" onclick="downloadAndShare('<?php echo $videoData['play']; ?>')" class="btn-action btn-gallery">
                    📤 حفظ في استوديو الصور (أيفون)
                </button>
                <a href="<?php echo $videoData['play']; ?>" download="SnapVideo.mp4" class="btn-action btn-files">
                    📥 تحميل مباشر للملفات
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function downloadAndShare(url) {
    const btn = document.getElementById('shareBtn');
    if (!navigator.share) {
        alert("يرجى استخدام متصفح سفاري.");
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
            title: 'Snapchat Video'
        });
        
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
