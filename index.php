<?php
session_start();
ini_set('display_errors', 1); // إظهار الخطأ لو حدث بدلاً من الصفحة البيضاء
error_reporting(E_ALL);

$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// دالة الاتصال السحابي باستخدام cURL (أفضل لـ Render)
function getCloudData($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$data = getCloudData($BIN_ID, $API_KEY);
$all_channels = isset($data['custom_channels']) ? $data['custom_channels'] : [];

function filterSec($channels, $sec) {
    return array_filter($channels, function($c) use ($sec) {
        return (isset($c['section']) && trim(strtolower($c['section'])) == trim(strtolower($sec)));
    });
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255,255,255,0.05); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: white; padding-top: 250px; }
        .header { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5,12,20,0.9); backdrop-filter: blur(15px); padding: 15px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .tabs { display: flex; gap: 10px; overflow-x: auto; padding: 10px; justify-content: center; }
        .tab { min-width: 80px; background: var(--glass); padding: 10px; border-radius: 15px; cursor: pointer; font-size: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .tab.active { border-color: var(--main); background: rgba(225,29,72,0.1); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; padding: 20px; }
        .sec { display: none; grid-column: 1/-1; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .sec.active { display: grid; }
        .card { background: var(--glass); border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; }
        .v-box { width: 100%; aspect-ratio: 16/9; background: #000; }
        iframe { width: 100%; height: 100%; border: none; }
        .btn { width: 90%; margin: 15px auto; display: block; background: var(--main); border: none; color: white; padding: 12px; border-radius: 50px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="header">
    <div style="color:#22c55e; margin-bottom:10px; font-size:12px;">● متصل بالسحابة</div>
    <div class="tabs">
        <div class="tab active" onclick="sw('bein', this)">beIN</div>
        <div class="tab" onclick="sw('mbc', this)">MBC</div>
        <div class="tab" onclick="sw('alkas', this)">الكاس</div>
        <div class="tab" onclick="sw('shahad', this)">شاهد</div>
    </div>
</div>

<div class="grid">
    <?php foreach(['bein', 'mbc', 'alkas', 'shahad'] as $s): 
        $list = filterSec($all_channels, $s); ?>
        <div id="s-<?php echo $s; ?>" class="sec <?php echo ($s=='bein'?'active':''); ?>">
            <?php foreach($list as $ch): ?>
            <div class="card">
                <div style="padding:15px; font-weight:bold;"><?php echo $ch['name']; ?></div>
                <div class="v-box" id="v-<?php echo $ch['id']; ?>"></div>
                <button class="btn" onclick="play('v-<?php echo $ch['id']; ?>','<?php echo $ch['file']; ?>',this)">تشغيل البث</button>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<script>
function sw(id, el) {
    document.querySelectorAll('.sec').forEach(x => x.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
    document.getElementById('s-'+id).classList.add('active');
    el.classList.add('active');
}
function play(id, f, b) {
    document.getElementById(id).innerHTML = `<iframe src="${f}?autoplay=1&muted=1" allow="autoplay" allowfullscreen></iframe>`;
    b.innerText = "تم الاتصال";
}
</script>
</body>
</html>
