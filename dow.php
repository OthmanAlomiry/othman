<?php
/**
 * د-سيرفس V13 - المعالج النهائي لاسم المستخدم
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['snap_username'])) {
    // تنظيف اليوزر من أي إضافات
    $username = trim($_POST['snap_username']);
    $username = ltrim($username, '@');
    
    // بناء الرابط المباشر للبروفايل
    $url = "https://www.snapchat.com/add/" . $username;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    // محاكاة متصفح أيفون حقيقي لتجاوز أنظمة الحماية
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Mobile/15E148 Safari/604.1");
    
    $html = curl_exec($ch);
    curl_close($ch);

    // محاولة استخراج روابط الفيديو (MP4) بذكاء من خلال البحث في أنماط متعددة
    preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
    
    if (!empty($matches[1])) {
        // نختار دائماً آخر فيديو موجود في الصفحة (الأحدث)
        $latest = end($matches[1]);
        $videoUrl = str_replace('\\u002F', '/', $latest);
        
        // استخراج صورة الغلاف (Poster)
        preg_match('/property="og:image" content="(.*?)"/', $html, $img);
        
        $videoData = [
            'play' => htmlspecialchars_decode($videoUrl),
            'cover' => $img[1] ?? ''
        ];
    } else {
        $error = "لم نجد مقاطع فيديو عامة لهذا اليوزر حالياً. تأكد أن الحساب يحتوي على قصص (Public Stories) نشطة.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | التحميل باليوزر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: 'Segoe UI', sans-serif; padding: 10px; }
        .app-card { max-width: 480px; margin: 40px auto; background: #111; border-radius: 35px; padding: 30px; border: 1px solid #222; box-shadow: 0 25px 60px rgba(0,0,0,0.9); }
        .logo { color: #FFFC00; font-weight: 900; font-size: 2.3rem; margin-bottom: 5px; }
        .input-group { background: #1a1a1a; border-radius: 20px; padding: 5px; border: 1px solid #333; }
        .form-control { background: transparent !important; border: none !important; color: #fff !important; text-align: center; font-weight: bold; font-size: 1.1rem; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: bold; border-radius: 15px; border: none; padding: 12px 30px; }
        video { width: 100%; border-radius: 25px; margin-top: 25px; border: 1px solid #FFFC00; max-height: 75vh; }
        .btn-action { display: block; width: 100%; padding: 18px; margin-top: 15px; border-radius: 20px; font-weight: bold; text-decoration: none; text-align: center; font-size: 1.1rem; }
        .btn-ios { background: #007bff; color: white; }
        .loader { display: none; margin-top: 15px; color: #FFFC00; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="app-card text-center">
        <h1 class="logo">د-سيرفس</h1>
        <p class="text-muted small mb-4">أدخل اسم مستخدم سناب شات فقط (User)</p>

        <form method="POST" id="snapForm">
            <div class="input-group">
                <input type="text" name="snap_username" class="form-control" placeholder="sousou3999" required autocomplete="off">
                <button type="submit" class="btn-fetch" id="btnSubmit">جلب</button>
            </div>
        </form>

        <div id="loadingStatus" class="loader">🔍 جاري سحب أحدث السنابات...</div>

        <?php if ($error): ?>
            <div class="alert mt-4 bg-danger bg-opacity-10 text-danger border-0 small p-3" style="border-radius: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($videoData): ?>
            <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
                <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
            </video>
            
            <button onclick="saveIos('<?php echo $videoData['play']; ?>')" class="btn-action btn-ios">
                📥 حفظ في الاستوديو (أيفون)
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('snapForm').onsubmit = function() {
        document.getElementById('btnSubmit').style.display = 'none';
        document.getElementById('loadingStatus').style.display = 'block';
    };

    async function saveIos(url) {
        if (!navigator.share) { alert("استخدم متصفح سفاري للحفظ المباشر"); return; }
        try {
            const res = await fetch(url);
            const blob = await res.blob();
            const file = new File([blob], "snap_dservice.mp4", { type: "video/mp4" });
            await navigator.share({ files: [file] });
        } catch (e) { console.error(e); }
    }
</script>

</body>
</html>
