<?php
header('Content-Type: application/json');

function get_all_bein_matches() {
    $url = "https://www.beinsports.com/ar-mena/tv-guide";
    $options = ["http" => ["header" => "User-Agent: Mozilla/5.0\r\n"]];
    $context = stream_context_create($options);
    $html = @file_get_contents($url, false, $context);

    if (!$html) return [];

    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    $items = $xpath->query("//div[contains(@class, 'event-card')]");
    
    $all_matches = [];
    foreach ($items as $item) {
        $text = $item->nodeValue;
        // البحث عن رقم القناة من 1 إلى 9
        for ($i = 1; $i <= 9; $i++) {
            if (strpos($text, "beIN SPORTS $i") !== false || strpos($text, "beIN SPORTS Premium $i") !== false) {
                $time = $xpath->query(".//time", $item)->item(0)->nodeValue ?? '';
                $title = $xpath->query(".//h3", $item)->item(0)->nodeValue ?? 'مباراة';
                
                $all_matches[$i][] = [
                    "time" => trim($time),
                    "title" => trim($title)
                ];
            }
        }
    }
    return $all_matches;
}

echo json_encode(get_all_bein_matches());
