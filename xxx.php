<?php

// مفتاح الـ API الخاص بك من الصورة
$apiKey = '273aaeb61360452588653ffea820cc19';

// جلب مباريات اليوم (سيقوم النظام تلقائياً بجلب مباريات تاريخ اليوم الحالي)
$url = 'https://api.football-data.org/v4/matches';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Auth-Token: ' . $apiKey
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// تحديد المنطقة الزمنية (مثلاً مكة المكرمة) لضبط الوقت
date_default_timezone_set('Asia/Riyadh');

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مباريات اليوم</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #1a1a1a; color: #fff; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .match-card { background: #2d2d2d; border-left: 5px solid #27ae60; margin-bottom: 15px; padding: 20px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; }
        .league-name { color: #27ae60; font-size: 0.9em; margin-bottom: 5px; font-weight: bold; }
        .teams { font-size: 1.1em; flex: 2; }
        .time { flex: 1; text-align: left; color: #f1c40f; font-weight: bold; }
        h1 { text-align: center; color: #27ae60; }
        .no-matches { text-align: center; padding: 50px; background: #2d2d2d; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h1>⚽ مباريات اليوم</h1>

    <?php if (isset($data['matches']) && count($data['matches']) > 0): ?>
        <?php foreach ($data['matches'] as $match): ?>
            <div class="match-card">
                <div class="teams">
                    <div class="league-name"><?php echo $match['competition']['name']; ?></div>
                    <span><?php echo $match['homeTeam']['name']; ?></span> 
                    <span style="color: #7f8c8d; margin: 0 10px;">VS</span> 
                    <span><?php echo $match['awayTeam']['name']; ?></span>
                </div>
                <div class="time">
                    <?php 
                        // تحويل الوقت من UTC إلى التوقيت المحلي
                        echo date('h:i A', strtotime($match['utcDate'])); 
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-matches">
            <p>لا توجد مباريات مسجلة حالياً في الـ API لهذا اليوم.</p>
            <small>ملاحظة: الحساب المجاني قد يغطي بطولات معينة فقط مثل الدوري الإنجليزي، الألماني، ودوري أبطال أوروبا.</small>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
