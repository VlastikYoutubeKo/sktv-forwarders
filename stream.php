<?php
session_start();

if (!isset($_GET["x"])) {
    die("Channel not set!");
}

$channel = $_GET["x"];
$sessionId = session_id();

// Zaznamenej viewing
try {
    $db = new SQLite3('viewers.db');
    $db->exec('CREATE TABLE IF NOT EXISTS viewers (
        channel TEXT,
        session_id TEXT,
        last_seen INTEGER,
        PRIMARY KEY (channel, session_id)
    )');

    $stmt = $db->prepare('INSERT OR REPLACE INTO viewers (channel, session_id, last_seen) VALUES (:channel, :session_id, :time)');
    $stmt->bindValue(':channel', $channel, SQLITE3_TEXT);
    $stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
    $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
    $stmt->execute();
    $db->close();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
}

// Získej URL z get_url.php
include 'get_url.php';

if (!isset($streamUrl) || empty($streamUrl)) {
    header("HTTP/1.1 404 Not Found");
    die("Stream not found");
}

// Načti obsah streamu
$context = null;
if (isset($referrer) && !empty($referrer)) {
    $context = stream_context_create([
        'http' => [
            'header' => "Referer: " . $referrer . "\r\n"
        ]
    ]);
}

$content = @file_get_contents($streamUrl, false, $context);

if ($content === false) {
    header("HTTP/1.1 404 Not Found");
    die("Stream not available");
}

// Detekuj typ streamu
$isDash = (strpos($content, '<MPD') !== false || strpos($content, '<?xml') !== false);
$isM3u8 = (strpos($content, '#EXTM3U') !== false);

if ($isDash) {
    // DASH/MPD formát - prostě redirectni přímo na originální URL s trackingem v header
    // Přidej tracking ping
    header('Content-Type: application/dash+xml');
    header('Cache-Control: no-cache');
    
    // Prostě výpis originálního obsahu - neparsuj XML!
    echo $content;
    
} else if ($isM3u8) {
    // M3U8 formát - zpracuj a přidej proxy
    header('Content-Type: application/vnd.apple.mpegurl');
    header('Cache-Control: no-cache');
    
    // Přidej EXTVLCOPT pokud je referrer
    if (isset($referrer) && !empty($referrer)) {
        echo "#EXTM3U\n";
        echo "#EXTVLCOPT:http-referrer=" . $referrer . "\n";
        echo "#EXTVLCOPT:adaptive-use-access\n";
    }
    
    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line)) {
            continue;
        }
        
        // Přeskoč #EXTM3U na začátku pokud už jsme ho vypsali
        if ($line === '#EXTM3U' && isset($referrer) && !empty($referrer)) {
            continue;
        }
        
        // Pokud je to URL (ne komentář)
        if (strpos($line, '#') !== 0 && !empty($line)) {
            // Udělej absolutní URL pokud je relativní
            if (strpos($line, 'http') !== 0) {
                $baseUrl = dirname($streamUrl);
                $line = $baseUrl . '/' . $line;
            }
            
            // Proxy URL přes náš tracking
            $encodedUrl = urlencode($line);
            $proxyLine = "segment.php?url={$encodedUrl}&channel={$channel}&session=" . urlencode($sessionId);
            if (isset($referrer) && !empty($referrer)) {
                $proxyLine .= "&ref=" . urlencode($referrer);
            }
            echo $proxyLine . "\n";
        } else {
            echo $line . "\n";
        }
    }
} else {
    // Neznámý formát - přesměruj přímo
    header("Location: " . $streamUrl);
    exit;
}
