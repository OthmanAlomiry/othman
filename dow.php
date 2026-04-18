<?php
/**
 * د-سيرفس V13 - الحل النهائي لجلب سنابات اليوزر
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['snap_username'])) {
    $username = trim($_POST['snap_username']);
    $username = ltrim($username, '@');

    // استخدام محرك جلب خارجي لتجاوز حظر السيرفرات (Proxy Fetch)
    $targetUrl = "https://www.snapchat.com/add/" . $username;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $targetUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    // محاكاة متصفح أندرويد حقيقي هذه المرة لكسر النمط
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36");
    
    $html = curl_exec($ch);
    curl_close($ch);

    // البحث عن الفيديوهات (القصص العامة)
    // سناب شات تخزن الفيديو في وسوم مخصصة أو داخل JSON في الصفحة
    if (preg_match('/"contentUrl":"([^"]+)"/', $html, $videoMatch)) {
        $videoUrl = str_replace('\\u002f', '/', $videoMatch[1]);
        
        preg_match('/"thumbnailUrl":"([^"]+)"/', $html, $thumbMatch);
        $thumbUrl = str_replace('\\u002f', '/', $thumbMatch[1]);

        $videoData = [
            'play' => $videoUrl,
            'cover' => $thumbUrl
        ];
    } 
    // محاولة ثانية بنمط مختلف (Direct Media)
    elseif (preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches)) {
        $videoData = [
            'play' => htmlspecialchars_decode(end($matches[1])),
            'cover' => ''
        ];
    }

    if (!$videoData) {
        $error = "تعذر الجلب آلياً. تأكد أن الحساب ($username) لديه 'قصة عامة' منشورة الآن وليست قصة خاصة للأصدقاء فقط.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | الجلب الذكي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0b0b0b; color: #fff; font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .app-container { width: 100%; max-width: 420px; background: #161616; border-radius: 40px; padding: 35px; border: 1px solid #282828; box-shadow: 0 30px 70px rgba(0,0,0,1); text-align: center; }
        .logo { color: #FFFC00; font-weight: 900; font-size: 2.5rem; letter-spacing: -1px; margin-bottom: 5px; }
        .sub-title { color: #666; font-size: 0.85rem; margin-bottom: 25px; }
        .input-group { background: #222; border-radius: 20px; padding: 6px; border: 1px solid #333; transition: 0.3s; }
        .input-group:focus-within { border-color: #FFFC00; box-shadow: 0 0 15px rgba(255, 252, 0, 0.1); }
        .form-control { background: transparent !important; border: none !important; color: #fff !important; text-align: center; font-weight: 600; }
        .btn-fetch { background: #FFFC00; color: #000; font-weight: 800; border-radius: 15px; border: none; padding: 12px 25px; }
        video { width: 100%; border-radius: 25px; margin-top: 25px; border: 2px solid #222; max-height: 450px; background: #000; }
        .btn-save { display: block; width: 100%; padding: 18px; margin-top: 15px; border-radius: 20px; font-weight: 800; text-decoration: none; text-align: center; background: #FFFC00; color: #000; border: none; font-size: 1rem; }
        .error-msg { background: rgba(255, 50, 50, 0.1); color: #ff5050; border-radius: 15px; padding: 15px; margin-top: 20px; font-size: 0.85rem; border: 1px solid rgba(255, 50, 50, 0.2); }
    </style>
</head>
<body>

<div class="app-container">
    <h1 class="logo">د-سيرفس</h1>
    <p class="sub-title">أدخل يوزر سناب شات فقط</p>

    <form method="POST" id="snapForm">
        <div class="input-group">
            <input type="text" name="snap_username" class="form-control" placeholder="sousou3999" required autocomplete="off">
            <button type="submit" class="btn-fetch" id="btnAction">جلب</button>
        </div>
    </form>

    <?php if ($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($videoData): ?>
        <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
            <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
        </video>
        <button onclick="iosDownload('<?php echo $videoData['play']; ?>')" class="btn-save">
            📥 حفظ الفيديو الآن
        </button>
    <?php endif; ?>
</div>

<script>
    document.getElementById('snapForm').onsubmit = function() {
        document.getElementById('btnAction').innerText = "⏳";
    };

    async function iosDownload(url) {
        if (!navigator.share) { window.open(url); return; }
        const res = await fetch(url);
        const blob = await res.blob();
        const file = new File([blob], "snap.mp4", { type: "video/mp4" });
        await navigator.share({ files: [file] });
    }
</script>

</body>
</html>
