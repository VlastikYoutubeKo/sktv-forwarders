<?php
function add_channel(&$arr, $name, $id, $streamURL) {
    array_push($arr, array("name"=>$name, "id"=>$id, "streamURL"=>$streamURL));
}

function add_country(&$arr, $name, $countrycode, $channels, $note = "") {
    array_push($arr, array("name"=>$name, "countrycode"=>$countrycode, "channels"=>$channels, "note"=>$note));
}

$channels = array();

// Slovakia
$chan_sk = array();
add_channel($chan_sk, "Jednotka", "STV1", "https://www.rtvs.sk/televizia/live-1");
add_channel($chan_sk, "Dvojka", "STV2", "https://www.rtvs.sk/televizia/live-2");
add_channel($chan_sk, "RTVS 24", "STV24", "https://www.rtvs.sk/televizia/live-24");
add_channel($chan_sk, "RTVS Šport", "SPORT", "https://www.rtvs.sk/televizia/sport");
add_channel($chan_sk, ":O", "STV-O", "https://www.rtvs.sk/televizia/live-o");
add_channel($chan_sk, "RTVS", "RTVS", "https://www.rtvs.sk/televizia/live-rtvs");
add_channel($chan_sk, "NR SR", "NR_SR", "https://www.rtvs.sk/televizia/live-nr-sr");
add_channel($chan_sk, "TA3", "TA3", "https://www.ta3.com/live");
add_channel($chan_sk, "Markiza", "Markiza", "https://media.cms.markiza.sk/embed/markiza-live?autoplay=any");
add_channel($chan_sk, "(Markiza) Dajto", "Dajto", "https://media.cms.markiza.sk/embed/dajto-live?autoplay=any");
add_channel($chan_sk, "(Markiza) Doma", "Doma", "https://media.cms.markiza.sk/embed/doma-live?autoplay=any");
add_channel($chan_sk, "Markiza Krimi", "Krimi", "https://media.cms.markiza.sk/embed/krimi-live?autoplay=any");
add_channel($chan_sk, "Markiza Klasik", "Klasik", "https://media.cms.markiza.sk/embed/klasik-live?autoplay=any");
add_channel($chan_sk, "TN Live.sk", "MarkizaTNLive", "https://media.cms.markiza.sk/embed/BQeGg0uPHJP?autoplay=any");
add_channel($chan_sk, "JOJ", "JOJ", "https://live.joj.sk/");
add_channel($chan_sk, "JOJ Plus", "JOJP", "https://plus.joj.sk/live");
add_channel($chan_sk, "(JOJ) Wau", "Wau", "https://wau.joj.sk/live");
add_channel($chan_sk, "JOJ 24", "JOJ24", "https://joj24.noviny.sk/");
add_channel($chan_sk, "JOJ Šport", "JOJSport", "https://jojsport.joj.sk");
add_channel($chan_sk, "Jojko", "Jojko", "https://jojko.joj.sk");

$slovakiaNote = '
    <div class="space-y-3">
        <p class="font-semibold text-gray-800">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            All Markiza channels need the Referer to be <code class="bg-purple-100 px-2 py-1 rounded text-purple-800">https://media.cms.markiza.sk/</code> and all Joj channels need <code class="bg-purple-100 px-2 py-1 rounded text-purple-800">https://media.joj.sk/</code>!
        </p>
        <details class="bg-white p-4 rounded-lg border border-gray-200">
            <summary class="cursor-pointer font-medium text-gray-700 hover:text-purple-600">Show VLC/MPV commands</summary>
            <div class="mt-3 space-y-2 font-mono text-xs">
                <p class="text-gray-600">The #EXTVLCOPT is already present in the m3u8, however, it does not work properly in some versions of VLC. Use explicit command for your favourite player:</p>
                <div class="bg-gray-50 p-3 rounded border-l-4 border-blue-500">
                    <p class="text-blue-600 font-semibold mb-2">Markiza:</p>
                    <p>vlc --adaptive-use-access --http-referrer=https://media.cms.markiza.sk/ [URL]</p>
                    <p>mpv --http-header-fields="Referer: https://media.cms.markiza.sk/" [URL]</p>
                </div>
                <div class="bg-gray-50 p-3 rounded border-l-4 border-green-500">
                    <p class="text-green-600 font-semibold mb-2">JOJ:</p>
                    <p>vlc --adaptive-use-access --http-referrer=https://media.joj.sk/ [URL]</p>
                    <p>mpv --http-header-fields="Referer: https://media.joj.sk/" [URL]</p>
                </div>
            </div>
        </details>
    </div>
';

add_country($channels, "Slovakia", "sk", $chan_sk, $slovakiaNote);

