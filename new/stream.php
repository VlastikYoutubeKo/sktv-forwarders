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

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$dir = rtrim(dirname($path), '/');
$baseUrl = $protocol . $host . $dir;

// Detekce robotů (Discord, Telegram, Twitter atd.) - šetříme API limity a nepočítáme je do statistik
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
if (preg_match('/discordbot|twitterbot|telegrambot|vkshare|facebookexternalhit|whatsapp|slackbot|linkedinbot|pinterest|slurp|yahoou|baiduspider|googlebot|bingbot/i', $userAgent)) {
    $countryCode = strtolower($channelData['group']); // 'cz' nebo 'sk'
    $flagUrl = "https://flagcdn.com/w1280/" . $countryCode . ".png";
    
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head>';
    echo '<title>Živě: ' . htmlspecialchars($chName) . ' | SKTV V2</title>';
    echo '<meta property="og:type" content="video.other">';
    echo '<meta property="og:title" content="🔴 Živé vysílání: ' . htmlspecialchars($chName) . '">';
    echo '<meta property="og:description" content="Sledujte kanál ' . htmlspecialchars($chName) . ' živě přes prémiovou SKTV V2 proxy s nulovou zátěží pásma. Kliknutím spustíte stream.">';
    echo '<meta property="og:image" content="' . $flagUrl . '">';
    echo '<meta property="og:url" content="' . $baseUrl . '/stream.php?ch=' . rawurlencode($chName) . '&proxy=' . $proxy . '">';
    echo '<meta name="twitter:card" content="summary_large_image">';
    echo '<meta name="theme-color" content="#8b5cf6">';
    echo '</head><body><script>window.location.href="' . $baseUrl . '";</script></body></html>';
    exit;
}

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
