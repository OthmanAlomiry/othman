<?php
// إعدادات API المباريات
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// خريطة الدوريات وربطها برقم القناة في موقعك (b1.php تعني 1، b3.php تعني 3 وهكذا)
$leagues_map = [
    'PL'  => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1', 'ch_num' => '10'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sport 5', 'ch_num' => '5'],
    'CL'  => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2', 'ch_num' => '2'],
];

function translate_to_arabic($text) {
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
        :root { --main: #e11d48; --bg-deep: #061626; --purple-grad: linear-gradient(45deg, #7c3aed, #fff); --green-grad: linear-gradient(45deg, #16a34a, #fff); }
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; color: #e2e8f0; }

        /* --- شاشة الدخول VFX --- */
        #pro-cinematic-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; display: flex; justify-content: center; align-items: center; z-index: 1000000; overflow: hidden; transition: all 1.2s cubic-bezier(0.7, 0, 0.3, 1); }
        .intro-finish-vfx { transform: scale(1.2); filter: blur(20px); opacity: 0; visibility: hidden; }
        .loading-fill-vfx { position: absolute; width: 0%; height: 100%; background: linear-gradient(to right, transparent, var(--main), #fff); animation: proLoadingFlow 3.5s forwards; }
        @keyframes proLoadingFlow { to { width: 100%; } }

        /* --- تصميم كروت المباريات --- */
        .matches-container { padding: 20px; max-width: 1200px; margin: auto; }
        .match-scroll { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 15px; scrollbar-width: none; }
        .match-scroll::-webkit-scrollbar { display: none; }
        .match-card-mini { 
            min-width: 280px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); 
            border-radius: 15px; padding: 12px; border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer; transition: 0.3s;
        }
        .match-card-mini:hover { background: rgba(255,255,255,0.1); transform: translateY(-5px); border-color: var(--main); }
        .m-league { font-size: 10px; color: #22c55e; font-weight: 900; margin-bottom: 8px; text-align: center; }
        .m-teams { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .m-team { text-align: center; flex: 1; }
        .m-team img { width: 30px; height: 30px; object-fit: contain; }
        .m-team span { display: block; font-size: 11px; font-weight: 700; margin-top: 5px; }
        .m-vs { font-size: 14px; font-weight: 900; color: #f1c40f; }
        .m-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; display: flex; justify-content: space-between; font-size: 10px; }

        /* --- الهيدر والقنوات --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url('https://www.transparenttextures.com/patterns/black-paper.png'), linear-gradient(135deg, var(--bg-deep) 0%, #0a1f33 100%); }
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 10px 40px rgba(0,0,0,0.6); }
        .promo-bar { background: rgba(6, 22, 38, 0.96); backdrop-filter: blur(15px); padding: 10px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .social-links { display: flex; justify-content: center; gap: 8px; }
        .social-btn { display: flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; transition: 0.3s; } 
        .btn-wa { background: var(--whatsapp); } .btn-snap { background: var(--snapchat); color: #000; } .btn-tg { background: var(--telegram); } .btn-x { background: #000; }
        .nav-shortcuts { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(25px); display: flex; justify-content: center; gap: 12px; padding: 10px; border-bottom: 1px solid rgba(225, 29, 72, 0.3); }
        .nav-box-purple, .nav-box-green { padding: 6px 18px; border-radius: 8px; display: flex; align-items: center; gap: 6px; font-weight: 900; font-size: 12px; color: #061626; text-decoration: none; }
        .nav-box-purple { background: var(--purple-grad); } .nav-box-green { background: var(--green-grad); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 10px 25px; max-width: 1400px; margin: auto; }
        .section-divider { grid-column: 1 / -1; padding: 10px 0; font-size: 20px; font-weight: 900; display: flex; align-items: center; gap: 10px; margin: 0; color: #fff; }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); transition: 0.3s; }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .live-status-box { display: flex; align-items: center; gap: 5px; background: rgba(34, 197, 94, 0.1); padding: 5px 12px; border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.3); }
        .live-dot { width: 6px; height: 6px; background-color: #22c55e; border-radius: 50%; animation: blinkStatus 1s infinite; }
        @keyframes blinkStatus { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn-premium { width: 92%; margin: 18px auto; display: flex; justify-content: center; align-items: center; gap: 12px; background: linear-gradient(135deg, rgba(225, 29, 72, 0.2) 0%, rgba(225, 29, 72, 0.1) 100%); backdrop-filter: blur(10px); color: #fff; border: 1px solid rgba(225, 29, 72, 0.5); padding: 14px; border-radius: 50px; font-weight: 900; font-size: 15px; cursor: pointer; transition: 0.4s; }
        footer { text-align: center; padding: 40px; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div class="video-bg-effect"></div>
    <div class="content-wrap">
        <div style="font-size:80px; color:#fff; margin-bottom:20px;"><i class="fas fa-play-circle"></i></div>
        <h1 class="brand-title-ar">الخدمة الرقمية</h1>
        <div style="width:250px; height:3px; background:rgba(255,255,255,0.1); margin:35px auto; position:relative; overflow:hidden;"><div class="loading-fill-vfx"></div></div>
    </div>
</div>

<div class="bg-pattern-animated"></div>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn btn-wa"><i class="fab fa-whatsapp"></i> واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn btn-tg"><i class="fab fa-telegram-plane"></i> تليجرام</a>
            <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn btn-snap"><i class="fab fa-snapchat"></i> سناب</a>
            <a href="https://x.com/d_service_pro" class="social-btn btn-x"><i class="fab fa-x-twitter"></i> تويتر</a>
        </div>
    </div>
    <div class="nav-shortcuts">
        <a href="#bein-section" class="nav-box-purple"><i class="fas fa-satellite-dish"></i> beIN Sport</a>
        <a href="#starz-section" class="nav-box-green"><i class="fas fa-play"></i> STARZPLAY</a>
    </div>
</div>

<div class="matches-container">
    <div class="section-divider"><i class="fas fa-calendar-check" style="color:#f1c40f"></i> مباريات اليوم الكبرى</div>
    <div class="match-scroll">
        <?php 
        $count = 0;
        if (isset($match_data['matches'])):
            foreach ($match_data['matches'] as $m): 
                $code = $m['competition']['code'];
                if (isset($leagues_map[$code])): 
                    $count++;
                    $target_ch = $leagues_map[$code]['ch_num'];
        ?>
            <div class="match-card-mini" onclick="goToChannel('<?php echo $target_ch; ?>')">
                <div class="m-league"><?php echo $leagues_map[$code]['name']; ?></div>
                <div class="m-teams">
                    <div class="m-team">
                        <img src="<?php echo $m['homeTeam']['crest']; ?>" alt="">
                        <span><?php echo translate_to_arabic($m['homeTeam']['name']); ?></span>
                    </div>
                    <div class="m-vs"><?php echo date('H:i', strtotime($m['utcDate'])); ?></div>
                    <div class="m-team">
                        <img src="<?php echo $m['awayTeam']['crest']; ?>" alt="">
                        <span><?php echo translate_to_arabic($m['awayTeam']['name']); ?></span>
                    </div>
                </div>
                <div class="m-footer">
                    <span>📺 <?php echo $leagues_map[$code]['channel']; ?></span>
                    <span style="color:#var(--main)">شاهد الآن ▶</span>
                </div>
            </div>
        <?php 
                endif;
            endforeach; 
        endif; 
        if($count == 0) echo '<div style="opacity:0.5; font-size:12px">لا توجد مباريات كبرى حالياً</div>';
        ?>
    </div>
</div>

<div class="grid">
    <div id="bein-section" class="section-divider"><i class="fas fa-trophy" style="color: #7c3aed;"></i> باقة beIN Sports</div>
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head">
            <div style="background:var(--purple-grad); padding:4px 12px; border-radius:6px; color:#000; font-weight:900; font-size:12px;">beIN Sport <?php echo $i; ?></div>
            <div class="live-status-box"><div class="live-dot"></div><span style="font-size:9px;color:#22c55e;font-weight:900;">LIVE</span></div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="smartPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)"> 
            <i class="fas fa-play"></i> <span>بدء البث المباشر الآن</span>
        </button>
    </div>
    <?php endfor; ?>

    <div id="starz-section" class="section-divider"><i class="fas fa-star" style="color: #16a34a;"></i> باقة STARZPLAY</div>
    <?php for($i = 10; $i <= 11; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head">
            <div style="background:var(--green-grad); padding:4px 12px; border-radius:6px; color:#000; font-weight:900; font-size:12px;">STARZPLAY <?php echo ($i-9); ?></div>
            <div class="live-status-box"><div class="live-dot"></div><span style="font-size:9px;color:#22c55e;font-weight:900;">LIVE</span></div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="smartPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)"> 
            <i class="fas fa-play"></i> <span>بدء البث المباشر الآن</span>
        </button>
    </div>
    <?php endfor; ?>
</div>

<script>
window.addEventListener('load', function() {
    setTimeout(function() {
        const intro = document.getElementById('pro-cinematic-intro');
        if(intro) { intro.classList.add('intro-finish-vfx'); setTimeout(() => intro.remove(), 1200); }
    }, 3500); 
});

function goToChannel(num) {
    const el = document.getElementById('ch-row-' + num);
    if(el) {
        window.scrollTo({ top: el.offsetTop - 200, behavior: 'smooth' });
        const btn = el.querySelector('.play-btn-premium');
        setTimeout(() => btn.click(), 800);
    }
}

function smartPlay(videoId, primary, backup, btn) {
    const video = document.getElementById(videoId);
    const btnText = btn.querySelector('span');
    const btnIcon = btn.querySelector('i');
    btn.style.background = "rgba(255, 255, 255, 0.1)";
    btn.style.pointerEvents = "none";
    btnText.innerText = "جاري الاتصال بالسيرفر...";
    btnIcon.className = "fas fa-spinner fa-spin";
    let isPlayed = false;

    function runStream(url, isBackup = false) {
        if (video.hls) { video.hls.destroy(); }
        if (Hls.isSupported()) {
            const hls = new Hls({ manifestLoadingTimeOut: 8000 });
            hls.loadSource(url); hls.attachMedia(video);
            hls.on(Hls.Events.MANIFEST_PARSED, () => { video.play(); onSuccess(isBackup); });
            video.hls = hls;
        } else {
            video.src = url; video.play(); onSuccess(isBackup);
        }
    }

    function onSuccess(isBackup) {
        isPlayed = true;
        btn.style.background = isBackup ? "var(--green-grad)" : "var(--purple-grad)";
        btnText.innerText = "تم تشغيل القناة بنجاح";
        btnIcon.className = "fas fa-check-circle";
        setTimeout(() => { btn.style.pointerEvents = "auto"; }, 2000);
    }

    runStream(primary);
    setTimeout(() => { if (!isPlayed) { btnText.innerText = "تفعيل الاحتياطي..."; runStream(backup, true); } }, 7000);
}
</script>
</body>
</html>
