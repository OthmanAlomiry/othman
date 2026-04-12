<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- بيانات السحابة ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';
$user_admin = "othman"; $pass_admin = "1405";

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; }
}

function callCloud($method, $bin, $key, $data = null) {
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest?nocache=" . time() : "");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $headers = ["X-Master-Key: " . $key];
    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    } else {
        $headers[] = "X-Bin-Meta: false";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

if(isset($_SESSION['ok'])){
    $record = callCloud("GET", $BIN_ID, $API_KEY);
    $channels = $record['custom_channels'] ?? [];
    $sections = $record['sections'] ?? [];
    $news_ticker = $record['news_ticker'] ?? ['text' => '', 'status' => 'hide'];
    $notification = $record['notification'] ?? ['id' => '', 'msg' => ''];

    function sync($bin, $key, $channels, $sections, $news, $notify) {
        return callCloud("PUT", $bin, $key, [
            'custom_channels' => array_values($channels),
            'sections' => array_values($sections),
            'news_ticker' => $news,
            'notification' => $notify
        ]);
    }

    if(isset($_POST['update_ticker'])){
        $news_ticker = ['text' => $_POST['ticker_text'], 'status' => $_POST['ticker_status']];
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    if(isset($_POST['save_sec'])){
        $sec_id = $_POST['sec_id'] ?: uniqid();
        $new_sec = ['id' => $sec_id, 'name' => $_POST['sec_name'], 'key' => $_POST['sec_key'], 'img' => $_POST['sec_img'], 'status' => $_POST['sec_status']];
        $found = false;
        foreach($sections as &$s){ if($s['id'] == $sec_id){ $s = $new_sec; $found = true; break; } }
        if(!$found) $sections[] = $new_sec;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#sections_area"); exit;
    }

    if(isset($_POST['save_ch'])){
        $target_id = $_POST['edit_id'] ?: uniqid();
        $ch_data = ['id' => $target_id, 'name' => $_POST['n'], 'file' => $_POST['f'], 'file_backup' => $_POST['f_backup'], 'section' => $_POST['s']];
        $found = false;
        foreach($channels as &$c){ if($c['id'] == $target_id){ $c = $ch_data; $found = true; break; } }
        if(!$found) $channels[] = $ch_data;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#channels_area"); exit;
    }

    if(isset($_GET['del'])){
        $channels = array_values(array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; }));
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#channels_area"); exit;
    }

    $edit_ch = null; if(isset($_GET['edit'])){ foreach($channels as $c){ if($c['id'] == $_GET['edit']){ $edit_ch = $c; break; } } }
    $edit_sec = null; if(isset($_GET['edit_sec'])){ foreach($sections as $s){ if($s['id'] == $_GET['edit_sec']){ $edit_sec = $s; break; } } }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم عثمان</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #050c14; color: white; font-family: 'Tajawal', sans-serif; padding: 20px; text-align: right; }
        .box { max-width: 600px; margin: 20px auto; background: #0f172a; padding: 20px; border-radius: 12px; border: 1px solid #1e293b; }
        input, select, textarea, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: white; }
        button { background: #e11d48; cursor: pointer; font-weight: bold; border: none; }
        .item { background: #1e293b; padding: 10px; margin: 5px 0; border-radius: 5px; display: flex; justify-content: space-between; }
        .edit { color: #38bdf8; text-decoration: none; } .del { color: #f87171; text-decoration: none; }
    </style>
</head>
<body>

<?php if(!isset($_SESSION['ok'])): ?>
    <div class="box" style="text-align: center;">
        <h2>🔐 تسجيل الدخول</h2>
        <form method="POST">
            <input name="u" placeholder="اسم المستخدم" required>
            <input type="password" name="p" placeholder="كلمة المرور" required>
            <button name="login">دخول</button>
        </form>
    </div>
<?php else: ?>
    <h2 style="text-align:center;">مرحباً عثمان 🛠️</h2>

    <div class="box">
        <h3>📰 الشريط الإخباري</h3>
        <form method="POST">
            <textarea name="ticker_text"><?= $news_ticker['text'] ?></textarea>
            <select name="ticker_status">
                <option value="show" <?= $news_ticker['status']=='show'?'selected':'' ?>>إظهار</option>
                <option value="hide" <?= $news_ticker['status']=='hide'?'selected':'' ?>>إخفاء</option>
            </select>
            <button name="update_ticker">تحديث الشريط</button>
        </form>
    </div>

    <div class="box" id="channels_area">
        <h3>📺 إضافة / تعديل قناة</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= $edit_ch['id'] ?? '' ?>">
            <input name="n" placeholder="اسم القناة" value="<?= $edit_ch['name'] ?? '' ?>" required>
            <input name="f" placeholder="رابط البث" value="<?= $edit_ch['file'] ?? '' ?>" required>
            <input name="f_backup" placeholder="رابط احتياطي (اختياري)" value="<?= $edit_ch['file_backup'] ?? '' ?>">
            <select name="s" required>
                <option value="">اختر القسم</option>
                <?php foreach($sections as $s): ?>
                    <option value="<?= $s['key'] ?>" <?= (isset($edit_ch) && $edit_ch['section']==$s['key'])?'selected':'' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button name="save_ch">حفظ القناة</button>
        </form>
        <hr>
        <?php foreach(array_reverse($channels) as $c): ?>
            <div class="item">
                <span><?= $c['name'] ?></span>
                <div>
                    <a href="?edit=<?= $c['id'] ?>#channels_area" class="edit">تعديل</a> | 
                    <a href="?del=<?= $c['id'] ?>" class="del" onclick="return confirm('حذف؟')">حذف</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p style="text-align: center;"><a href="?out=1" style="color: #94a3b8;">تسجيل الخروج</a></p>
<?php endif; ?>

</body>
</html>
