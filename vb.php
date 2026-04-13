<?php
// 1. تفعيل عرض الأخطاء مؤقتاً لمعرفة سبب الصفحة البيضاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحميل فيديو سناب شات</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-align: center; background-color: #f4f4f9; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input[type="text"] { width: 90%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 95%; padding: 12px; background-color: #FFFC00; border: 1px solid #000; cursor: pointer; border-radius: 5px; font-weight: bold; }
        .result { margin-top: 20px; padding: 15px; border-top: 1px solid #eee; min-height: 50px; }
        pre { text-align: left; background: #333; color: #fff; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <h2>سناب شات Downloader</h2>
    
    <form method="POST">
        <input type="text" name="snap_url" placeholder="الصح رابط الفيديو هنا (Spotlight/Public)" required>
        <button type="submit">استخراج الرابط</button>
    </form>

    <div class="result">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['snap_url'])) {
        
        $snapUrl = trim($_POST['snap_url']);
        $apiUrl = "https://download-snapchat-video-spotlight-online.p.rapidapi.com/download?url=" . urlencode($snapUrl);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: download-snapchat-video-spotlight-online.p.rapidapi.com",
                "x-rapidapi-key: 49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "<p style='color:red;'>خطأ cURL: " . $err . "</p>";
        } else {
            $data = json_decode($response, true);
            
            // محاولة إيجاد الرابط في عدة مفاتيح محتملة
            $videoUrl = $data['url'] ?? $data['download_url'] ?? $data['result'] ?? null;

            if (isset($data['error'])) {
                echo "<p style='color:red;'>خطأ من المصدر: " . $data['error'] . "</p>";
            } elseif ($videoUrl) {
                echo "<p>✅ تم استخراج الفيديو!</p>";
                echo "<a href='".$videoUrl."' target='_blank' style='display:block; padding:10px; background:green; color:white; text-decoration:none; border-radius:5px;'>تحميل الآن</a>";
            } else {
                echo "<p style='color:orange;'>لم يتم العثور على حقل URL. استجابة السيرفر:</p>";
                echo "<pre>";
                print_r($data);
                echo "</pre>";
            }
        }
    }
    ?>
    </div>
</div>

</body>
</html>
