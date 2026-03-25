<?php

$base = "http://shd-gcp-live.edgenextcdn.net/live/bitmovin-mbc-1/15cf99af5de54063fdabfefe66adc075/";
$file = "index.m3u8";

function stream($url){

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 8192);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch,$data){
echo $data;
flush();
return strlen($data);
});

curl_exec($ch);
curl_close($ch);

}

if(isset($_GET['ts'])){
header("Content-Type: video/mp2t");
stream($base.$_GET['ts']);
exit;
}

header("Content-Type: application/vnd.apple.mpegurl");

$m3u8 = file_get_contents($base.$file);
$lines = explode("\n",$m3u8);

foreach($lines as &$line){
if(strpos($line,'.ts') !== false){
$line = "mb1.php?ts=".$line;
}
}

echo implode("\n",$lines);

?>