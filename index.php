<?php
session_start();
error_reporting(0);

// --- إعدادات مباريات اليوم (عثمان) ---
date_default_timezone_set('Asia/Riyadh');
$FOOTBALL_API_KEY = '5dc2fedf076a0c9041ea3515dd83d2fa'; 
$date_get = date('Y-m-d');

// مصفوفة الترجمة الشاملة (عثمان)
$translate = [
    // الحالات
    'NS' => 'لم تبدأ', 'FT' => 'انتهت', '1H' => 'شوط 1', '2H' => 'شوط 2', 
    'HT' => 'بين الشوطين', 'P' => 'ركلات ترجيح', 'PST' => 'مؤجلة', 'CANC' => 'ملغاة',
    'TBD' => 'يحدد لاحقاً', 'ABD' => 'ملغاة', 'LIVE' => 'مباشر',

    // أندية الدوري السعودي
    'Al-Hilal' => 'الهلال', 'Al-Nassr' => 'النصر', 'Al-Ittihad' => 'الاتحاد', 'Al-Ahli' => 'الأهلي',
    'Al-Shabab' => 'الشباب', 'Al-Ettifaq' => 'الاتفاق', 'Al-Fateh' => 'الفتح', 'Al-Taawoun' => 'التعاون',
    'Al-Wehda' => 'الوحـدة', 'Al-Fayha' => 'الفيحاء', 'Abha' => 'أبها', 'Al-Hazem' => 'الحزم',
    'Al-Khaleej' => 'الخليج', 'Al-Okhdood' => 'الأخدود', 'Al-Riyadh' => 'الرياض', 'Al-Tai' => 'الطائي',
    'Damac' => 'ضمك', 'Al-Qadisiyah' => 'القادسية', 'Al-Orobah' => 'العروبة', 'Al-Kholood' => 'الخلود',

    // الدوري الإنجليزي
    'Manchester City' => 'مانشستر سيتي', 'Liverpool' => 'ليفربول', 'Arsenal' => 'أرسنال',
    'Manchester United' => 'مانشستر يونايتد', 'Chelsea' => 'تشيلسي', 'Tottenham' => 'توتنهام',
    'Aston Villa' => 'أستون فيلا', 'Newcastle' => 'نيوكاسل', 'Brighton' => 'برايتون',
    'West Ham' => 'وست هام', 'Everton' => 'إيفرتون', 'Leicester' => 'ليستر سيتي',
    'Brentford' => 'برينتفورد', 'Fulham' => 'فولهام', 'Crystal Palace' => 'كريستال بالاس',
    'Wolves' => 'وولفرهامبتون', 'Nottingham Forest' => 'نوتنجهام فورست', 'Ipswich' => 'إيبسويتش تاون',

    // الدوري الإسباني
    'Real Madrid' => 'ريال مدريد', 'Barcelona' => 'برشلونة', 'Atletico Madrid' => 'أتلتيكو مدريد',
    'Girona' => 'جيرونا', 'Athletic Club' => 'أتلتيك بلباو', 'Real Sociedad' => 'ريال سوسيداد',
    'Real Betis' => 'ريال بيتيس', 'Villarreal' => 'فاريال', 'Sevilla' => 'اشبيلية', 'Valencia' => 'فالنسيا',
    'Celta Vigo' => 'سلتا فيجو', 'Mallorca' => 'مايوركا', 'Osasuna' => 'أوساسونا', 'Getafe' => 'خيتافي',

    // الدوري الإيطالي
    'Inter' => 'إنتر ميلان', 'AC Milan' => 'ميلان', 'Juventus' => 'يوفنتوس', 'Napoli' => 'نابولي',
    'AS Roma' => 'روما', 'Lazio' => 'لاتسيو', 'Atalanta' => 'أتالانتا', 'Fiorentina' => 'فيورنتينا',
    'Torino' => 'تورينو', 'Bologna' => 'بولونيا', 'Udinese' => 'أودينيزي',

    // أندية عالمية أخرى
    'Bayern Munich' => 'بايرن ميونخ', 'Borussia Dortmund' => 'بوروسيا دورتموند', 'Bayer Leverkusen' => 'ليفركوزن',
    'RB Leipzig' => 'لايبزيج', 'Paris Saint Germain' => 'برايتون', 'Marseille' => 'مارسيليا',
    'Lyon' => 'ليون', 'Monaco' => 'موناكو', 'Benfica' => 'بنفيكا', 'Porto' => 'بورتو', 'Sporting CP' => 'سبورتينج لشبونة'
];

