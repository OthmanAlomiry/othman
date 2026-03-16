<?php
// مفتاح الـ API الخاص بك
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// مصفوفة الدوريات والقنوات
$leagues_map = [
    'PL'  => ['name' => 'الدوري الإنجليزي الممتاز', 'channel' => 'beIN Sports 1 HD'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sports 3 HD'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'AD Sports Premium'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sports 5 HD'],
];

// دالة الترجمة التلقائية
function translate_to_arabic($text) {
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
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh');
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم مع الشعارات</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0b0e11; color: #fff; padding: 10px; }
        .container { max-width: 700px; margin: auto; }
        h1 { text-align: center; color: #00ff87; font-size: 1.8em; margin-bottom: 25px; }
        .match-card { 
            background: #1c2127; border-radius: 12px; padding: 15px; margin-bottom: 12px; 
            border: 1px solid #2d3436; position: relative;
        }
        .league-header { font-size: 0.75em; color: #00ff87; text-align: center; margin-bottom: 10px; opacity: 0.8; }
        .main-row { display: flex; align-items: center; justify-content: space-between; }
        .team { flex: 1; text-align: center; }
        .team img { width: 45px; height: 45px; object-fit: contain; margin-bottom: 8px; }
        .team-name { font-size: 0.95em; display: block; font-weight: 600; }
        .vs-box { flex: 0.5; text-align: center; }
        .time { font-size: 1.1em; color: #f1c40f; font-weight: bold; display: block; }
        .vs-text { font-size: 0.7em; color: #636e72; text-transform: uppercase; }
        .footer-info { border-top: 1px solid #2d3436; margin-top: 10px; padding-top: 8px; display: flex; justify-content: space-between; font-size: 0.8em; color: #3498db; }
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
                
                $homeTeamName = translate_to_arabic($match['homeTeam']['name']);
                $awayTeamName = translate_to_arabic($match['awayTeam']['name']);
                $homeLogo = $match['homeTeam']['crest']; // رابط الشعار من الـ API
                $awayLogo = $match['awayTeam']['crest']; // رابط الشعار من الـ API
    ?>
            <div class="match-card">
                <div class="league-header"><?php echo $leagues_map[$league_code]['name']; ?></div>
                
                <div class="main-row">
                    <div class="team">
                        <img src="<?php echo $homeLogo; ?>" alt="logo">
                        <span class="team-name"><?php echo $homeTeamName; ?></span>
                    </div>

                    <div class="vs-box">
                        <span class="time"><?php echo date('h:i A', strtotime($match['utcDate'])); ?></span>
                        <span class="vs-text">ضد</span>
                    </div>

                    <div class="team">
                        <img src="<?php echo $awayLogo; ?>" alt="logo">
                        <span class="team-name"><?php echo $awayTeamName; ?></span>
                    </div>
                </div>

                <div class="footer-info">
                    <span>📺 <?php echo $leagues_map[$league_code]['channel']; ?></span>
                    <span style="color: #636e72;">دوري محلي</span>
                </div>
            </div>
    <?php 
            endif;
        endforeach; 
    endif;

    if ($match_count == 0): ?>
        <div style="text-align:center; margin-top: 50px; color: #636e72;">لا توجد مباريات كبرى اليوم.</div>
    <?php endif; ?>
</div>

</body>
</html>
