<?php

$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// مصفوفة لترجمة الدوريات والقنوات المتوقعة
$leagues_info = [
    'PL'  => ['name' => 'الدوري الإنجليزي الممتاز', 'channel' => 'beIN Sports 1/2'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sports 3'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'Starzplay / Abu Dhabi Sports'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sports 5'],
];

// مصفوفة بسيطة لترجمة بعض الأندية الشهيرة (يمكنك توسيعها)
$teams_ar = [
    'Real Madrid CF' => 'ريال مدريد',
    'FC Barcelona' => 'برشلونة',
    'Manchester City FC' => 'مانشستر سيتي',
    'Liverpool FC' => 'ليفربول',
    'Arsenal FC' => 'أرسنال',
    'FC Bayern München' => 'بايرن ميونخ',
    'AC Milan' => 'ميلان',
    'Juventus FC' => 'يوفنتوس',
    'Inter Milan' => 'إنتر ميلان'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh'); // توقيت مكة المكرمة

function translateTeam($name, $mapping) {
    return $mapping[$name] ?? $name; // إذا لم يوجد اسم عربي، يظهر الإنجليزي
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مباريات الدوريات الكبرى</title>
    <style>
        body { font-family: 'Cairo', sans-serif; background: #121212; color: #eee; padding: 20px; }
        .match-box { background: #1e1e1e; border-right: 4px solid #e74c3c; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; align-items: center; }
        .league-tag { background: #e74c3c; padding: 2px 8px; border-radius: 3px; font-size: 12px; margin-bottom: 5px; display: inline-block; }
        .info { flex: 2; }
        .channel-info { flex: 1; text-align: left; color: #3498db; font-size: 0.9em; }
        .time { color: #f1c40f; margin-left: 15px; font-weight: bold; }
    </style>
</head>
<body>

    <h2 style="text-align: center;">📅 مباريات اليوم في الدوريات الكبرى</h2>

    <?php 
    $found = false;
    if (isset($data['matches'])):
        foreach ($data['matches'] as $match): 
            $code = $match['competition']['code'];
            // التأكد أن المباراة تتبع أحد الدوريات المطلوبة فقط
            if (array_key_exists($code, $leagues_info)): 
                $found = true;
    ?>
            <div class="match-box">
                <div class="info">
                    <span class="league-tag"><?php echo $leagues_info[$code]['name']; ?></span>
                    <div style="font-size: 1.2em;">
                        <?php echo translateTeam($match['homeTeam']['name'], $teams_ar); ?> 
                        <span style="color: #555;">×</span> 
                        <?php echo translateTeam($match['awayTeam']['name'], $teams_ar); ?>
                    </div>
                </div>
                <div class="channel-info">
                    📺 <?php echo $leagues_info[$code]['channel']; ?>
                </div>
                <div class="time">
                    <?php echo date('h:i A', strtotime($match['utcDate'])); ?>
                </div>
            </div>
    <?php 
            endif;
        endforeach; 
    endif;

    if (!$found) echo "<p style='text-align:center;'>لا توجد مباريات لهذه الدوريات اليوم.</p>";
    ?>

</body>
</html>
