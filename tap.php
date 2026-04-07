<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// تحديد التاريخ المختار من الأسهم عثمان
$date = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date .' -1 day'));
$next_date = date('Y-m-d', strtotime($date .' +1 day'));

// عرض التاريخ بشكل مقروء عثمان
$display_date = date('d / m / Y', strtotime($date));

// مصفوفة تعريب المسميات
$translate = [
    'Premier League' => 'الدوري الإنجليزي',
    'La Liga' => 'الدوري الإسباني',
    'Saudi Pro League' => 'الدوري السعودي',
    'Egyptian Premier League' => 'الدوري المصري',
    'Champions League' => 'دوري أبطال أوروبا',
    'IN_PLAY' => 'مباشر',
    'FINISHED' => 'انتهت',
    'TIMED' => 'انتظار'
];

// دالة جلب البيانات (محدثة لدعم التاريخ) عثمان
function getFootballData($targetDate) {
    $token = '273aaeb61360452588653ffea820cc19';
    // ملاحظة: الدوريات العربية تتطلب اشتراك مدفوع في هذا المصدر، 
    // ولكن قمنا بتهيئة الكود لاستقبالها فور توفرها
    $url = "https://api.football-data.org/v4/matches?dateFrom=$targetDate&dateTo=$targetDate";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Auth-Token: $token"]);
    $res = json_decode(curl_exec($ch), true);
    return $res['matches'] ?: [];
}

$matches = getFootballData($date);

$grouped = [];
foreach ($matches as $m) {
    $league = $m['competition']['name'];
    $arLeague = isset($translate[$league]) ? $translate[$league] : $league;
    $grouped[$arLeague][] = $m;
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

        /* نظام الأسهم والتاريخ عثمان */
        .date-picker { 
            display: flex; align-items: center; justify-content: space-between; 
            background: var(--glass); border: 1px solid var(--border); 
            padding: 15px; border-radius: 20px; margin-bottom: 20px;
            backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .date-picker a { color: #fff; text-decoration: none; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--main); border-radius: 50%; transition: 0.3s; }
        .date-picker a:hover { transform: scale(1.1); box-shadow: 0 0 15px var(--main); }
        .current-date { text-align: center; }
        .current-date h3 { margin: 0; font-size: 16px; font-weight: 900; color: var(--main); }
        .current-date span { font-size: 11px; opacity: 0.6; }

        .league-header { background: linear-gradient(90deg, var(--main), transparent); padding: 8px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 20px 0 10px; border-right: 4px solid #fff; }

        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 18px; padding: 12px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; position: relative; }
        
        .team { flex: 1; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 5px; }
        .team img { width: 32px; height: 32px; object-fit: contain; }
        .team b { font-size: 10px; color: #eee; }

        .info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 3px; }
        .score { font-size: 20px; font-weight: 900; letter-spacing: 2px; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; }
        .live-tag { font-size: 9px; background: #e11d48; padding: 2px 8px; border-radius: 4px; animation: pulse 1s infinite; }

        @keyframes pulse { 50% { opacity: 0.5; } }
        .empty { text-align: center; padding: 40px; opacity: 0.4; font-size: 13px; border: 1px dashed var(--border); border-radius: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-picker">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <div class="current-date">
            <span>تاريخ المباريات</span>
            <h3><?= $display_date ?></h3>
        </div>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php if(empty($grouped)): ?>
        <div class="empty">لا توجد مباريات مجدولة لهذا التاريخ</div>
    <?php else: ?>
        <?php foreach($grouped as $league => $matchList): ?>
            <div class="league-header"><?= $league ?></div>
            <?php foreach($matchList as $match): 
                $mTime = date("H:i", strtotime($match['utcDate'] . " +3 hours"));
                $status = $match['status'];
            ?>
            <div class="match-card">
                <div class="team">
                    <img src="<?= $match['homeTeam']['crest'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $match['homeTeam']['name'] ?></b>
                </div>
                <div class="info">
                    <?php if($status == 'IN_PLAY'): ?>
                        <div class="score" style="color:var(--main)"><?= $match['score']['fullTime']['home'] ?> - <?= $match['score']['fullTime']['away'] ?></div>
                        <div class="live-tag">مباشر</div>
                    <?php elseif($status == 'FINISHED'): ?>
                        <div class="score"><?= $match['score']['fullTime']['home'] ?> - <?= $match['score']['fullTime']['away'] ?></div>
                        <div style="font-size:9px; opacity:0.5;">انتهت</div>
                    <?php else: ?>
                        <div style="font-size:12px; font-weight:900; opacity:0.3;">VS</div>
                        <div class="time"><?= $mTime ?></div>
                    <?php endif; ?>
                </div>
                <div class="team">
                    <img src="<?= $match['awayTeam']['crest'] ?>" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $match['awayTeam']['name'] ?></b>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
