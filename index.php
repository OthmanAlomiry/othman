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

// --- 2. جلب إعدادات القنوات والمباريات اليدوية ---
$manual_file = 'manual_channels.json';
$manual_channels = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : ['custom_matches' => []];

$channel_names_map = [
    "1"  => "beIN Sport 1", "2"  => "beIN Sport 2", "3"  => "beIN Sport 3",
    "4"  => "beIN Sport 4", "5"  => "beIN Sport 5", "6"  => "beIN Sport 6",
    "7"  => "beIN Sport 7", "8"  => "beIN Sport 8", "9"  => "beIN Sport 9",
    "10" => "STARZPLAY 1", "11" => "STARZPLAY 2",
    "12" => "MBC Action", "13" => "شاهد الرياضية 1", "14" => "شاهد الرياضية 2"
];

// --- 3. إعدادات API المباريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

function translate_name($text) {
    $names_map = ['Real Madrid CF' => 'ريال مدريد', 'FC Barcelona' => 'برشلونة', 'Al Ahly SC' => 'الأهلي المصري', 'Zamalek SC' => 'الزمالك'];
    if (isset($names_map[$text])) return $names_map[$text];
    $api_url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $res = @file_get_contents($api_url);
    if($res) { $json = json_decode($res, true); return $json[0][0][0] ?? $text; }
    return $text;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$match_data = json_decode(curl_exec($ch), true); curl_close($ch);
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignal.js" async=""></script>
    <script>
      window.OneSignal = window.OneSignal || [];
      OneSignal.push(function() {
        OneSignal.init({
          appId: "6e41fb93-1b65-4596-86f4-ad8589b38ad7",
          notifyButton: { enable: true },
          allowLocalhostAsSecureOrigin: true,
        });
      });
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg); padding-top: 175px; color: #e2e8f0; overflow-x: hidden; }
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.9); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .online-count-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 3px 10px; border-radius: 50px; color: #22c55e; font-size: 9px; font-weight: 900; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 10px; }
        .social-btn { padding: 7px 15px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; border: 1px solid rgba(255,255,255,0.15); }
        .match-scroll { display: flex; gap: 12px; overflow-x: auto; padding: 15px; scrollbar-width: none; }
        .match-card { min-width: 280px; background: var(--glass); border-radius: 20px; padding: 15px; border: 1px solid var(--glass-border); position: relative; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn-premium { width: 90%; margin: 15px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 50px; font-weight: 900; cursor: pointer; }
    </style>
</head>
<body>

<div class="header-fixed-container">
    <div class="online-count-badge"><span>متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div>
    <div style="font-size:11px; font-weight:700; margin-bottom:10px;">متجر الخدمة الرقمية - بث مباشر بدون إعلانات</div>
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366">واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc">تليجرام</a>
    </div>
</div>

<div class="match-scroll">
    <?php if(isset($manual_channels['custom_matches'])) foreach($manual_channels['custom_matches'] as $cm): ?>
        <div class="match-card">
            <div style="text-align:center; color:#00ff87; font-size:10px; margin-bottom:10px;"><?php echo $cm['league']; ?></div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="text-align:center;"><img src="<?php echo $cm['home_logo'] ?: 'https://via.placeholder.com/40'; ?>" width="35" height="35" style="border-radius:50%;"><br><span style="font-size:9px;"><?php echo $cm['home']; ?></span></div>
                <div style="text-align:center; font-size:12px; font-weight:bold; color:#f1c40f;"><?php echo ($cm['ch']=='postponed'?'مؤجلة':$cm['time']); ?></div>
                <div style="text-align:center;"><img src="<?php echo $cm['away_logo'] ?: 'https://via.placeholder.com/40'; ?>" width="35" height="35" style="border-radius:50%;"><br><span style="font-size:9px;"><?php echo $cm['away']; ?></span></div>
            </div>
            <div style="border-top: 1px solid var(--glass-border); margin-top:10px; padding-top:10px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:9px; color:#aaa;"><?php echo ($cm['ch']=='postponed'?'غير متاح':$channel_names_map[$cm['ch']]??"قناة ".$cm['ch']); ?></span>
                <?php if($cm['ch']!='postponed'): ?><span style="font-size:9px; color:#00ff87; font-weight:bold; cursor:pointer;" onclick="goToChannel('<?php echo $cm['ch']; ?>')">شاهد الآن ▶</span><?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 14; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
        <div style="padding:10px; background:rgba(0,0,0,0.3); font-size:11px; font-weight:bold; display:flex; justify-content:space-between;">
            <span><?php echo $channel_names_map[$i] ?? "قناة $i"; ?></span>
            <span style="color:#22c55e;">● LIVE</span>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', this)">بدء البث المباشر</button>
    </div>
    <?php endfor; ?>
</div>

<script>
function updateRealtimeVisitors() { fetch('index.php?fetch_visitors=1').then(res => res.text()).then(count => { document.getElementById('realtime-visitors').innerText = count; }); }
setInterval(updateRealtimeVisitors, 3000);
function goToChannel(num) { const el = document.getElementById('ch-row-' + num); if(el) window.scrollTo({ top: el.offsetTop - 190, behavior: 'smooth' }); }
function robustPlay(vId, p, btn) {
    const video = document.getElementById(vId); btn.innerText = "جاري التحميل...";
    if (Hls.isSupported()) {
        const hls = new Hls(); hls.loadSource(p); hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => { video.play(); btn.innerText = "تم التشغيل بنجاح"; });
    } else { video.src = p; video.play(); }
}
</script>
</body>
</html>
