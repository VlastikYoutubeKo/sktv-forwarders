<?php
session_start();

if (!isset($_GET["x"])) {
    die("Channel not set!");
}

$channel = $_GET["x"];
$sessionId = session_id();

// Zaznamenej viewing
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

// Získej URL z get_url.php
include 'get_url.php';

if (isset($streamUrl) && !empty($streamUrl)) {
    // Pokud má kanál referrer, nastav ho
    if (isset($referrer) && !empty($referrer)) {
        // Pro M3U8 s referrerem vypiš playlist s #EXTVLCOPT
        header('Content-Type: application/vnd.apple.mpegurl');
        header('Cache-Control: no-cache');
        
        echo "#EXTM3U\n";
        echo "#EXTVLCOPT:http-referrer=" . $referrer . "\n";
        echo "#EXTVLCOPT:adaptive-use-access\n";
        
        // Načti originální M3U8
        $context = stream_context_create([
            'http' => [
                'header' => "Referer: " . $referrer . "\r\n"
            ]
        ]);
        
        $m3u8Content = @file_get_contents($streamUrl, false, $context);
        
        if ($m3u8Content === false) {
            header("HTTP/1.1 404 Not Found");
            die("Stream not available");
        }
        
        // Zpracuj M3U8 a přidej tracking proxy
        $lines = explode("\n", $m3u8Content);
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) {
                continue;
            }
            
            // Přeskoč #EXTM3U na začátku (už jsme ho vypsali)
            if ($line === '#EXTM3U') {
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
                echo "segment.php?url={$encodedUrl}&channel={$channel}&session=" . urlencode($sessionId) . "&ref=" . urlencode($referrer) . "\n";
            } else {
                echo $line . "\n";
            }
        }
    } else {
        // Bez referreru
        // Načti originální M3U8
        $m3u8Content = @file_get_contents($streamUrl);
        
        if ($m3u8Content === false) {
            header("HTTP/1.1 404 Not Found");
            die("Stream not available");
        }
        
        // Zpracuj M3U8 a přidej tracking proxy
        header('Content-Type: application/vnd.apple.mpegurl');
        header('Cache-Control: no-cache');
        
        $lines = explode("\n", $m3u8Content);
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) {
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
                echo "segment.php?url={$encodedUrl}&channel={$channel}&session=" . urlencode($sessionId) . "\n";
            } else {
                echo $line . "\n";
            }
        }
    }
} else {
    header("HTTP/1.1 404 Not Found");
    die("Stream not found");
}
