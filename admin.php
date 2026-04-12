function callCloud($method, $bin, $key, $data = null) {
    // إضافة وقت حالي لإجبار السحابة على تجاوز الكاش
    $url = "https://api.jsonbin.io/v3/b/" . $bin . "/latest?v=" . time();
    if ($method == "PUT") {
        $url = "https://api.jsonbin.io/v3/b/" . $bin;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $headers = [
        "X-Master-Key: " . $key,
        "X-Bin-Meta: false"
    ];

    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        $headers[] = "X-Bin-Versioning: false"; // يمنع إنشاء إصدارات جديدة، يحدث النسخة الحالية فقط
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}
