<?php
$db_file = 'channels_db.json';
$channels_data = file_exists($db_file) ? json_decode(file_get_contents($db_file), true) : ['sections' => [], 'custom_channels' => []];

// 1. إضافة قناة جديدة
if (isset($_POST['add_channel'])) {
    $new_ch = [
        'id' => uniqid(),
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
    $msg = "تم تحديث الحالة!";
}

// 3. حذف قناة
if (isset($_GET['delete'])) {
    $channels_data['custom_channels'] = array_filter($channels_data['custom_channels'], function($ch) {
        return $ch['id'] !== $_GET['delete'];
    });
    file_put_contents($db_file, json_encode($channels_data));
    header("Location: admin.php?msg=deleted");
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة القنوات - الخدمة الرقمية</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #050c14; color: white; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px; }
        input, select, button { padding: 10px; margin: 5px; border-radius: 5px; border: 1px solid #444; }
        input, select { background: #1a1a1a; color: white; }
        button { background: #e11d48; color: white; cursor: pointer; border: none; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #333; text-align: center; }
        .status-show { color: #22c55e; }
        .status-hide { color: #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <h2>اضافة قناة جديدة</h2>
        <form method="POST">
            <input type="text" name="ch_name" placeholder="اسم القناة (مثلاً: MBC 2)" required>
            <input type="text" name="ch_file" placeholder="اسم الملف (مثلاً: mbe2.php)" required>
            <select name="ch_section">
                <option value="bein">beIN Sport</option>
                <option value="mbc">MBC</option>
                <option value="shahad">شاهد</option>
                <option value="alkas">الكاس</option>
                <option value="kuwait">الكويت</option>
                <option value="moc">المغربية</option>
            </select>
            <button type="submit" name="add_channel">إضافة القناة</button>
        </form>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #333;">

        <h2>التحكم في القنوات الحالية</h2>
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
                <td><?php echo $ch['section']; ?></td>
                <td class="status-<?php echo $ch['status']; ?>"><?php echo ($ch['status'] == 'show' ? 'ظاهرة' : 'مخفية'); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="ch_id" value="<?php echo $ch['id']; ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="show" <?php if($ch['status']=='show') echo 'selected'; ?>>إظهار</option>
                            <option value="hide" <?php if($ch['status']=='hide') echo 'selected'; ?>>إخفاء</option>
                        </select>
                        <input type="hidden" name="update_status">
                    </form>
                </td>
                <td><a href="?delete=<?php echo $ch['id']; ?>" style="color:red; text-decoration:none;">حذف</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <a href="liv.php" style="color:#0ea5e9;">الذهاب للموقع</a>
    </div>
</body>
</html>
