<?php
session_start();
error_reporting(0); 

// --- 1. إعدادات الوقت والقواعد الثابتة ---
date_default_timezone_set('Asia/Riyadh');
$date_get = date('Y-m-d');

$channels_cache_file = 'channels_cache.json';
$matches_cache_file = 'matches_cache.json';

$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';

// --- 2. تحديث البيانات يدوياً ---
if (isset($_GET['update_data'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $API_KEY, "X-Bin-Meta: false"]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    
    if ($res) {
        file_put_contents($channels_cache_file, $res);
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success=1" . (isset($_GET['admin']) ? "&admin=1" : ""));
        exit;
    }
}

// --- 3. جلب البيانات من الكاش ---
$cloud = file_exists($channels_cache_file) ? json_decode(file_get_contents($channels_cache_file), true) : [];
$all_channels = $cloud['custom_channels'] ?? [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return ($s['status'] ?? '') == 'show'; });
$news = $cloud['news_ticker'] ?? ['text' => '', 'status' => 'hide'];

// --- 4. جلب المباريات بكاش 10 دقائق ---
if (file_exists($matches_cache_file) && (time() - filemtime($matches_cache_file) < 600)) {
    $match_data_new = json_decode(file_get_contents($matches_cache_file), true);
} else {
    $FOOTBALL_API_KEY_NEW = '02e59a7388ad43b29661ef3bf22e74de'; 
    $ch = curl_init('https://api.football-data.org/v4/matches');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $FOOTBALL_API_KEY_NEW]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response_new = curl_exec($ch);
    curl_close($ch);
    if ($response_new) {
        file_put_contents($matches_cache_file, $response_new);
        $match_data_new = json_decode($response_new, true);
    }
}

$translate = [
    'NS' => 'لم تبدأ', 'FT' => 'انتهت', 'FINISHED' => 'انتهت', 'TIMED' => 'لم تبدأ', 'IN_PLAY' => 'مباشر', 
    '1H' => 'شوط 1', '2H' => 'شوط 2', 'HT' => 'بين الشوطين', 'PAUSED' => 'بين الشوطين',
    'Manchester City' => 'مانشستر سيتي', 'Liverpool' => 'ليفربول', 'Arsenal' => 'أرسنال',
    'Real Madrid' => 'ريال مدريد', 'Barcelona' => 'برشلونة', 'Al-Hilal' => 'الهلال', 'Al-Nassr' => 'النصر'
];

$leagues_map_new = [
    'PL'  => ['name' => 'الدوري الإنجليزي'], 'PD'  => ['name' => 'الدوري الإسباني'],
    'SA'  => ['name' => 'الدوري الإيطالي'], 'BL1' => ['name' => 'الدوري الألماني'],
    'FL1' => ['name' => 'الدوري الفرنسي'], 'CL'  => ['name' => 'دوري أبطال أوروبا'],
];

$ordered_matches = [];
if (isset($match_data_new['matches'])) {
    foreach ($match_data_new['matches'] as $m) {
        $l_code = $m['competition']['code'] ?? '';
        if (isset($leagues_map_new[$l_code])) {
            $ordered_matches[$l_code][] = $m;
        }
    }
}

// --- 5. نظام الزوار ---
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) { if ($time - $last_time > 120) unset($data[$id]); }
    @file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