$league_settings = array(
    307 => array('name' => 'الدوري السعودي', 'ch_name' => 'SSC'),
    42  => array('name' => 'دوري أبطال أوروبا', 'ch_name' => 'beIN Sports'),
    73  => array('name' => 'الدوري الأوروبي', 'ch_name' => 'beIN Sports'),
    525 => array('name' => 'نخبة آسيا', 'ch_name' => 'beIN AFC'),
    39  => array('name' => 'الدوري الإنجليزي', 'ch_name' => 'beIN Premium'),
    140 => array('name' => 'الدوري الإسباني', 'ch_name' => 'beIN Sports'),
    135 => array('name' => 'الدوري الإيطالي', 'ch_name' => 'AD Sports'),
    78  => array('name' => 'الدوري الألماني', 'ch_name' => 'beIN Sports'),
    61  => array('name' => 'الدوري الفرنسي', 'ch_name' => 'beIN Sports')
);

function getFixturesWithCache($date, $key) {
    $cache_file = "cache_" . $date . ".json";
    $expire_time = 600; 
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $expire_time)) {
        return json_decode(file_get_contents($cache_file), true);
    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date&timezone=Asia/Riyadh",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array("x-apisports-key: $key"),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    $results = (isset($data['response'])) ? $data['response'] : array();
    if (!empty($results)) { file_put_contents($cache_file, json_encode($results)); }
    return $results;
}

$fixtures = getFixturesWithCache($date_get, $FOOTBALL_API_KEY);
$ordered_matches = array();
if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $id = (int)$f['league']['id'];
        if (isset($league_settings[$id])) { $ordered_matches[$id][] = $f; }
    }
}

$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';

