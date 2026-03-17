<?php
// --- إعدادات API المباريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// خريطة الدوريات الشاملة
$leagues_map = [
    'PL'   => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    'PD'   => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    'BL1'  => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sport 5', 'ch_num' => '5'],
    'SA'   => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1', 'ch_num' => '10'],
    'FL1'  => ['name' => 'الدوري الفرنسي', 'channel' => 'beIN Sport 4', 'ch_num' => '4'],
    'CL'   => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    'EL'   => ['name' => 'الدوري الأوروبي', 'channel' => 'beIN Sport 6', 'ch_num' => '6'],
    'ACL'  => ['name' => 'دوري أبطال آسيا', 'channel' => 'beIN AFC', 'ch_num' => '7'],
    'CAF'  => ['name' => 'دوري أبطال أفريقيا', 'channel' => 'beIN Sport 6', 'ch_num' => '6'],
    'WC'   => ['name' => 'تصفيات كأس العالم', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    'EC'   => ['name' => 'كأس أمم أوروبا', 'channel' => 'beIN MAX', 'ch_num' => '1'],
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
            --main: #e11d48; --bg-deep: #050c14; --whatsapp: #25d366; 
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.15);
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff); 
            --green-grad: linear-gradient(45deg, #16a34a, #fff);
            --anim-speed: 0.4s;
        }
        
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; overflow-x: hidden; color: #e2e8f0; }

        /* --- شاشة الدخول VFX --- */
        #pro-cinematic-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 2000; transition: 1.2s cubic-bezier(0.8, 0, 0.2, 1); }
        .intro-finish-vfx { transform: scale(1.5); opacity: 0; visibility: hidden; }
        .intro-icon { font-size: 90px; color: #fff; filter: drop-shadow(0 0 30px var(--main)); animation: pulseLogo 2s infinite ease-in-out; }
        .intro-loading-box { width: 200px; height: 2px; background: rgba(255,255,255,0.1); margin-top: 30px; border-radius: 10px; overflow: hidden; }
        .intro-loading-bar { width: 0%; height: 100%; background: var(--main); box-shadow: 0 0 15px var(--main); animation: loadProgress 3s forwards; }
        @keyframes loadProgress { to { width: 100%; } }
        @keyframes pulseLogo { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }

        /* --- الخلفية الأنيقة المتحركة --- */
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }

        /* --- الهيدر الزجاجي --- */
        .header-fixed-container { 
            position: fixed; top: 0; left: 0; right: 0; width: 100%; z-index: 1000;
            background: rgba(5, 12, 20, 0.9); backdrop-filter: blur(25px); 
            border-bottom: 1px solid var(--glass-border); padding: 15px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
            display: flex; flex-direction: column; align-items: center; text-align: center;
        }
        .promo-text { font-size: 11px; font-weight: 700; color: #fff; margin-bottom: 10px; line-height: 1.6; width: 90%; max-width: 600px; margin-inline: auto; }
        .social-links { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
        .social-btn { padding: 7px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; border: 1px solid rgba(255,255,255,0.15); transition: 0.3s; }

        /* --- جدول المباريات --- */
        .matches-section { padding: 10px 15px; }
        .match-scroll { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
        .match-scroll::-webkit-scrollbar { display: none; }
        .match-card { min-width: 280px; background: var(--glass); border-radius: 20px; padding: 0 15px 15px 15px; border: 1px solid var(--glass-border); transition: all 0.3s ease; overflow: hidden; }
        .league-title-box { background: rgba(255, 255, 255, 0.03); border-bottom: 1px solid var(--glass-border); padding: 8px 15px; margin: 0 -15px 15px -15px; text-align: center; }
        .m-league { font-size: 10px; color: #00ff87; font-weight: 800; }
        
        .match-main { display: flex; align-items: center; justify-content: space-between; gap: 5px; margin-bottom: 12px; }
        .team { flex: 1; display: flex; flex-direction: column; align-items: center; text-align: center; min-width: 80px; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team-name { font-size: 10px; font-weight: 700; margin-top: 6px; }

        .m-score-container { flex: 0.8; display: flex; align-items: center; justify-content: center; gap: 5px; font-family: sans-serif; }
        .s-box { background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 8px; font-size: 1.4em; font-weight: 900; color: #fff; min-width: 30px; text-align: center; }
        .s-divider { opacity: 0.5; font-weight: bold; }

        .m-footer { border-top: 1px solid var(--glass-border); padding-top: 10px; display: flex; justify-content: space-between; font-size: 9px; align-items: center; }

        /* --- القنوات وأزرار التشغيل الزجاجية المتوهجة --- */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .card { background: var(--glass); backdrop-filter: blur(20px); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        .c-head { padding: 12px 18px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; }
        .name-box-purple { background: var(--purple-grad); padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        .name-box-green { background: var(--green-grad); padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        
        .live-box { display: flex; align-items: center; gap: 6px; background: rgba(34, 197, 94, 0.1); padding: 5px 12px; border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.2); }
        .live-dot { width: 7px; height: 7px; background: #22c55e; border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.2; } }

        /* تعديل الزر ليكون زجاجي متوهج */
        .play-btn-premium { 
            width: 90%; margin: 15px auto; display: flex; justify-content: center; align-items: center; gap: 10px; 
            background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); 
            padding: 14px; border-radius: 50px; font-weight: 900; font-size: 13px; cursor: pointer;
            backdrop-filter: blur(5px); transition: all 0.3s ease; 
            animation: glassGlow 3s infinite;
        }
        .play-btn-premium:hover { background: rgba(255, 255, 255, 0.2); transform: scale(1.02); }
        
        @keyframes glassGlow {
            0%, 100% { box-shadow: 0 0 10px rgba(255, 255, 255, 0.05); }
            50% { box-shadow: 0 0 20px rgba(255, 255, 255, 0.15); }
        }

        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        footer { text-align: center; padding: 40px; font-size: 11px; opacity: 0.5; }
    </style>
</head>
<body>

<div id="pro-cinematic-intro">
    <div class="intro-icon"><i class="fas fa-play-circle"></i></div>
    <h1 style="color:#fff; font-weight:900; font-size:28px; margin-top:15px;">الخدمة الرقمية</h1>
    <div class="intro-loading-box"><div class="intro-loading-bar"></div></div>
</div>

<div class="bg-pattern-animated"></div>

<div class="header-fixed-container">
    <div class="promo-text">هذه الصفحة مقدمة من متجر الخدمة الرقمية مجاناً وبدون إعلانات<br>للاشتراك في الباقة كاملة على جميع الأجهزة والشاشات تواصل معنا</div>
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a>
        <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a>
        <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000"><i class="fab fa-x-twitter"></i> تويتر</a>
    </div>
</div>

<div class="matches-section">
    <div style="font-size:15px; font-weight:900; margin-bottom:12px; display:flex; align-items:center; gap:8px;"><i class="fas fa-calendar-alt" style="color:#f1c40f"></i> مباريات ونتائج اليوم</div>
    <div class="match-scroll">
        <?php if (isset($match_data['matches'])):
            foreach ($match_data['matches'] as $m): 
                $code = $m['competition']['code'];
                if (isset($leagues_map[$code])): 
                    $is_live = ($m['status'] == 'IN_PLAY' || $m['status'] == 'PAUSED');
                    $homeScore = $m['score']['fullTime']['home'];
                    $awayScore = $m['score']['fullTime']['away'];
        ?>
                <div class="match-card">
                    <div class="league-title-box"><div class="m-league"><?php echo $leagues_map[$code]['name']; ?></div></div>
                    <div class="match-main">
                        <div class="team">
                            <img src="<?php echo $m['homeTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/40'">
                            <span class="team-name"><?php echo translate_name($m['homeTeam']['name']); ?></span>
                        </div>
                        <div class="m-score-container">
                            <?php if ($is_live || $m['status'] == 'FINISHED'): ?>
                                <div class="s-box"><?php echo $homeScore; ?></div>
                                <div class="s-divider">-</div>
                                <div class="s-box"><?php echo $awayScore; ?></div>
                            <?php else: ?>
                                <div style="font-size:11px; font-weight:bold; color:#f1c40f;"><?php echo date('h:i A', strtotime($m['utcDate'])); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="team">
                            <img src="<?php echo $m['awayTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/40'">
                            <span class="team-name"><?php echo translate_name($m['awayTeam']['name']); ?></span>
                        </div>
                    </div>
                    <div class="m-footer">
                        <span style="opacity:0.7;">📺 <?php echo $leagues_map[$code]['channel']; ?></span>
                        <?php if($is_live): ?><span style="color:#ff4d4d; font-weight:900;">● مباشر</span><?php endif; ?>
                        <span style="color:#00ff87; font-weight:900; cursor:pointer;" onclick="goToChannel('<?php echo $leagues_map[$code]['ch_num']; ?>')">شاهد الآن ▶</span>
                    </div>
                </div>
        <?php endif; endforeach; endif; ?>
    </div>
</div>

<div class="grid">
    <div style="grid-column: 1/-1; font-size:18px; font-weight:900; border-bottom:1px solid var(--glass-border); padding-bottom:10px; margin-bottom:5px;">قنوات beIN Sports</div>
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head">
            <div class="name-box-purple">beIN Sport <?php echo $i; ?></div>
            <div class="live-box"><div class="live-dot"></div><span style="font-size:9px; color:#22c55e; font-weight:900;">LIVE</span></div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)"> 
            <i class="fas fa-play"></i> <span>بدء البث المباشر</span>
        </button>
    </div>
    <?php endfor; ?>

    <div style="grid-column: 1/-1; font-size:18px; font-weight:900; border-bottom:1px solid var(--glass-border); padding-top:20px; padding-bottom:10px; margin-bottom:5px;">قنوات STARZPLAY</div>
    <?php for($i = 10; $i <= 11; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head">
            <div class="name-box-green">STARZPLAY <?php echo ($i-9); ?></div>
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
window.addEventListener('load', () => { setTimeout(() => document.getElementById('pro-cinematic-intro').classList.add('intro-finish-vfx'), 2500); });
function goToChannel(num) { const el = document.getElementById('ch-row-' + num); if(el) { window.scrollTo({ top: el.offsetTop - 190, behavior: 'smooth' }); setTimeout(() => el.querySelector('.play-btn-premium').click(), 800); } }
function robustPlay(id, p, b, btn) {
    const v = document.getElementById(id);
    if (v.hls) { v.hls.destroy(); }
    if (Hls.isSupported()) {
        const hls = new Hls(); hls.loadSource(p); hls.attachMedia(v);
        hls.on(Hls.Events.MANIFEST_PARSED, () => v.play());
        v.hls = hls;
    } else { v.src = p; v.play(); }
}
</script>
</body>
</html>
