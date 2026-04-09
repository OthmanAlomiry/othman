<?php
// إعدادات التوقيت لتناسب منطقتنا
date_default_timezone_set('Asia/Riyadh');

// المفتاح الخاص بك من RapidAPI
$api_key = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9"; 

$curl = curl_init();

// الرابط الجديد لجلب جدول مباريات اليوم بالكامل حسب التاريخ
$today = date("Ymd");
curl_setopt_array($curl, [
    CURLOPT_URL => "https://free-api-live-football-data.p.rapidapi.com/api/v1/get-fixtures-by-date?date=" . $today,
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

$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <meta http-equiv="refresh" content="120"> 
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .match-card { 
            background: white; 
            border-radius: 15px; 
            padding: 20px; 
            margin-bottom: 15px; 
            border: none; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .match-card:hover { transform: translateY(-3px); }
        .league-badge { 
            background: #eef2f7; 
            color: #555; 
            font-size: 0.75rem; 
            padding: 4px 12px; 
            border-radius: 20px; 
            margin-bottom: 15px; 
            display: inline-block;
        }
        .score { 
            font-size: 1.8rem; 
            font-weight: bold; 
            color: #198754; 
            min-width: 80px;
            display: inline-block;
        }
        .team-name { font-weight: 600; font-size: 1.1rem; flex: 1; }
        .status { font-size: 0.8rem; font-weight: bold; }
        .live { color: red; animation: blinker 1s linear infinite; }
        @keyframes blinker { 50% { opacity: 0; } }
    </style>
</head>
<body>

<div class="container py-5 text-center">
    <h2 class="mb-2">⚽ جدول مباريات اليوم</h2>
    <p class="text-muted mb-5"><?php echo date("Y-m-d"); ?></p>

    <?php if ($err): ?>
        <div class="alert alert-danger">حدث خطأ في الاتصال بالسيرفر.</div>
    <?php elseif (isset($data['status']) && $data['status'] == 'success' && !empty($data['data'])): ?>
        
        <div class="row justify-content-center">
            <?php foreach ($data['data'] as $match): ?>
                <div class="col-md-8 col-lg-6">
                    <div class="match-card">
                        <span class="league-badge"><?php echo htmlspecialchars($match['league_name']); ?></span>
                        
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="team-name text-end"><?php echo htmlspecialchars($match['home_team_name']); ?></div>
                            
                            <div class="text-center px-3">
                                <div class="score">
                                    <?php echo $match['score'] ?? 'VS'; ?>
                                </div>
                                <div class="status mt-1 <?php echo ($match['status_name'] == 'Live') ? 'live' : ''; ?>">
                                    <?php echo htmlspecialchars($match['status_name']); ?>
                                </div>
                            </div>
                            
                            <div class="team-name text-start"><?php echo htmlspecialchars($match['away_team_name']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="card p-5 shadow-sm mx-auto" style="max-width: 500px; border-radius: 20px;">
            <h4 class="text-muted">لا توجد مباريات مجدولة حالياً</h4>
            <p class="small text-secondary">تأكد من العودة في أوقات الذروة للمباريات.</p>
        </div>
    <?php endif; ?>

    <footer class="mt-5 text-muted small">
        &copy; <?php echo date("Y"); ?> d-service.pro - بوابة الرياضة
    </footer>
</div>

</body>
</html>
