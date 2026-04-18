<?php
// --- الجزء الأول: منطق التحميل المباشر (للملفات) ---
if (isset($_GET['dl']) && !empty($_GET['url'])) {
    $fileUrl = $_GET['url'];
    $fileName = isset($_GET['title']) ? $_GET['title'] : 'video';
    $cleanFileName = preg_replace('/[^A-Za-z0-9]/', '_', $fileName) . ".mp4";

    header('Content-Description: File Transfer');
    header('Content-Type: video/mp4');
    header('Content-Disposition: attachment; filename="' . $cleanFileName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    readfile($fileUrl);
    exit;
}

// --- الجزء الثاني: جلب البيانات من API ---
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tiktok_url'])) {
    $url = $_POST['tiktok_url'];
    $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if ($result && isset($result['data'])) {
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
    <title>محمل تيك توك الاحترافي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #121212; color: white; font-family: sans-serif; }
        .card { background: #1e1e1e; border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .btn-tiktok { background: #fe2c55; border: none; color: white; padding: 12px; }
        .video-container { 
            position: relative; 
            width: 100%; 
            max-width: 300px; 
            margin: 0 auto; 
            border-radius: 15px; 
            overflow: hidden;
            border: 2px solid #333;
        }
        video { width: 100%; display: block; background: #000; }
        .instruction-box { 
            background: #2c2c2c; 
            padding: 15px; 
            border-radius: 12px; 
            font-size: 0.85em; 
            border-right: 4px solid #fe2c55; 
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 card p-4">
            <h3 class="text-center mb-4">تحميل فيديو تيك توك</h3>
            
            <form method="POST" action="dow.php">
                <div class="input-group mb-3">
                    <input type="text" name="tiktok_url" class="form-control" placeholder="ضع رابط الفيديو هنا..." required>
                    <button class="btn btn-tiktok" type="submit">جلب</button>
                </div>
            </form>

            <?php if ($error): ?>
                <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($videoData): ?>
                <div class="text-center mt-4 pt-3 border-top border-secondary">
                    
                    <div class="video-container mb-3">
                        <video controls playsinline webkit-playsinline poster="<?php echo $videoData['cover']; ?>">
                            <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
                            متصفحك لا يدعم تشغيل الفيديو.
                        </video>
                    </div>

                    <h6 class="mb-3 text-truncate"><?php echo htmlspecialchars($videoData['title']); ?></h6>

                    <div class="d-grid gap-2">
                        <a href="dow.php?dl=true&url=<?php echo urlencode($videoData['play']); ?>&title=<?php echo urlencode($videoData['title']); ?>" 
                           class="btn btn-success btn-lg">
                           📥 تحميل إلى "الملفات"
                        </a>
                    </div>

                    <div class="instruction-box text-start">
                        <strong>📲 للحفظ في "استديو الصور" مباشرة:</strong>
                        <ul class="mt-2 mb-0">
                            <li>شغل الفيديو أعلاه (سيعمل داخل الصفحة).</li>
                            <li>اضغط على أيقونة <b>المشاركة</b> في متصفح سفاري (المربع مع السهم).</li>
                            <li>اختر <b>"حفظ في الاستديو"</b> أو <b>"Save Video"</b>.</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
