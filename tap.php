<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- تم وضع التوكن الخاص بك هنا يا عثمان ---
$API_KEY = '273aaeb61360452588653ffea820cc19'; 

function getMatches($key) {
    // الرابط يجلب مباريات اليوم من أهم الدوريات العالمية
    $url = "https://api.football-data.org/v4/matches";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Auth-Token: $key"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$data = getMatches($API_KEY);
$matches = $data['matches'] ?: [];

// ترتيب المباريات حسب البطولة
$grouped = [];
foreach ($matches as $m) {
    $grouped[$m['competition']['name']][] = $m;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); }
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; margin: 0; padding: 15px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 500px; }
        
        .header { text-align: center; padding: 20px 0; border-bottom: 1px solid var(--border); margin-bottom: 20px; }
        .header h2 { margin: 0; font-weight: 900; color: var(--main); text-shadow: 0 0 15px rgba(225, 29, 72, 0.3); font-size: 1.2rem; }

        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 8px 15px; border-radius: 8px; font-size: 13px; font-weight: 900; margin: 25px 0 12px; display: flex; align-items: center; gap: 10px; border-right: 4px solid #fff; }

        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 15px; padding: 12px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; transition: 0.3s; backdrop-filter: blur(5px); }
        
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 6px; }
        .team img { width: 30px; height: 30px; object-fit: contain; }
        .team span { font-size: 10px; font-weight: 700; }

        .match-info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 4px; }
        .score { font-size: 18px; font-weight: 900; color: #fff; letter-spacing: 3px; }
        .vs { font-size: 12px; color: var(--main); font-weight: 900; }
        .time { font-size: 11px; color: #aaa; background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 50px; }
        .live { color: #22c55e; font-size: 10px; font-weight: 900; animation: blink 1s infinite; }

        @keyframes blink { 50% { opacity: 0.3; } }
        .no-matches { text-align: center; padding: 50px; opacity: 0.5; font-size: 13px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fas fa-futbol"></i> جدول مباريات اليوم</h2>
    </div>

    <?php if(empty($grouped)): ?>
        <div class="no-matches">لا توجد مباريات جارية حالياً..</div>
    <?php else: ?>
        <?php foreach($grouped as $league => $matchList): ?>
            <div class="league-title">
                <i class="fas fa-trophy"></i> <?= $league ?>
            </div>
            
            <?php foreach($matchList as $match): 
                // تحويل التوقيت لمكة (+3 ساعات)
                $time = date("H:i", strtotime($match['utcDate'] . " +3 hours")); 
                $status = $match['status'];
                $homeScore = $match['score']['fullTime']['home'] ?? 0;
                $awayScore = $match['score']['fullTime']['away'] ?? 0;
            ?>
            <div class="match-card">
                <div class="team">
                    <img src="<?= $match['homeTeam']['crest'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <span><?= $match['homeTeam']['shortName'] ?: $match['homeTeam']['name'] ?></span>
                </div>

                <div class="match-info">
                    <?php if($status == 'IN_PLAY' || $status == 'PAUSED'): ?>
                        <div class="score" style="color:var(--main);"><?= $homeScore ?> - <?= $awayScore ?></div>
                        <div class="live">● مباشر</div>
                    <?php elseif($status == 'FINISHED'): ?>
                        <div class="score"><?= $homeScore ?> - <?= $awayScore ?></div>
                        <div style="font-size:9px; color:#666;">انتهت</div>
                    <?php else: ?>
                        <div class="vs">VS</div>
                        <div class="time"><?= $time ?></div>
                    <?php endif; ?>
                </div>

                <div class="team">
                    <img src="<?= $match['awayTeam']['crest'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <span><?= $match['awayTeam']['shortName'] ?: $match['awayTeam']['name'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer style="text-align:center; padding:30px; font-size:9px; opacity:0.3;">
        تحديث تلقائي للمباريات - الخدمة الرقمية 2026
    </footer>
</div>

</body>
</html>