$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) { if ($time - $last_time > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY);
$all_channels = isset($cloud['custom_channels']) ? $cloud['custom_channels'] : [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = isset($cloud['news_ticker']) ? $cloud['news_ticker'] : ['text' => '', 'status' => 'hide'];

function filterSection($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); });
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; transition: 0.3s; }

        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; transition: 0.4s ease-in-out; }
        
        @media (orientation: landscape) {
            body { padding-top: 190px; }
            .main-container { max-width: 95% !important; }
            .header-fixed { max-width: 95% !important; }
            .match-grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
            .league-sep { grid-column: span 2; }
            .channel-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
        }

        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .loader-content { display: flex; flex-direction: column; align-items: center; }
        .intro-icon-box { width: 100px; height: 100px; background: var(--main); border-radius: 30%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 50px rgba(225, 29, 72, 0.5); animation: glowPulse 2s infinite ease-in-out; }
        .intro-icon-box i { font-size: 50px; color: white; }
        .intro-title { margin-top: 25px; color: white; font-weight: 900; font-size: 24px; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
        .loading-bar { width: 150px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px; margin-top: 30px; overflow: hidden; position: relative; }
        .loading-bar::after { content: ""; position: absolute; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, var(--main), transparent); animation: loadingMove 1.5s infinite; }
        @keyframes glowPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        @keyframes loadingMove { 100% { left: 100%; } }

        /* نافذة الإعلان المنبثقة */
        .ad-popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); z-index: 8000; display: none; align-items: center; justify-content: center; }
        .ad-popup-content { position: relative; width: 85%; max-width: 320px; animation: popZoom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .ad-popup-image { width: 100%; border-radius: 20px; border: 2px solid var(--glass-border); box-shadow: 0 10px 40px rgba(0,0,0,0.5); display: block; }
        .ad-close-btn { position: absolute; top: -15px; right: -15px; width: 35px; height: 35px; background: var(--main); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; border: 2px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 10; }
        .ad-subscribe-btn { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background: #25d366; color: white; padding: 10px 25px; border-radius: 50px; text-decoration: none; font-weight: 900; font-size: 14px; display: flex; align-items: center; gap: 8px; box-shadow: 0 5px 20px rgba(37, 211, 102, 0.4); border: 2px solid white; white-space: nowrap; }
        @keyframes popZoom { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; transition: 0.4s; }
        
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }

        .news-ticker { background: rgba(225, 29, 72, 0.15); border-top: 1px solid rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.05); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-wrap { flex: 1; overflow: hidden; direction: ltr; display: flex; align-items: center; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 45s linear infinite; width: max-content; }
        .ticker-text { color: #fff; font-size: 12px; font-weight: 700; padding: 0 60px; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; transition: 0.2s; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: translateY(-2px); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }

        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 15px; width: 100%; }
        
        .play-btn { width: 92%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--glass-border); padding: 12px; border-radius: 12px; font-weight: 900; cursor: pointer; font-size: 13px; }
        .play-btn i { font-size: 16px; color: var(--main); }

        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; background-size: cover; background-position: center; }
        .video-box iframe { position: absolute; top:0; left:0; width: 100%; height: 100%; border: none; }

        .match-nav { display: flex; justify-content: center; align-items: center; margin-bottom: 10px; background: var(--glass); padding: 10px; border-radius: 15px; border: 1px solid var(--glass-border); }
        .league-sep { background: rgba(255, 255, 255, 0.07); padding: 10px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; margin: 15px 0 10px; border-right: 4px solid var(--main); color: #fff; }
        .m-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 10px; }
        .m-team-col { flex: 1; font-size: 10px; font-weight: 700; color: #fff; text-align: center; }
        .m-team-col img { width: 28px; height: 28px; display: block; margin: 0 auto 6px; }
        .m-time-box { flex: 0.6; background: rgba(225, 29, 72, 0.1); border: 1px solid rgba(225, 29, 72, 0.2); padding: 6px; border-radius: 10px; text-align: center; font-weight: 900; font-size: 12px; color: var(--main); }

        .dual-container { display: flex; flex-direction: column; gap: 15px; align-items: center; width: 100%; }
        @media (orientation: landscape) { .dual-container { flex-direction: row; justify-content: center; } }
        .dual-slot { background: #000; border-radius: 15px; overflow: hidden; border: 1px solid var(--glass-border); width: 85%; max-width: 320px; }
        
        .ch-picker-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); backdrop-filter: blur(10px); z-index: 7000; display: none; align-items: center; justify-content: center; }
        .ch-picker-window { width: 90%; max-width: 450px; background: #0f172a; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; max-height: 80vh; }
        .ch-picker-header { padding: 15px; background: var(--main); color: #fff; font-weight: 900; display: flex; justify-content: space-between; }
        .ch-picker-list { padding: 10px; overflow-y: auto; }
        .ch-pick-item { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; margin-bottom: 8px; cursor: pointer; color: #fff; font-size: 14px; text-align: right; }

        .visitors-badge-float { position: fixed; bottom: 20px; left: 20px; width: 40px; height: 40px; background: rgba(34, 197, 94, 0.1); backdrop-filter: blur(10px); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1px solid #22c55e; font-size: 10px; font-weight: 900; color: #22c55e; }
        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        footer { text-align: center; padding: 30px; font-size: 9px; opacity: 0.4; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="loader-content">
        <div class="intro-icon-box"><i class="fas fa-play-circle"></i></div>
        <h2 class="intro-title">الخدمة الرقمية</h2>
        <div class="loading-bar"></div>
    </div>
</div>

<div class="ad-popup-overlay" id="adPopup">
    <div class="ad-popup-content">
        <div class="ad-close-btn" onclick="closeAd()"><i class="fas fa-times"></i></div>
        <img src="https://files.catbox.moe/nnkepx.png" class="ad-popup-image" alt="إعلان">
        <a href="https://wa.me/966505571164" class="ad-subscribe-btn">
            <i class="fab fa-whatsapp"></i> اشترك الآن عبر واتساب
        </a>
    </div>
</div>

<div class="ch-picker-overlay" id="ch-picker">
    <div class="ch-picker-window">
        <div class="ch-picker-header"><span>📺 قائمة القنوات</span><i class="fas fa-times" onclick="closePicker()" style="cursor:pointer;"></i></div>
        <div class="ch-picker-list">
            <?php foreach($all_channels as $ch): ?>
                <div class="ch-pick-item" onclick="confirmPick('<?= $ch['file'] ?>', '<?= $ch['name'] ?>')"><?= $ch['name'] ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="visitors-badge-float"><i class="fas fa-users"></i><span id="realtime-visitors"><?= $online_now ?></span></div>
    <div class="bg-pattern"></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">منصة X</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span></div></div></div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item" onclick="switchSection('matches_table', this)"><img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="filter: brightness(0) invert(1);"><span>مباريات اليوم</span></div>
            <div class="cat-item" onclick="switchSection('dual_player', this)"><img src="mg/ch2.png"><span>شاشتين</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item <?= ($s['key'] == 'beIN') ? 'active' : '' ?>" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section">
            <div class="match-nav">
                <span style="font-weight:900; font-size:12px; color:#fff;">مباريات اليوم: <?= $date_get ?></span>
            </div>
            <div class="match-grid-container">
            <?php if(empty($ordered_matches)): ?>
                <p style="text-align:center; opacity:0.5; padding:20px; grid-column: 1/-1;">لا توجد مباريات هامة لهذا التاريخ</p>
            <?php else: foreach($league_settings as $id => $set): if(isset($ordered_matches[$id])): ?>
                <div class="league-sep"><?= $set['name'] ?></div>
                <?php foreach($ordered_matches[$id] as $m): 
                    $h_raw = $m['teams']['home']['name'];
                    $a_raw = $m['teams']['away']['name'];
                    $h_name = $translate[$h_raw] ?? $h_raw;
                    $a_name = $translate[$a_raw] ?? $a_raw;
                    $status = $translate[$m['fixture']['status']['short']] ?? $m['fixture']['status']['short'];
                ?>
                    <div class="card">
                        <div class="m-row">
                            <div class="m-team-col"><img src="<?= $m['teams']['home']['logo'] ?>"><?= $h_name ?></div>
                            <div class="m-time-box">
                                <?= ($m['fixture']['status']['short'] == 'NS') ? date("H:i", $m['fixture']['timestamp']) : $m['goals']['home'].'-'.$m['goals']['away'] ?>
                                <br><small style="font-size:7px; display:block;"><?= $status ?></small>
                            </div>
                            <div class="m-team-col"><img src="<?= $m['teams']['away']['logo'] ?>"><?= $a_name ?></div>
                        </div>
                    </div>
            <?php endforeach; endif; endforeach; endif; ?>
            </div>
        </div>

        <div id="section-dual_player" class="channel-section">
            <div style="background: rgba(14, 165, 233, 0.1); border: 1px dashed #0ea5e9; border-radius: 12px; padding: 10px; margin: 15px auto; text-align: center; width: 85%;">
                <p style="font-size: 10px; font-weight: 700; color: #38bdf8; margin: 0;">أكتم صوت القناة بالضغط على ( <i class="fas fa-volume-mute"></i> ) لتشغيل القناة الاخرى</p>
            </div>
            <div class="dual-container">
                <div class="dual-slot">
                    <div class="video-box" id="dual-screen-1" style="background-image:url('mg/chn1.png')"></div>
                    <button class="play-btn" onclick="openPicker(1)"><i class="fas fa-tv"></i> <span id="btn-text-1">القناة الأولى</span></button>
                </div>
                <div class="dual-slot">
                    <div class="video-box" id="dual-screen-2" style="background-image:url('mg/chn2.png')"></div>
                    <button class="play-btn" onclick="openPicker(2)"><i class="fas fa-tv"></i> <span id="btn-text-2">القناة الثانية</span></button>
                </div>
            </div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($s['key'] == 'beIN') ? 'active' : '' ?>">
            <div class="channel-grid">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>" style="background-image:url('mg/wel.GIF')"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">
                    <i class="fas fa-play-circle"></i> <span>تشغيل <?= $ch['name'] ?></span>
                </button>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
let activeSlot = 0; 

// وظائف الإعلان المنبثق
function closeAd() { document.getElementById('adPopup').style.display = 'none'; }

function openPicker(slot) { activeSlot = slot; document.getElementById('ch-picker').style.display = 'flex'; }
function closePicker() { document.getElementById('ch-picker').style.display = 'none'; }
function confirmPick(url, name) {
    let screen = document.getElementById('dual-screen-' + activeSlot);
    screen.style.backgroundImage = "none";
    screen.innerHTML = `<iframe src="${url}?autoplay=1&muted=1" allowfullscreen></iframe>`;
    document.getElementById('btn-text-' + activeSlot).innerText = name;
    closePicker();
}
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}
function startStream(boxId, file, btn) {
    let vBox = document.getElementById(boxId); vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=0" allowfullscreen></iframe>`;
    btn.querySelector('span').innerText = 'متصل الآن..'; 
}

window.addEventListener('load', () => { 
    setTimeout(() => { 
        document.getElementById('pro-intro').classList.add('intro-hide');
        // إظهار الإعلان بعد اختفاء صفحة التحميل بـ 500 ملي ثانية
        setTimeout(() => {
            document.getElementById('adPopup').style.display = 'flex';
        }, 500);
    }, 1500); 
});

setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(c => { document.getElementById('realtime-visitors').innerText = c; }); }, 5000);
</script>
</body>
</html>