function filterSection($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) { 
        return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); 
    });
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
        :root { --main: #D4AF37; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(212, 175, 55, 0.3); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        
        .admin-refresh-area { padding: 10px; text-align: center; }
        .refresh-btn { background: var(--main); color: #000; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 12px; }

        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at center, #1e293b 0%, #050c14 100%); display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.2); }
        .loader-content { display: flex; flex-direction: column; align-items: center; }
        .intro-icon-box { width: 100px; height: 100px; background: var(--main); border-radius: 30%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 50px rgba(212, 175, 55, 0.5); animation: glowPulse 2s infinite ease-in-out; }
        .intro-icon-box i { font-size: 50px; color: #000; }
        .intro-title { margin-top: 25px; color: var(--main); font-weight: 900; font-size: 24px; }
        .loading-bar { width: 150px; height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px; margin-top: 30px; overflow: hidden; position: relative; }
        .loading-bar::after { content: ""; position: absolute; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, var(--main), transparent); animation: loadingMove 1.5s infinite; }
        @keyframes glowPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        @keyframes loadingMove { 100% { left: 100%; } }

        .ad-popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); z-index: 8000; display: none; align-items: center; justify-content: center; }
        .ad-popup-content { position: relative; width: 85%; max-width: 320px; }
        .ad-popup-image { width: 100%; border-radius: 20px; border: 2px solid var(--main); }
        .ad-close-btn { position: absolute; top: -15px; right: -15px; width: 35px; height: 35px; background: var(--main); color: #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; cursor: pointer; z-index: 10; }
        .ad-subscribe-btn { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background: #25d366; color: white; padding: 10px 25px; border-radius: 50px; text-decoration: none; font-weight: 900; font-size: 14px; border: 2px solid white; display: flex; align-items: center; gap: 8px; }

        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; border: 1.5px solid rgba(212, 175, 55, 0.5); }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; } .btn-sn { background: #FFFC00; color: #000 !important; } .btn-tw { background: #000; }
        
        .news-ticker { background: rgba(212, 175, 55, 0.1); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; border-y: 1px solid rgba(212, 175, 55, 0.2); }
        .ticker-label { background: var(--main); color: #000; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-wrap { flex: 1; overflow: hidden; direction: ltr; display: flex; align-items: center; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 45s linear infinite; }
        .ticker-text { color: #fff; font-size: 12px; font-weight: 700; padding: 0 60px; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }

        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 65px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 12px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(212, 175, 55, 0.2); border-color: var(--main); }
        .cat-item img { width: 26px; height: 26px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; display: block; }
        .cat-item.active span { color: var(--main); }

        .grid { padding: 15px; }
        .channel-section { display: none; width: 100%; }
        .channel-section.active { display: block; animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 15px; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; background-size: cover; background-position: center; }
        .video-box iframe { position: absolute; top:0; left:0; width: 100%; height: 100%; border: none; }

        .match-card-top { padding: 12px; text-align: center; border-bottom: 1px solid var(--glass-border); background: rgba(255,255,255,0.02); }
        .m-league-name { font-size: 10px; color: var(--main); font-weight: 900; margin-bottom: 5px; display: block; }
        .m-teams-flex { display: flex; justify-content: center; align-items: center; gap: 15px; }
        .m-team-name { font-size: 13px; font-weight: 900; color: #fff; }
        .m-vs-tag { font-size: 10px; background: var(--main); padding: 2px 6px; border-radius: 4px; color: #000; font-weight: 900; }
        
        .play-btn { width: 92%; margin: 15px auto; display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(212, 175, 55, 0.1); color: var(--main); border: 1px solid var(--main); padding: 12px; border-radius: 12px; font-weight: 900; cursor: pointer; font-size: 13px; transition: 0.3s; }
        .play-btn:hover { background: var(--main); color: #000; }
        .backup-btn { width: 92%; margin: -10px auto 15px; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255, 255, 255, 0.03); color: #fff; border: 1px solid var(--glass-border); padding: 10px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 11px; }

        .league-sep { background: rgba(212, 175, 55, 0.05); padding: 10px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; margin: 15px 0 10px; border-right: 4px solid var(--main); color: var(--main); }
        .m-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 10px; }
        .m-team-col { flex: 1; font-size: 10px; font-weight: 700; text-align: center; }
        .m-team-col img { width: 28px; height: 28px; display: block; margin: 0 auto 6px; }
        .m-time-box { flex: 0.6; background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.2); padding: 6px; border-radius: 10px; text-align: center; font-weight: 900; font-size: 12px; color: var(--main); }
        
        .visitors-badge-float { position: fixed; bottom: 20px; left: 20px; width: 40px; height: 40px; background: rgba(212, 175, 55, 0.1); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 5000; border: 1px solid var(--main); font-size: 10px; font-weight: 900; color: var(--main); }
        footer { text-align: center; padding: 30px; font-size: 9px; opacity: 0.6; color: var(--main); }
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
        <img src="https://files.catbox.moe/nnkepx.png" class="ad-popup-image">
        <a href="https://wa.me/966505571164" class="ad-subscribe-btn"><i class="fab fa-whatsapp"></i> اشترك الآن عبر واتساب</a>
    </div>
</div>

<div class="main-container">
    <div class="visitors-badge-float"><i class="fas fa-users"></i><span id="realtime-visitors"><?= $online_now ?></span></div>

    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-sn">سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-tw">منصة X</a>
        </div>

        <?php if(isset($_GET['admin'])): ?>
        <div class="admin-refresh-area">
            <button class="refresh-btn" onclick="location.href='?update_data=1&admin=1'"><i class="fas fa-sync-alt"></i> تحديث الكاش من Cloud</button>
        </div>
        <?php endif; ?>

        <?php if(($news['status'] ?? 'hide') == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span></div></div></div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('matches_table', this)"><img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="filter: brightness(0) invert(1);"><span>جدول المباريات</span></div>
            <div class="cat-item" onclick="switchSection('important_matches', this)"><img src="https://files.catbox.moe/cjayfo.png"><span>مباريات مهمة</span></div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid">
        <div id="section-matches_table" class="channel-section active">
            <div class="match-grid-container">
            <?php if(empty($ordered_matches)): ?>
                <div class="card" style="padding: 20px; text-align: center;"><p style="opacity:0.5; margin:0;">لا توجد مباريات هامة لهذا التاريخ</p></div>
            <?php else: foreach($ordered_matches as $code => $matches_list): ?>
                <div class="league-sep"><?= $leagues_map_new[$code]['name'] ?></div>
                <?php foreach($matches_list as $m): 
                    $h_name = $translate[$m['homeTeam']['shortName']] ?? $m['homeTeam']['name'];
                    $a_name = $translate[$m['awayTeam']['shortName']] ?? $m['awayTeam']['name'];
                ?>
                    <div class="card">
                        <div class="m-row">
                            <div class="m-team-col"><img src="<?= $m['homeTeam']['crest'] ?>"><?= $h_name ?></div>
                            <div class="m-time-box">
                                <?php if($m['status'] == 'TIMED'): ?> <?= date("H:i", strtotime($m['utcDate'])) ?>
                                <?php else: ?> <span><?= ($m['score']['fullTime']['home'] ?? 0) ?> - <?= ($m['score']['fullTime']['away'] ?? 0) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="m-team-col"><img src="<?= $m['awayTeam']['crest'] ?>"><?= $a_name ?></div>
                        </div>
                    </div>
                <?php endforeach; endforeach; endif; ?>
            </div>
        </div>

        <div id="section-important_matches" class="channel-section">
            <?php 
            $important_channels = filterSection($all_channels, 'important'); 
            if(empty($important_channels)): echo '<div class="card" style="padding:20px; text-align:center; opacity:0.5;">لا توجد مباريات جارية مضافة حالياً</div>';
            else: foreach($important_channels as $ch): ?>
            <div class="card">
                <div class="match-card-top">
                    <span class="m-league-name"><?= $ch['match_name'] ?></span>
                    <div class="m-teams-flex">
                        <span class="m-team-name"><?= $ch['team1'] ?></span>
                        <span class="m-vs-tag">VS</span>
                        <span class="m-team-name"><?= $ch['team2'] ?></span>
                    </div>
                </div>
                <div class="video-box" id="vid-<?= $ch['id'] ?>" style="background-image:url('mg/wel.png')"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this, '<?= $ch['name'] ?>')">
                    <i class="fas fa-play-circle"></i> <span>تشغيل البث: <?= $ch['name'] ?></span>
                </button>
                <?php if(!empty($ch['file_backup'])): ?>
                <button class="backup-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file_backup'] ?>', this, '<?= $ch['name'] ?> (احتياطي)')">
                    <i class="fas fa-shield-alt"></i> <span>تشغيل البث الاحتياطي</span>
                </button>
                <?php endif; ?>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
        <div id="section-<?= $s['key'] ?>" class="channel-section">
            <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>" style="background-image:url('mg/wel.png')"></div>
                <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this, '<?= $ch['name'] ?>')">
                    <i class="fas fa-play-circle"></i> <span>تشغيل <?= $ch['name'] ?></span>
                </button>
                <?php if(!empty($ch['file_backup'])): ?>
                <button class="backup-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file_backup'] ?>', this, '<?= $ch['name'] ?> (احتياطي)')">
                    <i class="fas fa-shield-alt"></i> <span>تشغيل البث الاحتياطي</span>
                </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
</div>

<script>
function closeAd() { document.getElementById('adPopup').style.display = 'none'; }

function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

function startStream(boxId, file, btn, channelName) {
    let vBox = document.getElementById(boxId); 
    vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}" allowfullscreen allow="autoplay; encrypted-media"></iframe>`;
    
    if(btn.classList.contains('play-btn') || btn.classList.contains('backup-btn')) {
        btn.querySelector('span').innerText = channelName + ' (يبث الآن)';
    }
}

window.addEventListener('load', () => { 
    setTimeout(() => { 
        document.getElementById('pro-intro').classList.add('intro-hide');
        setTimeout(() => { document.getElementById('adPopup').style.display = 'flex'; }, 500);
    }, 1500); 
});

setInterval(() => { 
    fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(c => { 
        document.getElementById('realtime-visitors').innerText = c; 
    }); 
}, 5000);
</script>
</body>
</html>
