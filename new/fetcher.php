<?php
// fetcher.php

function curl_fetch($url, $referer = "", $proxy_region = null) {
    if ($proxy_region === null) {
        if (strpos($url, 'iprima.cz') !== false || strpos($url, 'nova.cz') !== false) {
            $proxy_region = 'CZ';
        } elseif (strpos($url, 'markiza.sk') !== false) {
            $proxy_region = 'SK';
        }
    } else if ($proxy_region === true) {
        // Zpětná kompatibilita pro Markízu
        $proxy_region = 'SK';
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    global $cz_proxies, $sk_proxies;
    if (empty($cz_proxies) && file_exists(__DIR__ . '/config.php')) {
        // We must include it in global scope, so we use a closure or just require and assign
        require __DIR__ . '/config.php';
        if (isset($cz_proxies)) {
            $GLOBALS['cz_proxies'] = $cz_proxies;
            $GLOBALS['sk_proxies'] = $sk_proxies;
        }
    }
    // Re-assign local vars from globals if they were just loaded
    $cz_proxies = $GLOBALS['cz_proxies'] ?? [];
    $sk_proxies = $GLOBALS['sk_proxies'] ?? [];

    if ($proxy_region === 'CZ' && !empty($cz_proxies)) {
        $randomProxy = $cz_proxies[array_rand($cz_proxies)];
        
        if (substr_count($randomProxy, ':') >= 2) {
            // Fallback if there was auth
            list($proxyHost, $proxyPort, $proxyUser, $proxyPass) = explode(':', $randomProxy);
            curl_setopt($ch, CURLOPT_PROXY, $proxyHost . ':' . $proxyPort);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUser . ':' . $proxyPass);
        } else {
            // No auth, just host:port
            curl_setopt($ch, CURLOPT_PROXY, $randomProxy);
        }
    } elseif ($proxy_region === 'SK' && !empty($sk_proxies)) {
        $randomProxy = $sk_proxies[array_rand($sk_proxies)];
        list($proxyHost, $proxyPort, $proxyUser, $proxyPass) = explode(':', $randomProxy);
        curl_setopt($ch, CURLOPT_PROXY, $proxyHost . ':' . $proxyPort);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUser . ':' . $proxyPass);
    }
    
    $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ];
    if (!empty($referer)) {
        $headers[] = 'Referer: ' . $referer;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    if ($data !== false && $httpCode >= 200 && $httpCode < 400) {
        return $data;
    }
    return null;
}

function prima_fetcher($id) {
    $resp = curl_fetch("https://api.play-backend.iprima.cz/api/v1/products/" . $id . "/play");
    if (!$resp) return null;
    $json = json_decode($resp);
    $url = $json->streamInfos[0]->url ?? null;
    return $url ? str_replace("lq.m3u8", "hd.m3u8", $url) : null;
}

function nova_fetcher($id) {
    if ($id === "tnlive") {
        $api_url = "https://tn.nova.cz/api/v1/tnlive/livestream?tnlive_channel_id=4";
        $api_resp = curl_fetch($api_url, "https://tn.nova.cz/");
        $data = json_decode($api_resp, true);
        if (isset($data['short']) && !empty($data['short'])) {
            $id = $data['short'] . "_tn";
        } else {
            return null; // Stream is not currently active
        }
    }

    if (strpos($id, "_tn") !== false) {
        $real_id = str_replace("_tn", "", $id);
        $resp = curl_fetch("https://mediatn.cms.nova.cz/embed/" . $real_id . "?autoplay=1", "https://mediatn.cms.nova.cz/");
    } else {
        $resp = curl_fetch("https://media.cms.nova.cz/embed/" . $id . "live?autoplay=1", "https://media.cms.nova.cz/");
    }
    if (!$resp) return null;
    if (preg_match('/\[{"src":"([^"]+)"/', $resp, $matches)) {
        return str_replace("\\", "", $matches[1]);
    }
    return null;
}

function ct_fetcher($id) {
    $resp = curl_fetch("https://api.ceskatelevize.cz/video/v1/playlist-live/v1/stream-data/channel/" . $id . "?canPlayDrm=false&streamType=dash&quality=1080p");
    if (!$resp) return null;
    $json = json_decode($resp, true);
    return $json["streamUrls"]["main"] ?? null;
}

function markiza_fetcher($id) {
    if (strpos($id, "_tn") !== false) {
        $real_id = str_replace("_tn", "", $id);
        $resp = curl_fetch("https://media.cms.markiza.sk/embed/" . $real_id . "?autoplay=any", "https://media.cms.markiza.sk/", true);
    } else {
        $resp = curl_fetch("https://media.cms.markiza.sk/embed/" . $id . "-live?autoplay=any", "https://media.cms.markiza.sk/", true);
    }
    if (!$resp) return null;
    if (preg_match('/\[{"src":"([^"]+)"/', $resp, $matches)) {
        return str_replace("\\", "", $matches[1]);
    }
    return null;
}

function joj_fetcher($id) {
    return "https://live.cdn.joj.sk/live/andromeda/" . $id . ".m3u8";
}

function stv_fetcher($id) {
    $resp = curl_fetch("https://www.stvr.sk/json/live5f.json?c=" . $id . "&b=msie&p=win&v=11&f=0&d=1");
    if (!$resp) return null;
    $json = json_decode($resp, true);
    return $json["clip"]["sources"][0]["src"] ?? null;
}

function ta3_fetcher($id) {
    $content = curl_fetch("https://embed.livebox.cz/ta3_v2/live-source.js");
    if (!$content) return null;
    if (preg_match('/" : "([^"]+)"/', $content, $matches)) {
        return "https:" . $matches[1];
    }
    return null;
}
