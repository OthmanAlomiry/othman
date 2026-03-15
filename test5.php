<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --main: #e11d48; --bg-deep: #061626; --purple-grad: linear-gradient(45deg, #7c3aed, #fff); --green-grad: linear-gradient(45deg, #16a34a, #fff); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; color: #e2e8f0; }
        .promo-sticky-container { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; box-shadow: 0 10px 40px rgba(0,0,0,0.6); }
        .promo-bar { background: rgba(6, 22, 38, 0.96); backdrop-filter: blur(15px); padding: 10px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .social-links { display: flex; justify-content: center; gap: 8px; }
        .social-btn { padding: 5px 12px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 10px; color: #fff; background: #000; }
        .nav-shortcuts { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(25px); display: flex; justify-content: center; gap: 12px; padding: 10px; border-bottom: 1px solid rgba(225, 29, 72, 0.3); }
        .nav-box-purple, .nav-box-green { padding: 6px 18px; border-radius: 8px; font-weight: 900; font-size: 12px; color: #061626; text-decoration: none; }
        .nav-box-purple { background: var(--purple-grad); } .nav-box-green { background: var(--green-grad); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 10px 25px; max-width: 1400px; margin: auto; }
        .card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); }
        .c-head { padding: 10px 15px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
        .name-box-purple { background: var(--purple-grad); padding: 4px 12px; border-radius: 6px; color: #000; font-weight: 900; font-size: 12px; }
        .name-box-green { background: var(--green-grad); padding: 4px 12px; border-radius: 6px; color: #000; font-weight: 900; font-size: 12px; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; object-fit: cover; }
        .play-btn-premium { width: 92%; margin: 18px auto; display: flex; justify-content: center; align-items: center; gap: 12px; background: var(--main); color: #fff; padding: 14px; border-radius: 50px; font-weight: 900; font-size: 15px; cursor: pointer; border: none; }
    </style>
</head>
<body>

<div class="promo-sticky-container">
    <div class="promo-bar">
        <div class="social-links">
            <a href="https://wa.me/966505571164" class="social-btn" style="background:#25d366">واتساب</a>
            <a href="https://t.me/d_s_pro" class="social-btn" style="background:#0088cc">تليجرام</a>
        </div>
    </div>
    <div class="nav-shortcuts">
        <a href="#bein" class="nav-box-purple">beIN Sport</a>
        <a href="#starz" class="nav-box-green">STARZPLAY</a>
    </div>
</div>

<div class="grid" id="bein">
    <?php for($i = 1; $i <= 11; $i++): 
        $isStarz = ($i > 9);
        $title = $isStarz ? "STARZPLAY ".($i-9) : "beIN Sport ".$i;
        $class = $isStarz ? "name-box-green" : "name-box-purple";
    ?>
    <div class="card" <?php if($i==10) echo 'id="starz"'; ?>>
        <div class="c-head">
            <div class="<?php echo $class; ?>"><?php echo $title; ?></div>
            <div style="color:#22c55e; font-size:10px;"><i class="fas fa-circle"></i> مباشر</div>
        </div>
        <video id="vid<?php echo $i; ?>" playsinline controls></video>
        <button class="play-btn-premium" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', 'bs<?php echo $i; ?>.php', this)">
            <i class="fas fa-play"></i> <span>تشغيل البث</span>
        </button>
    </div>
    <?php endfor; ?>
</div>

<script>
/**
 * دالة التشغيل القوية (Robust Play)
 * مصممة خصيصاً لحل مشاكل القنوات 6-7-8-9
 */
function robustPlay(videoId, primary, backup, btn) {
    const video = document.getElementById(videoId);
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-sync fa-spin"></i> جاري الاتصال...';
    btn.disabled = true;

    // محاولة التشغيل
    const hlsLoader = (url, isBackup = false) => {
        if (video.hls) { video.hls.destroy(); }
        
        if (Hls.isSupported()) {
            const hls = new Hls({
                manifestLoadingTimeOut: 15000, // زيادة المهلة لـ 15 ثانية
                manifestLoadingMaxRetry: 4,     // زيادة عدد المحاولات للقنوات الضعيفة
                enableWorker: true
            });
            
            hls.loadSource(url);
            hls.attachMedia(video);
            video.hls = hls;

            hls.on(Hls.Events.MANIFEST_PARSED, () => {
                video.play();
                btn.innerHTML = '<i class="fas fa-check"></i> تم التشغيل';
                setTimeout(() => { btn.innerHTML = originalContent; btn.disabled = false; }, 2000);
            });

            // إذا فشل الرابط الأساسي، حول للاحتياطي تلقائياً
            hls.on(Hls.Events.ERROR, (event, data) => {
                if (data.fatal && !isBackup) {
                    console.warn("فشل الأساسي، جاري الانتقال للاحتياطي...");
                    hlsLoader(backup, true);
                }
            });
        } else {
            video.src = url;
            video.play().catch(() => { if(!isBackup) hlsLoader(backup, true); });
        }
    };

    hlsLoader(primary);
}
</script>
</body>
</html>
