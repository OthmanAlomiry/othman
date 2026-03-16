<?php
// --- إعدادات API المباريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

$leagues_map = [
    'PL'  => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1', 'ch_num' => '10'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sport 5', 'ch_num' => '5'],
    'CL'  => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2', 'ch_num' => '2'],
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
            --main: #e11d48; --bg-deep: #061626; --whatsapp: #25d366; --snapchat: #FFFC00;
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff); --green-grad: linear-gradient(45deg, #16a34a, #fff);
        }
        
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 10px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول المؤقتة --- */
        #pro-cinematic-intro {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000;
            display: flex; justify-content: center; align-items: center; z-index: 1000000; transition: 1s ease-in-out;
        }
        .intro-finish-vfx { transform: translateY(-100%); }

        /* --- الخلفية الزجاجية --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.1; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }

        /* --- الهيدر --- */
        .header-container { padding: 15px; text-align: center; background: rgba(6, 22, 38, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .promo-text { font-size: 11px; font-weight: 700; line-height: 1.6; margin-bottom: 15px; color: #fff; }
        .social-links { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
        .social-btn { padding: 6px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; }

        /* --- جدول المباريات (تنسيق منضبط) --- */
        .matches-section { padding: 10px 15px; }
        .section-title { font-size: 16px; font-weight: 900; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
        .match-scroll { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
        .match-scroll::-webkit-scrollbar { display: none; }
        
        .match-card { 
            min-width: 260px; max-width: 260px; background: rgba(255,255,255,0.05); 
            border-radius: 15px; padding: 12px; border: 1px solid rgba(255,255,255,0.1); 
        }
        .m-league { font-size: 9px; color: #00ff87; font-weight: 800; text-align: center; margin-bottom: 8px; }
        .match-main { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .team { text-align: center; flex: 1; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team-name { font-size: 10px; font-weight: 700; display: block; margin-top: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .m-status { text-align: center; flex: 0.6; }
        .m-score { font-size: 1.2em; font-weight: 900; letter-spacing: 2px; }
        .m-time { font-size: 11px; font-weight: bold; color: #f1c40f; }
        .m-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; display: flex; justify-content: space-between; font-size: 9px; }

        /* --- القنوات --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .live-box { display: flex; align-items: center; gap: 5px; background: rgba(34, 197, 94, 0.1); padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(34, 197, 94, 0.3); }
        .live-dot { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.3; } }
        
        /* --- زر التشغيل المتوهج --- */
        .play-btn-premium { 
            width: 92%; margin: 12px auto; display: flex; justify-content: center; align-items: center; gap: 8px; 
            background: linear-gradient(135deg, var(--main), #ff4d4d); color: #fff; border: none; 
            padding: 12px; border-radius: 50px; font-weight: 900; font-size: 13px; cursor: pointer;
            box-shadow: 0 0 10px rgba(225, 29, 72, 0.4); animation: btnGlow 2s infinite; 
        }
        @keyframes btnGlow { 
            0%, 100% { box-shadow: 0 0 10px rgba(225, 29, 72, 0.4); transform: scale(1); } 
            50% { box-shadow: 0 0 20px rgba(225, 29, 72, 0.7); transform: scale(1.02); } 
        }

        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        footer { text-align: center; padding: 25px; font-size: 11px; opacity: 0.6; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div style="text-align: center;">
        <div style="font-size: 70px; color: #fff; filter: drop-shadow(0 0 20px var(--main));"><i class="fas fa-play-circle"></i></div>
        <h1 style="font-weight:900; color:#fff; font-size:30px; margin-top:15px;">الخدمة الرقمية</h1>
    </div>
</div>

<div class="bg-pattern-animated"></div>

<div class="header-container">
    <div class="promo-text">هذه الصفحة مقدمة من متجر الخدمة الرقمية مجانا وبدون إعلانات للاشتراك في الباقة كاملة على جميع الأجهزة والشاشات تواصل معنا</div>
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a>
        <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a>
        <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000"><i class="fab fa-x-twitter"></i> تويتر</a>
    </div>
</div>

<div class="matches-section">
    <div class="section-title"><i class="fas fa-calendar-alt" style="color:#f1c40f"></i> مباريات ونتائج اليوم</div>
    <div class="match-scroll">
        <?php if (isset($match_data['matches'])):
            foreach ($match_data['matches'] as $m): 
                $code = $m['competition']['code'];
                if (isset($leagues_map[$code])): 
                    $is_live = ($m['status'] == 'IN_PLAY' || $m['status'] == 'PAUSED');
                    $is_fin = ($m['status'] == 'FINISHED');
        ?>
                <div class="match-card">
                    <div class="m-league"><?php echo $leagues_map[$code]['name']; ?></div>
                    <div class="match-main">
                        <div class="team">
                            <img src="<?php echo $m['homeTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/35'">
                            <span class="team-name"><?php echo translate_name($m['homeTeam']['name']); ?></span>
                        </div>
                        <div class="m-status">
                            <?php if ($is_live || $is_fin): ?>
                                <div class="m-score"><?php echo $m['score']['fullTime']['home'].'-'.$m['score']['fullTime']['away']; ?></div>
                                <?php if($is_live): ?><span style="color:#ff4d4d; font-size:8px; font-weight:900;">LIVE</span><?php endif; ?>
                            <?php else: ?>
                                <div class="m-time"><?php echo date('h:i A', strtotime($m['utcDate'])); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="team">
                            <img src="<?php echo $m['awayTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/35'">
                            <span class="team-name"><?php echo translate_name($m['awayTeam']['name']); ?></span>
                        </div>
                    </div>
                    <div class="m-footer">
                        <span>📺 <?php echo $leagues_map[$code]['channel']; ?></span>
                        <span style="color:var(--main); font-weight:900;" onclick="goToChannel('<?php echo $leagues_map[$code]['ch_num']; ?>')">شاهد الآن ▶</span>
                    </div>
                </div>
        <?php endif; endforeach; endif; ?>
    </div>
</div>

<div class="grid">
    <div class="section-divider">قنوات beIN Sports</div>
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head">
            <div style="background:var(--purple-grad); padding:4px 10px; border-radius:6px; color:#000; font-weight:900; font-size:11px;">beIN Sport <?php echo $i; ?></div>
            <div class="live-box"><div class="live-dot"></div><span style="font-size:9px; color:#22c55e; font-weight:900;">LIVE</span></div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)"> 
            <i class="fas fa-play"></i> <span>بدء البث المباشر</span>
        </button>
    </div>
    <?php endfor; ?>
</div>

<footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية</footer>

<script>
window.addEventListener('load', function() {
    setTimeout(() => { document.getElementById('pro-cinematic-intro').classList.add('intro-finish-vfx'); }, 2500); 
});

function goToChannel(num) {
    const el = document.getElementById('ch-row-' + num);
    if(el) { window.scrollTo({ top: el.offsetTop - 100, behavior: 'smooth' }); setTimeout(() => el.querySelector('.play-btn-premium').click(), 800); }
}

function robustPlay(videoId, primary, backup, btn) {
    const video = document.getElementById(videoId);
    const btnText = btn.querySelector('span');
    btnText.innerText = "جاري الاتصال...";
    function run(url, isB = false) {
        if (video.hls) { video.hls.destroy(); }
        if (Hls.isSupported()) {
            const hls = new Hls(); hls.loadSource(url); hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => { video.play(); btnText.innerText = "تم التشغيل بنجاح"; });
            video.hls = hls;
        } else { video.src = url; video.play(); }
    }
    run(primary);
    setTimeout(() => { if (video.paused) run(backup, true); }, 7000);
}
</script>
</body>
</html>
