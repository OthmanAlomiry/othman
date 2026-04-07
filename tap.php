<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول المباريات - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --border: rgba(255, 255, 255, 0.1); }
        body { 
            background: var(--bg); 
            color: #fff; 
            font-family: 'Tajawal', sans-serif; 
            margin: 0; 
            padding: 10px; 
            display: flex; 
            justify-content: center; 
            overflow-x: hidden;
        }
        .container { 
            width: 100%; 
            max-width: 500px; 
            min-height: 100vh; 
        }
        
        .header { 
            text-align: center; 
            padding: 20px 0; 
            border-bottom: 1px solid var(--border); 
            margin-bottom: 15px; 
        }
        .header h2 { 
            margin: 0; 
            font-weight: 900; 
            color: var(--main); 
            font-size: 1.2rem; 
            text-shadow: 0 0 15px rgba(225, 29, 72, 0.3);
        }

        /* تنسيق الويجت ليتناسب مع تصميمك عثمان */
        #fs-wm { 
            background: rgba(255, 255, 255, 0.03) !important; 
            border-radius: 20px !important; 
            overflow: hidden;
            border: 1px solid var(--border) !important;
        }

        footer { 
            text-align: center; 
            padding: 30px; 
            font-size: 10px; 
            opacity: 0.3; 
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fas fa-futbol"></i> جدول مباريات اليوم</h2>
    </div>

    <div id="fs-wm" 
        class="fs-widget" 
        data-mode="matches" 
        data-show_logotypes="true" 
        data-show_full_names="false" 
        data-language="ar" 
        data-timezone="Asia/Riyadh" 
        data-categories="1,5,7,8,12,17,21,22,23,24,25,26,27,28,29,30,31,32,33,34" 
        data-theme="dark">
    </div>
    
    <script async src="https://widget.footystats.org/v2/widget.js"></script>

    <footer>تحديث تلقائي لحظي - الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
