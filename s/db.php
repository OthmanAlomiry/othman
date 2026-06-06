<?php
$host = "sql202.infinityfree.com"; // كما يظهر في صورتك (MySQL Hostname)
$dbname = "اكتب_الاسم_الذي_أنشأته_هنا"; // اسم القاعدة الجديد (مثلاً: if0_41269424_events)
$username = "if0_41269424"; // كما يظهر في صورتك (MySQL Username)
$password = "كلمة_مرورك_الخاصة"; // اضغط على علامة العين في الصورة لتظهر لك كلمة المرور وانسخها

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}
?>
