<?php
/**
 * د-سيرفس V17 - المحرك المخصص للقصص العامة
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['snap_username'])) {
    $username = trim($_POST['snap_username']);
    $username = ltrim($username, '@');

    $curl = curl_init();

    curl_setopt_array($curl, [
        // استخدام Endpoint مخصص للـ Stories وليس فقط الـ Video
        CURLOPT_URL => "https://snapchat-downloader-api.p.rapidapi.com/get-stories?username=" . urlencode($username),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: snapchat-downloader-api.p.rapidapi.com",
            "x-rapidapi-key: 49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9" // مفتاحك الفعلي
        ],
    ]);

    $response = curl_exec($curl);
    $result = json_decode($response, true);
    curl_close($curl);

    // معالجة البيانات بناءً على هيكلة الـ API الجديد للقصص
    if ($result && isset($result['stories']) && !empty($result['stories'])) {
        // نأخذ آخر ستوري منشورة
        $lastStory = end($result['stories']);
        $videoData = [
            'play' => $lastStory['url'],
            'cover' => $result['user_info']['avatar'] ?? '',
            'type' => $lastStory['type'] 
        ];
    } else {
        $error = "لم نجد سنابات (Stories) عامة نشطة حالياً لليوزر ($username). تأكد أن الحساب يحتوي على 'قصة عامة' منشورة الآن.";
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
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; padding: 15px; }
        .app-card { max-width: 480px; margin: 30px auto; background: #111; border-radius: 40px; padding: 35px; border: 1px solid #222; }
        .logo { color: #FFFC00; font-weight: 900; font-size: 2.5rem; margin-bottom: 5px; }
        .input-group { background: #1a1a1a; border-radius: 20px; padding: 5px; border: 1px solid #444; }
        .form-control { background: transparent !important; border: none !important; color: #fff !important; text-align: center; font-size: 1.2rem; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 10px 30px; }
        video { width: 100%; border-radius: 30px; margin-top: 25px; border: 2px solid #FFFC00; }
        .btn-save { display: block; width: 100%; padding: 18px; margin-top: 15px; border-radius: 20px; font-weight: bold; text-decoration: none; text-align: center; background: #007bff; color: #fff; border: none; }
        .loader { display: none; color: #FFFC00; margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container text-center">
    <div class="app-card">
        <h1 class="logo">د-سيرفس</h1>
        <p class="text-muted small mb-4">جلب القصص العامة (User Stories) باليوزر</p>

        <form method="POST" id="snapForm">
            <div class="input-group">
                <input type="text" name="snap_username" class="form-control" placeholder="sousou3999" required>
                <button type="submit" class="btn-fetch" id="btnSubmit">جلب</button>
            </div>
        </form>

        <div id="loading" class="loader">⏳ جاري فحص الستوري العام...</div>

        <?php if ($error): ?>
            <div class="alert mt-4 bg-danger bg-opacity-10 text-danger border-0 p-3" style="border-radius: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <?php if ($videoData['type'] == 'video' || strpos($videoData['play'], '.mp4') !== false): ?>
                <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
                    <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <img src="<?php echo $videoData['play']; ?>" class="img-fluid rounded-5 mt-3 border border-warning">
            <?php endif; ?>
            
            <button onclick="downloadFile('<?php echo $videoData['play']; ?>')" class="btn-save">
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

    async function downloadFile(url) {
        if (!navigator.share) { alert("استخدم متصفح سفاري"); return; }
        try {
            const response = await fetch(url);
            const blob = await response.blob();
            const file = new File([blob], "Snap_Story.mp4", { type: blob.type });
            await navigator.share({ files: [file] });
        } catch (e) { console.error(e); }
    }
</script>

</body>
</html>
