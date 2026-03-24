<?php
// --- 1. نظام عداد المتواجدين الحقيقي ---
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

// --- 2. جلب إعدادات القنوات ---
$manual_file = 'manual_channels.json';
$manual_channels = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : ['custom_matches' => []];

$channel_names_map = [
    "1"  => "beIN Sport 1", "2"  => "beIN Sport 2", "3"  => "beIN Sport 3",
    "4"  => "beIN Sport 4", "5"  => "beIN Sport 5", "6"  => "beIN Sport 6",
    "7"  => "beIN Sport 7", "8"  => "beIN Sport 8", "9"  => "beIN Sport 9",
    "10" => "STARZPLAY 1", "11" => "STARZPLAY 2",
    "12" => "MBC Action", "13" => "شاهد الرياضية 1", "14" => "شاهد الرياضية 2"
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>الخدمة الرقمية - بث مباشر</title>
    
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignal.js" async=""></script>
    <script>
      window.OneSignal = window.OneSignal || [];
      OneSignal.push(function() {
        OneSignal.init({
          appId: "6e41fb93-1b65-4596-86f4-ad8589b38ad7",
          allowLocalhostAsSecureOrigin: true,
          serviceWorkerPath: "OneSignalSDKWorker.js"
        });
      });

      async function forceSubscribe() {
        OneSignal.push(async function() {
            let isSubscribed = await OneSignal.isPushNotificationsEnabled();
            if (isSubscribed) {
                alert("أنت مشترك بالفعل في التنبيهات!");
            } else {
                OneSignal.showNativePrompt().catch(() => OneSignal.registerForPushNotifications());
                setTimeout(() => { alert("يرجى الضغط على 'سماح' لتفعيل التنبيهات."); }, 1500);
            }
        });
      }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg); padding-top: 250px; color: #e2e8f0; overflow-x: hidden; }
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); padding: 10px 0; text-align: center; }
        .online-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 4px 12px; border-radius: 50px; color: #22c55e; font-size: 10px; font-weight: 900; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 5px; }
        .dot { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: blink 1.5s infinite; }
        @keyframes blink { 50% { opacity: 0.2; } }
        .subscribe-btn { background: #e11d48; color: white; padding: 10px; font-size: 11px; font-weight: bold; cursor: pointer; border: none; width: 90%; border-radius: 10px; margin-top: 5px; font-family: 'Tajawal'; }
        
        /* شريط التصنيفات الجديد */
        .category-scroll { display: flex; justify-content: center; gap: 10px; padding: 10px 0; overflow-x: auto; scrollbar-width: none; }
        .cat-item { padding: 8px 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; font-size: 10px; font-weight: 900; cursor: pointer; color: #fff; transition: 0.3s; display: flex; align-items: center; gap: 5px; white-space: nowrap; }
        .cat-item.active { background: var(--main); border-color: var(--main); box-shadow: 0 0 15px rgba(225,29,72,0.3); }
        .cat-item img { height: 12px; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); transition: 0.3s; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 50px; font-weight: 900; cursor: pointer; text-align: center; }
    </style>
</head>
<body>

<div class="header-fixed-container">
    <div class="online-badge"><div class="dot"></div><span>متواجد الآن: <span id="v-count"><?php echo $online_now; ?></span></span></div>
    
    <div style="display:flex; justify-content:center; gap:10px; margin-bottom:5px;">
        <a href="https://wa.me/966505571164" style="text-decoration:none; color:#22c55e; font-size:11px; font-weight:bold;">واتساب</a>
        <a href="https://t.me/d_s_pro" style="text-decoration:none; color:#0088cc; font-size:11px; font-weight:bold;">تليجرام</a>
    </div>

    <button class="subscribe-btn" onclick="forceSubscribe()">تفعيل تنبيهات المباريات 🔔</button>

    <div class="category-scroll">
        <div class="cat-item active" onclick="filterCat('all', this)">الكل</div>
        <div class="cat-item" onclick="filterCat('bein', this)"><i class="fas fa-star" style="color:#f1c40f"></i> beIN</div>
        <div class="cat-item" onclick="filterCat('shahid', this)"><i class="fas fa-play-circle" style="color:#00ff87"></i> Shahid</div>
        <div class="cat-item" onclick="filterCat('mbc', this)"><i class="fas fa-tv" style="color:#0ea5e9"></i> MBC</div>
        <div class="cat-item" onclick="filterCat('starz', this)"><i class="fas fa-bolt" style="color:#7c3aed"></i> StarzPlay</div>
    </div>
</div>

<div class="grid" id="main-grid">
    <?php for($i = 1; $i <= 14; $i++): 
        // تحديد التصنيف برمجياً
        $cat = "";
        if($i <= 9) $cat = "bein";
        elseif($i == 10 || $i == 11) $cat = "starz";
        elseif($i == 12) $cat = "mbc";
        elseif($i == 13 || $i == 14) $cat = "shahid";
    ?>
    <div class="card channel-card" data-cat="<?php echo $cat; ?>" id="ch-row-<?php echo $i; ?>">
        <div style="padding:10px; background:rgba(0,0,0,0.3); font-size:11px; font-weight:bold; display:flex; justify-content:space-between; align-items:center;">
            <span><?php echo $channel_names_map[$i] ?? "قناة $i"; ?></span>
            <span style="color:#22c55e; font-size:9px;">● LIVE</span>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', this)">بدء البث المباشر</button>
    </div>
    <?php endfor; ?>
</div>

<script>
// دالة الفلترة
function filterCat(cat, el) {
    // تحديث شكل الأزرار
    document.querySelectorAll('.cat-item').forEach(item => item.classList.remove('active'));
    el.classList.add('active');

    // إخفاء وإظهار القنوات
    const cards = document.querySelectorAll('.channel-card');
    cards.forEach(card => {
        if (cat === 'all' || card.getAttribute('data-cat') === cat) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function updateRealtimeVisitors() { fetch('index.php?fetch_visitors=1').then(res => res.text()).then(count => { if(count) document.getElementById('v-count').innerText = count; }); }
setInterval(updateRealtimeVisitors, 4000);

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
