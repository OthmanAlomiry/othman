<?php
// إعدادات الوقت لضمان مطابقة توقيت المباريات مع توقيتك في السعودية
date_default_timezone_set('Asia/Riyadh');

// مفتاحك السري من RapidAPI (تأكد من بقائه سرياً)
$api_key = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9"; 

$curl = curl_init();

curl_setopt_array($curl, [
    // هذا الرابط (get-all-matches) هو الأضمن لجلب البيانات الشاملة
    CURLOPT_URL => "https://free-api-live-football-data.p.rapidapi.com/api/v1/get-all-matches",
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

// تحويل البيانات من JSON إلى مصفوفة PHP
$result = json_decode($response, true);

// استخراج المباريات من المصفوفة (API-specific structure)
$matches = $result['data'] ?? [];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        :root { --main-bg: #f4f7f6; --card-bg: #ffffff; --accent-color: #198754; }
        body { background-color: var(--main-bg); font-family: 'Segoe UI', Tahoma, sans-serif; }
        .match-card { 
            background: var(--card-bg); border-radius: 12px; padding: 15px; margin-bottom: 12px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: none; transition: 0.3s;
        }
        .match-card:hover { transform: scale(1.01); }
        .league-header { font-size: 0.8rem; color: #777; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 12px; }
        .score-box { background: #f8f9fa; border-radius: 8px; padding: 5px 15px; font-weight: bold; font-size: 1.4rem; color: var(--accent-color); min-width: 70px; }
        .team-name { font-weight: 600; width: 40%; }
        .status-badge { font-size: 0.75rem; font-weight: bold; margin-top: 5px; }
        .live { color: #dc3545; animation: pulse 1s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold">⚽ جدول مباريات اليوم</h2>
        <p class="text-muted"><?php echo date("l, d F Y"); ?></p>
    </div>

    <?php if ($err): ?>
        <div class="alert alert-danger text-center">خطأ في الاتصال بالسيرفر: <?php echo $err; ?></div>
    <?php elseif (!empty($matches)): ?>
        
        <div class="row justify-content-center">
            <?php foreach ($matches as $match): ?>
                <div class="col-md-8 col-lg-6">
                    <div class="match-card">
                        <div class="league-header d-flex justify-content-between">
                            <span><?php echo htmlspecialchars($match['league_name']); ?></span>
                            <span><?php echo htmlspecialchars($match['start_time'] ?? ''); ?></span>
                        </div>
                        
                        <div class="d-flex align-items-center text-center">
                            <div class="team-name text-end px-2"><?php echo htmlspecialchars($match['home_team_name']); ?></div>
                            
                            <div class="d-flex flex-column align-items-center flex-grow-1">
                                <div class="score-box">
                                    <?php echo $match['score'] ?? 'vs'; ?>
                                </div>
                                <div class="status-badge <?php echo ($match['status_name'] == 'Live') ? 'live' : ''; ?>">
                                    <?php echo htmlspecialchars($match['status_name']); ?>
                                </div>
                            </div>
                            
                            <div class="team-name text-start px-2"><?php echo htmlspecialchars($match['away_team_name']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="text-center py-5">
            <div class="card shadow-sm p-5 border-0 rounded-4 mx-auto" style="max-width: 500px;">
                <h4 class="text-secondary">لا توجد مباريات حالياً</h4>
                <p class="text-muted small">يرجى المحاولة مرة أخرى لاحقاً أو في أوقات ذروة المباريات.</p>
                <button onclick="window.location.reload();" class="btn btn-outline-success btn-sm mt-3">تحديث الصفحة</button>
            </div>
        </div>
    <?php endif; ?>

    <footer class="text-center mt-5 text-muted small">
        تم الجلب بواسطة d-service.pro &copy; <?php echo date("Y"); ?>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
