<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta property="og:title" content="مناسبات الجماعة | أفراحنا القادمة">
    <meta property="og:description" content="منصة لمتابعة وإضافة مناسبات وأفراح الجماعة تابعونا لمعرفة المواعيد والمواقع.">
    <meta property="og:image" content="https://d-service.pro/s/logo.png"> 
    <meta property="og:url" content="https://d-service.pro/s">
    <meta property="og:type" content="website">

    <title>مناسبات الجماعة | الرئيسية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f5f5f7;
            --card-bg: #ffffff;
            --text-primary: #1d1d1f;
            --text-secondary: #424245;
            --accent-blue: #0071e3;
            --accent-red: #ff3b30;
            --whatsapp-green: #25d366;
            --glass: rgba(255, 255, 255, 0.7);
            --gold-light: #f9e29b;
            --gold-dark: #b8860b;
            --gold-deep: #8b6508;
        }

        body {
            font-family: 'Tajawal', -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            margin: 0; padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* --- تأثيرات التحميل والدخول الاحترافي عند فتح الصفحة --- */
        .hero h1 { 
            font-size: 24px; font-weight: 700; margin: 0; 
            opacity: 0; transform: translateY(-20px);
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .hero p { 
            color: var(--text-secondary); font-size: 14px; margin-top: 5px; 
            opacity: 0; transform: translateY(-10px);
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards;
        }
        .nav-bar {
            position: fixed; top: 0; width: 100%; height: 52px;
            background: var(--glass);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            z-index: 1001; border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; animation: fadeIn 0.5s ease forwards;
        }
        .news-ticker-container {
            position: fixed; top: 52px; width: 100%; background: #000;
            color: #fff; z-index: 1000; height: 35px; display: flex;
            align-items: center; overflow: hidden; direction: ltr;
            opacity: 0; animation: fadeIn 0.5s ease 0.3s forwards;
        }

        @keyframes fadeInDown {
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }

        /* --- حالة الكروت قبل التمرير (مخفية ومزاحة لأسفل) --- */
        .event-card {
            background: var(--card-bg); border-radius: 28px; padding: 30px;
            display: flex; flex-direction: column; position: relative;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); 
            z-index: 5; overflow: hidden; 
            border: 1px solid rgba(0,0,0,0.03);
            
            /* إعدادات حركة التمرير */
            opacity: 0;
            transform: translateY(40px) scale(0.96);
            transition: opacity 0.7s cubic-bezier(0.215, 0.610, 0.355, 1), 
                        transform 0.7s cubic-bezier(0.215, 0.610, 0.355, 1),
                        box-shadow 0.4s ease;
        }

        /* --- حالة الكرت النشط عند ظهوره في نطاق الشاشة --- */
        .event-card.reveal-active {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* تأثير التمرير الفوقي اللطيف */
        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .event-card::before {
            content: ''; position: absolute; inset: 0; padding: 3px; border-radius: 28px; 
            background: linear-gradient(45deg, var(--gold-dark), var(--gold-light), var(--gold-dark));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            z-index: 2; animation: breathe 3s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes breathe { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

        /* --- نافذة عرض الصورة المحدثة بتأثير الزوم --- */
        .image-overlay {
            position: fixed; 
            top: 0; left: 0; 
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            z-index: 99999;
            display: none; 
            align-items: center; 
            justify-content: center;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-overlay.active { display: flex; opacity: 1; }

        .overlay-content { 
            position: relative; 
            width: 90%; 
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transform: scale(0.7);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .image-overlay.active .overlay-content { transform: scale(1); }

        .overlay-content img { 
            width: 100%;
            max-height: 80vh; 
            border-radius: 15px; 
            box-shadow: 0 0 50px rgba(0,0,0,0.8); 
            object-fit: contain;
        }

        .close-overlay {
            background: #fff; color: #000; 
            width: 50px; height: 50px;
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-weight: bold; font-size: 28px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            z-index: 100000;
        }

        /* --- أيقونة الواتساب تواصل معنا (تم تصغيرها وتنسيقها بشكل مريح) --- */
        .whatsapp-float {
            position: fixed;
            bottom: 24px;
            left: 24px;
            background-color: var(--whatsapp-green);
            color: #FFF;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            z-index: 1000;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .whatsapp-float:hover { transform: scale(1.1); box-shadow: 0 6px 16px rgba(0,0,0,0.35); }

        .nav-bar span { font-size: 16px; font-weight: 600; color: var(--text-primary); }

        .ticker-wrapper { display: flex; white-space: nowrap; animation: ticker-move-reverse 30s linear infinite; }
        .ticker-item { padding: 0 40px; font-size: 14px; font-weight: 500; direction: rtl; }

        @keyframes ticker-move-reverse { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }

        .hero { 
            position: fixed; top: 87px; left: 0; right: 0; text-align: center; 
            padding: 20px 20px; background: var(--glass); backdrop-filter: blur(15px);
            z-index: 999; border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .container { max-width: 1100px; margin: 190px auto 0; padding: 20px 20px 40px; }
        .event-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 32px; }

        .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; z-index: 10; }
        .date-container { display: flex; flex-direction: column; align-items: center; gap: 6px; }

        .calendar-icon {
            background: #ffffff; border-radius: 12px; min-width: 80px;
            text-align: center; border: 1.5px solid #efeff4; overflow: hidden;
        }

        .cal-header { background: var(--accent-red); color: #fff; font-size: 10px; font-weight: 700; padding: 3px 0; }
        .cal-number { color: var(--text-primary); font-size: 14px; font-weight: 700; padding: 6px 0; direction: ltr; }

        .countdown-tag {
            font-size: 11px; font-weight: 700; color: #fff;
            background: linear-gradient(135deg, var(--accent-blue), #0056b3);
            padding: 4px 10px; border-radius: 8px; visibility: hidden;
        }

        .day-tag { color: var(--gold-deep); font-weight: 800; font-size: 15px; background: rgba(184, 134, 11, 0.15); padding: 6px 16px; border-radius: 12px; }

        .host-title { font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 20px; z-index: 10; }

        .icon-badge {
            background: #ffffff; border: 1px solid #e5e5ea; border-radius: 10px;
            width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            font-size: 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); flex-shrink: 0;
        }

        .invitation-btn {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-deep));
            color: #fff; border: none; padding: 12px 20px; border-radius: 14px;
            font-size: 14px; font-weight: 700; cursor: pointer; display: flex;
            align-items: center; justify-content: center; gap: 8px; margin-top: 15px; width: 100%;
            transition: 0.3s; z-index: 20; position: relative;
        }

        .actions-wrapper { display: flex; gap: 8px; align-items: center; }

        .map-icon-btn, .cal-add-btn, .share-icon-btn {
            background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 5px;
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); z-index: 30;
            width: 24px; height: 24px; cursor: pointer;
        }
        .map-icon-btn:hover, .cal-add-btn:hover, .share-icon-btn:hover { background: #f0f0f0; transform: scale(1.1); }

        footer { text-align: center; padding: 60px 20px; color: var(--text-secondary); font-size: 13px; }

        /* --- ستايل بطاقة أقرب مناسبة (المتحركة والملفتة) --- */
        .next-event-banner {
            position: relative;
            padding: 24px;
            margin-bottom: 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(184, 134, 11, 0.15);
            overflow: hidden;
            animation: fadeInDown 0.8s ease forwards, floatBanner 5s ease-in-out infinite;
            z-index: 1;
        }

        /* الإطار المتحرك المتوهج */
        .next-event-banner::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: conic-gradient(
                from 0deg, 
                transparent 0%, 
                transparent 40%, 
                var(--gold-light) 50%, 
                var(--gold-dark) 60%, 
                transparent 100%
            );
            animation: rotateBorder 4s linear infinite;
            z-index: -2;
        }

        /* الطبقة الداخلية (لإخفاء منتصف الإطار وترك الحواف فقط) */
        .next-event-banner::after {
            content: '';
            position: absolute;
            inset: 3px; /* للتحكم بسماكة الإطار المتحرك */
            background: var(--card-bg);
            border-radius: 18px;
            z-index: -1;
        }

        /* شريط التنبيه (البادج) بالتدرج الأحمر الداكن الأنيق */
        .neb-badge {
            position: absolute;
            top: 0; right: 0;
            background: linear-gradient(270deg, #ff3b30, #b30000, #5a0000, #b30000);
            background-size: 300% 300%;
            color: #fff;
            font-size: 13px;
            padding: 8px 22px;
            border-bottom-left-radius: 16px;
            font-weight: 800;
            z-index: 2;
            box-shadow: -3px 3px 20px rgba(179, 0, 0, 0.5);
            text-shadow: 0px 1px 3px rgba(0,0,0,0.4);
            animation: flowingGradient 4s ease infinite, pulseGlow 2s infinite alternate;
        }

        .neb-details { 
            display: flex; flex-direction: column; gap: 6px; 
            margin-top: 15px; z-index: 2; 
        }
        .neb-host { font-size: 18px; font-weight: 800; color: var(--text-primary); }
        .neb-loc { font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 5px; font-weight: 500; }
        
        /* تكبير التاريخ وتنسيقه ليكون سميكاً وأنيقاً */
        .neb-date {
            background: linear-gradient(135deg, rgba(184, 134, 11, 0.1), rgba(184, 134, 11, 0.05));
            color: var(--gold-deep);
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 900;
            font-size: 20px;
            letter-spacing: 0.5px;
            white-space: nowrap;
            text-align: center;
            border: 1px solid rgba(184, 134, 11, 0.2);
            z-index: 2;
            backdrop-filter: blur(5px);
        }

        /* الحركات المستحدثة */
        @keyframes floatBanner {
            0%, 100% { transform: translateY(0); box-shadow: 0 15px 35px rgba(184, 134, 11, 0.15); }
            50% { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(184, 134, 11, 0.25); }
        }
        @keyframes rotateBorder {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* حركة التدرج اللوني المستمرة للبادج */
        @keyframes flowingGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* تعديل التوهج النابض ليتوافق مع الأحمر الداكن */
        @keyframes pulseGlow {
            from { box-shadow: -3px 3px 15px rgba(139, 0, 0, 0.5); }
            to { box-shadow: -3px 3px 25px rgba(255, 59, 48, 0.7); }
        }

        @media (max-width: 600px) {
            .next-event-banner { flex-direction: column; align-items: flex-start; gap: 15px; }
            .neb-date { align-self: stretch; }
            .neb-details { margin-top: 25px; }
        }
    </style>
</head>
<body>

<a href="https://wa.me/966505780710" class="whatsapp-float" target="_blank" title="تواصل معنا عبر الواتساب">
    <svg viewBox="0 0 448 512" width="24" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.5-2.8-23.2-8.5-44.2-27.1-16.4-14.6-27.4-32.7-30.6-38.2-3.2-5.6-.3-8.6 2.5-11.3 2.5-2.4 5.5-6.5 8.3-9.7 2.8-3.3 3.7-5.6 5.5-9.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.2 5.7 23.5 9.2 31.6 11.8 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
</a>

<div id="imageOverlay" class="image-overlay" onclick="closeImage()">
    <div class="overlay-content">
        <div class="close-overlay">✕</div>
        <img id="expandedImg" src="" alt="كرت الدعوة" onclick="event.stopPropagation()">
    </div>
</div>

<nav class="nav-bar"><span>كرت الدعوة</span></nav>

<?php
$settings_file = 'settings.json';
if (file_exists($settings_file)) {
    $settings = json_decode(file_get_contents($settings_file), true);
    if (isset($settings['news_status']) && $settings['news_status'] === 'on' && !empty($settings['news_text'])) {
        $news_text = htmlspecialchars($settings['news_text']);
        echo '<div class="news-ticker-container"><div class="ticker-wrapper"><div class="ticker-item">' . $news_text . ' • ' . $news_text . '</div></div></div>';
    }
}

function hijriToGregorian($hijriDate) {
    $clean = str_replace(['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'], ['0','1','2','3','4','5','6','7','8','9'], $hijriDate);
    $parts = explode('/', $clean);
    if(count($parts) !== 3) return date('Ymd');
    
    $year = (int)$parts[0];
    $month = (int)$parts[1];
    $day = (int)$parts[2];
    
    $jd = (int)((11 * $year + 3) / 30) + 354 * $year + 30 * $month - (int)(($month - 1) / 2) + $day + 1948440 - 385;
    if ($jd > 2299160) {
        $l = $jd + 68569;
        $n = (int)((4 * $l) / 146097);
        $l = $l - (int)((146097 * $n + 3) / 4);
        $i = (int)((4000 * ($l + 1)) / 1461001);
        $l = $l - (int)((1461 * $i) / 4) + 31;
        $j = (int)((80 * $l) / 2447);
        $d = $l - (int)((2447 * $j) / 80);
        $l = (int)($j / 11);
        $m = $j + 2 - 12 * $l;
        $y = 100 * ($n - 49) + $i + $l;
    }
    return sprintf('%04d%02d%02d', $y, $m, $d);
}
?>

<header class="hero">
    <h1>نرحب بضيوفنا الكرام</h1>
    <p>بكل حب، نبارك لكم أفراحنا ونتمنى لكم السعادة</p>
</header>

<div class="container">
    
    <?php
    // --- برمجة استخراج أقرب مناسبة قادمة ---
    $file = 'data.json';
    $closest_event = null;
    $data = [];
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (!empty($data)) {
            $today_greg = date('Ymd');
            $min_diff = null;

            foreach ($data as $row) {
                if(isset($row['date'])) {
                    $greg_date = hijriToGregorian($row['date']);
                    // التحقق من أن المناسبة إما اليوم أو في المستقبل
                    if ($greg_date >= $today_greg) {
                        $diff = (int)$greg_date - (int)$today_greg;
                        if ($min_diff === null || $diff < $min_diff) {
                            $min_diff = $diff;
                            $closest_event = $row;
                        }
                    }
                }
            }
        }
    }

    // --- عرض بطاقة أقرب مناسبة إذا وجدت ---
    if ($closest_event) {
        $c_date = isset($closest_event['date']) ? $closest_event['date'] : '';
        $c_host = isset($closest_event['host']) ? $closest_event['host'] : '';
        $c_hall = isset($closest_event['hall']) ? $closest_event['hall'] : '';
        $c_day  = isset($closest_event['day']) ? $closest_event['day'] : '';
        
        echo '
        <div class="next-event-banner">
            <div class="neb-badge"> المناسبة القادمة 🔔</div>
            <div class="neb-details">
                <div class="neb-host">'.$c_host.'</div>
                <div class="neb-loc">🏛️ '.$c_hall.'</div>
            </div>
            <div class="neb-date">
                '.$c_day.'<br><span style="font-size:15px; font-weight:700; opacity:0.9;">'.$c_date.'</span>
            </div>
        </div>';
    }
    ?>

    <div class="event-grid">
        <?php
        if (!empty($data)) {
            foreach ($data as $row) {
                $dateStr = isset($row['date']) ? $row['date'] : '';
                $invitationImg = (isset($row['invitation']) && !empty($row['invitation'])) ? $row['invitation'] : '';
                $dinnerTime = (isset($row['dinner_time']) && !empty($row['dinner_time'])) ? $row['dinner_time'] : '';
                $mapUrl = (isset($row['map_url']) && !empty($row['map_url'])) ? $row['map_url'] : '';
                $hostName = isset($row['host']) ? $row['host'] : '';
                $hallName = isset($row['hall']) ? $row['hall'] : '';
                $locationName = isset($row['loc']) ? $row['loc'] : '';
                
                $gregorianDate = hijriToGregorian($dateStr);

                echo '
                <div class="event-card">
                    <div class="card-header">
                        <span class="day-tag">'.(isset($row['day']) ? $row['day'] : '').'</span>
                        <div class="date-container">
                            <div class="calendar-icon">
                                <div class="cal-header">تاريخ المناسبة</div>
                                <div class="cal-number">'.$dateStr.'</div>
                            </div>
                            <div class="countdown-tag" data-hijri="'.$dateStr.'"></div>
                        </div>
                    </div>
                    <span style="font-size: 12px; color: var(--gold-deep); font-weight: 800; margin-bottom: 5px;">صاحب المناسبة</span>
                    <div class="host-title">'.$hostName.'</div>
                    
                    <div style="background: #f9f9fb; border-radius: 20px; padding: 15px; margin-top: auto; border: 1px solid rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px;">
                        
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="icon-badge">📍</div>
                                <div>
                                    <div style="color: var(--text-secondary); font-size: 11px; font-weight: 600;">الموقع</div>
                                    <div style="color: var(--text-primary); font-size: 14px; font-weight: 600;">'.$locationName.'</div>
                                </div>
                            </div>
                            <div class="actions-wrapper">
                                <button class="share-icon-btn" title="مشاركة تفاصيل المناسبة" onclick="shareEvent(\''.htmlspecialchars($hostName).'\', \''.$dateStr.'\', \''.htmlspecialchars($hallName).'\', \''.$mapUrl.'\')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0071e3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                </button>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(0,0,0,0.04); padding-top: 8px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="icon-badge">🏛️</div>
                                <div>
                                    <div style="color: var(--text-secondary); font-size: 11px; font-weight: 600;">القاعة أو القصر</div>
                                    <div style="color: var(--text-primary); font-size: 14px; font-weight: 600;">'.$hallName.'</div>
                                </div>
                            </div>
                            
                            <div class="actions-wrapper">';
                                if ($mapUrl) {
                                    echo '
                                    <a href="'.$mapUrl.'" target="_blank" class="map-icon-btn" title="الذهاب للموقع">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/39/Google_Maps_icon_%282015-2020%29.svg" width="24" height="24" alt="map">
                                    </a>';
                                }
                                echo '
                            </div>
                        </div>';
                        
                        if ($dinnerTime) {
                            echo '
                            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(0,0,0,0.04); padding-top: 8px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="icon-badge">⏰</div>
                                    <div>
                                        <div style="color: var(--text-secondary); font-size: 11px; font-weight: 600;">وقت العشاء</div>
                                        <div style="color: var(--text-primary); font-size: 14px; font-weight: 600;">'.$dinnerTime.'</div>
                                    </div>
                                </div>
                                
                                <div class="actions-wrapper">
                                    <a href="#" class="cal-add-btn" title="إضافة لتقويم الجوال" onclick="downloadICS(event, \''.htmlspecialchars($hostName).'\', \''.$gregorianDate.'\', \''.htmlspecialchars($hallName . ' - ' . $locationName).'\')">
                                        <svg viewBox="0 0 448 512" width="18" fill="var(--accent-blue)"><path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H48C21.5 64 0 85.5 0 112v32 320c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V144 112c0-26.5-21.5-48-48-48h-56V24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H152V24zM48 144H400V464H48V144zm72 64c0-13.3 10.7-24 24-24h24c13.3 0 24 10.7 24 24v24c0 13.3-10.7 24-24 24h-24c-13.3 0-24-10.7-24-24V208zm144 0c0-13.3 10.7-24 24-24h24c13.3 0 24 10.7 24 24v24c0 13.3-10.7 24-24 24h-24c-13.3 0-24-10.7-24-24V208zM120 336c0-13.3 10.7-24 24-24h24c13.3 0 24 10.7 24 24v24c0 13.3-10.7 24-24 24h-24c-13.3 0-24-10.7-24-24V336zm144 0c0-13.3 10.7-24 24-24h24c13.3 0 24 10.7 24 24v24c0 13.3-10.7 24-24 24h-24c-13.3 0-24-10.7-24-24V336z"/></svg>
                                    </a>
                                </div>
                            </div>';
                        }

                    echo '</div>';
                    
                    if($invitationImg) {
                        echo '<button type="button" class="invitation-btn" onclick="openImage(\''.htmlspecialchars($invitationImg).'\')">📩 عرض كرت الدعوة</button>';
                    }

                echo '</div>';
            }
        }
        ?>
    </div>
</div>

<script>
function openImage(imageSrc) {
    if(!imageSrc) return;
    const overlay = document.getElementById('imageOverlay');
    const expandedImg = document.getElementById('expandedImg');
    expandedImg.src = imageSrc;
    overlay.style.display = 'flex';
    setTimeout(() => { overlay.classList.add('active'); }, 10);
    document.body.style.overflow = 'hidden';
}

function closeImage() {
    const overlay = document.getElementById('imageOverlay');
    overlay.classList.remove('active');
    setTimeout(() => {
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

function shareEvent(host, date, hall, map) {
    const textStr = `🎯 كرت الدعوة\n\nصاحب المناسبة: ${host}\nالتاريخ: ${date}\nالمكان: ${hall}\n\nرابط موقع القاعة المباشر:\n${map || 'غير متوفر حالياً'}`;
    if (navigator.share) {
        navigator.share({
            title: 'مناسبات الجماعة',
            text: textStr,
            url: window.location.href
        }).catch(err => console.log(err));
    } else {
        const whatsappUrl = `https://wa.me/send?text=${encodeURIComponent(textStr)}`;
        window.open(whatsappUrl, '_blank');
    }
}

function downloadICS(e, host, gregDate, location) {
    e.preventDefault();
    const startTime = gregDate + "T200000"; 
    const endTime = gregDate + "T230000";   
    const dtStamp = new Date().toISOString().replace(/[-:]/g, "").split(".")[0] + "Z";
    const uniqueID = "uid_" + Math.random().toString(36).substr(2, 9) + "@d-service.pro";
    
    const icsContent = [
        "BEGIN:VCALENDAR",
        "VERSION:2.0",
        "PRODID:-//D-Service//Community Events//EN",
        "METHOD:PUBLISH",
        "BEGIN:VEVENT",
        "UID:" + uniqueID,
        "DTSTAMP:" + dtStamp,
        "SUMMARY:مناسبة " + host,
        "DESCRIPTION:مناسبة وأفراح الجماعة لـ " + host,
        "LOCATION:" + location,
        "DTSTART:" + startTime,
        "DTEND:" + endTime,
        "BEGIN:VALARM",
        "ACTION:DISPLAY",
        "DESCRIPTION:تذكير بمناسبة " + host,
        "TRIGGER:-PT2H",
        "END:VALARM",
        "END:VEVENT",
        "END:VCALENDAR"
    ].join("\r\n");

    const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = window.URL.createObjectURL(blob);
    link.setAttribute('download', 'event-' + host.replace(/\s+/g, '_') + '.ics');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function initCountdown() {
    const tags = document.querySelectorAll('.countdown-tag');
    const now = new Date();
    const todayParts = new Intl.DateTimeFormat('en-u-ca-islamic-umalqura-nu-latn', {
        day: 'numeric', month: 'numeric', year: 'numeric'
    }).formatToParts(now);

    const tDay = parseInt(todayParts.find(p => p.type === 'day').value);
    const tMonth = parseInt(todayParts.find(p => p.type === 'month').value);
    const tYear = parseInt(todayParts.find(p => p.type === 'year').value);

    function getDays(hStr) {
        if(!hStr) return null;
        let clean = hStr.replace(/[٠-٩]/g, d => "٠١٢٣٤٥٦٧٨٩".indexOf(d));
        let p = clean.split('/');
        if (p.length !== 3) return null;
        return (parseInt(p[0]) * 354.36) + (parseInt(p[1]) * 29.5) + parseInt(p[2]);
    }

    const todayTotal = (tYear * 354.36) + (tMonth * 29.5) + tDay;

    tags.forEach(tag => {
        const eventTotal = getDays(tag.getAttribute('data-hijri'));
        if (!eventTotal) return;
        const diff = Math.ceil(eventTotal - todayTotal);
        if (diff > 0) {
            tag.innerHTML = "بقي " + diff.toLocaleString('ar-SA') + " يوم";
            tag.style.visibility = 'visible';
        } else if (diff === 0) {
            tag.innerHTML = "اليوم 🎉";
            tag.style.background = "linear-gradient(135deg, var(--accent-red), #b3241d)";
            tag.style.visibility = 'visible';
        } else {
            tag.style.display = 'none'; 
        }
    });
}

function initScrollReveal() {
    const cards = document.querySelectorAll('.event-card');
    const revealOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15
    };

    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-active');
            } else {
                if (entry.boundingClientRect.top > 0) {
                    entry.target.classList.remove('reveal-active');
                }
            }
        });
    }, revealOptions);

    cards.forEach(card => cardObserver.observe(card));
}

window.addEventListener('DOMContentLoaded', () => {
    initCountdown();
    initScrollReveal();
});
</script>

<footer>&copy; <?php echo date("Y"); ?> تطوير وبرمجة عثمان الزبيدي</footer>
</body>
</html>