// Czech Republic
$chan_cz = array();
add_channel($chan_cz, "ČT1", "CT1", "https://www.ceskatelevize.cz/zive/ct1/");
add_channel($chan_cz, "ČT2", "CT2", "https://www.ceskatelevize.cz/zive/ct2/");
add_channel($chan_cz, "ČT24", "CT24", "https://ct24.ceskatelevize.cz/#live");
add_channel($chan_cz, "ČT sport", "CTsport", "https://sport.ceskatelevize.cz/#live");
add_channel($chan_cz, "ČT :D", "CT_D", "https://decko.ceskatelevize.cz/zive");
add_channel($chan_cz, "ČT art", "CTart", "https://www.ceskatelevize.cz/zive/art/");
add_channel($chan_cz, "ČT sport Plus", "CTsportPlus", "https://sport.ceskatelevize.cz/clanek/ostatni/program-vysilani-ct-sport-na-webu-v-mobilu-a-hbbtv/5ddda79bfccd259ea46d41bc");
add_channel($chan_cz, "Nova", "Nova", "https://tv.nova.cz/sledujte-zive/1-nova");
add_channel($chan_cz, "Nova Cinema", "NovaCinema", "https://tv.nova.cz/sledujte-zive/2-nova-cinema");
add_channel($chan_cz, "Nova Action", "NovaAction", "https://tv.nova.cz/sledujte-zive/3-nova-action");
add_channel($chan_cz, "Nova Fun", "NovaFun", "https://tv.nova.cz/sledujte-zive/4-nova-fun");
add_channel($chan_cz, "Nova Gold", "NovaGold", "https://tv.nova.cz/sledujte-zive/5-nova-gold");
add_channel($chan_cz, "Nova Lady", "NovaLady", "https://tv.nova.cz/sledujte-zive/29-nova-lady");
add_channel($chan_cz, "TN Live.cz", "NovaTNLive", "https://tn.nova.cz/tnlive");
add_channel($chan_cz, "Prima", "Prima", "https://iprima.cz");
add_channel($chan_cz, "Prima Cool", "PrimaCool", "https://iprima.cz");
add_channel($chan_cz, "Prima Zoom", "PrimaZoom", "https://iprima.cz");
add_channel($chan_cz, "Prima Love", "PrimaLove", "https://iprima.cz");
add_channel($chan_cz, "Prima Max", "PrimaMax", "https://iprima.cz");
add_channel($chan_cz, "Prima Krimi", "PrimaKrimi", "https://iprima.cz");
add_channel($chan_cz, "Prima Star", "PrimaStar", "https://iprima.cz");
add_channel($chan_cz, "Prima Show", "PrimaShow", "https://iprima.cz");
add_channel($chan_cz, "JOJ Family", "JOJFamily", "https://jojfamily.joj.cz");
add_channel($chan_cz, "JOJ Cinema", "JOJCinema", "https://jojcinema.cz/");
add_channel($chan_cz, "CS Film", "CSFilm", "https://csfilm.joj.cz");
add_channel($chan_cz, "CS History", "CSHistory", "https://cshistory.joj.cz");
add_channel($chan_cz, "CS Mystery", "CSMystery", "https://csmystery.joj.cz");
//Channel announced launch on 2025
//add_channel($chan_cz, "Prima Case", "PrimaCase", "https://iprima.cz");
//Channel announced launch on 2025/2026
//add_channel($chan_cz, "Prima Port", "PrimaPort", "https://iprima.cz");
add_channel($chan_cz, "CNN Prima News", "PrimaNews", "https://cnn.iprima.cz/vysilani");

$czechNote = '
    <div class="space-y-3">
        <p class="font-semibold text-gray-800">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            All Nova channels (Excluding TN Live) need the Referer to be <code class="bg-purple-100 px-2 py-1 rounded text-purple-800">https://media.cms.nova.cz/</code>! All Prima channels need czech IP!
        </p>
        <details class="bg-white p-4 rounded-lg border border-gray-200">
            <summary class="cursor-pointer font-medium text-gray-700 hover:text-purple-600">Show VLC/MPV commands</summary>
            <div class="mt-3 space-y-2 font-mono text-xs">
                <p class="text-gray-600">The #EXTVLCOPT is already present in the m3u8, however, it does not work properly in some versions of VLC. Use explicit command for your favourite player:</p>
                <div class="bg-gray-50 p-3 rounded border-l-4 border-blue-500">
                    <p class="text-blue-600 font-semibold mb-2">Nova:</p>
                    <p>vlc --adaptive-use-access --http-referrer=https://media.cms.nova.cz/ [URL]</p>
                    <p>mpv --http-header-fields="Referer: https://media.cms.nova.cz/" [URL]</p>
                </div>
                <div class="bg-gray-50 p-3 rounded border-l-4 border-orange-500">
                    <p class="text-orange-600 font-semibold mb-2">Prima:</p>
                    <p>Prima channels need czech IP or &forge=true added to the url. This doesn\'t work on some players.</p>
                </div>
            </div>
        </details>
    </div>
';

add_country($channels, "Czech Republic", "cz", $chan_cz, $czechNote);

// Portugal
$chan_pt = array();
add_channel($chan_pt, "CNN Portugal", "CNN_Portugal", "https://cnnportugal.iol.pt/direto");

add_country($channels, "Portugal", "pt", $chan_pt);
