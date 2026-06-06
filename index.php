<?php
// --- ضبط المنطقة الزمنية للسعودية ---
date_default_timezone_set('Asia/Riyadh');

// --- معالجة إضافة تهنئة جديدة ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sender_name'], $_POST['message_text'])) {
    $name = trim(strip_tags($_POST['sender_name']));
    $msg = trim(strip_tags($_POST['message_text']));
    if ($name && $msg) {
        $greetings_file = 'greetings.json';
        $greetings = [];
        if (file_exists($greetings_file)) {
            $data = json_decode(file_get_contents($greetings_file), true);
            if (is_array($data)) {
                $greetings = $data;
            }
        }
        // إضافة التهنئة الجديدة في بداية المصفوفة
        array_unshift($greetings, ['name' => $name, 'message' => $msg]);
        // الاحتفاظ بآخر 30 تهنئة فقط لعدم إثقال الصفحة
        $greetings = array_slice($greetings, 0, 30);
        file_put_contents($greetings_file, json_encode($greetings, JSON_UNESCAPED_UNICODE));
    }
    // إعادة التوجيه لمنع تكرار الإرسال عند تحديث الصفحة
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- دوال وتجهيز البيانات الأساسية ---

// 1. تحويل التاريخ الهجري (النصي) إلى رقم صحيح للمقارنة الدقيقة
function parseHijriToInt($hijriDate) {
    $clean = str_replace(['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'], ['0','1','2','3','4','5','6','7','8','9'], $hijriDate);
    $parts = explode('/', $clean);
    if(count($parts) !== 3) return 0;
    return (int)sprintf('%04d%02d%02d', $parts[0], $parts[1], $parts[2]);
}

