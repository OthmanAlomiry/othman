<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- بيانات السحابة ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';
$user_admin = "othman"; 
$pass_admin = "1405";

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; header("Location: admin.php"); exit; }
}

function callCloud($method, $bin, $key, $data = null) {
    // استخدمنا الإصدار v3 مع تعطيل الكاش تماماً
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest?v=" . time() : "");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $headers = [
        "X-Master-Key: " . $key,
        "X-Bin-Meta: false"
    ];

    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        $headers[] = "X-Bin-Versioning: false"; 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return json_decode($res, true);
}

if(isset($_SESSION['ok'])){
    // جلب البيانات مع التأكد من عدم وجود كاش
    $record = callCloud("GET", $BIN_ID, $API_KEY);
    
    $channels = $record['custom_channels'] ?? [];
    $sections = $record['sections'] ?? [];
    $news_ticker = $record['news_ticker'] ?? ['text' => '', 'status' => 'show'];
    $notification = $record['notification'] ?? ['msg' => ''];

    // وظيفة المزامنة
    function sync($bin, $key, $chans, $secs, $news, $notif) {
        $data = [
            'custom_channels' => array_values($chans),
            'sections' => array_values($secs),
            'news_ticker' => $news,
            'notification' => $notif
        ];
        return callCloud("PUT", $bin, $key, $data);
    }

    // --- تحديث الشريط الإخباري ---
    if(isset($_POST['update_ticker'])){
        // تأكد من أخذ القيم الجديدة من الفورم مباشرة
        $new_ticker = [
            'text' => $_POST['ticker_text'],
            'status' => $_POST['ticker_status']
        ];
        $res = sync($BIN_ID, $API_KEY, $channels, $sections, $new_ticker, $notification);
        
        // إذا نجح التحديث، ننتظر ثانية واحدة ثم نوجه الصفحة لضمان استقرار السحابة
        if($res) {
            header("Location: admin.php?success=1"); 
            exit;
        }
    }

    // بقية العمليات (الأقسام والقنوات) تبقى كما هي مع التأكد من استخدام sync
    if(isset($_POST['save_sec'])){
        $id = $_POST['sec_id'] ?: uniqid();
        $new_sec = ['id'=>$id, 'name'=>$_POST['sec_name'], 'key'=>$_POST['sec_key'], 'img'=>$_POST['sec_img'], 'status'=>$_POST['sec_status']];
        foreach($sections as &$s){ if($s['id'] == $id){ $s = $new_sec; $found = true; break; } }
        if(!isset($found)) $sections[] = $new_sec;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #050c14; color: white; font-family: 'Tajawal', sans-serif; padding: 20px; }
        .box { max-width: 600px; margin: auto; background: #0f172a; padding: 20px; border-radius: 12px; border: 1px solid #1e293b; }
        select, textarea, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; background: #1e293b; color: white; border: 1px solid #334155; }
        button { background: #e11d48; cursor: pointer; font-weight: bold; border: none; }
        .success { color: #22c55e; text-align: center; background: rgba(34, 197, 94, 0.1); padding: 10px; border-radius: 8px; }
    </style>
</head>
<body>

<?php if(!isset($_SESSION['ok'])): ?>
    <?php else: ?>

    <?php if(isset($_GET['success'])): ?>
        <p class="success">✅ تم تحديث السحابة بنجاح</p>
    <?php endif; ?>

    <div class="box">
        <h3>📰 الشريط الإخباري</h3>
        <form method="POST">
            <textarea name="ticker_text"><?= $news_ticker['text'] ?></textarea>
            <select name="ticker_status">
                <option value="show" <?= ($news_ticker['status'] == 'show') ? 'selected' : '' ?>>إظهار الشريط</option>
                <option value="hide" <?= ($news_ticker['status'] == 'hide') ? 'selected' : '' ?>>إخفاء الشريط</option>
            </select>
            <button name="update_ticker">تحديث الحالة</button>
        </form>
    </div>

<?php endif; ?>
</body>
</html>
