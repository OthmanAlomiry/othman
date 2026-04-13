    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['snap_url'])) {
        
        $snapUrl = $_POST['snap_url'];
        // تنظيف الرابط من أي مسافات زائدة
        $snapUrl = trim($snapUrl);
        
        $apiUrl = "https://download-snapchat-video-spotlight-online.p.rapidapi.com/download?url=" . urlencode($snapUrl);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: download-snapchat-video-spotlight-online.p.rapidapi.com",
                "x-rapidapi-key: 49e271c73amsh02ca0a4d3f5b237p145598jsn7c1cee0f8ec9"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "<p style='color:red;'>خطأ في الاتصال: " . $err . "</p>";
        } else {
            $data = json_decode($response, true);
            
            // فحص المفاتيح المحتملة للرابط (بعض الـ APIs تستخدم 'download_url' أو 'media_url')
            $videoUrl = $data['url'] ?? $data['download_url'] ?? $data['result'] ?? null;

            if (isset($data['error'])) {
                echo "<p style='color:red;'>تنبيه من الـ API: " . $data['error'] . "</p>";
            } elseif ($videoUrl) {
                echo "<h3>✅ تم استخراج الفيديو بنجاح:</h3>";
                echo "<a href='".$videoUrl."' target='_blank' style='display:inline-block; padding:15px; background:green; color:white; text-decoration:none; border-radius:5px;'>تحميل الفيديو الآن</a>";
            } else {
                // إذا لم نجد الرابط، سنعرض الاستجابة الخام لنفهم المشكلة
                echo "<p style='color:orange;'>لم يتم العثور على رابط مباشر. إليك الرد الفني من السيرفر:</p>";
                echo "<pre style='text-align:left; background:#eee; padding:10px; direction:ltr;'>";
                print_r($data);
                echo "</pre>";
            }
        }
    }
    ?>
