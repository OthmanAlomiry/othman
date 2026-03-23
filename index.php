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

      // دالة التفعيل مع رسالة تأكيد للفحص
      async function forceSubscribe() {
        console.log("محاولة طلب الإشعارات...");
        
        OneSignal.push(async function() {
            // التحقق إذا كان المستخدم مشتركاً بالفعل
            let isSubscribed = await OneSignal.isPushNotificationsEnabled();
            
            if (isSubscribed) {
                alert("أنت مشترك بالفعل في التنبيهات! ستصلك إشعارات المباريات فور بدئها.");
            } else {
                // طلب الإذن مباشرة
                OneSignal.showNativePrompt().then(() => {
                    console.log("تم إرسال الطلب للنظام");
                }).catch((err) => {
                    // إذا فشل الطلب المباشر، نستخدم الطريقة البديلة
                    OneSignal.registerForPushNotifications();
                });
                
                // رسالة مساعدة تظهر فقط إذا تأخر النظام في الاستجابة
                setTimeout(() => {
                    alert("إذا لم تظهر نافذة 'سماح' الآن، يرجى التأكد من أنك تفتح الموقع من أيقونة الشاشة الرئيسية.");
                }, 2000);
            }
        });
      }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg); padding-top: 180px; color: #e2e8f0; overflow-x: hidden; }
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .online-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 4px 12px; border-radius: 50px; color: #22c55e; font-size: 10px; font-weight: 900; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 8px; }
        .dot { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: blink 1.5s infinite; }
        @keyframes blink { 50% { opacity: 0.2; } }
        .subscribe-btn { background: #e11d48; color: white; padding: 12px; font-size: 13px; font-weight: bold; cursor: pointer; border: none; width: 90%; border-radius: 10px; margin-top: 10px; animation: pulse 2s infinite; font-family: 'Tajawal'; }
        @keyframes pulse { 0% { transform: scale(1); } 70% { transform: scale(1.03); } 100% { transform: scale(1); } }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 50px; font-weight: 900; cursor: pointer; text-align: center; }
    </style>
</head>
<body>

<div class="header-fixed-container">
    <div class="online-badge"><div class="dot"></div><span>متواجد الآن: <span id="v-count"><?php echo $online_now; ?></span></span></div>
    <div style="font-size:12px; font-weight:900;">متجر الخدمة الرقمية - بث مباشر للمباريات</div>
    
    <button class="subscribe-btn" onclick="forceSubscribe()">
        <i class="fas fa-bell"></i> تفعيل تنبيهات المباريات 🔔
    </button>

    <div style="display:flex; justify-content:center; gap:15px; margin-top:12px;">
        <a href="https://wa.me/966505571164" style="text-decoration:none; color:#22c55e; font-size:12px; font-weight:bold;">واتساب</a>
        <a href="https://t.me/d_s_pro" style="text-decoration:none; color:#0088cc; font-size:12px; font-weight:bold;">تليجرام</a>
    </div>
</div>

<div class="grid">
    <?php for($i = 1; $i <= 14; $i++): ?>
    <div class="card" id="ch-row-<?php echo $i; ?>">
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
