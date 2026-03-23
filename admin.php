<?php
session_start();
$password = "admin123"; 
$manual_file = 'manual_channels.json';

if (isset($_POST['login'])) { if ($_POST['pass'] == $password) { $_SESSION['admin'] = true; } }
if (!isset($_SESSION['admin'])) { die('<form method="POST" style="text-align:center;padding:100px;background:#050c14;color:#fff;height:100vh;"><h2>الخدمة الرقمية</h2><input type="password" name="pass" placeholder="Password" style="padding:10px;border-radius:5px;"><button name="login" style="padding:10px;background:#e11d48;color:#fff;border:none;margin-left:5px;">Enter</button></form>'); }

$saved_data = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : ['custom_matches' => []];

// إرسال الإشعار لـ OneSignal ببيانات عثمان الرسمية
function sendPush($title, $body) {
    $fields = array(
        'app_id' => "6e41fb93-1b65-4596-86f4-ad8589b38ad7",
        'included_segments' => array('All'),
        'contents' => array("en" => $body),
        'headings' => array("en" => $title)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 
        'Authorization: Basic os_v2_app_nza7xey3mvcznbxuvwcytm4k25selce42yienpf343e44llw337xhzaez32pw6qploucliayfuzr22aqfylhujxmh4ryv2w72obz4ti'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_exec($ch); curl_close($ch);
}

if (isset($_POST['send_push'])) { sendPush($_POST['push_title'], $_POST['push_body']); echo "<script>alert('تم إرسال الإشعار بنجاح!');</script>"; }

if (isset($_POST['add_custom'])) {
    $saved_data['custom_matches'][] = ['id'=>uniqid(), 'league'=>$_POST['m_league'], 'home'=>$_POST['m_home'], 'home_logo'=>$_POST['m_home_logo'], 'away'=>$_POST['m_away'], 'away_logo'=>$_POST['m_away_logo'], 'time'=>$_POST['m_time'], 'ch'=>$_POST['m_ch']];
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>window.location='admin.php';</script>";
}

if (isset($_GET['del_match'])) {
    foreach($saved_data['custom_matches'] as $k => $v) { if($v['id'] == $_GET['del_match']) unset($saved_data['custom_matches'][$k]); }
    $saved_data['custom_matches'] = array_values($saved_data['custom_matches']);
    file_put_contents($manual_file, json_encode($saved_data));
    header("Location: admin.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><title>لوحة التحكم</title>
    <style>
        body{font-family:Tahoma;background:#050c14;color:white;padding:20px;} 
        .card{background:rgba(255,255,255,0.05);padding:15px;margin-bottom:15px;border-radius:10px;border:1px solid #333;} 
        input,textarea,select{width:100%;padding:10px;margin:5px 0;background:#000;color:#fff;border:1px solid #444;box-sizing:border-box;}
        .btn{width:100%;padding:12px;border:none;border-radius:8px;color:#fff;font-weight:bold;cursor:pointer;margin-top:10px;}
    </style>
</head>
<body>
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2 style="color:#e11d48">لوحة تحكم عثمان</h2>
        <a href="index.php" style="color:#fff; text-decoration:none;">◀ العودة للموقع</a>
    </div>

    <div class="card">
        <h3><i class="fas fa-bell"></i> إرسال إشعار للنظام</h3>
        <form method="POST">
            <input type="text" name="push_title" placeholder="عنوان الإشعار (مثال: ريال مدريد يسجل!)" required>
            <textarea name="push_body" placeholder="نص الرسالة" rows="2" required></textarea>
            <button name="send_push" class="btn" style="background:#0ea5e9">إرسال الإشعار الآن</button>
        </form>
    </div>

    <div class="card">
        <h3>إضافة مباراة للجدول</h3>
        <form method="POST">
            <input type="text" name="m_league" placeholder="البطولة"><input type="text" name="m_home" placeholder="الفريق 1"><input type="text" name="m_home_logo" placeholder="رابط شعار 1"><input type="text" name="m_away" placeholder="الفريق 2"><input type="text" name="m_away_logo" placeholder="رابط شعار 2"><input type="text" name="m_time" placeholder="الوقت">
            <select name="m_ch">
                <option value="postponed">⚠️ مؤجلة</option>
                <?php for($i=1;$i<=14;$i++) echo "<option value='$i'>قناة $i</option>"; ?>
            </select>
            <button name="add_custom" class="btn" style="background:#22c55e">إضافة للموقع</button>
        </form>
    </div>
</body>
</html>
