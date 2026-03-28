<?php
require 'config.php'; // استدعاء ملف الاتصال بالقاعدة

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    // تشفير كلمة المرور (أمر ضروري للأمان)
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        echo "تم التسجيل بنجاح! <a href='login.php'>سجل دخولك من هنا</a>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // كود الخطأ لاسم المستخدم المكرر
            echo "اسم المستخدم أو البريد موجود مسبقاً.";
        } else {
            echo "خطأ: " . $e->getMessage();
        }
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="اسم المستخدم (سيكون رابط صفحتك)" required>
    <input type="email" name="email" placeholder="البريد الإلكتروني" required>
    <input type="password" name="password" placeholder="كلمة المرور" required>
    <button type="submit">إنشاء حساب</button>
</form>
