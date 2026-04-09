<?php
// 1. إعدادات المنطقة الزمنية والوقت
date_default_timezone_set('Asia/Riyadh');
$api_key = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9"; 
$todayDate = date("Y-m-d");

// 2. الاتصال بالـ API باستخدام cURL
$curl = curl_init();

curl_setopt_array($curl, [
    // استخدمنا رابط get-fixtures-by-date لجلب جدول اليوم كاملاً وليس فقط المباشر
    CURLOPT_URL => "https://free-api-live-football-data.p.rapidapi.com/api/v1/get-fixtures-by-date?date=" . $todayDate,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: free-api-live-football-data.p.rapidapi.com",
        "x-rapidapi-key: " . $api_key
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// 3. تحليل البيانات
$result = json_decode($response, true);

// التأكد من جلب المصفوفة الصحيحة (data) من استجابة الـ API
$matches = [];
if (isset($result['data']) && is_array($result['data'])) {
    $matches = $result['data'];
} elseif (isset($result['response']) && is_array($result['response'])) {
    $matches = $result['response'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        :root { --bg-color: #f4f7f6; --card-bg: #ffffff; --text-main: #333; --accent: #198754; }
        body { background-color: var(--bg-color); font-family: 'Segoe UI', Tahoma, sans-serif; color: var(--text-main); }
        .match-card { 
            background: var(--card-bg); border-radius: 15px; padding: 20px; margin-bottom: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: none; transition: transform 0.2s;
        }
        .match-card:hover { transform: translateY(-3px); }
        .league-name { font-size: 0.8rem; color: #777; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; display: block; }
        .team-name { font-weight: 600; font-size: 1.05rem; width: 40%; }
        .score-box { 
            background: #f8f9fa; border-radius: 10px; padding: 8px 15px; 
            font-weight: bold; font-size: 1.5rem; color: var(--accent); min-width: 80px; 
        }
        .status-text { font-size: 0.75rem; margin-top: 5px; font-weight: bold; }
        .live-blink { color: #dc3545; animation: blinker 1s linear infinite; }
        @keyframes blinker { 50% { opacity: 0; } }
        .refresh-btn { border-radius: 20px; padding: 8px 25px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">⚽ جدول مباريات اليوم</h2>
        <p class="text-muted"><?php echo date("l, d F Y"); ?></p>
    </div>

    <?php if ($err): ?>
        <div class="alert alert-danger text-center shadow-sm">حدث خطأ في جلب البيانات: <?php echo $err; ?></div>
    <?php elseif (!empty($matches)): ?>
        
        <div class="row justify-content-center">
            <?php foreach ($matches as $match): ?>
                <div class="col-md-8 col-lg-6">
                    <div class="match-card">
                        <span class="league-name text-center"><?php echo htmlspecialchars($match['league_name'] ?? 'دوري غير محدد'); ?></span>
                        
                        <div class="d-flex align-items-center text-center">
                            <div class="team-name text-end"><?php echo htmlspecialchars($match['home_team_name'] ?? 'فريق أ'); ?></div>
                            
                            <div class="flex-grow-1 px-2">
                                <div class="score-box mx-auto">
                                    <?php echo $match['score'] ?? 'vs'; ?>
                                </div>
                                <div class="status-text <?php echo (isset($match['status_name']) && $match['status_name'] == 'Live') ? 'live-blink' : ''; ?>">
                                    <?php echo htmlspecialchars($match['status_name'] ?? $match['start_time'] ?? ''); ?>
                                </div>
                            </div>
                            
                            <div class="team-name text-start"><?php echo htmlspecialchars($match['away_team_name'] ?? 'فريق ب'); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="text-center py-5">
            <div class="card shadow-sm p-5 border-0 rounded-4 mx-auto" style="max-width: 500px;">
                <h4 class="text-secondary mb-3">لا توجد مباريات مجدولة حالياً</h4>
                <p class="text-muted small">تأكد من العودة لاحقاً أو في أوقات الذروة للمباريات العالمية والعربية.</p>
                <button onclick="window.location.reload();" class="btn btn-outline-success btn-sm mt-3 refresh-btn">تحديث الصفحة</button>
            </div>
        </div>
    <?php endif; ?>

    <footer class="text-center mt-5 text-muted small">
        تم الجلب بواسطة <strong>d-service.pro</strong> &copy; <?php echo date("Y"); ?>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
