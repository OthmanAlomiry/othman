<?php
// --- 1. نظام عداد المتواجدين الحقيقي المدمج ---
session_start();
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id();
    $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) {
        if ($time - $last_time > 120) unset($data[$id]);
    }
    file_put_contents($visitors_file, serialize($data));
    echo count($data);
    exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

// --- 2. جلب إعدادات API المباريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

$leagues_map = [
    'PL'   => ['name' => 'الدوري الإنجليزي'],
    'PD'   => ['name' => 'الدوري الإسباني'],
    'BL1'  => ['name' => 'الدوري الألماني'],
    'SA'   => ['name' => 'الدوري الإيطالي'],
    'FL1'  => ['name' => 'الدوري الفرنسي'],
    'CL'   => ['name' => 'دوري أبطال أوروبا'],
    'EL'   => ['name' => 'الدوري الأوروبي'],
    'ACL'  => ['name' => 'دوري أبطال آسيا'],
    'CAF'  => ['name' => 'دوري أبطال أفريقيا'],
];

function translate_name($text) {
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if($response) {
        $result = json_decode($response, true);
        return $result[0][0][0] ?? $text;
    }
    return $text;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch);
curl_close($ch);
$match_data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { 
            --main: #e11d48; --bg-deep: #050c14; 
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.15);
        }
        
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 240px; overflow-x: hidden; color: #e2e8f0; }

        .header-fixed-container { 
            position: fixed; top: 0; left: 0; right: 0; width: 100%; z-index: 1000;
            background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); 
            border-bottom: 1px solid var(--glass-border); padding: 10px 0;
            display: flex; flex-direction: column; align-items: center; text-align: center;
        }
        .online-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 3px 10px; border-radius: 50px; color: #22c55e; font-size: 9px; font-weight: 900; display: flex; align-items: center; gap: 5px; margin-bottom: 8px; }
        .dot-blink { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: blink 1.5s infinite; }

        .social-links { display: flex; justify-content: center; gap: 10px; margin-bottom: 10px; }
        .social-btn { padding: 8px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 11px; color: #fff; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; }

        /* شريط الشعارات الحقيقية المطور */
        .category-tabs { 
            display: flex; justify-content: center; gap: 10px; width: 95%; overflow-x: auto; 
            scrollbar-width: none; padding: 10px 0;
        }
        .cat-item { 
            min-width: 85px; background: var(--glass); border: 1px solid var(--glass-border); 
            padding: 10px 5px; border-radius: 15px; cursor: pointer; transition: 0.3s;
            display: flex; flex-direction: column; align-items: center; gap: 8px;
        }
        .cat-item:hover { background: rgba(255,255,255,0.1); }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(225, 29, 72, 0.2); }
        .cat-item img { height: 35px; width: 100%; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5)); }
        .cat-item span { font-size: 8px; font-weight: 900; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; }

        #pro-cinematic-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 2000; transition: 1s; }
        .intro-finish-vfx { transform: scale(1.2); opacity: 0; visibility: hidden; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; min-height: 300px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; }
        .channel-section.active { display: grid; animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); backdrop-filter: blur(20px); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; }
        .name-box { background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 8px; color: #fff; font-weight: 900; font-size: 11px; border-right: 3px solid var(--main); }
        
        .play-btn-premium { width: 90%; margin: 15px auto; display: flex; justify-content: center; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 50px; font-weight: 900; font-size: 12px; cursor: pointer; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        
        .match-scroll { display: flex; gap: 12px; overflow-x: auto; padding: 10px 15px; scrollbar-width: none; }
        .match-card { min-width: 260px; background: var(--glass); border-radius: 20px; padding: 10px 15px; border: 1px solid var(--glass-border); }
        .league-title { background: rgba(255, 255, 255, 0.03); border-bottom: 1px solid var(--glass-border); padding: 5px 15px; margin: 0 -15px 10px -15px; text-align: center; color: #00ff87; font-size: 9px; font-weight: 800; }
        
        footer { text-align: center; padding: 40px; font-size: 11px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div style="font-size:70px; color:#fff;"><i class="fas fa-play-circle"></i></div>
    <h1 style="color:#fff; font-weight:900; font-size:24px; margin-top:15px;">الخدمة الرقمية</h1>
</div>

<div class="header-fixed-container">
    <div class="online-badge"><div class="dot-blink"></div><span>متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div>
    
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#22c55e">واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc">تليجرام</a>
    </div>

    <div class="category-tabs">
        <div class="cat-item" onclick="switchSection('bein', this)">
            <img src="[attachment_3](attachment)" alt="beIN">
            <span>beIN Sports</span>
        </div>
        <div class="cat-item" onclick="switchSection('shahid', this)">
            <img src="[attachment_1](attachment)" alt="Shahid">
            <span>SHAHID</span>
        </div>
        <div class="cat-item" onclick="switchSection('mbc', this)">
            <img src="[attachment_2](attachment)" alt="MBC">
            <span>MBC TV</span>
        </div>
        <div class="cat-item" onclick="switchSection('starz', this)">
            <img src="[attachment_0](attachment)" alt="Starzplay">
            <span>STARZPLAY</span>
        </div>
    </div>
</div>

<div class="matches-section" style="padding-top: 10px;">
    <div class="match-scroll">
        <?php if (isset($match_data['matches'])):
            foreach ($match_data['matches'] as $m): 
                $code = $m['competition']['code'];
                if (isset($leagues_map[$code])): 
                    $hName = translate_name($m['homeTeam']['name']);
                    $aName = translate_name($m['awayTeam']['name']);
        ?>
                <div class="match-card">
                    <div class="league-title"><div><?php echo $leagues_map[$code]['name']; ?></div></div>
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                        <div style="flex:1; text-align:center;">
                            <img src="<?php echo $m['homeTeam']['crest']; ?>" width="30" onerror="this.src='https://via.placeholder.com/40'">
                            <span style="font-size:8px; font-weight:700; margin-top:5px; display:block;"><?php echo $hName; ?></span>
                        </div>
                        <div style="flex: 1; text-align: center;">
                            <?php if (in_array($m['status'], ['IN_PLAY', 'PAUSED', 'LIVE'])): ?>
                                <span style="color:#ff4d4d; font-size:8px; font-weight:900;">● مباشر</span>
                            <?php else: ?>
                                <div style="font-size:10px; font-weight:bold; color:#f1c40f;"><?php echo date('h:i A', strtotime($m['utcDate'])); ?></div>
                            <?php endif; ?>
                        </div>
                        <div style="flex:1; text-align:center;">
                            <img src="<?php echo $m['awayTeam']['crest']; ?>" width="30" onerror="this.src='https://via.placeholder.com/40'">
                            <span style="font-size:8px; font-weight:700; margin-top:5px; display:block;"><?php echo $aName; ?></span>
                        </div>
                    </div>
                </div>
        <?php endif; endforeach; endif; ?>
    </div>
</div>

<div class="grid">
    <div id="section-bein" class="channel-section active">
        <div style="grid-column: 1/-1; font-size:16px; font-weight:900; color:#fff; padding:10px;">باقة beIN Sports</div>
        <?php for($i = 1; $i <= 9; $i++): ?>
        <div class="card">
            <div class="c-head"><div class="name-box">beIN Sport <?php echo $i; ?></div><div style="display:flex; align-items:center; gap:5px;"><div class="dot-blink"></div><span style="font-size:8px; color:#22c55e;">LIVE</span></div></div>
            <video id="vid<?php echo $i; ?>" playsinline controls></video>
            <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', this)"><span>تشغيل البث</span></button>
        </div>
        <?php endfor; ?>
    </div>

    </div>

<footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية 2026</footer>

<script>
window.addEventListener('load', () => { setTimeout(() => document.getElementById('pro-cinematic-intro').classList.add('intro-finish-vfx'), 1000); });
function updateRealtimeVisitors() { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => { if(count && !isNaN(count)) document.getElementById('realtime-visitors').innerText = count; }); }
setInterval(updateRealtimeVisitors, 4000);

function switchSection(sectionId, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    const target = document.getElementById('section-' + sectionId);
    if(target) target.classList.add('active');
    element.classList.add('active');
    window.scrollTo({ top: document.querySelector('.grid').offsetTop - 250, behavior: 'smooth' });
}

function robustPlay(vId, p, btn) {
    const video = document.getElementById(vId);
    const btnText = btn.querySelector('span');
    btnText.innerText = "جاري التحميل...";
    if (video.hls) { video.hls.destroy(); }
    if (Hls.isSupported()) {
        const hls = new Hls(); hls.loadSource(p); hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => { video.play(); btnText.innerText = "تم التشغيل"; });
        video.hls = hls;
    } else { video.src = p; video.play(); btnText.innerText = "تم التشغيل"; }
}
</script>
</body>
</html>
