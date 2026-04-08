<?php
// إظهار الأخطاء للمساعدة في التطوير - عثمان
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Riyadh');

// مفتاح الـ API الخاص بك (Sportmonks V3)
$API_TOKEN = 'OtBW4dzy796sKPRL4ctw4eG6U5UZX3rsd5Ial3gRSuW4vvHtYrm23ZK2Dfiv'; 

// جلب التاريخ
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));

// إعدادات الدوريات المشهورة (تأكد من تفعيلها في حسابك)
$league_settings = array(
    8    => array('name' => 'الدوري الإنجليزي', 'ch_name' => 'beIN Premium'),
    564  => array('name' => 'الدوري الإسباني', 'ch_name' => 'beIN Sports'),
    384  => array('name' => 'الدوري الإيطالي', 'ch_name' => 'AD Sports'),
    82   => array('name' => 'الدوري الألماني', 'ch_name' => 'beIN Sports'),
    501  => array('name' => 'الدوري السعودي', 'ch_name' => 'SSC'),
    2    => array('name' => 'دوري أبطال أوروبا', 'ch_name' => 'beIN Sports'),
    5    => array('name' => 'الدوري الأوروبي', 'ch_name' => 'beIN Sports')
);

function getSportmonksFixtures($date, $token) {
    // نطلب البيانات مع تضمين الدوريات والفرق والنتائج
    $url = "https://api.sportmonks.com/v3/football/fixtures/date/$date?api_token=$token&include=league;participants;scores";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    if ($err) return array();
    
    $data = json_decode($response, true);
    return (isset($data['data'])) ? $data['data'] : array();
}

$fixtures = getSportmonksFixtures($date_get, $API_TOKEN);

// ترتيب المباريات حسب الدوريات
$ordered_matches = array();
$other_leagues = array(); // لتخزين الدوريات غير المعرفة عندنا عثمان

if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $league_id = (int)$f['league_id'];
        $league_name = isset($f['league']['name']) ? $f['league']['name'] : 'دوري غير معروف';
        
        if (isset($league_settings[$league_id])) {
            $ordered_matches[$league_id][] = $f;
        } else {
            // إذا كان الدوري غير موجود في القائمة، نضعه في "أخرى"
            $other_leagues[$league_id]['name'] = $league_name;
            $other_leagues[$league_id]['matches'][] = $f;
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
        .nav a { color: #fff; background: var(--main); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; }
        .league-row { background: linear-gradient(90deg, var(--main), transparent); padding: 10px 15px; border-radius: 10px; margin: 25px 0 10px; font-weight: 900; font-size: 13px; border-right: 4px solid #fff; }
        .match { background: var(--card); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 15px; margin-bottom: 15px; }
        .match-top { display: flex; align-items: center; justify-content: space-between; }
        .team { flex: 1.2; text-align: center; font-size: 11px; }
        .team img { width: 35px; height: 35px; display: block; margin: 0 auto 8px; object-fit: contain; }
        .score-box { flex: 1; text-align: center; }
        .score { font-size: 22px; font-weight: 900; }
        .time { font-size: 16px; font-weight: 700; color: #aaa; }
        .match-bottom { border-top: 1px dashed rgba(255,255,255,0.1); margin-top: 10px; padding-top: 10px; text-align: center; }
        .ch-item { display: inline-block; background: rgba(56,189,248,0.1); padding: 3px 12px; border-radius: 50px; font-size: 10px; color: #38bdf8; }
        .live-tag { color: #22c55e; font-size: 9px; animation: blink 1s infinite; font-weight: bold; }
        @keyframes blink { 50% { opacity: 0.5; } }
        .no-matches { text-align: center; padding: 50px 20px; opacity: 0.6; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <span style="font-weight:900;"><?= date('d / m / Y', strtotime($date_get)) ?></span>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <?php 
    // إذا لم تكن هناك مباريات في الدوريات المفضلة، سأعرض الدوريات المتاحة الأخرى عثمان
    $final_list = !empty($ordered_matches) ? $ordered_matches : $other_leagues;

    if (empty($fixtures)): ?>
        <div class="no-matches">
            <i class="fas fa-search" style="font-size: 30px; margin-bottom: 10px;"></i><br>
            لا توجد مباريات مسجلة لهذا اليوم في حسابك.
        </div>
    <?php else: 
        foreach($final_list as $id => $data): 
            $league_name = isset($league_settings[$id]) ? $league_settings[$id]['name'] : (isset($data['name']) ? $data['name'] : 'دوري غير معروف');
            $matches = isset($data['matches']) ? $data['matches'] : $data;
    ?>
        <div class="league-row"><?= $league_name ?> (ID: <?= $id ?>)</div>
        
        <?php foreach($matches as $m): 
            $home = $m['participants'][0];
            $away = $m['participants'][1];
            $mTime = date("H:i", $m['starting_at_timestamp']);
            
            // جلب الأهداف
            $home_score = 0; $away_score = 0;
            if(isset($m['scores'])) {
                foreach($m['scores'] as $s) {
                    if($s['description'] == 'CURRENT') {
                        if($s['participant_id'] == $home['id']) $home_score = $s['score']['value'];
                        if($s['participant_id'] == $away['id']) $away_score = $s['score']['value'];
                    }
                }
            }
            
            // الحالة (3 تعني مباشر) عثمان
            $is_live = ($m['state_id'] == 3);
            $is_finished = ($m['state_id'] == 5);
        ?>
        <div class="match">
            <div class="match-top">
                <div class="team">
                    <img src="<?= $home['image_path'] ?>">
                    <b><?= $home['name'] ?></b>
                </div>
                
                <div class="score-box">
                    <?php if($is_live): ?>
                        <div class="score" style="color:var(--main)"><?= $home_score ?> - <?= $away_score ?></div>
                        <div class="live-tag">مباشر الآن</div>
                    <?php elseif($is_finished): ?>
                        <div class="score"><?= $home_score ?> - <?= $away_score ?></div>
                        <div style="font-size:10px; color:#666;">انتهت</div>
                    <?php else: ?>
                        <div class="time"><?= $mTime ?></div>
                    <?php endif; ?>
                </div>

                <div class="team">
                    <img src="<?= $away['image_path'] ?>">
                    <b><?= $away['name'] ?></b>
                </div>
            </div>
            <div class="match-bottom">
                <div class="ch-item">
                    <i class="fas fa-tv"></i> 
                    <?= isset($league_settings[$id]) ? $league_settings[$id]['ch_name'] : 'قناة غير محددة' ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endforeach; endif; ?>
</div>
</body>
</html>
