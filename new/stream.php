<?php
// stream.php
require_once 'fetcher.php';
$channels = require 'channels.php';

$chName = $_GET['ch'] ?? '';
$proxy = isset($_GET['proxy']) ? (int)$_GET['proxy'] : 0; // Default to direct stream (0 bandwidth)

if (!isset($channels[$chName])) {
    header("HTTP/1.1 404 Not Found");
    die("Channel not found");
}

$channelData = $channels[$chName];
$fetcher = $channelData['fetcher'];
$id = $channelData['id'];
$referer = $channelData['referer'];

// Ping stats
try {
    $db = new SQLite3("viewers.db");
    $db->exec("CREATE TABLE IF NOT EXISTS viewers (channel TEXT, session_id TEXT, last_seen INTEGER, PRIMARY KEY (channel, session_id))");
    $stmt = $db->prepare("INSERT OR REPLACE INTO viewers (channel, session_id, last_seen) VALUES (:channel, :session_id, :time)");
    $stmt->bindValue(":channel", $chName, SQLITE3_TEXT);
    $stmt->bindValue(":session_id", session_id() ?: md5($_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]), SQLITE3_TEXT);
    $stmt->bindValue(":time", time(), SQLITE3_INTEGER);
    $stmt->execute();
    $db->close();
} catch (Exception $e) {}
$rawUrl = call_user_func($fetcher, $id);

if (!$rawUrl) {
    header("HTTP/1.1 404 Not Found");
    die("Stream URL could not be fetched from API");
}

$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

if ($proxy === 1) {
    // Proxy mód - stáhne M3U8 a přesměruje segmenty přes proxy.php
    $m3u8Content = curl_fetch($rawUrl, $referer);
    if (!$m3u8Content) {
        header("HTTP/1.1 404 Not Found");
        die("Could not fetch M3U8 manifest");
    }
    
    header('Content-Type: application/vnd.apple.mpegurl');
    header('Cache-Control: no-cache');
    
    $lines = explode("\n", $m3u8Content);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        if (strpos($line, '#') === 0 && strpos($line, 'URI=') !== false) {
            $line = preg_replace_callback('/URI="([^"]+)"/', function($m) use ($rawUrl, $referer) {
                $uri = $m[1];
                $abs = makeAbsoluteUrl($uri, $rawUrl);
                return 'URI="proxy.php?url=' . urlencode($abs) . '&ref=' . urlencode($referer) . '"';
            }, $line);
            echo $line . "\n";
        } else if (strpos($line, '#') !== 0) {
            $abs = makeAbsoluteUrl($line, $rawUrl);
            echo 'proxy.php?url=' . urlencode($abs) . '&ref=' . urlencode($referer) . "\n";
        } else {
            echo $line . "\n";
        }
    }
} else {
    // Direct mode - ZERO bandwidth na serveru!
    if (!empty($referer)) {
        // Pokud kanál vyžaduje referer (Markíza, Nova), pošleme VLC příkaz, aby referer použilo
        header('Content-type: application/x-mpegURL');
        echo "#EXTVLCOPT:http-referrer=" . $referer . "\n" . "#EXTVLCOPT:adaptive-use-access" . "\n" . $rawUrl;
        exit;
    } else {
        // Jinak přímé HTTP přesměrování (302 Redirect)
        header('Location: ' . $rawUrl);
        exit;
    }
}

function makeAbsoluteUrl($url, $base) {
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) return $url;
    $baseParts = parse_url($base);
    if ($url[0] == '/') return $baseParts['scheme'] . '://' . $baseParts['host'] . $url;
    $path = isset($baseParts['path']) ? dirname($baseParts['path']) : '';
    return $baseParts['scheme'] . '://' . $baseParts['host'] . $path . '/' . $url;
}
