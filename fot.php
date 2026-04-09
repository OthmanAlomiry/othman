<?php
// إعداداتك الصحيحة
$api_key    = "FW8Nmn0WFMJw1CmG";
$api_secret = "hB90Qeyx73QRd1CTYLGk8HhOu1kwDGcU";

// رابط جلب المباريات المباشرة
$url = "https://live-score-api.com/api-client/scores/live.json?key={$api_key}&secret={$api_secret}";

// استخدام cURL بدلاً من file_get_contents لتفادي مشاكل الحماية
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // لتجاوز مشاكل شهادة الأمان
$response = curl_exec($ch);
curl_close($ch);

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
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .match-card { background: #fff; border-radius: 12px; border: 1px solid #ddd; margin-bottom: 15px; }
        .score { font-size: 1.8rem; font-weight: bold; color: #007bff; }
        .live-dot { height: 10px; width: 10px; background-color: red; border-radius: 50%; display: inline-block; margin-left: 5px; animation: blink 1s infinite; }
        @keyframes blink { 0% {opacity: 1;} 50% {opacity: 0;} 100% {opacity: 1;} }
    </style>
</head>
<body>

<div class="container py-5 text-center">
    <h2 class="mb-4">⚽ النتائج المباشرة</h2>

    <?php if (isset($data['success']) && $data['success'] == true): ?>
        <?php if (!empty($data['data']['match'])): ?>
            <?php foreach ($data['data']['match'] as $match): ?>
                <div class="match-card p-3 shadow-sm mx-auto" style="max-width: 600px;">
                    <div class="text-muted small mb-2"><?php echo $match['league_name']; ?></div>
                    <div class="row align-items-center">
                        <div class="col-4"><strong><?php echo $match['home_name']; ?></strong></div>
                        <div class="col-4">
                            <div class="score"><?php echo $match['score']; ?></div>
                            <div class="badge bg-light text-danger"><span class="live-dot"></span> مباشر</div>
                        </div>
                        <div class="col-4"><strong><?php echo $match['away_name']; ?></strong></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">لا توجد مباريات جارية حالياً.</div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-danger">
            <strong>خطأ من الـ API:</strong> <?php echo $data['error'] ?? 'تأكد من تفعيل الباقة المجانية (Free Trial) في حسابك.'; ?>
            <br><small>تأكد أنك ضغطت على "Start Free Trial" في موقع live-score-api.com</small>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
