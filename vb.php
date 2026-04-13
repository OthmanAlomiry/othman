<?php
// المفاتيح الجديدة من صورتك الأخيرة
$api_key = "sTkcO9Ziv3PfCcIl";
$api_secret = "BrotoDGaJ3VBKrbdkvp9Forasgp4t7N5";
$today = date('Y-m-d');

// رابط جلب جدول مباريات اليوم
$url = "https://live-score-api.com/api/live/fixtures.json?key=$api_key&secret=$api_secret&lang=ar&date=$today";

// استخدام cURL لجلب البيانات
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// استخراج البيانات
$matches = [];
if (isset($result['success']) && $result['success'] == true) {
    $matches = $result['data']['fixtures'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 15px; }
        .container { max-width: 600px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; text-align: center; }
        .match { background: white; padding: 15px; border-radius: 10px; display: flex; align-items: center; margin-bottom: 10px; border: 1px solid #eee; }
        .team { flex: 1; font-weight: bold; text-align: center; font-size: 14px; }
        .info { flex: 1; text-align: center; border-left: 1px solid #eee; border-right: 1px solid #eee; margin: 0 10px; }
        .time { font-size: 18px; color: #6200ea; font-weight: 900; display: block; }
        .league { font-size: 10px; color: #888; }
        .error { color: #d32f2f; background: #ffebee; padding: 15px; border-radius: 8px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2 style="color:#6200ea; margin:0;">⚽ مباريات اليوم</h2>
        <p style="color:#666;"><?php echo $today; ?></p>
    </div>

    <?php if (!empty($matches)): ?>
        <?php foreach ($matches as $match): ?>
            <div class="match">
                <div class="team"><?php echo $match['home_name']; ?></div>
                <div class="info">
                    <span class="league"><?php echo $match['league_name']; ?></span>
                    <span class="time"><?php echo substr($match['time'], 0, 5); ?></span>
                </div>
                <div class="team"><?php echo $match['away_name']; ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="error">
            <strong>لا توجد بيانات!</strong><br>
            <?php 
            if(isset($result['error'])) {
                echo "السبب: " . $result['error']; 
            } else {
                echo "ربما لم تبدأ أي مباريات مجدولة لهذه الباقة بعد.";
            }
            ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
