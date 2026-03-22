<?php
session_start();
$password = "admin123"; 
$manual_file = 'manual_channels.json';
$tokens_file = 'push_tokens.txt'; // ملف لتخزين اشتراكات الزوار

if (isset($_POST['login'])) {
    if ($_POST['pass'] == $password) { $_SESSION['admin'] = true; }
}

if (!isset($_SESSION['admin'])) {
    die('<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><title>دخول</title><style>body { font-family: Tahoma; background: #050c14; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; } form { background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); text-align: center; } input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 8px; border: none; background: #000; color: #fff; } button { width: 100%; padding: 10px; background: #e11d48; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }</style></head><body><form method="POST"><h2>الإدارة</h2><input type="password" name="pass" placeholder="كلمة المرور" required><button name="login">دخول</button></form></body></html>');
}

$saved_data = file_exists($manual_file) ? json_decode(file_get_contents($manual_file), true) : ['custom_matches' => []];

// --- نظام إرسال الإشعارات الجديد ---
if (isset($_POST['send_push'])) {
    $msg_title = $_POST['push_title'];
    $msg_body = $_POST['push_body'];
    
    // سنستخدم ملف JSON مؤقت ليقرأه المتصفح عند الزوار ويرسل التنبيه
    $push_payload = ['title' => $msg_title, 'body' => $msg_body, 'time' => time()];
    file_put_contents('push_msg.json', json_encode($push_payload));
    
    echo "<script>alert('تم إرسال الإشعار لجميع الزوار النشطين');</script>";
}

// إضافة مباراة يدوياً
if (isset($_POST['add_custom'])) {
    $new_match = ['id' => uniqid(), 'league' => $_POST['m_league'], 'home' => $_POST['m_home'], 'home_logo' => $_POST['m_home_logo'], 'away' => $_POST['m_away'], 'away_logo' => $_POST['m_away_logo'], 'time' => $_POST['m_time'], 'ch' => $_POST['m_ch']];
    $saved_data['custom_matches'][] = $new_match;
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>window.location='admin.php';</script>";
}

// حذف مباراة يدوية
if (isset($_GET['del_match'])) {
    $match_id = $_GET['del_match'];
    foreach($saved_data['custom_matches'] as $k => $v) { if($v['id'] == $match_id) unset($saved_data['custom_matches'][$k]); }
    $saved_data['custom_matches'] = array_values($saved_data['custom_matches'] ?? []);
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>window.location='admin.php';</script>";
}

// حفظ تعيينات القنوات
if (isset($_POST['save_all'])) {
    if (isset($_POST['channels']) && is_array($_POST['channels'])) {
        foreach ($_POST['channels'] as $id => $val) { $saved_data[$id] = $val; }
    }
    file_put_contents($manual_file, json_encode($saved_data));
    echo "<script>alert('تم الحفظ'); window.location='admin.php';</script>";
}

$available_channels = ["" => "تلقائي", "postponed" => "⚠️ مؤجلة", "1" => "beIN 1", "2" => "beIN 2", "3" => "beIN 3", "4" => "beIN 4", "5" => "beIN 5", "6" => "beIN 6", "7" => "beIN 7", "8" => "beIN 8", "9" => "beIN 9", "10" => "STARZ 1", "11" => "STARZ 2", "12" => "MBC Action", "13" => "شاهد 1", "14" => "شاهد 2"];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم الشاملة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --glass: rgba(255, 255, 255, 0.05); }
        body { font-family: 'Tajawal', sans-serif; background: var(--bg); color: #fff; padding: 15px; padding-top: 70px; padding-bottom: 100px; margin:0; }
        .header { position: fixed; top: 0; left: 0; right: 0; background: rgba(5,12,20,0.9); backdrop-filter: blur(15px); padding: 15px; z-index: 1000; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 600px; margin: auto; }
        .section-title { font-size: 15px; font-weight: 900; color: #f1c40f; margin: 20px 0 10px 0; border-right: 4px solid var(--main); padding-right: 10px; }
        .card-panel { background: var(--glass); border: 1px solid rgba(255,255,255,0.1); padding: 15px; border-radius: 18px; margin-bottom: 15px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 5px 0; border-radius: 8px; border: 1px solid #333; background: #000; color: #fff; box-sizing: border-box; font-family: inherit; }
        .btn-green { background: #22c55e; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; width: 100%; cursor: pointer; }
        .btn-blue { background: #0ea5e9; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; width: 100%; cursor: pointer; }
        .floating-save { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; background: var(--main); color: white; padding: 15px; border-radius: 50px; border: none; font-weight: 900; cursor: pointer; box-shadow: 0 10px 30px rgba(225, 29, 72, 0.4); z-index: 1001; }
    </style>
</head>
<body>

    <div class="header">
        <h2 style="margin:0; font-size:16px; color:var(--main);"><i class="fas fa-tools"></i> لوحة التحكم</h2>
        <a href="index.php" style="color:#fff; text-decoration:none; font-size:12px;">◀ الموقع</a>
    </div>

    <div class="container">
        
        <div class="section-title"><i class="fas fa-bell"></i> إرسال إشعار فوري للزوار</div>
        <form method="POST" class="card-panel" style="border-color: #0ea5e9;">
            <input type="text" name="push_title" placeholder="عنوان الإشعار (مثال: هدف الآن!)" required>
            <textarea name="push_body" placeholder="نص الرسالة (مثال: ريال مدريد يسجل الأول.. شاهد الآن)" rows="2" required></textarea>
            <button name="send_push" class="btn-blue"><i class="fas fa-paper-plane"></i> إرسال التنبيه للجميع</button>
        </form>

        <div class="section-title"><i class="fas fa-plus-circle"></i> إضافة مباراة يدوية</div>
        <form method="POST" class="card-panel" style="border-color: #22c55e;">
            <input type="text" name="m_league" placeholder="اسم البطولة" required>
            <div style="display:flex; gap:5px;">
                <input type="text" name="m_home" placeholder="الفريق 1" required>
                <input type="text" name="m_home_logo" placeholder="رابط شعار 1">
            </div>
            <div style="display:flex; gap:5px;">
                <input type="text" name="m_away" placeholder="الفريق 2" required>
                <input type="text" name="m_away_logo" placeholder="رابط شعار 2">
            </div>
            <input type="text" name="m_time" placeholder="الوقت (مثل: 10:00 PM)" required>
            <select name="m_ch">
                <?php foreach($available_channels as $v => $l): if($v=="")continue; ?>
                    <option value="<?php echo $v; ?>"><?php echo $l; ?></option>
                <?php endforeach; ?>
            </select>
            <button name="add_custom" class="btn-green">إضافة المباراة للجدول</button>
        </form>

        <form method="POST">
            <div class="section-title"><i class="fas fa-list"></i> إدارة القنوات الحالية</div>
            <?php foreach($saved_data['custom_matches'] as $cm): ?>
                <div class="card-panel" style="position:relative;">
                    <a href="?del_match=<?php echo $cm['id']; ?>" style="position:absolute; top:10px; left:10px; color:var(--main); text-decoration:none; font-size:11px;"><i class="fas fa-trash"></i> حذف</a>
                    <div style="font-size:13px; font-weight:bold;"><?php echo $cm['home']; ?> vs <?php echo $cm['away']; ?></div>
                    <div style="font-size:10px; opacity:0.6;"><?php echo $cm['league']; ?></div>
                </div>
            <?php endforeach; ?>

            <button type="submit" name="save_all" class="floating-save">حفظ جميع التعديلات</button>
        </form>
    </div>
</body>
</html>
