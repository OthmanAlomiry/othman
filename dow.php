<?php
// --- الجزء الأول: منطق التحميل عبر السيرفر (كاحتياط) ---
if (isset($_GET['dl']) && !empty($_GET['url'])) {
    $fileUrl = $_GET['url'];
    $fileName = isset($_GET['title']) ? $_GET['title'] : 'video';
    $cleanFileName = preg_replace('/[^A-Za-z0-9]/', '_', $fileName) . ".mp4";

    header('Content-Description: File Transfer');
    header('Content-Type: video/mp4');
    header('Content-Disposition: attachment; filename="' . $cleanFileName . '"');
    readfile($fileUrl);
    exit;
}

// --- الجزء الثاني: جلب البيانات ---
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
    <title>محمل فيديوهات احترافي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #121212; color: white; font-family: sans-serif; text-align: center; }
        .card { background: #1e1e1e; border: none; border-radius: 20px; margin-top: 20px; padding: 20px; }
        .btn-save { background: #fe2c55; border: none; color: white; font-weight: bold; padding: 15px; border-radius: 12px; width: 100%; }
        .loading-text { display: none; color: #fe2c55; font-weight: bold; margin-top: 10px; }
        video { width: 100%; max-width: 300px; border-radius: 15px; border: 1px solid #333; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="card col-md-6 mx-auto">
        <h3 class="mb-4">تحميل فيديو تيك توك</h3>
        
        <form method="POST">
            <div class="input-group mb-4">
                <input type="text" name="tiktok_url" class="form-control" placeholder="ضع الرابط هنا..." required>
                <button class="btn btn-danger" type="submit">جلب</button>
            </div>
        </form>

        <?php if ($videoData): ?>
            <video id="myVideo" controls playsinline webkit-playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>

            <button id="downloadBtn" class="btn-save" onclick="downloadVideo('<?php echo $videoData['play']; ?>', 'tiktok_video')">
                📥 حفظ الفيديو في الاستوديو (مباشر)
            </button>
            
            <div id="status" class="loading-text">جاري معالجة الفيديو للتحميل... انتظر ثواني</div>
        <?php endif; ?>
    </div>
</div>

<script>
async function downloadVideo(url, filename) {
    const btn = document.getElementById('downloadBtn');
    const status = document.getElementById('status');
    
    btn.disabled = true;
    btn.innerText = "⏳ جاري التحميل...";
    status.style.display = "block";

    try {
        // خطوة 1: جلب الفيديو كـ Blob (بيانات خام)
        const response = await fetch(url);
        const blob = await response.blob();
        
        // خطوة 2: إنشاء رابط وهمي للملف
        const blobUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = blobUrl;
        a.download = filename + '.mp4';
        
        // خطوة 3: تفعيل التحميل
        document.body.appendChild(a);
        a.click();
        
        // تنظيف
        window.URL.revokeObjectURL(blobUrl);
        btn.disabled = false;
        btn.innerText = "📥 حفظ الفيديو في الاستوديو (مباشر)";
        status.innerText = "✅ اكتمل التحميل! تحقق من تطبيق الصور أو الملفات.";
        
    } catch (error) {
        alert("حدث خطأ أثناء التحميل، جرب زر التحميل العادي.");
        btn.disabled = false;
        btn.innerText = "إعادة المحاولة";
    }
}
</script>

</body>
</html>
