<?php
// --- إعدادات API المباريات ---
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

// قائمة الدوريات الموسعة (أوروبا، آسيا، أفريقيا)
$leagues_map = [
    'PL'   => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    'PD'   => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    'SA'   => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1', 'ch_num' => '10'],
    'BL1'  => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sport 5', 'ch_num' => '5'],
    'FL1'  => ['name' => 'الدوري الفرنسي', 'channel' => 'beIN Sport 4', 'ch_num' => '4'],
    'CL'   => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2', 'ch_num' => '2'],
    'EL'   => ['name' => 'الدوري الأوروبي', 'channel' => 'beIN Sport 6', 'ch_num' => '6'],
    'ACL'  => ['name' => 'دوري أبطال آسيا', 'channel' => 'beIN AFC', 'ch_num' => '7'], // مثال لقناة آسيا
    'WC'   => ['name' => 'كأس العالم', 'channel' => 'beIN MAX', 'ch_num' => '1'],
    'EC'   => ['name' => 'بطولة أوروبا', 'channel' => 'beIN MAX', 'ch_num' => '1'],
    'PPL'  => ['name' => 'الدوري البرتغالي', 'channel' => 'beIN Sport 8', 'ch_num' => '8'],
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
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch);
curl_close($ch);
$match_data = json_decode($response, true);
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>بوابة الرياضة - متجر الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        /* CSS يظل كما هو لسرعة الأداء */
        :root { --main: #e11d48; --bg-deep: #050c14; --glass: rgba(255, 255, 255, 0.05); --glass-border: rgba(255, 255, 255, 0.15); }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background-color: var(--bg-deep); padding-top: 175px; color: #e2e8f0; }
        .header-fixed-container { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(5, 12, 20, 0.9); backdrop-filter: blur(25px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; text-align: center; }
        .matches-section { padding: 10px 15px; }
        .match-scroll { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 10px; scrollbar-width: none; }
        .match-card { min-width: 270px; background: var(--glass); border-radius: 20px; padding: 0 15px 15px 15px; border: 1px solid var(--glass-border); position: relative; }
        .league-title-box { background: rgba(255, 255, 255, 0.03); border-bottom: 1px solid var(--glass-border); padding: 8px 15px; margin: 0 -15px 15px -15px; text-align: center; }
        .m-league { font-size: 10px; color: #00ff87; font-weight: 800; }
        .match-main { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .team { flex: 1; text-align: center; }
        .team img { width: 35px; height: 35px; object-fit: contain; }
        .team-name { font-size: 10px; font-weight: 700; margin-top: 5px; display: block; }
        .m-score-container { flex: 0.7; text-align: center; direction: ltr; }
        .m-score { font-size: 1.5em; font-weight: 900; color: #fff; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; padding: 15px; }
        .card { background: var(--glass); border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border); }
        video { width: 100%; aspect-ratio: 16/9; background: #000; }
        .play-btn-premium { width: 90%; margin: 15px auto; display: block; padding: 12px; border-radius: 50px; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid var(--glass-border); cursor: pointer; }
    </style>
</head>
<body>

<div class="header-fixed-container">
    <div style="font-size:11px; color:#fff; margin-bottom:10px;">متجر الخدمة الرقمية - بث مباشر بدون إعلانات</div>
    <div style="display:flex; justify-content:center; gap:10px;">
        <a href="https://wa.me/966505571164" style="color:#25d366"><i class="fab fa-whatsapp"></i></a>
        <a href="https://t.me/d_s_pro" style="color:#0088cc"><i class="fab fa-telegram"></i></a>
    </div>
</div>

<div class="matches-section">
    <div style="font-size:15px; font-weight:900; margin-bottom:12px;"><i class="fas fa-trophy" style="color:#f1c40f"></i> أهم مباريات اليوم</div>
    <div class="match-scroll">
        <?php 
        if (isset($match_data['matches'])):
            foreach ($match_data['matches'] as $m): 
                $code = $m['competition']['code'];
                // سنعرض فقط الدوريات المحددة في القائمة أعلاه
                if (isset($leagues_map[$code])): 
                    $is_live = ($m['status'] == 'IN_PLAY' || $m['status'] == 'PAUSED');
                    $is_fin = ($m['status'] == 'FINISHED');
                    $homeScore = $m['score']['fullTime']['home'] ?? 0;
                    $awayScore = $m['score']['fullTime']['away'] ?? 0;
        ?>
                <div class="match-card">
                    <div class="league-title-box"><div class="m-league"><?php echo $leagues_map[$code]['name']; ?></div></div>
                    <div class="match-main">
                        <div class="team">
                            <img src="<?php echo $m['homeTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/40'">
                            <span class="team-name"><?php echo translate_name($m['homeTeam']['name']); ?></span>
                        </div>
                        <div class="m-score-container">
                            <?php if ($is_live || $is_fin): ?>
                                <div class="m-score"><?php echo $homeScore . ' - ' . $awayScore; ?></div>
                                <?php if($is_live): ?><span style="color:#ff4d4d; font-size:9px; font-weight:bold;">LIVE</span><?php endif; ?>
                            <?php else: ?>
                                <div style="font-size:12px; color:#f1c40f;"><?php echo date('h:i A', strtotime($m['utcDate'])); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="team">
                            <img src="<?php echo $m['awayTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/40'">
                            <span class="team-name"><?php echo translate_name($m['awayTeam']['name']); ?></span>
                        </div>
                    </div>
                </div>
        <?php endif; endforeach; endif; ?>
    </div>
</div>

</body>
</html>
