<?php

// Proxy paths
$SKTV_PROXY_SK = "https://mxnticek.eu/sktv/proxy_sk.php?q=";
$SKTV_PROXY_CZ = "https://mxnticek.eu/sktv/proxy.php?q=";
$ZELVAR_CZ_LEGACY = "https://mxnticek.eu/sktv/proxy.php?q=";

function loc($x) {
    header("Location: " . $x);
    die();
}

function notfound($x) {
    header($_SERVER['SERVER_PROTOCOL']." 301 Moved Permanently", true );
    header("Location: " . $x);
    die();
}

function m3u8_refer($url, $referer) {
    header('Content-type: application/x-mpegURL');
    echo "#EXTVLCOPT:http-referrer=" . $referer . "\n" . "#EXTVLCOPT:adaptive-use-access" . "\n" . $url;
    die();
}

$ssl_bypass_context = stream_context_create([
    "http" => [
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false
    ]
]);

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

header("Content-Type: text/plain");

if (!isset($_GET["x"])) die("Channel not set! (?x=)");
$channel = $_GET["x"];
$forge = isset($_GET['forge']) && $_GET['forge'] === 'true';

if ($channel == "TA3") {
    loc(ta3_url());
}
else if ($channel == "STV1") {
    loc(stv_url_proxy("1"));
}
else if ($channel == "STV2") {
    loc(stv_url_proxy("2"));
}
else if ($channel == "STV24") {
    loc(stv_url_proxy("3"));
}
else if ($channel == "STV-O") {
    loc(stv_url_proxy("4"));
}
else if ($channel == "RTVS") {
    loc(stv_url_proxy("6"));
}
else if ($channel == "NR_SR") {
    loc(stv_url_proxy("5"));
}
else if ($channel == "SPORT") {
    loc(stv_url_proxy("15"));
}
else if ($channel == "Markiza") {
    m3u8_refer(markiza_url_proxy("markiza"), "https://media.cms.markiza.sk/");
}
else if ($channel == "Doma") {
    m3u8_refer(markiza_url_proxy("doma"), "https://media.cms.markiza.sk/");
}
else if ($channel == "Dajto") {
    m3u8_refer(markiza_url_proxy("dajto"), "https://media.cms.markiza.sk/");
}
else if ($channel == "Krimi") {
    m3u8_refer(markiza_url_proxy("krimi"), "https://media.cms.markiza.sk/");
}
else if ($channel == "Klasik") {
    m3u8_refer(markiza_url_proxy("klasik"), "https://media.cms.markiza.sk/");
}
else if ($channel == "MarkizaTNLive") {
    m3u8_refer(markiza_url_proxy("BQeGg0uPHJP", true), "https://media.cms.markiza.sk/");
}
else if ($channel == "JOJ") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/joj-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "JOJP") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/plus-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "Wau") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/wau-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "JOJ24") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/joj_news-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "JOJFamily") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/family-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "JOJCinema") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/cinema-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "JOJSport") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/joj_sport-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "Jojko") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/jojko-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "CSFilm") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/cs_film-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "CSHistory") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/cs_history-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "CSMystery") {
    m3u8_refer("https://live.cdn.joj.sk/live/andromeda/cs_mystery-1080.m3u8", "https://media.joj.sk/");
}
else if ($channel == "Nova") {
    m3u8_refer(nova_url("nova-"), "https://media.cms.nova.cz/");
}
else if ($channel == "NovaFun") {
    m3u8_refer(nova_url("nova-2-"), "https://media.cms.nova.cz/");
}
else if ($channel == "NovaLady") {
    m3u8_refer(nova_url("nova-lady-"), "https://media.cms.nova.cz/");
}
else if ($channel == "NovaGold") {
    m3u8_refer(nova_url("nova-gold-"), "https://media.cms.nova.cz/");
}
else if ($channel == "NovaCinema") {
    m3u8_refer(nova_url("nova-cinema-"), "https://media.cms.nova.cz/");
}
else if ($channel == "NovaAction") {
    m3u8_refer(nova_url("nova-action-"), "https://media.cms.nova.cz/");
}
else if ($channel == "NovaTNLive") {
    loc(nova_url("ETpdC5paJa8", true));
}
else if ($channel == "CNN_Portugal") {
    loc(cnn_portugal());
}
else if ($channel == "CT1") {
    loc(ceskatelevize("CH_1"));
}
else if ($channel == "CT2") {
    loc(ceskatelevize("CH_2"));
}
else if ($channel == "CT24") {
    loc(ceskatelevize("CH_24"));
}
else if ($channel == "CTsport") {
    loc(ceskatelevize("CH_4"));
}
else if ($channel == "CT_D") {
    loc(ceskatelevize("CH_5"));
}
else if ($channel == "CTart") {
    loc(ceskatelevize("CH_6"));
}
else if ($channel == "CTsportPlus") {
    loc(ceskatelevize("CH_25"));
}
else if ($channel == "Prima") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p111013")) . "&m3u8-forge=true");
    else loc(prima("id-p111013"));
}
else if ($channel == "PrimaCool") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p111014")) . "&m3u8-forge=true");
    else loc(prima("id-p111014"));
}
else if ($channel == "PrimaZoom") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p111015")) . "&m3u8-forge=true");
    else loc(prima("id-p111015"));
}
else if ($channel == "PrimaLove") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p111016")) . "&m3u8-forge=true");
    else loc(prima("id-p111016"));
}
else if ($channel == "PrimaMax") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p111017")) . "&m3u8-forge=true");
    else loc(prima("id-p111017"));
}
else if ($channel == "PrimaKrimi") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p432829")) . "&m3u8-forge=true");
    else loc(prima("id-p432829"));
}
else if ($channel == "PrimaNews") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p650443")) . "&m3u8-forge=true");
    else loc(prima("id-p650443"));
}
else if ($channel == "PrimaStar") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p846043")) . "&m3u8-forge=true");
    else loc(prima("id-p846043"));
}
else if ($channel == "PrimaShow") {
    if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-p899572")) . "&m3u8-forge=true");
    else loc(prima("id-p899572"));
}
else if ($channel == "PrimaCase") {
    //Channel announced launch on 2025
    //if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-pXXXXXX")) . "&m3u8-forge=true");
    //else loc(prima("id-pXXXXXX"));
    notfound("video_unavailable/unavailable.m3u8");
}
else if ($channel == "PrimaPort") {
    //Channel announced launch on 2025/2026
    //if($forge) loc($SKTV_PROXY_CZ . urlencode(prima("id-pXXXXXX")) . "&m3u8-forge=true");
    //else loc(prima("id-pXXXXXX"));
    notfound("video_unavailable/unavailable.m3u8");
}
else {
    notfound("video_unavailable/unavailable.m3u8");
}
?>
