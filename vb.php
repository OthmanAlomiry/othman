<?php
// إعدادات الـ API الخاصة بك من الصورة
$api_key = "bYKapDzujDdGdDwq";
$api_secret = "MLDJw7w8gjbHauTlsEwRi6lBKyixCPoY";

// جلب مباريات اليوم - تم إضافة lang=ar للغة العربية
$url = "https://live-score-api.com/api/live/scores.json?key=$api_key&secret=$api_secret&lang=ar";

$response = file_get_contents($url);
$result = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم</title>
    <style>
        :root {
            --primary-color: #6200ea;
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        header {
            text-align: center;
            padding: 20px 0;
            color: var(--primary-color);
        }
        .match-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .team {
            flex: 1;
            text-align: center;
            font-weight: bold;
            font-size: 1.1em;
        }
        .score-area {
            flex: 1;
            text-align: center;
            background: #f0f0f0;
            padding: 10px;
            border-radius: 8px;
            margin: 0 15px;
        }
        .score {
            font-size: 1.5em;
            font-weight: 900;
            color: #333;
        }
        .status {
            display: block;
            font-size: 0.8em;
            color: #d32f2f;
            margin-top: 5px;
        }
        .league-name {
            font-size: 0.85em;
            color: #777;
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .no-matches {
            text-align: center;
            padding: 50px;
            color: #888;
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>⚽ مباريات اليوم المباشرة</h1>
        <p><?php echo date('Y-m-d'); ?></p>
    </header>

    <?php if ($result['success'] && !empty($result['data']['match'])): ?>
        <?php foreach ($result['data']['match'] as $match): ?>
            <div class="match-card">
                <div class="league-name"><?php echo $match['league_name']; ?></div>
                <div style="display: flex; width: 100%; align-items: center;">
                    <div class="team"><?php echo $match['home_name']; ?></div>
                    
                    <div class="score-area">
                        <div class="score"><?php echo $match['score']; ?></div>
                        <span class="status"><?php echo $match['status']; ?> '<?php echo $match['time']; ?></span>
                    </div>
                    
                    <div class="team"><?php echo $match['away_name']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-matches">
            <h3>لا توجد مباريات مباشرة في الوقت الحالي</h3>
            <p>تأكد من حالة الاشتراك في لوحة التحكم الخاصة بك.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
