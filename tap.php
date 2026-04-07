<?php
// إظهار الأخطاء للمساعدة في معرفة السبب عثمان
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Riyadh');

// مفتاحك الحالي - إذا استمرت المشكلة جرب مفتاح جديد عثمان
$API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97'; 

$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));

$league_settings = [
    307 => ['name' => 'الدوري السعودي', 'ch_name' => 'ثمانية'],
    2   => ['name' => 'دوري أبطال أوروبا', 'ch_name' => 'beIN Sports'],
    3   => ['name' => 'الدوري الأوروبي', 'ch_name' => 'beIN Sports'],
    5   => ['name' => 'دوري أبطال آسيا', 'ch_name' => 'beIN AFC'],
    39  => ['name' => 'الدوري الإنجليزي', 'ch_name' => 'beIN Premium'],
    140 => ['name' => 'الدوري الإسباني', 'ch_name' => 'beIN Sports'],
    135 => ['name' => 'الدوري الإيطالي', 'ch_name' => 'AD Sports'],
    78  => ['name' => 'الدوري الألماني', 'ch_name' => 'beIN Sports'],
    61  => ['name' => 'الدوري الفرنسي', 'ch_name' => 'beIN Sports']
];

function translateText($text) {
    if(empty($text)) return $text;
    // إضافة timeout للترجمة لكي لا يعلق الموقع عثمان
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $ctx = stream_context_create(['http'=> ['timeout' => 2]]); 
    $res = @file_get_contents($url, false, $ctx);
    if($res){
        $res = json_decode($res, true);
        return $res[0][0][0] ?: $text;
    }
    return $text;
}

function getFixtures($date, $key) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["x-apisports-key: $key"],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) return [];
    
    $data = json_decode($response, true);
    return (isset($data['response'])) ? $data['response'] : [];
}

$fixtures = getFixtures($date_get, $API_KEY);

$ordered_matches = [];
if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $id = (int)$f['league']['id'];
        if (isset($league_settings[$id])) {
            $ordered_matches[$id][] = $f;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>مباريات اليوم</title>
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

    <?php 
    if (empty($ordered_matches)): ?>
        <div class="no-matches">
            <i class="fas fa-info-circle"></i><br>
            لا توجد مباريات هامة لهذا اليوم <br>
            <small>(أو استنفدت حد طلبات الـ API)</small>
        </div>
    <?php else:
        foreach($league_settings as $id => $setting): 
            if(isset($ordered_matches[$id])):
                $ch_counter = 1; 
        ?>
            <div class="league-row"><?= $setting['name'] ?></div>
            <?php foreach($ordered_matches[$id] as $m): 
                $status = $m['fixture']['status']['short'];
                $mTime = date("H:i", $m['fixture']['timestamp']);
                $home_ar = translateText($m['teams']['home']['name']);
                $away_ar = translateText($m['teams']['away']['name']);
                $current_ch = $setting['ch_name'] . " " . $ch_counter;
                $ch_counter++; 
            ?>
            <div class="match">
                <div class="match-top">
                    <div class="team">
                        <img src="<?= $m['teams']['home']['logo'] ?>">
                        <b><?= $home_ar ?></b>
                    </div>
                    <div style="flex:1; text-align:center;">
                        <?php if(in_array($status, ['1H','2H','HT','ET','P'])): ?>
                            <div class="score" style="color:var(--main)"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                            <div class="live">مباشر</div>
                        <?php elseif($status == 'FT'): ?>
                            <div class="score"><?= $m['goals']['home'] ?> - <?= $m['goals']['away'] ?></div>
                            <div style="font-size:9px; opacity:0.5;">انتهت</div>
                        <?php else: ?>
                            <div style="font-size:18px; font-weight:900;"><?= $mTime ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="team">
                        <img src="<?= $m['teams']['away']['logo'] ?>">
                        <b><?= $away_ar ?></b>
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
