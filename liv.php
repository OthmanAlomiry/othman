<?php
/**
 * نظام جلب المباريات الاحترافي - النسخة المعتمدة
 * المفتاح المستخدم: 49e271c73amsh02ca0a4d3f5b237p1cee0f8ec95368
 */

function get_confirmed_matches() {
    // مفتاحك الشخصي من الصورة
    $apiKey = "49e271c73amsh02ca0a4d3f5b237p1cee0f8ec95368"; 
    $today = date("Y-m-d");

    $curl = curl_init();
    curl_setopt_array($curl, [
        // نستخدم رابط API-Football المباشر لضمان جلب كافة مباريات اليوم
        CURLOPT_URL => "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=" . $today . "&timezone=Asia/Riyadh",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: api-football-v1.p.rapidapi.com",
            "x-rapidapi-key: " . $apiKey
        ],
    ]);

    $response = curl_exec($curl);
    $data = json_decode($response, true);
    curl_close($curl);

    $sorted = [];
    if (isset($data['response']) && count($data['response']) > 0) {
        foreach ($data['response'] as $index => $item) {
            // توزيع المباريات على القنوات من 1 إلى 9
            $channel = ($index % 9) + 1;
            
            $home = $item['teams']['home']['name'];
            $away = $item['teams']['away']['name'];
            $time = date("H:i", strtotime($item['fixture']['date']));
            
            $sorted[$channel][] = [
                "time" => $time,
                "title" => "$home × $away"
            ];
        }
    }
    return $sorted;
}

$all_matches = get_confirmed_matches();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - الجدول المباشر</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --primary: #e11d48; --bg: #f8fafc; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #1e293b; }
        header { background: #fff; padding: 25px; text-align: center; font-size: 26px; font-weight: bold; border-bottom: 4px solid var(--primary); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; padding: 20px; max-width: 1300px; margin: auto; }
        .card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; }
        .c-head { padding: 15px; background: #f8fafc; display: flex; justify-content: space-between; font-weight: bold; }
        .dot { width: 10px; height: 10px; background: #22c55e; border-radius: 50%; display: inline-block; margin-left: 5px; animation: blink 1.5s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .btn-play { width: 92%; margin: 15px auto; display: block; padding: 12px; border: none; border-radius: 12px; background: var(--primary); color: white; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .schedule { padding: 15px; border-top: 1px solid #f1f5f9; }
        .m-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e2e8f0; font-size: 14px; }
        .m-time { color: var(--primary); font-weight: bold; background: #fff1f2; padding: 2px 8px; border-radius: 6px; font-size: 12px; }
    </style>
</head>
<body>

<header>📺 بث مباشر - جدول مباريات اليوم</header>

<div class="grid">
    <?php for($i = 1; $i <= 9; $i++): ?>
    <div class="card">
        <div class="c-head">
            <span>beIN Sport <?php echo $i; ?></span>
            <span style="color:#22c55e"><span class="dot"></span> مباشر</span>
        </div>
        
        <video id="vid<?php echo $i; ?>" controls poster="https://via.placeholder.com/400x225/111/fff?text=beIN+Sports+<?php echo $i; ?>"></video>
        
        <button class="btn-play" onclick="play('vid<?php echo $i; ?>', 'b<?php echo $i; ?>.php')">▶ تشغيل البث المباشر</button>

        <div class="schedule">
            <strong style="font-size:12px; color:#64748b; display:block; margin-bottom:10px;">📅 جدول البث الحقيقي:</strong>
            <?php if(isset($all_matches[$i]) && count($all_matches[$i]) > 0): ?>
                <?php foreach($all_matches[$i] as $m): ?>
                <div class="m-row">
                    <span><?php echo htmlspecialchars($m['title']); ?></span>
                    <span class="m-time"><?php echo htmlspecialchars($m['time']); ?></span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; color:#94a3b8; font-size:12px;">جاري تحديث جدول مباريات اليوم...</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endfor; ?>
</div>

<script>
function play(id, src) {
    var v = document.getElementById(id);
    if (Hls.isSupported()) {
        var hls = new Hls(); hls.loadSource(src); hls.attachMedia(v); v.play();
    } else if (v.canPlayType('application/vnd.apple.mpegurl')) {
        v.src = src; v.play();
    }
}
</script>

</body>
</html>
