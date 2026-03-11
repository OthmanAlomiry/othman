<?php
session_start();
$password = "123456"; // غير الرمز السري هنا
$file = 'matches.json';

if (isset($_POST['login'])) {
    if ($_POST['pass'] == $password) { $_SESSION['logged'] = true; }
}

if (!isset($_SESSION['logged'])) {
    exit('<form method="post" style="text-align:center;margin-top:100px;">
          <h2>دخول الإدارة</h2>
          <input type="password" name="pass" placeholder="الرمز السري">
          <input type="submit" name="login" value="دخول">
          </form>');
}

if (isset($_POST['save'])) {
    file_put_contents($file, json_encode($_POST['m']));
    echo "<p style='color:green; text-align:center;'>✅ تم تحديث الجدول بنجاح!</p>";
}

$data = json_decode(file_get_contents($file), true);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - إدارة المباريات</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .box { background: white; max-width: 600px; margin: auto; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .row { display: flex; gap: 10px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        input { padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { width: 100%; padding: 15px; background: #22c55e; color: white; border: none; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="text-align:center;">إدارة مباريات اليوم</h2>
        <form method="post">
            <?php for($i=1; $i<=9; $i++): ?>
            <div class="row">
                <strong>قناة <?php echo $i; ?>:</strong>
                <input type="text" name="m[<?php echo $i; ?>][h]" value="<?php echo $data[$i]['h']; ?>" placeholder="الفريق 1">
                <input type="text" name="m[<?php echo $i; ?>][a]" value="<?php echo $data[$i]['a']; ?>" placeholder="الفريق 2">
                <input type="text" name="m[<?php echo $i; ?>][t]" value="<?php echo $data[$i]['t']; ?>" placeholder="الوقت" style="width:60px;">
            </div>
            <?php endfor; ?>
            <button type="submit" name="save" class="btn">حفظ وتحديث الموقع فوراً</button>
        </form>
    </div>
</body>
</html>
