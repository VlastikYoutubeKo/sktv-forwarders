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
    $db = new SQLite3('viewers.db');
    $stmt = $db->prepare('INSERT OR REPLACE INTO viewers (channel, session_id, last_seen) VALUES (:channel, :session_id, :time)');
    $stmt->bindValue(':channel', $channel, SQLITE3_TEXT);
    $stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
    $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
    $stmt->execute();
    $db->close();
}

// Proxy originální segment
$url = urldecode($url);

$ch = curl_init($url);

$curlHeaders = [];
if (!empty($referrer)) {
    $curlHeaders[] = "Referer: " . urldecode($referrer);
}
$curlHeaders[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36";

curl_setopt_array($ch, [
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_HTTPHEADER => $curlHeaders,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);

if ($response === false) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Forward status code
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
