<?php

// Proxy paths
$SKTV_PROXY_SK = "https://mxnticek.eu/sktv/proxy_sk.php?q=";
$SKTV_PROXY_CZ = "https://mxnticek.eu/sktv/proxy.php?q=";
$ZELVAR_CZ_LEGACY = "https://mxnticek.eu/sktv/proxy.php?q=";

// Kontext pro bypass SSL certifikátů
$ssl_bypass_context = stream_context_create([
    "http" => [
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false
    ]
]);

// Základní fetcher s ošetřením chyb
function safe_fetch($url, $context = null) {
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $headers = [];
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        
        if ($context !== null) {
            $options = stream_context_get_options($context);
            if (isset($options['http']['header'])) {
                $headerLines = explode("\r\n", trim($options['http']['header']));
                foreach ($headerLines as $line) {
                    if (!empty($line)) {
                        $headers[] = $line;
                        if (stripos($line, 'User-Agent:') === 0) {
                            $userAgent = trim(substr($line, 11));
                        }
                    }
                }
            }
        }
        
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($data !== false && $httpCode >= 200 && $httpCode < 400) {
            return $data;
        }
        error_log("CURL failed to fetch: " . $url . " with code " . $httpCode);
        return null;
    }

    global $ssl_bypass_context;
    $ctx = $context ?: $ssl_bypass_context;
    $data = @file_get_contents($url, false, $ctx);
    if ($data === false) {
        error_log("Failed to fetch: " . $url);
        return null;
    }
    return $data;
}

function proxysktv_sk_simple($url) {
    // This server is in SK, so we don't need to bounce it to an external SK proxy
    return safe_fetch($url);
}

function proxysktv_cz_simple($url) {
    global $SKTV_PROXY_CZ;
    return safe_fetch($SKTV_PROXY_CZ . urlencode($url));
}

function proxy_zelvar_simple($url) {
    global $ZELVAR_CZ_LEGACY;
    return safe_fetch($ZELVAR_CZ_LEGACY . urlencode($url));
}

function stv_url_proxy($x) {
    $jsonContent = safe_fetch("https://www.rtvs.sk/json/live5f.json?c=" . $x . "&b=msie&p=win&v=11&f=0&d=1");
    if (!$jsonContent) return null;
    
    $json = json_decode($jsonContent, true);
    $playlisturl = $json["clip"]["sources"][0]["src"] ?? null;
    if (!$playlisturl) return null;

    $playlist = proxysktv_sk_simple($playlisturl);
    if (!$playlist) return null;

    $lines = explode("\n", $playlist);
    foreach ($lines as $index => $line) {
        if (strpos($line, '1080') !== false && isset($lines[$index + 1])) {
            return trim($lines[$index + 1]);
        }
    }
    return count($lines) > 0 ? trim($lines[count($lines)-1]) : null;
}

function markiza_url_proxy($x, $tn = false) {
    $siteurl = "https://media.cms.markiza.sk/embed/" . $x . ($tn ? "" : "-live") . "?autoplay=any";
    $sitecontent = proxysktv_sk_simple($siteurl);
    if (!$sitecontent) return null;
    preg_match('/\[{"src":"([^"]+)"/', $sitecontent, $matches);
    return isset($matches[1]) ? str_replace("\\", "", $matches[1]) : null;
}

function nova_url($x, $tn = false) {
    $content = safe_fetch("https://media" . ($tn ? "tn" : "") . ".cms.nova.cz/embed/" . $x . ($tn ? "" : "live") . "?autoplay=1");
    if (!$content) return null;
    preg_match('/\[{"src":"([^"]+)"/', $content, $matches);
    return isset($matches[1]) ? str_replace("\\", "", $matches[1]) : null;
}

function ta3_url() {
    $content = safe_fetch("https://embed.livebox.cz/ta3_v2/live-source.js");
    preg_match('/" : "([^"]+)"/', $content, $matches);
    return isset($matches[1]) ? "https:" . $matches[1] : null;
}

function cnn_portugal() {
    $endpoint = json_decode(safe_fetch("https://front-api.iol.pt/api/v1/live/broadcast?canal=CNN"), true);
    $auth = safe_fetch("https://services.iol.pt/matrix?userId=", stream_context_create(["http"=>["header"=>"User-Agent: Mozilla/5.0"]]));
    return ($endpoint["videoUrl"] ?? "") . "wmsAuthSign=" . $auth;
}

function ceskatelevize($code) {
    $response = safe_fetch("https://api.ceskatelevize.cz/video/v1/playlist-live/v1/stream-data/channel/" . $code . "?canPlayDrm=false&streamType=dash&quality=1080p");
    if (!$response) return null;
    $manifest = json_decode($response, true);
    return $manifest["streamUrls"]["main"] ?? null;
}

