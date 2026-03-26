<?php
session_start();

// --- إعدادات JSONbin السحابية الخاصة بك عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';

// --- إعدادات حماية الدخول ---
$admin_user = "othman";
$admin_pass = "1405";

// خروج
if (isset($_GET['logout'])) { session_destroy(); header("Location: admin.php"); exit; }

// دخول
if (isset($_POST['login'])) {
    if ($_POST['user'] == $admin_user && $_POST['pass'] == $admin_pass) {
        $_SESSION['logged_in'] = true;
    } else {
        $login_error = "بيانات الدخول غير صحيحة!";
    }
}

// دالة جلب البيانات من السحابة
function fetchFromCloud($bin, $key) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin . "/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Master-Key: " . $key, "X-Bin-Meta: false"]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// دالة حفظ البيانات للسحابة
function saveToCloud($bin, $key, $data) {
    $ch = curl_init("https://api.jsonbin.io/v3/b/" . $bin);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "X-Master-Key: " . $key]);
    curl_exec($ch);
    curl_close($ch);
}

// العمليات (فقط إذا كان مسجلاً للدخول)
if (isset($_SESSION['logged_in'])) {
    $data = fetchFromCloud($BIN_ID, $API_KEY);
    $channels = isset($data['custom_channels']) ? $data['custom_channels'] : [];

    // إضافة قناة
    if (isset($_POST['add_channel'])) {
        $channels[] = [
            'id' => 'ch_' . uniqid(),
            'name' => $_POST['ch_name'],
            'file' => $_POST['ch_file'],
            'section' => $_POST['ch_section'],
            'status' => 'show'
        ];
        saveToCloud($BIN_ID, $API_KEY, ['custom_channels' => array_values($channels)]);
        $msg = "تم حفظ القناة في السحابة بنجاح!";
    }

    // حذف قناة
    if (isset($_GET['delete'])) {
        $channels = array_filter($channels, function($c) { return $c['id'] !== $_GET['delete']; });
        saveToCloud($BIN_ID, $API_KEY, ['custom_channels' => array_values($channels)]);
        header("Location: admin.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم السحابية - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #050c14; color: white; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); }
        .login-box { max-width: 400px; margin: 100px auto; background: rgba(255,255,255,0.05); padding: 40px; border-radius: 20px; border: 1px solid #e11d48; text-align: center; }
        input, select, button { padding: 12px; margin: 5px; border-radius: 8px; border: 1px solid #333; background: #111; color: white; width: 100%; box-sizing: border-box; }
        .form-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
        .form-row input, .form-row select { width: 200px; }
        button { background: #e11d48; cursor: pointer; border: none; font-weight: bold; width: auto; padding: 12px 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; background: rgba(0,0,0,0.2); }
        th, td { padding: 15px; border-bottom: 1px solid #222; text-align: center; }
        th { color: #e11d48; background: rgba(225, 29, 72, 0.1); }
        .logout { float: left; color: #888; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['logged_in'])): ?>
    <div class="login-box">
        <h2>تسجيل دخول الإدارة</h2>
        <?php if(isset($login_error)) echo "<p style='color:red'>$login_error</p>"; ?>
        <form method="POST">
            <input type="text" name="user" placeholder="اسم المستخدم" required>
            <input type="password" name="pass" placeholder="كلمة المرور" required>
            <button type="submit" name="login">دخول</button>
        </form>
    </div>
<?php else: ?>
    <div class="container">
        <a href="?logout=1" class="logout">❌ تسجيل الخروج</a>
        <h2>➕ إضافة قناة للسحابة</h2>
        <?php if(isset($msg)) echo "<p style='color:#22c55e; font-weight:bold;'>$msg</p>"; ?>
        
        <form method="POST" class="form-row">
            <input type="text" name="ch_name" placeholder="اسم القناة" required>
            <input type="text" name="ch_file" placeholder="الملف (مثال: b1.php)" required>
            <select name="ch_section">
                <option value="bein">beIN Sport</option>
                <option value="shahad">شاهد</option>
                <option value="mbc">باقة MBC</option>
                <option value="alkas">باقة الكاس</option>
                <option value="on">On Sport</option>
                <option value="ado">أبوظبي</option>
                <option value="dubai">دبي</option>
                <option value="kuwait">الكويت</option>
                <option value="star">STARZPLAY</option>
                <option value="moc">المغربية</option>
                <option value="sky">Sky Sport</option>
                <option value="plus">Canal+</option>
            </select>
            <button type="submit" name="add_channel">حفظ القناة</button>
        </form>

        <hr style="border: 0; border-top: 1px solid #333; margin: 30px 0;">

        <h2>📺 القنوات الحالية في السحابة</h2>
        <table>
            <thead>
                <tr>
                    <th>القناة</th>
                    <th>القسم</th>
                    <th>الملف</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($channels as $ch): ?>
                <tr>
                    <td><?php echo $ch['name']; ?></td>
                    <td><?php echo strtoupper($ch['section']); ?></td>
                    <td><code><?php echo $ch['file']; ?></code></td>
                    <td><a href="?delete=<?php echo $ch['id']; ?>" onclick="return confirm('هل أنت متأكد من الحذف؟')" style="color:#ef4444; text-decoration:none; border:1px solid #ef4444; padding:5px 10px; border-radius:5px;">حذف</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($channels)) echo "<tr><td colspan='4'>لا توجد قنوات مخزنة حالياً.</td></tr>"; ?>
            </tbody>
        </table>
        <br>
        <div style="text-align:center;">
            <a href="index.php" style="color:#0ea5e9; text-decoration:none;">← العودة للموقع الرئيسي</a>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
