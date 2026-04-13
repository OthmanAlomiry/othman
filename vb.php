<?php
// --- إعدادات API المباريات باستخدام مفتاحك ---
$apiKey = '49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9'; 
$dateToday = date('Y-m-d');
// تم إزالة الفلاتر لجلب كافة المباريات المتاحة عالمياً اليوم
$url = "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$dateToday";

// خريطة البطولات المفضلة لتمييزها (يمكنك إضافة أي ID بطولة هنا)
$leagues_map = [
    850 => ['name' => 'دوري أبطال آسيا للنخبة', 'channel' => 'beIN AFC', 'ch_num' => '11'],
    307 => ['name' => 'دوري روشن السعودي', 'channel' => 'SSC 1', 'ch_num' => '12'],
    39  => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    140 => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    2   => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2', 'ch_num' => '2'],
    135 => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1', 'ch_num' => '10'],
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
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
    "X-RapidAPI-Key: $apiKey"
]);
$response = curl_exec($ch);
curl_close($ch);
$match_data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh');
?>

<div id="matches-table-only" style="font-family: 'Tajawal', sans-serif; direction: rtl; background: #050c14; padding: 15px; border-radius: 20px;">
    <style>
        .match-row { 
            background: rgba(255, 255, 255, 0.05); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 12px; padding: 12px; margin-bottom: 10px; 
            display: flex; align-items: center; justify-content: space-between;
            color: #fff;
        }
        .m-team { text-align: center; flex: 1; }
        .m-team img { width: 30px; height: 30px; object-fit: contain; }
        .m-team span { display: block; font-size: 10px; margin-top: 4px; font-weight: bold; }
        .m-center { flex: 1; text-align: center; }
        .m-score { font-size: 18px; font-weight: 900; color: #fff; }
        .m-badge { font-size: 8px; padding: 2px 8px; border-radius: 4px; background: #e11d48; color: #fff; display: inline-block; margin-top: 5px; }
        .m-league-title { font-size: 9px; color: #00ff87; display: block; margin-bottom: 4px; font-weight: 800; }
        .m-time { font-size: 11px; color: #f1c40f; font-weight: bold; }
    </style>

    <h4 style="color: #fff; margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 10px;">🏆 جدول مباريات اليوم</h4>

    <?php 
    if (!empty($match_data['response'])):
        foreach ($match_data['response'] as $m): 
            $lgID = $m['league']['id'];
            // التحقق: إذا كانت البطولة ضمن قائمتنا المفضلة، نقوم بعرضها
            if (isset($leagues_map[$lgID])): 
                $status = $m['fixture']['status']['short'];
                $homeScore = $m['goals']['home'] ?? 0;
                $awayScore = $m['goals']['away'] ?? 0;
    ?>
    <div class="match-row">
        <div class="m-team">
            <img src="<?= $m['teams']['home']['logo'] ?>" onerror="this.src='https://via.placeholder.com/30'">
            <span><?= translate_name($m['teams']['home']['name']) ?></span>
        </div>
        
        <div class="m-center">
            <span class="m-league-title"><?= $leagues_map[$lgID]['name'] ?></span>
            
            <?php if(in_array($status, ['1H','2H','HT','ET','P','LIVE'])): ?>
                <div class="m-score"><?= $homeScore ?> - <?= $awayScore ?></div>
                <div class="m-badge">مباشر الآن</div>
            <?php elseif($status == 'FT'): ?>
                <div class="m-score"><?= $homeScore ?> - <?= $awayScore ?></div>
                <div style="font-size: 9px; opacity: 0.6;">انتهت</div>
            <?php else: ?>
                <div class="m-time"><?= date('h:i A', strtotime($m['fixture']['date'])) ?></div>
                <div style="font-size: 8px; opacity: 0.5; margin-top:3px;">قناة <?= $leagues_map[$lgID]['channel'] ?></div>
            <?php endif; ?>
        </div>

        <div class="m-team">
            <img src="<?= $m['teams']['away']['logo'] ?>" onerror="this.src='https://via.placeholder.com/30'">
            <span><?= translate_name($m['teams']['away']['name']) ?></span>
        </div>
    </div>
    <?php 
            endif;
        endforeach; 
    else: 
    ?>
        <p style="text-align:center; color:#aaa; font-size:12px;">لا توجد مباريات جارية حالياً لهذه البطولات</p>
    <?php endif; ?>
</div>
