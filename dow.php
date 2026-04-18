<?php
/**
 * د-سيرفس V16 - استخدام مفتاح RapidAPI الفعلي
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['snap_username'])) {
    $username = trim($_POST['snap_username']);
    $username = ltrim($username, '@');

    // بناء رابط البروفايل الذي يحتاجه الـ API كـ Parameter
    $profileUrl = "https://www.snapchat.com/add/" . $username;

    $curl = curl_init();

    curl_setopt_array($curl, [
        // استخدام الرابط الظاهر في صورتك للـ API
        CURLOPT_URL => "https://snapchat-video-downloader-api.p.rapidapi.com/downloadVideo?link=" . urlencode($profileUrl),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: snapchat-video-downloader-api.p.rapidapi.com",
            "x-rapidapi-key: 49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9" // مفتاحك الفعلي من الصورة
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = json_decode($response, true);

    // التحقق من استجابة الـ API (تعديل بناءً على بنية JSON المتوقعة من هذا الـ API)
    if (!$err && $result && (isset($result['url']) || isset($result['video_url']))) {
        $videoData = [
            'play' => $result['url'] ?? $result['video_url'],
            'cover' => $result['thumbnail'] ?? '',
            'title' => $result['title'] ?? 'Snapchat Video'
        ];
    } else {
        $error = "عذراً، الـ API لم يجد سنابات عامة نشطة لهذا اليوزر ($username). تأكد من وجود قصة عامة حالياً.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | جلب احترافي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; padding: 15px; }
        .main-card { max-width: 480px; margin: 30px auto; background: #111; border-radius: 35px; padding: 30px; border: 1px solid #222; box-shadow: 0 25px 50px rgba(0,0,0,0.8); }
        .logo { color: #FFFC00; font-weight: 900; font-size: 2.2rem; margin-bottom: 10px; }
        .input-group { background: #1a1a1a; border-radius: 20px; padding: 5px; border: 1px solid #333; }
        .form-control { background: transparent !important; border: none !important; color: #fff !important; text-align: center; font-size: 1.1rem; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 10px 30px; }
        video { width: 100%; border-radius: 25px; margin-top: 20px; border: 1px solid #333; }
        .btn-save { display: block; width: 100%; padding: 16px; margin-top: 15px; border-radius: 20px; font-weight: bold; text-decoration: none; text-align: center; background: #007bff; color: #fff; border: none; font-size: 1rem; }
        .loader { display: none; color: #FFFC00; margin-top: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="main-card text-center">
        <h1 class="logo">د-سيرفس</h1>
        <p class="text-muted small mb-4">الجلب المعتمد على مفتاح RapidAPI</p>

        <form method="POST" id="snapForm">
            <div class="input-group">
                <input type="text" name="snap_username" class="form-control" placeholder="sousou3999" required>
                <button type="submit" class="btn-fetch" id="btnSubmit">جلب</button>
            </div>
        </form>

        <div id="wait" class="loader">⏳ جاري الاتصال بـ RapidAPI وسحب المقطع...</div>

        <?php if ($error): ?>
            <div class="alert mt-4 bg-danger bg-opacity-10 text-danger border-0 small" style="border-radius: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>
            
            <button onclick="saveVideo('<?php echo $videoData['play']; ?>')" class="btn-save">
                📥 حفظ في الاستوديو (أيفون)
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('snapForm').onsubmit = function() {
        document.getElementById('btnSubmit').style.display = 'none';
        document.getElementById('wait').style.display = 'block';
    };

    async function saveVideo(url) {
        if (!navigator.share) { alert("استخدم متصفح سفاري"); return; }
        try {
            const response = await fetch(url);
            const blob = await response.blob();
            const file = new File([blob], "Snap_DService.mp4", { type: "video/mp4" });
            await navigator.share({ files: [file] });
        } catch (e) { console.error(e); }
    }
</script>

</body>
</html>
