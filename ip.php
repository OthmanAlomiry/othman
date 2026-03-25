<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل القنوات العربية المباشر</title>
    <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
    <style>
        :root { --main-color: #00d1b2; --bg-dark: #0f1218; --card-bg: #1a1f29; }
        body { background-color: var(--bg-dark); color: #eee; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; }
        .header { background: #000; padding: 20px; text-align: center; border-bottom: 2px solid var(--main-color); position: sticky; top: 0; z-index: 1000; }
        
        /* مشغل الفيديو */
        .player-container { max-width: 900px; margin: 20px auto; background: #000; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.8); }
        
        /* أدوات البحث والتصنيف */
        .controls { max-width: 1000px; margin: 20px auto; padding: 0 15px; display: flex; gap: 10px; flex-wrap: wrap; }
        #searchInput { flex: 2; padding: 12px; border-radius: 8px; border: none; background: var(--card-bg); color: white; min-width: 250px; }
        #categorySelect { flex: 1; padding: 12px; border-radius: 8px; background: var(--card-bg); color: white; border: none; }

        /* شبكة القنوات */
        .channels-container { max-width: 1200px; margin: 20px auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; padding: 15px; }
        .channel-card { background: var(--card-bg); border-radius: 10px; padding: 15px; text-align: center; cursor: pointer; transition: 0.3s; border: 1px solid #2d3442; display: flex; flex-direction: column; align-items: center; }
        .channel-card:hover { transform: scale(1.05); border-color: var(--main-color); background: #242b38; }
        .channel-card img { width: 60px; height: 60px; object-fit: contain; margin-bottom: 10px; border-radius: 5px; background: #fff; padding: 2px; }
        .channel-name { font-size: 0.85rem; font-weight: 600; line-height: 1.4; color: #fff; }
        .channel-group { font-size: 0.7rem; color: var(--main-color); margin-top: 5px; opacity: 0.8; }
        
        .no-results { grid-column: 1 / -1; text-align: center; padding: 50px; color: #777; }
    </style>
</head>
<body>

<div class="header">
    <h1>📺 قنوات عربية بث مباشر</h1>
</div>

<div class="player-container">
    <video id="tv-player" class="video-js vjs-16-9 vjs-big-play-centered" controls preload="auto" data-setup='{}'>
        <p class="vjs-no-js">يرجى تفعيل جافا سكريبت للمشاهدة</p>
    </video>
</div>

<div class="controls">
    <input type="text" id="searchInput" placeholder="بحث عن اسم القناة..." onkeyup="filterChannels()">
    <select id="categorySelect" onchange="filterChannels()">
        <option value="">كل التصنيفات</option>
        </select>
</div>

<div class="channels-container" id="channelsGrid">
    <?php
    // رابط القنوات العربية فقط من iptv-org
    $url = "https://iptv-org.github.io/iptv/languages/ara.m3u";
    $content = @file_get_contents($url);
    
    if (!$content) {
        echo "<div class='no-results'>عذراً، فشل جلب القنوات. تأكد من اتصال السيرفر بالإنترنت.</div>";
        exit;
    }

    $lines = explode("\n", $content);
    $categories = [];

    foreach ($lines as $index => $line) {
        if (strpos($line, '#EXTINF') !== false) {
            // استخراج البيانات
            preg_match('/group-title="([^"]+)"/', $line, $cat_match);
            preg_match('/tvg-logo="([^"]+)"/', $line, $logo_match);
            $name = trim(explode(',', $line)[1] ?? 'قناة غير معروفة');
            $category = $cat_match[1] ?? 'أخرى';
            $logo = $logo_match[1] ?? 'https://via.placeholder.com/60?text=TV';
            $streamUrl = trim($lines[$index + 1] ?? '');

            if (!empty($streamUrl) && strpos($streamUrl, 'http') === 0) {
                $categories[] = $category;
                echo "
                <div class='channel-card' data-name='{$name}' data-category='{$category}' onclick='playStream(\"{$streamUrl}\")'>
                    <img src='{$logo}' onerror=\"this.src='https://via.placeholder.com/60?text=TV'\">
                    <div class='channel-name'>{$name}</div>
                    <div class='channel-group'>{$category}</div>
                </div>";
            }
        }
    }
    $unique_categories = array_unique($categories);
    ?>
</div>

<script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
<script>
    const player = videojs('tv-player');
    
    // ملء قائمة التصنيفات
    const categories = <?php echo json_encode(array_values($unique_categories)); ?>;
    const select = document.getElementById('categorySelect');
    categories.forEach(cat => {
        let opt = document.createElement('option');
        opt.value = cat;
        opt.innerHTML = cat;
        select.appendChild(opt);
    });

    function playStream(url) {
        player.src({ src: url, type: 'application/x-mpegURL' });
        player.play();
        window.scrollTo({ top: 150, behavior: 'smooth' });
    }

    function filterChannels() {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const selectedCat = document.getElementById('categorySelect').value;
        const cards = document.querySelectorAll('.channel-card');

        cards.forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            const category = card.getAttribute('data-category');
            
            const matchesSearch = name.includes(searchText);
            const matchesCat = selectedCat === "" || category === selectedCat;

            if (matchesSearch && matchesCat) {
                card.style.display = "flex";
            } else {
                card.style.display = "none";
            }
        });
    }
</script>

</body>
</html>
