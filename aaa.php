<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الرياضة - تسجيل الدخول</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --main: #e11d48;
            --bg-deep: #061626;
            --purple-grad: linear-gradient(45deg, #7c3aed, #fff);
            --green-grad: linear-gradient(45deg, #16a34a, #fff);
        }

        body {
            margin: 0;
            font-family: 'Tajawal', sans-serif;
            background-color: var(--bg-deep);
            color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden; /* لمنع التمرير غير الضروري */
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.05); /* شفافية خفيفة */
            backdrop-filter: blur(10px); /* تأثير بلور للخلفية */
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5); /* ظل ثلاثي الأبعاد */
            width: 90%;
            max-width: 400px;
            text-align: center;
            transform-style: preserve-3d; /* لتأثيرات ثلاثية الأبعاد */
            perspective: 1000px; /* منظور ثلاثي الأبعاد */
        }

        .login-container h2 {
            margin-top: 0;
            font-weight: 900;
            background: var(--purple-grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 5px 10px rgba(0, 0, 0, 0.3); /* ظل للنص */
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 40px; /* مسافة للأيقونة */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .input-group input:focus {
            border-color: var(--main);
            box-shadow: 0 0 10px rgba(225, 29, 72, 0.5);
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: var(--main);
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s, box-shadow 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .login-btn:hover {
            background: #f43f5e; /* لون أفتح قليلاً عند الحوم */
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .login-btn:active {
            transform: translateY(1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        }

        .forgot-password {
            margin-top: 15px;
            font-size: 14px;
            color: #888;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: #fff;
        }

        /* خلفية ثلاثية الأبعاد متحركة */
        .bg-3d {
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background-image:
                linear-gradient(rgba(255,255,255,0.01) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.01) 1px, transparent 1px);
            background-size: 50px 50px;
            transform: rotateX(60deg);
            animation: bgMove 20s linear infinite;
            z-index: -1;
        }

        @keyframes bgMove {
            0% { transform: rotateX(60deg) translateY(0); }
            100% { transform: rotateX(60deg) translateY(-50px); }
        }
    </style>
</head>
<body>
    <div class="bg-3d"></div>

    <div class="login-container">
        <h2>بوابة الرياضة</h2>
        <form>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" placeholder="اسم المستخدم" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" placeholder="كلمة المرور" required>
            </div>
            <button type="submit" class="login-btn">تسجيل الدخول</button>
        </form>
        <a href="#" class="forgot-password">نسيت كلمة المرور؟</a>
    </div>
</body>
</html>
