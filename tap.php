<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// إعداد التاريخ عثمان
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
$display_date = date('d / m / Y', strtotime($date_get));

// دالة جلب البيانات من المصدر الرياضي المباشر عثمان
function fetchMatches($targetDate) {
    // هذا الرابط مفتوح وسريع جداً عثمان
    $url = "https://livescore-api.com/api-client/scores/history.json?key=52mS6A6I0S1m8t6c&date=" . $targetDate;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $res = curl_exec($ch);
    curl_close($ch);
    
    $all_data = json_decode($res, true);
    return $all_data['data']['match'] ?: [];
}

$raw_matches = fetchMatches($date_get);

// ترتيب المباريات حسب البطولة عثمان
$leagues = [];
foreach ($raw_matches as $m) {
    $league_name = $m['competition_name'];
    $leagues[$league_name][] = $m;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول المباريات - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); }
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; margin: 0; padding: 10px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; }

        .date-picker { display: flex; align-items: center; justify-content: space-between; background: var(--glass); border: 1px solid var(--border); padding: 12px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(10px); }
        .date-picker a { color: #fff; text-decoration: none; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background: var(--main); border-radius: 50%; transition: 0.3s; }
        .current-date { text-align: center; }
        .current-date h3 { margin: 0; font-size: 15px; font-weight: 900; color: var(--main); }
        .current-date span { font-size: 10px; opacity: 0.6; }

        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 25px 0 10px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 8px; }

        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 18px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 6px; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team b { font-size: 10px; color: #eee; }

        .info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 4px; }
        .score { font-size: 24px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; }
        .live-tag { font-size: 9px; background: #e11d48; padding: 2px 8px; border-radius: 50px; animation: blink 1.2s infinite; width: fit-content; margin: 0 auto; }
        
        @keyframes blink { 50% { opacity: 0.4; } }
        .empty { text-align: center; padding: 60px; opacity: 0.4; font-size: 14px; border: 1px dashed var(--border); border-radius: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-picker">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <div class="current-date">
            <span>جدول مباريات</span>
            <h3><?= $display_date ?></h3>
        </div>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php if(empty($leagues)): ?>
        <div class="empty">لا توجد مباريات متاحة لهذا التاريخ حالياً</div>
    <?php else: ?>
        <?php foreach($leagues as $name => $matchList): ?>
            <div class="league-title">
                <i class="fas fa-futbol"></i> <?= $name ?>
            </div>
            
            <?php foreach($matchList as $m): 
                $status = $m['status']; // IN PLAY, FINISHED, الخ
                $score = $m['ft_score'] ?: ($m['score'] ?: 'VS');
                $mTime = substr($m['scheduled'], 0, 5); // وقت المباراة عثمان
            ?>
            <div class="match-card">
                <div class="team">
                    <img src="https://ls.sport-mobi.com/api/v2/team/logo/<?= $m['home_id'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $m['home_name'] ?></b>
                </div>

                <div class="info">
                    <?php if($status == 'IN PLAY'): ?>
                        <div class="score" style="color:var(--main);"><?= $score ?></div>
                        <div class="live-tag">مباشر</div>
                    <?php elseif($status == 'FINISHED'): ?>
                        <div class="score"><?= $score ?></div>
                        <div style="font-size:9px; opacity:0.5;">انتهت</div>
                    <?php else: ?>
                        <div style="font-size:12px; font-weight:900; opacity:0.3;">VS</div>
                        <div class="time"><?= $mTime ?></div>
                    <?php endif; ?>
                </div>

                <div class="team">
                    <img src="https://ls.sport-mobi.com/api/v2/team/logo/<?= $m['away_id'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $m['away_name'] ?></b>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">تحديث تلقائي - الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
