<?php
// إعدادات الوقت
date_default_timezone_set('Asia/Riyadh');

// استخدام مصدر بيانات مفتوح (هذا الرابط يجلب بيانات حية مباشرة)
$url = "https://worldcupjson.net/matches/today"; // مثال لمصدر مفتوح ومجاني

// محاولة جلب البيانات
$response = @file_get_contents($url);

if ($response === FALSE) {
    // إذا فشل المصدر الأول، نستخدم مصدر يدوي بسيط للعرض كنموذج (Placeholder)
    $matches = [];
} else {
    $matches = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: sans-serif; }
        .match-card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .score { font-size: 2rem; font-weight: bold; color: #0d6efd; }
        .no-data { background: white; border-radius: 20px; padding: 40px; margin-top: 50px; }
    </style>
</head>
<body>

<div class="container py-5 text-center">
    <h2 class="mb-4">⚽ جدول مباريات اليوم المباشر</h2>
    <p class="text-muted mb-5">بتوقيت مكة المكرمة: <?php echo date("H:i"); ?></p>

    <?php if (!empty($matches) && is_array($matches)): ?>
        <div class="row justify-content-center">
            <?php foreach ($matches as $m): ?>
                <div class="col-md-8">
                    <div class="match-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div style="flex: 1;"><strong><?php echo $m['home_team']['name']; ?></strong></div>
                            <div class="score px-4">
                                <?php echo ($m['home_team']['goals'] ?? 0) . " - " . ($m['away_team']['goals'] ?? 0); ?>
                            </div>
                            <div style="flex: 1;"><strong><?php echo $m['away_team']['name']; ?></strong></div>
                        </div>
                        <div class="mt-2 text-muted small">الحالة: <?php echo $m['status']; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-data shadow-sm mx-auto" style="max-width: 600px;">
            <h4 class="text-primary">نظام الربط قيد التحديث ⚙️</h4>
            <p>يا عثمان، يبدو أن جميع الـ APIs المجانية تطلب بطاقة تفعيل هذا اليوم.</p>
            <hr>
            <h5>الحل النهائي لك الآن:</h5>
            <p class="text-start small">
                بما أنك تستخدم <b>Render</b>، يمكنك استخدام نظام <b>Iframe</b> لعرض جدول المباريات من موقع رياضي شهير داخل صفحتك مباشرة. هذا الحل "يشتغل 100%" بدون مفاتيح وبدون بطاقة بنكية.
            </p>
            <div class="alert alert-secondary text-start small">
                سأعطيك كود الـ Iframe إذا فشل هذا الـ API في جلب البيانات، لكي تضمن أن موقعك فيه محتوى رياضي الآن.
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
