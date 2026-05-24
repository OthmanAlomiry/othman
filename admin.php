<?php
session_start();

// --- نظام تسجيل الدخول ---
$username_allowed = "talal";
$password_allowed = "8114";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (isset($_POST['login'])) {
    if ($_POST['user'] === $username_allowed && $_POST['pass'] === $password_allowed) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "اسم المستخدم أو كلمة المرور غير صحيحة!";
    }
}

if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>مناسبات الجماعة - لوحة التحكم</title>
        <style>
            body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; padding: 20px; box-sizing: border-box; }
            .login-card { background: #fff; padding: 40px 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); width: 100%; max-width: 400px; text-align: center; }
            h2 { color: #305496; margin-bottom: 25px; }
            input { width: 100%; padding: 15px; margin: 12px 0; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box; font-size: 16px; }
            button { width: 100%; padding: 15px; background: #305496; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 18px; font-weight: bold; margin-top: 10px; }
            .error { color: #d9534f; font-size: 14px; margin-bottom: 15px; background: #fdf2f2; padding: 10px; border-radius: 8px; }
        </style>
    </head>
    <body>
        <div class="login-card">
            <h2>مناسبات الجماعة -  لوحة التحكم</h2>
            <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
            <form method="POST">
                <input type="text" name="user" placeholder="اسم المستخدم" required>
                <input type="password" name="pass" placeholder="كلمة المرور" required>
                <button type="submit" name="login">دخول</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// --- إعدادات الملفات ---
$file = 'data.json';
$settings_file = 'settings.json';
$upload_dir = 'uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$editMode = false;
// تم إضافة map_url للمصفوفة الافتراضية
$editItem = ['day'=>'', 'date'=>'', 'host'=>'', 'loc'=>'', 'hall'=>'', 'id'=>'', 'invitation'=>'', 'dinner_time'=>'', 'map_url'=>''];

$data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$settings = file_exists($settings_file) ? json_decode(file_get_contents($settings_file), true) : ['news_text' => '', 'news_status' => 'off'];

if (isset($_POST['save_settings'])) {
    $settings['news_text'] = $_POST['news_text'];
    $settings['news_status'] = isset($_POST['news_status']) ? 'on' : 'off';
    file_put_contents($settings_file, json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

if (isset($_POST['save'])) {
    $image_path = $_POST['existing_invitation'] ?? '';

    if (isset($_FILES['invitation_file']) && $_FILES['invitation_file']['error'] === 0) {
        $ext = pathinfo($_FILES['invitation_file']['name'], PATHINFO_EXTENSION);
        $new_name = uniqid('card_') . '.' . $ext;
        $target = $upload_dir . $new_name;
        
        if (move_uploaded_file($_FILES['invitation_file']['tmp_name'], $target)) {
            if (!empty($image_path) && file_exists($image_path)) {
                unlink($image_path);
            }
            $image_path = $target;
        }
    }

    if (!empty($_POST['id'])) { 
        foreach ($data as &$item) {
            if ($item['id'] === $_POST['id']) {
                $item['day']  = $_POST['day'];
                $item['date'] = $_POST['date'];
                $item['host'] = $_POST['host'];
                $item['loc']  = $_POST['loc'];
                $item['hall'] = $_POST['hall'];
                $item['dinner_time'] = $_POST['dinner_time'];
                $item['map_url'] = $_POST['map_url']; // تحديث الرابط
                $item['invitation'] = $image_path;
            }
        }
    } else { 
        $data[] = [
            'id' => uniqid(), 
            'day' => $_POST['day'], 
            'date' => $_POST['date'], 
            'host' => $_POST['host'], 
            'loc' => $_POST['loc'], 
            'hall' => $_POST['hall'],
            'dinner_time' => $_POST['dinner_time'],
            'map_url' => $_POST['map_url'], // حفظ الرابط
            'invitation' => $image_path
        ];
    }
    file_put_contents($file, json_encode(array_values($data), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

if (isset($_GET['delete'])) {
    $targetId = $_GET['delete'];
    foreach ($data as $item) {
        if ($item['id'] === $targetId && !empty($item['invitation']) && file_exists($item['invitation'])) {
            unlink($item['invitation']);
        }
    }
    $data = array_filter($data, function($item) use ($targetId) { return $item['id'] !== $targetId; });
    file_put_contents($file, json_encode(array_values($data), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header("Location: admin.php");
    exit;
}

if (isset($_GET['edit'])) {
    $targetId = $_GET['edit'];
    foreach ($data as $item) {
        if ($item['id'] === $targetId) { 
            $editItem = array_merge($editItem, $item); 
            $editMode = true; 
            break; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - إدارة المناسبات</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; padding: 15px; margin: 0; }
        .card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 20px auto; position: relative; }
        h2 { color: #305496; margin-top: 15px; text-align: center; }
        input[type="text"], input[type="file"], input[type="url"] { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 16px; }
        label { font-size: 14px; color: #555; font-weight: bold; display: block; margin-top: 10px; }
        .btn-save { width: 100%; padding: 15px; background: #305496; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px; }
        .logout-link { position: absolute; top: 15px; left: 15px; color: #d9534f; text-decoration: none; font-size: 12px; font-weight: bold; border: 1px solid #d9534f; padding: 5px 10px; border-radius: 6px; }
        
        .news-settings { background: #eef3ff; padding: 20px; border-radius: 10px; border: 1px dashed #305496; margin-bottom: 30px; }
        .news-settings h3 { margin: 0 0 15px 0; font-size: 16px; color: #305496; display: flex; align-items: center; gap: 8px; }
        .toggle-container { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 14px; font-weight: bold; }
        
        .switch { position: relative; display: inline-block; width: 45px; height: 22px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 4px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #25d366; }
        input:checked + .slider:before { transform: translateX(21px); }
        .btn-small { background: #305496; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }

        .list-container { margin-top: 30px; }
        .item-row { display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #eee; background: #fafafa; margin-bottom: 10px; border-radius: 10px; }
        .item-info { flex: 1; margin-left: 10px; }
        .item-info strong { display: block; font-size: 16px; color: #333; margin-bottom: 4px; }
        .item-info small { color: #888; }
        .action-btns { display: flex; gap: 8px; }
        .badge-edit { background: #305496; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 14px; }
        .badge-delete { background: #fff; color: #d9534f; border: 1px solid #d9534f; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 14px; }
        .img-preview { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; margin-left: 10px; border: 1px solid #ddd; }

        .official-site { margin-top: 25px; text-align: center; border-top: 2px solid #f0f2f5; padding-top: 20px; }
        .official-site a { color: #305496; text-decoration: none; font-weight: bold; font-size: 15px; }

        @media (max-width: 500px) {
            .item-row { flex-direction: column; align-items: flex-start; gap: 15px; }
            .action-btns { width: 100%; justify-content: flex-end; border-top: 1px solid #eee; padding-top: 10px; }
        }
    </style>
</head>
<body>

<div class="card">
    <a href="admin.php?logout=1" class="logout-link">خروج</a>
    <h2>إدارة المناسبات</h2>

    <div class="news-settings">
        <h3>📢 إعدادات الشريط الإخباري</h3>
        <form method="POST">
            <div class="toggle-container">
                <span>حالة الشريط:</span>
                <label class="switch">
                    <input type="checkbox" name="news_status" <?php echo ($settings['news_status'] === 'on') ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>
            <input type="text" name="news_text" placeholder="اكتب الخبر أو التنبيه هنا..." value="<?php echo htmlspecialchars($settings['news_text']); ?>">
            <button type="submit" name="save_settings" class="btn-small">حفظ إعدادات الشريط</button>
        </form>
    </div>

    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
        <input type="hidden" name="existing_invitation" value="<?php echo $editItem['invitation']; ?>">
        
        <input type="text" name="day" placeholder="اليوم (مثال: الجمعة)" value="<?php echo $editItem['day']; ?>" required>
        <input type="text" name="date" placeholder="التاريخ (مثال: 1447/05/20)" value="<?php echo $editItem['date']; ?>" required>
        <input type="text" name="host" placeholder="صاحب المناسبة" value="<?php echo $editItem['host']; ?>" required>
        <input type="text" name="loc" placeholder="المدينة أو القرية" value="<?php echo $editItem['loc']; ?>" required>
        <input type="text" name="hall" placeholder="اسم القاعة" value="<?php echo $editItem['hall']; ?>" required>
        
        <input type="url" name="map_url" placeholder="رابط قوقل ماب (مثال: https://maps.app.goo.gl/...)" value="<?php echo isset($editItem['map_url']) ? $editItem['map_url'] : ''; ?>">

        <input type="text" name="dinner_time" placeholder="وقت العشاء (اختياري - مثال: بعد صلاة العشاء)" value="<?php echo isset($editItem['dinner_time']) ? $editItem['dinner_time'] : ''; ?>">

        <label>🖼️ أضافة كرت الدعوة (اختياري):</label>
        <input type="file" name="invitation_file" accept="image/*">
        
        <?php if($editMode && !empty($editItem['invitation'])): ?>
            <p style="font-size: 12px; color: #25d366;">يوجد كرت دعوة حالي ✅</p>
        <?php endif; ?>

        <button type="submit" name="save" class="btn-save"><?php echo $editMode ? "تحديث البيانات" : "حفظ المناسبة"; ?></button>
    </form>

    <div class="list-container">
        <?php foreach (array_reverse($data) as $row): ?>
        <div class="item-row">
            <div style="display: flex; align-items: center;">
                <?php if(!empty($row['invitation'])): ?>
                    <img src="<?php echo $row['invitation']; ?>" class="img-preview">
                <?php else: ?>
                    <div class="img-preview" style="display:flex; align-items:center; justify-content:center; font-size:10px; background:#eee;">بلا كرت</div>
                <?php endif; ?>
                <div class="item-info">
                    <strong><?php echo $row['host']; ?></strong>
                    <small><?php echo $row['day']; ?> | <?php echo $row['date']; ?></small>
                </div>
            </div>
            <div class="action-btns">
                <a href="admin.php?edit=<?php echo $row['id']; ?>" class="badge-edit">تعديل</a>
                <a href="admin.php?delete=<?php echo $row['id']; ?>" class="badge-delete" onclick="return confirm('حذف المناسبة والصورة نهائياً؟')">حذف</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="official-site">
        <a href="index.php" target="_blank">⇠ انتقل للموقع الرسمي</a>
    </div>
</div>

</body>
</html>
