<?php
// --- إعدادات API المباريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// ربط الدوريات والقنوات الناقلة (الرجاء التأكد من صحة الروابط bs1.php للتحويل التلقائي)
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
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <style>
        :root { 
            --main: #e11d48; --bg-deep: #061626; --whatsapp: #25d366; --snapchat: #FFFC00; --telegram: #0088cc;
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff); --green-grad: linear-gradient(45deg, #16a34a, #fff);
        }
        
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول الاحترافية 3D --- */
        #pro-cinematic-intro {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000;
            display: flex; justify-content: center; align-items: center; z-index: 1000000;
            overflow: hidden; perspective: 1000px; transition: all 1.2s cubic-bezier(0.7, 0, 0.3, 1);
        }
        .intro-content { text-align: center; animation: float3D 6s infinite ease-in-out; }
        .logo-glow { font-size: 80px; color: #fff; margin-bottom: 20px; filter: drop-shadow(0 0 30px var(--main)); }
        .loading-bar-3d { width: 250px; height: 4px; background: rgba(255,255,255,0.1); margin: 20px auto; border-radius: 10px; position: relative; overflow: hidden; }
        .loading-fill { position: absolute; width: 0%; height: 100%; background: var(--main); box-shadow: 0 0 15px var(--main); animation: fillProgress 3.5s forwards; }
        @keyframes fillProgress { to { width: 100%; } }
        @keyframes float3D { 0%, 100% { transform: rotateY(0deg); } 50% { transform: rotateY(15deg) rotateX(5deg); } }
        .intro-finish-vfx { transform: scale(1.5); filter: blur(30px); opacity: 0; visibility: hidden; }

        /* --- الخلفية الزجاجية السابقة --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.15; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }

        /* --- الهيدر الملتصق --- */
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 10px 40px rgba(0,0,0,0.6); }
        .promo-bar { background: rgba(6, 22, 38, 0.96); backdrop-filter: blur(15px); color: #fff; padding: 10px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .social-links { display: flex; justify-content: center; gap: 8px; }
        .social-btn { display: flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; background: var(--main); transition: 0.3s; } 
        .nav-shortcuts { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(25px); display: flex; justify-content: center; gap: 12px; padding: 10px; border-bottom: 1px solid rgba(225, 29, 72, 0.3); }
        .nav-box { padding: 6px 18px; border-radius: 8px; font-weight: 900; font-size: 12px; color: #061626; text-decoration: none; }
        
        /* --- تصميم جدول المباريات المطور --- */
        .matches-section { padding: 20px; max-width: 1200px; margin: auto; }
        .match-scroll { display: flex; gap: 15px; overflow-x: auto; padding: 10px 5px 20px; scrollbar-width: none; }
        .match-scroll::-webkit-scrollbar { display: none; }
        .match-card { 
            min-width: 290px; background: rgba(255,255,255,0.03); backdrop-filter: blur(15px); 
            border-radius: 20px; padding: 15px; border: 1px solid rgba(255,255,255,0.08);
            cursor: pointer; transition: 0.3s;
        }
        .match-card:hover { transform: translateY(-5px); border-color: var(--main); background: rgba(255,255,255,0.07); }
        .league-header { font-size: 10px; color: #00ff87; text-align: center; font-weight: 800; margin-bottom: 10px; }
        .match-main { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .team { flex: 1; text-align: center; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team-name { font-size: 11px; font-weight: 700; margin-top: 5px; display: block; }
        .status-box { flex: 0.7; text-align: center; }
        .score { font-size: 1.4em; font-weight: 900; letter-spacing: 3px; }
        .time { font-size: 1.1em; color: #f1c40f; font-weight: bold; }
        .m-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; display: flex; justify-content: space-between; font-size: 10px; }

        /* --- تصميم القنوات --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; max-width: 1400px; margin: auto; }
        .section-divider { grid-column: 1 / -1; padding: 15px 0; font-size: 20px; font-weight: 900; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px; }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); transition: 0.3s; }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .live-status-box { display: flex; align-items: center; gap: 5px; background: rgba(34, 197, 94, 0.1); padding: 5px 12px; border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.3); }
        .live-dot-glow { width: 6px; height: 6px; background-color: #22c55e; border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn-premium { width: 92%; margin: 15px auto; display: flex; justify-content: center; align-items: center; gap: 10px; background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05)); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 50px; font-weight: 900; font-size: 14px; cursor: pointer; transition: 0.4s; }
        .play-btn-premium:hover { background: var(--main); }
        footer { text-align: center; padding: 40px; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div class="intro-content">
        <div class="logo-glow"><i class="fas fa-play-circle"></i></div>
        <h1 style="font-weight:900; font-size:40px;">الخدمة الرقمية</h1>
        <div class="loading-bar-3d"><div class="loading-fill"></div></div>
    </div>
</div>

<div class="bg-pattern-animated"></div>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000"><i class="fab fa-x-twitter"></i> تويتر</a>
        </div>
    </div>
    <div class="nav-shortcuts">
        <a href="#bein-section" class="nav-box" style="background:var(--purple-grad)">beIN Sport</a>
        <a href="#starz-section" class="nav-box" style="background:var(--green-grad)">STARZPLAY</a>
    </div>
</div>

<div class="matches-section">
    <div class="section-divider"><i class="fas fa-calendar-alt"></i> جدول مباريات ونتائج اليوم</div>
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
                    $home_crest = $match['homeTeam']['crest'];
                    $away_crest = $match['awayTeam']['crest'];
        ?>
                <div class="match-card">
                    <div class="league-header"><?php echo $leagues_map[$code]['name']; ?></div>
                    <div class="match-main">
                        <div class="team">
                            <img src="<?php echo $home_crest; ?>" onerror="this.src='https://via.placeholder.com/40x40?text=Logo'">
                            <span class="team-name"><?php echo translate_name($match['homeTeam']['name']); ?></span>
                        </div>
                        <div class="status-box">
                            <?php if ($is_live || $is_finished): ?>
                                <div class="score"><?php echo $match['score']['fullTime']['home'] . '-' . $match['score']['fullTime']['away']; ?></div>
                                <?php if ($is_live): ?><span style="color:#ff4d4d; font-size:8px;">● مباشر</span><?php else: ?><span style="font-size:8px; opacity:0.5">انتهت</span><?php endif; ?>
                            <?php else: ?>
                                <div class="time"><?php echo date('h:i A', strtotime($match['utcDate'])); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="team">
                            <img src="<?php echo $away_crest; ?>" onerror="this.src='https://via.placeholder.com/40x40?text=Logo'">
                            <span class="team-name"><?php echo translate_name($match['awayTeam']['name']); ?></span>
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
            <div class="live-status-box"><div class="live-dot-glow"></div><span class="live-text">LIVE</span></div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)"> 
            <i class="fas fa-play"></i> <span>بدء البث المباشر</span>
        </button>
    </div>
    <?php endfor; ?>

    <div id="starz-section" class="section-divider">قنوات STARZPLAY</div>
    <?php for($i = 10; $i <= 11; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head">
            <div style="background:var(--green-grad); padding:4px 12px; border-radius:6px; color:#000; font-weight:900; font-size:11px;">STARZPLAY <?php echo ($i-9); ?></div>
            <div class="live-status-box"><div class="live-dot-glow"></div><span class="live-text">LIVE</span></div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)"> 
            <i class="fas fa-play"></i> <span>بدء البث المباشر</span>
        </button>
    </div>
    <?php endfor; ?>
</div>

<footer><div id="count-num">0</div></footer>

<script>
window.addEventListener('load', function() {
    setTimeout(() => {
        document.getElementById('pro-cinematic-intro').classList.add('intro-finish-vfx');
        setTimeout(() => document.getElementById('pro-cinematic-intro').remove(), 1200);
    }, 3500); 
});

function goToChannel(num) {
    const el = document.getElementById('ch-row-' + num);
    if(el) {
        window.scrollTo({ top: el.offsetTop - 220, behavior: 'smooth' });
        setTimeout(() => el.querySelector('.play-btn-premium').click(), 800);
    }
}

function robustPlay(videoId, primary, backup, btn) {
    const video = document.getElementById(videoId);
    const btnText = btn.querySelector('span');
    btnText.innerText = "جاري الاتصال...";
    btn.style.pointerEvents = "none";
    
    function runStream(url, isBackup = false) {
        if (video.hls) { video.hls.destroy(); }
        if (Hls.isSupported()) {
            const hls = new Hls({ manifestLoadingTimeOut: 10000 });
            hls.loadSource(url);
            hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => { 
                video.play(); 
                btnText.innerText = isBackup ? "تم تشغيل الاحتياطي" : "تم التشغيل بنجاح";
                btn.style.background = isBackup ? "var(--green-grad)" : "var(--purple-grad)";
                btn.style.color = "#000";
                setTimeout(() => { btn.style.pointerEvents = "auto"; }, 2000);
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
