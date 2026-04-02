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
    $channels = isset($res['record']['custom_channels']) ? $res['record']['custom_channels'] : [];

    // ميزة الحفظ (إضافة أو تعديل)
    if(isset($_POST['save_ch'])){
        $target_id = $_POST['edit_id'];
        $ch_data = [
            'id' => (!empty($target_id)) ? $target_id : uniqid(),
            'name' => $_POST['n'],
            'file' => $_POST['f'],
            'section' => $_POST['s']
        ];

        if(!empty($target_id)){
            // وضع التعديل
            foreach($channels as &$c){
                if($c['id'] == $target_id){ $c = $ch_data; break; }
            }
        } else {
            // وضع الإضافة
            $channels[] = $ch_data;
        }

        callCloud("PUT", $BIN_ID, $API_KEY, ['custom_channels'=>array_values($channels)]);
        header("Location: admin.php"); exit;
    }

    // ميزة الحذف
    if(isset($_GET['del'])){
        $channels = array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; });
        callCloud("PUT", $BIN_ID, $API_KEY, ['custom_channels'=>array_values($channels)]);
        header("Location: admin.php"); exit;
    }

    // جلب بيانات القناة المراد تعديلها
    $edit_ch = null;
    if(isset($_GET['edit'])){
        foreach($channels as $c){
            if($c['id'] == $_GET['edit']){ $edit_ch = $c; break; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #050c14; color: white; font-family: 'Tajawal', sans-serif; margin: 0; padding: 20px; }
        .box { max-width: 600px; margin: auto; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); }
        h2 { color: #e11d48; font-size: 1.2rem; }
        input, select, button { 
            width: 100%; padding: 12px; margin: 8px 0; border-radius: 8px; 
            border: 1px solid #333; background: #111; color: white; box-sizing: border-box; 
        }
        button { background: #e11d48; border: none; font-weight: bold; cursor: pointer; }
        .ch-card { 
            background: rgba(255,255,255,0.03); margin: 10px 0; padding: 15px; 
            border-radius: 10px; border-right: 4px solid #e11d48; text-align: right;
            display: flex; justify-content: space-between; align-items: center;
        }
        .ch-info span { display: block; font-size: 12px; color: #aaa; }
        .actions { display: flex; gap: 10px; }
        .del-link { color: #ff4d4d; text-decoration: none; font-size: 13px; font-weight: bold; }
        .edit-link { color: #0ea5e9; text-decoration: none; font-size: 13px; font-weight: bold; }
        .logout { display: block; margin-top: 30px; color: #888; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<?php if(!isset($_SESSION['ok'])): ?>
    <div class="box" style="margin-top: 50px;">
        <h2>🔐 دخول الإدارة</h2>
        <form method="POST">
            <input name="u" placeholder="اسم المستخدم" required>
            <input type="password" name="p" placeholder="كلمة المرور" required>
            <button name="login">دخول</button>
        </form>
    </div>
<?php else: ?>
    <div class="box">
        <h2><?php echo $edit_ch ? "✏️ تعديل قناة" : "➕ إضافة قناة سحابية"; ?></h2>
        <form method="POST">
            <input type="hidden" name="edit_id" value="<?php echo $edit_ch ? $edit_ch['id'] : ''; ?>">
            <input name="n" placeholder="اسم القناة" value="<?php echo $edit_ch ? $edit_ch['name'] : ''; ?>" required>
            <input name="f" placeholder="اسم الملف" value="<?php echo $edit_ch ? $edit_ch['file'] : ''; ?>" required>
            <select name="s">
                <?php 
                $sections = [
                    'bein'=>'beIN Sport', 'shahad'=>'شاهد الرياضية', 'mbc'=>'باقة MBC', 
                    'alkas'=>'باقة الكاس', 'on'=>'On Sport', 'ado'=>'أبوظبي الرياضية', 
                    'dubai'=>'دبي الرياضية', 'kuwait'=>'الكويت الرياضية', 'sporttv'=>'Sport TV',
                    'sky'=>'Sky Sport', 'plus'=>'Canal+', 'star'=>'STARZPLAY', 'moc'=>'الباقة المغربية'
                ];
                foreach($sections as $val => $name){
                    $sel = ($edit_ch && $edit_ch['section'] == $val) ? "selected" : "";
                    echo "<option value='$val' $sel>$name</option>";
                }
                ?>
            </select>
            <button name="save_ch"><?php echo $edit_ch ? "تحديث البيانات" : "حفظ القناة"; ?></button>
            <?php if($edit_ch): ?><a href="admin.php" style="display:block; text-align:center; color:#aaa; font-size:12px; margin-top:5px; text-decoration:none;">إلغاء التعديل</a><?php endif; ?>
        </form>

        <hr style="border:0; border-top:1px solid #333; margin:20px 0;">

        <h2>📺 القنوات المضافة (<?php echo count($channels); ?>)</h2>
        <?php foreach(array_reverse($channels) as $c): ?>
            <div class="ch-card">
                <div class="ch-info">
                    <strong><?php echo $c['name']; ?></strong>
                    <span>القسم: <?php echo strtoupper($c['section']); ?> | الملف: <?php echo $c['file']; ?></span>
                </div>
                <div class="actions">
                    <a href="?edit=<?php echo $c['id']; ?>" class="edit-link">تعديل</a>
                    <a href="?del=<?php echo $c['id']; ?>" class="del-link" onclick="return confirm('حذف القناة؟')">حذف</a>
                </div>
            </div>
        <?php endforeach; ?>

        <a href="?out=1" class="logout">❌ تسجيل الخروج</a>
        <br>
        <a href="index.php" style="color:#0ea5e9; text-decoration:none; font-size:13px;">← العودة للموقع</a>
    </div>
<?php endif; ?>

</body>
</html>
