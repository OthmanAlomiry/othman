<?php
// مفتاح الـ API الخاص بك
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// مصفوفة معلومات الدوريات والقنوات
$leagues_map = [
    'PL'  => ['name' => 'الدوري الإنجليزي الممتاز', 'channel' => 'beIN Sports 1 HD'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sports 3 HD'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'AD Sports Premium'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sports 5 HD'],
];

// دالة للترجمة الفورية عبر جوجل
function translate_to_arabic($text) {
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = file_get_contents($url);
    $result = json_decode($response, true);
    return $result[0][0][0] ?? $text;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مباريات اليوم بالعربي</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0f0f0f; color: #fff; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .match-card { 
            background: linear-gradient(145deg, #1a1a1a, #222); 
            border-radius: 15px; padding: 20px; margin-bottom: 15px; 
            display: flex; align-items: center; border: 1px solid #333;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .league-title { color: #00ff87; font-size: 0.85em; margin-bottom: 8px; font-weight: 800; }
        .team-section { flex: 3; display: flex; justify-content: space-around; align-items: center; text-align: center; }
        .team-name { width: 40%; font-size: 1.1em; font-weight: 600; }
        .vs { background: #333; padding: 5px 10px; border-radius: 5px; font-size: 0.7em; color: #aaa; }
        .meta-section { flex: 1; text-align: left; border-right: 2px solid #00ff87; padding-right: 15px; margin-right: 10px; }
        .time { font-size: 1.2em; color: #f1c40f; display: block; }
        .channel { font-size: 0.8em; color: #3498db; margin-top: 5px; display: block; }
        h1 { text-align: center; font-size: 2em; color: #00ff87; text-shadow: 0 2px 10px rgba(0,255,135,0.3); }
    </style>
</head>
<body>

<div class="container">
    <h1>⚽ مباريات اليوم</h1>

    <?php 
    $match_count = 0;
    if (isset($data['matches'])):
        foreach ($data['matches'] as $match): 
            $league_code = $match['competition']['code'];
            if (isset($leagues_map[$league_code])): 
                $match_count++;
                
                // ترجمة أسماء الفرق فوراً
                $homeTeam = translate_to_arabic($match['homeTeam']['name']);
                $awayTeam = translate_to_arabic($match['awayTeam']['name']);
    ?>
            <div class="match-card">
                <div class="meta-section">
                    <span class="time"><?php echo date('h:i A', strtotime($match['utcDate'])); ?></span>
                    <span class="channel">📺 <?php echo $leagues_map[$league_code]['channel']; ?></span>
                </div>

                <div class="team-section">
                    <div class="league-title" style="position: absolute; margin-top: -65px;">
                        <?php echo $leagues_map[$league_code]['name']; ?>
                    </div>
                    <div class="team-name"><?php echo $homeTeam; ?></div>
                    <div class="vs">ضد</div>
                    <div class="team-name"><?php echo $awayTeam; ?></div>
                </div>
            </div>
    <?php 
            endif;
        endforeach; 
    endif;

    if ($match_count == 0) echo "<div style='text-align:center; padding:50px;'>لا توجد مباريات جارية حالياً لهذه الدوريات.</div>";
    ?>
</div>

</body>
</html>
