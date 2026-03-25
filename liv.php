<?php
// --- 1. نظام عداد المتواجدين الحقيقي المدمج ---
session_start();
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

date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>الخدمة الرقمية - البث المباشر</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { 
            --main: #e11d48; --bg-deep: #050c14; --whatsapp: #25d366; 
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.15);
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff); 
            --green-grad: linear-gradient(45deg, #16a34a, #fff);
            --blue-grad: linear-gradient(45deg, #0ea5e9, #fff);
        }
        
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 310px; overflow-x: hidden; color: #e2e8f0; }

        .header-fixed-container { 
            position: fixed; top: 0; left: 0; right: 0; width: 100%; z-index: 1000;
            background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); 
            border-bottom: 1px solid var(--glass-border); padding: 10px 0;
            display: flex; flex-direction: column; align-items: center; text-align: center;
        }

        .online-count-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 3px 15px; border-radius: 50px; color: #22c55e; font-size: 9px; font-weight: 900; display: flex; align-items: center; gap: 5px; animation: badgePulse 2s infinite; }
        @keyframes badgePulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); box-shadow: 0 0 10px rgba(34, 197, 94, 0.2); } 100% { transform: scale(1); } }
        .dot-blink { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: blink 1.5s infinite; }

        .social-links { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; margin-bottom: 10px; }
        .social-btn { padding: 6px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; border: 1px solid rgba(255,255,255,0.15); transition: 0.3s; display: flex; align-items: center; gap: 5px; }

        .category-tabs { display: flex; justify-content: flex-start; gap: 12px; width: 95%; overflow-x: auto; scrollbar-width: none; padding: 5px 0; margin-top: 10px; }
        .category-tabs::-webkit-scrollbar { display: none; }
        .cat-item { min-width: 80px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 10px 5px; border-radius: 15px; cursor: pointer; transition: 0.3s; display: flex; flex-direction: column; align-items: center; gap: 6px; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: translateY(-3px); }
        .cat-item img { width: 40px; height: 40px; object-fit: contain; border-radius: 8px; }
        .cat-item span { font-size: 9px; font-weight: 900; color: #fff; white-space: nowrap; }

        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; }
        .channel-section.active { display: grid; animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); backdrop-filter: blur(20px); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); transition: 0.3s; }
        .c-head { padding: 12px 18px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; }
        .name-box-blue { background: var(--blue-grad); padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        .name-box-purple { background: var(--purple-grad); padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        .name-box-green { background: var(--green-grad); padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        
        .play-btn-premium { width: 90%; margin: 15px auto; display: flex; justify-content: center; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; font-size: 13px; cursor: pointer; animation: glassGlow 3s infinite; }
        @keyframes glassGlow { 0%, 100% { box-shadow: 0 0 5px rgba(255,255,255,0.05); } 50% { box-shadow: 0 0 20px rgba(255,255,255,0.15); } }

        .video-container { width: 100%; aspect-ratio: 16/9; background: #000; position: relative; overflow: hidden; }
        video, iframe { width: 100%; height: 100%; border: none; display: block; }
        
        @keyframes blink { 50% { opacity: 0.1; } }
        footer { text-align: center; padding: 40px; font-size: 11px; opacity: 0.5; }
    </style>
</head>
<body>

<div class="bg-pattern-animated"></div>

<div class="header-fixed-container">
    <div class="top-header-row">
        <div class="online-count-badge"><div class="dot-blink"></div><span>متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div>
    </div>
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a>
        <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a>
    </div>

    <div class="category-tabs">
        <div class="cat-item active" onclick="switchSection('bein', this)"><img src="mg/bein.png"><span>beIN Sport</span></div>
        <div class="cat-item" onclick="switchSection('shahad', this)"><img src="mg/shahd.png"><span>شاهد</span></div>
        <div class="cat-item" onclick="switchSection('star', this)"><img src="mg/star.png"><span>STARZ</span></div>
        <div class="cat-item" onclick="switchSection('mbc', this)"><img src="mg/mbc.png"><span>MBC</span></div>
        <div class="cat-item" onclick="switchSection('alkas', this)"><img src="mg/alkas.png"><span>الكاس</span></div>
        <div class="cat-item" onclick="switchSection('ado', this)"><img src="mg/ado.png"><span>أبوظبي</span></div>
        <div class="cat-item" onclick="switchSection('on', this)"><img src="mg/on.png"><span>On sport</span></div>
        <div class="cat-item" onclick="switchSection('dubai', this)"><img src="mg/du.png"><span>دبي الرياضية</span></div>
        <div class="cat-item" onclick="switchSection('kuwait', this)"><img src="mg/ku.png"><span>الكويت</span></div>
        <div class="cat-item" onclick="switchSection('moc', this)"><img src="mg/moc.png"><span>المغربية</span></div>
        <div class="cat-item" onclick="switchSection('sky', this)"><img src="mg/sky.png"><span>sky</span></div>
        <div class="cat-item" onclick="switchSection('plus', this)"><img src="mg/plus.png"><span>canal</span></div>
        <div class="cat-item" onclick="switchSection('sporttv', this)"><img src="mg/sp.png"><span>sport tv</span></div>
    </div>
</div>

<div class="grid">
    <div id="section-bein" class="channel-section active">
        <?php for($i = 1; $i <= 9; $i++): ?>
        <div class="card">
            <div class="c-head"><div class="name-box-purple">beIN Sport <?php echo $i; ?></div><div style="color:#ff4d4d; animation: blink 1s infinite; font-weight:900; font-size:9px;">● مباشر</div></div>
            <div class="video-container" id="cont-vid<?php echo $i; ?>"><video id="vid<?php echo $i; ?>" playsinline controls></video></div>
            <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', this)"><span>تشغيل البث</span></button>
        </div>
        <?php endfor; ?>
    </div>

    <div id="section-mbc" class="channel-section">
        <?php 
        $mbc_channels = [['n'=>'MBC 1','f'=>'mb1.php'], ['n'=>'MBC Action','f'=>'b12.php'], ['n'=>'MBC مصر 1','f'=>'mbe1.php'], ['n'=>'MBC مصر 2','f'=>'mbe2.php']];
        foreach($mbc_channels as $idx => $ch): ?>
        <div class="card">
            <div class="c-head"><div class="name-box-blue"><?php echo $ch['n']; ?></div><div class="dot-blink"></div></div>
            <div class="video-container" id="cont-mbc<?php echo $idx; ?>"><video playsinline controls></video></div>
            <button class="play-btn-premium" onclick="forceIframePlay('cont-mbc<?php echo $idx; ?>', '<?php echo $ch['f']; ?>', this)"><span>تشغيل البث المباشر</span></button>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="section-shahad" class="channel-section">
        <div class="card"><div class="c-head"><div class="name-box-blue">شاهد الرياضية 1</div></div><div class="video-container" id="cont-sh1"><video playsinline controls></video></div><button class="play-btn-premium" onclick="forceIframePlay('cont-sh1', 'b13.php', this)"><span>تشغيل</span></button></div>
        <div class="card"><div class="c-head"><div class="name-box-blue">شاهد الرياضية 2</div></div><div class="video-container" id="cont-sh2"><video playsinline controls></video></div><button class="play-btn-premium" onclick="forceIframePlay('cont-sh2', 'b14.php', this)"><span>تشغيل</span></button></div>
    </div>

    <div id="section-star" class="channel-section">
        <div class="card"><div class="c-head"><div class="name-box-green">STARZPLAY 1</div></div><div class="video-container" id="cont-st1"><video playsinline controls></video></div><button class="play-btn-premium" onclick="robustPlay('vidst1', 'b10.php', this)"><span>تشغيل</span></button></div>
        <div class="card"><div class="c-head"><div class="name-box-green">STARZPLAY 2</div></div><div class="video-container" id="cont-st2"><video playsinline controls></video></div><button class="play-btn-premium" onclick="robustPlay('vidst2', 'b11.php', this)"><span>تشغيل</span></button></div>
    </div>

    <div id="section-alkas" class="channel-section">
        <?php 
        $kas_channels = [['n'=>'SHOOF 1','f'=>'sh1.php'], ['n'=>'SHOOF 2','f'=>'sh2.php'],['n'=>'الكاس 1','f'=>'k1.php'],['n'=>'الكاس 4','f'=>'k4.php'],['n'=>'الكاس 5','f'=>'k5.php'],['n'=>'الكاس 6','f'=>'k6.php'],['n'=>'الكاس 7','f'=>'k7.php']];
        foreach($kas_channels as $idx => $ch): ?>
        <div class="card">
            <div class="c-head"><div class="name-box-purple"><?php echo $ch['n']; ?></div><div class="dot-blink"></div></div>
            <div class="video-container" id="cont-kas<?php echo $idx; ?>"><video playsinline controls></video></div>
            <button class="play-btn-premium" onclick="forceIframePlay('cont-kas<?php echo $idx; ?>', '<?php echo $ch['f']; ?>', this)"><span>تشغيل البث</span></button>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="section-kuwait" class="channel-section">
        <?php 
        $ku_channels = [['n'=>'KTV Sport','f'=>'ku1.php'], ['n'=>'KTV Sport Plus','f'=>'ku2.php']];
        foreach($ku_channels as $idx => $ch): ?>
        <div class="card">
            <div class="c-head"><div class="name-box-blue"><?php echo $ch['n']; ?></div><div class="dot-blink"></div></div>
            <div class="video-container" id="cont-ku<?php echo $idx; ?>"><video playsinline controls></video></div>
            <button class="play-btn-premium" onclick="forceIframePlay('cont-ku<?php echo $idx; ?>', '<?php echo $ch['f']; ?>', this)"><span>تشغيل البث</span></button>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="section-moc" class="channel-section">
        <?php 
        $ma_channels = [['n'=>'2M','f'=>'2m.php'],['n'=>'2M Monde +1','f'=>'2m2.php'],['n'=>'Al Aoula Inter','f'=>'aou.php'],['n'=>'Al Maghribia','f'=>'mg.php'],['n'=>'Arryadia','f'=>'arr.php']];
        foreach($ma_channels as $idx => $ch): ?>
        <div class="card">
            <div class="c-head"><div class="name-box-green"><?php echo $ch['n']; ?></div><div class="dot-blink"></div></div>
            <div class="video-container" id="cont-ma<?php echo $idx; ?>"><video playsinline controls></video></div>
            <button class="play-btn-premium" onclick="forceIframePlay('cont-ma<?php echo $idx; ?>', '<?php echo $ch['f']; ?>', this)"><span>تشغيل البث</span></button>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="section-dubai" class="channel-section">
        <div class="card"><div class="c-head"><div class="name-box-green">دبي الرياضية 1</div></div><div class="video-container" id="cont-du1"><video playsinline controls></video></div><button class="play-btn-premium" onclick="forceIframePlay('cont-du1', 'd1.php', this)"><span>تشغيل</span></button></div>
        <div class="card"><div class="c-head"><div class="name-box-green">دبي الرياضية 2</div></div><div class="video-container" id="cont-du2"><video playsinline controls></video></div><button class="play-btn-premium" onclick="forceIframePlay('cont-du2', 'd2.php', this)"><span>تشغيل</span></button></div>
    </div>
</div>

<footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية © 2026</footer>

<script>
function updateRealtimeVisitors() { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => { if(count && !isNaN(count)) document.getElementById('realtime-visitors').innerText = count; }); }
setInterval(updateRealtimeVisitors, 4000);

function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    const target = document.getElementById('section-' + id);
    if(target) { target.classList.add('active'); element.classList.add('active'); }
}

function forceIframePlay(contId, file, btn) {
    const container = document.getElementById(contId);
    btn.innerText = "جاري الاتصال بالسيرفر...";
    container.innerHTML = `<iframe src="${file}" allowfullscreen allow="autoplay; encrypted-media"></iframe>`;
    btn.innerText = "تم التشغيل بنجاح";
}

function robustPlay(vId, p, btn) {
    const video = document.getElementById(vId);
    const btnText = btn.querySelector('span') || btn;
    btnText.innerText = "جاري التحميل...";
    if (video.hls) { video.hls.destroy(); }
    if (Hls.isSupported()) {
        const hls = new Hls({ xhrSetup: function (xhr) { xhr.withCredentials = false; } });
        hls.loadSource(p); hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => { video.play(); btnText.innerText = "تم التشغيل"; });
        video.hls = hls;
    } else { video.src = p; video.play(); btnText.innerText = "تم التشغيل"; }
}
</script>
</body>
</html>
