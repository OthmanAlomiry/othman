<?php
// المفاتيح الخاصة بك من الصور السابقة
$api_key    = "P0dgFndfWhrXNdda"; 
$api_secret = "BBvwCZdhld8mV1vK1J1H6eX3wo0jBdtd";

// الرابط الرسمي المسموح به عادة في الباقة التجريبية لجلب مباريات اليوم
$url = "https://live-score-api.com/api-client/fixtures/matches.json?key=" . $api_key . "&secret=" . $api_secret;

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
    <title>جدول المباريات - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .match-card { background: white; border-radius: 12px; margin-bottom: 15px; border: none; }
        .time-badge { background: #e9ecef; color: #495057; font-weight: bold; border-radius: 5px; padding: 5px 10px; }
        .team-name { font-weight: 600; color: #212529; }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-5">📅 جدول مباريات اليوم</h2>

    <?php if (isset($data['success']) && $data['success'] == true && !empty($data['data']['fixtures'])): ?>
        <div class="row justify-content-center">
            <?php foreach ($data['data']['fixtures'] as $match): ?>
                <div class="col-md-8 col-lg-6">
                    <div class="match-card p-3 shadow-sm text-center">
                        <div class="small text-primary mb-2"><?php echo htmlspecialchars($match['league_name']); ?></div>
                        <div class="row align-items-center">
                            <div class="col-4 team-name"><?php echo htmlspecialchars($match['home_name']); ?></div>
                            <div class="col-4">
                                <span class="time-badge"><?php echo substr($match['time'], 0, 5); ?></span>
                                <div class="small text-muted mt-1"><?php echo $match['date']; ?></div>
                            </div>
                            <div class="col-4 team-name"><?php echo htmlspecialchars($match['away_name']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-danger mx-auto text-center" style="max-width: 600px;">
            <?php 
            if (isset($data['error'])) {
                echo "<strong>خطأ:</strong> " . $data['error'];
            } else {
                echo "لا توجد مباريات مجدولة حالياً في حسابك.";
            }
            ?>
            <hr>
            <p class="mb-0 small">إذا استمر الخطأ، جرب الدخول للموقع والتأكد من تفعيل "Matches" في قائمة الـ Endpoints الخاصة بك.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