// 2. جلب تاريخ اليوم الهجري برقم صحيح بناءً على توقيت السعودية
function getTodayHijriInt() {
    if (class_exists('IntlDateFormatter')) {
        $fmt = new IntlDateFormatter('en_US@calendar=islamic-umalqura', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Asia/Riyadh', IntlDateFormatter::TRADITIONAL, 'yyyyMMdd');
        return (int)$fmt->format(time());
    }
    $y = (int)date('Y'); $m = (int)date('m'); $d = (int)date('d');
    $jd = gregoriantojd($m, $d, $y);
    $l = $jd - 1948440 + 10632;
    $n = (int)(($l - 1) / 10631);
    $l = $l - 10631 * $n + 354;
    $j = ((int)((10985 - $l) / 5316)) * ((int)(50 * $l / 17719)) + ((int)($l / 5670)) * ((int)(43 * $l / 15238));
    $l = $l - ((int)((30 - $j) / 15)) * ((int)((17719 * $j) / 50)) - ((int)($j / 16)) * ((int)((15238 * $j) / 43)) + 29;
    $month = (int)(24 * $l / 709);
    $day = $l - (int)(709 * $month / 24);
    $year = 30 * $n + $j - 30;
    return (int)sprintf('%04d%02d%02d', $year, $month, $day);
}

// 3. دالة التحويل للميلادي
function hijriToGregorian($hijriDate) {
    $clean = str_replace(['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'], ['0','1','2','3','4','5','6','7','8','9'], $hijriDate);
    $parts = explode('/', $clean);
    if(count($parts) !== 3) return date('Ymd');
    $year = (int)$parts[0]; $month = (int)$parts[1]; $day = (int)$parts[2];
    $jd = (int)((11 * $year + 3) / 30) + 354 * $year + 30 * $month - (int)(($month - 1) / 2) + $day + 1948440 - 385;
    if ($jd > 2299160) {
        $l = $jd + 68569; $n = (int)((4 * $l) / 146097); $l = $l - (int)((146097 * $n + 3) / 4);
        $i = (int)((4000 * ($l + 1)) / 1461001); $l = $l - (int)((1461 * $i) / 4) + 31;
        $j = (int)((80 * $l) / 2447); $d = $l - (int)((2447 * $j) / 80);
        $l = (int)($j / 11); $m = $j + 2 - 12 * $l; $y = 100 * ($n - 49) + $i + $l;
    }
    return sprintf('%04d%02d%02d', $y, $m, $d);
}

// جلب المناسبات
$events_file = 'data.json';
$events_data = [];
if (file_exists($events_file)) {
    $fetched_data = json_decode(file_get_contents($events_file), true);
    if (is_array($fetched_data)) {
        $events_data = $fetched_data;
    }
}

$today_hijri_int = getTodayHijriInt();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    
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
        
        .menu-btn {
            position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
            background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-primary);
            display: flex; align-items: center; justify-content: center; width: 35px; height: 35px; border-radius: 8px; transition: 0.3s;
        }
        .menu-btn:hover { background: rgba(0,0,0,0.05); }

        .news-ticker-container {
            position: fixed; top: 52px; width: 100%; background: #000;
            color: #fff; z-index: 1000; height: 35px; display: flex;
            align-items: center; overflow: hidden; direction: ltr;
            opacity: 0; animation: fadeIn 0.5s ease 0.3s forwards;
        }

        @keyframes fadeInDown { to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { to { opacity: 1; } }

        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
            z-index: 10005; opacity: 0; visibility: hidden; transition: 0.3s ease;
        }
        .sidebar-overlay.active { opacity: 1; visibility: visible; }
        
        .sidebar {
            position: fixed; top: 0; right: -320px; width: 280px; height: 100%;
            background: var(--glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            z-index: 10006; box-shadow: -5px 0 30px rgba(0,0,0,0.1);
            transition: right 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: flex; flex-direction: column; padding-top: 80px;
        }
        .sidebar.active { right: 0; }
        .sidebar-link {
            padding: 18px 25px; font-size: 15px; font-weight: 700; color: var(--text-primary);
            border-bottom: 1px solid rgba(0,0,0,0.03); cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-link:hover { background: rgba(184, 134, 11, 0.08); color: var(--gold-dark); padding-right: 30px;}
        .close-sidebar {
            position: absolute; top: 20px; left: 20px; font-size: 20px; cursor: pointer;
            color: var(--text-secondary); background: #fff; width: 35px; height: 35px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08); transition: 0.3s;
        }
        .close-sidebar:hover { background: var(--accent-red); color: #fff; transform: scale(1.1); }
        .sidebar-logo { text-align: center; margin-bottom: 20px; color: var(--gold-dark); font-weight: 900; font-size: 22px; }

        /* --- تصميم بطاقة المناسبة الأنيق --- */
        .event-card {
            background: var(--card-bg); 
            border-radius: 24px; /* زوايا ناعمة */
            padding: 25px;
            display: flex; flex-direction: column; position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); /* ظل أنيق وخفيف */
            z-index: 5; overflow: hidden; 
            border: 1px solid rgba(0,0,0,0.03);
            border-top: 5px solid var(--gold-dark); /* لمسة ذهبية علوية فخمة */
            opacity: 0; transform: translateY(40px) scale(0.96);
            transition: opacity 0.7s cubic-bezier(0.215, 0.610, 0.355, 1), 
                        transform 0.7s cubic-bezier(0.215, 0.610, 0.355, 1), box-shadow 0.4s ease;
        }
        .event-card.reveal-active { opacity: 1; transform: translateY(0) scale(1); }
        .event-card:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 15px 40px rgba(184, 134, 11, 0.12); /* توهج ذهبي خفيف عند التأشير */
        }
        .event-card::before {
            content: ''; position: absolute; inset: 0; padding: 3px; border-radius: 24px; 
            background: linear-gradient(45deg, var(--gold-dark), var(--gold-light), var(--gold-dark));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude;
            z-index: 2; animation: breathe 3s ease-in-out infinite; pointer-events: none;
        }
        @keyframes breathe { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

        .image-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); z-index: 99999; display: none; 
            align-items: center; justify-content: center; backdrop-filter: blur(15px);
            opacity: 0; transition: opacity 0.3s ease;
        }
        .image-overlay.active { display: flex; opacity: 1; }
        .overlay-content { 
            position: relative; width: 90%; max-width: 600px; display: flex; flex-direction: column;
            align-items: center; transform: scale(0.7); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .image-overlay.active .overlay-content { transform: scale(1); }
        .overlay-content img { width: 100%; max-height: 80vh; border-radius: 15px; box-shadow: 0 0 50px rgba(0,0,0,0.8); object-fit: contain; }
        .close-overlay {
            background: #fff; color: #000; width: 50px; height: 50px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; cursor: pointer; 
            font-weight: bold; font-size: 28px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.5); z-index: 100000;
        }

        .whatsapp-float {
            position: fixed; bottom: 24px; left: 24px; background-color: var(--whatsapp-green);
            color: #FFF; border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            z-index: 1000; width: 48px; height: 48px; display: flex; align-items: center;
            justify-content: center; text-decoration: none; transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
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

        .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; z-index: 10; }
        
        /* --- التنسيق الجديد لخانة التاريخ واليوم (عمودي وأنيق) --- */
        .date-day-box {
            display: flex;
            flex-direction: column; /* جعل اليوم فوق والتاريخ تحت */
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(184, 134, 11, 0.08), rgba(184, 134, 11, 0.02));
            padding: 10px 20px;
            border-radius: 16px;
            border: 1px solid rgba(184, 134, 11, 0.15);
            box-shadow: 0 4px 15px rgba(184, 134, 11, 0.05);
            min-width: 80px;
        }
        .day-name {
            font-size: 16px;
            font-weight: 900;
            color: var(--gold-dark);
            margin-bottom: 5px; /* مسافة بسيطة بين اليوم والتاريخ */
            padding: 0;
            border: none; /* إزالة الخط الجانبي القديم */
        }
        .date-num {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-secondary);
            letter-spacing: 0.5px;
            text-decoration: none !important;
            pointer-events: none;
        }

        .countdown-tag {
            font-size: 12px; font-weight: 800; color: var(--accent-blue);
            background: rgba(0, 113, 227, 0.08); border: 1px solid rgba(0, 113, 227, 0.2);
            padding: 6px 14px; border-radius: 20px; visibility: hidden;
            display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0, 113, 227, 0.05);
            margin-top: 5px; /* محاذاة أفضل مع شكل التاريخ الجديد */
        }
        .countdown-tag.today {
            color: var(--accent-red);
            background: rgba(255, 59, 48, 0.08); border: 1px solid rgba(255, 59, 48, 0.2);
            box-shadow: 0 2px 8px rgba(255, 59, 48, 0.05);
        }
        
        .host-title { font-size: 19px; font-weight: 800; color: var(--text-primary); margin-bottom: 20px; z-index: 10; }

        .icon-badge {
            background: #ffffff; border: 1px solid #e5e5ea; border-radius: 10px; width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); flex-shrink: 0;
        }

        .invitation-btn {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-deep));
            color: #fff; border: none; padding: 12px 20px; border-radius: 14px; font-size: 14px; font-weight: 700; 
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 15px; width: 100%;
            transition: 0.3s; z-index: 20; position: relative;
        }

        .actions-wrapper { display: flex; gap: 8px; align-items: center; }
        .map-icon-btn, .cal-add-btn, .share-icon-btn {
            background: #fff; border: 1px solid #ddd; border-radius: 10px; padding: 5px;
            display: flex; align-items: center; justify-content: center; transition: 0.3s; 
            text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); z-index: 30; width: 26px; height: 26px; cursor: pointer;
        }
        .map-icon-btn:hover, .cal-add-btn:hover, .share-icon-btn:hover { background: #f0f0f0; transform: scale(1.1); }

        footer { text-align: center; padding: 60px 20px; color: var(--text-secondary); font-size: 13px; }

        .next-event-banner {
            position: relative; padding: 24px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;
            border-radius: 20px; box-shadow: 0 15px 35px rgba(184, 134, 11, 0.15); overflow: hidden;
            animation: fadeInDown 0.8s ease forwards, floatBanner 5s ease-in-out infinite; z-index: 1;
        }
        .next-event-banner::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: conic-gradient(from 0deg, transparent 0%, transparent 40%, var(--gold-light) 50%, var(--gold-dark) 60%, transparent 100%);
            animation: rotateBorder 4s linear infinite; z-index: -2;
        }
        .next-event-banner::after { content: ''; position: absolute; inset: 3px; background: var(--card-bg); border-radius: 18px; z-index: -1; }
        
        .neb-badge {
            position: absolute; top: 0; right: 0; background: linear-gradient(270deg, #ff3b30, #b30000, #5a0000, #b30000);
            background-size: 300% 300%; color: #fff; font-size: 13px; padding: 8px 22px; border-bottom-left-radius: 16px;
            font-weight: 800; z-index: 2; box-shadow: -3px 3px 20px rgba(179, 0, 0, 0.5); text-shadow: 0px 1px 3px rgba(0,0,0,0.4);
            animation: flowingGradient 4s ease infinite, pulseGlow 2s infinite alternate;
        }
        .neb-details { display: flex; flex-direction: column; gap: 6px; margin-top: 15px; z-index: 2; }
        .neb-host { font-size: 18px; font-weight: 800; color: var(--text-primary); }
        .neb-loc { font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 5px; font-weight: 500; }
        
        .neb-date {
            background: linear-gradient(135deg, rgba(184, 134, 11, 0.1), rgba(184, 134, 11, 0.05)); color: var(--gold-deep);
            padding: 12px 24px; border-radius: 14px; font-weight: 900; font-size: 20px; letter-spacing: 0.5px;
            white-space: nowrap; text-align: center; border: 1px solid rgba(184, 134, 11, 0.2); z-index: 2; backdrop-filter: blur(5px);
        }

        @keyframes floatBanner { 0%, 100% { transform: translateY(0); box-shadow: 0 15px 35px rgba(184, 134, 11, 0.15); } 50% { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(184, 134, 11, 0.25); } }
        @keyframes rotateBorder { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes flowingGradient { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        @keyframes pulseGlow { from { box-shadow: -3px 3px 15px rgba(139, 0, 0, 0.5); } to { box-shadow: -3px 3px 25px rgba(255, 59, 48, 0.7); } }

        @media (max-width: 600px) { 
            .next-event-banner { flex-direction: column; align-items: flex-start; gap: 15px; } 
            .next-event-banner > div:last-child { align-self: stretch; justify-content: space-between; flex-direction: row-reverse; } 
            .neb-details { margin-top: 25px; } 
        }

        .greetings-section { margin-bottom: 35px; padding: 0; animation: fadeInDown 1s ease forwards; }
        .greetings-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding: 0 10px; }
        .greetings-title { font-size: 16px; font-weight: 800; color: var(--gold-dark); display: flex; align-items: center; gap: 6px; }
        .add-greeting-btn {
            background: linear-gradient(135deg, rgba(184, 134, 11, 0.1), rgba(184, 134, 11, 0.05)); color: var(--gold-deep); 
            border: 1px solid rgba(184, 134, 11, 0.2); padding: 6px 14px; border-radius: 12px; font-size: 13px; 
            font-weight: 800; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 4px; backdrop-filter: blur(5px);
        }
        .add-greeting-btn:hover { background: var(--gold-dark); color: #fff; transform: translateY(-2px); }
        
        .greetings-slider { 
            overflow: hidden; width: 100%; position: relative; direction: ltr; 
            -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent); 
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent); 
            padding: 5px 0; 
        }
        .greetings-track { display: flex; gap: 15px; width: max-content; touch-action: pan-y; }
        
        .greeting-card {
            background: var(--card-bg); border: 1px solid rgba(184, 134, 11, 0.15); border-radius: 16px; padding: 15px; 
            min-width: 240px; max-width: 280px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); display: flex; flex-direction: column; 
            gap: 8px; direction: rtl; transition: 0.3s;
        }
        .greeting-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(184, 134, 11, 0.1); }
        .gc-header { display: flex; align-items: center; gap: 10px; }
        .gc-avatar { width: 32px; height: 32px; background: linear-gradient(135deg, var(--gold-light), var(--gold-dark)); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex-shrink: 0;}
        .gc-name { font-size: 14px; font-weight: 800; color: var(--text-primary); }
        .gc-msg { font-size: 13px; color: var(--text-secondary); line-height: 1.6; font-weight: 500; white-space: normal;}

        .g-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); z-index: 100000; display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); opacity: 0; transition: opacity 0.3s ease;
        }
        .g-modal-overlay.active { display: flex; opacity: 1; }
        .g-modal-box {
            background: #fff; border-radius: 24px; padding: 30px; width: 90%; max-width: 400px; max-height: 85vh; overflow-y: auto;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2); transform: scale(0.8); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative; direction: rtl;
        }
        .g-modal-overlay.active .g-modal-box { transform: scale(1); }
        .g-modal-close {
            position: absolute; top: 15px; left: 15px; width: 30px; height: 30px;
            background: #f5f5f7; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-weight: bold; color: var(--text-secondary); transition: 0.3s;
        }
        .g-modal-close:hover { background: #e5e5ea; color: #000; }
        .g-modal-title { font-size: 20px; font-weight: 800; margin-bottom: 20px; color: var(--text-primary); text-align: center; }
        .g-input-group { margin-bottom: 15px; text-align: right; }
        .g-input-group label { display: block; font-size: 13px; font-weight: 700; color: var(--text-secondary); margin-bottom: 8px; }
        .g-input-group input, .g-input-group textarea {
            width: 100%; box-sizing: border-box; padding: 12px 15px; border: 1.5px solid #e5e5ea; border-radius: 14px;
            font-family: 'Tajawal', sans-serif; font-size: 14px; transition: 0.3s; background: #f9f9fb; color: var(--text-primary);
        }
        .g-input-group input:focus, .g-input-group textarea:focus { border-color: var(--gold-dark); outline: none; background: #fff; }
        .g-submit-btn {
            width: 100%; padding: 14px; background: linear-gradient(135deg, var(--gold-dark), var(--gold-deep));
            color: #fff; border: none; border-radius: 14px; font-size: 15px; font-weight: 800; cursor: pointer; transition: 0.3s;
        }
        .g-submit-btn:hover { box-shadow: 0 5px 15px rgba(184, 134, 11, 0.3); transform: translateY(-2px); }

        .past-event-item {
            background: #f9f9fb; border: 1px solid rgba(0,0,0,0.04); border-radius: 12px; padding: 12px 15px;
            margin-bottom: 12px; display: flex; flex-direction: column; gap: 5px; border-right: 4px solid var(--gold-light);
        }
        .past-event-item .pe-title { font-weight: 800; color: var(--text-primary); font-size: 15px; }
        .past-event-item .pe-date { font-size: 12px; color: var(--text-secondary); font-weight: 600; display: flex; gap: 10px; }
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

<div id="greetingModal" class="g-modal-overlay" onclick="if(event.target===this) closeInfoModal('greetingModal')">
    <div class="g-modal-box">
        <div class="g-modal-close" onclick="closeInfoModal('greetingModal')">✕</div>
        <div class="g-modal-title">أضف تهنئة جديدة 🎈</div>
        <form method="POST" action="">
            <div class="g-input-group">
                <label>الاسم الكريم</label>
                <input type="text" name="sender_name" placeholder="اكتب اسمك هنا..." required maxlength="50">
            </div>
            <div class="g-input-group">
                <label>نص التهنئة</label>
                <textarea name="message_text" rows="3" placeholder="اكتب تهنئتك ومشاعرك هنا..." required maxlength="200"></textarea>
            </div>
            <button type="submit" class="g-submit-btn">إرسال التهنئة</button>
        </form>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="sidebar" id="sidebar">
    <div class="close-sidebar" onclick="toggleSidebar()">✕</div>
    <div class="sidebar-logo">مناسبات الجماعة</div>
    <div class="sidebar-link" onclick="openInfoModal('pastEventsModal')">📜 سجل المناسبات الماضية</div>
    <div class="sidebar-link" onclick="openInfoModal('whyModal')">💡 لماذا تم إنشاء هذا الموقع</div>
    <div class="sidebar-link" onclick="openInfoModal('contactModal')">📞 التنسيق وإضافة مناسبة</div>
</div>

<div id="pastEventsModal" class="g-modal-overlay" onclick="if(event.target===this) closeInfoModal('pastEventsModal')">
    <div class="g-modal-box">
        <div class="g-modal-close" onclick="closeInfoModal('pastEventsModal')">✕</div>
        <div class="g-modal-title">سجل المناسبات الماضية</div>
        <div>
            <?php
            $hasPast = false;
            if (!empty($events_data)) {
                $reversed_events = array_reverse($events_data);
                foreach ($reversed_events as $row) {
                    if(isset($row['date'])) {
                        $event_hijri_int = parseHijriToInt($row['date']);
                        if ($event_hijri_int > 0 && $event_hijri_int < $today_hijri_int) {
                            $hasPast = true;
                            echo '<div class="past-event-item">
                                    <div class="pe-title">'.htmlspecialchars($row['host']).'</div>
                                    <div class="pe-date"><span>📅 '.htmlspecialchars($row['date']).'</span> <span>🏛️ '.htmlspecialchars($row['hall']).'</span></div>
                                  </div>';
                        }
                    }
                }
            }
            if(!$hasPast) echo '<p style="text-align:center; color:var(--text-secondary); font-weight:600;">لا توجد مناسبات منتهية في السجل حالياً.</p>';
            ?>
        </div>
    </div>
</div>

<div id="whyModal" class="g-modal-overlay" onclick="if(event.target===this) closeInfoModal('whyModal')">
    <div class="g-modal-box">
        <div class="g-modal-close" onclick="closeInfoModal('whyModal')">✕</div>
        <div class="g-modal-title">الهدف من الموقع</div>
        <p style="text-align: center; line-height: 1.8; color: var(--text-secondary); font-weight: 600; font-size: 16px;">
            تم إنشاء الموقع لتعرف على مواعيد المناسبة ومراعاة تواريخ المناسبات القادمة والتنسيق بينها.
        </p>
    </div>
</div>

<div id="contactModal" class="g-modal-overlay" onclick="if(event.target===this) closeInfoModal('contactModal')">
    <div class="g-modal-box">
        <div class="g-modal-close" onclick="closeInfoModal('contactModal')">✕</div>
        <div class="g-modal-title">التنسيق وإضافة مناسبة</div>
        <div style="text-align: center; line-height: 1.8; color: var(--text-primary); font-weight: 600;">
            <p>للتواصل والتنسيق وإضافة مناسبة</p>
            <hr style="border:0; border-top: 1px solid #eee; margin: 20px 0;">
            <p style="color: var(--text-secondary); font-size: 14px;">مدير الموقع</p>
            <p style="font-size: 18px; color: var(--gold-dark); margin: 5px 0;">الاستاذ : طلال بن زاهر الزبيدي</p>
            <p style="font-size: 24px; font-weight: 800; margin-top: 10px; direction: ltr;">
                <a href="tel:0505780710" style="color: var(--accent-blue); text-decoration: none;">0505780710</a>
            </p>
        </div>
    </div>
</div>

<nav class="nav-bar">
    <span>كرت الدعوة</span>
    <button class="menu-btn" onclick="toggleSidebar()" aria-label="القائمة">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>
</nav>

<?php
$settings_file = 'settings.json';
if (file_exists($settings_file)) {
    $settings = json_decode(file_get_contents($settings_file), true);
    if (isset($settings['news_status']) && $settings['news_status'] === 'on' && !empty($settings['news_text'])) {
        $news_text = htmlspecialchars($settings['news_text']);
        echo '<div class="news-ticker-container"><div class="ticker-wrapper"><div class="ticker-item">' . $news_text . ' • ' . $news_text . '</div></div></div>';
    }
}
?>

<header class="hero">
    <h1>نرحب بضيوفنا الكرام</h1>
    <p>بكل حب، نبارك لكم أفراحنا ونتمنى لكم السعادة</p>
</header>

<div class="container">
    <?php
    $closest_event = null;
    $min_diff = null;

    if (!empty($events_data)) {
        foreach ($events_data as $row) {
            if(isset($row['date'])) {
                $event_int = parseHijriToInt($row['date']);
                if ($event_int >= $today_hijri_int) {
                    $diff = $event_int - $today_hijri_int;
                    if ($min_diff === null || $diff < $min_diff) {
                        $min_diff = $diff;
                        $closest_event = $row;
                    }
                }
            }
        }
    }

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
            <div style="display: flex; align-items: center; gap: 12px; z-index: 2;">
                <span class="countdown-tag" data-hijri="'.$c_date.'" style="padding: 6px 14px; font-size: 13px;"></span>
                <div class="neb-date">
                    '.$c_day.'<br><span style="font-size:15px; font-weight:700; opacity:0.9;">'.$c_date.'</span>
                </div>
            </div>
        </div>';

        $greetings_file = 'greetings.json';
        $greetings_html = '';
        if (file_exists($greetings_file)) {
            $greetings_data = json_decode(file_get_contents($greetings_file), true);
            if (!empty($greetings_data) && is_array($greetings_data)) {
                foreach ($greetings_data as $g) {
                    $n = htmlspecialchars($g['name']);
                    $m = htmlspecialchars($g['message']);
                    $initial = mb_substr($n, 0, 1, 'UTF-8');
                    $greetings_html .= '
                    <div class="greeting-card">
                        <div class="gc-header">
                            <div class="gc-avatar">'.$initial.'</div>
                            <div class="gc-name">'.$n.'</div>
                        </div>
                        <div class="gc-msg">'.$m.'</div>
                    </div>';
                }
            }
        }
        
        $track_content = $greetings_html ? $greetings_html . $greetings_html : '<div style="direction:rtl; padding:15px; font-weight:600; color:var(--text-secondary); text-align:center; width:100%;">لا توجد تهاني بعد، كُن أول المهنئين! 🎈</div>';

        echo '
        <div class="greetings-section">
            <div class="greetings-header">
                <div class="greetings-title">💌 تهاني وتبريكات</div>
                <button class="add-greeting-btn" onclick="openInfoModal(\'greetingModal\')">
                    <svg viewBox="0 0 448 512" width="12" fill="currentColor"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg>
                    أضف تهنئة
                </button>
            </div>
            <div class="greetings-slider">
                <div class="greetings-track">
                    ' . $track_content . '
                </div>
            </div>
        </div>';
    }
    ?>

    <div class="event-grid">
        <?php
        if (!empty($events_data)) {
            // فرز المناسبات القادمة لتكون الأقرب تاريخاً في البداية
            $display_events = [];
            foreach ($events_data as $row) {
                $sort_val = 99999999; // قيمة افتراضية للمناسبات بدون تاريخ (لتظهر في النهاية)
                if(isset($row['date'])) {
                    $event_int = parseHijriToInt($row['date']);
                    // إخفاء المناسبات المنتهية
                    if ($event_int < $today_hijri_int) {
                        continue;
                    }
                    $sort_val = $event_int;
                }
                $row['sort_val'] = $sort_val;
                $display_events[] = $row;
            }
            
            // ترتيب المصفوفة تصاعدياً بناءً على التاريخ
            usort($display_events, function($a, $b) {
                if ($a['sort_val'] == $b['sort_val']) return 0;
                return ($a['sort_val'] < $b['sort_val']) ? -1 : 1;
            });

            // عرض البطاقات بعد ترتيبها
            foreach ($display_events as $row) {
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
                        
                        <div class="date-day-box">
                            <span class="day-name">'.(isset($row['day']) ? $row['day'] : '').'</span>
                            <span class="date-num">'.$dateStr.'</span>
                        </div>
                        
                        <div style="display: flex; align-items: center;">
                            <span class="countdown-tag" data-hijri="'.$dateStr.'"></span>
                        </div>
                        
                    </div>
                    
                    <span style="font-size: 13px; color: var(--gold-deep); font-weight: 800; margin-bottom: 5px;">صاحب المناسبة</span>
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
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : 'auto';
}

function openInfoModal(modalId) {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if(sidebar.classList.contains('active')){
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    }

    const modal = document.getElementById(modalId);
    modal.style.display = 'flex';
    setTimeout(() => { modal.classList.add('active'); }, 10);
    document.body.style.overflow = 'hidden';
}

function closeInfoModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

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
            tag.innerHTML = "⏳ بقي " + diff.toLocaleString('ar-SA') + " يوم";
            tag.style.visibility = 'visible';
            tag.classList.remove('today');
        } else if (diff === 0) {
            tag.innerHTML = "اليوم 🎉";
            tag.classList.add('today'); 
            tag.style.visibility = 'visible';
        } else {
            tag.style.display = 'none'; 
        }
    });
}

