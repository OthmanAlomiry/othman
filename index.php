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

// --- 2. جلب إعدادات القنوات من لوحة التحكم (Admin) ---
$manual_file = 'manual_channels.json';
$manual_channels = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : [];

$channel_names_map = [
    "1"  => "beIN Sport 1", "2"  => "beIN Sport 2", "3"  => "beIN Sport 3",
    "4"  => "beIN Sport 4", "5"  => "beIN Sport 5", "6"  => "beIN Sport 6",
    "7"  => "beIN Sport 7", "8"  => "beIN Sport 8", "9"  => "beIN Sport 9",
    "10" => "STARZPLAY 1", "11" => "STARZPLAY 2",
    "12" => "MBC Action", "13" => "شاهد الرياضية 1", "14" => "شاهد الرياضية 2"
];

// --- 3. إعدادات API المباريات والدوريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

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
];

function translate_name($text) {
    $names_map = [
        'Galatasaray SK' => 'غلطة سراي', 'Fenerbahçe SK' => 'فنربخشة', 'Beşiktaş JK' => 'بشكتاش',
        'Tottenham Hotspur FC' => 'توتنهام', 'Arsenal FC' => 'أرسنال', 'Chelsea FC' => 'تشيلسي', 'Liverpool FC' => 'ليفربول',
        'Manchester City FC' => 'مانشستر سيتي', 'Manchester United FC' => 'مانشستر يونايتد', 'Real Madrid CF' => 'ريال مدريد',
        'FC Barcelona' => 'برشلونة', 'Atlético Madrid' => 'أتلتيكو مدريد', 'Atalanta BC' => 'أتالانتا', 
        'Al Ahly SC' => 'الأهلي المصري', 'Zamalek SC' => 'الزمالك', 'Wydad AC' => 'الوداد الرياضي', 'Raja Casablanca' => 'الرجاء الرياضي',
        'Espérance de Tunis' => 'الترجي التونسي', 'Mamelodi Sundowns FC' => 'صن داونز'
    ];
    if (isset($names_map[$text])) return $names_map[$text];
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if($response) {
        $result = json_decode($response, true);
        $translated = $result[0][0][0] ?? $text;
        $unwanted = ['إس كيه', 'جي كيه', 'إف سي', 'سي اف', 'بي سي', 'نادي', 'كرة القدم', 'الرياضي', 'قبل الميلاد', 'هوتسبير'];
        return trim(str_replace($unwanted, '', $translated));
    }
    return $text;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch); curl_close($ch);
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
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --purple-grad: linear-gradient(45deg, #7c3aed, #fff); --green-grad: linear-gradient(45deg, #16a34a, #fff); --blue-grad: linear-gradient(45deg, #3498db, #fff); --anim-speed: 0.4s; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; overflow-x: hidden; color: #e2e8f0; }
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; width: 100%; z-index: 1000; background: rgba(5, 12, 20, 0.9); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .top-header-row { width: 95%; display: flex; justify-content: flex-start; margin-bottom: 5px; }
        .online-count-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 3px 10px; border-radius: 50px; color: #22c55e; font-size: 9px; font-weight: 900; display: flex; align-items: center; gap: 5px; }
        .dot-blink { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: blink 1.5s infinite; }
        .promo-text { font-size: 11px; font-weight: 700; color: #fff; margin-bottom: 10px; line-height: 1.6; width: 90%; max-width: 600px; margin-inline: auto; }
        .social-links { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
        .social-btn { padding: 7px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; border: 1px solid rgba(255,255,255,0.15); transition: 0.3s; }
        #pro-cinematic-intro { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000; display: flex; flex-direction: column; justify-content: center; align-items: center; z-index: 2000; transition: 1.2s cubic-bezier(0.8, 0, 0.2, 1); }
        .intro-finish-vfx { transform: scale(1.5); opacity: 0; visibility: hidden; }
        .intro-icon { font-size: 90px; color: #fff; filter: drop-shadow(0 0 30px var(--main)); animation: pulseLogo 2s infinite ease-in-out; }
        .intro-loading-box { width: 200px; height: 2px; background: rgba(255,255,255,0.1); margin-top: 30px; border-radius: 10px; overflow: hidden; }
        .intro-loading-bar { width: 0%; height: 100%; background: var(--main); box-shadow: 0 0 15px var(--main); animation: loadProgress 3s forwards; }
        @keyframes loadProgress { to { width: 100%; } }
        @keyframes pulseLogo { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
        @keyframes blink { 50% { opacity: 0.2; } }
        .bg-pattern-animated { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: linear-gradient(135deg, #050c14 0%, #0a1f33 100%); }
        .bg-pattern-animated::after { content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 200%; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.05; animation: movePattern 60s linear infinite; }
        @keyframes movePattern { from { transform: translate(0, 0); } to { transform: translate(-50px, -50px); } }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .card { background: var(--glass); backdrop-filter: blur(20px); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); transition: 0.3s; }
        .c-head { padding: 12px 18px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; }
        .name-box-purple { background: var(--purple-grad); padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        .name-box-green { background: var(--green-grad); padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        .name-box-blue { background: var(--blue-grad); padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        .live-box { display: flex; align-items: center; gap: 6px; background: rgba(34, 197, 94, 0.1); padding: 5px 12px; border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.2); }
        .play-btn-premium { width: 90%; margin: 15px auto; display: flex; justify-content: center; align-items: center; gap: 10px; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 14px; border-radius: 50px; font-weight: 900; font-size: 13px; cursor: pointer; backdrop-filter: blur(5px); animation: glassGlow 3s infinite; }
        @keyframes glassGlow { 0%, 100% { box-shadow: 0 0 10px rgba(255, 255, 255, 0.05); } 50% { box-shadow: 0 0 20px rgba(255, 255, 255, 0.15); } }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .matches-section { padding: 10px 15px; }
        .match-scroll { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
        .match-card { min-width: 280px; background: var(--glass); border-radius: 20px; padding: 0 15px 15px 15px; border: 1px solid var(--glass-border); transition: all 0.3s ease; overflow: hidden; }
        .league-title-box { background: rgba(255, 255, 255, 0.03); border-bottom: 1px solid var(--glass-border); padding: 8px 15px; margin: 0 -15px 15px -15px; text-align: center; color: #00ff87; font-size: 10px; font-weight: 800; }
        .s-box { background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 8px; font-size: 1.4em; font-weight: 900; }
        .channel-label-box { display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.12); padding: 4px 10px; border-radius: 6px; color: #fff; font-size: 9px; font-weight: 700; }
        .channel-label-box i { color: #f1c40f; font-size: 10px; }
        footer { text-align: center; padding: 40px; font-size: 11px; opacity: 0.5; }
    </style>
</head>
<body>
<div id="pro-cinematic-intro"><div class="intro-icon"><i class="fas fa-play-circle"></i></div><h1 style="color:#fff; font-weight:900; font-size:28px; margin-top:15px;">الخدمة الرقمية</h1><div class="intro-loading-box"><div class="intro-loading-bar"></div></div></div>
<div class="bg-pattern-animated"></div>
<div class="header-fixed-container"><div class="top-header-row"><div class="online-count-badge"><div class="dot-blink"></div><span>متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div></div><div class="promo-text">هذه الصفحة مقدمة من متجر الخدمة الرقمية مجاناً وبدون إعلانات<br>للاشتراك في الباقة كاملة على جميع الأجهزة والشاشات تواصل معنا</div><div class="social-links"><a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366"><i class="fab fa-whatsapp"></i> واتساب</a><a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc"><i class="fab fa-telegram-plane"></i> تليجرام</a><a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000"><i class="fab fa-snapchat"></i> سناب</a><a href="https://x.com/d_service_pro" class="social-btn" style="background:#000"><i class="fab fa-x-twitter"></i> تويتر</a></div></div>

<div class="matches-section">
    <div style="font-size:15px; font-weight:900; margin-bottom:12px; display:flex; align-items:center; gap:8px;"><i class="fas fa-calendar-alt" style="color:#f1c40f"></i> مباريات ونتائج اليوم</div>
    <div class="match-scroll">
        <?php if (isset($match_data['matches'])):
            foreach ($match_data['matches'] as $m): 
                $code = $m['competition']['code'];
                if (isset($leagues_map[$code])): 
                    $hName = $m['homeTeam']['name']; $aName = $m['awayTeam']['name'];
                    $match_key = $hName . ' vs ' . $aName;
                    
                    if (isset($manual_channels[$match_key]) && $manual_channels[$match_key] != "") { 
                        $target_ch_num = $manual_channels[$match_key]; 
                        $display_ch = $channel_names_map[$target_ch_num] ?? "beIN Sport " . $target_ch_num;
                    } else { 
                        $display_ch = $leagues_map[$code]['channel']; $target_ch_num = $leagues_map[$code]['ch_num']; 
                    }
                    
                    $status = $m['status'];
                    $is_live = (in_array($status, ['IN_PLAY', 'PAUSED', 'LIVE']));
                    $is_finished = ($status == 'FINISHED');
                    $homeScore = $m['score']['fullTime']['home'] ?? 0;
                    $awayScore = $m['score']['fullTime']['away'] ?? 0;
        ?>
                <div class="match-card">
                    <div class="league-title-box"><div class="m-league"><?php echo $leagues_map[$code]['name']; ?></div></div>
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <div style="flex:1; text-align:center;"><img src="<?php echo $m['homeTeam']['crest']; ?>" width="35" onerror="this.src='https://via.placeholder.com/40'"><span style="font-size:9px; font-weight:700; margin-top:5px; display:block;"><?php echo translate_name($hName); ?></span></div>
                        <div style="flex: 1.2; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px;">
                            <?php if ($is_live || $is_finished): ?>
                                <div style="display:flex; align-items:center; gap:5px;"><div class="s-box"><?php echo $homeScore; ?></div><span style="font-weight:900; opacity:0.6;">-</span><div class="s-box"><?php echo $awayScore; ?></div></div>
                                <?php if($is_live): ?><span style="color:#ff4d4d; font-size:8px; font-weight:900; animation: blink 1s infinite;">● مباشر</span><?php elseif($is_finished): ?><span style="color:#aaa; font-size:8px; font-weight:900;">انتهت</span><?php endif; ?>
                            <?php else: ?>
                                <div style="font-size:11px; font-weight:bold; color:#f1c40f; text-align:center;"><?php echo date('h:i A', strtotime($m['utcDate'])); ?><br><span style="font-size:8px; opacity:0.6;">لم تبدأ</span></div>
                            <?php endif; ?>
                        </div>
                        <div style="flex:1; text-align:center;"><img src="<?php echo $m['awayTeam']['crest']; ?>" width="35" onerror="this.src='https://via.placeholder.com/40'"><span style="font-size:9px; font-weight:700; margin-top:5px; display:block;"><?php echo translate_name($aName); ?></span></div>
                    </div>
                    <div style="border-top: 1px solid var(--glass-border); padding-top: 10px; display: flex; justify-content: space-between; font-size: 9px; align-items: center;">
                        <div class="channel-label-box"><i class="fas fa-tv"></i> <?php echo $display_ch; ?></div>
                        <span style="color:#00ff87; font-weight:900; cursor:pointer;" onclick="goToChannel('<?php echo $target_ch_num; ?>')">شاهد الآن ▶</span>
                    </div>
                </div>
        <?php endif; endforeach; endif; ?>
    </div>
</div>

<div class="grid">
    <div style="grid-column: 1/-1; font-size:18px; font-weight:900; border-bottom:1px solid var(--glass-border); padding-bottom:10px; margin-bottom:5px;">قنوات beIN Sports</div>
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head"><div class="name-box-purple">beIN Sport <?php echo $i; ?></div><div class="live-box"><div class="live-dot" style="width:7px; height:7px; background:#22c55e; border-radius:50%; animation:blink 1s infinite;"></div><span style="font-size:9px; color:#22c55e; font-weight:900; margin-right:5px;">LIVE</span></div></div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)"><span>بدء البث المباشر</span></button>
    </div>
    <?php endfor; ?>
    <div style="grid-column: 1/-1; font-size:18px; font-weight:900; border-bottom:1px solid var(--glass-border); padding-top:20px; padding-bottom:10px; margin-bottom:5px;">قنوات STARZPLAY</div>
    <?php for($i = 10; $i <= 11; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div class="c-head"><div class="name-box-green">STARZPLAY <?php echo ($i-9); ?></div><div class="live-box"><div class="live-dot" style="width:7px; height:7px; background:#22c55e; border-radius:50%; animation:blink 1s infinite;"></div><span style="font-size:9px; color:#22c55e; font-weight:900; margin-right:5px;">LIVE</span></div></div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)"><span>بدء البث المباشر</span></button>
    </div>
    <?php endfor; ?>
    <div style="grid-column: 1/-1; font-size:18px; font-weight:900; border-bottom:1px solid var(--glass-border); padding-top:20px; padding-bottom:10px; margin-bottom:5px;">قنوات منوعة ورياضية</div>
    <div class="card" id="ch-row-12"><div class="c-head"><div class="name-box-blue">MBC Action</div><div class="live-box"><div class="live-dot" style="width:7px; height:7px; background:#22c55e; border-radius:50%; animation:blink 1s infinite;"></div><span style="font-size:9px; color:#22c55e; font-weight:900; margin-right:5px;">LIVE</span></div></div><video id="vid12" playsinline controls></video><button class="play-btn-premium" onclick="robustPlay('vid12', 'b12.php', 'bs12.php', this)"><span>بدء البث المباشر</span></button></div>
    <div class="card" id="ch-row-13"><div class="c-head"><div class="name-box-blue">شاهد MBC الرياضية 1</div><div class="live-box"><div class="live-dot" style="width:7px; height:7px; background:#22c55e; border-radius:50%; animation:blink 1s infinite;"></div><span style="font-size:9px; color:#22c55e; font-weight:900; margin-right:5px;">LIVE</span></div></div><video id="vid13" playsinline controls></video><button class="play-btn-premium" onclick="robustPlay('vid13', 'b13.php', 'bs13.php', this)"><span>بدء البث المباشر</span></button></div>
    <div class="card" id="ch-row-14"><div class="c-head"><div class="name-box-blue">شاهد MBC الرياضية 2</div><div class="live-box"><div class="live-dot" style="width:7px; height:7px; background:#22c55e; border-radius:50%; animation:blink 1s infinite;"></div><span style="font-size:9px; color:#22c55e; font-weight:900; margin-right:5px;">LIVE</span></div></div><video id="vid14" playsinline controls></video><button class="play-btn-premium" onclick="robustPlay('vid14', 'b14.php', 'bs14.php', this)"><span>بدء البث المباشر</span></button></div>
</div>

<footer>جميع الحقوق محفوظة لمتجر الخدمة الرقمية</footer>

<script>
window.addEventListener('load', () => { setTimeout(() => document.getElementById('pro-cinematic-intro').classList.add('intro-finish-vfx'), 2500); });
function updateRealtimeVisitors() { fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => { if(count && !isNaN(count)) document.getElementById('realtime-visitors').innerText = count; }).catch(e => {}); }
setInterval(updateRealtimeVisitors, 3000);
function goToChannel(num) { const el = document.getElementById('ch-row-' + num); if(el) { window.scrollTo({ top: el.offsetTop - 190, behavior: 'smooth' }); setTimeout(() => el.querySelector('.play-btn-premium').click(), 800); } }
function robustPlay(vId, p, b, btn) {
    const video = document.getElementById(vId); const btnText = btn.querySelector('span'); btnText.innerText = "جاري تشغيل القناة...";
    if (video.hls) { video.hls.destroy(); }
    if (Hls.isSupported()) {
        const hls = new Hls(); hls.loadSource(p); hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => { video.play(); btnText.innerText = "تم تشغيل البث بنجاح"; });
        video.hls = hls;
    } else { video.src = p; video.play(); btnText.innerText = "تم تشغيل البث بنجاح"; }
}
</script>
</body>
</html>
