<?php
session_start();

// --- إعدادات الحماية (تستطيع تغييرها من هنا) ---
$admin_user = "othman";
$admin_pass = "1405";

// تسجيل الخروج
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// التحقق من بيانات تسجيل الدخول
if (isset($_POST['login'])) {
    if ($_POST['user'] == $admin_user && $_POST['pass'] == $admin_pass) {
        $_SESSION['logged_in'] = true;
    } else {
        $login_error = "بيانات الدخول غير صحيحة!";
    }
}

// ملف قاعدة البيانات
$db_file = 'channels_db.json';

// --- مصفوفة القنوات الأساسية الافتراضية ---
$default_channels = [
    ['id' => 'b1', 'name' => 'beIN Sport 1', 'file' => 'b1.php', 'section' => 'bein', 'status' => 'show'],
    ['id' => 'mbe2', 'name' => 'MBC مصر 2', 'file' => 'mbe2.php', 'section' => 'mbc', 'status' => 'show'],
    ['id' => 'sh1', 'name' => 'SHOOF 1', 'file' => 'sh1.php', 'section' => 'alkas', 'status' => 'show'],
    ['id' => 'ku1', 'name' => 'الكويت الرياضية', 'file' => 'ku1.php', 'section' => 'kuwait', 'status' => 'show']
];

// إنشاء الملف إذا لم يوجد
if (!file_exists($db_file)) {
    $initial_data = ['custom_channels' => $default_channels];
    file_put_contents($db_file, json_encode($initial_data, JSON_UNESCAPED_UNICODE));
}

$channels_data = json_decode(file_get_contents($db_file), true);

// عمليات الإدارة (فقط إذا كان مسجلاً للدخول)
if (isset($_SESSION['logged_in'])) {
    // إضافة قناة
    if (isset($_POST['add_channel'])) {
        $new_ch = [
            'id' => 'ch_' . uniqid(),
            'name' => $_POST['ch_name'],
            'file' => $_POST['ch_file'],
            'section' => $_POST['ch_section'],
            'status' => 'show'
        ];
        $channels_data['custom_channels'][] = $new_ch;
        file_put_contents($db_file, json_encode($channels_data, JSON_UNESCAPED_UNICODE));
        $msg = "تمت إضافة القناة بنجاح!";
    }

    // تحديث حالة
    if (isset($_POST['update_status'])) {
        foreach ($channels_data['custom_channels'] as &$ch) {
            if ($ch['id'] == $_POST['ch_id']) {
                $ch['status'] = $_POST['status'];
                break;
            }
        }
        file_put_contents($db_file, json_encode($channels_data, JSON_UNESCAPED_UNICODE));
    }

    // حذف قناة
    if (isset($_GET['delete'])) {
        $channels_data['custom_channels'] = array_filter($channels_data['custom_channels'], function($ch) {
            return $ch['id'] !== $_GET['delete'];
        });
        file_put_contents($db_file, json_encode($channels_data, JSON_UNESCAPED_UNICODE));
        header("Location: admin.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة القنوات - الحماية النشطة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #050c14; color: white; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: rgba(255,255,255,0.05); padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); }
        .login-box { max-width: 400px; margin: 100px auto; text-align: center; background: rgba(255,255,255,0.05); padding: 40px; border-radius: 20px; border: 1px solid var(--main); }
        .msg { background: #22c55e; color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; }
        .error { background: #ef4444; color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; }
        input, select, button { padding: 12px; margin: 5px; border-radius: 8px; border: 1px solid #333; background: #111; color: white; width: calc(100% - 30px); }
        .admin-form input, .admin-form select { width: auto; min-width: 180px; }
        button { background: #e11d48; cursor: pointer; border: none; font-weight: bold; width: auto; padding: 12px 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #222; text-align: center; }
        th { color: #e11d48; }
        .status-show { color: #22c55e; }
        .status-hide { color: #ef4444; }
        .logout-btn { float: left; background: #333; font-size: 12px; padding: 5px 15px; }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['logged_in'])): ?>
    <div class="login-box">
        <h2>تسجيل دخول الإدارة</h2>
        <?php if(isset($login_error)) echo "<div class='error'>$login_error</div>"; ?>
        <form method="POST">
            <input type="text" name="user" placeholder="اسم المستخدم" required>
            <input type="password" name="pass" placeholder="كلمة المرور" required>
            <button type="submit" name="login">دخول</button>
        </form>
    </div>
<?php else: ?>
    <div class="container">
        <a href="?logout=1" class="logout-btn" style="color:white; text-decoration:none; border-radius:5px;">تسجيل الخروج</a>
        <h2>➕ إضافة قناة للقسم</h2>
        
        <?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?>

        <form method="POST" class="admin-form">
            <input type="text" name="ch_name" placeholder="اسم القناة" required>
            <input type="text" name="ch_file" placeholder="ملف القناة (مثال: k1.php)" required>
            <select name="ch_section">
                <option value="bein">beIN Sport</option>
                <option value="mbc">باقة MBC</option>
                <option value="alkas">باقة الكاس</option>
                <option value="kuwait">الكويت الرياضية</option>
                <option value="ado">أبوظبي الرياضية</option>
                <option value="on">On Sport</option>
                <option value="dubai">دبي الرياضية</option>
                <option value="shahad">شاهد الرياضية</option>
                <option value="star">STARZPLAY</option>
                <option value="moc">الباقة المغربية</option>
                <option value="sky">Sky Sport</option>
                <option value="plus">Canal+ Sport</option>
                <option value="sporttv">Sport TV</option>
            </select>
            <button type="submit" name="add_channel">إضافة</button>
        </form>

        <hr style="border: 0; border-top: 1px solid #333; margin: 30px 0;">

        <h2>📺 التحكم بالقنوات</h2>
        <table>
            <tr>
                <th>اسم القناة</th>
                <th>القسم</th>
                <th>الحالة</th>
                <th>تغيير</th>
                <th>حذف</th>
            </tr>
            <?php foreach($channels_data['custom_channels'] as $ch): ?>
            <tr>
                <td><?php echo $ch['name']; ?></td>
                <td><?php echo strtoupper($ch['section']); ?></td>
                <td class="status-<?php echo $ch['status']; ?>"><?php echo ($ch['status'] == 'show' ? 'ظاهرة' : 'مخفية'); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="ch_id" value="<?php echo $ch['id']; ?>">
                        <select name="status" onchange="this.form.submit()" style="width: auto; min-width: 80px;">
                            <option value="show" <?php if($ch['status']=='show') echo 'selected'; ?>>إظهار</option>
                            <option value="hide" <?php if($ch['status']=='hide') echo 'selected'; ?>>إخفاء</option>
                        </select>
                        <input type="hidden" name="update_status">
                    </form>
                </td>
                <td><a href="?delete=<?php echo $ch['id']; ?>" onclick="return confirm('حذف؟')" style="color:#ef4444; text-decoration:none;">حذف</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <a href="index.php" style="color:#0ea5e9;">العودة للبث المباشر</a>
    </div>
<?php endif; ?>

</body>
</html>
