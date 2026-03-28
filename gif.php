<?php
error_reporting(0);
session_start();

// --- ضع اشتراكك الوحيد هنا ---
$final_prize = "D-SERVICE-PREMIUM-2026-X"; 

function generatePuzzle() {
    $start = rand(5, 20);
    $step = rand(3, 7);
    $sequence = [];
    for ($i = 0; $i < 4; $i++) { $sequence[] = $start + ($i * $step); }
    return ['seq' => implode(", ", $sequence), 'ans' => $start + (4 * $step)];
}

// إذا لم يكن هناك لغز، قم بتوليده
if (!isset($_SESSION['puzzle'])) {
    $_SESSION['puzzle'] = generatePuzzle();
}

$msg = ""; 
$type = ""; 
$show_prize = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ans'])) {
    // التحقق من الحل ومن أن المستخدم لم يربح بالفعل في هذه الجلسة
    if (intval($_POST['ans']) === $_SESSION['puzzle']['ans']) {
        if (!isset($_SESSION['already_won'])) {
            $_SESSION['already_won'] = true; // تسجيل الفوز في الجلسة
            $msg = "🎉 تهانينا! أنت الفائز الأول والوحيد:";
            $type = "success";
            $show_prize = true;
        } else {
            $msg = "لقد حصلت على جائزتك بالفعل!";
            $type = "error";
        }
    } else {
        $msg = "❌ إجابة خاطئة.. حاول التركيز أكثر!";
        $type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>d-service | مسابقة الجائزة الكبرى</title>
    <style>
        body { background: #000; color: #00ff41; font-family: 'Courier New', monospace; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .terminal { border: 2px solid #00ff41; padding: 40px; border-radius: 15px; width: 380px; background: #0a0a0a; text-align: center; box-shadow: 0 0 25px rgba(0,255,65,0.3); }
        .puzzle-box { font-size: 26px; background: #111; padding: 20px; margin: 20px 0; border: 1px dashed #00ff41; letter-spacing: 2px; }
        input { background: #000; border: 1px solid #00ff41; color: #00ff41; padding: 12px; width: 100%; box-sizing: border-box; text-align: center; font-size: 18px; margin-bottom: 15px; outline: none; }
        button { background: #00ff41; color: #000; border: none; padding: 15px; width: 100%; font-weight: bold; cursor: pointer; font-size: 16px; transition: 0.3s; }
        button:hover { background: #fff; box-shadow: 0 0 10px #fff; }
        .alert { margin-top: 25px; padding: 15px; border: 1px solid; border-radius: 8px; }
        .success { border-color: #00ff41; background: rgba(0,255,65,0.1); }
        .error { border-color: #ff4141; color: #ff4141; }
        .prize-text { display: block; margin-top: 10px; font-size: 1.3em; color: #fff; text-shadow: 0 0 5px #00ff41; background: #222; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="terminal">
        <h1 style="font-size: 1.3em; margin-bottom: 5px;">🏆 جائزة d-service الكبرى</h1>
        <p style="font-size: 12px; color: #888;">يوجد فائز واحد فقط.. هل ستكون أنت؟</p>
        
        <div class="puzzle-box">
            <?php echo $_SESSION['puzzle']['seq']; ?>, ??
        </div>
        
        <form method="POST">
            <input type="number" name="ans" placeholder="أدخل الرقم المفقود" required autofocus>
            <button type="submit">محاولة سحب الجائزة</button>
        </form>

        <?php if ($msg): ?>
            <div class="alert <?php echo $type; ?>">
                <?php echo $msg; ?>
                <?php if($show_prize): ?>
                    <span class="prize-text"><?php echo $final_prize; ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
