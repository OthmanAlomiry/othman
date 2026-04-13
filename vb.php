<?php
// مفتاحك الخاص من الصورة
$apiKey = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9";
$date = date('Y-m-d');
$timezone = "Asia/Riyadh";

// قائمة أهم الدوريات (IDs) لضمان جلب بياناتها
$leagues = "39-140-135-78-61-307-2-3-17-19"; 

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$date&league=$leagues&timezone=$timezone",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
        "X-RapidAPI-Key: $apiKey"
    ],
]);

$response = curl_exec($curl);
$result = json_decode($response, true);
curl_close($curl);

$matches = $result['response'] ?? [];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 15px; }
        .match-card { background: white; border-radius: 12px; padding: 15px; margin-bottom: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .league-title { font-size: 11px; color: #6200ea; font-weight: bold; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px; margin-bottom: 10px; }
        .flex-container { display: flex; align-items: center; justify-content: space-between; }
        .team { flex: 1; text-align: center; font-size: 14px; font-weight: 600; }
        .team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .score-info { flex: 0.7; text-align: center; background: #fafafa; border-radius: 8px; padding: 5px; }
        .time { font-size: 16px; color: #d32f2f; font-weight: bold; }
        .goals { font-size: 20px; font-weight: 900; }
        .no-data { text-align: center; padding: 50px; background: white; border-radius: 20px; color: #777; }
    </style>
</head>
<body>

    <h2 style="text-align:center; color:#1a237e;">⚽ جدول مباريات اليوم</h2>

    <?php if (!empty($matches)): ?>
        <?php foreach ($matches as $m): ?>
            <div class="match-card">
                <div class="league-title"><?php echo $m['league']['name']; ?> (<?php echo $m['league']['country']; ?>)</div>
                <div class="flex-container">
                    <div class="team">
                        <img src="<?php echo $m['teams']['home']['logo']; ?>">
                        <?php echo $m['teams']['home']['name']; ?>
                    </div>
                    
                    <div class="score-info">
                        <?php if($m['fixture']['status']['short'] == 'NS'): ?>
                            <span class="time"><?php echo date('H:i', strtotime($m['fixture']['date'])); ?></span>
                        <?php else: ?>
                            <div class="goals"><?php echo $m['goals']['home']; ?> - <?php echo $m['goals']['away']; ?></div>
                            <small style="color:green"><?php echo $m['fixture']['status']['elapsed']; ?>'</small>
                        <?php endif; ?>
                    </div>

                    <div class="team">
                        <img src="<?php echo $m['teams']['away']['logo']; ?>">
                        <?php echo $m['teams']['away']['name']; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-data">
            <p>لا توجد مباريات مجدولة اليوم في الدوريات المختارة.</p>
            <p><small>تأكد من تفعيل "Subscriptions" في صفحة الـ API على RapidAPI.</small></p>
            </div>
    <?php endif; ?>

</body>
</html>
