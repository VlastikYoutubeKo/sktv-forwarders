<?php
// index.php
// Dual mode: Serves HTML for browsers, M3U playlist for media players

$channels = require 'channels.php';
$proxy = isset($_GET['proxy']) ? (int)$_GET['proxy'] : 0;

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (basename($path) === 'index.php') {
    $dir = dirname($path);
} else {
    $dir = rtrim($path, '/');
}
$baseUrl = $protocol . $host . $dir;

$isBrowser = false;
$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
if ((strpos($ua, 'mozilla') !== false || strpos($ua, 'chrome') !== false || strpos($ua, 'safari') !== false) 
    && strpos($ua, 'vlc') === false 
    && strpos($ua, 'mpv') === false 
    && strpos($ua, 'kodi') === false) {
    $isBrowser = true;
}

if (isset($_GET['m3u']) && $_GET['m3u'] === '1') {
    $isBrowser = false;
}

if (!$isBrowser) {
    header('Content-Type: audio/x-mpegurl');
    header('Content-Disposition: attachment; filename="sktv_v2.m3u"');
    header('Cache-Control: no-cache');
    echo "#EXTM3U\n";
    foreach ($channels as $name => $data) {
        echo "#EXTINF:-1 group-title=\"{$data['group']}\", {$name}\n";
        echo "{$baseUrl}/stream.php?ch={$name}&proxy={$proxy}\n";
    }
    exit;
}

$groupedChannels = [];
foreach ($channels as $name => $data) {
    $group = $data['group'];
    if (!isset($groupedChannels[$group])) {
        $groupedChannels[$group] = [
            'name' => $group,
            'countrycode' => strtolower($group),
            'channels' => []
        ];
    }
    $groupedChannels[$group]['channels'][] = [
        'name' => $name,
        'id' => $name
    ];
}
$uiChannels = array_values($groupedChannels);

