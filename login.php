<?php
session_start(); // ضروري لبدء الجلسة وتذكر المستخدم
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // التحقق من وجود المستخدم ومطابقة كلمة المرور المشفرة
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // التوجيه إلى لوحة التحكم بعد النجاح
        header("Location: dashboard.php");
        exit();
    } else {
        echo "خطأ في اسم المستخدم أو كلمة المرور.";
    }
}
?>

<form method="POST">
    <h2>تسجيل الدخول</h2>
    <input type="text" name="username" placeholder="اسم المستخدم" required>
    <input type="password" name="password" placeholder="كلمة المرور" required>
    <button type="submit">دخول</button>
</form>
