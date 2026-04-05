<?php
session_start();
error_reporting(0);

// --- بيانات السحابة الخاصة بك عثمان ---
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

// --- دالة جلب البيانات السحابية الكاملة ---
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
    return array_filter($channels, function($c) use ($sec) { 
        return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec))); 
    });
}
date_default_timezone_set('Asia/Riyadh');
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
        :root { 
            --main: #e11d48; --bg-deep: #050c14; 
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.15);
            --blue-grad: linear-gradient(45deg, #0ea5e9, #fff);
        }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 240px; color: #e2e8f0; overflow-x: hidden; }

        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 3000; transition: 1s ease-in-out; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.1); }
        .intro-icon { font-size: 80px; color: var(--main); animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); text-shadow: 0 0 30px var(--main); } }

        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .bg-pattern::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }

        .header-fixed { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 10px 0; text-align: center; }
        .online-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 4px 14px; border-radius: 50px; color: #22c55e; font-size: 9px; font-weight: 900; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 8px; }
        .promo-text { font-size: 10px; font-weight: 700; color: #fff; margin-bottom: 8px; }

        .social-links { display: flex; justify-content: center; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
        .social-btn { padding: 6px 14px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; display: flex; align-items: center; gap: 5px; transition: 0.3s; }
        .social-btn:hover { transform: translateY(-2px); }

        /* تعديل الشريط الإخباري: سرعة هادئة، مقاس أصغر، انسيابية تامة عثمان */
        .news-ticker { background: rgba(225, 29, 72, 0.15); border-top: 1px solid rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.05); height: 35px; overflow: hidden; margin-bottom: 8px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; box-shadow: 5px 0 15px rgba(0,0,0,0.5); }
        .ticker-wrap { flex: 1; overflow: hidden; direction: rtl; position: relative; width: 100%; height: 100%; display: flex; align-items: center; padding-right: 80px; }
        .ticker-move { display: inline-block; white-space: nowrap; animation: ticker-scroll-rtl 40s linear infinite; }
        .ticker-text { color: #fff; font-size: 13px; font-weight: 700; padding: 0 60px; display: inline-block; }
        
        @keyframes ticker-scroll-rtl {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        .category-tabs { display: flex; gap: 10px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 8px 0; }
        .category-tabs::-webkit-scrollbar { display: none; }
        .cat-item { min-width: 70px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 15px; cursor: pointer; text-align: center; transition: 0.4s; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: scale(1.03); }
        .cat-item img { width: 28px; height: 28px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 9px; font-weight: 900; color: #fff; display: block; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; margin-top: 0px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .channel-section.active { display: grid; animation: slideUp 0.6s ease-out; }
        .card { background: var(--glass); border-radius: 25px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); }
        .c-head { padding: 15px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .name-badge { padding: 5px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; color: #000; background: var(--blue-grad); }
        .play-btn { width: 90%; margin: 20px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
        footer { text-align: center; padding: 50px; font-size: 11px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="intro-icon"><i class="fas fa-play-circle"></i></div>
    <h2 style="color:white; font-weight:900;">الخدمة الرقمية</h2>
</div>

<div class="bg-pattern"></div>

<div class="header-fixed">
    <div class="online-badge"><span>● متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div>
    <div class="promo-text">للاشتراك في الباقة كاملة تواصل معنا عبر:</div>
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a>
        <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a>
        <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000"><i class="fab fa-x-twitter"></i> تويتر</a>
    </div>

    <?php if($news['status'] == 'show'): ?>
    <div class="news-ticker">
        <span class="ticker-label">تنبيهات</span>
        <div class="ticker-wrap">
            <div class="ticker-move">
                <span class="ticker-text"><?= $news['text'] ?></span>
                <span class="ticker-text"><?= $news['text'] ?></span>
            </div>
        </div>
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
    <?php $count = 0; foreach($active_sections as $s): 
        $channels = filterSection($all_channels, $s['key']);
    ?>
    <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($count == 0 ? 'active' : '') ?>">
        <?php if(empty($channels)): ?>
            <div style="grid-column: 1/-1; text-align:center; padding:80px; opacity:0.3;"><p>لا توجد قنوات حالياً.</p></div>
        <?php endif; ?>
        <?php foreach($channels as $ch): ?>
        <div class="card">
            <div class="c-head">
                <div class="name-badge"><?= $ch['name'] ?></div>
                <div style="color:#ff4d4d; animation: blink 1s infinite; font-weight:900; font-size:10px;">● مباشر</div>
            </div>
            <div class="video-box" id="vid-<?= $ch['id'] ?>"></div>
            <button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث</button>
        </div>
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