?>
<!DOCTYPE html>
<html lang="cs" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKTV V2 – Premium Forwarders | Sledujte CZ/SK TV online</title>
    <meta name="description" content="SKTV V2 Forwarders nabízí revoluční přístup k přehrávání CZ a SK televizních kanálů bez hranic, s nulovou zátěží pásma a naprostým soukromím.">
    <meta name="keywords" content="SKTV, televize, CZ TV, SK TV, online televize, m3u8 playlist, iptv proxy">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://sktv.mxnticek.eu/">
    <meta property="og:title" content="SKTV V2 – Premium TV Forwarders">
    <meta property="og:description" content="Sledujte české a slovenské televizní kanály online přes naší revoluční zero-bandwidth proxy.">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="SKTV V2 – Premium TV Forwarders">
    <meta property="twitter:description" content="Sledujte české a slovenské televizní kanály online přes naší revoluční zero-bandwidth proxy.">

    <!-- Schema.org Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebApplication",
      "name": "SKTV V2 Forwarders",
      "description": "Online platforma pro přesměrování a bezpečné přehrávání českých a slovenských televizních kanálů z oficiálních API bez zatěžování serveru.",
      "url": "https://sktv.mxnticek.eu/",
      "applicationCategory": "EntertainmentApplication",
      "operatingSystem": "All"
    }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff', 100: '#ede9fe', 400: '#a78bfa', 500: '#8b5cf6',
                            600: '#7c3aed', 900: '#4c1d95', 950: '#2e1065',
                        },
                        dark: { 800: '#1a1b23', 900: '#13141a', 950: '#0d0e12', }
                    },
                    animation: { 'gradient-x': 'gradient-x 15s ease infinite', 'float': 'float 6s ease-in-out infinite', },
                    keyframes: {
                        'gradient-x': {
                            '0%, 100%': { 'background-size': '200% 200%', 'background-position': 'left center' },
                            '50%': { 'background-size': '200% 200%', 'background-position': 'right center' },
                        },
                        'float': {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; -webkit-font-smoothing: antialiased; }
        .glass-nav { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
        .dark .glass-nav { background: rgba(13, 14, 18, 0.7); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .dark .glass-card { background: rgba(26, 27, 35, 0.8); border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); }
        .glass-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.1); border-color: rgba(139, 92, 246, 0.3); }
        .dark .glass-card:hover { box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.2); }
        .bg-mesh { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; background: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%); opacity: 0; transition: opacity 0.5s; }
        .dark .bg-mesh { opacity: 0.15; }
        .light-mesh { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; background: radial-gradient(at 0% 0%, hsla(253,100%,95%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,100%,95%,1) 0, transparent 50%); opacity: 1; transition: opacity 0.5s; }
        .dark .light-mesh { opacity: 0; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-dark-950 dark:text-gray-100 transition-colors duration-500 overflow-x-hidden relative min-h-screen flex flex-col">

    <div class="light-mesh pointer-events-none"></div>
    <div class="bg-mesh pointer-events-none"></div>

    <nav class="glass-nav fixed w-full z-50 top-0 transition-all duration-300 h-20">
        <div class="container mx-auto px-6 h-full flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-pink-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 transform transition-transform hover:rotate-12 hover:scale-110">
                    <i class="fas fa-play text-xl ml-1"></i>
                </div>
                <div>
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 tracking-tight block">
                        SKTV<span class="font-light">v2</span>
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">Proxy Active</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="?m3u=1" class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-sm bg-white dark:bg-dark-800 border border-gray-200 dark:border-gray-700 hover:border-brand-500 hover:text-brand-500 transition-all shadow-sm">
                    <i class="fas fa-list-ul"></i>
                    <span>M3U8 Playlist</span>
                </a>
                <button id="themeToggle" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white dark:bg-dark-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-brand-500 dark:hover:text-brand-400 transition-all shadow-sm">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:block"></i>
                </button>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 pt-32 pb-20 flex-grow relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-20 animate-float">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 text-xs font-bold uppercase tracking-wider mb-6 border border-brand-200 dark:border-brand-800/50">
                <i class="fas fa-bolt"></i> Nová generace proxy
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 text-gray-900 dark:text-white leading-tight">
                Televize <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-pink-500 animate-gradient-x">bez hranic.</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto leading-relaxed">
                Revoluční API bypass s rezidenčními IP. Extrémní spolehlivost, <strong>nulová zátěž pásma</strong>, dokonalý zážitek. Vítejte ve verzi 2.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#channels" class="px-8 py-4 rounded-xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold hover:scale-105 transition-transform shadow-xl shadow-gray-900/20 dark:shadow-white/10">
                    Procházet kanály
                </a>
                <a href="privacy.php" class="px-8 py-4 rounded-xl bg-white dark:bg-dark-800 border border-gray-200 dark:border-gray-700 font-bold hover:border-brand-500 transition-all shadow-sm">
                    Jak to celé funguje?
                </a>
            </div>
        </div>

        <div id="channels" class="scroll-mt-32">
            <?php foreach($uiChannels as $i) { ?>
            <div class="mb-16">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 rounded-full bg-white dark:bg-dark-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden shadow-sm">
                        <img src="https://flagcdn.com/24x18/<?php echo htmlspecialchars($i['countrycode']); ?>.png" alt="<?php echo htmlspecialchars($i['countrycode']); ?>" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($i["name"]); ?></h3>
                    <div class="flex-grow h-px bg-gradient-to-r from-gray-200 to-transparent dark:from-gray-800 ml-4"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach($i["channels"] as $j) { ?>
                    <div class="glass-card rounded-2xl overflow-hidden group relative flex flex-col">
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 to-pink-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="p-6 relative z-10 flex-grow flex flex-col">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-1 group-hover:text-brand-500 transition-colors">
                                        <?php echo htmlspecialchars($j["name"]); ?>
                                    </h4>
                                    <span class="font-mono text-[10px] text-gray-400 bg-gray-100 dark:bg-dark-800 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-700">
                                        ID: <?php echo htmlspecialchars($j["id"]); ?>
                                    </span>
                                </div>
                                <div data-channel-id="<?php echo htmlspecialchars($j["id"]); ?>" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 dark:bg-dark-800 border border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-500 transition-colors duration-500">
                                    <i class="fas fa-circle-notch fa-spin text-[10px]"></i> --
                                </div>
                            </div>
                            <div class="mt-auto pt-6 flex gap-3">
                                <a href="stream.php?ch=<?php echo $j["id"]; ?>&proxy=0" class="flex-1 bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold py-3 rounded-xl text-center shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-play text-xs"></i> Přehrát
                                </a>
                                <button onclick="copyToClipboard('<?php echo $baseUrl; ?>/stream.php?ch=<?php echo $j['id']; ?>&proxy=0')" class="w-12 flex-shrink-0 bg-gray-100 dark:bg-dark-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-xl flex items-center justify-center hover:bg-gray-200 dark:hover:bg-dark-900 hover:text-brand-500 transition-all" title="Kopírovat odkaz">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </main>

    <footer class="mt-auto border-t border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-dark-900/50 backdrop-blur-lg">
        <div class="container mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 opacity-80 hover:opacity-100 transition-opacity">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-pink-500 flex items-center justify-center text-white text-xs">
                    <i class="fas fa-bolt"></i>
                </div>
                <p class="text-sm font-medium">
                    Vyrobeno s <i class="fas fa-heart text-red-500 mx-1 text-xs"></i> mxnticek / cyn
                </p>
            </div>
            <div class="flex items-center gap-6">
                <a href="privacy.php" class="text-sm font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-500 to-pink-500 hover:scale-105 transition-transform">Tech Stack & O projektu</a>
                <a href="https://github.com/vlastikyoutubeko/sktv-forwarders" class="text-sm font-semibold text-gray-500 hover:text-brand-500 transition-colors"><i class="fab fa-github mr-1"></i> GitHub</a>
            </div>
        </div>
    </footer>

    <div id="toast" class="fixed bottom-6 right-6 translate-y-[150%] opacity-0 transition-all duration-300 z-50">
        <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 font-semibold text-sm border border-gray-700 dark:border-gray-200">
            <div class="w-6 h-6 rounded-full bg-green-500/20 text-green-500 flex items-center justify-center">
                <i class="fas fa-check text-[10px]"></i>
            </div>
            Zkopírováno!
        </div>
    </div>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) { html.classList.add('dark'); }
        themeToggle.addEventListener('click', () => { html.classList.toggle('dark'); localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light'; });
        window.copyToClipboard = function(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.getElementById('toast');
                toast.classList.remove('translate-y-[150%]', 'opacity-0');
                setTimeout(() => toast.classList.add('translate-y-[150%]', 'opacity-0'), 2500);
            });
        }
        function updateStats() {
            fetch('stats.php?action=get')
                .then(r => r.json())
                .then(stats => {
                    document.querySelectorAll('[data-channel-id]').forEach(el => {
                        const count = stats[el.dataset.channelId] || 0;
                        el.innerHTML = `<i class="fas fa-eye text-[10px]"></i> ${count}`;
                        el.className = 'flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-colors duration-500 border ';
                        if (count === 0) el.className += 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-dark-800 dark:border-gray-700';
                        else if (count < 5) el.className += 'bg-green-50 text-green-600 border-green-200 dark:bg-green-900/20 dark:border-green-800/50 dark:text-green-400';
                        else el.className += 'bg-brand-50 text-brand-600 border-brand-200 dark:bg-brand-900/20 dark:border-brand-800/50 dark:text-brand-400 animate-pulse';
                    });
                }).catch(e => console.error(e));
        }
        updateStats(); setInterval(updateStats, 5000);
    </script>
</body>
</html>
