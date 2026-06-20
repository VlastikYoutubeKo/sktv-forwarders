<?php
// proxy.php
// Highly optimized chunk proxy for M3U8 and TS segments

$url = $_GET['url'] ?? '';
$referer = $_GET['ref'] ?? '';

if (empty($url)) {
    header("HTTP/1.1 400 Bad Request");
    die("No URL provided");
}

$url = urldecode($url);

$parsed_url = parse_url($url);
if (!isset($parsed_url["host"])) {
    header("HTTP/1.1 400 Bad Request");
    die("Invalid URL format");
}
$ip = gethostbyname($parsed_url["host"]);
if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
    header("HTTP/1.1 403 Forbidden");
    die("SSRF Protection: Cannot proxy to internal networks.");
}

$isM3u8Url = (stripos(parse_url($url, PHP_URL_PATH), '.m3u8') !== false) || (stripos($url, 'm3u8') !== false);

$curlHeaders = [];
if (!empty($referer)) {
    $curlHeaders[] = "Referer: " . urldecode($referer);
}
$curlHeaders[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36";

if (!$isM3u8Url) {
    // STREAMUJ PŘÍMO bez paměťového bufferu - zamezuje stutteringu (video proxy)
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => $curlHeaders,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HEADERFUNCTION => function($curl, $header) {
            // Přepošli bezpečné hlavičky
            if (stripos($header, 'Transfer-Encoding:') === false && 
                stripos($header, 'Content-Encoding:') === false && 
                stripos($header, 'HTTP/') === false) {
                header($header, false);
            }
            return strlen($header);
        },
        CURLOPT_WRITEFUNCTION => function($curl, $data) {
            echo $data;
            if (ob_get_level() > 0) ob_flush();
            flush();
            return strlen($data);
        }
    ]);
    curl_exec($ch);
    curl_close($ch);
    exit;
}

// Nahradíme proxy za externí proxy mxnticek.eu
$is_sk = (strpos($url, 'rtvs.sk') !== false || strpos($url, 'markiza.sk') !== false || strpos($url, 'joj.sk') !== false);
$proxy_base = $is_sk ? "https://mxnticek.eu/sktv/proxy_sk.php?q=" : "https://mxnticek.eu/sktv/proxy.php?q=";
$target_url = $proxy_base . urlencode($url);

// M3U8 Playlist (nested) -> Zpracuj a rewrite URL
$ch = curl_init($target_url);
curl_setopt_array($ch, [
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_HTTPHEADER => $curlHeaders,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($ch);

if ($response === false) {
    header("HTTP/1.1 500 Internal Server Error");
    die("CURL Error");
}

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    http_response_code($httpCode);
    die("HTTP Error");
}

header('Content-Type: application/vnd.apple.mpegurl');
header('Cache-Control: no-cache');

$lines = explode("\n", $body);
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;
    
    if (strpos($line, '#') === 0 && strpos($line, 'URI=') !== false) {
        $line = preg_replace_callback('/URI="([^"]+)"/', function($m) use ($url, $referer) {
            $uri = $m[1];
            $abs = makeAbsoluteUrl($uri, $url);
            return 'URI="proxy.php?url=' . urlencode($abs) . '&ref=' . urlencode($referer) . '"';
        }, $line);
        echo $line . "\n";
    } else if (strpos($line, '#') !== 0) {
        $abs = makeAbsoluteUrl($line, $url);
        echo 'proxy.php?url=' . urlencode($abs) . '&ref=' . urlencode($referer) . "\n";
    } else {
        echo $line . "\n";
    }
}

function makeAbsoluteUrl($url, $base) {
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) return $url;
    $baseParts = parse_url($base);
    if ($url[0] == '/') return $baseParts['scheme'] . '://' . $baseParts['host'] . $url;
    $path = isset($baseParts['path']) ? dirname($baseParts['path']) : '';
    return $baseParts['scheme'] . '://' . $baseParts['host'] . $path . '/' . $url;
}
