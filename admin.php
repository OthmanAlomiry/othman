<?php
session_start();
error_reporting(0);

$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

$user = "othman"; $pass = "1405";

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['in'])){
    if($_POST['u']==$user && $_POST['p']==$pass){ $_SESSION['ok']=true; }
}

function callCloud($m, $bin, $key, $data = null){
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($m=="GET"?"/latest":"");
    $h = ["X-Master-Key: " . $key];
    if($m=="PUT") $h[] = "Content-Type: application/json";
    
    $opts = ["http" => ["method" => $m, "header" => implode("\r\n", $h)]];
    if($data) $opts["http"]["content"] = json_encode($data);
    
    return json_decode(file_get_contents($url, false, stream_context_create($opts)), true);
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
<head><meta charset="UTF-8"><title>Admin</title><style>body{background:#050c14;color:white;font-family:sans-serif;text-align:center;} input,select,button{padding:10px;margin:5px;border-radius:5px;}</style></head>
<body>
<?php if(!isset($_SESSION['ok'])): ?>
    <form method="POST" style="margin-top:100px;">
        <input name="u" placeholder="User"><br><input type="password" name="p" placeholder="Pass"><br><button name="in">Login</button>
    </form>
<?php else: ?>
    <h2>إضافة قناة</h2>
    <form method="POST">
        <input name="n" placeholder="الاسم" required>
        <input name="f" placeholder="الملف (b1.php)" required>
        <select name="s"><option value="bein">beIN</option><option value="mbc">MBC</option><option value="alkas">الكاس</option><option value="shahad">شاهد</option></select>
        <button name="add">إضافة</button>
    </form>
    <table border="1" style="margin:20px auto; width:80%;">
        <?php foreach($channels as $c): ?>
        <tr><td><?php echo $c['name']; ?></td><td><a href="?del=<?php echo $c['id']; ?>" style="color:red;">حذف</a></td></tr>
        <?php endforeach; ?>
    </table>
    <a href="?out=1">خروج</a>
<?php endif; ?>
</body>
</html>
