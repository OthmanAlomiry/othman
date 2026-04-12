<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- بيانات السحابة ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69d6f6b636566621a891e6c1';
$user_admin = "othman"; $pass_admin = "1405";

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }
if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; }
}

function callCloud($method, $bin, $key, $data = null) {
    // أضفنا رقم عشوائي للرابط لمنع الكاش عند الجلب (GET)
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest?nocache=" . time() : "");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // مهلة زمنية للاتصال

    $headers = [
        "X-Master-Key: " . $key,
        "X-Bin-Private: true"
    ];

    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    } else {
        $headers[] = "X-Bin-Meta: false";
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // إذا فشل الاتصال، ارجع مصفوفة فارغة
    if($httpCode != 200) return [];
    return json_decode($res, true);
}

if(isset($_SESSION['ok'])){
    // جلب البيانات
    $record = callCloud("GET", $BIN_ID, $API_KEY);
    
    // تصحيح: إذا لم يجد بيانات، نضع هيكل افتراضي بدلاً من مصفوفة فارغة تماماً
    $channels = isset($record['custom_channels']) ? $record['custom_channels'] : [];
    $sections = isset($record['sections']) ? $record['sections'] : [];
    $news_ticker = isset($record['news_ticker']) ? $record['news_ticker'] : ['text' => 'مرحباً بكم', 'status' => 'hide'];
    $notification = isset($record['notification']) ? $record['notification'] : ['id' => '', 'msg' => ''];

    // --- وظيفة الحفظ المطورة ---
    function sync($bin, $key, $channels, $sections, $news, $notify) {
        $newData = [
            'custom_channels' => array_values($channels),
            'sections' => array_values($sections),
            'news_ticker' => $news,
            'notification' => $notify
        ];
        $result = callCloud("PUT", $bin, $key, $newData);
        return $result;
    }

    // إدارة الإشعارات
    if(isset($_POST['send_notify'])){
        $notification = ['id' => uniqid(), 'msg' => $_POST['notify_msg'], 'time' => time()];
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // تحديث الشريط الإخباري (هنا كانت مشكلة الإخفاء/الإظهار)
    if(isset($_POST['update_ticker'])){
        $news_ticker = ['text' => $_POST['ticker_text'], 'status' => $_POST['ticker_status']];
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // حفظ الأقسام
    if(isset($_POST['save_sec'])){
        $sec_id = $_POST['sec_id'] ?: uniqid();
        $new_sec = [
            'id' => $sec_id, 
            'name' => $_POST['sec_name'], 
            'key' => $_POST['sec_key'], 
            'img' => $_POST['sec_img'], 
            'status' => $_POST['sec_status']
        ];
        $found = false;
        foreach($sections as &$s){ if($s['id'] == $sec_id){ $s = $new_sec; $found = true; break; } }
        if(!$found) $sections[] = $new_sec;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#sections_area"); exit;
    }

    // حذف الأقسام
    if(isset($_GET['del_sec'])){
        $sections = array_values(array_filter($sections, function($s){ return $s['id'] !== $_GET['del_sec']; }));
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#sections_area"); exit;
    }

    // حفظ القنوات
    if(isset($_POST['save_ch'])){
        $target_id = $_POST['edit_id'] ?: uniqid();
        $ch_data = [
            'id' => $target_id, 
            'name' => $_POST['n'], 
            'file' => $_POST['f'], 
            'file_backup' => $_POST['f_backup'], 
            'section' => $_POST['s']
        ];
        $found = false;
        foreach($channels as &$c){ if($c['id'] == $target_id){ $c = $ch_data; $found = true; break; } }
        if(!$found) $channels[] = $ch_data;
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#channels_area"); exit;
    }

    // حذف القنوات
    if(isset($_GET['del'])){
        $channels = array_values(array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; }));
        sync($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1#channels_area"); exit;
    }

    $edit_ch = null; if(isset($_GET['edit'])){ foreach($channels as $c){ if($c['id'] == $_GET['edit']){ $edit_ch = $c; break; } } }
    $edit_sec = null; if(isset($_GET['edit_sec'])){ foreach($sections as $s){ if($s['id'] == $_GET['edit_sec']){ $edit_sec = $s; break; } } }
}
?>
