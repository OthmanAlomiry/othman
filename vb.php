<?php
// بيانات الـ API الخاصة بك من الصورة
$apiKey = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9";
$date = date('Y-m-d');

// قائمة الـ IDs الخاصة بالبطولات التي طلبتها (بناءً على API-Football)
$targetLeagues = [
    39  => "الدوري الإنجليزي",
    140 => "الدوري الإسباني",
    61  => "الدوري الفرنسي",
    135 => "الدوري الإيطالي",
    78  => "الدوري الألماني",
    307 => "الدوري السعودي",
    2   => "دوري أبطال أوروبا",
    3   => "الدوري الأوروبي",
    17  => "دوري أبطال آسيا للنخبة",
    19  => "دوري أبطال آسيا 2"
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=$date",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
        "X-RapidAPI-Key: $apiKey"
    ],
]);

$response = curl_exec($curl);
$result = json_decode($response, true);
curl_close($curl);

$allFixtures = $result['response'] ?? [];

// تصفية المباريات لتشمل فقط الدوريات المطلوبة
$filteredMatches = array_filter($allFixtures, function($item) use ($targetLeagues) {
    return array_key_exists($item['league']['id'], $targetLeagues);
});
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم المختارة</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; margin: 0; padding: 15px; }
        .container { max-width: 600px; margin: auto; }
        .league-group { margin-bottom: 25px; }
        .league-header { background: #1a237e; color: white; padding: 10px 15px; border-radius: 10px 10px 0 0; font-size: 14px; font-weight: bold; display: flex; align-items: center; }
        .league-header img { width: 25px; margin-left: 10px; }
        .match-card { background: white; padding: 15px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; }
        .match-card:last-child { border-radius: 0 0 10px 10px; border-bottom: none; }
        .team { flex: 1; text-align: center; font-size: 13px; font-weight: 600; }
        .team img { width: 30px; height: 30px; display: block; margin: 0 auto 5px; }
        .score-box { flex: 0.6; text-align: center; }
        .time { font-size: 16px; color: #d32f2f; font-weight: bold; }
        .score { font-size: 20px; font-weight: 900; color: #333; }
        .status { font-size: 10px; color: #2ecc71; display: block; }
        .no-matches { text-align: center; background: white; padding: 30px; border-radius: 15px; color: #888; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align:center; color:#1a237e;">⚽ مباريات اليوم</h2>
    
    <?php if (!empty($filteredMatches)): 
        // تجميع المباريات حسب الدوري
        $grouped = [];
        foreach ($filteredMatches as $m) {
            $grouped[$m['league']['name']][] = $m;
        }
        
        foreach ($grouped as $leagueName => $matches): ?>
            <div class="league-group">
                <div class="league-header">
                    <img src="<?php echo $matches[0]['league']['logo']; ?>" alt="">
                    <?php echo $leagueName; ?>
                </div>
                <?php foreach ($matches as $match): ?>
                    <div class="match-card">
                        <div class="team">
                            <img src="<?php echo $match['teams']['home']['logo']; ?>">
                            <?php echo $match['teams']['home']['name']; ?>
                        </div>
                        
                        <div class="score-box">
                            <?php if($match['fixture']['status']['short'] == 'NS'): ?>
                                <span class="time"><?php echo date('H:i', strtotime($match['fixture']['date'])); ?></span>
                            <?php else: ?>
                                <div class="score"><?php echo $match['goals']['home']; ?> - <?php echo $match['goals']['away']; ?></div>
                                <span class="status"><?php echo $match['fixture']['status']['elapsed']; ?>'</span>
                            <?php endif; ?>
                        </div>

                        <div class="team">
                            <img src="<?php echo $match['teams']['away']['logo']; ?>">
                            <?php echo $match['teams']['away']['name']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-matches">
            <h3>لا توجد مباريات اليوم في الدوريات المختارة</h3>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
