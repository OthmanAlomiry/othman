<?php
/**
 * تصميم كامل لصفحة API Download All In One
 * تم إصلاح أخطاء الثوابت وتنسيق إرسال المعلمات
 */

$api_key = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9";
$api_host = "instagram-downloader-download-instagram-videos-stories1.p.rapidapi.com";
$result_data = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['url'])) {
    $target_url = trim($_POST['url']); 
    
    $curl = curl_init();

    // بناء الرابط مع التأكد من دمج المعلمات بشكل صحيح
    // ملاحظة: إذا استمر الخطأ، جرب تغيير 'userinfo' إلى 'url'
    $api_url = "https://" . $api_host . "/?userinfo=" . urlencode($target_url);

    curl_setopt_array($curl, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: " . $api_host,
            "x-rapidapi-key: " . $api_key,
            "Accept: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        $error = "خطأ في الاتصال بالسيرفر: " . $err;
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
    <title>Download All In One - Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        :root { --primary-color: #6a11cb; --secondary-color: #2575fc; }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .main-card { border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 900px; }
        .card-header-custom { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; padding: 40px 20px; text-align: center; }
        .btn-custom { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border: none; color: white; font-weight: bold; transition: 0.3s; }
        .btn-custom:hover { opacity: 0.9; transform: translateY(-2px); color: white; }
        .json-output { background: #282c34; color: #abb2bf; padding: 20px; border-radius: 10px; direction: ltr; text-align: left; font-family: 'Courier New', Courier, monospace; overflow-x: auto; }
        .status-badge { font-size: 0.8rem; padding: 5px 12px; border-radius: 20px; }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="card main-card">
        <div class="card-header-custom">
            <i class="fas fa-cloud-download-alt fa-3x mb-3"></i>
            <h1 class="h3">Download All In One</h1>
            <p class="mb-0 opactiy-75">استخرج البيانات من أي رابط تواصل اجتماعي عبر API</p>
        </div>
        
        <div class="card-body p-4 p-lg-5">
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="form-label fw-bold">ضع الرابط هنا (Instagram, TikTok, etc...):</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white"><i class="fas fa-link text-muted"></i></span>
                        <input type="text" name="url" class="form-control" placeholder="https://www.instagram.com/..." required>
                    </div>
                    <div class="form-text text-muted mt-2 small text-center">
                        <i class="fas fa-info-circle"></i> يتم معالجة الطلب عبر استدعاء RapidAPI بشكل مباشر.
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-custom btn-lg shadow-sm">
                        <i class="fas fa-magic me-2"></i> جلب البيانات
                    </button>
                </div>
            </form>

            <?php if ($error || $result_data): ?>
                <hr class="my-5">
                <div class="results-section">
                    <h5 class="mb-3"><i class="fas fa-poll-h me-2 text-primary"></i> استجابة السيرفر:</h5>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger shadow-sm border-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($result_data): ?>
                        <div class="json-output shadow-sm">
                            <pre class="mb-0"><?php echo json_encode($result_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                        </div>
                        
                        <?php if (isset($result_data['message']) && strpos($result_data['message'], 'Missing') !== false): ?>
                            <div class="alert alert-warning mt-3 small shadow-sm">
                                <strong>تنبيه:</strong> يبدو أن الـ API لا يزال يطلب معلمات إضافية. جرب تغيير كلمة <code>userinfo</code> في الكود إلى <code>url</code> أو تأكد من توثيق الـ API الرسمي.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card-footer bg-light text-center py-3">
            <small class="text-muted">نظام API متطور &copy; <?php echo date('Y'); ?></small>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
