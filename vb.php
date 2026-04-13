<?php
// --- إعدادات API المباريات ---
$apiKey = '49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9'; 
$dateToday = date('Y-m-d');
// جلب كافة مباريات اليوم بدون فلاتر لضمان الظهور
$url = "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$dateToday";

// تعريف البطولات الهامة لتمييزها فقط
$important_leagues = [850, 307, 39, 140, 2, 135]; 

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
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
    "X-RapidAPI-Key: $apiKey"
]);
$response = curl_exec($ch);
curl_close($ch);
$match_data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh');
?>

<div id="matches-standalone-table" style="font-family: 'Tajawal', sans-serif; direction: rtl; background: #050c14; padding: 15px; border-radius: 15px; max-width: 600px; margin: auto;">
    <style>
        .m-row { 
            background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); 
            border-radius: 10px; padding: 10px; margin-bottom: 8px; display: flex; align-items: center; 
        }
        .m-side { flex: 1; text-align: center; font-size: 11px; color: #fff; }
        .m-side img { width: 28px; height: 28px; object-fit: contain; margin-bottom: 4px; }
        .m-info { flex: 1.2; text-align: center; }
        .m-lg { font-size: 8px; color: #00ff87; font-weight: bold; margin-bottom: 4px; display: block; }
        .m-sc { font-size: 18px; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .m-tm { font-size: 11px; color: #f1c40f; font-weight: bold; }
        .live-tag { font-size: 8px; color: #ff4d4d; font-weight: 900; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0.3; } }
    </style>

    <h5 style="color: #fff; text-align: center; margin-bottom: 15px;">📅 مباريات اليوم</h5>

    <?php 
    if (!empty($match_data['response'])):
        // سنعرض أول 15 مباراة تتوفر اليوم لضمان عدم بقاء الصفحة فارغة
        $count = 0;
        foreach ($match_data['response'] as $m): 
            if ($count >= 15) break; 
            $status = $m['fixture']['status']['short'];
            $is_live = in_array($status, ['1H','2H','HT','ET','P','LIVE']);
    ?>
    <div class="m-row">
        <div class="m-side">
            <img src="<?= $m['teams']['home']['logo'] ?>">
            <span><?= translate_name($m['teams']['home']['name']) ?></span>
        </div>
        
        <div class="m-info">
            <span class="m-lg"><?= translate_name($m['league']['name']) ?></span>
            <?php if($is_live || $status == 'FT'): ?>
                <div class="m-sc"><?= ($m['goals']['home'] ?? 0) ?> - <?= ($m['goals']['away'] ?? 0) ?></div>
                <?php if($is_live): ?><span class="live-tag">● مباشر</span><?php endif; ?>
            <?php else: ?>
                <div class="m-tm"><?= date('h:i A', strtotime($m['fixture']['date'])) ?></div>
            <?php endif; ?>
        </div>

        <div class="m-side">
            <img src="<?= $m['teams']['away']['logo'] ?>">
            <span><?= translate_name($m['teams']['away']['name']) ?></span>
        </div>
    </div>
    <?php 
            $count++;
        endforeach; 
    else: 
    ?>
        <div style="text-align:center; color:#666; font-size:12px; padding:20px;">لا يوجد بيانات مباريات متاحة حالياً. تأكد من مفتاح الـ API.</div>
    <?php endif; ?>
</div>
