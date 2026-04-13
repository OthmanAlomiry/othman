<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحميل فيديو سناب شات</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-align: center; background-color: #f4f4f9; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        input[type="text"] { width: 90%; padding: 15px; margin: 10px 0; border: 2px solid #eee; border-radius: 10px; outline: none; transition: 0.3s; }
        input[type="text"]:focus { border-color: #FFFC00; }
        button { width: 95%; padding: 15px; background-color: #FFFC00; border: none; cursor: pointer; border-radius: 10px; font-weight: bold; font-size: 16px; }
        button:hover { background-color: #e6e300; }
        .result { margin-top: 20px; padding: 15px; border-top: 1px solid #eee; }
        .btn-download { display: block; padding: 15px; background: #28a745; color: white; text-decoration: none; border-radius: 10px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="color: #333;">سناب شات Downloader</h2>
    
    <form method="POST">
        <input type="text" name="snap_url" placeholder="ضع رابط الفيديو هنا..." required>
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
        $data = json_decode($response, true);
        curl_close($curl);

        // التعديل الجوهري هنا لقراءة mediaUrl
        $videoUrl = $data['mediaUrl'] ?? $data['url'] ?? null;

        if ($videoUrl) {
            echo "<p style='color: green;'>✅ تم العثور على الفيديو!</p>";
            echo "<a href='".$videoUrl."' target='_blank' class='btn-download'>تحميل الفيديو الآن</a>";
        } else {
            echo "<p style='color: red;'>عذراً، لم يتم العثور على رابط مباشر. تأكد أن الحساب عام.</p>";
        }
    }
    ?>
    </div>
</div>

</body>
</html>
