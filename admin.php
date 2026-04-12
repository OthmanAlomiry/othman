<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- بيانات السحابة (تأكد من صحتها) ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';
$user_admin = "othman"; 
$pass_admin = "1405";

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; }
}

function callCloud($method, $bin, $key, $data = null) {
    $url = "https://api.jsonbin.io/v3/b/" . $bin;
    if ($method == "GET") $url .= "/latest?nocache=" . time();

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $headers = [
        "X-Master-Key: " . $key,
        "X-Bin-Meta: false" // لكي نستلم البيانات مباشرة بدون زوائد
    ];

    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        // ترويسة هامة جداً لمنع تراكم النسخ القديمة
        $headers[] = "X-Bin-Versioning: false"; 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($info == 200) ? json_decode($res, true) : null;
}

if(isset($_SESSION['ok'])){
    // جلب البيانات
    $record = callCloud("GET", $BIN_ID, $API_KEY);
    
    // إذا فشل الجلب، نضع هيكل فارغ لكي لا يمسح البيانات الموجودة
    if (!$record) {
        $channels = []; $sections = []; $news_ticker = ['text'=>'', 'status'=>'hide']; $notification = ['msg'=>''];
    } else {
        $channels = $record['custom_channels'] ?? [];
        $sections = $record['sections'] ?? [];
        $news_ticker = $record['news_ticker'] ?? ['text'=>'', 'status'=>'hide'];
        $notification = $record['notification'] ?? ['msg'=>''];
    }

    // دالة المزامنة
    function sync($bin, $key, $chans, $secs, $news, $notif) {
        $data = [
            'custom_channels' => array_values($chans),
            'sections' => array_values($secs),
            'news_ticker' => $news,
            'notification' => $notif
        ];
        return callCloud("PUT", $bin, $key, $data);
    }

    // 1. تحديث الشريط الإخباري
    if(isset($_POST['update_ticker'])){
        $news_ticker = ['text' => $_POST['ticker_text'], 'status' => $_POST['ticker_status']];
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // 2. إدارة الأقسام
    if(isset($_POST['save_sec'])){
        $id = $_POST['sec_id'] ?: uniqid();
        $new_sec = ['id'=>$id, 'name'=>$_POST['sec_name'], 'key'=>$_POST['sec_key'], 'img'=>$_POST['sec_img'], 'status'=>$_POST['sec_status']];
        $found = false;
        foreach($sections as &$s){ if($s['id'] == $id){ $s = $new_sec; $found = true; break; } }
        if(!$found) $sections[] = $new_sec;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // 3. إدارة القنوات
    if(isset($_POST['save_ch'])){
        $id = $_POST['edit_id'] ?: uniqid();
        $new_ch = ['id'=>$id, 'name'=>$_POST['n'], 'file'=>$_POST['f'], 'file_backup'=>$_POST['f_backup'], 'section'=>$_POST['s']];
        $found = false;
        foreach($channels as &$c){ if($c['id'] == $id){ $c = $new_ch; $found = true; break; } }
        if(!$found) $channels[] = $new_ch;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // 4. الحذف
    if(isset($_GET['del'])){
        $channels = array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; });
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    $edit_ch = null; if(isset($_GET['edit'])){ foreach($channels as $c){ if($c['id']==$_GET['edit']) $edit_ch=$c; } }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | عثمان</title>
    <style>
        body { background: #0b0f19; color: white; font-family: sans-serif; padding: 20px; }
        .box { max-width: 500px; margin: auto; background: #161b22; padding: 20px; border-radius: 10px; border: 1px solid #30363d; margin-bottom: 20px; }
        input, select, textarea, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #30363d; background: #0d1117; color: white; box-sizing: border-box; }
        button { background: #238636; border: none; cursor: pointer; font-weight: bold; }
        .item { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #30363d; }
        .msg { color: #2ea043; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

<?php if(!isset($_SESSION['ok'])): ?>
    <div class="box">
        <h2 align="center">دخول الإدارة</h2>
        <form method="POST"><input name="u" placeholder="اليوزر"><input type="password" name="p" placeholder="الباسورد"><button name="login">دخول</button></form>
    </div>
<?php else: ?>
    
    <?php if(isset($_GET['success'])) echo '<p class="msg">✅ تم الحفظ بنجاح</p>'; ?>

    <div class="box">
        <h3>📰 الشريط الإخباري</h3>
        <form method="POST">
            <textarea name="ticker_text"><?= $news_ticker['text'] ?></textarea>
            <select name="ticker_status">
                <option value="show" <?= $news_ticker['status']=='show'?'selected':'' ?>>إظهار</option>
                <option value="hide" <?= $news_ticker['status']=='hide'?'selected':'' ?>>إخفاء</option>
            </select>
            <button name="update_ticker">تحديث</button>
        </form>
    </div>

    <div class="box">
        <h3>📺 إدارة القنوات</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= $edit_ch['id'] ?? '' ?>">
            <input name="n" placeholder="اسم القناة" value="<?= $edit_ch['name'] ?? '' ?>" required>
            <input name="f" placeholder="رابط البث" value="<?= $edit_ch['file'] ?? '' ?>" required>
            <input name="f_backup" placeholder="الرابط الاحتياطي" value="<?= $edit_ch['file_backup'] ?? '' ?>">
            <select name="s" required>
                <option value="">اختر القسم</option>
                <?php foreach($sections as $s): ?>
                    <option value="<?= $s['key'] ?>" <?= (isset($edit_ch) && $edit_ch['section']==$s['key'])?'selected':'' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button name="save_ch">حفظ</button>
        </form>
        <hr>
        <?php foreach(array_reverse($channels) as $c): ?>
            <div class="item">
                <span><?= $c['name'] ?></span>
                <a href="?edit=<?= $c['id'] ?>" style="color:#58a6ff">تعديل</a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <p align="center"><a href="?out=1" style="color:#f85149">خروج</a></p>
<?php endif; ?>

</body>
</html>
