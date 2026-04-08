<?php
// إظهار الأخطاء للمساعدة في معرفة السبب عثمان
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Riyadh');

// مفتاح الـ API الجديد الخاص بك (Sportmonks) عثمان
$API_TOKEN = 'OtBW4dzy796sKPRL4ctw4eG6U5UZX3rsd5Ial3gRSuW4vvHtYrm23ZK2Dfiv'; 

// جلب التاريخ من الرابط أو استخدام تاريخ اليوم
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));

// إعدادات الدوريات (IDs الخاصة بـ Sportmonks تختلف عن API-Sports) عثمان
// ملاحظة: تأكد من أن باقتك تدعم هذه الدوريات
$league_settings = array(
    501  => array('name' => 'الدوري السعودي', 'ch_name' => 'SSC'),
    2    => array('name' => 'دوري أبطال أوروبا', 'ch_name' => 'beIN Sports'),
    5    => array('name' => 'الدوري الأوروبي', 'ch_name' => 'beIN Sports'),
    8    => array('name' => 'الدوري الإنجليزي', 'ch_name' => 'beIN Premium'),
    564  => array('name' => 'الدوري الإسباني', 'ch_name' => 'beIN Sports'),
    384  => array('name' => 'الدوري الإيطالي', 'ch_name' => 'AD Sports'),
    82   => array('name' => 'الدوري الألماني', 'ch_name' => 'beIN Sports')
);

// دالة جلب البيانات من Sportmonks عثمان
function getSportmonksFixtures($date, $token) {
    $url = "https://api.sportmonks.com/v3/football/fixtures/date/$date?api_token=$token&include=league;participants;scores";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    if ($err) return array();
    
    $data = json_decode($response, true);
    return (isset($data['data'])) ? $data['data'] : array();
}

$fixtures = getSportmonksFixtures($date_get, $API_TOKEN);

$ordered_matches = array();
if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $league_id = (int)$f['league_id'];
        if (isset($league_settings[$league_id])) {
            $ordered_matches[$league_id][] = $f;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>مباريات اليوم - <?= $date_get ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --card: rgba(255, 255, 255, 0.05); }
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; margin: 0; padding: 10px; }
        .container { max-width: 480px; margin: auto; }
        .nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: var(--card); padding: 12px; border-radius: 18px; border: 1px solid rgba(255,255,255,0.1); }
        .nav a { color: #fff; background: var(--main); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; transition: 0.3s; }
        .league-row { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 10px; margin: 25px 0 10px; font-weight: 900; font-size: 13px; border-right: 4px solid #fff; }
        .match { background: var(--card); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 15px; margin-bottom: 15px; }
        .match-top { display: flex; align-items: center; justify-content: space-between; padding-bottom: 10px; }
        .team { flex: 1.2; text-align: center; font-size: 11px; }
        .team img { width: 38px; height: 38px; display: block; margin: 0 auto 8px; object-fit: contain; }
        .score { font-size: 24px; font-weight: 900; letter-spacing: 2px; }
        .match-bottom { border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 10px; display: flex; justify-content: center; }
        .ch-item { display: flex; align-items: center; gap: 5px; background: rgba(56,189,248,0.1); padding: 4px 15px; border-radius: 50px; font-size: 11px; font-weight: bold; color: #38bdf8; }
        .live { color: #22c55e; font-size: 9px; animation: blink 1s infinite; font-weight: 900; }
        @keyframes blink { 50% { opacity: 0.5; } }
        .no-matches { text-align: center; padding: 40px; opacity: 0.5; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <span style="font-weight:900;"><?= date('Y / m / d', strtotime($date_get)) ?></span>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php if (empty($ordered_matches)): ?>
        <div class="no-matches">
            <i class="fas fa-info-circle"></i><br>
            لا توجد مباريات هامة في Sportmonks اليوم <br>
            <small>(تأكد من تفعيل الدوريات في حسابك)</small>
        </div>
    <?php else:
        foreach($league_settings as $id => $setting): 
            if(isset($ordered_matches[$id])):
                $ch_counter = 1; 
        ?>
            <div class="league-row"><?= $setting['name'] ?></div>
            <?php foreach($ordered_matches[$id] as $m): 
                // استخراج الفرق (في Sportmonks تأتي في مصفوفة participants)
                $home = $m['participants'][0]['meta']['location'] == 'home' ? $m['participants'][0] : $m['participants'][1];
                $away = $m['participants'][1]['meta']['location'] == 'away' ? $m['participants'][1] : $m['participants'][0];
                
                // الوقت والحالة عثمان
                $mTime = date("H:i", $m['starting_at_timestamp']);
                $state = $m['state_id']; // 1: لم تبدأ، 3: مباشرة، 5: انتهت (حسب توثيق Sportmonks)
                
                // الأهداف
                $home_score = 0; $away_score = 0;
                foreach($m['scores'] as $s) {
                    if($s['description'] == 'CURRENT') {
                        if($s['participant_id'] == $home['id']) $home_score = $s['score']['value'];
                        if($s['participant_id'] == $away['id']) $away_score = $s['score']['value'];
                    }
                }

                $current_ch = $setting['ch_name'] . " " . $ch_counter;
                $ch_counter++; 
            ?>
            <div class="match">
                <div class="match-top">
                    <div class="team">
                        <img src="<?= $home['image_path'] ?>">
                        <b><?= $home['name'] ?></b>
                    </div>
                    <div style="flex:1; text-align:center;">
                        <?php if($state == 3): // مباشرة ?>
                            <div class="score" style="color:var(--main)"><?= $home_score ?> - <?= $away_score ?></div>
                            <div class="live">مباشر</div>
                        <?php elseif($state == 5): // انتهت ?>
                            <div class="score"><?= $home_score ?> - <?= $away_score ?></div>
                            <div style="font-size:9px; opacity:0.5;">انتهت</div>
                        <?php else: // لم تبدأ ?>
                            <div style="font-size:18px; font-weight:900;"><?= $mTime ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="team">
                        <img src="<?= $away['image_path'] ?>">
                        <b><?= $away['name'] ?></b>
                    </div>
                </div>
                <div class="match-bottom">
                    <div class="ch-item"><i class="fas fa-tv"></i> <?= $current_ch ?></div>
                </div>
            </div>
    <?php endforeach; endif; endforeach; endif; ?>
</div>
</body>
</html>
