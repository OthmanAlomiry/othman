<?php
// --- إعدادات الـ API الخاصة بك من الصورة ---
$api_key    = "FW8Nmn0WFMJw1CmG";
$api_secret = "hB90Qeyx73QRd1CTYLGk8HhOu1kwDGcU";

// رابط جلب المباريات المباشرة (يمكنك تغيير الأندبوينت لاحقاً لجلب جدول الدوري السعودي)
$url = "https://live-score-api.com/api-client/scores/live.json?key=" . $api_key . "&secret=" . $api_secret;

// جلب البيانات
$response = file_get_contents($url);
$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .match-card { background: white; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 15px; transition: 0.3s; }
        .match-card:hover { transform: translateY(-5px); }
        .score { font-weight: bold; font-size: 1.5rem; color: #0d6efd; }
        .league-name { font-size: 0.8rem; color: #6c757d; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .live-badge { animation: blinker 1.5s linear infinite; color: red; font-weight: bold; }
        @keyframes blinker { 50% { opacity: 0; } }
    </style>
</head>
<body>

<div class="container py-4">
    <h2 class="text-center mb-4">⚽ مباريات مباشرة</h2>

    <div class="row justify-content-center">
        <?php if (isset($data['data']['match']) && !empty($data['data']['match'])): ?>
            <?php foreach ($data['data']['match'] as $match): ?>
                <div class="col-md-8">
                    <div class="match-card p-3 text-center">
                        <div class="league-name mb-2"><?php echo $match['league_name']; ?></div>
                        <div class="row align-items-center">
                            <div class="col-4">
                                <h6><?php echo $match['home_name']; ?></h6>
                            </div>
                            <div class="col-4">
                                <div class="score">
                                    <?php echo $match['score']; ?>
                                </div>
                                <div class="small text-muted"><?php echo $match['time']; ?></div>
                                <span class="live-badge small">● مباشر</span>
                            </div>
                            <div class="col-4">
                                <h6><?php echo $match['away_name']; ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-md-8">
                <div class="alert alert-warning text-center">
                    لا توجد مباريات جارية حالياً أو انتهت حصة الـ API. تأكد من تفعيل "Start Free Trial" في حسابك.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
