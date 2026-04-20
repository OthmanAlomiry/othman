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

// --- 4. جلب المباريات التلقائية (API خارجي) ---
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
    '1H' => 'شوط 1', '2H' => 'شوط 2', 'HT' => 'بين الشوطين', 'PAUSED' => 'بين الشوطين'
];

$leagues_map_new = [
    'PL'  => ['name' => 'الدوري الإنجليزي'], 'PD'  => ['name' => 'الدوري الإسباني'],
    'SA'  => ['name' => 'الدوري الإيطالي'], 'CL'  => ['name' => 'دوري أبطال أوروبا'],
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
    <title>الخدمة الرقمية - بث مباشر</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; display: flex; justify-content: center; }
        .main-container { width: 100%; max-width: 500px; position: relative; min-height: 100vh; }
        
        /* الهيدر والتبويبات */
        .header-fixed { position: fixed; top: 0; width: 100%; max-width: 500px; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .category-tabs { display: flex; gap: 8px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 75px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 5px; border-radius: 12px; cursor: pointer; text-align: center; transition: 0.3s; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); box-shadow: 0 4px 15px rgba(225, 29, 72, 0.2); }
        .cat-item img { width: 24px; height: 24px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 9px; font-weight: 900; color: #fff; display: block; }

        /* ستايل الكروت الجديد */
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); margin-bottom: 20px; position: relative; }
        
        /* قسم معلومات المباراة أعلى الكرت */
        .card-match-header { padding: 12px; background: linear-gradient(to bottom, rgba(225,29,72,0.1), transparent); border-bottom: 1px solid var(--glass-border); text-align: center; }
        .m-info-title { font-size: 11px; color: var(--main); font-weight: 900; margin-bottom: 8px; display: block; opacity: 0.9; }
        .m-teams-display { display: flex; align-items: center; justify-content: center; gap: 15px; }
        .m-team-name { font-size: 13px; font-weight: 700; color: #fff; }
        .m-vs { font-size: 10px; background: var(--main); color: #fff; padding: 2px 6px; border-radius: 5px; font-weight: 900; }

        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; }
        .video-box iframe { position: absolute; top:0; left:0; width: 100%; height: 100%; border: none; }

        /* أزرار التحكم أسفل الكرت */
        .card-footer-links { padding: 12px; display: flex; flex-direction: column; gap: 8px; background: rgba(0,0,0,0.2); }
        .link-row { display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 10px; border: 1px solid var(--glass-border); cursor: pointer; transition: 0.2s; }
        .link-row:hover { background: rgba(255,255,255,0.08); }
        .link-info { display: flex; align-items: center; gap: 8px; }
        .link-info i { color: var(--main); font-size: 14px; }
        .link-name { font-size: 12px; font-weight: 700; color: #e2e8f0; }
        .link-status { font-size: 10px; color: #22c55e; font-weight: 900; display: flex; align-items: center; gap: 4px; }
        .link-status::before { content: ''; width: 6px; height: 6px; background: #22c55e; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite; }

        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

        .channel-section { display: none; padding: 15px; }
        .channel-section.active { display: block; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* باقي الستايليات */
        .social-links { display: flex; justify-content: space-between; gap: 5px; margin-bottom: 15px; padding: 0 10px; }
        .social-btn { padding: 7px 5px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; flex: 1; border: 1.5px solid #ffffff; text-align: center; }
        .btn-wa { background: #25d366; } .btn-tg { background: #0088cc; }
        .news-ticker { background: rgba(225, 29, 72, 0.15); height: 32px; overflow: hidden; margin-bottom: 10px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 45s linear infinite; }
        .ticker-text { color: #fff; font-size: 12px; font-weight: 700; padding: 0 60px; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }
        footer { text-align: center; padding: 30px; font-size: 9px; opacity: 0.4; }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #050c14; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 9999; transition: 0.8s; }
        .intro-hide { opacity: 0; visibility: hidden; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="loader-content" style="text-align:center;">
        <i class="fas fa-play-circle" style="font-size:50px; color:var(--main);"></i>
        <h2 style="color:white; margin-top:20px;">الخدمة الرقمية</h2>
    </div>
</div>

<div class="main-container">
    <div class="header-fixed">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg">تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" style="background:#FFFC00; color:#000;" class="social-btn">سناب</a>
            <a href="https://x.com/d_service_pro" style="background:#000;" class="social-btn">منصة X</a>
        </div>

        <?php if(($news['status'] ?? 'hide') == 'show'): ?>
        <div class="news-ticker"><span class="ticker-label">تنبيه</span><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span></div></div>
        <?php endif; ?>

        <div class="category-tabs">
            <div class="cat-item active" onclick="switchSection('important_matches', this)">
                <img src="https://cdn-icons-png.flaticon.com/512/833/833593.png" style="filter: brightness(0) invert(1);">
                <span>مباريات مهمة</span>
            </div>
            <div class="cat-item" onclick="switchSection('all_table', this)">
                <img src="https://cdn-icons-png.flaticon.com/512/3592/3592518.png" style="filter: brightness(0) invert(1);">
                <span>جدول المباريات</span>
            </div>
            <?php foreach($active_sections as $s): ?>
                <div class="cat-item" onclick="switchSection('<?= $s['key'] ?>', this)">
                    <img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="section-important_matches" class="channel-section active">
        <?php 
        $important = filterSection($all_channels, 'important'); // افترضنا أن القسم اسمه important في الـ admin
        if(empty($important)): echo '<p style="text-align:center; opacity:0.5;">لا توجد مباريات جارية حالياً</p>';
        else: foreach($important as $ch): 
        ?>
        <div class="card">
            <div class="card-match-header">
                <span class="m-info-title"><?= $ch['match_title'] ?: 'بث مباشر للمباراة' ?></span>
                <div class="m-teams-display">
                    <span class="m-team-name"><?= $ch['team_home'] ?: 'الفريق الأول' ?></span>
                    <span class="m-vs">VS</span>
                    <span class="m-team-name"><?= $ch['team_away'] ?: 'الفريق الثاني' ?></span>
                </div>
            </div>

            <div class="video-box" id="vid-<?= $ch['id'] ?>" style="background-image:url('mg/wel.GIF'); background-size:cover;"></div>
            
            <div class="card-footer-links">
                <div class="link-row" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', '<?= $ch['name'] ?>')">
                    <div class="link-info">
                        <i class="fas fa-play-circle"></i>
                        <span class="link-name"><?= $ch['link_name'] ?: 'تشغيل القناة: ' . $ch['name'] ?></span>
                    </div>
                    <span class="link-status">متصل</span>
                </div>

                <?php if(!empty($ch['file_backup'])): ?>
                <div class="link-row" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file_backup'] ?>', '<?= $ch['name'] ?> (احتياطي)')">
                    <div class="link-info">
                        <i class="fas fa-shield-alt"></i>
                        <span class="link-name"><?= $ch['link_backup_name'] ?: 'سيرفر احتياطي سريع' ?></span>
                    </div>
                    <span class="link-status" style="color:#0ea5e9; border-color:#0ea5e9;">احتياطي</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <div id="section-all_table" class="channel-section">
        <?php foreach($ordered_matches as $code => $matches_list): ?>
            <div style="background:rgba(255,255,255,0.05); padding:8px 15px; border-radius:10px; font-size:11px; font-weight:900; margin:10px 0; border-right:4px solid var(--main);">
                <?= $leagues_map_new[$code]['name'] ?>
            </div>
            <?php foreach($matches_list as $m): ?>
                <div class="card" style="padding:15px; display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <span style="font-size:11px; flex:1; text-align:right;"><?= $m['homeTeam']['name'] ?></span>
                    <span style="background:rgba(225,29,72,0.1); color:var(--main); padding:4px 10px; border-radius:5px; font-size:12px; font-weight:900;">
                        <?= $m['status'] == 'TIMED' ? date("H:i", strtotime($m['utcDate'])) : ($m['score']['fullTime']['home'].'-'.$m['score']['fullTime']['away']) ?>
                    </span>
                    <span style="font-size:11px; flex:1; text-align:left;"><?= $m['awayTeam']['name'] ?></span>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <?php foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
    <div id="section-<?= $s['key'] ?>" class="channel-section">
        <?php foreach($channels as $ch): ?>
            <div class="card">
                <div class="video-box" id="vid-<?= $ch['id'] ?>" style="background-image:url('mg/wel.GIF'); background-size:cover;"></div>
                <div class="card-footer-links">
                    <div class="link-row" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', '<?= $ch['name'] ?>')">
                        <div class="link-info"><i class="fas fa-tv"></i><span class="link-name">بث مباشر: <?= $ch['name'] ?></span></div>
                        <span class="link-status">يعمل</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <footer>جميع الحقوق محفوظة - الخدمة الرقمية © 2026</footer>
</div>

<script>
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

function startStream(boxId, file, channelName) {
    let vBox = document.getElementById(boxId);
    vBox.style.backgroundImage = "none";
    vBox.innerHTML = `<iframe src="${file}?autoplay=1&muted=0" allowfullscreen></iframe>`;
}

window.addEventListener('load', () => { 
    setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); 
});
</script>
</body>
</html>
