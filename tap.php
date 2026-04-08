<?php
// إعدادات API المباريات الخاصة بك عثمان
$FOOTBALL_API_KEY = '895397d292e24b08cf4b107b68f52524'; 
$date = date('Y-m-d');
$timezone = "Asia/Riyadh";

// جلب البيانات من السيرفر مباشرة عثمان
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://v3.football.api-sports.io/fixtures?date=$date&timezone=$timezone");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["x-apisports-key: $FOOTBALL_API_KEY"]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$res = json_decode($response, true);
curl_close($ch);

$matches = $res['response'] ?? [];

// ترتيب الدوري السعودي ليكون في المقدمة عثمان
usort($matches, function($a, $b) {
    return ($a['league']['id'] == 307) ? -1 : 1;
});
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة المباريات عثمان</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #050c14; --sky: #0ea5e9; --glass: rgba(255,255,255,0.05); }
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; display: flex; justify-content: center; padding: 20px; margin: 0; }
        .container { width: 100%; max-width: 450px; }
        .match-card { background: var(--glass); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 15px; margin-bottom: 12px; position: relative; display: flex; align-items: center; justify-content: space-between; }
        .league-name { position: absolute; top: -8px; right: 15px; background: var(--sky); font-size: 8px; padding: 2px 10px; border-radius: 10px; font-weight: 900; }
        .team { flex: 1; text-align: center; font-size: 11px; font-weight: 700; }
        .team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .info { flex: 0.8; text-align: center; border-left: 1px solid rgba(255,255,255,0.05); border-right: 1px solid rgba(255,255,255,0.05); }
        .score { font-size: 20px; font-weight: 900; }
        .time { font-size: 12px; color: var(--sky); }
    </style>
</head>
<body>
<div class="container">
    <h2 style="color:var(--sky); border-right:3px solid var(--sky); padding-right:10px; font-size:16px;">مباريات اليوم (<?= $date ?>)</h2>
    
    <?php if (empty($matches)): ?>
        <div style="text-align:center; padding:50px; opacity:0.5;">
            لا توجد مباريات مسجلة حالياً.<br>
            <small>تأكد من تفعيل باقة Football في حسابك.</small>
        </div>
    <?php else: foreach ($matches as $m): 
        $status = $m['fixture']['status']['short'];
        $isLive = in_array($status, ['1H','2H','HT','ET','P']);
    ?>
        <div class="match-card">
            <div class="league-name"><?= $m['league']['name'] ?></div>
            <div class="team">
                <img src="<?= $m['teams']['home']['logo'] ?>">
                <span><?= $m['teams']['home']['name'] ?></span>
            </div>
            <div class="info">
                <div class="score" style="<?= $isLive ? 'color:#e11d48' : '' ?>">
                    <?= $m['goals']['home'] ?? 0 ?> - <?= $m['goals']['away'] ?? 0 ?>
                </div>
                <div class="time">
                    <?= $isLive ? 'مباشر' : date("H:i", $m['fixture']['timestamp']) ?>
                </div>
            </div>
            <div class="team">
                <img src="<?= $m['teams']['away']['logo'] ?>">
                <span><?= $m['teams']['away']['name'] ?></span>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>
</body>
</html>
