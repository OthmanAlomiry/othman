<script>
window.addEventListener('load', function() {
    setTimeout(function() {
        const intro = document.getElementById('pro-cinematic-intro');
        if(intro) { intro.classList.add('intro-finish-vfx'); setTimeout(() => intro.remove(), 1200); updateCounter(); }
    }, 3500); 
});

function updateCounter() {
    let count = localStorage.getItem('vCount') || 1452;
    count = parseInt(count) + 1;
    localStorage.setItem('vCount', count);
    document.getElementById('count-num').innerText = count.toLocaleString();
}

/**
 * نظام تشغيل فائق القوة للقنوات 6 إلى 9
 */
async function smartPlay(videoId, primary, backup) {
    const video = document.getElementById(videoId);
    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-sync fa-spin"></i> <span>جاري تهيئة البث...</span>';
    btn.style.pointerEvents = 'none';

    // تنظيف المشغل القديم تماماً
    if (video.hls) {
        video.hls.destroy();
        delete video.hls;
    }

    try {
        // محاولة جلب رابط البث من ملف b.php
        const response = await fetch(primary);
        const streamUrl = await response.text();
        const finalUrl = streamUrl.trim();

        if (finalUrl && finalUrl.startsWith('http')) {
            console.log("تشغيل الرابط الأساسي لـ: " + videoId);
            runHls(video, finalUrl);
            
            let playCheck = false;
            video.onplaying = () => { 
                playCheck = true; 
                btn.innerHTML = '<i class="fas fa-check-circle"></i> <span>بث مباشر مستقر</span>';
                setTimeout(() => { btn.innerHTML = originalText; btn.style.pointerEvents = 'auto'; }, 2000);
            };

            // مهلة انتظار أطول (10 ثوانٍ) للقنوات 6-9
            setTimeout(() => {
                if (!playCheck) {
                    console.warn("الأساسي معلق.. تفعيل الاحتياطي");
                    triggerBackup(video, backup, btn, originalText);
                }
            }, 10000);

        } else {
            triggerBackup(video, backup, btn, originalText);
        }
    } catch (e) {
        triggerBackup(video, backup, btn, originalText);
    }
}

function triggerBackup(video, backupFile, btn, originalText) {
    btn.innerHTML = '<i class="fas fa-shield-alt"></i> <span>تفعيل الاحتياطي...</span>';
    fetch(backupFile).then(r => r.text()).then(url => {
        runHls(video, url.trim());
        setTimeout(() => { btn.innerHTML = originalText; btn.style.pointerEvents = 'auto'; }, 3000);
    }).catch(() => {
        btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span>خطأ في المصدر</span>';
        setTimeout(() => { btn.innerHTML = originalText; btn.style.pointerEvents = 'auto'; }, 3000);
    });
}

function runHls(video, url) {
    if (Hls.isSupported()) {
        const hls = new Hls({
            manifestLoadingTimeOut: 15000,
            manifestLoadingMaxRetry: 4,
            fragLoadingTimeOut: 20000,
            enableWorker: true, // استخدام قوة المعالج لتسريع البث
            xhrSetup: function (xhr) {
                xhr.withCredentials = false; // تجاوز مشاكل الكوكيز
            }
        });
        hls.loadSource(url);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
        video.hls = hls;
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = url;
        video.play();
    }
}
</script>
