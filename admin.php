<?php
session_start();
$password = "admin123"; 
$manual_file = 'manual_channels.json';

if (isset($_POST['login'])) {
    if ($_POST['pass'] == $password) { $_SESSION['admin'] = true; }
}

if (!isset($_SESSION['admin'])) {
    die('<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><title>دخول</title><style>body { font-family: Tahoma; background: #050c14; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; } form { background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); text-align: center; } input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 8px; border: none; background: #000; color: #fff; } button { width: 100%; padding: 10px; background: #e11d48; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }</style></head><body><form method="POST"><h2>الإدارة</h2><input type="password" name="pass" placeholder="كلمة المرور" required><button name="login">دخول</button></form></body></html>');
}

$saved_data = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : ['custom_matches' => []];

function sendOneSignalNotification($title, $body) {
    $content = array("en" => $body);
    $headings = array("en" => $title);
    $fields = array(
        'app_id' => "6e41fb93-1b65-4596-86f4-ad8589b38ad7",
        'included_segments' => array('All'),
        'contents' => $content,
        'headings' => $headings
    );
    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic os_v2_app_nza7xey3mvcznbxuvwcytm4k25selce42yienpf343e44llw337xhzaez32pw6qploucliayfuzr22aqfylhujxmh4ryv2w72obz4ti'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_exec($ch);
    curl_close($ch);
}

if (isset($_POST['send_push'])) {
    sendOneSignalNotification($_POST['push_title'], $_POST['push_body']);
    echo "<script>alert('تم إرسال الإشعار');</script>";
}

if (isset($_POST['add_custom'])) {
    $new_match = ['id'=>uniqid(), 'league'=>$_POST['m_league'], 'home'=>$_POST['m_home'], 'home_logo'=>$_POST['m_home_logo'], 'away'=>$_POST['m_away'], 'away_logo'=>$_POST['m_away_logo'], 'time'=>$_POST['m_time'], 'ch'=>$_POST['m_ch']];
    $saved_data['custom_matches'][] = $new_match;
    file_put_contents($manual_file, json_encode($saved_data));
}

if (isset($_POST['save_all'])) {
    if (isset($_POST['channels'])) { foreach($_POST['channels'] as $id => $v) $saved_data[$id] = $v; }
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>alert('تم الحفظ');</script>";
}

if (isset($_GET['del_match'])) {
    foreach($saved_data['custom_matches'] as $k => $v) { if($v['id'] == $_GET['del_match']) unset($saved_data['custom_matches'][$k]); }
    $saved_data['custom_matches'] = array_values($saved_data['custom_matches']);
    file_put_contents($manual_file, json_encode($saved_data));
    header("Location: admin.php"); exit;
}

$apiKey = '273aaeb61360452588653ffea820cc19';
$url = 'https://api.football-data.org/v4/matches';
$ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Auth-Token: '.$apiKey]);
$match_data = json_decode(curl_exec($ch), true); curl_close($ch);

$available_channels = [""=>"تلقائي", "postponed"=>"⚠️ مؤجلة", "1"=>"beIN 1", "2"=>"beIN 2", "3"=>"beIN 3", "4"=>"beIN 4", "5"=>"beIN 5", "6"=>"beIN 6", "7"=>"beIN 7", "8"=>"beIN 8", "9"=>"beIN 9", "10"=>"STARZ 1", "11"=>"STARZ 2", "12"=>"MBC", "13"=>"شاهد 1", "14"=>"شاهد 2"];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Tahoma; background: #050c14; color: white; padding: 20px; padding-bottom: 100px; }
        .card { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 15px; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.1); }
        input, select, textarea { width: 100%; padding: 10px; margin: 5px 0; border-radius: 8px; border: 1px solid #333; background: #000; color: #fff; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 8px; color: white; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .save-btn { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; background: #e11d48; box-shadow: 0 5px 15px rgba(225,29,72,0.4); }
    </style>
</head>
<body>
    <h2 style="color:#e11d48">لوحة التحكم</h2>
    
    <div class="card">
        <h3><i class="fas fa-bell"></i> إرسال إشعار للنظام</h3>
        <form method="POST">
            <input type="text" name="push_title" placeholder="العنوان" required>
            <textarea name="push_body" placeholder="الرسالة" rows="2" required></textarea>
            <button name="send_push" class="btn" style="background:#0ea5e9">إرسال الآن</button>
        </form>
    </div>

    <div class="card">
        <h3><i class="fas fa-plus"></i> إضافة مباراة يدوية</h3>
        <form method="POST">
            <input type="text" name="m_league" placeholder="اسم البطولة" required>
            <input type="text" name="m_home" placeholder="الفريق 1" required>
            <input type="text" name="m_home_logo" placeholder="رابط شعار 1">
            <input type="text" name="m_away" placeholder="الفريق 2" required>
            <input type="text" name="m_away_logo" placeholder="رابط شعار 2">
            <input type="text" name="m_time" placeholder="الوقت" required>
            <select name="m_ch"><?php foreach($available_channels as $v=>$l) if($v!="") echo "<option value='$v'>$l</option>"; ?></select>
            <button name="add_custom" class="btn" style="background:#22c55e">إضافة</button>
        </form>
    </div>

    <form method="POST">
        <h3>مباريات يدوية مضافة</h3>
        <?php foreach($saved_data['custom_matches'] as $cm): ?>
            <div class="card" style="position:relative">
                <a href="?del_match=<?php echo $cm['id']; ?>" style="position:absolute; top:10px; left:10px; color:#e11d48"><i class="fas fa-trash"></i></a>
                <?php echo $cm['home']; ?> vs <?php echo $cm['away']; ?>
            </div>
        <?php endforeach; ?>

        <h3>مباريات API</h3>
        <?php if(isset($match_data['matches'])) foreach($match_data['matches'] as $m): 
            $id = $m['homeTeam']['name']." vs ".$m['awayTeam']['name']; ?>
            <div class="card">
                <?php echo $id; ?><br>
                <select name="channels[<?php echo $id; ?>]">
                    <?php foreach($available_channels as $v=>$l): ?>
                        <option value="<?php echo $v; ?>" <?php echo (($saved_data[$id]??'')==$v)?'selected':''; ?>><?php echo $l; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>
        <button name="save_all" class="btn save-btn">حفظ جميع التعديلات</button>
    </form>
</body>
</html>
