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

$saved_data = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : [];

// الحفظ الجماعي
if (isset($_POST['save_all'])) {
    $new_data = [];
    if (isset($_POST['channels']) && is_array($_POST['channels'])) {
        foreach ($_POST['channels'] as $match_id => $ch_num) {
            if (!empty($ch_num)) {
                $new_data[$match_id] = $ch_num;
            }
        }
    }
    file_put_contents($manual_file, json_encode($new_data));
    echo "<script>alert('تم حفظ القنوات بنجاح'); window.location='admin.php';</script>";
}

// جلب مباريات اليوم
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
        body { font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; margin: 0; padding: 15px; padding-top: 80px; padding-bottom: 100px; }
        .header { position: fixed; top: 0; left: 0; right: 0; background: rgba(5,12,20,0.9); backdrop-filter: blur(15px); padding: 15px; z-index: 1000; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h2 { margin: 0; font-size: 16px; font-weight: 900; color: var(--main); }
        .back-btn { text-decoration: none; color: #fff; font-size: 11px; background: rgba(255,255,255,0.1); padding: 6px 15px; border-radius: 50px; }
        .container { max-width: 600px; margin: auto; }
        .match-card { background: var(--glass); border: 1px solid rgba(255,255,255,0.1); padding: 15px; border-radius: 18px; margin-bottom: 15px; }
        .league-name { font-size: 10px; color: #00ff87; font-weight: 800; margin-bottom: 8px; display: block; }
        .teams { font-size: 14px; font-weight: 700; margin-bottom: 12px; color: #e2e8f0; }
        .control-row { display: flex; gap: 10px; align-items: center; background: rgba(0,0,0,0.3); padding: 8px 12px; border-radius: 12px; }
        .ch-input { flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #333; background: #000; color: #00ff87; text-align: center; font-weight: bold; font-size: 18px; outline: none; }
        .floating-save { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; background: #22c55e; color: white; padding: 15px; border-radius: 50px; border: none; font-weight: 900; font-size: 16px; cursor: pointer; box-shadow: 0 10px 30px rgba(34, 197, 94, 0.4); z-index: 1001; }
        .active-badge { background: #7c3aed; color: #fff; padding: 2px 8px; border-radius: 5px; font-size: 10px; margin-right: 8px; vertical-align: middle; }
    </style>
</head>
<body>

    <div class="header">
        <h2><i class="fas fa-edit"></i> تحديث القنوات</h2>
        <a href="live.php" class="back-btn">◀ رجوع للموقع</a>
    </div>

    <div class="container">
        <form method="POST">
            <?php 
            // إصلاح الخطأ: التحقق من وجود بيانات قبل العد
            if (isset($match_data['matches']) && is_array($match_data['matches']) && count($match_data['matches']) > 0):
                foreach ($match_data['matches'] as $m): 
                    $match_id = $m['homeTeam']['name'] . ' vs ' . $m['awayTeam']['name'];
                    $current_ch = $saved_data[$match_id] ?? '';
                    $ar_league = translate_ar($m['competition']['name']);
                    $ar_home = translate_ar($m['homeTeam']['name']);
                    $ar_away = translate_ar($m['awayTeam']['name']);
            ?>
                <div class="match-card">
                    <span class="league-name"><?php echo $ar_league; ?></span>
                    <div class="teams">
                        <?php echo $ar_home; ?> vs <?php echo $ar_away; ?>
                        <?php if($current_ch) echo "<span class='active-badge'>beIN $current_ch</span>"; ?>
                    </div>
                    <div class="control-row">
                        <span style="font-size: 11px; opacity: 0.7;">رقم القناة:</span>
                        <input type="number" name="channels[<?php echo $match_id; ?>]" class="ch-input" placeholder="0" value="<?php echo $current_ch; ?>">
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" name="save_all" class="floating-save">حفظ جميع التعديلات</button>
            <?php else: ?>
                <div style="text-align:center; padding: 50px; opacity:0.5;">
                    <i class="fas fa-clock fa-3x"></i>
                    <p>لا توجد مباريات جارية حالياً</p>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
