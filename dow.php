<?php
/**
 * سكربت د-سيرفس V10 - مخصص للقصص اليومية (Stories)
 */

error_reporting(0);
$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    if (strpos($url, 'snapchat.com') !== false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // محاكاة متصفح أيفون حديث جداً لجلب القصص
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1");
        $html = curl_exec($ch);
        curl_close($ch);

        // البحث عن روابط الميديا التي تظهر في سياق "القصص" (Stories)
        // روابط القصص غالباً ما تكون بصيغة مختلفة في كود الصفحة
        preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
        
        if (!empty($matches[1])) {
            // في القصص اليومية، الرابط الأول في المصفوفة غالباً هو القصة الحالية
            // بينما الروابط الأخيرة تكون لمنصة الأضواء
            $targetVideo = $matches[1][0]; 
            
            $videoUrl = str_replace('\\u002F', '/', $targetVideo);
            preg_match('/property="og:image" content="(.*?)"/', $html, $img);

            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $img[1] ?? ''
            ];
        }
    } elseif (strpos($url, 'tiktok.com') !== false) {
        // محرك تيك توك المستقر
        $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);
        $res = file_get_contents($apiUrl);
        $result = json_decode($res, true);
        if ($result && isset($result['data'])) {
            $videoData = ['play' => $result['data']['play'], 'cover' => $result['data']['cover']];
        }
    }

    if (!$videoData) {
        $error = "لم نجد سنابات حالية نشطة. تأكد من وجود 'قصة عامة' (Public Story) منشورة الآن.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | جلب السنابات الحالية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; padding: 20px; }
        .card-custom { max-width: 450px; margin: auto; background: #111; border-radius: 25px; padding: 25px; border: 1px solid #333; }
        .btn-snap { background: #FFFC00; color: #000; font-weight: bold; border-radius: 12px; border: none; padding: 10px 20px; }
        .input-style { background: #222; border: 1px solid #444; color: #fff; border-radius: 12px !important; text-align: center; }
        video { width: 100%; border-radius: 15px; margin-top: 20px; border: 1px solid #FFFC00; }
        .btn-download { display: block; width: 100%; padding: 15px; margin-top: 10px; border-radius: 15px; font-weight: bold; text-decoration: none; text-align: center; background: #007bff; color: #fff; }
    </style>
</head>
<body>

<div class="card-custom text-center">
    <h2 style="color: #FFFC00;">د-سيرفس</h2>
    <p class="text-muted small">جلب القصص الحالية (Stories)</p>
    
    <form method="POST">
        <div class="input-group mb-3">
            <input type="text" name="video_url" class="form-control input-style" placeholder="رابط الملف الشخصي" required>
            <button class="btn-snap" type="submit">جلب</button>
        </div>
    </form>

    <?php if ($videoData): ?>
        <video id="myVid" controls playsinline poster="<?php echo $videoData['cover']; ?>">
            <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
        </video>
        <button onclick="saveVideo('<?php echo $videoData['play']; ?>')" class="btn-download">📤 حفظ في الاستوديو</button>
    <?php elseif ($error): ?>
        <div class="alert alert-danger bg-transparent text-danger border-0 small"><?php echo $error; ?></div>
    <?php endif; ?>
</div>

<script>
async function saveVideo(url) {
    if (!navigator.share) { alert("استخدم سفاري"); return; }
    const res = await fetch(url);
    const blob = await res.blob();
    const file = new File([blob], "story.mp4", { type: "video/mp4" });
    await navigator.share({ files: [file] });
}
</script>

</body>
</html>
