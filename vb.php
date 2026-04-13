<?php
// إعدادات الـ API
$api_key = "bYKapDzujDdGdDwq";
$api_secret = "MLDJw7w8gjbHauTlsEwRi6lBKyixCPoY";
$url = "https://live-score-api.com/api/live/scores.json?key=$api_key&secret=$api_secret&lang=ar";

// استخدام cURL بدلاً من file_get_contents لضمان العمل في بيئة Docker
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // لتجنب مشاكل شهادات SSL
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// التحقق من وجود بيانات لتجنب خطأ Array offset on null
$matches = (isset($result['data']['match']) && is_array($result['data']['match'])) ? $result['data']['match'] : [];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; text-align: center; }
        .container { max-width: 600px; margin: auto; }
        .match-card { background: white; padding: 15px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: space-around; align-items: center; }
        .score { font-weight: bold; font-size: 1.2em; color: #6200ea; }
        .league { font-size: 0.8em; color: #888; display: block; margin-bottom: 5px; }
        .error-msg { color: #d32f2f; background: #ffcdd2; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h1 style="color: #6200ea;">⚽ مباريات اليوم المباشرة</h1>
    <p><?php echo date('Y-m-d'); ?></p>

    <?php if (!empty($matches)): ?>
        <?php foreach ($matches as $match): ?>
            <div class="match-card">
                <div style="flex:1"><?php echo $match['home_name']; ?></div>
                <div style="flex:1">
                    <span class="league"><?php echo $match['league_name']; ?></span>
                    <span class="score"><?php echo $match['score']; ?></span>
                    <div style="font-size: 0.7em; color: red;"><?php echo $match['time']; ?>'</div>
                </div>
                <div style="flex:1"><?php echo $match['away_name']; ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="error-msg">
            لا توجد مباريات مباشرة حالياً أو انتهت صلاحية المفتاح التجريبي.
            <br>
            <small>تأكد من تفعيل الباقة في موقع live-score-api.com</small>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
