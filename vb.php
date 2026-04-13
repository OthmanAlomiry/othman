<?php
/**
 * صفحة المباريات المتكاملة - متجر الخدمة الرقمية
 * API: API-Football (الأفضل للدوريات العربية والأوروبية)
 */

$apiKey = '49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9'; // مفتاحك
$dateToday = date('Y-m-d');

// رابط الـ API (يجب التأكد من الاشتراك في باقة Basic المجانية لـ API-Football في RapidAPI)
$url = "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$dateToday";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
        "X-RapidAPI-Key: $apiKey"
    ],
]);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// جلب القنوات من Bin الخاص بك
$binUrl = "https://api.jsonbin.io/v3/b/69db5855aaba882197ed8b66/latest";
$ch_data = json_decode(@file_get_contents($binUrl), true)['record']['custom_channels'] ?? [];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول المباريات | d-service.pro</title>
    <style>
        body { background: #050c14; color: #fff; font-family: sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; }
        .match-card { background: rgba(255,255,255,0.05); border: 1px solid #222; border-radius: 12px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; }
        .team { flex: 1; text-align: center; font-size: 13px; }
        .team img { width: 35px; margin-bottom: 5px; }
        .info { flex: 1.5; text-align: center; border-left: 1px solid #333; border-right: 1px solid #333; }
        .time { font-size: 18px; font-weight: bold; color: #f1c40f; }
        .league { font-size: 10px; color: #00ff87; display: block; }
        .btn { display: inline-block; background: #e11d48; color: #fff; text-decoration: none; padding: 5px 15px; border-radius: 5px; font-size: 11px; margin-top: 8px; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align: center;">🏆 مباريات اليوم</h2>
    
    <?php if (!empty($data['response'])): ?>
        <?php foreach ($data['response'] as $m): ?>
        <div class="match-card">
            <div class="team">
                <img src="<?= $m['teams']['home']['logo'] ?>">
                <span><?= $m['teams']['home']['name'] ?></span>
            </div>
            
            <div class="info">
                <span class="league"><?= $m['league']['name'] ?></span>
                <div class="time"><?= date('H:i', strtotime($m['fixture']['date'])) ?></div>
                <a href="watch.php" class="btn">شاهد الآن</a>
            </div>

            <div class="team">
                <img src="<?= $m['teams']['away']['logo'] ?>">
                <span><?= $m['teams']['away']['name'] ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; opacity: 0.5;">لا توجد مباريات جارية. تأكد من الاشتراك في <b>API-Football</b> عبر RapidAPI.</p>
    <?php endif; ?>
</div>

</body>
</html>
