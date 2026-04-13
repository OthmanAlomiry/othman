<?php
/**
 * صفحة المباريات المباشرة - d-service.pro
 * مبرمجة لتعمل مع Free Livescore API و jsonbin.io
 */

// 1. إعدادات الـ API من صورك
$apiKey = '49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9'; // مفتاحك المستخرج
$host = 'free-livescore-api.p.rapidapi.com';

// طلب البيانات من الـ API لنتائج اليوم
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://free-livescore-api.p.rapidapi.com/livescore/search",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: $host",
        "X-RapidAPI-Key: $apiKey"
    ],
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

$matches = json_decode($response, true)['response']['Teams'] ?? []; // استخراج الفرق كما في صورتك

// 2. قنواتك من jsonbin.io
$binId = "69db5855aaba882197ed8b66"; 
$channelsJson = @file_get_contents("https://api.jsonbin.io/v3/b/$binId/latest");
$channels = json_decode($channelsJson, true)['record']['custom_channels'] ?? [];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>جدول المباريات - d-service.pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@500;800&display=swap" rel="stylesheet">
    <style>
        body { background: #050c14; color: white; font-family: 'Tajawal', sans-serif; padding: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
        .match-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px; }
        .team-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .team-name { font-weight: bold; font-size: 14px; }
        .score { background: #e11d48; padding: 2px 10px; border-radius: 4px; font-weight: 900; }
        .channel-btn { display: block; text-align: center; background: #25d366; color: white; text-decoration: none; padding: 8px; border-radius: 6px; font-size: 12px; margin-top: 10px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">🏆 نتائج ومباريات اليوم</h2>

<div class="grid">
    <?php if (!empty($matches)): ?>
        <?php foreach (array_chunk($matches, 2) as $pair): // تقسيم النتائج لفرق متواجهة ?>
            <?php if(count($pair) == 2): ?>
            <div class="match-card">
                <div class="team-row">
                    <span class="team-name"><?= $pair[0]['Nm'] ?></span>
                    <span class="score">?</span>
                </div>
                <div class="team-row">
                    <span class="team-name"><?= $pair[1]['Nm'] ?></span>
                    <span class="score">?</span>
                </div>
                <a href="watch.php?id=<?= $channels[0]['id'] ?? '' ?>" class="channel-btn">مشاهدة عبر <?= $channels[0]['name'] ?? 'البث المباشر' ?></a>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; grid-column: 1/-1;">لا توجد بيانات حالياً. تأكد من تفعيل باقة API-Football الأكثر دقة للدوريات العربية.</p>
    <?php endif; ?>
</div>

</body>
</html>
