<?php
// جلب البيانات من API
$videoData = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tiktok_url'])) {
    $url = $_POST['tiktok_url'];
    $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = json_decode(curl_exec($ch), true);
    $videoData = $result['data'] ?? null;
    curl_close($ch);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محمل فيديوهات د-سيرفس</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #121212; color: white; font-family: sans-serif; text-align: center; padding-top: 20px; }
        .card { background: #1e1e1e; border: none; border-radius: 20px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .btn-main { background: #fe2c55; border: none; color: white; font-weight: bold; padding: 15px; border-radius: 12px; width: 100%; margin-bottom: 10px; }
        .btn-share { background: #007bff; border: none; color: white; font-weight: bold; padding: 15px; border-radius: 12px; width: 100%; }
        video { width: 100%; max-width: 300px; border-radius: 15px; border: 1px solid #333; margin-bottom: 20px; }
        .info-box { background: #2c2c2c; padding: 10px; border-radius: 10px; font-size: 0.8em; color: #bbb; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card col-md-6 mx-auto">
        <h4 class="mb-4">تحميل فيديو تيك توك</h4>
        
        <form method="POST">
            <div class="input-group mb-4">
                <input type="text" name="tiktok_url" class="form-control bg-dark text-white" placeholder="ضع الرابط هنا..." required>
                <button class="btn btn-danger" type="submit">جلب</button>
            </div>
        </form>

        <?php if ($videoData): ?>
            <video id="vPlayer" controls playsinline webkit-playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <a href="?dl=true&url=<?php echo urlencode($videoData['play']); ?>" class="btn-main d-block text-decoration-none">
                📥 تحميل إلى "الملفات"
            </a>

            <button onclick="shareVideo('<?php echo $videoData['play']; ?>', '<?php echo htmlspecialchars($videoData['title']); ?>')" class="btn-share">
                📤 مشاركة وحفظ في الاستوديو
            </button>
            
            <div class="info-box">
                بعد الضغط على <b>"مشاركة"</b>، اختر <b>"حفظ الفيديو" (Save Video)</b> من قائمة الأيفون ليظهر في الاستوديو فوراً.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function shareVideo(url, title) {
    // التأكد أن المتصفح يدعم خاصية المشاركة
    if (navigator.share) {
        try {
            // جلب الفيديو كملف حقيقي ليتم إرساله للقائمة
            const response = await fetch(url);
            const blob = await response.blob();
            const file = new File([blob], "video.mp4", { type: "video/mp4" });

            await navigator.share({
                files: [file],
                title: title,
                text: 'تحميل فيديو من د-سيرفس'
            });
        } catch (error) {
            console.log('Error sharing:', error);
            alert("يرجى الضغط على زر التحميل العادي أو فتح الرابط في سفاري.");
        }
    } else {
        alert("متصفحك لا يدعم خاصية المشاركة المباشرة.");
    }
}
</script>

</body>
</html>
