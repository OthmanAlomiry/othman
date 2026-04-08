<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Riyadh');

// --- بيانات السحابة عثمان ---
$API_KEY = '$2a$10$HsgEopXEHj.LV8oAFpXB..ziTCTUK/9q6h/aHygbnFeW42h4B90Ge';
$BIN_ID = '69c4ad66c3097a1dd55f06d6';
$user_admin = "othman"; 
$pass_admin = "1405";

// دالة الاتصال بالسحابة عثمان - تم تحسينها لضمان الاستقرار
function callCloud($method, $bin, $key, $data = null) {
    $url = "https://api.jsonbin.io/v3/b/" . $bin . ($method == "GET" ? "/latest" : "");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array("X-Master-Key: " . $key);
    if($method == "PUT") {
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// دالة التزامن الآمنة عثمان - تمنع مسح البيانات إذا كانت المدخلات فارغة
function syncData($bin, $key, $channels, $sections, $news, $notify) {
    if(empty($channels) && empty($sections)) {
        return false; // حماية عثمان: لا ترسل بيانات فارغة للسيرفر
    }
    $payload = array(
        'custom_channels' => array_values($channels),
        'sections' => array_values($sections),
        'news_ticker' => $news,
        'notification' => $notify
    );
    return callCloud("PUT", $bin, $key, $payload);
}

if(isset($_GET['out'])){ session_destroy(); header("Location: admin.php"); exit; }

if(isset($_POST['login'])){
    if($_POST['u'] == $user_admin && $_POST['p'] == $pass_admin){ $_SESSION['ok'] = true; }
}

if(isset($_SESSION['ok'])){
    $res = callCloud("GET", $BIN_ID, $API_KEY);
    $record = (isset($res['record'])) ? $res['record'] : array();
    
    $channels = (isset($record['custom_channels'])) ? $record['custom_channels'] : array();
    $sections = (isset($record['sections'])) ? $record['sections'] : array();
    $news_ticker = (isset($record['news_ticker'])) ? $record['news_ticker'] : array('text' => 'مرحباً بكم', 'status' => 'hide');
    $notification = (isset($record['notification'])) ? $record['notification'] : array('id' => '', 'msg' => '');

    // --- 0. إرسال إشعار فوري عثمان ---
    if(isset($_POST['send_notify'])){
        $notification = array(
            'id' => uniqid(),
            'msg' => $_POST['notify_msg'],
            'time' => time()
        );
        syncData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // --- 1. إدارة الشريط الإخباري ---
    if(isset($_POST['update_ticker'])){
        $news_ticker = array('text' => $_POST['ticker_text'], 'status' => $_POST['ticker_status']);
        syncData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php?success=1"); exit;
    }

    // --- 2. إدارة الأقسام ---
    if(isset($_POST['save_sec'])){
        $sec_id = (!empty($_POST['sec_id'])) ? $_POST['sec_id'] : uniqid();
        $new_sec = array('id' => $sec_id, 'name' => $_POST['sec_name'], 'key' => $_POST['sec_key'], 'img' => $_POST['sec_img'], 'status' => $_POST['sec_status']);
        $found = false;
        foreach($sections as &$s){ if($s['id'] == $sec_id){ $s = $new_sec; $found = true; break; } }
        if(!$found) $sections[] = $new_sec;
        syncData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php#sections_area"); exit;
    }

    // --- 3. إدارة القنوات ---
    if(isset($_POST['save_ch'])){
        $target_id = $_POST['edit_id'];
        $ch_data = array('id' => ($target_id ?: uniqid()), 'name' => $_POST['n'], 'file' => $_POST['f'], 'section' => $_POST['s']);
        if(!empty($target_id)){ 
            foreach($channels as &$c){ if($c['id'] == $target_id){ $c = $ch_data; break; } } 
        } else { 
            $channels[] = $ch_data; 
        }
        syncData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php#channels_area"); exit;
    }

    // الحذف
    if(isset($_GET['del'])){
        $channels = array_filter($channels, function($c){ return $c['id'] !== $_GET['del']; });
        syncData($BIN_ID, $API_KEY, $channels, $sections, $news_ticker, $notification);
        header("Location: admin.php"); exit;
    }
}
?>
