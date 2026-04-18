<?php
/**
 * سكربت د-سيرفس V8
 * معالج ذكي لجلب أحدث سنابة متوفرة في الرابط
 */

$videoData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['video_url'])) {
    $url = trim($_POST['video_url']);

    if (strpos($url, 'tiktok.com') !== false) {
        // تيك توك يعمل بشكل ممتاز
        $apiUrl = "https://www.tikwm.com/api/?url=" . urlencode($url);
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if ($result && isset($result['data'])) {
            $videoData = ['play' => $result['data']['play'], 'cover' => $result['data']['cover']];
        }
    } elseif (strpos($url, 'snapchat.com') !== false) {
        // معالجة سناب شات - البحث عن الأحدث
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1");
        $html = curl_exec($ch);
        curl_close($ch);

        // جلب جميع روابط الـ MP4 الموجودة في الصفحة
        preg_match_all('/"(https:\/\/media\.snapchat\.com\/.*?\.mp4)"/', $html, $matches);
        
        if (!empty($matches[1])) {
            // التعديل الجوهري: اختيار آخر رابط فيديو في المصفوفة (عادة يكون الأحدث)
            $latestVideo = end($matches[1]); 
            $videoUrl = str_replace('\\u002F', '/', $latestVideo);
            
            preg_match('/property="og:image" content="(.*?)"/', $html, $img);
            $videoData = [
                'play' => htmlspecialchars_decode($videoUrl),
                'cover' => $img[1] ?? ''
            ];
        }
    }

    if (!$videoData) {
        $error = "عذراً، لم نتمكن من العثور على محتوى جديد. تأكد أن الحساب عام.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د-سيرفس | أحدث السنابات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #000; color: #fff; font-family: sans-serif; text-align: center; padding: 15px; }
        .main-container { max-width: 450px; margin: auto; background: #111; border-radius: 30px; padding: 25px; border: 1px solid #333; }
        .logo { color: #FFFC00; font-weight: bold; font-size: 2rem; }
        .input-group { background: #222; border-radius: 15px; padding: 5px; border: 1px solid #444; }
        .form-control { background: transparent; border: none; color: white; text-align: center; }
        .btn-fetch { background: #FFFC00; color: #000; border-radius: 12px; font-weight: bold; border: none; padding: 8px 20px; }
        video { width: 100%; border-radius: 20px; margin-top: 20px; border: 1px solid #FFFC00; }
        .btn-action { display: block; width: 100%; padding: 15px; margin-top: 10px; border-radius: 15px; font-weight: bold; text-decoration: none; text-align: center; }
        .btn-blue { background: #007bff; color: white; }
    </style>
</head>
<body>

<div class="main-container mt-5">
    <h1 class="logo mb-4">د-سيرفس</h1>
    <form method="POST">
        <div class="input-group">
            <input type="text" name="video_url" class="form-control" placeholder="رابط البروفايل أو السنابة" required>
            <button class="btn-fetch" type="submit">جلب</button>
        </div>
    </form>

    <?php if ($videoData): ?>
        <video controls playsinline poster="<?php echo $videoData['cover']; ?>">
            <source src="<?php echo $videoData['play']; ?>" type="video/mp4">
        </video>
        <button onclick="shareVideo('<?php echo $videoData['play']; ?>')" class="btn-action btn-blue mt-3">
            📥 حفظ في الاستوديو (أيفون)
        </button>
    <?php endif; ?>
</div>

<script>
async function shareVideo(url) {
    if (!navigator.share) { alert("استخدم متصفح سفاري"); return; }
    const res = await fetch(url);
    const blob = await res.blob();
    const file = new File([blob], "latest_snap.mp4", { type: "video/mp4" });
    await navigator.share({ files: [file] });
}
</script>

</body>
</html>
