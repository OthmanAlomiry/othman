<?php
// مفتاحك من الصورة الثانية
$apiKey = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9";

// تحديد التاريخ والمنطقة الزمنية للسعودية
$timezone = "Asia/Riyadh";
$date = date('Y-m-d');

$curl = curl_init();

// جلب كافة مباريات اليوم مع تحديد المنطقة الزمنية لضمان الدقة
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$date&timezone=$timezone",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
        "X-RapidAPI-Key: $apiKey"
    ],
]);

$response = curl_exec($curl);
$result = json_decode($response, true);
curl_close($curl);

$fixtures = $result['response'] ?? [];

// مصفوفة الدوريات التي تهمك (لتمييزها في العرض)
$myLeagues = [39, 140, 135, 78, 61, 307, 2, 3, 17, 19];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول المباريات</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 10px; }
        .match-box { background: white; margin-bottom: 8px; padding: 12px; border-radius: 8px; display: flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .league-name { font-size: 10px; color: #6200ea; font-weight: bold; display: block; }
        .team { flex: 1; text-align: center; font-size: 13px; font-weight: bold; }
        .team img { width: 25px; height: 25px; vertical-align: middle; margin: 0 5px; }
        .score { width: 60px; text-align: center; background: #eee; border-radius: 4px; font-weight: 900; padding: 5px 0; }
        .time { color: #d32f2f; font-size: 12px; }
        .header { text-align: center; padding: 20px; color: #1a237e; }
    </style>
</head>
<body>

<div class="header">
    <h2>⚽ مباريات اليوم (بتوقيت الرياض)</h2>
    <p><?php echo $date; ?></p>
</div>

<?php if (!empty($fixtures)): ?>
    <?php foreach ($fixtures as $item): ?>
        <div class="match-box">
            <div class="team">
                <?php echo $item['teams']['home']['name']; ?>
                <img src="<?php echo $item['teams']['home']['logo']; ?>">
            </div>

            <div style="text-align:center; flex: 0.8;">
                <span class="league-name"><?php echo $item['league']['name']; ?></span>
                <?php if($item['fixture']['status']['short'] == 'NS'): ?>
                    <span class="time"><?php echo date('H:i', strtotime($item['fixture']['date'])); ?></span>
                <?php else: ?>
                    <div class="score"><?php echo $item['goals']['home']; ?> - <?php echo $item['goals']['away']; ?></div>
                <?php endif; ?>
            </div>

            <div class="team">
                <img src="<?php echo $item['teams']['away']['logo']; ?>">
                <?php echo $item['teams']['away']['name']; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div style="text-align: center; padding: 50px; background: white; border-radius: 15px;">
        <p>لا توجد مباريات مسجلة حالياً في الـ API لهذا اليوم.</p>
        <p><small>جرب تغيير التاريخ في الكود إلى 2026-04-12 للتأكد من جلب بيانات الأمس.</small></p>
    </div>
<?php endif; ?>

</body>
</html>
