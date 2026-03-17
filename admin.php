<?php
session_start();
$password = "admin123"; 
$manual_file = 'manual_channels.json';

// --- دالة الترجمة التلقائية ---
function translate_ar($text) {
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ar&dt=t&q=" . urlencode($text);
    $response = @file_get_contents($url);
    if($response) {
        $result = json_decode($response, true);
        return $result[0][0][0] ?? $text;
    }
    return $text;
}

// تسجيل الدخول
if (isset($_POST['login'])) {
    if ($_POST['pass'] == $password) { $_SESSION['admin'] = true; }
}

if (!isset($_SESSION['admin'])) {
    die('
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>دخول الإدارة</title>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
        <style>
            body { font-family: "Tajawal", sans-serif; background: #050c14; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            form { background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); text-align: center; width: 85%; max-width: 350px; backdrop-filter: blur(10px); }
            input { width: 100%; padding: 12px; margin: 15px 0; border-radius: 10px; border: none; background: #000; color: #fff; text-align: center; box-sizing: border-box; }
            button { width: 100%; padding: 12px; background: #e11d48; color: white; border: none; border-radius: 10px; font-weight: 900; cursor: pointer; }
        </style>
    </head>
    <body>
        <form method="POST">
            <h2 style="margin-top:0;">الخدمة الرقمية</h2>
            <p style="font-size:12px; opacity:0.6;">الرجاء إدخال كلمة المرور للمتابعة</p>
            <input type="password" name="pass" placeholder="••••••••" required> 
            <button name="login">دخول الإدارة</button>
        </form>
    </body>
    </html>');
}

// حفظ البيانات
$saved_data = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : [];

if (isset($_POST['update_ch'])) {
    $match_id = $_POST['match_id'];
    $ch_num = $_POST['ch_num'];
    if (!empty($ch_num)) {
        $saved_data[$match_id] = $ch_num;
    } else {
        unset($saved_data[$match_id]);
    }
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>window.location='admin.php';</script>";
}

// جلب مباريات اليوم من الـ API
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch);
curl_close($ch);
$match_data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>لوحة التحكم - القنوات الناقلة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255,255,255,0.05); }
        body { font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; margin: 0; padding: 15px; padding-top: 80px; }
        
        .header { position: fixed; top: 0; left: 0; right: 0; background: rgba(5,12,20,0.9); backdrop-filter: blur(15px); padding: 15px; z-index: 1000; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h2 { margin: 0; font-size: 16px; font-weight: 900; color: var(--main); }
        .back-btn { text-decoration: none; color: #fff; font-size: 12px; background: rgba(255,255,255,0.1); padding: 5px 12px; border-radius: 50px; }

        .container { max-width: 600px; margin: auto; }
        
        .match-card { 
            background: var(--glass); border: 1px solid rgba(255,255,255,0.1);
            padding: 15px; border-radius: 18px; margin-bottom: 15px;
            display: flex; flex-direction: column; gap: 12px;
        }
        
        .league-name { font-size: 10px; color: #00ff87; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 5px; margin-bottom: 5px; display: block; }
        
        .teams { font-size: 14px; font-weight: 700; line-height: 1.4; color: #e2e8f0; }
        
        .control-row { display: flex; gap: 10px; align-items: center; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 12px; }
        
        .ch-input { flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #333; background: #000; color: #00ff87; text-align: center; font-weight: bold; font-size: 16px; }
        
        .save-btn { background: #22c55e; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 900; cursor: pointer; transition: 0.3s; }
        .save-btn:active { transform: scale(0.95); }

        .active-badge { background: #7c3aed; color: #fff; padding: 2px 8px; border-radius: 5px; font-size: 10px; margin-right: 8px; }
    </style>
</head>
<body>

    <div class="header">
        <h2><i class="fas fa-tools"></i> لوحة التحكم</h2>
        <a href="index.php" class="back-btn">◀ رجوع للموقع</a>
    </div>

    <div class="container">
        <p style="text-align: center; font-size: 12px; opacity: 0.6; margin-bottom: 20px;">حدد القناة واضغط حفظ لتحديث الجدول فوراً</p>

        <?php 
        if (isset($match_data['matches']) && count($match_data['matches']) > 0):
            foreach ($match_data['matches'] as $m): 
                $match_id = $m['homeTeam']['name'] . ' vs ' . $m['awayTeam']['name'];
                $current_ch = $saved_data[$match_id] ?? '';
                
                // ترجمة الدوري والفرق
                $ar_league = translate_ar($m['competition']['name']);
                $ar_home = translate_ar($m['homeTeam']['name']);
                $ar_away = translate_ar($m['awayTeam']['name']);
        ?>
            <div class="match-card">
                <div>
                    <span class="league-name"><?php echo $ar_league; ?></span>
                    <div class="teams">
                        <?php if($current_ch) echo "<span class='active-badge'>قناة beIN $current_ch</span>"; ?>
                        <?php echo $ar_home; ?> vs <?php echo $ar_away; ?>
                    </div>
                </div>
                
                <form method="POST" class="control-row">
                    <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                    <span style="font-size: 11px; opacity: 0.7;">رقم القناة:</span>
                    <input type="number" name="ch_num" class="ch-input" placeholder="0" value="<?php echo $current_ch; ?>">
                    <button name="update_ch" class="save-btn"><i class="fas fa-save"></i> حفظ</button>
                </form>
            </div>
        <?php endforeach; else: ?>
            <div style="text-align:center; padding: 50px; opacity:0.5;">
                <i class="fas fa-clock fa-3x"></i>
                <p>لا توجد مباريات جارية حالياً لجلبها</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
