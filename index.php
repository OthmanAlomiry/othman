<?php
session_start();
error_reporting(0);

// --- بيانات السحابة ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// --- نظام عداد المتواجدين ---
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

// --- دالة جلب البيانات السحابية ---
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
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - الخدمة الرقمية</title>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2760895204432673" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --blue-grad: linear-gradient(45deg, #0ea5e9, #fff); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 300px; color: #e2e8f0; overflow-x: hidden; }
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 3000; transition: 1s ease-in-out; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.1); }
        .intro-icon { font-size: 60px; color: var(--main); animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); text-shadow: 0 0 30px var(--main); } }
        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .bg-pattern::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }
        .header-fixed { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 5px 0; text-align: center; }
        .online-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 3px 12px; border-radius: 50px; color: #22c55e; font-size: 9px; font-weight: 900; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 5px; }
        .promo-text { font-size: 10px; font-weight: 700; color: #fff; margin-bottom: 5px; }
        .social-links { display: flex; justify-content: center; gap: 6px; margin-bottom: 8px; flex-wrap: wrap; }
        .social-btn { padding: 5px 12px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 9px; color: #fff; display: flex; align-items: center; gap: 4px; }
        .ad-spot { width: 100%; max-width: 728px; margin: 2px auto; min-height: 40px; }
        
        /* تصميم شريط الأخبار المتحرك */
        .news-ticker { background: rgba(225, 29, 72, 0.15); border-top: 1px solid rgba(225, 29, 72, 0.3); border-bottom: 1px solid rgba(225, 29, 72, 0.3); padding: 6px 0; overflow: hidden; white-space: nowrap; margin-top: 5px; }
        .news-ticker marquee { color: #fff; font-size: 11px; font-weight: 700; }
        .ticker-label { background: var(--main); color: #fff; padding: 2px 10px; border-radius: 4px; font-size: 10px; margin-left: 10px; font-weight: 900; display: inline-block; position: relative; z-index: 2; }

        .category-tabs { display: flex; gap: 10px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 5px 0; }
        .cat-item { min-width: 75px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 4px; border-radius: 15px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 30px; height: 30px; object-fit: contain; margin-bottom: 3px; }
        .cat-item span { font-size: 9px; font-weight: 900; color: #fff; display: block; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; padding: 15px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; }
        .channel-section.active { display: grid; animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .name-badge { padding: 4px 12px; border-radius: 8px; font-size: 10px; font-weight: 900; color: #000; background: var(--blue-grad); }
        .play-btn { width: 85%; margin: 15px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 10px; border-radius: 50px; font-weight: 900; cursor: pointer; font-size: 11px; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
        footer { text-align: center; padding: 30px; font-size: 10px; opacity: 0.5; }
    </style>
</head>
<body>
<div id="pro-intro"><div class="intro-icon"><i class="fas fa-play-circle"></i></div><h2 style="color:white; font-weight:900; font-size: 1.2rem;">الخدمة الرقمية</h2></div>
<div class="bg-pattern"></div>
<div class="header-fixed">
    <div class="online-badge"><span>● متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div>
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366">واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc">تليجرام</a>
        <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000">سناب</a>
        <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000">تويتر</a>
    </div>
    <div class="ad-spot"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2760895204432673" crossorigin="anonymous"></script></div>

    <?php if($news['status'] == 'show'): ?>
    <div class="news-ticker">
        <span class="ticker-label">تنبيهات</span>
        <marquee behavior="scroll" direction="right" scrollamount="6"><?= $news['text'] ?></marquee>
    </div>
    <?php endif; ?>

    <div class="category-tabs">
        <?php $count = 0; foreach($active_sections as $s): ?>
            <div class="cat-item <?= ($count == 0 ? 'active' : '') ?>" onclick="switchSection('<?= $s['key'] ?>', this)">
                <img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span>
            </div>
        <?php $count++; endforeach; ?>
    </div>
</div>

<div class="grid">
    <?php $count = 0; foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
    <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($count == 0 ? 'active' : '') ?>">
        <?php if(empty($channels)): ?><div style="grid-column: 1/-1; text-align:center; padding:60px; opacity:0.3;"><p>لا توجد قنوات حالياً.</p></div><?php endif; ?>
        <?php foreach($channels as $ch): ?>
        <div class="card"><div class="c-head"><div class="name-badge"><?= $ch['name'] ?></div><div style="color:#ff4d4d; animation: blink 1s infinite; font-weight:900; font-size:9px;">● مباشر</div></div><div class="video-box" id="vid-<?= $ch['id'] ?>"></div><button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث</button></div>
        <?php endforeach; ?>
    </div>
    <?php $count++; endforeach; ?>
</div>
<footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>
<script>
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 1500); });
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    let target = document.getElementById('section-' + id);
    if(target) target.classList.add('active');
    element.classList.add('active');
}
function startStream(boxId, file, btn) {
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
    btn.innerText = "تم الاتصال";
}
setInterval(() => { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => { document.getElementById('realtime-visitors').innerText = count; }); }, 4000);
</script>
</body>
</html>
