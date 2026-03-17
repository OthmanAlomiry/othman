<?php
session_start();
$visitors_file = 'online_visitors.txt';
$session_id = session_id();
$time = time();
$data = file_exists($visitors_file) ? unserialize(file_get_contents($visitors_file)) : [];
$data[$session_id] = $time;
foreach ($data as $id => $last_time) {
    if ($time - $last_time > 120) unset($data[$id]);
}
file_put_contents($visitors_file, serialize($data));
echo count($data);
?>
