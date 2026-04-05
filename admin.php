<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- بيانات السحابة ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$user_admin = "othman"; $pass_admin = "1405";

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; }
}

function callCloud($method, $bin, $key, $data = null) {
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest" : "");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = ["X-Master-Key: " . $key];
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

if(isset($_SESSION['ok'])){
    $res = callCloud("GET", $BIN_ID, $API_KEY);
    $record = isset($res['record']) ? $res['record'] : [];
    $channels = isset($record['custom_channels']) ? $record['custom_channels'] : [];
    $sections = isset($record['sections']) ? $record['sections'] : [];
    $news_ticker = isset($record['news_ticker']) ? $record['news_ticker'] : ['text' => 'مرحباً بكم', 'status' => 'hide'];

    // --- تحديث السحابة الشامل ---
    function sync($bin, $key, $channels, $sections, $news) {
        callCloud("PUT", $bin, $key, [
            'custom_channels' => array_values($channels),
            'sections' => array_values($sections),
            'news_ticker' => $news
        ]);
    }

    // --- 1. إدارة الشريط الإخباري ---
    if(isset($_POST['update_ticker'])){
        $news_ticker = ['text' => $_POST['ticker_text'], 'status' => $_POST['ticker_status']];
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker);
        header("Location: admin.php"); exit;
    }

    // --- 2. إدارة الأقسام (حفظ / تعديل) ---
    if(isset($_POST['save_sec'])){
        $sec_id = $_POST['sec_id'] ?: uniqid();
        $new_sec = [
            'id' => $sec_id,
            'name' => $_POST['sec_name'],
            'key' => $_POST['sec_key'],
            'img' => $_POST['sec_img'],
            'status' => $_POST['sec_status']
        ];
        $found = false;
        foreach($sections as &$s){ if($s['id'] == $sec_id){ $s = $new_sec; $found = true; break; } }
        if(!$found) $sections[] = $new_sec;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker);
        header("Location: admin.php#sections_area"); exit;
    }

    if(isset($_GET['del_sec'])){
        $sections = array_filter($sections, function($s){ return $s['id'] !== $_GET['del_sec']; });
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker);
        header("Location: admin.php#sections_area"); exit;
    }

    // --- 3. إدارة القنوات (حفظ / تعديل) ---
    if(isset($_POST['save_ch'])){
        $target_id = $_POST['edit_id'];
        $ch_data = ['id' => $target_id ?: uniqid(), 'name' => $_POST['n'], 'file' => $_POST['f'], 'section' => $_POST['s']];
        if(!empty($target_id)){
            foreach($channels as &$c){ if($c['id'] == $target_id){ $c = $ch_data; break; } }
        } else { $channels[] = $ch_data; }
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker);
        header("Location: admin.php#channels_area"); exit;
    }

    if(isset($_GET['del'])){
        $channels = array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; });
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker);
        header("Location: admin.php#channels_area"); exit;
    }

    // جلب بيانات التعديل
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
        .info b { display: block; color: #fff; } .info span { font-size: 11px; color: #aaa; }
        .btns a { text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 10px; }
        .del { color: #ff4d4d; } .edit { color: #0ea5e9; }
        .badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; background: #333; margin-right: 5px; }
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
        <h2>📰 الشريط الإخباري</h2>
        <form method="POST">
            <textarea name="ticker_text" placeholder="اكتب نص الشريط هنا..." required><?= $news_ticker['text'] ?></textarea>
            <select name="ticker_status">
                <option value="show" <?= ($news_ticker['status']=='show')?'selected':'' ?>>إظهار الشريط</option>
                <option value="hide" <?= ($news_ticker['status']=='hide')?'selected':'' ?>>إخفاء الشريط</option>
            </select>
            <button name="update_ticker" style="background:#22c55e">تحديث الشريط</button>
        </form>
    </div>

    <div class="box" id="sections_area">
        <h2>📂 إدارة الأقسام (Icons)</h2>
        <form method="POST">
            <input type="hidden" name="sec_id" value="<?= $edit_sec ? $edit_sec['id'] : '' ?>">
            <input name="sec_name" placeholder="اسم القسم" value="<?= $edit_sec ? $edit_sec['name'] : '' ?>" required>
            <input name="sec_key" placeholder="الكود (ssc, bein...)" value="<?= $edit_sec ? $edit_sec['key'] : '' ?>" required>
            <input name="sec_img" placeholder="مسار الصورة (mg/icon.png)" value="<?= $edit_sec ? $edit_sec['img'] : '' ?>" required>
            <select name="sec_status">
                <option value="show" <?= ($edit_sec && $edit_sec['status']=='show')?'selected':'' ?>>إظهار في الموقع</option>
                <option value="hide" <?= ($edit_sec && $edit_sec['status']=='hide')?'selected':'' ?>>إخفاء من الموقع</option>
            </select>
            <button name="save_sec" style="background:#0ea5e9"><?= $edit_sec ? 'تحديث القسم' : 'إضافة قسم جديد' ?></button>
            <?php if($edit_sec): ?><a href="admin.php" style="color:#aaa; font-size:11px; display:block; text-align:center;">إلغاء التعديل</a><?php endif; ?>
        </form>
        <?php foreach($sections as $s): ?>
            <div class="item-card" style="border-color: <?= $s['status']=='show'?'#22c55e':'#555' ?>">
                <div class="info"><b><?= $s['name'] ?> <small class="badge"><?= $s['key'] ?></small></b><span><?= $s['img'] ?></span></div>
                <div class="btns"><a href="?edit_sec=<?= $s['id'] ?>#sections_area" class="edit">تعديل</a><a href="?del_sec=<?= $s['id'] ?>" class="del" onclick="return confirm('حذف القسم؟')">حذف</a></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="box" id="channels_area">
        <h2>📺 إدارة القنوات</h2>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?= $edit_ch ? $edit_ch['id'] : '' ?>">
            <input name="n" placeholder="اسم القناة" value="<?= $edit_ch ? $edit_ch['name'] : '' ?>" required>
            <input name="f" placeholder="الملف (b1.php)" value="<?= $edit_ch ? $edit_ch['file'] : '' ?>" required>
            <select name="s" required>
                <option value="">-- اختر القسم --</option>
                <?php foreach($sections as $s): ?>
                    <option value="<?= $s['key'] ?>" <?= ($edit_ch && $edit_ch['section']==$s['key'])?'selected':'' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button name="save_ch"><?= $edit_ch ? 'تحديث القناة' : 'إضافة القناة' ?></button>
            <?php if($edit_ch): ?><a href="admin.php" style="color:#aaa; font-size:11px; display:block; text-align:center;">إلغاء التعديل</a><?php endif; ?>
        </form>
        <?php foreach(array_reverse($channels) as $c): ?>
            <div class="item-card">
                <div class="info"><b><?= $c['name'] ?></b><span>القسم: <?= strtoupper($c['section']) ?></span></div>
                <div class="btns"><a href="?edit=<?= $c['id'] ?>#channels_area" class="edit">تعديل</a><a href="?del=<?= $c['id'] ?>" class="del" onclick="return confirm('حذف القناة؟')">حذف</a></div>
            </div>
        <?php endforeach; ?>
        <center style="margin-top:20px;">
            <a href="index.php" style="color:#aaa; text-decoration:none; font-size:12px;">← الموقع</a> | 
            <a href="?out=1" style="color:#ff4d4d; text-decoration:none; font-size:12px;">خروج</a>
        </center>
    </div>
<?php endif; ?>
</body>
</html>
