<?php
session_start();

// --- إعدادات السحابة الخاصة بك (JSONbin) عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// --- نظام عداد المتواجدين الحقيقي ---
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

// --- دالة جلب القنوات من السحابة لضمان عدم الحذف ---
function getChannelsCloud($section, $bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    $allChannels = isset($data['custom_channels']) ? $data['custom_channels'] : [];
    
    return array_filter($allChannels, function($c) use ($section) {
        return ($c['section'] == $section);
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
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 310px; color: #e2e8f0; overflow-x: hidden; }

        /* الصفحة الترحيبية */
        #pro-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 3000; transition: 1s ease-in-out; }
        .intro-hide { opacity: 0; visibility: hidden; transform: scale(1.1); }
        .intro-icon { font-size: 80px; color: var(--main); animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); text-shadow: 0 0 30px var(--main); } }
        @keyframes load { from { width: 0%; } to { width: 100%; } }

        /* الخلفية المتحركة */
        .bg-pattern { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .bg-pattern::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }

        /* الهيدر */
        .header-fixed { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 10px 0; text-align: center; }
        .online-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 5px 15px; border-radius: 50px; color: #22c55e; font-size: 10px; font-weight: 900; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 10px; }
        .promo-text { font-size: 11px; font-weight: 700; color: #fff; margin-bottom: 10px; }

        /* أزرار التواصل */
        .social-links { display: flex; justify-content: center; gap: 8px; margin-bottom: 15px; flex-wrap: wrap; }
        .social-btn { padding: 7px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; display: flex; align-items: center; gap: 5px; transition: 0.3s; }

        /* الأقسام */
        .category-tabs { display: flex; gap: 12px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 10px 0; }
        .cat-item { min-width: 85px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 12px 5px; border-radius: 20px; cursor: pointer; text-align: center; transition: 0.4s; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 38px; height: 38px; object-fit: contain; margin-bottom: 5px; }
        .cat-item span { font-size: 10px; font-weight: 900; color: #fff; display: block; }

        /* البطاقات */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .channel-section.active { display: grid; animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); border-radius: 25px; overflow: hidden; border: 1px solid var(--glass-border); }
        .c-head { padding: 15px; background: rgba(0,0,0,0.4); display: flex; justify-content: space-between; align-items: center; }
        .name-badge { padding: 5px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; color: #000; }
        
        .play-btn { width: 90%; margin: 20px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 50px; font-weight: 900; cursor: pointer; transition: 0.3s; }
        .play-btn:hover { background: rgba(255,255,255,0.15); }

        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
        @keyframes blink { 50% { opacity: 0.1; } }
        footer { text-align: center; padding: 50px; font-size: 11px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-intro">
    <div class="intro-icon"><i class="fas fa-play-circle"></i></div>
    <h2 style="color:white; font-weight:900;">الخدمة الرقمية</h2>
    <div style="width:150px; height:3px; background:rgba(255,255,255,0.1); border-radius:10px; margin-top:20px; overflow:hidden;">
        <div style="width:100%; height:100%; background:var(--main); animation: load 2s forwards;"></div>
    </div>
</div>

<div class="bg-pattern"></div>

<div class="header-fixed">
    <div class="online-badge">
        <div style="width:7px; height:7px; background:#22c55e; border-radius:50%; animation: blink 1.5s infinite;"></div>
        <span>متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span>
    </div>

    <div class="promo-text">للاشتراك في الباقة كاملة تواصل معنا عبر:</div>

    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a>
        <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a>
        <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000"><i class="fab fa-x-twitter"></i> تويتر</a>
    </div>

    <div class="category-tabs">
        <div class="cat-item active" onclick="switchSection('bein', this)"><img src="mg/bein.png"><span>beIN Sport</span></div>
        <div class="cat-item" onclick="switchSection('shahad', this)"><img src="mg/shahd.png"><span>شاهد</span></div>
        <div class="cat-item" onclick="switchSection('mbc', this)"><img src="mg/mbc.png"><span>باقة MBC</span></div>
        <div class="cat-item" onclick="switchSection('alkas', this)"><img src="mg/alkas.png"><span>الكاس</span></div>
        <div class="cat-item" onclick="switchSection('on', this)"><img src="mg/on.png"><span>On Sport</span></div>
        <div class="cat-item" onclick="switchSection('ado', this)"><img src="mg/ado.png"><span>أبوظبي</span></div>
        <div class="cat-item" onclick="switchSection('dubai', this)"><img src="mg/du.png"><span>دبي</span></div>
        <div class="cat-item" onclick="switchSection('kuwait', this)"><img src="mg/ku.png"><span>الكويت</span></div>
        <div class="cat-item" onclick="switchSection('star', this)"><img src="mg/star.png"><span>STARZPLAY</span></div>
        <div class="cat-item" onclick="switchSection('moc', this)"><img src="mg/moc.png"><span>المغربية</span></div>
        <div class="cat-item" onclick="switchSection('sky', this)"><img src="mg/sky.png"><span>Sky</span></div>
        <div class="cat-item" onclick="switchSection('plus', this)"><img src="mg/plus.png"><span>Canal+</span></div>
    </div>
</div>

<div class="grid">
    <?php 
    $sections = ['bein', 'shahad', 'mbc', 'alkas', 'on', 'ado', 'dubai', 'kuwait', 'star', 'moc', 'sky', 'plus'];
    foreach($sections as $sec): 
        $channels = getChannelsCloud($sec, $BIN_ID, $API_KEY);
    ?>
    <div id="section-<?php echo $sec; ?>" class="channel-section <?php echo ($sec == 'bein' ? 'active' : ''); ?>">
        <?php if(empty($channels)): ?>
            <div style="grid-column: 1/-1; text-align:center; padding:80px; opacity:0.3;">
                <i class="fas fa-tv" style="font-size:40px; margin-bottom:10px;"></i>
                <p>لا توجد قنوات حالياً.</p>
            </div>
        <?php endif; ?>
        
        <?php foreach($channels as $ch): ?>
        <div class="card">
            <div class="c-head">
                <div class="name-badge" style="background:var(--blue-grad)"><?php echo $ch['name']; ?></div>
                <div style="color:#ff4d4d; animation: blink 1s infinite; font-weight:900; font-size:10px;">● مباشر</div>
            </div>
            <div class="video-box" id="vid-<?php echo $ch['id']; ?>"></div>
            <button class="play-btn" onclick="startStream('vid-<?php echo $ch['id']; ?>', '<?php echo $ch['file']; ?>', this)">تشغيل البث</button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>

<script>
window.addEventListener('load', () => {
    setTimeout(() => { document.getElementById('pro-intro').classList.add('intro-hide'); }, 2000);
});

function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

function startStream(boxId, file, btn) {
    const container = document.getElementById(boxId);
    container.innerHTML = ""; 
    const iframe = document.createElement('iframe');
    // التشغيل التلقائي مع الصمت لتجاوز حماية المتصفحات عثمان
    iframe.src = file + (file.includes('?') ? '&' : '?') + "autoplay=1&muted=1";
    iframe.allow = "autoplay; encrypted-media";
    iframe.allowFullscreen = true;
    iframe.style.width = "100%";
    iframe.style.height = "100%";
    iframe.style.border = "none";
    container.appendChild(iframe);
    btn.innerText = "جاري البث...";
}

setInterval(() => {
    fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => {
        if(count && !isNaN(count)) document.getElementById('realtime-visitors').innerText = count;
    });
}, 4000);
</script>
</body>
</html>
