<?php
// ... (نفس الجزء العلوي من ملفك بدون أي تغيير في بيانات السحابة) ...
session_start();
error_reporting(0);
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// دالة جلب الإشعارات
if(isset($_GET['check_notify'])) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $BIN_ID . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $API_KEY, "X-Bin-Meta: false"]);
    $res = json_decode(curl_exec($ch), true);
    echo json_encode($res['notification']); exit;
}
// ... (باقي كود الـ PHP الخاص بجلب القنوات والأقسام كما هو) ...
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ... (نفس ستايل body والألوان الخاص بك) ... */
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); --blue-grad: linear-gradient(45deg, #0ea5e9, #fff); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 240px; color: #e2e8f0; overflow-x: hidden; }

        /* ستايل الإشعار الجديد عثمان */
        #notify-toast { position: fixed; top: -100px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; background: #0ea5e9; color: white; padding: 15px; border-radius: 15px; z-index: 5000; box-shadow: 0 10px 30px rgba(0,0,0,0.5); transition: 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28); display: flex; align-items: center; gap: 10px; font-weight: bold; }
        #notify-toast.show { top: 20px; }
        .notify-bell-btn { position: fixed; bottom: 20px; left: 20px; width: 50px; height: 50px; background: var(--main); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; z-index: 2000; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.4); cursor: pointer; }
        .notify-dot { position: absolute; top: 0; right: 0; width: 12px; height: 12px; background: #22c55e; border-radius: 50%; border: 2px solid var(--bg-deep); display: none; }
        
        /* ... (باقي التنسيقات الخاصة بك للشريط والشبكة) ... */
        .header-fixed { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 10px 0; text-align: center; }
        .online-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 4px 14px; border-radius: 50px; color: #22c55e; font-size: 9px; font-weight: 900; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 8px; }
        .social-links { display: flex; justify-content: center; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
        .social-btn { padding: 6px 14px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; display: flex; align-items: center; gap: 5px; }
        .news-ticker { background: rgba(225, 29, 72, 0.15); border-top: 1px solid rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.05); height: 32px; overflow: hidden; margin-bottom: 8px; display: flex; align-items: center; position: relative; }
        .ticker-label { background: var(--main); color: #fff; padding: 0 12px; height: 100%; display: flex; align-items: center; font-size: 10px; font-weight: 900; z-index: 10; position: absolute; right: 0; }
        .ticker-wrap { flex: 1; overflow: hidden; direction: ltr; position: relative; width: 100%; height: 100%; display: flex; align-items: center; }
        .ticker-move { display: flex; white-space: nowrap; animation: ticker-infinite 50s linear infinite; width: max-content; }
        .ticker-text { color: #fff; font-size: 13px; font-weight: 700; padding: 0 60px; display: inline-block; }
        @keyframes ticker-infinite { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }
        .category-tabs { display: flex; gap: 10px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 8px 0; }
        .cat-item { min-width: 70px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 3px; border-radius: 15px; cursor: pointer; text-align: center; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); }
        .cat-item img { width: 28px; height: 28px; object-fit: contain; margin-bottom: 4px; }
        .cat-item span { font-size: 9px; font-weight: 900; color: #fff; display: block; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; margin-top: 0px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .channel-section.active { display: grid; animation: slideUp 0.6s ease-out; }
        .card { background: var(--glass); border-radius: 25px; overflow: hidden; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); }
        .name-badge { padding: 5px 15px; border-radius: 10px; font-size: 11px; font-weight: 900; color: #000; background: var(--blue-grad); }
        .play-btn { width: 90%; margin: 20px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 50px; font-weight: 900; cursor: pointer; }
    </style>
</head>
<body>

<div id="notify-toast"><i class="fas fa-info-circle"></i> <span id="notify-txt"></span></div>

<div class="notify-bell-btn" onclick="showLastNotify()"><i class="fas fa-bell"></i><div class="notify-dot" id="n-dot"></div></div>

<div class="header-fixed">
    <div class="online-badge"><span>● متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></span></div>
    <div class="social-links">
        <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366">واتساب</a>
        <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc">تليجرام</a>
        <a href="https://snapchat.com/t/4DVEkM5k" class="social-btn" style="background:#FFFC00; color:#000">سناب</a>
        <a href="https://x.com/d_service_pro" class="social-btn" style="background:#000">تويتر</a>
    </div>

    <?php if($news['status'] == 'show'): ?>
    <div class="news-ticker"><span class="ticker-label">تنبيهات</span><div class="ticker-wrap"><div class="ticker-move"><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span><span class="ticker-text"><?= $news['text'] ?></span></div></div></div>
    <?php endif; ?>

    <div class="category-tabs">
        <?php $count = 0; foreach($active_sections as $s): ?>
            <div class="cat-item <?= ($count == 0 ? 'active' : '') ?>" onclick="switchSection('<?= $s['key'] ?>', this)"><img src="<?= $s['img'] ?>"><span><?= $s['name'] ?></span></div>
        <?php $count++; endforeach; ?>
    </div>
</div>

<div class="grid">
    <?php $count = 0; foreach($active_sections as $s): $channels = filterSection($all_channels, $s['key']); ?>
    <div id="section-<?= $s['key'] ?>" class="channel-section <?= ($count == 0 ? 'active' : '') ?>">
        <?php if(empty($channels)): ?><div style="grid-column:1/-1; text-align:center; padding:80px; opacity:0.3;"><p>لا توجد قنوات حالياً.</p></div><?php endif; ?>
        <?php foreach($channels as $ch): ?>
        <div class="card"><div class="c-head"><div class="name-badge"><?= $ch['name'] ?></div><div style="color:#ff4d4d; animation: blink 1s infinite; font-weight:900; font-size:10px;">● مباشر</div></div><div class="video-box" id="vid-<?= $ch['id'] ?>"></div><button class="play-btn" onclick="startStream('vid-<?= $ch['id'] ?>', '<?= $ch['file'] ?>', this)">تشغيل البث</button></div>
        <?php endforeach; ?>
    </div>
    <?php $count++; endforeach; ?>
</div>

<script>
let lastNotifyId = localStorage.getItem('last_notify_id') || "";
let currentNotifyMsg = "";

function checkNotifications() {
    fetch(window.location.pathname + '?check_notify=1')
    .then(res => res.json())
    .then(data => {
        if(data && data.id !== lastNotifyId) {
            lastNotifyId = data.id;
            currentNotifyMsg = data.msg;
            localStorage.setItem('last_notify_id', data.id);
            localStorage.setItem('last_notify_msg', data.msg);
            
            // إظهار الإشعار بوب أب
            document.getElementById('notify-txt').innerText = data.msg;
            document.getElementById('notify-toast').classList.add('show');
            document.getElementById('n-dot').style.display = 'block'; // تنبيه على الجرس
            
            setTimeout(() => { document.getElementById('notify-toast').classList.remove('show'); }, 6000); // يختفي بعد 6 ثواني
        }
    });
}

function showLastNotify() {
    let msg = localStorage.getItem('last_notify_msg');
    if(msg) {
        document.getElementById('notify-txt').innerText = msg;
        document.getElementById('notify-toast').classList.add('show');
        document.getElementById('n-dot').style.display = 'none'; // إخفاء النقطة عند القراءة
        setTimeout(() => { document.getElementById('notify-toast').classList.remove('show'); }, 4000);
    }
}

setInterval(checkNotifications, 10000); // يفحص السحابة كل 10 ثوانٍ
window.addEventListener('load', () => { setTimeout(() => { document.getElementById('pro-intro').style.display='none'; }, 1500); checkNotifications(); });

function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}
function startStream(boxId, file, btn) {
    document.getElementById(boxId).innerHTML = `<iframe src="${file}?autoplay=1&muted=1" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
    btn.innerText = "تم الاتصال";
}
</script>
</body>
</html>
