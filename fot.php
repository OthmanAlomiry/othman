<?php
// المفتاح المستخرج من صورتك
$api_key = "49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9"; 

$curl = curl_init();

curl_setopt_array($curl, [
    // الرابط المخصص لجلب جميع المباريات المباشرة واليومية
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

$data = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .match-card { background: white; border-radius: 15px; padding: 15px; margin-bottom: 15px; border-right: 5px solid #198754; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .score { font-size: 1.5rem; font-weight: bold; color: #198754; background: #e9f7ef; padding: 5px 15px; border-radius: 8px; }
        .league-name { font-size: 0.8rem; color: #6c757d; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>

<div class="container py-5 text-center">
    <h2 class="mb-4">⚽ مباريات اليوم المباشرة</h2>

    <?php if ($err): ?>
        <div class="alert alert-danger">خطأ في الاتصال: <?php echo $err; ?></div>
    <?php elseif (isset($data['status']) && $data['status'] == 'success' && !empty($data['data'])): ?>
        
        <div class="row justify-content-center">
            <?php foreach ($data['data'] as $match): ?>
                <div class="col-md-8 col-lg-6">
                    <div class="match-card">
                        <span class="league-name"><?php echo $match['league_name']; ?></span>
                        <div class="d-flex justify-content-between align-items-center">
                            <div style="flex: 1;"><strong><?php echo $match['home_team_name']; ?></strong></div>
                            <div class="score mx-3"><?php echo $match['score'] ?? 'VS'; ?></div>
                            <div style="flex: 1;"><strong><?php echo $match['away_team_name']; ?></strong></div>
                        </div>
                        <div class="mt-2 small text-muted"><?php echo $match['status_name'] ?? ''; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-info">لا توجد مباريات جارية حالياً. جرب العودة لاحقاً.</div>
    <?php endif; ?>
</div>

</body>
</html>
