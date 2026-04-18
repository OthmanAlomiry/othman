<?php
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tiktok_url'])) {
    $url = $_POST['tiktok_url'];
    
    // سنستخدم هنا API مجاني سريع لجلب بيانات الفيديو
    // يمكنك لاحقاً استخدام RapidAPI لضمان استقرار أكبر
    $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result && $result['code'] === 0) {
        $videoData = $result['data'];
    } else {
        $error = "عذراً، لم نتمكن من جلب الفيديو. تأكد من الرابط.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محمل فيديوهات تيك توك</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .main-card { max-width: 600px; margin: 50px auto; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .video-preview { width: 100%; border-radius: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card main-card p-4">
        <h2 class="text-center mb-4">تحميل فيديو تيك توك (بدون علامة)</h2>
        
        <form method="POST">
            <div class="input-group mb-3">
                <input type="text" name="tiktok_url" class="form-control" placeholder="ضع رابط الفيديو هنا..." required>
                <button class="btn btn-primary" type="submit">جلب الفيديو</button>
            </div>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <div class="text-center mt-4">
                <img src="<?php echo $videoData['cover']; ?>" class="video-preview" alt="Cover">
                <h5><?php echo htmlspecialchars($videoData['title']); ?></h5>
                <hr>
                <div class="d-grid gap-2">
                    <a href="<?php echo $videoData['play']; ?>" class="btn btn-success" target="_blank">تحميل الفيديو (بدون علامة)</a>
                    <a href="<?php echo $videoData['music']; ?>" class="btn btn-outline-secondary" target="_blank">تحميل الموسيقى فقط (MP3)</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
