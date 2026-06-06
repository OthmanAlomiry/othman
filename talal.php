<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>روابط الإدارة | عثمان الزبيدي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f5f5f7;
            --card-bg: rgba(255, 255, 255, 0.8);
            --primary-text: #1d1d1f;
            --secondary-text: #86868b;
            --accent-blue: #0071e3;
            --success-green: #28a745;
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #e2e2e2 0%, #ffffff 100%);
            color: var(--primary-text);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        header {
            text-align: center;
            margin-bottom: 40px;
        }

        header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #000;
        }

        header p {
            color: var(--secondary-text);
            font-size: 16px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--secondary-text);
            margin: 25px 10px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .link-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 18px;
            margin-bottom: 15px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .link-card:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }

        .link-info h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 600;
        }

        .link-url {
            font-size: 13px;
            color: var(--accent-blue);
            word-break: break-all;
            font-family: monospace;
            background: rgba(0, 113, 227, 0.05);
            padding: 5px 10px;
            border-radius: 8px;
            display: block;
            margin-top: 5px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            border-radius: 12px;
            border: none;
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-visit {
            background: var(--primary-text);
            color: white;
        }

        .btn-visit:hover { background: #333; }

        .btn-copy {
            background: #fff;
            color: var(--primary-text);
            border: 1px solid #d2d2d7;
        }

        .btn-copy:hover { background: #f5f5f7; }

        .btn-copy.copied {
            background: var(--success-green);
            color: white;
            border-color: var(--success-green);
        }

        footer {
            text-align: center;
            margin-top: 40px;
            font-size: 13px;
            color: var(--secondary-text);
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>لوحة روابطي الخاصة</h1>
        <p>إدارة سريعة ومنظمة لمشاريعك</p>
    </header>

    <div class="section-title">الموقع الأول</div>
    
    <div class="link-card">
        <div class="link-info">
            <h3>موقع الجماعة الرسمي</h3>
            <span class="link-url" id="url1">https://d-service.pro/s/</span>
        </div>
        <div class="actions">
            <a href="https://d-service.pro/s/" target="_blank" class="btn btn-visit">انتقال للموقع</a>
            <button onclick="copyLink('url1', this)" class="btn btn-copy">نسخ الرابط</button>
        </div>
    </div>

    <div class="link-card">
        <div class="link-info">
            <h3>صفحة التحكم (الجماعة)</h3>
            <span class="link-url">https://d-service.pro/s/admin.php</span>
        </div>
        <div class="actions">
            <a href="https://d-service.pro/s/admin.php" target="_blank" class="btn btn-visit">انتقال للموقع</a>
        </div>
    </div>

    <hr style="border: 0; height: 1px; background: #d2d2d7; margin: 30px 0;">

    <div class="section-title">موقع وجوه السعد</div>

    <div class="link-card">
        <div class="link-info">
            <h3>رابط الموقع الرسمي</h3>
            <span class="link-url" id="url3">https://d-service.pro/t/s/</span>
        </div>
        <div class="actions">
            <a href="https://d-service.pro/t/s/" target="_blank" class="btn btn-visit">انتقال للموقع</a>
            <button onclick="copyLink('url3', this)" class="btn btn-copy">نسخ الرابط</button>
        </div>
    </div>

    <div class="link-card">
        <div class="link-info">
            <h3>صفحة التحكم (وجوه السعد)</h3>
            <span class="link-url">https://d-service.pro/t/s/admin.php</span>
        </div>
        <div class="actions">
            <a href="https://d-service.pro/t/s/admin.php" target="_blank" class="btn btn-visit">انتقال للموقع</a>
        </div>
    </div>

    <footer>
        تطوير وبرمجة عثمان الزبيدي &copy; 2026
    </footer>
</div>

<script>
    function copyLink(id, btn) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            const originalText = "نسخ الرابط";
            btn.innerText = "تم النسخ! ✓";
            btn.classList.add('copied');
            
            setTimeout(() => {
                btn.innerText = originalText;
                btn.classList.remove('copied');
            }, 2000);
        });
    }
</script>

</body>
</html>
