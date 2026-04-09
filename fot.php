<?php
$api_key    = "P0dgFndfWhrXNdda"; 
$api_secret = "BBvwCZdhld8mV1vK1J1H6eX3wo0jBdtd";

// رابط جلب الدوريات (هذا الموديول عادة ما يكون مفتوحاً للجميع)
$url = "https://live-score-api.com/api-client/leagues/list.json?key=" . $api_key . "&secret=" . $api_secret;

$response = file_get_contents($url);
$data = json_decode($response, true);

echo "<h3>فحص حالة الـ API:</h3>";
if (isset($data['success']) && $data['success'] == true) {
    echo "<p style='color:green;'>✅ الاتصال ناجح! المفاتيح تعمل.</p>";
    echo "عرض أول 5 دوريات متاحة لك:<br><ul>";
    foreach (array_slice($data['data']['league'], 0, 5) as $l) {
        echo "<li>" . $l['name'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red;'>❌ خطأ: " . ($data['error'] ?? 'لا يمكن الوصول للموديول') . "</p>";
}
?>
