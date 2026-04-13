<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تحميل فيديو سناب شات</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; text-align: center; }
        input[type="text"] { width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; background-color: #FFFC00; border: 1px solid #000; cursor: pointer; border-radius: 5px; font-weight: bold; }
        .result { margin-top: 20px; padding: 20px; border: 1px solid #eee; background: #fafafa; word-wrap: break-word; }
    </style>
</head>
<body>

    <h2>أداة تحميل فيديوهات سناب شات</h2>
    
    <form method="POST">
        <input type="text" name="snap_url" placeholder="ضع رابط سناب شات هنا..." required>
        <button type="submit">استخراج الفيديو</button>
    </form>

    <div class="result">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['snap_url'])) {
        
        $snapUrl = $_POST['snap_url'];
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
            echo "خطأ في الاتصال: " . $err;
        } else {
            $data = json_decode($response, true);
            
            if (isset($data['error'])) {
                echo "<p style='color:red;'>عذراً: " . $data['error'] . "</p>";
            } elseif (isset($data['url'])) {
                echo "<h3>تم العثور على الفيديو:</h3>";
                echo "<a href='".$data['url']."' target='_blank' style='color:blue; text-decoration:underline;'>اضغط هنا لفتح وتحميل الفيديو</a>";
            } else {
                echo "استجابة غير معروفة، جرب رابطاً آخر.";
            }
        }
    } else {
        echo "يرجى إدخال الرابط أعلاه لبدء العملية.";
    }
    ?>
    </div>

</body>
</html>
