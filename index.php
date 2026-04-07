<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات السحابة الخاصة بك عثمان ---
$API_KEY_BIN = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$FOOTBALL_API_KEY = 'ef02886bbd68ecb3bdfc630f4546eb97'; // مفتاح المباريات عثمان

// --- نظام جلب مباريات اليوم (الشغل السابق) ---
function getFixtures($date, $key) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://v3.football.api-sports.io/fixtures?date=$date",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["x-apisports-key: $key"],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    return $data['response'] ?: [];
}

// دالة الترجمة التلقائية عثمان
function translateText($text) {
    if(empty($text)) return $text;
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $res = @file_get_contents($url);
    $res = json_decode($res, true);
    return $res[0][0][0] ?: $text;
}

// حصر الدوريات المطلوبة عثمان
$my_leagues = [
    307 => 'الدوري السعودي', 2 => 'أبطال أوروبا', 3 => 'الدوري الأوروبي', 
    39 => 'الدوري الإنجليزي', 140 => 'الدوري الإسباني', 135 => 'الدوري الإيطالي'
];

// جلب المباريات فقط عند طلب صفحة الجدول لتوفير الجهد عثمان
$fixtures = getFixtures(date('Y-m-d'), $FOOTBALL_API_KEY);

// --- نظام القنوات (JSONBIN) ---
function getCloudFullData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER => true);
    curl_setopt($ch, CURLOPT_HTTPHEADER => ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$cloud = getCloudFullData($BIN_ID, $API_KEY_BIN);
$all_channels = $cloud['custom_channels'] ?: [];
$active_sections = array_filter($cloud['sections'] ?: [], function($s) { return $s['status'] == 'show'; });
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --card: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding-bottom: 80px; }
        
        /* شريط التنقل السفلي - عثمان */
        .bottom-nav { position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 500px; height: 70px; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(20px); display: flex; justify-content: space-around; align-items: center; border-top: 1px solid var(--border); z-index: 9000; padding-bottom: env(safe-area-inset-bottom); }
        .nav-item { display: flex; flex-direction: column; align-items: center; color: #94a3b8; text-decoration: none; font-size: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .nav-item i { font-size: 20px; margin-bottom: 4px; }
        .nav-item.active { color: var(--main); }
        .nav-item.active i { transform: translateY(-5px); text-shadow: 0 0 15px var(--main); }

        /* حاويات الصفحات عثمان */
        .page { display: none; padding: 15px; animation: fadeIn 0.4s ease-in-out; }
        .page.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* استايل الجدول عثمان */
        .league-title { background: linear-gradient(90deg, var(--main), transparent); padding: 8px 15px; border-radius: 10px; font-size: 13px; font-weight: 900; margin: 20px 0 10px; border-right: 4px solid #fff; }
        .match-card { background: var(--card); border: 1px solid var(--border); border-radius: 15px; padding: 12px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; }
        .m-team { flex: 1; text-align: center; font-size: 11px; font-weight: 700; }
        .m-team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .m-info { flex: 1; text-align: center; }
        .m-score { font-size: 20px; font-weight: 900; }

        /* الهيدر عثمان */
        .header { text-align: center; padding: 20px 0; background: rgba(225, 29, 72, 0.05); border-bottom: 1px solid var(--border); }
        .header h1 { font-size: 18px; font-weight: 900; margin: 0; color: var(--main); }

        /* قسم من نحن عثمان */
        .about-box { background: var(--card); padding: 25px; border-radius: 20px; border: 1px solid var(--border); text-align: center; line-height: 1.8; }
        .about-box i { font-size: 40px; color: var(--main); margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h1 id="page-title">بث القنوات</h1>
    </div>

    <div id="page-channels" class="page active">
        <div class="grid">
            <?php foreach($all_channels as $ch): ?>
            <div class="match-card">
                 <div style="font-weight:900;"><?= $ch['name'] ?></div>
                 <button onclick="alert('سيتم فتح البث: <?= $ch['name'] ?>')" style="background:var(--main); border:none; color:white; padding:5px 15px; border-radius:10px; font-family:'Tajawal';">مشاهدة</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="page-schedule" class="page">
        <?php 
        $grouped = [];
        foreach($fixtures as $f) { if(isset($my_leagues[$f['league']['id']])) $grouped[$my_leagues[$f['league']['id']]][] = $f; }
        if(empty($grouped)) echo "<p style='text-align:center; opacity:0.5;'>لا توجد مباريات هامة اليوم</p>";
        foreach($grouped as $league => $matches): ?>
            <div class="league-title"><?= $league ?></div>
            <?php foreach($matches as $m): ?>
                <div class="match-card">
                    <div class="m-team"><img src="<?= $m['teams']['home']['logo'] ?>"><?= translateText($m['teams']['home']['name']) ?></div>
                    <div class="m-info">
                        <div class="m-score"><?= date("H:i", $m['fixture']['timestamp']) ?></div>
                        <div style="font-size:9px; opacity:0.5;">بتوقيت مكة</div>
                    </div>
                    <div class="m-team"><img src="<?= $m['teams']['away']['logo'] ?>"><?= translateText($m['teams']['away']['name']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div id="page-news" class="page">
        <div class="about-box">
            <i class="fas fa-newspaper"></i>
            <h2>آخر الأخبار</h2>
            <p>ترقبوا قريباً تغطية حصرية لآخر أخبار الانتقالات والنتائج العالمية والمحلية في هذا القسم.</p>
        </div>
    </div>

    <div id="page-about" class="page">
        <div class="about-box">
            <i class="fas fa-info-circle"></i>
            <h2>من نحن</h2>
            <p>بوابة <b>الخدمة الرقمية</b> هي منصة رياضية متكاملة تهدف لتوفير أفضل تجربة لمتابعة مباريات كرة القدم والنتائج المباشرة. نحن نسعى دائماً لتوفير المعلومة بسرعة ودقة عالية لمتابعينا في الوطن العربي.</p>
            <p style="margin-top:20px; font-size:12px; color:var(--main);">تواصل معنا عبر الواتساب للاقتراحات</p>
        </div>
    </div>

    <div class="bottom-nav">
        <div class="nav-item active" onclick="showPage('channels', 'بث القنوات', this)">
            <i class="fas fa-tv"></i>
            <span>القنوات</span>
        </div>
        <div class="nav-item" onclick="showPage('schedule', 'جدول المباريات', this)">
            <i class="far fa-calendar-alt"></i>
            <span>الجدول</span>
        </div>
        <div class="nav-item" onclick="showPage('news', 'الأخبار الرياضية', this)">
            <i class="fas fa-bullhorn"></i>
            <span>الأخبار</span>
        </div>
        <div class="nav-item" onclick="showPage('about', 'عن المنصة', this)">
            <i class="fas fa-user-shield"></i>
            <span>من نحن</span>
        </div>
    </div>

    <script>
        function showPage(pageId, title, element) {
            // إخفاء كل الصفحات عثمان
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            // إظهار الصفحة المطلوبة
            document.getElementById('page-' + pageId).classList.add('active');
            
            // تغيير العنوان عثمان
            document.getElementById('page-title').innerText = title;
            
            // تغيير الحالة النشطة في الشريط عثمان
            document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
            element.classList.add('active');

            // العودة لأعلى الصفحة عند التبديل
            window.scrollTo(0, 0);
        }
    </script>
</body>
</html>
