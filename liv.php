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

// --- 2. جلب إعدادات API المباريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

$leagues_map = [
    'PL'   => ['name' => 'الدوري الإنجليزي'],
    'PD'   => ['name' => 'الدوري الإسباني'],
    'BL1'  => ['name' => 'الدوري الألماني'],
    'SA'   => ['name' => 'الدوري الإيطالي'],
    'FL1'  => ['name' => 'الدوري الفرنسي'],
    'CL'   => ['name' => 'دوري أبطال أوروبا'],
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
$match_response = curl_exec($ch);
curl_close($ch);
$match_data = json_decode($match_response, true);
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
            --main: #e11d48; --bg-deep: #050c14;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.15);
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff); 
            --green-grad: linear-gradient(45deg, #16a34a, #fff);
            --blue-grad: linear-gradient(45deg, #0ea5e9, #fff);
        }
        
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 260px; color: #e2e8f0; overflow-x: hidden; }

        .header-fixed-container { 
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); 
            border-bottom: 1px solid var(--glass-border); padding: 10px 0;
            display: flex; flex-direction: column; align-items: center;
        }

        .category-tabs { display: flex; gap: 12px; width: 95%; overflow-x: auto; scrollbar-width: none; padding: 10px 0; }
        .category-tabs::-webkit-scrollbar { display: none; }
        
        .cat-item { min-width: 75px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 10px 5px; border-radius: 15px; cursor: pointer; text-align: center; transition: 0.3s; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: translateY(-3px); }
        .cat-item img { width: 40px; height: 40px; object-fit: contain; margin-bottom: 5px; }
        .cat-item span { font-size: 9px; font-weight: 900; display: block; color: #fff; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; }
        .channel-section.active { display: grid; }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        .c-head { padding: 12px 18px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; }
        
        .name-box { padding: 5px 15px; border-radius: 8px; color: #061626; font-weight: 900; font-size: 11px; }
        .purple { background: var(--purple-grad); }
        .green { background: var(--green-grad); }
        .blue { background: var(--blue-grad); }
        
        .play-btn { width: 90%; margin: 15px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; }

        .dot-blink { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: blink 1.5s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
    </style>
</head>
<body>

<div class="header-fixed-container">
    <div class="category-tabs">
        <div class="cat-item active" onclick="switchSection('bein', this)"><img src="mg/bein.png"><span>beIN Sport</span></div>
        <div class="cat-item" onclick="switchSection('shahad', this)"><img src="mg/shahd.png"><span>شاهد الرياضية</span></div>
        <div class="cat-item" onclick="switchSection('mbc', this)"><img src="mg/mbc.png"><span>MBC</span></div>
        <div class="cat-item" onclick="switchSection('star', this)"><img src="mg/star.png"><span>Starzplay</span></div>
    </div>
</div>

<div class="grid">
    <div id="section-bein" class="channel-section active">
        <?php for($i = 1; $i <= 9; $i++): ?>
        <div class="card">
            <div class="c-head"><div class="name-box purple">beIN Sport <?php echo $i; ?></div><div class="dot-blink"></div></div>
            <video id="vid<?php echo $i; ?>" playsinline controls></video>
            <button class="play-btn" onclick="robustPlay('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php', this)">تشغيل البث</button>
        </div>
        <?php endfor; ?>
    </div>

    <div id="section-shahad" class="channel-section">
        <div class="card">
            <div class="c-head"><div class="name-box blue">شاهد الرياضية 1</div><div class="dot-blink"></div></div>
            <video id="vid13" playsinline controls></video>
            <button class="play-btn" onclick="robustPlay('vid13', 'b13.php', this)">تشغيل البث</button>
        </div>
        <div class="card">
            <div class="c-head"><div class="name-box blue">شاهد الرياضية 2</div><div class="dot-blink"></div></div>
            <video id="vid14" playsinline controls></video>
            <button class="play-btn" onclick="robustPlay('vid14', 'b14.php', this)">تشغيل البث</button>
        </div>
    </div>

    <div id="section-mbc" class="channel-section">
        <div class="card">
            <div class="c-head"><div class="name-box blue">MBC Action</div><div class="dot-blink"></div></div>
            <video id="vid12" playsinline controls></video>
            <button class="play-btn" onclick="robustPlay('vid12', 'b12.php', this)">تشغيل البث</button>
        </div>
    </div>

    <div id="section-star" class="channel-section">
        <div class="card">
            <div class="c-head"><div class="name-box green">STARZPLAY 1</div><div class="dot-blink"></div></div>
            <video id="vid10" playsinline controls></video>
            <button class="play-btn" onclick="robustPlay('vid10', 'b10.php', this)">تشغيل البث</button>
        </div>
        <div class="card">
            <div class="c-head"><div class="name-box green">STARZPLAY 2</div><div class="dot-blink"></div></div>
            <video id="vid11" playsinline controls></video>
            <button class="play-btn" onclick="robustPlay('vid11', 'b11.php', this)">تشغيل البث</button>
        </div>
    </div>
</div>

<script>
function switchSection(id, el) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    el.classList.add('active');
}

function robustPlay(vId, path, btn) {
    const video = document.getElementById(vId);
    btn.innerText = "جاري التحميل...";
    if (Hls.isSupported()) {
        const hls = new Hls();
        hls.loadSource(path);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, () => { video.play(); btn.innerText = "تم التشغيل"; });
    } else {
        video.src = path;
        video.play();
        btn.innerText = "تم التشغيل";
    }
}
</script>
</body>
</html>
