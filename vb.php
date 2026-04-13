<?php
// إعدادات الـ API الخاصة بك
$api_key = "bYKapDzujDdGdDwq";
$api_secret = "MLDJw7w8gjbHauTlsEwRi6lBKyixCPoY";
$today = date('Y-m-d');

// استخدام رابط الـ Fixtures لجلب جدول اليوم كاملاً
$url = "https://live-score-api.com/api/live/fixtures.json?key=$api_key&secret=$api_secret&lang=ar&date=$today";

// استخدام cURL لضمان العمل على Render و Docker
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// استخراج المباريات بشكل آمن
$matches = [];
if (isset($result['success']) && $result['success'] == true) {
    $matches = $result['data']['fixtures'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مباريات اليوم</title>
    <style>
        :root {
            --main-purple: #6200ea;
            --bg-color: #f0f2f5;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 15px;
        }
        .container {
            max-width: 700px;
            margin: auto;
        }
        .header {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .header h1 { color: var(--main-purple); margin: 0; font-size: 24px; }
        .header p { color: #666; margin-top: 5px; }

        .match-item {
            background: white;
            margin-bottom: 12px;
            padding: 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .match-item:hover { transform: translateY(-2px); }

        .league-tag {
            font-size: 11px;
            background: #eee;
            padding: 3px 8px;
            border-radius: 5px;
            color: #555;
            display: inline-block;
            margin-bottom: 8px;
        }
        .team-name {
            flex: 1;
            font-weight: 600;
            font-size: 15px;
            text-align: center;
        }
        .match-info {
            flex: 0.8;
            text-align: center;
            border-right: 1px solid #eee;
            border-left: 1px solid #eee;
            margin: 0 10px;
        }
        .time {
            font-weight: bold;
            color: var(--main-purple);
            font-size: 18px;
            display: block;
        }
        .status {
            font-size: 12px;
            color: #888;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 15px;
            color: #999;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>⚽ جدول مباريات اليوم</h1>
        <p><?php echo $today; ?></p>
    </div>

    <?php if (!empty($matches)): ?>
        <?php foreach ($matches as $match): ?>
            <div class="match-item">
                <div class="team-name"><?php echo $match['home_name']; ?></div>
                
                <div class="match-info">
                    <span class="league-tag"><?php echo $match['league_name']; ?></span>
                    <span class="time"><?php echo substr($match['time'], 0, 5); ?></span>
                    <span class="status"><?php echo $match['location']; ?></span>
                </div>
                
                <div class="team-name"><?php echo $match['away_name']; ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-data">
            <p>لا توجد مباريات مجدولة اليوم في حسابك.</p>
            <p><small>ملاحظة: تأكد من أن باقة الـ Trial مفعلة وتدعم الدوريات الحالية.</small></p>
            <?php if(isset($result['error'])): ?>
                <p style="color:red">سبب الرفض من الموقع: <?php echo $result['error']; ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