function initScrollReveal() {
    const cards = document.querySelectorAll('.event-card');
    const revealOptions = { root: null, rootMargin: '0px', threshold: 0.15 };

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

// السكربت الخاص بشريط التهاني
function initGreetingsSlider() {
    const track = document.querySelector('.greetings-track');
    const slider = document.querySelector('.greetings-slider');
    const cards = document.querySelectorAll('.greeting-card');
    
    if (!track || !slider || cards.length === 0) return;
    
    let isDown = false;
    let startX;
    let currentTranslate = 0;
    let prevTranslate = 0;
    let animationId;
    
    const speed = 0.6; 
    const gap = 15;
    
    let halfWidth = (track.scrollWidth + gap) / 2;

    window.addEventListener('resize', () => {
        halfWidth = (track.scrollWidth + gap) / 2;
    });

    currentTranslate = -halfWidth;

    function autoScroll() {
        if (!isDown) {
            currentTranslate += speed;
            if (currentTranslate >= 0) {
                currentTranslate = -halfWidth;
            }
            setTransform(currentTranslate);
        }
        animationId = requestAnimationFrame(autoScroll);
    }

    function setTransform(x) {
        track.style.transform = `translateX(${x}px)`;
    }

    track.addEventListener('mousedown', (e) => {
        isDown = true;
        track.style.cursor = 'grabbing';
        startX = e.pageX;
        prevTranslate = currentTranslate;
        cancelAnimationFrame(animationId);
    });
    
    window.addEventListener('mouseup', () => {
        if(isDown) {
            isDown = false;
            track.style.cursor = 'grab';
            autoScroll();
        }
    });
    
    window.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const walk = (e.pageX - startX) * 1.5; 
        currentTranslate = prevTranslate + walk;
        
        if (currentTranslate > 0) currentTranslate = -halfWidth + (currentTranslate % halfWidth);
        if (currentTranslate < -halfWidth) currentTranslate = (currentTranslate % halfWidth);
        
        setTransform(currentTranslate);
    });

    track.addEventListener('touchstart', (e) => {
        isDown = true;
        startX = e.touches[0].pageX;
        prevTranslate = currentTranslate;
        cancelAnimationFrame(animationId);
    }, {passive: true});
    
    window.addEventListener('touchend', () => {
        if(isDown) {
            isDown = false;
            autoScroll();
        }
    });
    
    window.addEventListener('touchmove', (e) => {
        if (!isDown) return;
        const walk = (e.touches[0].pageX - startX) * 1.5;
        currentTranslate = prevTranslate + walk;
        
        if (currentTranslate > 0) currentTranslate = -halfWidth + (currentTranslate % halfWidth);
        if (currentTranslate < -halfWidth) currentTranslate = (currentTranslate % halfWidth);
        
        setTransform(currentTranslate);
    }, {passive: true});

    slider.addEventListener('mouseenter', () => {
        if (!isDown) cancelAnimationFrame(animationId);
    });
    
    slider.addEventListener('mouseleave', () => {
        if (!isDown) autoScroll();
    });

    track.style.cursor = 'grab';
    autoScroll();
}

window.addEventListener('DOMContentLoaded', () => {
    initCountdown();
    initScrollReveal();
    initGreetingsSlider();
});
</script>

<footer>&copy; <?php echo date("Y"); ?> تطوير وبرمجة عثمان الزبيدي</footer>
</body>
</html>
