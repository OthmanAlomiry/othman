<?php
session_start();
error_reporting(0);

// --- إعدادات جدول المباريات ---
date_default_timezone_set('Asia/Riyadh');
$FOOTBALL_API_KEY = '6b9915e3b84f54b3962e5817b9e26e5f'; 

$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));

// مصفوفة الحالات الثابتة
$status_translate = [
    'NS' => 'لم تبدأ', 'FT' => 'انتهت', '1H' => 'شوط 1', '2H' => 'شوط 2', 
    'HT' => 'بين الشوطين', 'P' => 'ركلات ترجيح', 'PST' => 'مؤجلة', 'CANC' => 'ملغاة'
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

// --- بيانات السحابة الأصلية ---
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

        /* إخفاء شريط جوجل العلوي المزعج */
        .goog-te-banner-frame.skiptranslate, .goog-te-gadget-icon { display: none !important; }
        body { top: 0px !important; }
        .goog-te-gadget-simple { background-color: transparent !important; border: none !important; padding: 0 !important; font-size: 0 !important; }
        #google_translate_element { display: none; }

        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }

        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .loader-content { display: flex; flex-direction: column; align-items: center; }
        .intro-icon-box { width: 100px; height: 100px; background: var(--main); border-radius: 30%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 50px rgba(225, 29, 72, 0.5); animation: bounceIn 1s ease-out; }
        .intro-icon-box i { font-size: 50px; color: white; }
        .intro-title { margin-top: 25px; color: white; font-weight: 900; font-size: 24px; }
        .loading-bar { width: 150px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px; margin-top: 30px; overflow: hidden; position: relative; }
        .loading-bar::after { content: ""; position: absolute; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, var(--main), transparent); animation: loadingMove 1.5s infinite; }
        @keyframes loadingMove { 100% { left: 100%; } }
        @keyframes bounceIn { 0% { opacity: 0; transform: scale(0.3); } 50% { opacity: 1; transform: scale(1.1); } 100% { transform: scale(1); } }

        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        
        .visitors-badge-float { position: fixed; bottom: 25px; left: 25px; width: 45px; height: 45px; background: rgba(34, 197, 94, 0.15); backdrop-filter: blur(10px); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 5000; border: 1.5px solid #22c55e; }

        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; text-align: center; border: 1.5px solid #ffffff; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }

        .news-ticker { background: rgba(225, 29, 72, 0.15); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-wrap { flex: 1; overflow: hidden; direction: ltr; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 45s linear infinite; }
        .ticker-text { color: #fff; font-size: 12px; font-weight: 700; padding: 0 60px; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: scale(1.03); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }

        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 20px; }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        
        .live-badge { display: flex; align-items: center; gap: 5px; background: rgba(225, 29, 72, 0.1); padding: 5px 12px; border-radius: 20px; border: 1px solid rgba(225, 29, 72, 0.4); color: #ff4d4d; font-weight: 900; font-size: 11px; }
        .live-dot { width: 7px; height: 7px; background: #ff4d4d; border-radius: 50%; animation: pulse-red 1.2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(255, 77, 77, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0); } }

        .play-btn { width: 92%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 10px; background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%); color: #fff; border: 1px solid var(--glass-border); padding: 15px; border-radius: 15px; font-weight: 900; cursor: pointer; transition: 0.4s; }

        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; background-image: url('mg/wel.GIF'); background-size: cover; background-position: center; position: relative; overflow: hidden; }
        .video-box iframe { position: absolute; top:0; left:0; width: 100%; height: 100%; border: none; }

        .match-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; background: var(--glass); padding: 10px; border-radius: 15px; border: 1px solid var(--glass-border); }
        .match-nav a { color: #fff; background: rgba(255,255,255,0.1); width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; }
        .league-sep { background: rgba(255, 255, 255, 0.07); padding: 8px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; margin: 15px 0 10px; border-right: 4px solid var(--main); color: #fff; }
        .m-row { display: flex; justify-content: space-between; align-items: center; padding: 15px 10px; text-align: center; }
        .m-team-col { flex: 1; font-size: 11px; font-weight: 700; color: #fff; }
        .m-team-col img { width: 32px; height: 32px; display: block; margin: 0 auto 8px; }
        .m-time-box { flex: 0.6; background: rgba(225, 29, 72, 0.1); border: 1px solid rgba(225, 29, 72, 0.3); padding: 5px; border-radius: 10px; font-weight: 900; font-size: 13px; color: var(--main); }

        .ch-picker-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(15px); z-index: 7000; display: none; align-items: center; justify-content: center; }
        .ch-picker-window { width: 95%; max-width: 480px; background: #0f172a; border-radius: 25px; max-height: 80vh; overflow: hidden; display: flex; flex-direction: column; }
        .ch-picker-header { padding: 20px; background: var(--main); color: white; font-weight: 900; display: flex; justify-content: space-between; }
        .ch-picker-list { padding: 15px; overflow-y: auto; display: grid; gap: 10px; }
        .ch-pick-item { background: rgba(255,255,255,0.08); padding: 18px; border-radius: 15px; text-align: right; cursor: pointer; color: #fff; }

        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        footer { text-align: center; padding: 40px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body class="notranslate"> <div id="google_translate_element"></div>

<div id="pro-intro">
    <div class="loader-content">
        <div class="intro-icon-box"><i class="fas fa-play-circle"></i></div>
        <h2 class="intro-title">الخدمة الرقمية</h2>
        <div class="loading-bar"></div>
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
    <div class="visitors-badge-float"><i class="fas fa-users" style="color:#22c55e;"></i> <span id="realtime-visitors"><?php echo $online_now; ?></span></div>

    <div class="bg-pattern"></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">تويتر</a>
        </div>

        <?php if($news['status'] == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span></div></div></div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)"><img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="filter: brightness(0) invert(1);"><span>جدول المباريات</span></div>
            <div class="cat-item" onclick="switchSection('dual_player', this)"><img src="mg/ch2.png"><span>تشغيل مباريتين</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section active translate">
            <div class="match-nav">
                <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
                <span style="font-weight:900; font-size:13px; color:#fff;"><?= $date_get ?></span>
                <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
            </div>
            <?php if(empty($ordered_matches)): ?>
                <p style="text-align:center; opacity:0.5; padding:20px; color:#fff;">لا توجد مباريات هامة لهذا التاريخ</p>
            <?php else: foreach($league_settings as $id => $set): if(isset($ordered_matches[$id])): ?>
                <div class="league-sep"><?= $set['name'] ?></div>
                <?php foreach($ordered_matches[$id] as $m): 
                    $h_name = $m['teams']['home']['name'];
                    $a_name = $m['teams']['away']['name'];
                    $status = isset($status_translate[$m['fixture']['status']['short']]) ? $status_translate[$m['fixture']['status']['short']] : $m['fixture']['status']['short'];
                ?>
                    <div class="card" style="margin-bottom:10px;">
                        <div class="m-row">
                            <div class="m-team-col"><img src="<?= $m['teams']['home']['logo'] ?>"><?= $h_name ?></div>
                            <div class="m-time-box">
                                <?= ($m['fixture']['status']['short'] == 'NS') ? date("H:i", $m['fixture']['timestamp']) : $m['goals']['home'].'-'.$m['goals']['away'] ?>
                                <br><small style="font-size:8px; display:block;"><?= $status ?></small>
                            </div>
                            <div class="m-team-col"><img src="<?= $m['teams']['away']['logo'] ?>"><?= $a_name ?></div>
                        </div>
                    </div>
            <?php endforeach; endif; endforeach; endif; ?>
        </div>

        <div id="section-dual_player" class="channel-section">
             <div class="dual-container" style="margin-top:20px;">
                <div id="dual-screen-1" class="video-box" style="border-radius:15px; margin-bottom:10px;"></div>
                <button class="play-btn" onclick="openPicker(1)">اختيار القناة الأولى</button>
                <div id="dual-screen-2" class="video-box" style="border-radius:15px; margin-bottom:10px;"></div>
                <button class="play-btn" onclick="openPicker(2)">اختيار القناة الثانية</button>
             </div>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="c-head">
                    <div class="live-badge"><div class="live-dot"></div> مباشر</div>
                    <div style="font-size: 10px; opacity: 0.6; font-weight: bold;">بث مباشر</div>
                </div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">
                    <i class="fas fa-play-circle"></i> <span>تشغيل <?= $ch['name'] ?></span>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'en',
    includedLanguages: 'ar',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
    autoDisplay: false
  }, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<script>
// وظيفة إجبارية لبدء الترجمة للعربية فوراً
function triggerTranslate() {
    const googleDiv = document.querySelector(".goog-te-combo");
    if (googleDiv) {
        googleDiv.value = "ar";
        googleDiv.dispatchEvent(new Event('change'));
    } else {
        setTimeout(triggerTranslate, 500);
    }
}
window.addEventListener('load', triggerTranslate);

let activeSlot = 0; 
function openPicker(slot) { activeSlot = slot; document.getElementById('ch-picker').style.display = 'flex'; }
function closePicker() { document.getElementById('ch-picker').style.display = 'none'; }
function confirmPick(url, name) {
    if(!url) return;
    let screen = document.getElementById('dual-screen-' + activeSlot);
    screen.style.backgroundImage = "none";
    screen.innerHTML = `<iframe src="${url}?autoplay=1&muted=1" allowfullscreen></iframe>`;
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
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); });
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1') .then(res => res.text()) .then(c => { document.getElementById('realtime-visitors').innerText = c; }); }, 5000);
</script>
</body>
</html>
