<?php
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $title = isset($_GET['title']) ? $_GET['title'] : 'video';
    
    // تنظيف اسم الملف
    $filename = preg_replace('/[^A-Za-z0-9]/', '_', $title) . ".mp4";

    // إرسال الهيدرز لإجبار التحميل
    header('Content-Description: File Transfer');
    header('Content-Type: video/mp4');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // قراءة الملف وإرساله
    readfile($url);
    exit;
}
