<?php
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';

$leagues_map = [
    'PL'  => ['name' => 'الدوري الإنجليزي', 'channel' => 'beIN Sport 1', 'ch_num' => '1'],
    'PD'  => ['name' => 'الدوري الإسباني', 'channel' => 'beIN Sport 3', 'ch_num' => '3'],
    'SA'  => ['name' => 'الدوري الإيطالي', 'channel' => 'STARZPLAY 1', 'ch_num' => '10'],
    'BL1' => ['name' => 'الدوري الألماني', 'channel' => 'beIN Sport 5', 'ch_num' => '5'],
    'CL'  => ['name' => 'دوري أبطال أوروبا', 'channel' => 'beIN Sport 2', 'ch_num' => '2'],
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

if (isset($match_data['matches'])):
    foreach ($match_data['matches'] as $m): 
        $code = $m['competition']['code'];
        if (isset($leagues_map[$code])): 
            $is_live = ($m['status'] == 'IN_PLAY' || $m['status'] == 'PAUSED');
            $is_fin = ($m['status'] == 'FINISHED');
?>
        <div class="match-card">
            <div class="league-title-box">
                <div class="m-league"><?php echo $leagues_map[$code]['name']; ?></div>
            </div>
            <div class="match-main">
                <div class="team">
                    <img src="<?php echo $m['homeTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/40'">
                    <span class="team-name"><?php echo translate_name($m['homeTeam']['name']); ?></span>
                </div>
                <div class="m-status" style="flex:0.6; text-align:center;">
                    <?php if ($is_live || $is_fin): ?>
                        <div class="m-score"><?php echo $m['score']['fullTime']['home'].'-'.$m['score']['fullTime']['away']; ?></div>
                        <?php if($is_live): ?><span style="color:#ff4d4d; font-size:8px; font-weight:900;">LIVE</span><?php endif; ?>
                    <?php else: ?>
                        <div style="font-size:11px; font-weight:bold; color:#f1c40f;"><?php echo date('h:i A', strtotime($m['utcDate'])); ?></div>
                    <?php endif; ?>
                </div>
                <div class="team">
                    <img src="<?php echo $m['awayTeam']['crest']; ?>" onerror="this.src='https://via.placeholder.com/40'">
                    <span class="team-name"><?php echo translate_name($m['awayTeam']['name']); ?></span>
                </div>
            </div>
            <div class="m-footer">
                <span>📺 <?php echo $leagues_map[$code]['channel']; ?></span>
                <span style="color:#00ff87; font-weight:900; cursor:pointer;" onclick="goToChannel('<?php echo $leagues_map[$code]['ch_num']; ?>')">شاهد الآن ▶</span>
            </div>
        </div>
<?php endif; endforeach; endif; ?>
