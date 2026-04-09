<?php
// --- المفاتيح الجديدة من صورتك الأخيرة ---
$api_key    = "P0dgFndfWhrXNdda"; 
$api_secret = "BBvwCZdhld8mV1vK1J1H6eX3wo0jBdtd";

// تم تعديل الرابط لاستخدام 'scores' بدلاً من 'live' للتأكد من عمله مع الباقة المجانية
$url = "https://live-score-api.com/api-client/scores/history.json?key=" . $api_key . "&secret=" . $api_secret . "&day=today";

// جلب البيانات باستخدام cURL لضمان الاستقرار على سيرفر Render
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج المباريات - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: sans-serif; }
        .match-card { background: white; border-radius: 10px; border-right: 5px solid #0d6efd; margin-bottom: 10px; }
        .score { font-size: 1.4rem; font-weight: bold; color: #0d6efd; background: #eef5ff; padding: 5px 15px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container py-4">
    <h3 class="text-center mb-4">⚽ نتائج مباريات اليوم</h3>

    <?php 
    // التأكد من نجاح الطلب وجود بيانات
    if (isset($data['success']) && $data['success'] == true && !empty($data['data']['scores'])): 
    ?>
        <div class="row justify-content-center">
            <?php foreach ($data['data']['scores'] as $match): ?>
                <div class="col-md-8">
                    <div class="match-card p-3 shadow-sm">
                        <div class="text-muted small mb-2"><?php echo $match['league_name']; ?></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-end" style="flex: 1;"><strong><?php echo $match['home_name']; ?></strong></div>
                            <div class="mx-3"><span class="score"><?php echo $match['score']; ?></span></div>
                            <div class="text-start" style="flex: 1;"><strong><?php echo $match['away_name']; ?></strong></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-danger text-center">
            <?php 
            if (isset($data['error'])) {
                echo "خطأ من الـ API: " . $data['error'];
            } else {
                echo "لا توجد مباريات مسجلة لهذا اليوم حتى الآن.";
            }
            ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
