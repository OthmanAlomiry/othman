<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جدول مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f0f2f5; 
            margin: 0; 
            padding: 0; 
            overflow-x: hidden;
            font-family: sans-serif;
        }
        .header-title {
            background: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 10px;
        }
        /* تنسيق النافذة لتناسب شاشة الجوال تماماً */
        .iframe-container {
            position: relative;
            width: 100%;
            height: 1500px; /* طول الصفحة */
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>

<div class="header-title">
    <h3 class="fw-bold mb-1">⚽ جدول مباريات اليوم</h3>
    <p class="text-muted small mb-0">تحديث مباشر - d-service.pro</p>
</div>

<div class="iframe-container">
    <iframe src="https://m.kooora.com/?region=-1&area=0" scrolling="yes"></iframe>
</div>

<footer class="text-center py-4 text-muted small">
    &copy; 2026 d-service.pro - جميع الحقوق محفوظة
</footer>

</body>
</html>
