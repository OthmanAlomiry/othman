<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- بيانات السحابة المحدثة ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69db5fa1856a6821892565a6'; // الـ Bin الجديد الخاص بك
$user_admin = "othman"; 
$pass_admin = "1405";

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; header("Location: admin.php"); exit; }
}

function callCloud($method, $bin, $key, $data = null) {
    // إضافة تكتيك منع الكاش عبر ?v=time
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest?v=" . time() : "");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $headers = [
        "X-Master-Key: " . $key,
        "X-Bin-Meta: false" // للحصول على البيانات مباشرة بدون تعقيد
    ];

    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        $headers[] = "X-Bin-Versioning: false"; // السطر السحري: استبدل النسخة فوراً
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

if(isset($_SESSION['ok'])){
    // جلب البيانات مع التأكد من هيكل JSONBin v3
    $record = callCloud("GET", $BIN_ID, $API_KEY);
    
    // تصحيح: JSONBin v3 قد يعيد البيانات مباشرة أو داخل ['record']
    $data = isset($record['record']) ? $record['record'] : $record;
    
    $channels = $data['custom_channels'] ?? [];
    $sections = $data['sections'] ?? [];
    $news_ticker = $data['news_ticker'] ?? ['text' => '', 'status' => 'hide'];
    $notification = $data['notification'] ?? ['id' => '', 'msg' => ''];

    function sync($bin, $key, $chans, $secs, $news, $notif) {
        $payload = [
            'custom_channels' => array_values($chans),
            'sections' => array_values($secs),
            'news_ticker' => $news,
            'notification' => $notif
        ];
        return callCloud("PUT", $bin, $key, $payload);
    }

    // إدارة الشريط
    if(isset($_POST['update_ticker'])){
        $news_ticker = ['text' => $_POST['ticker_text'], 'status' => $_POST['ticker_status']];
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // إدارة الأقسام
    if(isset($_POST['save_sec'])){
        $id = $_POST['sec_id'] ?: uniqid();
        $new_sec = ['id'=>$id, 'name'=>$_POST['sec_name'], 'key'=>$_POST['sec_key'], 'img'=>$_POST['sec_img'], 'status'=>$_POST['sec_status']];
        foreach($sections as &$s){ if($s['id'] == $id){ $s = $new_sec; $found = true; break; } }
        if(!isset($found)) $sections[] = $new_sec;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#sections_area"); exit;
    }

    // إدارة القنوات
    if(isset($_POST['save_ch'])){
        $id = $_POST['edit_id'] ?: uniqid();
        $new_ch = ['id'=>$id, 'name'=>$_POST['n'], 'file'=>$_POST['f'], 'section'=>$_POST['s']];
        foreach($channels as &$c){ if($c['id'] == $id){ $c = $new_ch; $found = true; break; } }
        if(!isset($found)) $channels[] = $new_ch;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#channels_area"); exit;
    }

    if(isset($_GET['del'])){
        $channels = array_values(array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; }));
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    $edit_ch = null; if(isset($_GET['edit'])){ foreach($channels as $c){ if($c['id']==$_GET['edit']) $edit_ch=$c; } }
    $edit_sec = null; if(isset($_GET['edit_sec'])){ foreach($sections as $s){ if($s['id']==$_GET['edit_sec']) $edit_sec=$s; } }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"><title>لوحة التحكم | عثمان</title>
    <style>
        body { background: #050c14; color: white; font-family: sans-serif; padding: 20px; }
        .box { max-width: 550px; margin: auto; background: #0f172a; padding: 20px; border-radius: 12px; border: 1px solid #1e293b; margin-bottom: 20px; }
        input, select, textarea, button { width: 100%; padding: 12px; margin: 8px 0; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: white; box-sizing: border-box; }
        button { background: #e11d48; border: none; font-weight: bold; cursor: pointer; }
        .success { color: #22c55e; text-align: center; font-weight: bold; }
        .item { background: #1e293b; padding: 10px; margin: 5px 0; display: flex; justify-content: space-between; border-radius: 5px; }
    </style>
</head>
<body>

<?php if(!isset($_SESSION['ok'])): ?>
    <div class="box">
        <h2>🔐 دخول الإدارة</h2>
        <form method="POST"><input name="u" placeholder="اليوزر"><input type="password" name="p" placeholder="الباسورد"><button name="login">دخول</button></form>
    </div>
<?php else: ?>

    <?php if(isset($_GET['success'])) echo '<p class="success">✅ تم الحفظ في السحابة بنجاح</p>'; ?>

    <div class="box">
        <h3>📰 الشريط الإخباري</h3>
        <form method="POST">
            <textarea name="ticker_text"><?= $news_ticker['text'] ?></textarea>
            <select name="ticker_status">
                <option value="show" <?= $news_ticker['status']=='show'?'selected':'' ?>>إظهار الشريط</option>
                <option value="hide" <?= $news_ticker['status']=='hide'?'selected':'' ?>>إخفاء الشريط</option>
            </select>
            <button name="update_ticker">حفظ التغيير</button>
        </form>
    </div>

    <div class="box" id="sections_area">
        <h3>📂 إدارة الأقسام</h3>
        <form method="POST">
            <input type="hidden" name="sec_id" value="<?= $edit_sec['id'] ?? '' ?>">
            <input name="sec_name" placeholder="اسم القسم" value="<?= $edit_sec['name'] ?? '' ?>" required>
            <input name="sec_key" placeholder="الكود" value="<?= $edit_sec['key'] ?? '' ?>" required>
            <input name="sec_img" placeholder="الصورة" value="<?= $edit_sec['img'] ?? '' ?>" required>
            <select name="sec_status"><option value="show" <?= (isset($edit_sec) && $edit_sec['status']=='show')?'selected':'' ?>>إظهار</option><option value="hide" <?= (isset($edit_sec) && $edit_sec['status']=='hide')?'selected':'' ?>>إخفاء</option></select>
            <button name="save_sec">حفظ القسم</button>
        </form>
        <?php foreach($sections as $s): ?>
            <div class="item"><span><?= $s['name'] ?></span><a href="?edit_sec=<?= $s['id'] ?>#sections_area" style="color:#0ea5e9">تعديل</a></div>
        <?php endforeach; ?>
    </div>

    <div class="box" id="channels_area">
        <h3>📺 إدارة القنوات</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= $edit_ch['id'] ?? '' ?>">
            <input name="n" placeholder="اسم القناة" value="<?= $edit_ch['name'] ?? '' ?>" required>
            <input name="f" placeholder="الملف" value="<?= $edit_ch['file'] ?? '' ?>" required>
            <select name="s" required>
                <option value="">-- اختر القسم --</option>
                <?php foreach($sections as $s): ?>
                    <option value="<?= $s['key'] ?>" <?= (isset($edit_ch) && $edit_ch['section']==$s['key'])?'selected':'' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button name="save_ch">حفظ القناة</button>
        </form>
        <?php foreach(array_reverse($channels) as $c): ?>
            <div class="item"><span><?= $c['name'] ?></span><div><a href="?edit=<?= $c['id'] ?>#channels_area" style="color:#0ea5e9">تعديل</a> | <a href="?del=<?= $c['id'] ?>" style="color:#e11d48">حذف</a></div></div>
        <?php endforeach; ?>
    </div>
    
    <center><a href="?out=1" style="color:#94a3b8">خروج</a></center>
<?php endif; ?>
</body>
</html>
