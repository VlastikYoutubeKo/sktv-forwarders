<?php

// Proxy paths
$SKTV_PROXY_SK="${SECRET_SKTV_PROXY_SK}";
$SKTV_PROXY_CZ="${SECRET_SKTV_PROXY_CZ}";
$ZELVAR_CZ_LEGACY="https://proxy.zelvar.cz/subdom/proxy/index.php?hl=200&q=";

function proxysktv_sk_simple($url) {
    global $SKTV_PROXY_SK;
    return file_get_contents($SKTV_PROXY_SK . urlencode($url), false);
}

function proxysktv_cz_simple($url) {
    global $SKTV_PROXY_CZ;
    return file_get_contents($SKTV_PROXY_CZ . urlencode($url), false);
}

function proxy_zelvar_simple($url) {
    global $ZELVAR_CZ_LEGACY;
    return file_get_contents($ZELVAR_CZ_LEGACY . urlencode($url), false, stream_context_create(array("ssl"=>array("verify_peer_name"=>false))));
}

function stv_url_proxy($x) {
    $playlisturl = json_decode(file_get_contents("https://www.rtvs.sk/json/live5f.json?c=" . $x . "&b=msie&p=win&v=11&f=0&d=1"), true)["clip"]["sources"][0]["src"];
    $playlist = proxysktv_sk_simple($playlisturl);
    $lines = explode("\n", $playlist);
    return $lines[5]; //1080p is on line 5
}

function markiza_url_proxy($x, $tn = false) {
    $siteurl = "https://media.cms.markiza.sk/embed/" . $x . ($tn ? "" : "-live") . "?autoplay=any";
    $sitecontent = proxysktv_sk_simple($siteurl);
    $streamurl = join("", explode("\\", explode("\"", explode("[{\"src\":\"", $sitecontent)[1])[0]));
    return $streamurl;
}

function nova_url($x, $tn = false) {
    $content = proxy_zelvar_simple("https://media" . ($tn ? "tn" : "") . ".cms.nova.cz/embed/" . $x . ($tn ? "" : "live") . "?autoplay=1");
    return join("", explode("\\", explode("\"", explode("[{\"src\":\"", $content)[1])[0]));
}

function ta3_url() {
    return "https:" . explode("\"", explode("\" : \"", file_get_contents("https://embed.livebox.cz/ta3_v2/live-source.js"))[1])[0];
}

function cnn_portugal() {
    $endpoint = json_decode(file_get_contents("https://front-api.iol.pt/api/v1/live/broadcast?canal=CNN"), true);
    return $endpoint["videoUrl"] . "wmsAuthSign=" . file_get_contents("https://services.iol.pt/matrix?userId=", false, stream_context_create(array("http"=>array("header"=>"User-Agent: Mozilla/5.0 (Linux; Android 8.1.0; SM-A260F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Mobile Safari/537.36\r\n"))));
}

function ceskatelevize($code) {
    $url = "https://api.ceskatelevize.cz/video/v1/playlist-live/v1/stream-data/channel/" . $code . "?canPlayDrm=false&streamType=dash&quality=1080p";
    $response = proxysktv_cz_simple($url);
    $manifest = json_decode($response, true);
    $finalurl = $manifest["streamUrls"]["main"];
    return $finalurl;
}

function prima($id) {
    global $SKTV_PROXY_CZ;
    $primajson = json_decode(proxysktv_cz_simple("https://api.play-backend.iprima.cz/api/v1/products/" . $id . "/play"));
    $primaurl = $primajson->streamInfos[0]->url;
    $primaurlhq = str_replace("lq.m3u8", "hd.m3u8", $primaurl);
    return $primaurlhq;
}

// Pouze vrať URL, ne redirect
$channel = $_GET["x"] ?? '';
$forge = isset($_GET['forge']) && $_GET['forge'] === 'true';

$streamUrl = null;
$referrer = null;

