<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// الحصول على التاريخ للتنقل بالأسهم عثمان
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$display_date = date('d / m / Y', strtotime($date_get));

$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --border: rgba(255, 255, 255, 0.1); }
        
        body { 
            margin: 0; padding: 0; background-color: var(--bg); 
            font-family: 'Tajawal', sans-serif; color: #fff;
            display: flex; justify-content: center; overflow-x: hidden;
        }

        .container { 
            width: 100%; max-width: 500px; min-height: 100vh; 
            padding: 15px; box-sizing: border-box;
        }

        /* نظام الأسهم عثمان */
        .date-nav {
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border);
            padding: 12px; border-radius: 20px; margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        .date-nav a {
            width: 35px; height: 35px; background: var(--main); color: #fff;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; text-decoration: none; font-size: 14px;
            transition: 0.3s; box-shadow: 0 5px 15px rgba(225, 29, 72, 0.3);
        }
        .date-nav h3 { margin: 0; font-size: 16px; font-weight: 900; }

        /* منطقة الويجت عثمان */
        .widget-box {
            width: 100%; border-radius: 20px; overflow: hidden;
            border: 1px solid var(--border); background: #0b1118;
            min-height: 800px; position: relative;
        }

        footer { text-align: center; padding: 25px; font-size: 10px; opacity: 0.4; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-nav">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <h3><?= $display_date ?></h3>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <div class="widget-box">
        <div class="scoreaxis-widget" 
             data-widget="scoreaxis-live-scores" 
             data-date="<?= $date_get ?>" 
             data-language="ar" 
             data-timezone="Asia/Riyadh" 
             data-theme="dark" 
             data-body-background="050c14" 
             data-font="Tajawal">
        </div>
        <script src="https://www.scoreaxis.com/widget/developers.js"></script>
    </div>

    <footer>الخدمة الرقمية © 2026 - تحديث لحظي للمباريات</footer>
</div>

</body>
</html>
