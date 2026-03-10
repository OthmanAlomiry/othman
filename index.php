<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>بث القنوات الرياضية</title>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

<style>

body{
margin:0;
font-family:Tahoma;
background:linear-gradient(135deg,#020617,#0f172a);
color:white;
text-align:center;
}

header{
padding:25px;
font-size:28px;
font-weight:bold;
background:#020617;
box-shadow:0 4px 20px rgba(0,0,0,0.6);
}

.channels{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:30px;
padding:40px;
max-width:1100px;
margin:auto;
}

.card{
background:#0f172a;
border-radius:14px;
overflow:hidden;
box-shadow:0 10px 25px rgba(0,0,0,0.7);
transition:0.3s;
}

.card:hover{
transform:translateY(-8px);
box-shadow:0 20px 35px rgba(0,0,0,0.9);
}

.channel-title{
background:#111827;
padding:12px;
font-size:18px;
}

video{
width:100%;
background:black;
}

.play{
margin:15px;
padding:10px 18px;
font-size:16px;
border:none;
border-radius:8px;
background:#e11d48;
color:white;
cursor:pointer;
transition:0.3s;
}

.play:hover{
background:#be123c;
}

footer{
margin-top:20px;
padding:15px;
font-size:14px;
opacity:0.6;
}

</style>
</head>

<body>

<header>
📺 البث المباشر للقنوات
</header>

<div class="channels">

<div class="card">
<div class="channel-title">beIN Sport 1</div>
<video id="v1" controls></video>
<button class="play" onclick="playStream('v1','b1.php')">▶ تشغيل</button>
</div>

<div class="card">
<div class="channel-title">beIN Sport 2</div>
<video id="v2" controls></video>
<button class="play" onclick="playStream('v2','b2.php')">▶ تشغيل</button>
</div>

<div class="card">
<div class="channel-title">beIN Sport 3</div>
<video id="v3" controls></video>
<button class="play" onclick="playStream('v3','b3.php')">▶ تشغيل</button>
</div>

</div>

<footer>

قنوات مجانية بدون إعلانات
يمكنك الاشتراك في الباقة الكاملة
وتعمل على الجوالات والشاشات الذكية
تواصل عبر الواتساب
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