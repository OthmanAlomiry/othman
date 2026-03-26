<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$user_admin = "admin"; $pass_admin = "123456";

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

    if(isset($_POST['add'])){
        $channels[] = ['id'=>uniqid(), 'name'=>$_POST['n'], 'file'=>$_POST['f'], 'section'=>$_POST['s']];
        callCloud("PUT", $BIN_ID, $API_KEY, ['custom_channels'=>array_values($channels)]);
        header("Location: admin.php"); exit;
    }
    if(isset($_GET['del'])){
        $channels = array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; });
        callCloud("PUT", $BIN_ID, $API_KEY, ['custom_channels'=>array_values($channels)]);
        header("Location: admin.php"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="UTF-8"><title>Admin Panel</title><style>body{background:#050c14;color:white;font-family:sans-serif;text-align:center;padding:50px;} input,select,button{padding:12px;margin:5px;border-radius:5px; border:1px solid #333; background:#111; color:white;}</style></head>
<body>
<?php if(!isset($_SESSION['ok'])): ?>
    <div style="border:1px solid #e11d48; display:inline-block; padding:30px; border-radius:15px;">
        <h2>دخول المشرف</h2>
        <form method="POST">
            <input name="u" placeholder="اسم المستخدم"><br>
            <input type="password" name="p" placeholder="كلمة المرور"><br>
            <button name="login" style="background:#e11d48; width:100%;">دخول</button>
        </form>
    </div>
<?php else: ?>
    <h2>إضافة قناة جديدة للسحابة</h2>
    <form method="POST">
        <input name="n" placeholder="اسم القناة" required>
        <input name="f" placeholder="الملف (b1.php)" required>
        <select name="s"><option value="bein">beIN</option><option value="mbc">MBC</option><option value="alkas">الكاس</option><option value="shahad">شاهد</option></select>
        <button name="add" style="background:#e11d48;">حفظ القناة</button>
    </form>
    <table border="1" style="margin:30px auto; width:80%; border-color:#333;">
        <tr><th>اسم القناة</th><th>القسم</th><th>حذف</th></tr>
        <?php foreach($channels as $c): ?>
        <tr><td><?php echo $c['name']; ?></td><td><?php echo strtoupper($c['section']); ?></td><td><a href="?del=<?php echo $c['id']; ?>" style="color:red;">حذف</a></td></tr>
        <?php endforeach; ?>
    </table>
    <a href="?out=1" style="color:#aaa;">خروج من الإدارة</a>
<?php endif; ?>
</body>
</html>
