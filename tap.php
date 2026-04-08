<?php
// إعدادات المفتاح الجديد عثمان
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// نظام جلب البيانات من السيرفر عثمان
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Auth-Token: ' . $apiKey
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

$matches = $data['matches'] ?? [];

date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول المباريات - المصدر الجديد</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --sky: #0ea5e9; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 20px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; }
        .title-header { margin-bottom: 25px; font-weight: 900; font-size: 18px; color: var(--sky); border-right: 5px solid var(--sky); padding-right: 15px; }
        
        .match-card { background: var(--glass); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 20px 15px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        .m-league { position: absolute; top: -10px; right: 20px; background: var(--sky); font-size: 9px; padding: 3px 12px; border-radius: 50px; font-weight: 900; }
        
        .team { flex: 1.2; text-align: center; font-size: 11px; font-weight: 900; }
        .team img { width: 35px; height: 35px; display: block; margin: 0 auto 8px; filter: drop-shadow(0 2px 5px rgba(0,0,0,0.5)); }
        
        .info { flex: 0.9; text-align: center; border-left: 1px solid rgba(255,255,255,0.05); border-right: 1px solid rgba(255,255,255,0.05); margin: 0 8px; }
        .score { font-size: 22px; font-weight: 900; letter-spacing: 2px; }
        .time { font-size: 13px; color: var(--sky); font-weight: 900; }
        .status-live { color: #22c55e; font-size: 10px; animation: blink 1s infinite; }
        
        @keyframes blink { 50% { opacity: 0.5; } }
        .no-matches { text-align: center; padding: 50px; opacity: 0.5; background: var(--glass); border-radius: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="title-header">مباريات اليوم (المصدر الجديد)</div>
    
    <?php if (empty($matches)): ?>
        <div class="no-matches">لا توجد مباريات متاحة حالياً في هذا المصدر.</div>
    <?php else: foreach ($matches as $m): 
        $matchDate = new DateTime($m['utcDate']);
        $matchDate->setTimezone(new DateTimeZone('Asia/Riyadh'));
        $matchTime = $matchDate->format('H:i');
        $status = $m['status'];
    ?>
        <div class="match-card">
            <div class="m-league"><?php echo $m['competition']['name']; ?></div>
            <div class="team">
                <img src="<?php echo $m['homeTeam']['crest']; ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/33/33736.png'">
                <span><?php echo $m['homeTeam']['shortName'] ?: $m['homeTeam']['name']; ?></span>
            </div>
            <div class="info">
                <div class="score">
                    <?php echo ($status == 'TIMED') ? '- : -' : $m['score']['fullTime']['home'] . ' - ' . $m['score']['fullTime']['away']; ?>
                </div>
                <div class="time">
                    <?php 
                        if ($status == 'IN_PLAY' || $status == 'PAUSED') echo '<span class="status-live">مباشر الآن</span>';
                        elseif ($status == 'FINISHED') echo 'انتهت';
                        else echo $matchTime;
                    ?>
                </div>
            </div>
            <div class="team">
                <img src="<?php echo $m['awayTeam']['crest']; ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/33/33736.png'">
                <span><?php echo $m['awayTeam']['shortName'] ?: $m['awayTeam']['name']; ?></span>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

</body>
</html>
