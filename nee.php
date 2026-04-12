<?php
session_start();
error_reporting(0);

// --- إعدادات جدول المباريات (عثمان) ---
date_default_timezone_set('Asia/Riyadh');
$FOOTBALL_API_KEY = '6b9915e3b84f54b3962e5817b9e26e5f'; 
$date_get = date('Y-m-d');

// مصفوفة الترجمة
$translate = [
    'NS' => 'لم تبدأ', 'FT' => 'انتهت', '1H' => 'شوط 1', '2H' => 'شوط 2', 
    'HT' => 'بين الشوطين', 'P' => 'ركلات ترجيح', 'PST' => 'مؤجلة', 'CANC' => 'ملغاة',
    'TBD' => 'يحدد لاحقاً', 'ABD' => 'ملغاة', 'LIVE' => 'مباشر',
    'Al-Hilal' => 'الهلال', 'Al-Nassr' => 'النصر', 'Al-Ittihad' => 'الاتحاد', 'Al-Ahli' => 'الأهلي',
    'Al-Shabab' => 'الشباب', 'Al-Ettifaq' => 'الاتفاق', 'Al-Fateh' => 'الفتح', 'Al-Taawoun' => 'التعاون',
    'Real Madrid' => 'ريال مدريد', 'Barcelona' => 'برشلونة', 'Manchester City' => 'مانشستر سيتي'
];

$league_settings = array(
    307 => array('name' => 'الدوري السعودي'),
    42  => array('name' => 'دوري أبطال أوروبا'),
    525 => array('name' => 'نخبة آسيا'),
    39  => array('name' => 'الدوري الإنجليزي'),
    140 => array('name' => 'الدوري الإسباني'),
    135 => array('name' => 'الدوري الإيطالي'),
    78  => array('name' => 'الدوري الألماني'),
    61  => array('name' => 'الدوري الفرنسي')
);

function getFixturesWithCache($date, $key) {
    $cache_file = "cache_" . $date . ".json";
    $expire_time = 180; // تقليل الوقت لـ 3 دقائق لضمان التحديث

    // التحقق من الكاش
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $expire_time)) {
        $content = file_get_contents($cache_file);
        $cached_data = json_decode($content, true);
        if (!empty($cached_data)) return $cached_data;
    }

    // طلب بيانات جديدة
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date&timezone=Asia/Riyadh",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "x-apisports-key: $key", // تم استخدام الرأس الافتراضي لـ API-Sports
            "x-rapidapi-key: $key",  // احتياطي في حال كان الحساب مرتبط بـ RapidAPI
            "Accept: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $data = json_decode($response, true);
    curl_close($curl);

    if (!empty($data['response'])) {
        file_put_contents($cache_file, json_encode($data['response']));
        return $data['response'];
    }
    return array();
}

$fixtures = getFixturesWithCache($date_get, $FOOTBALL_API_KEY);
$ordered_matches = array();

if (!empty($fixtures)) {
    foreach ($fixtures as $f) {
        $id = (int)$f['league']['id'];
        if (isset($league_settings[$id])) { 
            $ordered_matches[$id][] = $f; 
        }
    }
}

// --- جلب بيانات القنوات من JSONBin ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';

