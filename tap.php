<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// التوكن الخاص بك عثمان
$API_KEY = '273aaeb61360452588653ffea820cc19'; 

// مصفوفة تعريب المسميات عثمان
$translate = [
    'Premier League' => 'الدوري الإنجليزي',
    'La Liga' => 'الدوري الإسباني',
    'Serie A' => 'الدوري الإيطالي',
    'Bundesliga' => 'الدوري الألماني',
    'Ligue 1' => 'الدوري الفرنسي',
    'UEFA Champions League' => 'دوري أبطال أوروبا',
    'Saudi Professional League' => 'الدوري السعودي للمحترفين',
    'Egyptian Premier League' => 'الدوري المصري الممتاز',
    'Champions League' => 'دوري الأبطال',
    'World Cup' => 'كأس العالم',
    'IN_PLAY' => 'مباشر الآن',
    'FINISHED' => 'انتهت',
    'TIMED' => 'لم تبدأ',
    'PAUSED' => 'استراحة'
];

function getMatches($key) {
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

$grouped = [];
foreach ($matches as $m) {
    $leagueName = $m['competition']['name'];
    // تعريب اسم الدوري إذا وجد في المصفوفة عثمان
    $arLeague = isset($translate[$leagueName]) ? $translate[$leagueName] : $leagueName;
    $grouped[$arLeague][] = $m;
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
        .header h2 { margin: 0; font-weight: 900; color: var(--main); text-shadow: 0 0 15px rgba(225, 29, 72, 0.3); font-size: 1.3rem; }

        .league-title { 
            background: linear-gradient(90deg, var(--main), transparent); 
            padding: 10px 15px; border-radius: 12px; font-size: 14px; 
            font-weight: 900; margin: 25px 0 15px; display: flex; 
            align-items: center; gap: 10px; border-right: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .match-card { 
            background: var(--glass); border: 1px solid var(--border); 
            border-radius: 18px; padding: 15px; margin-bottom: 12px; 
            display: flex; align-items: center; justify-content: space-between; 
            transition: 0.3s; backdrop-filter: blur(10px);
        }
        
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px; }
        .team img { width: 35px; height: 35px; object-fit: contain; filter: drop-shadow(0 0 5px rgba(255,255,255,0.2)); }
        .team span { font-size: 11px; font-weight: 700; color: #e2e8f0; }

        .match-info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 6px; }
        .score { font-size: 22px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .vs { font-size: 12px; color: var(--main); font-weight: 900; background: rgba(225, 29, 72, 0.1); padding: 2px 10px; border-radius: 50px; display: inline-block; margin: 0 auto; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; }
        .status-label { font-size: 10px; padding: 2px 8px; border-radius: 5px; font-weight: 900; }
        .live { background: #e11d48; color: #fff; animation: pulse 1.5s infinite; }

        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .no-matches { text-align: center; padding: 60px; opacity: 0.5; font-size: 14px; border: 1px dashed var(--border); border-radius: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fas fa-trophy"></i> مباريات اليوم المباشرة</h2>
    </div>

    <?php if(empty($grouped)): ?>
        <div class="no-matches">
            <i class="fas fa-info-circle" style="display:block; font-size:30px; margin-bottom:10px;"></i>
            لا توجد مباريات متاحة حالياً في قاعدة البيانات
        </div>
    <?php else: ?>
        <?php foreach($grouped as $league => $matchList): ?>
            <div class="league-title">
                <i class="fas fa-star"></i> <?= $league ?>
            </div>
            
            <?php foreach($matchList as $match): 
                $time = date("H:i", strtotime($match['utcDate'] . " +3 hours")); 
                $status = $match['status'];
                $homeScore = $match['score']['fullTime']['home'] ?? 0;
                $awayScore = $match['score']['fullTime']['away'] ?? 0;
            ?>
            <div class="match-card">
                <div class="team">
                    <img src="<?= $match['homeTeam']['crest'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <span><?= $match['homeTeam']['name'] ?></span>
                </div>

                <div class="match-info">
                    <?php if($status == 'IN_PLAY' || $status == 'PAUSED'): ?>
                        <div class="score" style="color:var(--main);"><?= $homeScore ?> - <?= $awayScore ?></div>
                        <span class="status-label live">مباشر الآن</span>
                    <?php elseif($status == 'FINISHED'): ?>
                        <div class="score"><?= $homeScore ?> - <?= $awayScore ?></div>
                        <span class="status-label" style="background:#333;">انتهت</span>
                    <?php else: ?>
                        <div class="vs">VS</div>
                        <div class="time"><?= $time ?></div>
                    <?php endif; ?>
                </div>

                <div class="team">
                    <img src="<?= $match['awayTeam']['crest'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <span><?= $match['awayTeam']['name'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer style="text-align:center; padding:40px; font-size:10px; opacity:0.3; letter-spacing:1px;">
        جميع الأوقات بتوقيت مكة المكرمة<br>
        الخدمة الرقمية © 2026
    </footer>
</div>

</body>
</html>
