<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; margin: 0; padding: 0; font-family: sans-serif; }
        .main-header { background: #1a1a1a; color: #fff; padding: 15px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .match-container { max-width: 1000px; margin: 20px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        iframe { width: 100%; min-height: 800px; border: none; }
    </style>
</head>
<body>

<div class="main-header">
    <h4 class="fw-bold mb-0">⚽ جدول مباريات اليوم المباشر</h4>
    <small style="color: #00ff00;">تحديث تلقائي لجميع الدوريات العربية والعالمية</small>
</div>

<div class="container px-2">
    <div class="match-container">
        <iframe src="https://m.livescore.com/" scrolling="yes"></iframe>
    </div>
</div>

<footer class="text-center py-4 text-muted small">
    &copy; 2026 d-service.pro | خدمة النتائج المباشرة
</footer>

</body>
</html>
