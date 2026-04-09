<?php
// --- إعدادات RapidAPI ---
// ضع هنا المفتاح الذي استخرجناه من موقع RapidAPI (x-rapidapi-key)
$api_key = "ضَع_مفتاحك_السري_هنا"; 

$curl = curl_init();

curl_setopt_array($curl, [
    // هذا الرابط يجلب بيانات البحث التي كنت تجربها في لوحة تحكم RapidAPI
    CURLOPT_URL => "https://free-api-live-football-data.p.rapidapi.com/football-players-search?search=m",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
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
    <title>بيانات كرة القدم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .player-card { background: white; border-radius: 10px; padding: 15px; margin-bottom: 10px; border-right: 5px solid #198754; }
    </style>
</head>
<body>
<div class="container py-5">
    <h3 class="text-center mb-4">✅ النتائج من RapidAPI</h3>

    <?php if ($err): ?>
        <div class="alert alert-danger">حدث خطأ في الاتصال: <?php echo $err; ?></div>
    <?php elseif (isset($data['status']) && $data['status'] == 'success' && !empty($data['data'])): ?>
        <div class="row justify-content-center">
            <?php foreach ($data['data'] as $player): ?>
                <div class="col-md-8">
                    <div class="player-card shadow-sm text-end">
                        <h5 class="mb-1"><?php echo htmlspecialchars($player['name']); ?></h5>
                        <p class="mb-0 text-muted">الفريق: <?php echo htmlspecialchars($player['team_name'] ?? 'غير معروف'); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            لا توجد بيانات حالياً. تأكد من استبدال <b>"ضَع_مفتاحك_السري_هنا"</b> بالمفتاح الحقيقي من RapidAPI.
        </div>
    <?php endif; ?>
</div>
</body>
</html>
