<?php
session_start();
$password = "admin123"; 
$manual_file = 'manual_channels.json';

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
            form { background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); text-align: center; width: 85%; max-width: 350px; }
            input { width: 100%; padding: 12px; margin: 15px 0; border-radius: 10px; border: none; background: #000; color: #fff; text-align: center; box-sizing: border-box; }
            button { width: 100%; padding: 12px; background: #e11d48; color: white; border: none; border-radius: 10px; font-weight: 900; cursor: pointer; }
        </style>
    </head>
    <body>
        <form method="POST"><h2 style="margin-top:0;">الخدمة الرقمية</h2><input type="password" name="pass" placeholder="كلمة المرور" required><button name="login">دخول الإدارة</button></form>
    </body>
    </html>');
}

$saved_data = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : ['custom_matches' => []];

// إضافة مباراة يدوياً مع دعم الشعارات
if (isset($_POST['add_custom'])) {
    $new_match = [
        'id' => uniqid(),
        'league' => $_POST['m_league'],
        'home' => $_POST['m_home'],
        'home_logo' => $_POST['m_home_logo'], // رابط شعار الفريق الأول
        'away' => $_POST['m_away'],
        'away_logo' => $_POST['m_away_logo'], // رابط شعار الفريق الثاني
        'time' => $_POST['m_time'],
        'ch' => $_POST['m_ch']
    ];
    $saved_data['custom_matches'][] = $new_match;
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>window.location='admin.php';</script>";
}

// حذف مباراة يدوية
if (isset($_GET['del_match'])) {
    $match_id = $_GET['del_match'];
    foreach($saved_data['custom_matches'] as $k => $v) {
        if($v['id'] == $match_id) unset($saved_data['custom_matches'][$k]);
    }
    $saved_data['custom_matches'] = array_values($saved_data['custom_matches'] ?? []);
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>window.location='admin.php';</script>";
}

// حفظ تعديلات الـ API
if (isset($_POST['save_all'])) {
    if (isset($_POST['channels']) && is_array($_POST['channels'])) {
        foreach ($_POST['channels'] as $id => $val) { $saved_data[$id] = $val; }
    }
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>alert('تم الحفظ'); window.location='admin.php';</script>";
}

// جلب بيانات الـ API
$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: ' . $apiKey]);
$response = curl_exec($ch); curl_close($ch);
$match_data = json_decode($response, true);

$available_channels = [
    ""   => "تلقائي (حسب الدوري)",
    "postponed" => "⚠️ مباراة مؤجلة",
    "1"  => "beIN Sport 1", "2"  => "beIN Sport 2", "3"  => "beIN Sport 3",
    "4"  => "beIN Sport 4", "5"  => "beIN Sport 5", "6"  => "beIN Sport 6",
    "7"  => "beIN Sport 7", "8"  => "beIN Sport 8", "9"  => "beIN Sport 9",
    "10" => "STARZPLAY 1", "11" => "STARZPLAY 2",
    "12" => "MBC Action", "13" => "شاهد الرياضية 1", "14" => "شاهد الرياضية 2"
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); }
        body { font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 15px; padding-top: 80px; padding-bottom: 100px; }
        .header { position: fixed; top: 0; left: 0; right: 0; background: rgba(5,12,20,0.9); backdrop-filter: blur(15px); padding: 15px; z-index: 1000; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h2 { margin: 0; font-size: 16px; color: var(--main); }
        .container { max-width: 600px; margin: auto; }
        .section-title { font-size: 16px; font-weight: 900; color: #f1c40f; margin: 25px 0 10px 0; border-right: 4px solid var(--main); padding-right: 10px; }
        .add-card { background: rgba(34, 197, 94, 0.1); border: 1px dashed #22c55e; padding: 15px; border-radius: 18px; margin-bottom: 20px; }
        .add-card input, .add-card select { width: 100%; padding: 10px; margin: 5px 0; border-radius: 8px; border: 1px solid #333; background: #000; color: #fff; box-sizing: border-box; }
        .add-btn { width: 100%; padding: 12px; background: #22c55e; border: none; border-radius: 8px; color: white; font-weight: 900; cursor: pointer; }
        .match-card { background: var(--glass); border: 1px solid rgba(255,255,255,0.1); padding: 15px; border-radius: 18px; margin-bottom: 15px; position: relative; }
        .league-name { font-size: 10px; color: #00ff87; font-weight: 800; display: block; margin-bottom: 5px; }
        .teams { font-size: 14px; font-weight: 700; margin-bottom: 10px; }
        .ch-select { width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #444; background: #000; color: #00ff87; font-weight: bold; }
        .del-match { position: absolute; top: 10px; left: 10px; color: #e11d48; text-decoration: none; font-size: 11px; background: rgba(225,29,72,0.1); padding: 5px; border-radius: 5px; }
        .floating-save { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; background: #e11d48; color: white; padding: 15px; border-radius: 50px; border: none; font-weight: 900; font-size: 16px; cursor: pointer; box-shadow: 0 10px 30px rgba(225, 29, 72, 0.4); z-index: 1001; }
    </style>
</head>
<body>

    <div class="header">
        <h2>لوحة التحكم</h2>
        <a href="live.php" style="color:#fff; text-decoration:none; font-size:12px;">◀ الموقع</a>
    </div>

    <div class="container">
        
        <div class="section-title">إضافة مباراة يدوياً (بالشعارات)</div>
        <form method="POST" class="add-card">
            <input type="text" name="m_league" placeholder="اسم البطولة (مثل: أبطال أفريقيا)" required>
            <div style="display:flex; gap:5px;">
                <input type="text" name="m_home" placeholder="الفريق 1" required>
                <input type="text" name="m_home_logo" placeholder="رابط الشعار">
            </div>
            <div style="display:flex; gap:5px;">
                <input type="text" name="m_away" placeholder="الفريق 2" required>
                <input type="text" name="m_away_logo" placeholder="رابط الشعار">
            </div>
            <input type="text" name="m_time" placeholder="الوقت (مثل: 10:00 PM)" required>
            <select name="m_ch">
                <?php foreach($available_channels as $v => $l): if($v=="")continue; ?>
                    <option value="<?php echo $v; ?>"><?php echo $l; ?></option>
                <?php endforeach; ?>
            </select>
            <button name="add_custom" class="add-btn">إضافة للموقع</button>
        </form>

        <form method="POST">
            <?php if(!empty($saved_data['custom_matches'])): ?>
            <div class="section-title">المباريات اليدوية الحالية</div>
            <?php foreach($saved_data['custom_matches'] as $cm): ?>
                <div class="match-card">
                    <a href="?del_match=<?php echo $cm['id']; ?>" class="del-match"><i class="fas fa-trash"></i> حذف</a>
                    <span class="league-name"><?php echo $cm['league']; ?></span>
                    <div class="teams"><?php echo $cm['home']; ?> vs <?php echo $cm['away']; ?></div>
                </div>
            <?php endforeach; endif; ?>

            <div class="section-title">مباريات API التلقائية</div>
            <?php 
            if (isset($match_data['matches']) && is_array($match_data['matches'])):
                foreach ($match_data['matches'] as $m): 
                    $match_id = $m['homeTeam']['name'] . ' vs ' . $m['awayTeam']['name'];
                    $current_val = $saved_data[$match_id] ?? '';
            ?>
                <div class="match-card">
                    <span class="league-name"><?php echo $m['competition']['name']; ?></span>
                    <div class="teams"><?php echo $m['homeTeam']['name']; ?> vs <?php echo $m['awayTeam']['name']; ?></div>
                    <select name="channels[<?php echo $match_id; ?>]" class="ch-select">
                        <?php foreach ($available_channels as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo ($current_val == $val) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endforeach; endif; ?>
            
            <button type="submit" name="save_all" class="floating-save">حفظ التغييرات</button>
        </form>
    </div>
</body>
</html>
