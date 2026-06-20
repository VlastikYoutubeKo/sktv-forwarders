<!DOCTYPE html>
<html lang="cs" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O projektu & Tech Stack – SKTV V2 | Privacy Policy</title>
    <meta name="description" content="Přečtěte si, jak funguje SKTV V2 pod kapotou, jak chráníme vaše soukromí a jak můžete podpořit provoz našich rezidenčních proxy sítí.">
    
    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://sktv.mxnticek.eu/privacy.php">
    <meta property="og:title" content="O projektu & Tech Stack – SKTV V2">
    <meta property="og:description" content="Přečtěte si, jak funguje SKTV V2 a jak chráníme vaše soukromí.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'], },
                    colors: {
                        brand: { 50: '#f5f3ff', 100: '#ede9fe', 400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 900: '#4c1d95', 950: '#2e1065', },
                        dark: { 800: '#1a1b23', 900: '#13141a', 950: '#0d0e12', }
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
        .glass-panel { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); }
        .dark .glass-panel { background: rgba(26, 27, 35, 0.8); border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.3); }
        .bg-mesh { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; background: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%); opacity: 0; transition: opacity 0.5s; }
        .dark .bg-mesh { opacity: 0.15; }
        .light-mesh { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; background: radial-gradient(at 0% 0%, hsla(253,100%,95%,1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(339,100%,95%,1) 0, transparent 50%); opacity: 1; transition: opacity 0.5s; }
        .dark .light-mesh { opacity: 0; }
        
        .prose h2 { font-size: 1.875rem; font-weight: 800; margin-top: 3rem; margin-bottom: 1.5rem; letter-spacing: -0.025em; }
        .prose h3 { font-size: 1.25rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; }
        .prose p { margin-bottom: 1.25rem; line-height: 1.75; }
        .prose ul { margin-bottom: 1.5rem; }
        .prose li { margin-bottom: 0.5rem; display: flex; align-items: flex-start; }
        .prose li i { margin-top: 0.35rem; margin-right: 0.75rem; color: #8b5cf6; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-dark-950 dark:text-gray-300 transition-colors duration-500 overflow-x-hidden relative min-h-screen flex flex-col">

    <div class="light-mesh pointer-events-none"></div>
    <div class="bg-mesh pointer-events-none"></div>

    <nav class="glass-nav fixed w-full z-50 top-0 transition-all duration-300 h-20">
        <div class="container mx-auto px-6 h-full flex items-center justify-between">
            <a href="index.php" class="flex items-center space-x-4 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-pink-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 transform transition-transform group-hover:scale-110">
                    <i class="fas fa-arrow-left text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 tracking-tight">
                        Zpět na kanály
                    </h1>
                </div>
            </a>
            <button id="themeToggle" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white dark:bg-dark-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-brand-500 dark:hover:text-brand-400 transition-all shadow-sm">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
            </button>
        </div>
    </nav>

    <main class="container mx-auto px-6 pt-32 pb-20 flex-grow relative z-10 max-w-4xl">
        
        <div class="glass-panel rounded-3xl p-8 md:p-12">
            
            <div class="mb-12 border-b border-gray-200 dark:border-gray-800 pb-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-500 mb-6 border border-brand-200 dark:border-brand-800">
                    <i class="fas fa-microchip text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4 tracking-tight">Tech Stack & O projektu</h1>
                <p class="text-lg text-gray-500 dark:text-gray-400">Pohled pod kapotu verze 2, ochrana soukromí a možnost podpory.</p>
            </div>

            <div class="prose text-gray-700 dark:text-gray-300">
                
                <h2 class="text-gray-900 dark:text-white"><i class="fas fa-code-branch text-brand-500 mr-3"></i> O SKTV Forwarders V2</h2>
                <p>
                    Tento projekt vznikl jako evoluce starého <strong>SKTV Forwarders</strong> (původně anonymní autor, dříve udržováno uživatelem <em>santomet</em>). 
                    Současnou <strong>Verzi 2</strong> od základů přepsal a navrhl <strong>mxnticek / cyn</strong> za vydatné asistence moderní AI 
                    (<em>Claude Sonnet 4.5 & Gemini 3.1 Pro / Antigravity</em>).
                </p>

                <h2 class="text-gray-900 dark:text-white"><i class="fas fa-cogs text-brand-500 mr-3"></i> Jak to technicky funguje? (The Magic)</h2>
                <p>
                    Většina CZ/SK televizí nasadila masivní DRM a geoblocking. V2 to řeší unikátním, vysoce optimalizovaným přístupem bez toho, aniž by server musel stahovat gigabyty videa:
                </p>
                <div class="grid md:grid-cols-2 gap-6 my-8">
                    <div class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                        <h3 class="text-gray-900 dark:text-white mt-0 pt-0"><i class="fas fa-globe text-brand-500 mr-2"></i> Rezidenční Proxy</h3>
                        <p class="text-sm">
                            Běžné datacentrum (VPS) CDN servery okamžitě zaříznou. Náš skript (<code>fetcher.php</code>) dělá API dotazy přes reálné české a slovenské rezidenční sítě (vodafone, O2 atd.). CDN si tak myslí, že o přístup žádá běžný divák z obýváku, a pustí nás k originálnímu <code>.m3u8</code> manifestu.
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-dark-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                        <h3 class="text-gray-900 dark:text-white mt-0 pt-0"><i class="fas fa-network-wired text-brand-500 mr-2"></i> Zero-Bandwidth Stream</h3>
                        <p class="text-sm">
                            V minulé verzi tekl <strong>každý</strong> <code>.ts</code> video segment skrz náš server, což žralo terabyty dat. Verze 2 to nedělá! 
                            Používáme <code>HTTP 302 Redirect</code> a u VLC předáváme parametry <code>#EXTVLCOPT:http-referrer</code>. Server pouze získá povolenku, a vaše koncové zařízení už pak streamuje video přímo z CDN televize.
                        </p>
                    </div>
                </div>

                <h2 class="text-gray-900 dark:text-white"><i class="fas fa-heart text-pink-500 mr-3"></i> Podpořte provoz proxy sítí</h2>
                <div class="bg-gradient-to-br from-brand-50 to-pink-50 dark:from-brand-900/20 dark:to-pink-900/20 border border-brand-200 dark:border-brand-800/50 rounded-3xl p-8 my-8 text-center">
                    <p class="mb-6 font-medium text-gray-900 dark:text-white">
                        Udržet v chodu rezidenční proxy (které televize neblokují) a stabilní servery není zadarmo. Pokud ti projekt udělal radost nebo ušetřil nervy, budeme nesmírně vděční za jakýkoliv drobný příspěvek na kávu a chod serverů.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="https://ko-fi.com/vlastimilnovotny" target="_blank" class="inline-flex justify-center items-center px-8 py-4 bg-[#FF5E5B] hover:bg-[#ff4744] text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                            <i class="fas fa-mug-hot mr-3 text-xl"></i> Podpořit na Ko-fi
                        </a>
                        <a href="https://paypal.me/mxnticek" target="_blank" class="inline-flex justify-center items-center px-8 py-4 bg-[#0079C1] hover:bg-[#00609a] text-white font-bold rounded-2xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                            <i class="fab fa-paypal mr-3 text-xl"></i> PayPal
                        </a>
                    </div>
                </div>

                <h2 class="text-gray-900 dark:text-white"><i class="fas fa-shield-alt text-green-500 mr-3"></i> Ochrana soukromí a dat (Privacy)</h2>
                <p>Náš server funguje čistě jako průchozí bod. Vaše data jsou u nás naprosto v bezpečí:</p>
                <ul>
                    <li><i class="fas fa-check-circle"></i> <strong>Sledujeme pouze statistiky na 1 hodinu:</strong> Ukládáme jen hash návštěvy a kanál pro zobrazení "live viewers".</li>
                    <li><i class="fas fa-check-circle"></i> <strong>Automatické mazání:</strong> Databáze mažou záznamy starší než hodinu. Neexistuje žádná dlouhodobá historie.</li>
                    <li><i class="fas fa-times-circle text-red-500"></i> <strong>Zcela nulové zaznamenávání IP adres:</strong> Náš server nemá na webovém serveru Caddy ani zapnuté access logy (záznamy přístupů). To znamená, že vaše IP adresa se fyzicky nikam nezapíše už v momentě, kdy stránku otevřete. Nikde a nikdy neevidujeme, kdo konkrétně na co kouká.</li>
                    <li><i class="fas fa-times-circle text-red-500"></i> <strong>Žádný prodej dat třetím stranám:</strong> Vaše data nikdy neprodáváme ani neposkytujeme žádným reklamním agenturám ani třetím stranám. Vše zůstává na open-source serveru bez sledovacích pixelů a trackerů.</li>
                </ul>

                <h2 class="text-gray-900 dark:text-white"><i class="fas fa-gavel text-gray-500 dark:text-gray-400 mr-3"></i> Podmínky použití (Terms)</h2>
                <ul>
                    <li><i class="fas fa-balance-scale text-gray-400"></i> Služba je poskytována <strong>"tak jak je" (as-is)</strong> pro vzdělávací a osobní účely. Není garantována žádná dostupnost.</li>
                    <li><i class="fas fa-ban text-gray-400"></i> Projekt neschraňuje ani nenabízí vlastní multimediální obsah, slouží jen jako proxy pro API.</li>
                    <li><i class="fas fa-exclamation-triangle text-gray-400"></i> Jako uživatel zodpovídáte za to, že máte k přehrávanému obsahu legální přístup dle podmínek vysílatele.</li>
                </ul>

            </div>
        </div>
    </main>

    <footer class="mt-auto border-t border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-dark-900/50 backdrop-blur-lg">
        <div class="container mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 opacity-80">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-pink-500 flex items-center justify-center text-white text-xs">
                    <i class="fas fa-bolt"></i>
                </div>
                <p class="text-sm font-medium">Vyrobeno s <i class="fas fa-heart text-red-500 mx-1 text-xs"></i> mxnticek / cyn</p>
            </div>
            <div class="flex items-center gap-6">
                <a href="https://github.com/vlastikyoutubeko/sktv-forwarders" class="text-sm font-semibold text-gray-500 hover:text-brand-500 transition-colors"><i class="fab fa-github mr-1"></i> Zdrojový kód na GitHubu</a>
            </div>
        </div>
    </footer>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) { html.classList.add('dark'); }
        themeToggle.addEventListener('click', () => { html.classList.toggle('dark'); localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light'; });
    </script>
</body>
</html>
