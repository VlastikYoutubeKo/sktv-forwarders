<?php
header('Content-Type: application/json');

// Vytvoř/otevři SQLite databázi
$db = new SQLite3('viewers.db');

// Vytvoř tabulku pokud neexistuje
$db->exec('CREATE TABLE IF NOT EXISTS viewers (
    channel TEXT,
    session_id TEXT,
    last_seen INTEGER,
    PRIMARY KEY (channel, session_id)
)');

$action = $_GET['action'] ?? '';
$channel = $_GET['channel'] ?? '';

if ($action === 'ping' && !empty($channel)) {
    // Generuj nebo použij session ID
    session_start();
    $sessionId = session_id();
    
    // Aktualizuj nebo vlož záznam
    $stmt = $db->prepare('INSERT OR REPLACE INTO viewers (channel, session_id, last_seen) VALUES (:channel, :session_id, :time)');
    $stmt->bindValue(':channel', $channel, SQLITE3_TEXT);
    $stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
    $stmt->bindValue(':time', time(), SQLITE3_INTEGER);
    $stmt->execute();
    
    // Smaž staré záznamy (starší než 30 sekund)
    $db->exec('DELETE FROM viewers WHERE last_seen < ' . (time() - 30));
    
    echo json_encode(['success' => true]);
} elseif ($action === 'get') {
    // Smaž staré záznamy
    $db->exec('DELETE FROM viewers WHERE last_seen < ' . (time() - 30));
    
    // Získej počet diváků pro každý kanál
    $result = $db->query('SELECT channel, COUNT(*) as viewers FROM viewers GROUP BY channel');
    
    $stats = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $stats[$row['channel']] = (int)$row['viewers'];
    }
    
    echo json_encode($stats);
} elseif ($action === 'leave' && !empty($channel)) {
    session_start();
    $sessionId = session_id();
    
    $stmt = $db->prepare('DELETE FROM viewers WHERE channel = :channel AND session_id = :session_id');
    $stmt->bindValue(':channel', $channel, SQLITE3_TEXT);
    $stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
}

$db->close();
