<?php
$data = json_decode(file_get_contents('matches.json'), true);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - مباشر</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        body { font-family: 'Cairo', sans-serif; background: #f8fafc; margin: 0; }
        header { background: #111827; color: white; padding: 20px; text-align: center; border-bottom: 4px solid #e11d48; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; padding: 20px; }
        .card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; }
        .c-head { padding: 10px; background: #f8fafc; font-weight: bold; border-bottom: 1px solid #eee; }
        video { width: 100%; aspect-ratio: 16/9; background: #000; }
        .btn-play { width: 100%; padding: 12px; border: none; background: #e11d48; color: white; font-weight: bold; cursor: pointer; }
        .match-info { padding: 15px; font-size: 16px; }
        .time { color: #e11d48; font-size: 12px; display: block; margin-top: 5px; }
    </style>
</head>
<body>

<header>📺 جدول المباريات المباشرة</header>

<div class="grid">
    <?php for($i=1; $i<=9; $i++): ?>
    <div class="card">
        <div class="c-head">beIN Sport <?php echo $i; ?></div>
        <video id="vid<?php echo $i; ?>" poster="https://via.placeholder.com/400x225/111/fff?text=beIN"></video>
        <button class="btn-play" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ مشاهدة</button>
        <div class="match-info">
            <strong><?php echo $data[$i]['h'] ?: 'مباراة'; ?> × <?php echo $data[$i]['a'] ?: 'قادمة'; ?></strong>
            <span class="time">⏰ <?php echo $data[$i]['t'] ?: '--:--'; ?></span>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script>
function play(id, src) {
    var v = document.getElementById(id);
    if (Hls.isSupported()) { var hls = new Hls(); hls.loadSource(src); hls.attachMedia(v); v.play(); }
    else { v.src = src; v.play(); }
}
</script>
</body>
</html>
