<?php
session_start();
// --- نظام العداد ---
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

// --- جلب القنوات من قاعدة البيانات عثمان ---
$db_file = 'channels_db.json';
$channels_db = file_exists($db_file) ? json_decode(file_get_contents($db_file), true) : ['custom_channels' => []];

function getChannelsBySection($section, $db) {
    return array_filter($db['custom_channels'], function($ch) use ($section) {
        return ($ch['section'] == $section && $ch['status'] == 'show');
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
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 310px; color: #e2e8f0; }
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 10px 0; text-align: center; }
        .online-count-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 3px 15px; border-radius: 50px; color: #22c55e; font-size: 10px; font-weight: 900; display: inline-flex; align-items: center; gap: 5px; animation: badgePulse 2s infinite; margin-bottom: 10px; }
        @keyframes badgePulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
        .social-links { display: flex; justify-content: center; gap: 8px; margin-bottom: 15px; }
        .social-btn { padding: 7px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; border: 1px solid rgba(255,255,255,0.15); display: flex; align-items: center; gap: 5px; }
        .category-tabs { display: flex; gap: 12px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 80px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 10px 5px; border-radius: 15px; cursor: pointer; transition: 0.3s; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: translateY(-3px); }
        .cat-item img { width: 40px; height: 40px; object-fit: contain; }
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; }
        .channel-section.active { display: grid; animation: fadeIn 0.5s; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        .c-head { padding: 15px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-container { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
    </style>
</head>
<body>
<div class="bg-pattern-animated"></div>
<div class="header-fixed-container">
    <div class="online-count-badge"><span>متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div>
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366">واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc">تليجرام</a>
    </div>
    <div class="category-tabs">
        <div class="cat-item active" onclick="switchSection('bein', this)"><img src="mg/bein.png"><span>beIN</span></div>
        <div class="cat-item" onclick="switchSection('mbc', this)"><img src="mg/mbc.png"><span>MBC</span></div>
        <div class="cat-item" onclick="switchSection('alkas', this)"><img src="mg/alkas.png"><span>الكاس</span></div>
        <div class="cat-item" onclick="switchSection('kuwait', this)"><img src="mg/ku.png"><span>الكويت</span></div>
    </div>
</div>

<div class="grid">
    <?php 
    $sections = ['bein', 'mbc', 'alkas', 'kuwait', 'shahad', 'moc'];
    foreach($sections as $sec): 
        $active_class = ($sec == 'bein') ? 'active' : '';
        $channels = getChannelsBySection($sec, $channels_db);
    ?>
    <div id="section-<?php echo $sec; ?>" class="channel-section <?php echo $active_class; ?>">
        <?php foreach($channels as $ch): ?>
        <div class="card">
            <div class="c-head"><div style="background:var(--blue-grad); padding:5px 12px; border-radius:8px; color:#000; font-weight:900; font-size:11px;"><?php echo $ch['name']; ?></div></div>
            <div class="video-container" id="cont-<?php echo $ch['id']; ?>"><video playsinline controls></video></div>
            <button class="play-btn" onclick="forceIframePlay('cont-<?php echo $ch['id']; ?>', '<?php echo $ch['file']; ?>', this)">تشغيل البث</button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}
function forceIframePlay(contId, file, btn) {
    document.getElementById(contId).innerHTML = `<iframe src="${file}" allowfullscreen allow="autoplay"></iframe>`;
    btn.innerText = "تم التشغيل";
}
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => { document.getElementById('realtime-visitors').innerText = count; }); }, 4000);
</script>
</body>
</html>
