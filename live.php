<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>البث المباشر</title>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

<style>

body{
margin:0;
font-family:Tajawal, Tahoma;
background:#ffffff;
text-align:center;
}

header{
background:#ffffff;
padding:20px;
font-size:26px;
font-weight:bold;
box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

.top-text{
padding:15px;
font-size:15px;
background:#f3f4f6;
line-height:1.8;
}

.top-text a{
color:#e11d48;
font-weight:bold;
text-decoration:none;
}

.channels{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
gap:25px;
padding:30px;
max-width:1200px;
margin:auto;
}

.card{
background:white;
border-radius:14px;
overflow:hidden;
box-shadow:0 6px 18px rgba(0,0,0,0.12);
transition:0.3s;
}

.card:hover{
transform:translateY(-6px);
box-shadow:0 10px 25px rgba(0,0,0,0.18);
}

.title{
padding:12px;
font-size:17px;
font-weight:bold;
background:#f3f4f6;
}

video{
width:100%;
background:black;
}

.play{
margin:15px;
padding:10px 18px;
border:none;
border-radius:8px;
background:#e11d48;
color:white;
font-size:15px;
cursor:pointer;
}

.play:hover{
background:#be123c;
}

</style>
</head>

<body>

<header>
📺 البث المباشر للقنوات الرياضية
</header>

<div class="top-text">
جميع القنوات تعمل على الجوال والشاشات الذكية<br>
للاشتراك في الباقة الكاملة تواصل واتساب<br>
<a href="https://wa.me/966505571164">0505571164</a>
</div>

<div class="channels">

<div class="card">
<div class="title">beIN Sport 1</div>
<video id="v1" controls></video>
<button class="play" onclick="playStream('v1','b1.php')">تشغيل</button>
</div>

<div class="card">
<div class="title">beIN Sport 2</div>
<video id="v2" controls></video>
<button class="play" onclick="playStream('v2','b2.php')">تشغيل</button>
</div>

<div class="card">
<div class="title">beIN Sport 3</div>
<video id="v3" controls></video>
<button class="play" onclick="playStream('v3','b3.php')">تشغيل</button>
</div>

<div class="card">
<div class="title">beIN Sport 4</div>
<video id="v4" controls></video>
<button class="play" onclick="playStream('v4','b4.php')">تشغيل</button>
</div>

<div class="card">
<div class="title">beIN Sport 5</div>
<video id="v5" controls></video>
<button class="play" onclick="playStream('v5','b5.php')">تشغيل</button>
</div>

<div class="card">
<div class="title">beIN Sport 6</div>
<video id="v6" controls></video>
<button class="play" onclick="playStream('v6','b6.php')">تشغيل</button>
</div>

<div class="card">
<div class="title">beIN Sport 7</div>
<video id="v7" controls></video>
<button class="play" onclick="playStream('v7','b7.php')">تشغيل</button>
</div>

<div class="card">
<div class="title">beIN Sport 8</div>
<video id="v8" controls></video>
<button class="play" onclick="playStream('v8','b8.php')">تشغيل</button>
</div>

<div class="card">
<div class="title">beIN Sport 9</div>
<video id="v9" controls></video>
<button class="play" onclick="playStream('v9','b9.php')">تشغيل</button>
</div>

</div>

<script>

function playStream(videoId,stream){

var video=document.getElementById(videoId);

if(Hls.isSupported()){
var hls=new Hls();
hls.loadSource(stream);
hls.attachMedia(video);
}

video.play();

}

</script>

</body>
</html>