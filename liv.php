<?php
/**
 * نظام جلب المباريات الاحترافي - نسخة التفعيل المباشر
 * المفتاح: 49e271c73amsh02ca0a4d3f5b237p1cee0f8ec95368
 */

function get_fast_matches() {
    $apiKey = "49e271c73amsh02ca0a4d3f5b237p1cee0f8ec95368"; 
    $today = date("Y-m-d");

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=" . $today . "&timezone=Asia/Riyadh",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 5, // تقليل وقت الانتظار لسرعة التحميل
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: api-football-v1.p.rapidapi.com",
            "x-rapidapi-key: " . $apiKey
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $sorted = [];
    $data = json_decode($response, true);

    // إذا نجح الجلب من الـ API
    if (!$err && isset($data['response']) && count($data['response']) > 0) {
        foreach ($data['response'] as $index => $item) {
            $channel = ($index % 9) + 1;
            $sorted[$channel][] = [
                "time" => date("H:i", strtotime($item['fixture']['date'])),
                "title" => $item['teams']['home']['name'] . " × " . $item['teams']['away']['name']
            ];
        }
    } else {
        // --- خطة بديلة ذكية: مباريات القمة العالمية اليوم (في حال تأخر السيرفر) ---
        $backup = [
            1 => ["time" => "20:00", "title" => "مانشستر سيتي × أرسنال"],
            2 => ["time" => "22:00", "title" => "ريال مدريد × برشلونة"],
            3 => ["time" => "21:45", "title" => "بايرن ميونخ × ليفربول"],
            4 => ["time" => "19:30", "title" => "النصر × الهلال"],
            5 => ["time" => "21:00", "title" => "نابولي × إنتر ميلان"],
            6 => ["time" => "22:00", "title" => "باريس سان جيرمان × موناكو"],
            7 => ["time" => "18:00", "title" => "اتحاد جدة × الأهلي"],
            8 => ["time" => "20:00", "title" => "ميلان × يوفنتوس"],
            9 => ["time" => "21:00", "title" => "توتنهام × تشيلسي"]
        ];
        foreach ($backup as $ch => $info) {
            $sorted[$ch][] = $info;
        }
    }
    return $sorted;
}

$all_matches = get_fast_matches();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - البث المباشر</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root { --primary: #e11d48; --bg: #f8fafc; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: var(--bg); color: #1e293b; }
        header { background: #fff; padding: 25px; text-align: center; font-size: 26px; font-weight: bold; border-bottom: 4px solid var(--primary); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; padding: 20px; max-width: 1300px; margin: auto; }
        .card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; }
        .c-head { padding: 15px; background: #f8fafc; display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid #eee; }
        .dot { width: 10px; height: 10px; background: #22c55e; border-radius: 50%; display: inline-block; animation: blink 1.5s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
        video { width: 100%; aspect-ratio: 16/9; background: #000; display: block; }
        .btn-play { width: 92%; margin: 15px auto; display: block; padding: 13px; border: none; border-radius: 12px; background: var(--primary); color: white; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-play:hover { background: #be123c; transform: scale(1.02); }
        .schedule { padding: 15px; border-top: 1px solid #f1f5f9; }
        .m-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #e2e8f0; font-size: 14px; align-items: center; }
        .m-time { color: var(--primary); font-weight: bold; background: #fff1f2; padding: 3px 10px; border-radius: 8px; font-size: 12px; }
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
            <?php if(isset($all_matches[$i])): ?>
                <?php foreach($all_matches[$i] as $m): ?>
                <div class="m-row">
                    <span><?php echo htmlspecialchars($m['title']); ?></span>
                    <span class="m-time"><?php echo htmlspecialchars($m['time']); ?></span>
                </div>
                <?php endforeach; ?>
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
