<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// إعدادات التاريخ عثمان
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
$display_date = date('d / m / Y', strtotime($date_get));

// دالة جلب البيانات من المصدر المفتوح عثمان
function fetchMatches($date) {
    // نستخدم رابط مباشر يعطي بيانات بصيغة JSON لضمان السرعة عثمان
    $url = "https://ls.sport-mobi.com/api/v2/matches?date=" . $date . "&timezone=3";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $res = curl_exec($ch);
    curl_close($ch);
    $all = json_decode($res, true);
    return $all['data'] ?: [];
}

$raw_data = fetchMatches($date_get);

// ترتيب المباريات حسب البطولة
$leagues = [];
foreach ($raw_data as $m) {
    $lname = $m['league']['name_ar'] ?: $m['league']['name'];
    $leagues[$lname][] = $m;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>جدول مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; padding: 10px; background-color: var(--bg); font-family: 'Tajawal', sans-serif; color: #fff; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; min-height: 100vh; }

        /* الأسهم عثمان */
        .date-picker { display: flex; align-items: center; justify-content: space-between; background: var(--glass); border: 1px solid var(--border); padding: 15px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(10px); }
        .date-picker a { color: #fff; text-decoration: none; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background: var(--main); border-radius: 50%; transition: 0.3s; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.3); }
        .date-picker h3 { margin: 0; font-size: 16px; font-weight: 900; }

        /* الدوري عثمان */
        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 25px 0 10px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 8px; }

        /* كرت المباراة عثمان */
        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 18px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; position: relative; backdrop-filter: blur(5px); }
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 8px; }
        .team img { width: 38px; height: 38px; object-fit: contain; }
        .team b { font-size: 11px; color: #eee; line-height: 1.2; }

        .score-info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 5px; }
        .score { font-size: 24px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .m-time { font-size: 11px; color: #38bdf8; font-weight: bold; background: rgba(56, 189, 248, 0.1); padding: 2px 8px; border-radius: 50px; display: inline-block; margin: 0 auto; }
        .live-tag { font-size: 9px; background: #e11d48; padding: 2px 8px; border-radius: 5px; animation: blink 1s infinite; font-weight: 900; width: fit-content; margin: 0 auto; }
        @keyframes blink { 50% { opacity: 0.5; } }
        
        .empty { text-align: center; padding: 80px 20px; opacity: 0.4; border: 1px dashed var(--border); border-radius: 20px; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-picker">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <h3><?= $display_date ?></h3>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php if(empty($leagues)): ?>
        <div class="empty">
            <i class="fas fa-calendar-times" style="font-size: 40px; display: block; margin-bottom: 15px;"></i>
            لا توجد مباريات مجدولة لهذا التاريخ حالياً
        </div>
    <?php else: ?>
        <?php foreach($leagues as $name => $matches): ?>
            <div class="league-title"><i class="fas fa-trophy"></i> <?= $name ?></div>
            <?php foreach($matches as $m): 
                $isLive = $m['status']['type'] == 'live';
                $isFinished = $m['status']['type'] == 'finished';
                $homeName = $m['home_team']['name_ar'] ?: $m['home_team']['name'];
                $awayName = $m['away_team']['name_ar'] ?: $m['away_team']['name'];
                $time = date("H:i", $m['start_at']);
            ?>
            <div class="match-card">
                <div class="team">
                    <img src="<?= $m['home_team']['logo'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $homeName ?></b>
                </div>
                <div class="score-info">
                    <?php if($isLive): ?>
                        <div class="score" style="color:var(--main)"><?= $m['home_score'] ?> - <?= $m['away_score'] ?></div>
                        <div class="live-tag">مباشر</div>
                    <?php elseif($isFinished): ?>
                        <div class="score"><?= $m['home_score'] ?> - <?= $m['away_score'] ?></div>
                        <div style="font-size:9px; opacity:0.5;">انتهت</div>
                    <?php else: ?>
                        <div style="font-size:12px; font-weight:900; opacity:0.3;">VS</div>
                        <div class="m-time"><?= $time ?></div>
                    <?php endif; ?>
                </div>
                <div class="team">
                    <img src="<?= $m['away_team']['logo'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $awayName ?></b>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">الخدمة الرقمية © 2026 - تحديث تلقائي</footer>
</div>

</body>
</html>
