<?php
// --- الجزء الأول: منطق الإجبار على التحميل (Proxy Download) ---
if (isset($_GET['dl']) && !empty($_GET['url'])) {
    $fileUrl = $_GET['url'];
    $fileName = isset($_GET['title']) ? $_GET['title'] : 'video';
    
    // تنظيف اسم الملف ليكون متوافقاً مع أنظمة التشغيل
    $cleanFileName = preg_replace('/[^A-Za-z0-9]/', '_', $fileName) . ".mp4";

    // إرسال هيدرز التحميل المباشر
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $cleanFileName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // قراءة الملف من المصدر وإرساله للمتصفح
    readfile($fileUrl);
    exit;
}

// --- الجزء الثاني: منطق جلب بيانات الفيديو من API ---
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tiktok_url'])) {
    $url = $_POST['tiktok_url'];
    $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // لتجنب مشاكل SSL في بعض الاستضافات
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result && isset($result['data'])) {
        $videoData = $result['data'];
    } else {
        $error = "عذراً، لم نتمكن من العثور على الفيديو. تأكد أن الرابط صحيح وعام.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محمل فيديوهات تيك توك الذكي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #121212; color: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { background: #1e1e1e; border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .btn-primary { background: #fe2c55; border: none; } /* لون تيك توك الشهير */
        .btn-primary:hover { background: #ef2950; }
        .video-preview { width: 100%; border-radius: 15px; max-height: 300px; object-fit: cover; }
        input.form-control { background: #2c2c2c; border: 1px solid #444; color: white; }
        input.form-control:focus { background: #333; color: white; border-color: #fe2c55; box-shadow: none; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 card p-4">
            <h2 class="text-center mb-4">تحميل من تيك توك <small class="text-muted d-block" style="font-size: 0.5em;">بدون علامة مائية</small></h2>
            
            <form method="POST" action="dow.php">
                <div class="input-group mb-3">
                    <input type="text" name="tiktok_url" class="form-control form-control-lg" placeholder="ضع رابط الفيديو هنا..." required>
                    <button class="btn btn-primary px-4" type="submit">جلب</button>
                </div>
            </form>

            <?php if ($error): ?>
                <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($videoData): ?>
                <div class="text-center mt-4 border-top pt-4">
                    <img src="<?php echo $videoData['cover']; ?>" class="video-preview mb-3" alt="Cover">
                    <h5 class="mb-4"><?php echo htmlspecialchars($videoData['title']); ?></h5>
                    
                    <div class="d-grid gap-3">
                        <a href="dow.php?dl=true&url=<?php echo urlencode($videoData['play']); ?>&title=<?php echo urlencode($videoData['title']); ?>" 
                           class="btn btn-success btn-lg">
                           💾 حفظ الفيديو في الجهاز
                        </a>
                        
                        <a href="dow.php?dl=true&url=<?php echo urlencode($videoData['music']); ?>&title=music_track" 
                           class="btn btn-outline-light">
                           🎵 تحميل الصوت فقط (MP3)
                        </a>
                    </div>
                    <p class="text-muted mt-3 small">سيتم البدء بالتحميل فوراً كملف مرفق.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