function prima($id) {
    $response = safe_fetch("https://api.play-backend.iprima.cz/api/v1/products/" . $id . "/play");
    if (!$response) return null;
    $primajson = json_decode($response);
    $url = $primajson->streamInfos[0]->url ?? null;
    return $url ? str_replace("lq.m3u8", "hd.m3u8", $url) : null;
}

$channel = $_GET["x"] ?? '';
$forge = isset($_GET['forge']) && $_GET['forge'] === 'true';
$streamUrl = null;

// Mapování kanálů
$channels = [
    "TA3" => "ta3_url",
    "STV1" => ["stv_url_proxy", "1"],
    "STV2" => ["stv_url_proxy", "2"],
    "STV24" => ["stv_url_proxy", "3"],
    "STV-O" => ["stv_url_proxy", "4"],
    "RTVS" => ["stv_url_proxy", "6"],
    "NR_SR" => ["stv_url_proxy", "5"],
    "SPORT" => ["stv_url_proxy", "15"],
    "Markiza" => ["markiza_url_proxy", "markiza"],
    "Doma" => ["markiza_url_proxy", "doma"],
    "Dajto" => ["markiza_url_proxy", "dajto"],
    "Krimi" => ["markiza_url_proxy", "krimi"],
    "Klasik" => ["markiza_url_proxy", "klasik"],
    "MarkizaTNLive" => ["markiza_url_proxy", "BQeGg0uPHJP", true],
    "Nova" => ["nova_url", "nova-"],
    "NovaFun" => ["nova_url", "nova-2-"],
    "NovaLady" => ["nova_url", "nova-lady-"],
    "NovaGold" => ["nova_url", "nova-gold-"],
    "NovaCinema" => ["nova_url", "nova-cinema-"],
    "NovaAction" => ["nova_url", "nova-action-"],
    "NovaTNLive" => ["nova_url", "ETpdC5paJa8", true],
    "CNN_Portugal" => "cnn_portugal",
    "CT1" => ["ceskatelevize", "CH_1"],
    "CT2" => ["ceskatelevize", "CH_2"],
    "CT24" => ["ceskatelevize", "CH_24"],
    "CTsport" => ["ceskatelevize", "CH_4"],
    "CT_D" => ["ceskatelevize", "CH_5"],
    "CTart" => ["ceskatelevize", "CH_6"],
    "CTsportPlus" => ["ceskatelevize", "CH_25"]
];

if (isset($channels[$channel])) {
    $func = $channels[$channel];
    $streamUrl = is_array($func) ? call_user_func_array($func[0], array_slice($func, 1)) : call_user_func($func);
} elseif (strpos($channel, "Prima") !== false) {
    $ids = ["Prima"=>"id-p111013","PrimaCool"=>"id-p111014","PrimaZoom"=>"id-p111015","PrimaLove"=>"id-p111016","PrimaMax"=>"id-p111017","PrimaKrimi"=>"id-p432829","PrimaNews"=>"id-p650443","PrimaStar"=>"id-p846043","PrimaShow"=>"id-p899572"];
    $id = $ids[$channel] ?? null;
    if ($id) {
        $rawUrl = prima($id);
        $streamUrl = ($forge && $rawUrl) ? $SKTV_PROXY_CZ . urlencode($rawUrl) . "&m3u8-forge=true" : $rawUrl;
    }
} elseif (in_array($channel, ["JOJ","JOJP","Wau","JOJ24","JOJFamily","JOJCinema","JOJSport","Jojko","CSFilm","CSHistory","CSMystery"])) {
    $map = ["JOJ"=>"joj-1080","JOJP"=>"plus-1080","Wau"=>"wau-1080","JOJ24"=>"joj_news-1080","JOJFamily"=>"family-1080","JOJCinema"=>"cinema-1080","JOJSport"=>"joj_sport-1080","Jojko"=>"jojko-1080","CSFilm"=>"cs_film-1080","CSHistory"=>"cs_history-1080","CSMystery"=>"cs_mystery-1080"];
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/" . $map[$channel] . ".m3u8";
}

// Urči referrer pro stream.php
$referrer = null;
if (strpos($channel, "Nova") !== false) {
    $referrer = "https://media.cms.nova.cz/";
} elseif (strpos($channel, "Markiza") !== false || in_array($channel, ["Doma", "Dajto", "Krimi", "Klasik"])) {
    $referrer = "https://media.cms.markiza.sk/";
} elseif (in_array($channel, ["JOJ","JOJP","Wau","JOJ24","JOJFamily","JOJCinema","JOJSport","Jojko","CSFilm","CSHistory","CSMystery"])) {
    $referrer = "https://media.joj.sk/";
}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    if ($streamUrl) {
        echo $streamUrl;
    } else {
        header("HTTP/1.1 404 Not Found");
        echo "Stream not found";
    }
}
?>