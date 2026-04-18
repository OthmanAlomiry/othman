<?php
// --- الجزء الأول: منطق التحميل ---
if (isset($_GET['dl']) && !empty($_GET['url'])) {
    $fileUrl = $_GET['url'];
    $fileName = isset($_GET['title']) ? $_GET['title'] : 'video';
    $cleanFileName = preg_replace('/[^A-Za-z0-9]/', '_', $fileName) . ".mp4";

    header('Content-Description: File Transfer');
    header('Content-Type: video/mp4'); // تم التعديل ليكون متوافقاً مع iOS
    header('Content-Disposition: attachment; filename="' . $cleanFileName . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    readfile($fileUrl);
    exit;
}

// --- الجزء الثاني: جلب البيانات ---
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
        $error = "عذراً، لم نتمكن من العثور على الفيديو.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محمل تيك توك للأيفون</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #121212; color: white; font-family: sans-serif; }
        .card { background: #1e1e1e; border: none; border-radius: 20px; }
        .btn-tiktok { background: #fe2c55; border: none; color: white; }
        .iphone-tip { background: #2c2c2c; padding: 15px; border-radius: 10px; font-size: 0.9em; border-right: 4px solid #007bff; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 card p-4">
            <h2 class="text-center mb-4">تحميل للأيفون والطلب</h2>
            
            <form method="POST" action="dow.php">
                <div class="input-group mb-3">
                    <input type="text" name="tiktok_url" class="form-control" placeholder="ضع رابط الفيديو هنا..." required>
                    <button class="btn btn-tiktok" type="submit">جلب</button>
                </div>
            </form>

            <?php if ($videoData): ?>
                <div class="text-center mt-4 border-top pt-4">
                    <img src="<?php echo $videoData['cover']; ?>" style="width:150px; border-radius:10px;" class="mb-3">
                    
                    <div class="d-grid gap-3">
                        <a href="dow.php?dl=true&url=<?php echo urlencode($videoData['play']); ?>&title=<?php echo urlencode($videoData['title']); ?>" 
                           class="btn btn-success btn-lg">
                           📥 تحميل (يُحفظ في الملفات)
                        </a>

                        <a href="<?php echo $videoData['play']; ?>" target="_blank" class="btn btn-primary btn-lg">
                           📱 فتح الفيديو للحفظ في الاستوديو
                        </a>
                    </div>

                    <div class="iphone-tip mt-4 text-start">
                        <strong>💡 لمستخدمي الأيفون:</strong>
                        <ol class="mt-2">
                            <li>اضغط على زر <b>"فتح الفيديو"</b> بالأعلى.</li>
                            <li>سيفتح الفيديو في صفحة جديدة، اضغط على أيقونة <b>المشاركة (السهم المربع)</b> في أسفل المتصفح.</li>
                            <li>اختر <b>"حفظ الفيديو" (Save Video)</b> وسيتم نقله فوراً للاستوديو.</li>
                        </ol>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
