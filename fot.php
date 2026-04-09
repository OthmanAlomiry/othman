<?php
// إعدادات الوقت
date_default_timezone_set('Asia/Riyadh');

// هذا مفتاح API مخصص للاختبار (تأكد من استبداله لاحقاً بمفتاحك الخاص إذا توقف)
$api_key = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9"; 

$curl = curl_init();

curl_setopt_array($curl, [
    // سنستخدم هذا الرابط لجلب مباريات اليوم العالمية والسعودية
    CURLOPT_URL => "https://api-football-v1.p.rapidapi.com/v3/fixtures?date=" . date("Y-m-d"),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: api-football-v1.p.rapidapi.com",
        "x-rapidapi-key: " . $api_key
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

$result = json_decode($response, true);
$matches = $result['response'] ?? [];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .match-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: none; }
        .team-logo { width: 40px; height: 40px; object-fit: contain; margin-bottom: 5px; }
        .score { font-size: 1.8rem; font-weight: bold; color: #198754; }
        .league-info { font-size: 0.75rem; color: #888; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container py-5 text-center">
    <h2 class="fw-bold mb-4">⚽ جدول مباريات اليوم</h2>

    <?php if ($err): ?>
        <div class="alert alert-danger">خطأ في الاتصال: <?php echo $err; ?></div>
    <?php elseif (!empty($matches)): ?>
        <div class="row justify-content-center">
            <?php foreach (array_slice($matches, 0, 20) as $m): ?>
                <div class="col-md-8 col-lg-6">
                    <div class="match-card">
                        <div class="league-info"><?php echo $m['league']['name']; ?> (<?php echo $m['league']['country']; ?>)</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div style="flex: 1;">
                                <img src="<?php echo $m['teams']['home']['logo']; ?>" class="team-logo"><br>
                                <strong><?php echo $m['teams']['home']['name']; ?></strong>
                            </div>
                            <div class="score mx-3">
                                <?php echo ($m['goals']['home'] ?? 0); ?> - <?php echo ($m['goals']['away'] ?? 0); ?>
                            </div>
                            <div style="flex: 1;">
                                <img src="<?php echo $m['teams']['away']['logo']; ?>" class="team-logo"><br>
                                <strong><?php echo $m['teams']['away']['name']; ?></strong>
                            </div>
                        </div>
                        <div class="mt-2 badge bg-light text-dark">
                            <?php echo $m['fixture']['status']['long']; ?> | <?php echo substr($m['fixture']['date'], 11, 5); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            لا توجد مباريات متاحة حالياً. تأكد من تفعيل "Subscribe" في RapidAPI.
        </div>
    <?php endif; ?>
</div>

</body>
</html>
