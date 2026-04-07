<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// إعداد التاريخ عثمان
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
$display_date = date('d / m / Y', strtotime($date_get));

// دالة جلب البيانات بنظام السحب المباشر عثمان
function getMatchesData($date) {
    // نستخدم مصدر بيانات مفتوح وموثوق جداً عثمان
    $url = "https://web-api.livescore.com/wrapper/api/w/en/matches/soccer/" . $date . "?timezone=3";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $res = json_decode($response, true);
    return $res['Stages'] ?: [];
}

$stages = getMatchesData($date_get);
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
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; margin: 0; padding: 10px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 480px; }

        .date-picker { display: flex; align-items: center; justify-content: space-between; background: var(--glass); border: 1px solid var(--border); padding: 12px; border-radius: 20px; margin-bottom: 20px; backdrop-filter: blur(10px); }
        .date-picker a { color: #fff; text-decoration: none; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background: var(--main); border-radius: 50%; transition: 0.3s; }
        .current-date { text-align: center; }
        .current-date h3 { margin: 0; font-size: 15px; font-weight: 900; color: var(--main); }
        .current-date span { font-size: 10px; opacity: 0.6; }

        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 8px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 25px 0 10px; border-right: 4px solid #fff; display: flex; align-items: center; gap: 8px; }

        .match-card { background: var(--glass); border: 1px solid var(--border); border-radius: 18px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; transition: 0.3s; }
        
        .team { flex: 1.2; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 6px; }
        .team img { width: 32px; height: 32px; object-fit: contain; }
        .team b { font-size: 10px; color: #eee; }

        .info { flex: 1; text-align: center; display: flex; flex-direction: column; gap: 4px; }
        .score { font-size: 22px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .time { font-size: 11px; color: #38bdf8; font-weight: bold; }
        .live-tag { font-size: 9px; background: #e11d48; padding: 2px 8px; border-radius: 5px; animation: blink 1.2s infinite; width: fit-content; margin: 0 auto; }
        
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

    <?php if(empty($stages)): ?>
        <div class="empty">لا توجد مباريات جارية لهذا اليوم..</div>
    <?php else: ?>
        <?php foreach($stages as $stage): ?>
            <div class="league-title">
                <i class="fas fa-trophy"></i> <?= $stage['Sn'] ?> </div>
            
            <?php foreach($stage['Events'] as $m): 
                $mStatus = $m['Eps']; // حالة المباراة (Live, NS, FT)
                $score = $m['Tr1'] . " - " . $m['Tr2'];
                $startTime = substr($m['Esd'], 8, 2) . ":" . substr($m['Esd'], 10, 2);
            ?>
            <div class="match-card">
                <div class="team">
                    <img src="https://api.sofascore.app/api/v1/team/<?= $m['T1'][0]['ID'] ?>/image" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $m['T1'][0]['Nm'] ?></b>
                </div>

                <div class="info">
                    <?php if($m['Esid'] == 2): // حالة المباشر عثمان ?>
                        <div class="score" style="color:var(--main);"><?= ($m['Tr1'] ?: 0) ?> - <?= ($m['Tr2'] ?: 0) ?></div>
                        <div class="live-tag">مباشر <?= $m['Ep'] ?>'</div>
                    <?php elseif($m['Esid'] == 3): // منتهية ?>
                        <div class="score"><?= $m['Tr1'] ?> - <?= $m['Tr2'] ?></div>
                        <div style="font-size:9px; opacity:0.5;">انتهت</div>
                    <?php else: ?>
                        <div style="font-size:12px; font-weight:900; opacity:0.3;">VS</div>
                        <div class="time"><?= date("H:i", strtotime($m['Esd'])) ?></div>
                    <?php endif; ?>
                </div>

                <div class="team">
                    <img src="https://api.sofascore.app/api/v1/team/<?= $m['T2'][0]['ID'] ?>/image" onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53251.png'">
                    <b><?= $m['T2'][0]['Nm'] ?></b>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <footer style="text-align:center; padding:30px; font-size:10px; opacity:0.3;">تحديث لحظي - الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
