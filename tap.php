<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// إعدادات التاريخ عثمان
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
$display_date = date('Y / m / d', strtotime($date_get));

// دالة جلب البيانات من المصدر المفتوح والمستقر عثمان
function fetchGlobalMatches($date) {
    // نستخدم مصدر بيانات مفتوح يدعم كل البطولات العربية والعالمية عثمان
    $url = "https://ls.sport-mobi.com/api/v2/matches?date=" . $date . "&timezone=3";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    return $data['data'] ?: [];
}

$raw_matches = fetchGlobalMatches($date_get);

// ترتيب المباريات حسب البطولة عثمان
$leagues = [];
foreach ($raw_matches as $m) {
    $league_name = $m['league']['name_ar'] ?: $m['league']['name'];
    $leagues[$league_name][] = $m;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة المباريات - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; padding: 10px; background-color: var(--bg); font-family: 'Tajawal', sans-serif; color: #fff; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; min-height: 100vh; }

        /* الأسهم عثمان */
        .date-nav { display: flex; align-items: center; justify-content: space-between; background: var(--glass); border: 1px solid var(--border); padding: 12px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(10px); }
        .date-nav a { width: 35px; height: 35px; background: var(--main); color: #fff; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; transition: 0.3s; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.3); }
        .date-nav h3 { margin: 0; font-size: 15px; font-weight: 900; }

        /* الدوريات عثمان */
        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 25px 0 10px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 8px; }

        /* كرت المباراة عثمان */
        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 20px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; backdrop-filter: blur(5px); }
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team b { font-size: 10px; color: #eee; line-height: 1.2; }

        .info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 5px; }
        .score { font-size: 24px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .m-time { font-size: 11px; color: #38bdf8; font-weight: bold; background: rgba(56, 189, 248, 0.1); padding: 2px 10px; border-radius: 50px; display: inline-block; margin: 0 auto; }
        .live-tag { font-size: 9px; background: #e11d48; color: #fff; padding: 2px 8px; border-radius: 5px; animation: blink 1s infinite; font-weight: 900; width: fit-content; margin: 0 auto; }
        @keyframes blink { 50% { opacity: 0.5; } }
        
        .empty { text-align: center; padding: 80px 20px; opacity: 0.4; border: 1px dashed var(--border); border-radius: 20px; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-nav">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <h3><?= $display_date ?></h3>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php if(empty($leagues)): ?>
        <div class="empty">لا توجد مباريات متاحة حالياً.</div>
    <?php else: ?>
        <?php foreach($leagues as $name => $matches): ?>
            <div class="league-title"><i class="fas fa-futbol"></i> <?= $name ?></div>
            <?php foreach($matches as $m): 
                $status = $m['status']['type'];
                $hName = $m['home_team']['name_ar'] ?: $m['home_team']['name'];
                $aName = $m['away_team']['name_ar'] ?: $m['away_team']['name'];
                $startTime = date("H:i", $m['start_at']);
            ?>
            <div class="match-card">
                <div class="team">
                    <img src="<?= $m['home_team']['logo'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $hName ?></b>
                </div>
                <div class="info">
                    <?php if($status == 'live'): ?>
                        <div class="score" style="color:var(--main)"><?= $m['home_score'] ?> - <?= $m['away_score'] ?></div>
                        <div class="live-tag">مباشر</div>
                    <?php elseif($status == 'finished'): ?>
                        <div class="score"><?= $m['home_score'] ?> - <?= $m['away_score'] ?></div>
                        <div style="font-size:9px; opacity:0.5;">انتهت</div>
                    <?php else: ?>
                        <div style="font-size:12px; font-weight:900; opacity:0.2;">VS</div>
                        <div class="m-time"><?= $startTime ?></div>
                    <?php endif; ?>
                </div>
                <div class="team">
                    <img src="<?= $m['away_team']['logo'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $aName ?></b>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
