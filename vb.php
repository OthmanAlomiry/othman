<?php
// إعدادات الوصول
$apiKey = '49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9'; 
$dateToday = date('Y-m-d');
$url = "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$dateToday";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
    "X-RapidAPI-Key: $apiKey"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

$match_data = json_decode($response, true);
?>

<div style="direction: rtl; font-family: sans-serif; color: #fff; background: #050c14; padding: 20px;">
    
    <?php if ($httpCode !== 200): ?>
        <div style="background: #e11d48; padding: 10px; border-radius: 5px;">
            <strong>خطأ في الاتصال (كود: <?= $httpCode ?>)</strong><br>
            التفسير: يبدو أن المفتاح غير مفعل في موقع RapidAPI. يرجى الدخول للموقع والضغط على "Subscribe to Test".
        </div>
    <?php elseif (empty($match_data['response'])): ?>
        <div style="background: #f1c40f; color: #000; padding: 10px; border-radius: 5px;">
            <strong>لا توجد مباريات مجدولة حالياً</strong><br>
            الـ API لا يجد أي مباريات لهذا التاريخ (<?= $dateToday ?>). جرب غداً أو تأكد من توقيت السيرفر.
        </div>
    <?php else: ?>
        <h3>🏆 المباريات المتوفرة حالياً (<?= count($match_data['response']) ?> مباراة)</h3>
        <table border="1" style="width: 100%; border-collapse: collapse; text-align: center;">
            <tr style="background: #333;">
                <th>البطولة</th>
                <th>المباراة</th>
                <th>الحالة</th>
            </tr>
            <?php foreach ($match_data['response'] as $m): ?>
            <tr>
                <td style="font-size: 11px;"><?= $m['league']['name'] ?></td>
                <td><?= $m['teams']['home']['name'] ?> vs <?= $m['teams']['away']['name'] ?></td>
                <td style="color: #00ff87;"><?= $m['fixture']['status']['short'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