function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = $cloud['custom_channels'] ?? [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
$news = $cloud['news_ticker'] ?? ['text' => '', 'status' => 'hide'];

// نظام الزوار
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $v_data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
    $v_data[$session_id] = $time;
    foreach ($v_data as $id => $last_time) { if ($time - $last_time > 120) unset($v_data[$id]); }
    file_put_contents($visitors_file, serialize($v_data));
    echo count($v_data); exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

function filterSection($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { return (isset($c['section']) && strtolower($c['section']) == strtolower($sec)); });
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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        
        /* Intro */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.1); }
        .intro-icon-box { width: 80px; height: 80px; background: var(--main); border-radius: 25px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 30px rgba(225, 29, 72, 0.4); animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }

        /* Header */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.9); backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .social-links { display: flex; justify-content: center; gap: 8px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { flex: 1; padding: 8px; border-radius: 50px; text-decoration: none; font-size: 10px; font-weight: bold; color: white; border: 1px solid rgba(255,255,255,0.2); }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }

        /* Ticker */
        .news-ticker { background: rgba(225, 29, 72, 0.1); height: 30px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; }
        .ticker-label { background: var(--main); color: white; padding: 0 10px; font-size: 10px; font-weight: 900; height: 100%; display: flex; align-items: center; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker 30s linear infinite; }
        .ticker-text { padding: 0 40px; font-size: 12px; font-weight: bold; }
        @keyframes ticker { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        /* Tabs */
        .category-tabs { display: flex; gap: 8px; overflow-x: auto; padding: 5px 10px; scrollbar-width: none; }
        .cat-item { min-width: 70px; background: var(--glass); border: 1px solid var(--glass-border); padding: 10px 5px; border-radius: 15px; text-align: center; cursor: pointer; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 24px; height: 24px; margin-bottom: 5px; filter: brightness(0) invert(1); }
        .cat-item span { display: block; font-size: 9px; font-weight: bold; }

        /* Matches */
        .league-sep { background: rgba(255,255,255,0.05); padding: 8px 15px; border-radius: 8px; font-size: 11px; font-weight: 900; margin: 15px 10px 10px; border-right: 4px solid var(--main); }
        .card { background: var(--glass); border: 1px solid var(--glass-border); border-radius: 15px; margin: 0 10px 10px; overflow: hidden; }
        .m-row { display: flex; align-items: center; justify-content: space-between; padding: 15px; }
        .m-team-col { flex: 1; text-align: center; font-size: 11px; font-weight: bold; }
        .m-team-col img { width: 30px; height: 30px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto; }
        .m-time-box { background: rgba(225,29,72,0.1); padding: 8px; border-radius: 10px; min-width: 60px; text-align: center; color: var(--main); font-weight: 900; }
        
        /* Video Section */
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; }
        .video-box iframe { width: 100%; height: 100%; border: none; }
        .play-btn { width: 90%; margin: 10px auto; background: var(--glass); border: 1px solid var(--glass-border); color: white; padding: 12px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: bold; }

        .channel-section { display: none; }
        .channel-section.active { display: block; }

        .visitors-badge-float { position: fixed; bottom: 20px; left: 20px; background: rgba(34, 197, 94, 0.2); color: #22c55e; padding: 10px; border-radius: 50px; font-size: 12px; font-weight: bold; border: 1px solid #22c55e; z-index: 100; }
        footer { text-align: center; padding: 40px 10px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="intro-icon-box"><i class="fas fa-play-circle fa-2x"></i></div>
    <h2 style="color:white; margin-top:20px; font-size:18px;">الخدمة الرقمية</h2>
</div>

<div class="main-container">
    <div class="visitors-badge-float"><i class="fas fa-users"></i> <span id="realtime-visitors"><?= $online_now ?></span></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker">
            <div class="ticker-label">تنبيه</div>
            <div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span></div>
        </div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)">
                <img src="https://cdn-icons-png.flaticon.com/512/833/833593.png"><span>المباريات</span>
            </div>
            <?php foreach($active_sections as $s): ?>
            <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)">
                <img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section active">
            <?php if(empty($ordered_matches)): ?>
                <div style="text-align:center; padding:50px; opacity:0.6;">لا توجد مباريات هامة مسجلة الآن</div>
            <?php else: ?>
                <?php foreach($league_settings as $id => $set): if(isset($ordered_matches[$id])): ?>
                    <div class="league-sep"><?= $set['name'] ?></div>
                    <?php foreach($ordered_matches[$id] as $m): 
                        $h_name = $translate[$m['teams']['home']['name']] ?? $m['teams']['home']['name'];
                        $a_name = $translate[$m['teams']['away']['name']] ?? $m['teams']['away']['name'];
                        $status = $translate[$m['fixture']['status']['short']] ?? $m['fixture']['status']['short'];
                    ?>
                    <div class="card">
                        <div class="m-row">
                            <div class="m-team-col"><img src="<?= $m['teams']['home']['logo'] ?>"><?= $h_name ?></div>
                            <div class="m-time-box">
                                <?= ($m['fixture']['status']['short'] == 'NS') ? date("H:i", $m['fixture']['timestamp']) : $m['goals']['home'].'-'.$m['goals']['away'] ?>
                                <div style="font-size:8px;"><?= $status ?></div>
                            </div>
                            <div class="m-team-col"><img src="<?= $m['teams']['away']['logo'] ?>"><?= $a_name ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; endforeach; ?>
            <?php endif; ?>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>" style="background: url('https://i.ibb.co/3S1mQvX/player-bg.jpg') center/cover;"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">
                    <i class="fas fa-play"></i> <span>تشغيل <?= $ch['name'] ?></span>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <footer>متجر الخدمة الرقمية © 2026</footer>
</div>

<script>
function switchSection(id, el) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    el.classList.add('active');
}

function startStream(boxId, url, btn) {
    const box = document.getElementById(boxId);
    box.innerHTML = `<iframe src="${url}" allowfullscreen allow="autoplay"></iframe>`;
    btn.innerHTML = '<i class="fas fa-broadcast-tower"></i> متصل الآن...';
}

window.onload = () => {
    setTimeout(() => document.getElementById('pro-intro').classList.add('intro-hide'), 1200);
    setInterval(() => {
        fetch(window.location.pathname + '?fetch_visitors=1')
        .then(r => r.text())
        .then(c => document.getElementById('realtime-visitors').innerText = c);
    }, 10000);
};
</script>
</body>
</html>