if ($channel == "TA3") {
    $streamUrl = ta3_url();
}
else if ($channel == "STV1") {
    $streamUrl = stv_url_proxy("1");
}
else if ($channel == "STV2") {
    $streamUrl = stv_url_proxy("2");
}
else if ($channel == "STV24") {
    $streamUrl = stv_url_proxy("3");
}
else if ($channel == "STV-O") {
    $streamUrl = stv_url_proxy("4");
}
else if ($channel == "RTVS") {
    $streamUrl = stv_url_proxy("6");
}
else if ($channel == "NR_SR") {
    $streamUrl = stv_url_proxy("5");
}
else if ($channel == "SPORT") {
    $streamUrl = stv_url_proxy("15");
}
else if ($channel == "Markiza") {
    $streamUrl = markiza_url_proxy("markiza");
    $referrer = "https://media.cms.markiza.sk/";
}
else if ($channel == "Doma") {
    $streamUrl = markiza_url_proxy("doma");
    $referrer = "https://media.cms.markiza.sk/";
}
else if ($channel == "Dajto") {
    $streamUrl = markiza_url_proxy("dajto");
    $referrer = "https://media.cms.markiza.sk/";
}
else if ($channel == "Krimi") {
    $streamUrl = markiza_url_proxy("krimi");
    $referrer = "https://media.cms.markiza.sk/";
}
else if ($channel == "Klasik") {
    $streamUrl = markiza_url_proxy("klasik");
    $referrer = "https://media.cms.markiza.sk/";
}
else if ($channel == "MarkizaTNLive") {
    $streamUrl = markiza_url_proxy("BQeGg0uPHJP", true);
    $referrer = "https://media.cms.markiza.sk/";
}
else if ($channel == "JOJ") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/joj-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "JOJP") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/plus-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "Wau") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/wau-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "JOJ24") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/joj_news-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "JOJFamily") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/family-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "JOJCinema") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/cinema-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "JOJSport") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/joj_sport-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "Jojko") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/jojko-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "CSFilm") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/cs_film-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "CSHistory") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/cs_history-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "CSMystery") {
    $streamUrl = "https://live.cdn.joj.sk/live/andromeda/cs_mystery-1080.m3u8";
    $referrer = "https://media.joj.sk/";
}
else if ($channel == "Nova") {
    $streamUrl = nova_url("nova-");
    $referrer = "https://media.cms.nova.cz/";
}
else if ($channel == "NovaFun") {
    $streamUrl = nova_url("nova-2-");
    $referrer = "https://media.cms.nova.cz/";
}
else if ($channel == "NovaLady") {
    $streamUrl = nova_url("nova-lady-");
    $referrer = "https://media.cms.nova.cz/";
}
else if ($channel == "NovaGold") {
    $streamUrl = nova_url("nova-gold-");
    $referrer = "https://media.cms.nova.cz/";
}
else if ($channel == "NovaCinema") {
    $streamUrl = nova_url("nova-cinema-");
    $referrer = "https://media.cms.nova.cz/";
}
else if ($channel == "NovaAction") {
    $streamUrl = nova_url("nova-action-");
    $referrer = "https://media.cms.nova.cz/";
}
else if ($channel == "NovaTNLive") {
    $streamUrl = nova_url("ETpdC5paJa8", true);
}
else if ($channel == "CNN_Portugal") {
    $streamUrl = cnn_portugal();
}
else if ($channel == "CT1") {
    $streamUrl = ceskatelevize("CH_1");
}
else if ($channel == "CT2") {
    $streamUrl = ceskatelevize("CH_2");
}
else if ($channel == "CT24") {
    $streamUrl = ceskatelevize("CH_24");
}
else if ($channel == "CTsport") {
    $streamUrl = ceskatelevize("CH_4");
}
else if ($channel == "CT_D") {
    $streamUrl = ceskatelevize("CH_5");
}
else if ($channel == "CTart") {
    $streamUrl = ceskatelevize("CH_6");
}
else if ($channel == "CTsportPlus") {
    $streamUrl = ceskatelevize("CH_25");
}
else if ($channel == "Prima") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p111013")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p111013");
}
else if ($channel == "PrimaCool") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p111014")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p111014");
}
else if ($channel == "PrimaZoom") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p111015")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p111015");
}
else if ($channel == "PrimaLove") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p111016")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p111016");
}
else if ($channel == "PrimaMax") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p111017")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p111017");
}
else if ($channel == "PrimaKrimi") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p432829")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p432829");
}
else if ($channel == "PrimaNews") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p650443")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p650443");
}
else if ($channel == "PrimaStar") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p846043")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p846043");
}
else if ($channel == "PrimaShow") {
    if($forge) $streamUrl = $SKTV_PROXY_CZ . urlencode(prima("id-p899572")) . "&m3u8-forge=true";
    else $streamUrl = prima("id-p899572");
}
