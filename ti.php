<?php
// إعداد المتغيرات الأساسية
$api_key = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9";
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
        CURLOPT_HTTPHEADER => [ // تم التصحيح هنا: حذف الشرطة السفلية الزائدة
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
        .btn-download { background-color: #6a11cb; border: none; padding: 10px 30px; border-radius: 8px; transition: 0.3s; }
        .btn-download:hover { background-color: #2575fc; transform: translateY(-2px); }
        .result-box { background: #ffffff; border: 1px solid #ddd; padding: 20px; border-radius: 10px; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 8px; direction: ltr; text-align: left; overflow: auto; max-height: 500px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card main-card">
                <div class="header-api">
                    <h2>Download All in One!</h2>
                    <p>أداة جلب بيانات حسابات التواصل الاجتماعي</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold">ضع رابط البروفايل أو اسم المستخدم (مثال: Instagram):</label>
                            <input type="text" name="url" class="form-control form-control-lg text-center" placeholder="https://www.instagram.com/..." required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-download text-white btn-lg">جلب البيانات الآن</button>
                        </div>
                    </form>

                    <hr class="my-5">

                    <?php if ($error): ?>
                        <div class="alert alert-danger shadow-sm"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($result_data): ?>
                        <div class="result-box">
                            <h4 class="mb-3 text-primary">البيانات المستلمة (JSON):</h4>
                            <pre><?php echo json_encode($result_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                        </div>
                    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <div class="alert alert-warning text-center shadow-sm">
                            لم نتمكن من العثور على بيانات، تأكد من صحة الرابط أو صلاحية مفتاح الـ API.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-center mt-4 text-muted" style="font-size: 0.9rem;">تم التطوير باستخدام PHP & RapidAPI</p>
        </div>
    </div>
</div>

</body>
</html>
