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

// دالة الاتصال بالسحابة عثمان
function callCloud($method, $bin, $key, $data = null) {
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest" : "");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array("X-Master-Key: " . $key);
    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// دالة المزامنة الآمنة عثمان (تمنع الحذف التلقائي)
function sync($bin, $key, $channels, $sections, $news, $notify) {
    // حماية عثمان: إذا كانت البيانات فارغة بشكل مريب، لا تحفظها
    if (empty($channels) && empty($sections) && $news['text'] == '') {
        return false;
    }
    $payload = array(
        'custom_channels' => array_values($channels),
        'sections' => array_values($sections),
        'news_ticker' => $news,
        'notification' => $notify
    );
    return callCloud("PUT", $bin, $key, $payload);
}

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; }
}

if(isset($_SESSION['ok'])){
    // جلب البيانات الحالية عثمان
    $res = callCloud("GET", $BIN_ID, $API_KEY);
    $record = (isset($res['record'])) ? $res['record'] : array();
    
    $channels = (isset($record['custom_channels'])) ? $record['custom_channels'] : array();
    $sections = (isset($record['sections'])) ? $record['sections'] : array();
    $news_ticker = (isset($record['news_ticker'])) ? $record['news_ticker'] : array('text' => 'مرحباً بكم', 'status' => 'hide');
    $notification = (isset($record['notification'])) ? $record['notification'] : array('id' => '', 'msg' => '', 'time' => 0);

    // 0. إرسال إشعار فوري
    if(isset($_POST['send_notify'])){
        $notification = array(
            'id' => uniqid(),
            'msg' => $_POST['notify_msg'],
            'time' => time()
        );
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php"); exit;
    }

    // 1. إدارة الشريط الإخباري
    if(isset($_POST['update_ticker'])){
        $news_ticker = array('text' => $_POST['ticker_text'], 'status' => $_POST['ticker_status']);
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php"); exit;
    }

    // 2. إدارة الأقسام
    if(isset($_POST['save_sec'])){
        $sec_id = (!empty($_POST['sec_id'])) ? $_POST['sec_id'] : uniqid();
        $new_sec = array(
            'id' => $sec_id, 
            'name' => $_POST['sec_name'], 
            'key' => $_POST['sec_key'], 
            'img' => $_POST['sec_img'], 
            'status' => $_POST['sec_status']
        );
        $found = false;
        foreach($sections as &$s){ if($s['id'] == $sec_id){ $s = $new_sec; $found = true; break; } }
        if(!$found) $sections[] = $new_sec;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php#sections_area"); exit;
    }

    if(isset($_GET['del_sec'])){
        $sections = array_filter($sections, function($s){ return $s['id'] !== $_GET['del_sec']; });
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php#sections_area"); exit;
    }

    // 3. إدارة القنوات
    if(isset($_POST['save_ch'])){
        $target_id = $_POST['edit_id'];
        $ch_data = array(
            'id' => ($target_id ?: uniqid()), 
            'name' => $_POST['n'], 
            'file' => $_POST['f'], 
            'section' => $_POST['s']
        );
        $ch_found = false;
        if(!empty($target_id)){ 
            foreach($channels as &$c){ if($c['id'] == $target_id){ $c = $ch_data; $ch_found = true; break; } } 
        }
        if(!$ch_found) $channels[] = $ch_data;

        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php#channels_area"); exit;
    }

    if(isset($_GET['del'])){
        $channels = array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; });
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php#channels_area"); exit;
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
        body { background: #050c14; color: white; font-family: 'Tajawal', sans-serif; margin: 0; padding: 15px; font-size: 14px; }
        .box { max-width: 600px; margin: 15px auto; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); }
        h2 { color: #e11d48; font-size: 1.1rem; border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 0; }
        input, select, button, textarea { width: 100%; padding: 10px; margin: 5px 0; border-radius: 8px; border: 1px solid #333; background: #111; color: white; box-sizing: border-box; font-family: inherit; }
        button { background: #e11d48; border: none; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #f43f5e; }
        .item-card { background: rgba(255,255,255,0.03); margin: 8px 0; padding: 12px; border-radius: 10px; border-right: 3px solid #e11d48; display: flex; justify-content: space-between; align-items: center; }
        .btns a { text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 10px; }
        .del { color: #ff4d4d; } .edit { color: #0ea5e9; }
    </style>
</head>
<body>

<?php if(!isset($_SESSION['ok'])): ?>
    <div class="box" style="margin-top: 100px; text-align: center;">
        <h2>🔐 دخول الإدارة</h2>
        <form method="POST"><input name="u" placeholder="اليوزر"><input type="password" name="p" placeholder="الباسورد"><button name="login">دخول</button></form>
    </div>
<?php else: ?>

    <div class="box">
        <h2>🔔 إرسال إشعار فوري</h2>
        <form method="POST">
            <textarea name="notify_msg" placeholder="اكتب نص الإشعار هنا..." rows="2" required></textarea>
            <button name="send_notify" style="background:#0ea5e9">إرسال الإشعار الآن</button>
        </form>
    </div>

    <div class="box">
        <h2>📰 الشريط الإخباري</h2>
        <form method="POST">
            <textarea name="ticker_text" placeholder="نص الشريط..." required><?= $news_ticker['text'] ?></textarea>
            <select name="ticker_status">
                <option value="show" <?= ($news_ticker['status']=='show')?'selected':'' ?>>إظهار الشريط</option>
                <option value="hide" <?= ($news_ticker['status']=='hide')?'selected':'' ?>>إخفاء الشريط</option>
            </select>
            <button name="update_ticker" style="background:#22c55e">تحديث الشريط</button>
        </form>
    </div>

    <div class="box" id="sections_area">
        <h2>📂 إدارة الأقسام</h2>
        <form method="POST">
            <input type="hidden" name="sec_id" value="<?= $edit_sec ? $edit_sec['id'] : '' ?>">
            <input name="sec_name" placeholder="اسم القسم" value="<?= $edit_sec ? $edit_sec['name'] : '' ?>" required>
            <input name="sec_key" placeholder="الكود (مثال: sports)" value="<?= $edit_sec ? $edit_sec['key'] : '' ?>" required>
            <input name="sec_img" placeholder="رابط الأيقونة" value="<?= $edit_sec ? $edit_sec['img'] : '' ?>" required>
            <select name="sec_status"><option value="show" <?= ($edit_sec && $edit_sec['status']=='show')?'selected':'' ?>>إظهار</option><option value="hide" <?= ($edit_sec && $edit_sec['status']=='hide')?'selected':'' ?>>إخفاء</option></select>
            <button name="save_sec" style="background:#0ea5e9">حفظ القسم</button>
        </form>
        <?php foreach($sections as $s): ?>
            <div class="item-card"><div><b><?= $s['name'] ?></b></div><div class="btns"><a href="?edit_sec=<?= $s['id'] ?>#sections_area" class="edit">تعديل</a><a href="?del_sec=<?= $s['id'] ?>" class="del">حذف</a></div></div>
        <?php endforeach; ?>
    </div>

    <div class="box" id="channels_area">
        <h2>📺 إدارة القنوات</h2>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= $edit_ch ? $edit_ch['id'] : '' ?>">
            <input name="n" placeholder="اسم القناة" value="<?= $edit_ch ? $edit_ch['name'] : '' ?>" required>
            <input name="f" placeholder="رابط الملف (m3u8/iframe)" value="<?= $edit_ch ? $edit_ch['file'] : '' ?>" required>
            <select name="s" required>
                <option value="">-- اختر القسم --</option>
                <?php foreach($sections as $s): ?>
                    <option value="<?= $s['key'] ?>" <?= ($edit_ch && $edit_ch['section']==$s['key'])?'selected':'' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button name="save_ch">حفظ القناة</button>
        </form>
        <?php foreach(array_reverse($channels) as $c): ?>
            <div class="item-card"><div><b><?= $c['name'] ?></b></div><div class="btns"><a href="?edit=<?= $c['id'] ?>#channels_area" class="edit">تعديل</a><a href="?del=<?= $c['id'] ?>" class="del">حذف</a></div></div>
        <?php endforeach; ?>
        <center style="margin-top:20px;"><a href="index.php" style="color:#aaa; text-decoration:none;">← الموقع</a> | <a href="?out=1" style="color:#ff4d4d; text-decoration:none;">خروج</a></center>
    </div>
<?php endif; ?>
</body>
</html>
