<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات السحابة عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$user_admin = "othman"; 
$pass_admin = "1405";

// دالة جلب البيانات مع دعم نظام الـ record عثمان
function callCloud($method, $bin, $key, $data = null) {
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest" : "");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array("X-Master-Key: " . $key, "X-Bin-Meta: false");
    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($res, true);
    // إذا كانت البيانات داخل record نقوم باستخراجها عثمان
    return (isset($result['record'])) ? $result['record'] : $result;
}

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; }
}

if(isset($_SESSION['ok'])){
    // جلب البيانات الحالية
    $db = callCloud("GET", $BIN_ID, $API_KEY);
    
    $channels = (isset($db['custom_channels'])) ? $db['custom_channels'] : array();
    $sections = (isset($db['sections'])) ? $db['sections'] : array();
    $news_ticker = (isset($db['news_ticker'])) ? $db['news_ticker'] : array('text' => '', 'status' => 'hide');
    $notification = (isset($db['notification'])) ? $db['notification'] : array('id' => '', 'msg' => '');

    // وظيفة الحفظ الآمنة عثمان
    function saveData($bin, $key, $ch, $sec, $news, $notif) {
        $newData = array(
            'custom_channels' => array_values($ch),
            'sections' => array_values($sec),
            'news_ticker' => $news,
            'notification' => $notif
        );
        return callCloud("PUT", $bin, $key, $newData);
    }

    // إرسال إشعار
    if(isset($_POST['send_notify'])){
        $notification = array('id' => uniqid(), 'msg' => $_POST['notify_msg'], 'time' => time());
        saveData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php"); exit;
    }

    // إدارة الأقسام
    if(isset($_POST['save_sec'])){
        $new_sec = array(
            'id' => $_POST['sec_id'] ?: uniqid(),
            'name' => $_POST['sec_name'],
            'key' => $_POST['sec_key'],
            'img' => $_POST['sec_img'],
            'status' => $_POST['sec_status']
        );
        $found = false;
        foreach($sections as &$s){ if($s['id'] == $new_sec['id']){ $s = $new_sec; $found = true; break; } }
        if(!$found) $sections[] = $new_sec;
        saveData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php"); exit;
    }

    // إدارة القنوات
    if(isset($_POST['save_ch'])){
        $new_ch = array(
            'id' => $_POST['edit_id'] ?: uniqid(),
            'name' => $_POST['n'],
            'file' => $_POST['f'],
            'section' => $_POST['s']
        );
        $found = false;
        foreach($channels as &$c){ if($c['id'] == $new_ch['id']){ $c = $new_ch; $found = true; break; } }
        if(!$found) $channels[] = $new_ch;
        saveData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php"); exit;
    }

    if(isset($_GET['del'])){
        $channels = array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; });
        saveData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php"); exit;
    }
    
    $edit_ch = null; if(isset($_GET['edit'])){ foreach($channels as $c){ if($c['id'] == $_GET['edit']){ $edit_ch = $c; break; } } }
    $edit_sec = null; if(isset($_GET['edit_sec'])){ foreach($sections as $s){ if($s['id'] == $_GET['edit_sec']){ $edit_sec = $s; break; } } }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم عثمان</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #050c14; color: white; font-family: 'Tajawal', sans-serif; padding: 15px; }
        .box { max-width: 600px; margin: 20px auto; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); }
        input, select, button, textarea { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #333; background: #111; color: white; box-sizing: border-box; }
        button { background: #e11d48; border: none; font-weight: bold; cursor: pointer; }
        .item-card { background: rgba(255,255,255,0.03); margin: 10px 0; padding: 15px; border-radius: 10px; display: flex; justify-content: space-between; }
        a { color: #0ea5e9; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>

<?php if(!isset($_SESSION['ok'])): ?>
    <div class="box" style="text-align:center;">
        <h2>🔐 دخول الإدارة</h2>
        <form method="POST"><input name="u" placeholder="اليوزر"><input type="password" name="p" placeholder="الباسورد"><button name="login">دخول</button></form>
    </div>
<?php else: ?>

    <div class="box">
        <h2>🔔 إرسال إشعار</h2>
        <form method="POST"><textarea name="notify_msg" placeholder="نص الإشعار..."></textarea><button name="send_notify">إرسال الآن</button></form>
    </div>

    <div class="box">
        <h2>📂 الأقسام (الموجودة: <?= count($sections) ?>)</h2>
        <form method="POST">
            <input type="hidden" name="sec_id" value="<?= $edit_sec ? $edit_sec['id'] : '' ?>">
            <input name="sec_name" placeholder="اسم القسم" value="<?= $edit_sec ? $edit_sec['name'] : '' ?>" required>
            <input name="sec_key" placeholder="الكود" value="<?= $edit_sec ? $edit_sec['key'] : '' ?>" required>
            <input name="sec_img" placeholder="رابط الصورة" value="<?= $edit_sec ? $edit_sec['img'] : '' ?>" required>
            <select name="sec_status"><option value="show">إظهار</option><option value="hide">إخفاء</option></select>
            <button name="save_sec">حفظ القسم</button>
        </form>
        <?php foreach($sections as $s): ?>
            <div class="item-card"><span><?= $s['name'] ?></span><a href="?edit_sec=<?= $s['id'] ?>">تعديل</a></div>
        <?php endforeach; ?>
    </div>

    <div class="box">
        <h2>📺 القنوات (الموجودة: <?= count($channels) ?>)</h2>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= $edit_ch ? $edit_ch['id'] : '' ?>">
            <input name="n" placeholder="اسم القناة" value="<?= $edit_ch ? $edit_ch['name'] : '' ?>" required>
            <input name="f" placeholder="رابط البث" value="<?= $edit_ch ? $edit_ch['file'] : '' ?>" required>
            <select name="s">
                <?php foreach($sections as $s): ?>
                    <option value="<?= $s['key'] ?>" <?= ($edit_ch && $edit_ch['section']==$s['key'])?'selected':'' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button name="save_ch">حفظ القناة</button>
        </form>
        <?php foreach(array_reverse($channels) as $c): ?>
            <div class="item-card"><span><?= $c['name'] ?></span><div><a href="?edit=<?= $c['id'] ?>">تعديل</a> | <a href="?del=<?= $c['id'] ?>" style="color:red;">حذف</a></div></div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
</body>
</html>
