<?php
$config_file = 'channels_config.json';

// جلب الإعدادات الحالية
$config = file_exists($config_file) ? json_decode(file_get_contents($config_file), true) : [];

// إذا تم إرسال تحديث
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $channel_id = $_POST['channel_id'];
    $status = $_POST['status'];
    $config[$channel_id] = $status;
    file_put_contents($config_file, json_encode($config));
    $msg = "تم تحديث حالة القناة بنجاح!";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم القنوات</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #050c14; color: white; padding: 20px; text-align: center; }
        table { width: 100%; max-width: 600px; margin: 20px auto; border-collapse: collapse; background: rgba(255,255,255,0.05); }
        th, td { padding: 15px; border: 1px solid rgba(255,255,255,0.1); }
        select { padding: 5px; border-radius: 5px; }
        button { padding: 5px 15px; background: #e11d48; color: white; border: none; cursor: pointer; border-radius: 5px; }
        .msg { color: #22c55e; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>لوحة تحكم قنوات الخدمة الرقمية</h2>
    <?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?>
    
    <table>
        <tr>
            <th>القناة/القسم</th>
            <th>الحالة</th>
            <th>إجراء</th>
        </tr>
        <?php
        $channels = [
            'section-bein' => 'باقة beIN',
            'section-mbc' => 'باقة MBC',
            'section-shahad' => 'باقة شاهد',
            'section-star' => 'باقة STARZ',
            'section-alkas' => 'باقة الكاس',
            'section-kuwait' => 'باقة الكويت',
            'section-moc' => 'الباقة المغربية',
            'section-dubai' => 'باقة دبي'
        ];
        
        foreach($channels as $id => $name): 
            $current_status = isset($config[$id]) ? $config[$id] : 'show';
        ?>
        <tr>
            <td><?php echo $name; ?></td>
            <td><?php echo ($current_status == 'show') ? '✅ ظاهرة' : '❌ مخفية'; ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="channel_id" value="<?php echo $id; ?>">
                    <select name="status">
                        <option value="show" <?php if($current_status == 'show') echo 'selected'; ?>>إظهار</option>
                        <option value="hide" <?php if($current_status == 'hide') echo 'selected'; ?>>إخفاء</option>
                    </select>
                    <button type="submit">تحديث</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="liv.php" style="color: #0ea5e9;">العودة للموقع الرئيسي</a>
</body>
</html>
