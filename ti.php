<?php
// إعداد المتغيرات الأساسية
$api_key = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9"; // ضع مفتاحك هنا
$api_host = "instagram-downloader-download-instagram-videos-stories1.p.rapidapi.com";
$result_data = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['url'])) {
    $target_url = $_POST['url'];
    
    // تجهيز الطلب عبر cURL
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://" . $api_host . "/?userinfo=" . urlencode($target_url),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_HEADER => [
            "x-rapidapi-host: " . $api_host,
            "x-rapidapi-key: " . $api_key
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        $error = "حدث خطأ في الاتصال: " . $err;
    } else {
        $result_data = json_decode($response, true);
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download All In One - PHP Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .main-card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-top: 50px; }
        .header-api { background: linear-gradient(45deg, #6a11cb, #2575fc); color: white; padding: 30px; border-radius: 15px 15px 0 0; text-align: center; }
        .btn-download { background-color: #6a11cb; border: none; padding: 10px 30px; }
        .btn-download:hover { background-color: #2575fc; }
        pre { background: #eee; padding: 15px; border-radius: 8px; direction: ltr; text-align: left; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card main-card">
                <div class="header-api">
                    <h2>Download All in One!</h2>
                    <p>أداة تحميل معلومات الحسابات والفيديوهات</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">أدخل رابط الإنستغرام أو اسم المستخدم:</label>
                            <input type="text" name="url" class="form-control" placeholder="https://www.instagram.com/user_name" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-download text-white">جلب البيانات</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($result_data): ?>
                        <h4 class="mb-3">النتائج:</h4>
                        <div class="result-box">
                            <pre><?php print_r($result_data); ?></pre>
                        </div>
                    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <p class="text-muted text-center">لا توجد بيانات لعرضها أو الرابط غير صحيح.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
