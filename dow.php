<?php
/**
 * د-سيرفس V12 - نظام الجلب عبر اسم المستخدم
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['snap_username'])) {
    // تنظيف المدخلات (حذف @ إذا وجدت)
    $username = trim($_POST['snap_username']);
    $username = ltrim($username, '@');
    
    // بناء رابط البروفايل الرسمي
    $url = "https://www.snapchat.com/add/" . $username;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    // محاكاة متصفح أيفون حديث لتجاوز الحماية
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1");
    
    $html = curl_exec($ch);
    curl_close($ch);

    // البحث عن الفيديوهات في الصفحة (الستوري العام)
    preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
    
    if (!empty($matches[1])) {
        // جلب أحدث فيديو
        $latestVideo = end($matches[1]);
        $videoUrl = str_replace('\\u002F', '/', $latestVideo);
        
        // جلب صورة الغلاف
        preg_match('/property="og:image" content="(.*?)"/', $html, $img);
        
        $videoData = [
            'play' => htmlspecialchars_decode($videoUrl),
            'cover' => $img[1] ?? ''
        ];
    } else {
        $error = "عذراً، لم نجد سنابات عامة منشورة حالياً لهذا المستخدم ($username). تأكد من وجود قصة عامة نشطة.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | جلب باليوزر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; padding: 15px; }
        .app-card { max-width: 450px; margin: 50px auto; background: #111; border-radius: 30px; padding: 30px; border: 1px solid #222; box-shadow: 0 20px 50px rgba(0,0,0,0.8); }
        .logo { color: #FFFC00; font-weight: 900; font-size: 2.2rem; margin-bottom: 10px; }
        .input-group { background: #1a1a1a; border-radius: 20px; padding: 5px; border: 1px solid #333; }
        .form-control { background: transparent !important; border: none !important; color: #fff !important; text-align: center; font-weight: bold; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 10px 30px; }
        video { width: 100%; border-radius: 20px; margin-top: 25px; border: 1px solid #333; }
        .btn-save { display: block; width: 100%; padding: 16px; margin-top: 15px; border-radius: 18px; font-weight: bold; text-decoration: none; text-align: center; background: #007bff; color: #fff; border: none; }
        .loading { display: none; color: #FFFC00; margin-top: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="app-card text-center">
        <h1 class="logo">د-سيرفس</h1>
        <p class="text-muted small mb-4">أدخل اسم مستخدم سناب شات فقط</p>

        <form method="POST" id="snapForm">
            <div class="input-group">
                <input type="text" name="snap_username" class="form-control" placeholder="مثال: sousou3999" required autocomplete="off">
                <button type="submit" class="btn-fetch" id="btnAction">جلب</button>
            </div>
        </form>

        <div id="loader" class="loading">🔍 جاري فحص حساب المستخدم...</div>

        <?php if ($error): ?>
            <div class="alert mt-4 bg-danger bg-opacity-10 text-danger border-0 small" style="border-radius: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>
            <button onclick="downloadVideo('<?php echo $videoData['play']; ?>')" class="btn-save">
                📥 حفظ في الاستوديو (أيفون)
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('snapForm').onsubmit = function() {
        document.getElementById('btnAction').style.display = 'none';
        document.getElementById('loader').style.display = 'block';
    };

    async function downloadVideo(url) {
        if (!navigator.share) { alert("يرجى استخدام متصفح سفاري"); return; }
        try {
            const res = await fetch(url);
            const blob = await res.blob();
            const file = new File([blob], "snap.mp4", { type: "video/mp4" });
            await navigator.share({ files: [file] });
        } catch (e) { console.error(e); }
    }
</script>

</body>
</html>
