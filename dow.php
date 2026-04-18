<?php
/**
 * د-سيرفس V14 - المحرك الخارجي القوي
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['snap_username'])) {
    $username = trim($_POST['snap_username']);
    $username = ltrim($username, '@');

    // استخدام محرك جلب خارجي متطور لتجاوز حماية سناب شات
    $apiUrl = "https://story-downloader-api.vercel.app/api/snapchat?username=" . urlencode($username);
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $response = curl_exec($ch);
    $result = json_decode($response, true);
    curl_close($ch);

    if ($result && isset($result['stories']) && !empty($result['stories'])) {
        // جلب آخر سنابة تم نشرها
        $lastStory = end($result['stories']);
        $videoData = [
            'play' => $lastStory['url'],
            'cover' => $result['user']['avatar'] ?? '',
            'type' => $lastStory['type'] // للتأكد أنه فيديو
        ];
    } else {
        $error = "لم نجد سنابات عامة نشطة لهذا اليوزر ($username). تأكد أن الحساب يحتوي على 'قصة عامة' منشورة الآن.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | جلب الستوري</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; }
        .app-container { max-width: 480px; margin: 40px auto; background: #111; border-radius: 35px; padding: 30px; border: 1px solid #222; }
        .logo { color: #FFFC00; font-weight: 900; font-size: 2.3rem; }
        .input-group { background: #1a1a1a; border-radius: 20px; padding: 5px; border: 1px solid #333; }
        .form-control { background: transparent !important; border: none !important; color: #fff !important; text-align: center; font-size: 1.1rem; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 12px 30px; }
        video { width: 100%; border-radius: 25px; margin-top: 20px; border: 1px solid #FFFC00; }
        .btn-save { display: block; width: 100%; padding: 18px; margin-top: 15px; border-radius: 20px; font-weight: bold; text-decoration: none; text-align: center; background: #007bff; color: #fff; border: none; }
        .loader { display: none; color: #FFFC00; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="app-container text-center">
        <h1 class="logo">د-سيرفس</h1>
        <p class="text-muted small mb-4">جلب سريع عبر اسم المستخدم (User)</p>

        <form method="POST" id="snapForm">
            <div class="input-group">
                <input type="text" name="snap_username" class="form-control" placeholder="sousou3999" required>
                <button type="submit" class="btn-fetch" id="btnSubmit">جلب</button>
            </div>
        </form>

        <div id="loading" class="loader">⏳ جاري الاتصال بالسيرفر وجلب السنابات...</div>

        <?php if ($error): ?>
            <div class="alert mt-4 bg-danger bg-opacity-10 text-danger border-0 small" style="border-radius: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <?php if ($videoData['type'] == 'video'): ?>
                <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
                    <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <img src="<?php echo $videoData['play']; ?>" class="img-fluid rounded-4 mt-3 border border-warning">
            <?php endif; ?>
            
            <button onclick="saveToIos('<?php echo $videoData['play']; ?>')" class="btn-save">
                📥 حفظ في الاستوديو (أيفون)
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('snapForm').onsubmit = function() {
        document.getElementById('btnSubmit').style.display = 'none';
        document.getElementById('loading').style.display = 'block';
    };

    async function saveToIos(url) {
        if (!navigator.share) { alert("استخدم متصفح سفاري"); return; }
        try {
            const res = await fetch(url);
            const blob = await res.blob();
            const file = new File([blob], "snap_video.mp4", { type: blob.type });
            await navigator.share({ files: [file] });
        } catch (e) { console.error(e); }
    }
</script>

</body>
</html>
