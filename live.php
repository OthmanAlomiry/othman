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

.logo{
padding:15px;
background:#f3f4f6;
}

.logo img{
width:120px;
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

footer{
padding:20px;
font-size:14px;
color:#555;
}

</style>
</head>

<body>

<header>
📺 البث المباشر للقنوات الرياضية
</header>

<div class="channels">

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN1"></div>
<video id="v1" controls></video>
<button class="play" onclick="playStream('v1','b1.php')">تشغيل</button>
</div>

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN2"></div>
<video id="v2" controls></video>
<button class="play" onclick="playStream('v2','b2.php')">تشغيل</button>
</div>

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN3"></div>
<video id="v3" controls></video>
<button class="play" onclick="playStream('v3','b3.php')">تشغيل</button>
</div>

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN4"></div>
<video id="v4" controls></video>
<button class="play" onclick="playStream('v4','b4.php')">تشغيل</button>
</div>

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN5"></div>
<video id="v5" controls></video>
<button class="play" onclick="playStream('v5','b5.php')">تشغيل</button>
</div>

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN6"></div>
<video id="v6" controls></video>
<button class="play" onclick="playStream('v6','b6.php')">تشغيل</button>
</div>

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN7"></div>
<video id="v7" controls></video>
<button class="play" onclick="playStream('v7','b7.php')">تشغيل</button>
</div>

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN8"></div>
<video id="v8" controls></video>
<button class="play" onclick="playStream('v8','b8.php')">تشغيل</button>
</div>

<div class="card">
<div class="logo"><img src="https://via.placeholder.com/120x60?text=beIN9"></div>
<video id="v9" controls></video>
<button class="play" onclick="playStream('v9','b9.php')">تشغيل</button>
</div>

</div>

<footer>
جميع القنوات تعمل على الجوال والشاشات الذكية<br>
للاشتراك في الباقة الكاملة تواصل واتساب<br>
0505571164
</footer>

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