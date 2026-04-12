<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- بيانات السحابة (JSONBin.io) ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';
$user_admin = "othman"; 
$pass_admin = "1405";

// --- تسجيل الخروج ---
if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }

// --- تسجيل الدخول ---
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ 
        $_SESSION['ok'] = true; 
        header("Location: admin.php"); exit;
    }
}

// --- وظيفة الاتصال بالسحابة ---
function callCloud($method, $bin, $key, $data = null) {
    // إضافة متغير زمني لمنع الكاش عند الجلب
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest?v=" . time() : "");
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $headers = [
        "X-Master-Key: " . $key,
        "X-Bin-Meta: false"
    ];

    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        $headers[] = "X-Bin-Versioning: false"; // لتحديث نفس النسخة دائماً
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode == 200) ? json_decode($res, true) : null;
}

// --- العمليات (فقط إذا كان الدخول صحيحاً) ---
if(isset($_SESSION['ok'])){
    // جلب البيانات الحالية
    $record = callCloud("GET", $BIN_ID, $API_KEY);
    
    $channels = $record['custom_channels'] ?? [];
    $sections = $record['sections'] ?? [];
    $news_ticker = $record['news_ticker'] ?? ['text' => '', 'status' => 'hide'];
    $notification = $record['notification'] ?? ['id' => '', 'msg' => ''];

    // وظيفة الحفظ الموحدة
    function sync($bin, $key, $chans, $secs, $news, $notif) {
        $data = [
            'custom_channels' => array_values($chans),
            'sections' => array_values($secs),
            'news_ticker' => $news,
            'notification' => $notif
        ];
        return callCloud("PUT", $bin, $key, $data);
    }

    // تحديث الشريط الإخباري
    if(isset($_POST['update_ticker'])){
        $news_ticker = ['text' => $_POST['ticker_text'], 'status' => $_POST['ticker_status']];
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // حفظ القسم
    if(isset($_POST['save_sec'])){
        $id = $_POST['sec_id'] ?: uniqid();
        $new_sec = ['id'=>$id, 'name'=>$_POST['sec_name'], 'key'=>$_POST['sec_key'], 'img'=>$_POST['sec_img'], 'status'=>$_POST['sec_status']];
        $found = false;
        foreach($sections as &$s){ if($s['id'] == $id){ $s = $new_sec; $found = true; break; } }
        if(!$found) $sections[] = $new_sec;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#sections_area"); exit;
    }

    // حذف القسم
    if(isset($_GET['del_sec'])){
        $sections = array_values(array_filter($sections, function($s){ return $s['id'] !== $_GET['del_sec']; }));
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#sections_area"); exit;
    }

    // حفظ القناة
    if(isset($_POST['save_ch'])){
        $id = $_POST['edit_id'] ?: uniqid();
        $new_ch = ['id'=>$id, 'name'=>$_POST['n'], 'file'=>$_POST['f'], 'file_backup'=>$_POST['f_backup'], 'section'=>$_POST['s']];
        $found = false;
        foreach($channels as &$c){ if($c['id'] == $id){ $c = $new_ch; $found = true; break; } }
        if(!$found) $channels[] = $new_ch;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#channels_area"); exit;
    }

    // حذف القناة
    if(isset($_GET['del'])){
        $channels = array_values(array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; }));
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#channels_area"); exit;
    }

    // لجلب بيانات التعديل
    $edit_ch = null; if(isset($_GET['edit'])){ foreach($channels as $c){ if($c['id']==$_GET['edit']) $edit_ch=$c; } }
    $edit_sec = null; if(isset($_GET['edit_sec'])){ foreach($sections as $s){ if($s['id']==$_GET['edit_sec']) $edit_sec=$s; } }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم عثمان المطورة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #050c14; color: white; font-family: 'Tajawal', sans-serif; margin: 0; padding: 15px; }
        .box { max-width: 600px; margin: 15px auto; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); }
        h2, h3 { color: #e11d48; margin-top: 0; }
        input, select, button, textarea { width: 100%; padding: 12px; margin: 8px 0; border-radius: 8px; border: 1px solid #333; background: #111; color: white; box-sizing: border-box; }
        button { background: #e11d48; border: none; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #f43f5e; }
        .item-card { background: rgba(255,255,255,0.03); margin: 8px 0; padding: 12px; border-radius: 10px; border-right: 3px solid #e11d48; display: flex; justify-content: space-between; align-items: center; }
        .edit { color: #0ea5e9; text-decoration: none; font-weight: bold; margin-left: 10px; }
        .del { color: #ff4d4d; text-decoration: none; font-weight: bold; }
        .success { background: #22c55e; color: white; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>

<?php if(!isset($_SESSION['ok'])): ?>
    <div class="box" style="margin-top: 100px; text-align: center;">
        <h2>🔐 دخول الإدارة</h2>
        <form method="POST">
            <input name="u" placeholder="اليوزر">
            <input type="password" name="p" placeholder="الباسورد">
            <button name="login">دخول</button>
        </form>
    </div>
<?php else: ?>

    <?php if(isset($_GET['success'])): ?>
        <div class="box success">✅ تم تحديث البيانات بنجاح في السحابة</div>
    <?php endif; ?>

    <div class="box">
        <h3>📰 الشريط الإخباري</h3>
        <form method="POST">
            <textarea name="ticker_text" placeholder="نص الشريط..." required><?= $news_ticker['text'] ?></textarea>
            <select name="ticker_status">
                <option value="show" <?= $news_ticker['status']=='show'?'selected':'' ?>>إظهار الشريط</option>
                <option value="hide" <?= $news_ticker['status']=='hide'?'selected':'' ?>>إخفاء الشريط</option>
            </select>
            <button name="update_ticker" style="background:#22c55e">تحديث الشريط</button>
        </form>
    </div>

    <div class="box" id="sections_area">
        <h3>📂 إدارة الأقسام</h3>
        <form method="POST">
            <input type="hidden" name="sec_id" value="<?= $edit_sec['id'] ?? '' ?>">
            <input name="sec_name" placeholder="اسم القسم" value="<?= $edit_sec['name'] ?? '' ?>" required>
            <input name="sec_key" placeholder="الكود (مثل beIN)" value="<?= $edit_sec['key'] ?? '' ?>" required>
            <input name="sec_img" placeholder="رابط صورة القسم" value="<?= $edit_sec['img'] ?? '' ?>" required>
            <select name="sec_status">
                <option value="show" <?= (isset($edit_sec) && $edit_sec['status']=='show')?'selected':'' ?>>إظهار</option>
                <option value="hide" <?= (isset($edit_sec) && $edit_sec['status']=='hide')?'selected':'' ?>>إخفاء</option>
            </select>
            <button name="save_sec" style="background:#0ea5e9">حفظ القسم</button>
        </form>
        <?php foreach($sections as $s): ?>
            <div class="item-card">
                <span><b><?= $s['name'] ?></b> (<?= $s['key'] ?>)</span>
                <div>
                    <a href="?edit_sec=<?= $s['id'] ?>#sections_area" class="edit">تعديل</a>
                    <a href="?del_sec=<?= $s['id'] ?>" class="del" onclick="return confirm('حذف القسم؟')">حذف</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="box" id="channels_area">
        <h3>📺 إدارة القنوات</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= $edit_ch['id'] ?? '' ?>">
            <input name="n" placeholder="اسم القناة" value="<?= $edit_ch['name'] ?? '' ?>" required>
            <input name="f" placeholder="رابط البث الأساسي" value="<?= $edit_ch['file'] ?? '' ?>" required>
            <input name="f_backup" placeholder="رابط البث الاحتياطي (اختياري)" value="<?= $edit_ch['file_backup'] ?? '' ?>">
            <select name="s" required>
                <option value="">-- اختر القسم --</option>
                <?php foreach($sections as $s): ?>
                    <option value="<?= $s['key'] ?>" <?= (isset($edit_ch) && $edit_ch['section']==$s['key'])?'selected':'' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button name="save_ch">حفظ القناة</button>
        </form>

        <?php foreach(array_reverse($channels) as $c): ?>
            <div class="item-card">
                <span><?= $c['name'] ?></span>
                <div>
                    <a href="?edit=<?= $c['id'] ?>#channels_area" class="edit">تعديل</a>
                    <a href="?del=<?= $c['id'] ?>" class="del" onclick="return confirm('حذف القناة؟')">حذف</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p align="center">
        <a href="index.php" style="color:#aaa; text-decoration:none;">← العودة للموقع</a> | 
        <a href="?out=1" style="color:#ff4d4d; text-decoration:none;">خروج آمن</a>
    </p>

<?php endif; ?>

</body>
</html>
