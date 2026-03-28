<?php
session_start();

// دالة لتوليد لغز ذكاء (متتالية حسابية)
function generatePuzzle() {
    $start = rand(1, 15);
    $step = rand(2, 6);
    $sequence = [];
    for ($i = 0; $i < 4; $i++) {
        $sequence[] = $start + ($i * $step);
    }
    return [
        'seq' => implode(", ", $sequence),
        'ans' => $start + (4 * $step)
    ];
}

// دالة سحب كود حقيقي من الملف وحذفه
function pullRealCode() {
    $filename = "codes.txt";
    if (!file_exists($filename) || filesize($filename) == 0) {
        return "للأسف، نفدت الأكواد حالياً! انتظر التحديث.";
    }

    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $winner_code = array_shift($lines); // سحب أول سطر (الكود)
    
    // إعادة حفظ باقي الأكواد في الملف
    file_put_contents($filename, implode(PHP_EOL, $lines));
    
    return $winner_code;
}

if (!isset($_SESSION['puzzle']) || isset($_GET['new'])) {
    $_SESSION['puzzle'] = generatePuzzle();
}

$msg = "";
$type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ans'])) {
    $user_ans = intval($_POST['ans']);
    if ($user_ans === $_SESSION['puzzle']['ans']) {
        // سحب كود حقيقي من الملف
        $final_code = pullRealCode();
        $msg = "✅ ذكاء مذهل! اشتراكك الحقيقي هو: <br><span style='color:#fff; font-size:1.5em;'>$final_code</span>";
        $type = "success";
        unset($_SESSION['puzzle']); 
    } else {
        $msg = "❌ خطأ في النظام.. حاول فك الشفرة بتركيز!";
        $type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>d-service | تحدي الأكواد</title>
    <style>
        body { background: #050505; color: #00ff41; font-family: 'Courier New', monospace; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .terminal { border: 2px solid #00ff41; padding: 40px; border-radius: 10px; box-shadow: 0 0 30px rgba(0, 255, 65, 0.1); text-align: center; width: 90%; max-width: 450px; background: #0a0a0a; }
        .code-box { background: #111; border: 1px dashed #00ff41; padding: 20px; margin: 20px 0; font-size: 28px; font-weight: bold; }
        input { background: #000; border: 1px solid #00ff41; color: #00ff41; padding: 12px; width: 100%; text-align: center; margin-bottom: 10px; box-sizing: border-box; }
        button { background: #00ff41; color: #000; border: none; padding: 15px; width: 100%; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #008f11; box-shadow: 0 0 15px #00ff41; }
        .msg { margin-top: 25px; padding: 15px; border: 1px solid; border-radius: 5px; line-height: 1.6; }
        .success { border-color: #00ff41; background: rgba(0, 255, 65, 0.1); }
        .error { border-color: #ff4141; color: #ff4141; background: rgba(255, 65, 65, 0.1); }
        .footer-link { display: block; margin-top: 20px; color: #555; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>

<div class="terminal">
    <h1 style="font-size: 1.5em; margin-top: 0;">UNAUTHORIZED ACCESS REWARD</h1>
    <p>حل اللغز الرياضي للوصول إلى قاعدة بيانات الاشتراكات:</p>
    
    <div class="code-box">
        <?php echo $_SESSION['puzzle']['seq']; ?>, ??
    </div>

    <form method="POST">
        <input type="number" name="ans" placeholder="أدخل الحل الرقمي" required autofocus>
        <button type="submit">تجاوز الحماية الحالية</button>
    </form>

    <?php if ($msg): ?>
        <div class="msg <?php echo $type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <a href="gif.php?new=1" class="footer-link">توليد شفرة جديدة (Reset System)</a>
</div>

</body>
</html>
