<?php
function add_channel(&$arr, $name, $id, $streamURL) {
    array_push($arr, array("name"=>$name, "id"=>$id, "streamURL"=>$streamURL));
}

function add_country(&$arr, $name, $countrycode, $channels, $note = "") {
    array_push($arr, array("name"=>$name, "countrycode"=>$countrycode, "channels"=>$channels, "note"=>$note));
}

$channels = array();

// --- Slovakia ---
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

// Updated HTML Design for Slovakia Note
$slovakiaNote = '
    <div class="space-y-3">
        <p class="font-medium text-gray-800 dark:text-gray-200 text-sm leading-relaxed">
            <i class="fas fa-exclamation-triangle mr-1.5 text-orange-500"></i>
            Markiza channels require Referer: <code class="font-mono text-xs bg-white/60 dark:bg-black/30 px-1.5 py-0.5 rounded text-purple-700 dark:text-purple-300 border border-purple-100 dark:border-purple-800/50">https://media.cms.markiza.sk/</code> 
            <br class="hidden sm:block mb-1">
            Joj channels require Referer: <code class="font-mono text-xs bg-white/60 dark:bg-black/30 px-1.5 py-0.5 rounded text-purple-700 dark:text-purple-300 border border-purple-100 dark:border-purple-800/50">https://media.joj.sk/</code>
        </p>
        
        <details class="group bg-white/60 dark:bg-gray-900/40 backdrop-blur-sm rounded-lg border border-blue-200 dark:border-blue-800/50 overflow-hidden transition-all duration-300">
            <summary class="cursor-pointer p-3 font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between select-none">
                <span><i class="fas fa-terminal mr-2 text-gray-400"></i>Show VLC/MPV commands</span>
                <i class="fas fa-chevron-down text-xs text-gray-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            
            <div class="p-4 space-y-4 border-t border-blue-100 dark:border-blue-800/30 bg-gray-50/50 dark:bg-gray-950/30 text-xs">
                
                <!-- Markiza Block -->
                <div class="bg-white dark:bg-gray-900 rounded border-l-4 border-blue-500 shadow-sm overflow-hidden">
                    <div class="bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 border-b border-blue-100 dark:border-blue-800/50">
                        <span class="font-bold text-blue-700 dark:text-blue-400">Markiza Config</span>
                    </div>
                    <div class="p-3 font-mono text-gray-600 dark:text-gray-400 break-all space-y-2">
                        <div>
                            <span class="select-none text-gray-400 block text-[10px] uppercase">VLC</span>
                            <span class="text-gray-800 dark:text-gray-200">vlc --adaptive-use-access --http-referrer=https://media.cms.markiza.sk/ [URL]</span>
                        </div>
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                            <span class="select-none text-gray-400 block text-[10px] uppercase">MPV</span>
                            <span class="text-gray-800 dark:text-gray-200">mpv --http-header-fields="Referer: https://media.cms.markiza.sk/" [URL]</span>
                        </div>
                    </div>
                </div>

                <!-- JOJ Block -->
                <div class="bg-white dark:bg-gray-900 rounded border-l-4 border-green-500 shadow-sm overflow-hidden">
                    <div class="bg-green-50 dark:bg-green-900/20 px-3 py-1.5 border-b border-green-100 dark:border-green-800/50">
                        <span class="font-bold text-green-700 dark:text-green-400">JOJ Config</span>
                    </div>
                    <div class="p-3 font-mono text-gray-600 dark:text-gray-400 break-all space-y-2">
                        <div>
                            <span class="select-none text-gray-400 block text-[10px] uppercase">VLC</span>
                            <span class="text-gray-800 dark:text-gray-200">vlc --adaptive-use-access --http-referrer=https://media.joj.sk/ [URL]</span>
                        </div>
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                            <span class="select-none text-gray-400 block text-[10px] uppercase">MPV</span>
                            <span class="text-gray-800 dark:text-gray-200">mpv --http-header-fields="Referer: https://media.joj.sk/" [URL]</span>
                        </div>
                    </div>
                </div>

            </div>
        </details>
    </div>
';

add_country($channels, "Slovakia", "sk", $chan_sk, $slovakiaNote);


// --- Czech Republic ---
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

// Updated HTML Design for Czech Note
$czechNote = '
    <div class="space-y-3">
        <p class="font-medium text-gray-800 dark:text-gray-200 text-sm leading-relaxed">
            <i class="fas fa-shield-alt mr-1.5 text-orange-500"></i>
            Nova channels need Referer: <code class="font-mono text-xs bg-white/60 dark:bg-black/30 px-1.5 py-0.5 rounded text-purple-700 dark:text-purple-300 border border-purple-100 dark:border-purple-800/50">https://media.cms.nova.cz/</code>
            <br class="hidden sm:block mb-1">
            <span class="text-orange-600 dark:text-orange-400 font-bold">Prima channels require a Czech IP!</span>
        </p>
        
        <details class="group bg-white/60 dark:bg-gray-900/40 backdrop-blur-sm rounded-lg border border-blue-200 dark:border-blue-800/50 overflow-hidden transition-all duration-300">
            <summary class="cursor-pointer p-3 font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between select-none">
                <span><i class="fas fa-terminal mr-2 text-gray-400"></i>Show VLC/MPV commands</span>
                <i class="fas fa-chevron-down text-xs text-gray-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            
            <div class="p-4 space-y-4 border-t border-blue-100 dark:border-blue-800/30 bg-gray-50/50 dark:bg-gray-950/30 text-xs">
                
                <!-- Nova Block -->
                <div class="bg-white dark:bg-gray-900 rounded border-l-4 border-blue-500 shadow-sm overflow-hidden">
                    <div class="bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 border-b border-blue-100 dark:border-blue-800/50">
                        <span class="font-bold text-blue-700 dark:text-blue-400">Nova Config</span>
                    </div>
                    <div class="p-3 font-mono text-gray-600 dark:text-gray-400 break-all space-y-2">
                        <div>
                            <span class="select-none text-gray-400 block text-[10px] uppercase">VLC</span>
                            <span class="text-gray-800 dark:text-gray-200">vlc --adaptive-use-access --http-referrer=https://media.cms.nova.cz/ [URL]</span>
                        </div>
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                            <span class="select-none text-gray-400 block text-[10px] uppercase">MPV</span>
                            <span class="text-gray-800 dark:text-gray-200">mpv --http-header-fields="Referer: https://media.cms.nova.cz/" [URL]</span>
                        </div>
                    </div>
                </div>

                <!-- Prima Block -->
                <div class="bg-white dark:bg-gray-900 rounded border-l-4 border-orange-500 shadow-sm overflow-hidden">
                    <div class="bg-orange-50 dark:bg-orange-900/20 px-3 py-1.5 border-b border-orange-100 dark:border-orange-800/50">
                        <span class="font-bold text-orange-700 dark:text-orange-400">Prima Geo-Block</span>
                    </div>
                    <div class="p-3 text-gray-600 dark:text-gray-300 leading-relaxed">
                        Prima channels strictly enforce Geo-Location. You must be in the Czech Republic or use a VPN. Adding <code class="font-mono text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">&forge=true</code> to the URL might help, but support is limited.
                    </div>
                </div>

            </div>
        </details>
    </div>
';

add_country($channels, "Czech Republic", "cz", $chan_cz, $czechNote);

// --- Portugal ---
$chan_pt = array();
add_channel($chan_pt, "CNN Portugal", "CNN_Portugal", "https://cnnportugal.iol.pt/direto");

add_country($channels, "Portugal", "pt", $chan_pt);
?>
