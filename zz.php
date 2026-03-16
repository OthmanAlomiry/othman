<?php
// --- إعدادات API المباريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

$leagues_map = [
    'PL'  => ['name' => 'الدوري الإنجليزي الممتاز', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
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
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 130px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول المؤقتة السينمائية --- */
        #pro-cinematic-intro {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000;
            display: flex; justify-content: center; align-items: center; z-index: 1000000; transition: 1.2s cubic-bezier(0.7, 0, 0.3, 1);
        }
        .intro-finish-vfx { transform: translateY(-100%); opacity: 0; visibility: hidden; }
        .loading-fill-vfx { position: absolute; bottom: 0; left: 0; width: 0%; height: 4px; background: var(--main); box-shadow: 0 0 20px var(--main); animation: proLoadingFlow 3s forwards; }
        @keyframes proLoadingFlow { to { width: 100%; } }

        /* --- الخلفية الزجاجية --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.15; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }

        /* --- الهيدر الملتصق --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 10px 40px rgba(0,0,0,0.6); }
        .promo-bar { background: rgba(6, 22, 38, 0.96); backdrop-filter: blur(15px); padding: 12px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .promo-top-text { font-size: 11px; line-height: 1.6; color: #fff; margin-bottom: 10px; font-weight: 700; }
        .social-links { display: flex; justify-content: center; gap: 8px; }
        .social-btn { padding: 6px 14px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; background: var(--main); }

        /* --- جدول المباريات --- */
        .matches-section { padding: 10px 20px; max-width: 1200px; margin: auto; }
        .match-scroll { display: flex; gap: 12px; overflow-x: auto; padding: 10px 0; scrollbar-width: none; }
        .match-scroll::-webkit-scrollbar { display: none; }
        .match-card { min-width: 280px; background: rgba(255,255,255,0.03); backdrop-filter: blur(15px); border-radius: 18px; padding: 12px; border: 1px solid rgba(255,255,255,0.08); }
        .match-main { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
        .score { font-size: 1.3em; font-weight: 900; letter-spacing: 2px; color: #fff; }

        /* --- القنوات والتوهج --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 10px 20px; max-width: 1400px; margin: auto; }
        .section-divider { grid-column: 1 / -1; padding: 10px 0; font-size: 18px; font-weight: 900; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 18px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .live-status-box { display: flex; align-items: center; gap: 5px; background: rgba(34, 197, 94, 0.1); padding: 5px 12px; border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.3); }
        .live-dot-glow { width: 6px; height: 6px; background-color: #22c55e; border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        
        /* --- توهج زر التشغيل المحدث --- */
        .play-btn-premium { 
            width: 92%; margin: 12px auto; display: flex; justify-content: center; align-items: center; gap: 8px; 
            background: linear-gradient(135deg, var(--main), #ff4d4d); color: #fff; border: none; 
            padding: 12px; border-radius: 50px; font-weight: 900; font-size: 13px; cursor: pointer;
            box-shadow: 0 0 15px rgba(225, 29, 72, 0.4); animation: btnGlow 2s infinite; 
        }
        @keyframes btnGlow {
            0% { box-shadow: 0 0 10px rgba(225, 29, 72, 0.4); transform: scale(1); }
            50% { box-shadow: 0 0 25px rgba(225, 29, 72, 0.7); transform: scale(1.02); }
            100% { box-shadow: 0 0 10px rgba(225, 29, 72, 0.4); transform: scale(1); }
        }

        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        footer { text-align: center; padding: 30px; font-size: 11px; opacity: 0.6; font-weight: bold; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div style="text-align: center; position: relative;">
        <div style="font-size: 80px; color: #fff; filter: drop-shadow(0 0 30px var(--main));"><i class="fas fa-play-circle"></i></div>
        <h1 style="font-weight:900; color:#fff; font-size:35px; margin-top:20px;">الخدمة الرقمية</h1>
        <p style="color: rgba(255,255,255,0.5); font-size: 12px;">جاري تحضير البث المباشر...</p>
    </div>
    <div class="loading-fill-vfx"></div>
</div>

<div class="bg-pattern-animated"></div>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="promo-top-text">
            هذه الصفحة مقدمة من متجر الخدمة الرقمية مجانا وبدون إعلانات للاشتراك في الباقة كاملة على جميع الأجهزة والشاشات تواصل معنا
        </div>
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000"><i class="fab fa-x-twitter"></i> تويتر</a>
        </div>
    </div>
</div>

<div class="matches-section">
    <div class="section-divider"><i class="fas fa-calendar-alt"></i> مباريات ونتائج اليوم</div>
    <div class="match-scroll">
        <?php 
        $match_count = 0;
        if (isset($match_data['matches'])):
            foreach ($match_data['matches'] as $match): 
                $code = $match['competition']['code'];
                if (isset($leagues_map[$code])): 
                    $match_count++;
                    $is_live = ($match['status'] == 'IN_PLAY' || $match['status'] == 'PAUSED');
                    $is_finished = ($match['status'] == 'FINISHED');
                    $target_ch = $leagues_map[$code]['ch_num'];
        ?>
                <div class="match-card">
                    <div class="match-main">
                        <div class="team" style="flex:1; text-align:center;">
                            <img src="<?php echo $match['homeTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/40?text=T1'">
                            <span class="team-name" style="font-size:10px;"><?php echo translate_name($match['homeTeam']['name']); ?></span>
                        </div>
                        <div style="flex:0.7; text-align:center;">
                            <?php if ($is_live || $is_finished): ?>
                                <div class="score"><?php echo $match['score']['fullTime']['home'] . '-' . $match['score']['fullTime']['away']; ?></div>
                                <?php if ($is_live): ?><span style="color:#ff4d4d; font-size:8px;">● مباشر</span><?php endif; ?>
                            <?php else: ?>
                                <div style="font-size:12px; font-weight:bold; color:#f1c40f;"><?php echo date('h:i A', strtotime($match['utcDate'])); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="team" style="flex:1; text-align:center;">
                            <img src="<?php echo $match['awayTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/40?text=T2'">
                            <span class="team-name" style="font-size:10px;"><?php echo translate_name($match['awayTeam']['name']); ?></span>
                        </div>
                    </div>
                    <div class="m-footer">
                        <span>📺 <?php echo $leagues_map[$code]['channel']; ?></span>
                        <span style="color:var(--main); font-weight:900;" onclick="goToChannel('<?php echo $target_ch; ?>')">شاهد الآن ▶</span>
                    </div>
                </div>
        <?php 
                endif;
            endforeach; 
        endif;
        if($match_count == 0) echo '<div style="opacity:0.5; font-size:12px">لا توجد مباريات كبرى اليوم.</div>';
        ?>
    </div>
</div>

<div class="grid">
    <div id="bein-section" class="section-divider">قنوات beIN Sports</div>
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head">
            <div style="background:var(--purple-grad); padding:4px 12px; border-radius:6px; color:#000; font-weight:900; font-size:11px;">beIN Sport <?php echo $i; ?></div>
            <div class="live-status-box"><div class="live-dot-glow"></div><span style="font-size:9px;color:#22c55e;font-weight:900;">LIVE</span></div>
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
    setTimeout(() => {
        document.getElementById('pro-cinematic-intro').classList.add('intro-finish-vfx');
        setTimeout(() => document.getElementById('pro-cinematic-intro').remove(), 1200);
    }, 3000); 
});

function goToChannel(num) {
    const el = document.getElementById('ch-row-' + num);
    if(el) {
        window.scrollTo({ top: el.offsetTop - 150, behavior: 'smooth' });
        setTimeout(() => el.querySelector('.play-btn-premium').click(), 800);
    }
}

function robustPlay(videoId, primary, backup, btn) {
    const video = document.getElementById(videoId);
    const btnText = btn.querySelector('span');
    btnText.innerText = "جاري الاتصال...";
    
    function runStream(url, isBackup = false) {
        if (video.hls) { video.hls.destroy(); }
        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource(url);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => { 
                video.play(); 
                btnText.innerText = isBackup ? "تم تشغيل الاحتياطي" : "تم التشغيل بنجاح";
            });
            video.hls = hls;
        } else {
            video.src = url; video.play();
        }
    }
    runStream(primary);
    setTimeout(() => { if (video.paused) runStream(backup, true); }, 7000);
}
</script>
</body>
</html>
