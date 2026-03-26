<?php
session_start();
// --- نظام عداد المتواجدين ---
$visitors_file = 'online_visitors.txt';
if (isset($_GET['fetch_visitors'])) {
    $session_id = session_id(); $time = time();
    $data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
    $data[$session_id] = $time;
    foreach ($data as $id => $last_time) { if ($time - $last_time > 120) unset($data[$id]); }
    file_put_contents($visitors_file, serialize($data));
    echo count($data); exit; 
}
$online_now = file_exists($visitors_file) ? count(unserialize(file_get_contents($visitors_file))) : 1;

// --- جلب بيانات القنوات من اللوحة عثمان ---
$db_file = 'channels_db.json';
$channels_db = file_exists($db_file) ? json_decode(file_get_contents($db_file), true) : ['custom_channels' => []];

function getChannels($section, $db) {
    return array_filter($db['custom_channels'], function($ch) use ($section) {
        return ($ch['section'] == $section && $ch['status'] == 'show');
    });
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 280px; color: #e2e8f0; overflow-x: hidden; }
        
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.95); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 10px 0; }
        .online-badge { background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); padding: 4px 15px; border-radius: 50px; color: #22c55e; font-size: 10px; font-weight: 900; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 10px; }
        
        .category-tabs { display: flex; gap: 12px; width: 95%; margin: 0 auto; overflow-x: auto; scrollbar-width: none; padding: 10px 0; }
        .cat-item { min-width: 85px; flex-shrink: 0; background: var(--glass); border: 1px solid var(--glass-border); padding: 12px 5px; border-radius: 15px; cursor: pointer; text-align: center; transition: 0.3s; }
        .cat-item.active { background: rgba(225, 29, 72, 0.2); border-color: var(--main); transform: translateY(-3px); }
        .cat-item img { width: 35px; height: 35px; object-fit: contain; margin-bottom: 5px; }
        .cat-item span { font-size: 9px; font-weight: 900; color: #fff; display: block; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .channel-section { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; }
        .channel-section.active { display: grid; animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        .c-head { padding: 12px; background: rgba(0,0,0,0.3); display: flex; justify-content: space-between; align-items: center; }
        .play-btn { width: 90%; margin: 15px auto; display: block; background: rgba(255, 255, 255, 0.08); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px; border-radius: 50px; font-weight: 900; cursor: pointer; }
        .video-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
    </style>
</head>
<body>

<div class="header-fixed-container">
    <div style="text-align: center;">
        <div class="online-badge">● متواجد الآن: <span id="realtime-visitors"><?php echo $online_now; ?></span></div>
    </div>
    
    <div class="category-tabs">
        <div class="cat-item active" onclick="switchSection('bein', this)"><img src="mg/bein.png"><span>beIN Sport</span></div>
        <div class="cat-item" onclick="switchSection('mbc', this)"><img src="mg/mbc.png"><span>باقة MBC</span></div>
        <div class="cat-item" onclick="switchSection('alkas', this)"><img src="mg/alkas.png"><span>الكاس</span></div>
        <div class="cat-item" onclick="switchSection('kuwait', this)"><img src="mg/ku.png"><span>الكويت</span></div>
        <div class="cat-item" onclick="switchSection('on', this)"><img src="mg/on.png"><span>On Sport</span></div>
        <div class="cat-item" onclick="switchSection('shahad', this)"><img src="mg/shahd.png"><span>شاهد</span></div>
    </div>
</div>

<div class="grid">
    <?php 
    $sections = ['bein', 'mbc', 'alkas', 'kuwait', 'on', 'shahad', 'ado', 'dubai', 'moc'];
    foreach($sections as $sec): 
        $channels = getChannels($sec, $channels_db);
    ?>
    <div id="section-<?php echo $sec; ?>" class="channel-section <?php echo ($sec == 'bein' ? 'active' : ''); ?>">
        <?php if(empty($channels)) echo "<p style='text-align:center; grid-column:1/-1; opacity:0.5;'>لا توجد قنوات ظاهرة في هذا القسم حالياً.</p>"; ?>
        <?php foreach($channels as $ch): ?>
        <div class="card">
            <div class="c-head"><div style="background:#7c3aed; color:#fff; padding:4px 12px; border-radius:6px; font-size:11px; font-weight:900;"><?php echo $ch['name']; ?></div></div>
            <div class="video-box" id="vid-<?php echo $ch['id']; ?>"></div>
            <button class="play-btn" onclick="startStream('vid-<?php echo $ch['id']; ?>', '<?php echo $ch['file']; ?>', this)">تشغيل البث</button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
function switchSection(id, element) {
    document.querySelectorAll('.channel-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    element.classList.add('active');
}

function startStream(boxId, file, btn) {
    document.getElementById(boxId).innerHTML = `<iframe src="${file}" allowfullscreen allow="autoplay"></iframe>`;
    btn.innerText = "جاري البث...";
}

setInterval(() => {
    fetch(window.location.pathname + '?fetch_visitors=1').then(res => res.text()).then(count => {
        document.getElementById('realtime-visitors').innerText = count;
    });
}, 4000);
</script>
</body>
</html>
