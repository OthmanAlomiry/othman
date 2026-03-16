<?php
// إعدادات الـ API الخاصة بك
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// مصفوفة معلومات الدوريات والقنوات
$leagues_map = [
    'PL'  => ['name' => 'الدوري الإنجليزي الممتاز', 'channel' => 'beIN Sports 1 HD'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sports 3 HD'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'AD Sports Premium'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sports 5 HD'],
];

// دالة بسيطة للترجمة (تحسين الأسماء الشائعة)
function translate_to_arabic($text) {
    $dictionary = [
        'Real Madrid CF' => 'ريال مدريد', 'FC Barcelona' => 'برشلونة',
        'Manchester City FC' => 'مانشستر سيتي', 'Liverpool FC' => 'ليفربول',
        'Arsenal FC' => 'أرسنال', 'Chelsea FC' => 'تشيلسي',
        'FC Bayern München' => 'بايرن ميونخ', 'Inter Milan' => 'إنتر ميلان',
        'Juventus FC' => 'يوفنتوس', 'AC Milan' => 'ميلان',
        'Manchester United FC' => 'مانشستر يونايتد', 'Atletico Madrid' => 'أتلتيكو مدريد'
    ];
    return $dictionary[$text] ?? $text; 
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh'); // توقيت مكة
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مباريات اليوم - الدوريات الكبرى</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #121212; color: #fff; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; }
        h1 { text-align: center; color: #00ff87; margin-bottom: 30px; }
        .match-card { background: #1e1e1e; border-radius: 12px; padding: 20px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #333; }
        .league-info { font-size: 0.8em; color: #00ff87; margin-bottom: 5px; font-weight: bold; }
        .teams { flex: 2; font-size: 1.2em; text-align: center; display: flex; align-items: center; justify-content: center; gap: 15px; }
        .vs { color: #888; font-size: 0.8em; font-weight: bold; }
        .details { flex: 1; text-align: left; border-right: 1px solid #333; padding-right: 20px; }
        .channel { color: #3498db; font-size: 0.9em; display: block; margin-top: 5px; }
        .time { color: #f1c40f; font-size: 1.1em; font-weight: bold; }
        .no-matches { text-align: center; background: #1e1e1e; padding: 40px; border-radius: 12px; }
    </style>
</head>
<body>

<div class="container">
    <h1>⚽ جدول مباريات اليوم</h1>

    <?php 
    $match_count = 0;
    if (isset($data['matches'])):
        foreach ($data['matches'] as $match): 
            $league_code = $match['competition']['code'];
            if (isset($leagues_map[$league_code])): 
                $match_count++;
    ?>
            <div class="match-card">
                <div class="info-group" style="flex: 1;">
                    <div class="league-info"><?php echo $leagues_map[$league_code]['name']; ?></div>
                    <div class="time"><?php echo date('h:i A', strtotime($match['utcDate'])); ?></div>
                </div>

                <div class="teams">
                    <span><?php echo translate_to_arabic($match['homeTeam']['name']); ?></span>
                    <span class="vs">ضد</span>
                    <span><?php echo translate_to_arabic($match['awayTeam']['name']); ?></span>
                </div>

                <div class="details">
                    <span class="channel">📺 <?php echo $leagues_map[$league_code]['channel']; ?></span>
                </div>
            </div>
    <?php 
            endif;
        endforeach; 
    endif;

    if ($match_count == 0): ?>
        <div class="no-matches">
            <h3>لا توجد مباريات في الدوريات الكبرى اليوم</h3>
            <p>يمكنك التحقق من البطولات القادمة غداً.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
