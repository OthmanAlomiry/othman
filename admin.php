<?php
$db_file = 'channels_db.json';

// القنوات الأساسية التي نريدها أن تظهر فوراً في لوحة التحكم
$default_channels = [
    ['id' => 'b1', 'name' => 'beIN Sport 1', 'file' => 'b1.php', 'section' => 'bein', 'status' => 'show'],
    ['id' => 'b2', 'name' => 'beIN Sport 2', 'file' => 'b2.php', 'section' => 'bein', 'status' => 'show'],
    ['id' => 'mb1', 'name' => 'MBC 1', 'file' => 'mb1.php', 'section' => 'mbc', 'status' => 'show'],
    ['id' => 'mbe2', 'name' => 'MBC مصر 2', 'file' => 'mbe2.php', 'section' => 'mbc', 'status' => 'show'],
    ['id' => 'sh1', 'name' => 'SHOOF 1', 'file' => 'sh1.php', 'section' => 'alkas', 'status' => 'show'],
    ['id' => 'k1', 'name' => 'الكاس 1', 'file' => 'k1.php', 'section' => 'alkas', 'status' => 'show'],
    ['id' => 'arr', 'name' => 'الرياضية المغربية', 'file' => 'arr.php', 'section' => 'moc', 'status' => 'show'],
    ['id' => 'ku1', 'name' => 'الكويت الرياضية', 'file' => 'ku1.php', 'section' => 'kuwait', 'status' => 'show']
];

// جلب البيانات أو إنشاء القنوات الأساسية
if (!file_exists($db_file)) {
    $channels_data = ['custom_channels' => $default_channels];
    file_put_contents($db_file, json_encode($channels_data));
} else {
    $channels_data = json_decode(file_get_contents($db_file), true);
}

// 1. إضافة قناة جديدة (مع دعم كافة الأقسام)
if (isset($_POST['add_channel'])) {
    $new_ch = [
        'id' => 'ch_' . uniqid(),
        'name' => $_POST['ch_name'],
        'file' => $_POST['ch_file'],
        'section' => $_POST['ch_section'],
        'status' => 'show'
    ];
    $channels_data['custom_channels'][] = $new_ch;
    file_put_contents($db_file, json_encode($channels_data));
    $msg = "تمت إضافة القناة بنجاح!";
}

// 2. تحديث حالة قناة (إخفاء/إظهار)
if (isset($_POST['update_status'])) {
    foreach ($channels_data['custom_channels'] as &$ch) {
        if ($ch['id'] == $_POST['ch_id']) {
            $ch['status'] = $_POST['status'];
            break;
        }
    }
    file_put_contents($db_file, json_encode($channels_data));
}

// 3. حذف قناة
if (isset($_GET['delete'])) {
    $channels_data['custom_channels'] = array_filter($channels_data['custom_channels'], function($ch) {
        return $ch['id'] !== $_GET['delete'];
    });
    file_put_contents($db_file, json_encode($channels_data));
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم القنوات - عثمان</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #050c14; color: white; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: rgba(255,255,255,0.05); padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); }
        input, select, button { padding: 12px; margin: 5px; border-radius: 8px; border: 1px solid #333; outline: none; }
        input, select { background: #111; color: white; width: 200px; }
        button { background: #e11d48; color: white; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #be123c; }
        table { width: 100%; margin-top: 30px; border-collapse: collapse; background: rgba(0,0,0,0.2); border-radius: 10px; overflow: hidden; }
        th, td { padding: 15px; border-bottom: 1px solid #222; text-align: center; font-size: 14px; }
        th { background: rgba(225, 29, 72, 0.1); color: #e11d48; }
        .status-show { color: #22c55e; font-weight: bold; }
        .status-hide { color: #ef4444; font-weight: bold; }
        .btn-delete { color: #ef4444; text-decoration: none; font-size: 12px; border: 1px solid #ef4444; padding: 4px 8px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>➕ إضافة قناة جديدة للقسم</h2>
        <form method="POST">
            <input type="text" name="ch_name" placeholder="اسم القناة" required>
            <input type="text" name="ch_file" placeholder="ملف القناة (مثال: k4.php)" required>
            <select name="ch_section">
                <option value="bein">beIN Sport</option>
                <option value="shahad">شاهد الرياضية</option>
                <option value="star">STARZPLAY</option>
                <option value="mbc">باقة MBC</option>
                <option value="alkas">باقة الكاس</option>
                <option value="ado">أبوظبي الرياضية</option>
                <option value="on">On Sport</option>
                <option value="dubai">دبي الرياضية</option>
                <option value="kuwait">الكويت الرياضية</option>
                <option value="moc">الباقة المغربية</option>
                <option value="sky">Sky Sport</option>
                <option value="plus">Canal+ Sport</option>
                <option value="sporttv">Sport TV</option>
            </select>
            <button type="submit" name="add_channel">حفظ القناة</button>
        </form>

        <hr style="margin: 40px 0; border: 0; border-top: 1px solid #333;">

        <h2>📺 التحكم بالقنوات الحالية (إظهار/إخفاء)</h2>
        <table>
            <thead>
                <tr>
                    <th>اسم القناة</th>
                    <th>القسم</th>
                    <th>الملف</th>
                    <th>الحالة</th>
                    <th>الإجراء</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($channels_data['custom_channels'] as $ch): ?>
                <tr>
                    <td><?php echo $ch['name']; ?></td>
                    <td><?php echo strtoupper($ch['section']); ?></td>
                    <td><code><?php echo $ch['file']; ?></code></td>
                    <td class="status-<?php echo $ch['status']; ?>">
                        <?php echo ($ch['status'] == 'show' ? '● ظاهرة' : '○ مخفية'); ?>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="ch_id" value="<?php echo $ch['id']; ?>">
                            <select name="status" onchange="this.form.submit()" style="width: auto; padding: 5px;">
                                <option value="show" <?php if($ch['status']=='show') echo 'selected'; ?>>إظهار</option>
                                <option value="hide" <?php if($ch['status']=='hide') echo 'selected'; ?>>إخفاء</option>
                            </select>
                            <input type="hidden" name="update_status">
                        </form>
                    </td>
                    <td><a href="?delete=<?php echo $ch['id']; ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <div style="text-align: center;">
            <a href="liv.php" style="color:#0ea5e9; text-decoration: none; font-weight: bold;">← العودة للبث المباشر</a>
        </div>
    </div>
</body>
</html>
