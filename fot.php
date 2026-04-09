<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - d-service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; margin: 0; padding: 0; font-family: sans-serif; }
        .header-box { background: #1a1a1a; color: white; padding: 20px; text-align: center; }
        .content-area { max-width: 900px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="header-box">
    <h3 class="mb-0">⚽ نتائج ومباريات اليوم</h3>
    <small style="color: #00ff00;">تحديث مباشر لجميع الدوريات</small>
</div>

<div class="container">
    <div class="content-area">
        <iframe src="https://www.livescore.bz/api.gw?api=1&lang=ar" 
                width="100%" 
                height="1200" 
                frameborder="0" 
                scrolling="yes" 
                style="width:100% !important; border:none !important; min-height:1200px;">
        </iframe>
    </div>
</div>

<footer class="text-center py-4 text-muted small">
    &copy; 2026 d-service.pro | جميع الحقوق محفوظة
</footer>

</body>
</html>
