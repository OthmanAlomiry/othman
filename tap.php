<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// إعداد التاريخ عثمان
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
$display_date = date('Y / m / d', strtotime($date_get));

// دالة سحب البيانات المباشرة عثمان
function fetchLiveMatches($date) {
    // نستخدم مصدر بيانات LiveScore المباشر عثمان
    $url = "https://web-api.livescore.com/wrapper/api/w/en/matches/soccer/" . $date . "?timezone=3";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($res, true);
    return $data['Stages'] ?: [];
}

$stages = fetchLiveMatches($date_get);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; padding: 10px; background-color: var(--bg); font-family: 'Tajawal', sans-serif; color: #fff; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; min-height: 100vh; }

        .date-nav { display: flex; align-items: center; justify-content: space-between; background: var(--glass); border: 1px solid var(--border); padding: 12px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(10px); }
        .date-nav a { width: 35px; height: 35px; background: var(--main); color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.3); }
        .date-nav h3 { margin: 0; font-size: 15px; font-weight: 900; color: #fff; }

        .league-header { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 12px; font-size: 13px; font-weight: 900; margin: 25px 0 12px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }

        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 18px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; position: relative; transition: 0.3s; }
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px; }
        .team img { width: 35px; height: 35px; object-fit: contain; filter: drop-shadow(0 0 5px rgba(255,255,255,0.2)); }
        .team b { font-size: 11px; color: #eee; line-height: 1.2; }

        .info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 5px; }
        .score { font-size: 24px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; background: rgba(56, 189, 248, 0.1); padding: 2px 10px; border-radius: 50px; display: inline-block; margin: 0 auto; }
        .live-tag { font-size: 10px; background: #e11d48; color: #fff; padding: 2px 10px; border-radius: 6px; animation: pulse 1s infinite; font-weight: 900; width: fit-content; margin: 0 auto; }
        @keyframes pulse { 50% { opacity: 0.5; } }
        
        .empty { text-align: center; padding: 80px 20px; opacity: 0.4; font-size: 14px; border: 1px dashed var(--border); border-radius: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-nav">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <h3><?= $display_date ?></h3>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php if(empty($stages)): ?>
        <div class="empty">لا توجد مباريات متاحة لهذا اليوم حالياً</div>
    <?php else: ?>
        <?php foreach($stages as $stage): ?>
            <div class="league-header">
                <i class="fas fa-trophy"></i> <?= $stage['Sn'] ?> </div>
            
            <?php foreach($stage['Events'] as $m): 
                $status = $m['Eps']; // الحالة (FT, NS, Live)
                $homeTeam = $m['T1'][0]['Nm'];
                $awayTeam = $m['T2'][0]['Nm'];
                $homeScore = $m['Tr1'] ?? 0;
                $awayScore = $m['Tr2'] ?? 0;
                $mTime = date("H:i", strtotime($m['Esd']));
            ?>
            <div class="match-card">
                <div class="team">
                    <img src="https://api.sofascore.app/api/v1/team/<?= $m['T1'][0]['ID'] ?>/image" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $homeTeam ?></b>
                </div>

                <div class="info">
                    <?php if($m['Esid'] == 2): // مباشر عثمان ?>
                        <div class="score" style="color:var(--main)"><?= $homeScore ?> - <?= $awayScore ?></div>
                        <div class="live-tag">مباشر <?= $m['Ep'] ?>'</div>
                    <?php elseif($m['Esid'] == 3): // منتهية عثمان ?>
                        <div class="score"><?= $homeScore ?> - <?= $awayScore ?></div>
                        <div style="font-size: 9px; opacity: 0.5;">انتهت</div>
                    <?php else: ?>
                        <div style="font-size: 12px; font-weight: 900; opacity: 0.3;">VS</div>
                        <div class="time"><?= $mTime ?></div>
                    <?php endif; ?>
                </div>

                <div class="team">
                    <img src="https://api.sofascore.app/api/v1/team/<?= $m['T2'][0]['ID'] ?>/image" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $awayTeam ?></b>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
