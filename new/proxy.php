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

$isM3u8Url = (stripos(parse_url($url, PHP_URL_PATH), '.m3u8') !== false) || (stripos($url, 'm3u8') !== false);

$curlHeaders = [];
if (!empty($referer)) {
    $curlHeaders[] = "Referer: " . urldecode($referer);
}
$curlHeaders[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36";

$is_sk = (strpos($url, 'rtvs.sk') !== false || strpos($url, 'markiza.sk') !== false || strpos($url, 'joj.sk') !== false || strpos($url, 'cmesk-ott') !== false);
$is_cz = (strpos($url, 'iprima.cz') !== false || strpos($url, 'nova.cz') !== false || strpos($url, 'nova-ott') !== false || strpos($url, 'prima-ott') !== false);

if (file_exists(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}
$cz_proxies = $cz_proxies ?? [];
$sk_proxies = $sk_proxies ?? [];

$ch = curl_init($url);
$options = [
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => $curlHeaders,
    CURLOPT_TIMEOUT => $isM3u8Url ? 15 : 60,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
];

if ($is_sk && !empty($sk_proxies)) {
    $randomProxy = $sk_proxies[array_rand($sk_proxies)];
    if (substr_count($randomProxy, ':') >= 2) {
        list($proxyHost, $proxyPort, $proxyUser, $proxyPass) = explode(':', $randomProxy);
        $options[CURLOPT_PROXY] = $proxyHost . ':' . $proxyPort;
        $options[CURLOPT_PROXYUSERPWD] = $proxyUser . ':' . $proxyPass;
    } else {
        $options[CURLOPT_PROXY] = $randomProxy;
    }
} elseif ($is_cz && !empty($cz_proxies)) {
    $randomProxy = $cz_proxies[array_rand($cz_proxies)];
    if (substr_count($randomProxy, ':') >= 2) {
        list($proxyHost, $proxyPort, $proxyUser, $proxyPass) = explode(':', $randomProxy);
        $options[CURLOPT_PROXY] = $proxyHost . ':' . $proxyPort;
        $options[CURLOPT_PROXYUSERPWD] = $proxyUser . ':' . $proxyPass;
    } else {
        $options[CURLOPT_PROXY] = $randomProxy;
    }
}

if (!$isM3u8Url) {
    // STREAMUJ PŘÍMO bez paměťového bufferu - zamezuje stutteringu
    $options[CURLOPT_HEADERFUNCTION] = function($curl, $header) {
        if (stripos($header, 'Transfer-Encoding:') === false && 
            stripos($header, 'Content-Encoding:') === false && 
            stripos($header, 'HTTP/') === false) {
            header($header, false);
        }
        return strlen($header);
    };
    $options[CURLOPT_WRITEFUNCTION] = function($curl, $data) {
        echo $data;
        if (ob_get_level() > 0) ob_flush();
        flush();
        return strlen($data);
    };
    curl_setopt_array($ch, $options);
    curl_exec($ch);
    curl_close($ch);
    exit;
}

// Pro M3U8 stáhneme do paměti a nahradíme cesty
$options[CURLOPT_RETURNTRANSFER] = true;
$options[CURLOPT_HEADER] = true;
curl_setopt_array($ch, $options);

$response = curl_exec($ch);

if ($response === false) {
    header("HTTP/1.1 500 Internal Server Error");
    die("CURL Error: " . curl_error($ch));
}

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    http_response_code($httpCode);
    die("HTTP Error: " . $httpCode);
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
