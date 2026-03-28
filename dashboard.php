<?php
session_start();
require 'config.php';

// حماية الصفحة: إذا لم يكن هناك جلسة، ارجع لصفحة الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// معالجة إضافة رابط جديد
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_link'])) {
    $platform = $_POST['platform_name']; // مثل WhatsApp
    $url = $_POST['url'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO links (user_id, platform_name, url) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $platform, $url]);
    echo "تم إضافة الرابط بنجاح!";
}
?>

<h1>أهلاً بك يا <?php echo $_SESSION['username']; ?></h1>
<a href="logout.php">تسجيل الخروج</a>

<hr>

<h3>إضافة رابط جديد</h3>
<form method="POST">
    <input type="text" name="platform_name" placeholder="اسم المنصة (مثلاً: Instagram)" required>
    <input type="url" name="url" placeholder="الرابط (https://...)" required>
    <button type="submit" name="add_link">إضافة الرابط</button>
</form>
