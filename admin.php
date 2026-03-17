<?php
session_start();
$password = "admin123"; // كلمة المرور الخاصة بك
$manual_file = 'manual_channels.json';

// تسجيل الدخول
if (isset($_POST['login'])) {
    if ($_POST['pass'] == $password) { $_SESSION['admin'] = true; }
}

if (!isset($_SESSION['admin'])) {
    die('<dir dir="rtl" style="text-align:center; padding-top:100px; font-family:Tahoma; background:#050c14; color:white; height:100vh; margin:0;">
        <form method="POST" style="background:rgba(255,255,255,0.05); padding:30px; display:inline-block; border-radius:15px; border:1px solid rgba(255,255,255,0.1);">
            <h2 style="margin-top:0;">لوحة تحكم الخدمة الرقمية</h2>
            كلمة المرور: <input type="password" name="pass" style="padding:8px; border-radius:5px; border:none;"> 
            <button name="login" style="padding:8px 20px; background:#e11d48; color:white; border:none; border-radius:5px; cursor:pointer;">دخول</button>
        </form>
    </dir>');
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
    echo "<script>alert('تم تحديث القناة بنجاح'); window.location='admin.php';</script>";
}

// جلب مباريات اليوم من الـ API للعرض في لوحة التحكم
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
    <title>إدارة القنوات الناقلة</title>
    <style>
        body { font-family: 'Tahoma', sans-serif; background: #050c14; color: white; padding: 20px; }
        .match-row { 
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            padding: 15px; border-radius: 12px; margin-bottom: 10px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .team-info { font-weight: bold; font-size: 14px; flex: 1; }
        .ch-input { width: 60px; padding: 8px; border-radius: 5px; border: 1px solid #444; background: #000; color: #00ff87; text-align: center; font-weight: bold; }
        .save-btn { background: #22c55e; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-right: 10px; }
        .badge { background: #e11d48; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-left: 10px; }
    </style>
</head>
<body>
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>تحديد القنوات لمباريات اليوم</h2>
        <a href="live.php" style="color:#aaa; text-decoration:none;">◀ العودة للموقع</a>
    </div>
    <hr style="opacity:0.1;">

    <?php 
    if (isset($match_data['matches'])):
        foreach ($match_data['matches'] as $m): 
            $match_id = $m['homeTeam']['name'] . ' vs ' . $m['awayTeam']['name']; // معرف فريد للمباراة
            $current_ch = $saved_data[$match_id] ?? '';
    ?>
        <div class="match-row">
            <div class="team-info">
                <span style="color:#00ff87;"><?php echo $m['competition']['name']; ?></span> <br>
                <?php echo $m['homeTeam']['name']; ?> vs <?php echo $m['awayTeam']['name']; ?>
                <?php if($current_ch) echo "<span class='badge'>قناة $current_ch</span>"; ?>
            </div>
            
            <form method="POST" style="display:flex; align-items:center;">
                <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                <input type="number" name="ch_num" class="ch-input" placeholder="0" value="<?php echo $current_ch; ?>">
                <button name="update_ch" class="save-btn">حفظ</button>
            </form>
        </div>
    <?php endforeach; else: echo "لا توجد مباريات حالياً لجلبها."; endif; ?>

</body>
</html>
