<?php
/**
 * بوابة المباريات - متجر الخدمة الرقمية
 * تم استخدام مفتاحك: 49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9
 */

// 1. إعدادات API المباريات
$apiKey = '49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9'; // مفتاحك المستخرج
$dateToday = date('Y-m-d');
$url = "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$dateToday";

// جلب بيانات المباريات
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
    "X-RapidAPI-Key: $apiKey"
]);
$response = curl_exec($ch);
curl_close($ch);
$match_data = json_decode($response, true);

// 2. جلب بيانات القنوات من jsonbin.io الخاص بك
$bin_url = "https://api.jsonbin.io/v3/b/69db5855aaba882197ed8b66"; // Bin ID من صورتك
$channels_json = @file_get_contents($bin_url);
$channels_data = json_decode($channels_json, true)['record']['custom_channels'] ?? [];

function translate_ar($text) {
    // دالة مبسطة للترجمة أو يمكنك تركها كما هي
    return $text;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مباريات اليوم - d-service.pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --card: rgba(255, 255, 255, 0.05); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; }
        .match-card { background: var(--card); border-radius: 15px; padding: 15px; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: space-between; }
        .team { text-align: center; flex: 1; }
        .team img { width: 45px; height: 45px; object-fit: contain; }
        .team span { display: block; font-size: 12px; margin-top: 5px; font-weight: bold; }
        .center-info { flex: 1.2; text-align: center; }
        .league { font-size: 10px; color: #00ff87; font-weight: bold; }
        .time-score { font-size: 20px; font-weight: 900; margin: 5px 0; }
        .status { font-size: 10px; padding: 3px 8px; border-radius: 5px; background: var(--main); }
        .watch-btn { display: inline-block; margin-top: 10px; padding: 8px 20px; background: #25d366; color: #fff; text-decoration: none; border-radius: 50px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <img src="https://d-service.pro/logo.png" alt="Logo" style="width: 120px;">
    <h2>🏆 مباريات اليوم</h2>
    <p style="font-size: 12px; opacity: 0.7;">بوابة النقل المباشر - متجر الخدمة الرقمية</p>
</div>

<div class="container">
    <?php if (!empty($match_data['response'])): ?>
        <?php foreach ($match_data['response'] as $m): 
            $status = $m['fixture']['status']['short'];
            $is_live = in_array($status, ['1H','2H','HT','LIVE']);
        ?>
        <div class="match-card">
            <div class="team">
                <img src="<?= $m['teams']['home']['logo'] ?>">
                <span><?= translate_ar($m['teams']['home']['name']) ?></span>
            </div>

            <div class="center-info">
                <span class="league"><?= translate_ar($m['league']['name']) ?></span>
                <div class="time-score">
                    <?php if($is_live || $status == 'FT'): ?>
                        <?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?>
                    <?php else: ?>
                        <?= date('H:i', strtotime($m['fixture']['date'])) ?>
                    <?php endif; ?>
                </div>
                <?php if($is_live): ?>
                    <span class="status">مباشر الآن</span>
                <?php else: ?>
                    <span style="font-size: 10px; opacity: 0.6;"><?= $status ?></span>
                <?php endif; ?>
                <br>
                <a href="https://d-service.pro/watch" class="watch-btn">شاهد الآن</a>
            </div>

            <div class="team">
                <img src="<?= $m['teams']['away']['logo'] ?>">
                <span><?= translate_ar($m['teams']['away']['name']) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align:center; padding:50px;">
            <p>لا توجد مباريات متوفرة حالياً.</p>
            <p style="font-size:12px; opacity:0.5;">تأكد من تفعيل باقة API-Football في RapidAPI.</p>
        </div>
    <?php endif; ?>
</div>

<div style="text-align:center; margin-top:30px; font-size:11px; opacity:0.5;">
    جميع الحقوق محفوظة لمتجر الخدمة الرقمية © <?= date('Y') ?>
</div>

</body>
</html>
