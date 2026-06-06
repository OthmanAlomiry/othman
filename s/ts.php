<?php
// إعداد المنطقة الزمنية والتاريخ الهجري
date_default_timezone_set('Asia/Riyadh');

// استخدام ar_SA لإجبار الأرقام على الظهور بالرسم العربي الأصيل
$formatter = new IntlDateFormatter(
    "ar_SA@calendar=islamic-uma", 
    IntlDateFormatter::FULL, 
    IntlDateFormatter::FULL, 
    'Asia/Riyadh', 
    IntlDateFormatter::TRADITIONAL
);

// تحديد التاريخ المطلوب (١٤٤٧/١٢/١٧)
$targetDate = DateTime::createFromFormat('Y-m-d', '2026-06-03'); 

// جلب اسم اليوم (الأربعاء)
$formatter->setPattern("EEEE"); 
$dayName = $formatter->format($targetDate); 

// جلب التاريخ كاملاً بالأرقام العربية (١٤٤٧/١٢/١٧)
$formatter->setPattern("yyyy/MM/dd");
$fullNumericDate = $formatter->format($targetDate);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            background: #0f172a;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Cairo', sans-serif;
            margin: 0;
        }

        /* حاوية التقويم المصممة كشريط أفقي يشبه الصورة */
        .calendar-horizontal-card {
            display: flex;
            align-items: center;
            width: 450px; /* عرض عريض يتناسب مع الشكل الأفقي */
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        /* كتلة اليوم الجانبية (الأيمن) */
        .card-day-block {
            background: linear-gradient(135deg, #e11d48 0%, #9f1239 100%);
            color: #ffffff;
            padding: 20px 30px;
            font-size: 2rem;
            font-weight: 900;
            text-align: center;
            min-width: 120px;
            box-shadow: -4px 0 15px rgba(0,0,0,0.1);
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border-left: 5px solid #fbbf24; /* الفاصل الذهبي أصبح عمودياً الآن */
        }

        /* كتلة التاريخ بالأرقام (الأيسر) */
        .card-date-block {
            flex-grow: 1;
            padding: 20px;
            text-align: center;
            background: #ffffff;
        }

        .full-date {
            font-size: 2.6rem; /* حجم خط عريض ومميز للأرقام */
            font-weight: 900;
            color: #1e293b;
            letter-spacing: 2px;
            direction: ltr; /* لضمان صحة اتجاه الشرطات بين الأرقام العربية */
            display: inline-block;
        }

        /* ثقوب التقويم الجمالية (تم نقلها لتناسب النمط الأفقي في الأعلى) */
        .calendar-horizontal-card::before, .calendar-horizontal-card::after {
            content: '';
            position: absolute;
            top: -8px;
            width: 14px;
            height: 14px;
            background: #0f172a; /* نفس لون خلفية الصفحة لتبدو كمقطع مثقوب */
            border-radius: 50%;
            box-shadow: inset 0 -3px 5px rgba(0,0,0,0.3);
            z-index: 10;
        }
        .calendar-horizontal-card::before { right: 80px; }
        .calendar-horizontal-card::after { left: 80px; }

    </style>
</head>
<body>

    <!-- كارت التقويم الأفقي -->
    <div class="calendar-horizontal-card">
        
        <!-- كتلة اسم اليوم في اليمين -->
        <div class="card-day-block">
            <?php echo $dayName; ?>
        </div>
        
        <!-- كتلة التاريخ الرقمي في اليسار -->
        <div class="card-date-block">
            <div class="full-date"><?php echo $fullNumericDate; ?></div>
        </div>

    </div>

</body>
</html>
