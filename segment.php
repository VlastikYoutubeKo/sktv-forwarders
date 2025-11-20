<?php
session_start();

$url = $_GET['url'] ?? '';
$channel = $_GET['channel'] ?? '';
$sessionId = $_GET['session'] ?? session_id();
$referrer = $_GET['ref'] ?? '';

if (empty($url)) {
    header("HTTP/1.1 400 Bad Request");
    die("URL not provided");
}

// Aktualizuj last_seen při každém načtení segmentu
if (!empty($channel)) {
    try {
        $db = new SQLite3('viewers.db');
        $stmt = $db->prepare('INSERT OR REPLACE INTO viewers (channel, session_id, last_seen) VALUES (:channel, :session_id, :time)');
        $stmt->bindValue(':channel', $channel, SQLITE3_TEXT);
        $stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
        $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
        $stmt->execute();
        $db->close();
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

// Funkce pro vytvoření absolutní URL
function makeAbsoluteUrl($url, $base) {
    // Už je absolutní
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }
    
    $baseParts = parse_url($base);
    
    // Začíná lomítkem - absolutní cesta
    if ($url[0] == '/') {
        return $baseParts['scheme'] . '://' . $baseParts['host'] . $url;
    }
    
    // Relativní cesta
    $path = isset($baseParts['path']) ? dirname($baseParts['path']) : '';
    return $baseParts['scheme'] . '://' . $baseParts['host'] . $path . '/' . $url;
}

// Proxy originální segment/playlist
$url = urldecode($url);

// Ochrana proti rekurzi - max 3 úrovně vnořených M3U8
$depth = intval($_GET['depth'] ?? 0);
if ($depth > 3) {
    header("HTTP/1.1 508 Loop Detected");
    die("Too many nested playlists");
}

$ch = curl_init($url);

$curlHeaders = array();
if (!empty($referrer)) {
    $curlHeaders[] = "Referer: " . urldecode($referrer);
}
$curlHeaders[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36";

curl_setopt_array($ch, array(
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_HTTPHEADER => $curlHeaders,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false
));

$response = curl_exec($ch);

if ($response === false) {
    header("HTTP/1.1 500 Internal Server Error");
    error_log("CURL error for URL $url: " . curl_error($ch));
    echo "Error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    http_response_code($httpCode);
    error_log("HTTP error $httpCode for URL $url");
    echo "HTTP Error: $httpCode";
    exit;
}

// Zkontroluj jestli je to M3U8 playlist (nested)
$isM3u8 = (strpos($body, '#EXTM3U') !== false);

if ($isM3u8) {
    // Je to další M3U8 playlist - zpracuj ho!
    header('Content-Type: application/vnd.apple.mpegurl');
    header('Cache-Control: no-cache');
    
    $lines = explode("\n", $body);
    $nextDepth = $depth + 1;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line)) {
            continue;
        }
        
        // Pokud je to URL v tagu (např. #EXT-X-KEY:METHOD=AES-128,URI="...")
        if (strpos($line, '#') === 0 && strpos($line, 'URI=') !== false) {
            // Zpracuj URI v tagu pomocí preg_replace_callback
            $processedLine = preg_replace_callback(
                '/URI="([^"]+)"/',
                function($matches) use ($url, $channel, $sessionId, $referrer, $nextDepth) {
                    $uri = $matches[1];
                    $absoluteUrl = makeAbsoluteUrl($uri, $url);
                    
                    // Proxy přes segment.php
                    $encodedUrl = urlencode($absoluteUrl);
                    $proxyUrl = "segment.php?url={$encodedUrl}&channel={$channel}&session=" . urlencode($sessionId) . "&depth={$nextDepth}";
                    if (!empty($referrer)) {
                        $proxyUrl .= "&ref=" . urlencode($referrer);
                    }
                    return 'URI="' . $proxyUrl . '"';
                },
                $line
            );
            echo $processedLine . "\n";
        }
        // Pokud je to URL segment (ne komentář)
        else if (strpos($line, '#') !== 0 && !empty($line)) {
            // Udělej absolutní URL
            $absoluteUrl = makeAbsoluteUrl($line, $url);
            
            // Proxy URL přes náš tracking
            $encodedUrl = urlencode($absoluteUrl);
            $proxyLine = "segment.php?url={$encodedUrl}&channel={$channel}&session=" . urlencode($sessionId) . "&depth={$nextDepth}";
            if (!empty($referrer)) {
                $proxyLine .= "&ref=" . urlencode($referrer);
            }
            echo $proxyLine . "\n";
        } else {
            // Komentář nebo tag bez URI
            echo $line . "\n";
        }
    }
} else {
    // Je to normální segment (video/audio data) - forward headers a tělo
    http_response_code($httpCode);
    
    // Forward headers (kromě Transfer-Encoding)
    foreach (explode("\r\n", $headers) as $header) {
        if ($header && 
            stripos($header, 'HTTP/') === false && 
            stripos($header, 'Transfer-Encoding:') === false &&
            stripos($header, 'Content-Encoding:') === false) {
            header($header);
        }
    }
    
    echo $body;
}
