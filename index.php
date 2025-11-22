<?php
// Include channels definition
include "channels.inc.php";

// --- Dynamic Base URL Calculation ---
// This detects the protocol (http/https), host (domain.com), and folder (/sktv) automatically.
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$host = $_SERVER['HTTP_HOST'];
// dirname($_SERVER['PHP_SELF']) gets the current folder (e.g., "/sktv")
// rtrim removes the trailing slash if we are at the root to avoid double slashes
$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); 
$baseUrl = $protocol . "://" . $host . $path;
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKTV Forwarders Revival</title>
    <meta name="description" content="Open-source TV streaming proxy">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        gray: {
                            850: '#1f2937',
                            900: '#111827',
                            950: '#0B0F19',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; padding-top: 60px; padding-bottom: 60px; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        /* Glassmorphism for sticky header */
        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .dark .glass-header {
            background: rgba(17, 24, 39, 0.85);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        /* Card Hover Effect */
        .channel-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .channel-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1);
        }
        .dark .channel-card:hover {
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">

    <!-- Navbar -->
    <nav class="glass-header fixed w-full z-50 top-0 transition-colors duration-300">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Logo Area -->
            <div class="flex items-center space-x-3 select-none">
                <div class="bg-gradient-to-br from-purple-600 to-blue-600 text-white p-2 rounded-lg shadow-lg">
                    <i class="fas fa-tower-broadcast text-lg"></i>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="font-bold text-xl tracking-tight">sktv<span class="text-purple-600 dark:text-purple-400">forwarders</span></span>
                    <span class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400 font-medium">Revival Project</span>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex items-center gap-4">
                <!-- Search Input (Desktop) -->
                <div class="hidden md:flex relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search channels..." 
                           class="bg-gray-100 dark:bg-gray-900 text-sm rounded-full pl-10 pr-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-purple-500/50 border border-transparent focus:border-purple-500/50 transition-all">
                </div>

                <!-- Theme Toggle -->
                <button id="themeToggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors text-gray-600 dark:text-gray-300 focus:outline-none">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:block"></i>
                </button>
                
                <a href="https://github.com/vlastikyoutubeko/sktv-forwarders" class="hidden sm:block text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="fab fa-github text-xl"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Search Input (Mobile) -->
    <div class="md:hidden fixed top-16 left-0 w-full bg-white dark:bg-gray-900 z-40 px-4 py-3 border-b border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="mobileSearchInput" placeholder="Search channels..." 
                   class="bg-gray-100 dark:bg-gray-800 text-sm rounded-lg pl-10 pr-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all">
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 pt-24 pb-12 sm:pt-28">
        
        <?php foreach($channels as $i) { ?>
        
        <!-- Country Section -->
        <div class="mb-12 country-section" data-country="<?php echo htmlspecialchars($i['name']); ?>">
            <!-- Section Header -->
            <div class="flex items-center mb-6 space-x-3 border-b border-gray-200 dark:border-gray-800 pb-4">
                <!-- Flag based on country code if available, generic fallback -->
                <?php if(isset($i['countrycode'])) { ?>
                    <img src="https://flagcdn.com/24x18/<?php echo htmlspecialchars($i['countrycode']); ?>.png" alt="<?php echo htmlspecialchars($i['countrycode']); ?>" class="rounded shadow-sm">
                <?php } else { ?>
                    <i class="fas fa-globe text-gray-400"></i>
                <?php } ?>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($i["name"]); ?></h2>
            </div>

            <!-- Info Box (If Note exists) -->
            <?php if(!empty($i["note"])) { ?>
            <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-900 dark:text-blue-100 prose-sm max-w-none">
                        <?php echo $i["note"]; ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <!-- Channels Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                <?php foreach($i["channels"] as $j) { ?>
                
                <!-- Channel Card -->
                <div class="channel-card-wrapper">
                    <div class="channel-card bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden group flex flex-col h-full">
                        <div class="p-5 flex-grow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-900 dark:text-white group-hover:text-purple-600 transition-colors channel-name">
                                        <?php echo htmlspecialchars($j["name"]); ?>
                                    </h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <button 
                                            onclick="copyToClipboard('<?php echo htmlspecialchars($j["id"]); ?>')" 
                                            class="text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors cursor-pointer channel-id" 
                                            title="Copy Channel ID">
                                            <?php echo htmlspecialchars($j["id"]); ?>
                                        </button>
                                    </div>
                                </div>
                                <!-- Viewer Badge -->
                                <span data-channel-id="<?php echo htmlspecialchars($j["id"]); ?>" 
                                      class="viewer-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700 transition-colors duration-500">
                                    <i class="fas fa-circle-notch fa-spin mr-1.5 text-[10px]"></i> --
                                </span>
                            </div>

                            <div class="flex gap-2 mt-2">
                                <a href="stream.php?x=<?php echo $j["id"]; ?>" 
                                   class="flex-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium py-2.5 px-4 rounded-lg text-center text-sm hover:bg-purple-600 dark:hover:bg-purple-400 hover:shadow-lg transition-all duration-300 flex items-center justify-center group-hover:gap-2">
                                    <i class="fas fa-play text-xs"></i>
                                    <span>Watch</span>
                                </a>
                                <button 
                                    onclick="copyToClipboard('<?php echo $baseUrl; ?>/stream.php?x=<?php echo $j['id']; ?>')" 
                                    class="p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all"
                                    title="Copy Stream URL">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Card Footer -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 px-5 py-2 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs">
                            <span class="text-gray-500">Source</span>
                            <a href="<?php echo $j["streamURL"]; ?>" target="_blank" 
                               class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 flex items-center gap-1 transition-colors truncate max-w-[150px]">
                                <?php echo htmlspecialchars(parse_url($j["streamURL"], PHP_URL_HOST)); ?>
                                <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <!-- Privacy Notice Card -->
        <div class="mt-12 pt-6 border-t border-gray-200 dark:border-gray-800">
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-900 dark:to-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 md:p-8">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                            <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Privacy & Data Transparency</h3>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <div>
                            <p class="mb-2"><strong class="text-gray-900 dark:text-gray-200">What we track:</strong> Channel name, random session ID, and timestamp for live viewer counts only.</p>
                            <p><strong class="text-gray-900 dark:text-gray-200">Retention:</strong> Data is automatically deleted after 30 seconds of inactivity.</p>
                        </div>
                        <div>
                            <p class="mb-2"><strong class="text-gray-900 dark:text-gray-200">We DON'T track:</strong> IP addresses, personal info, or viewing history.</p>
                            <p><a href="privacy.php" class="text-purple-600 dark:text-purple-400 hover:underline font-medium">Read Full Policy &rarr;</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-16 pt-8 text-center md:text-left">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div class="col-span-1">
                    <div class="flex items-center justify-center md:justify-start space-x-2 mb-4">
                        <i class="fas fa-tower-broadcast text-purple-600"></i>
                        <span class="font-bold text-lg text-gray-900 dark:text-white">sktv forwarders</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                        Created originally by an author who doesn't want to be disclosed, now maintained by <a href="https://santomet.eu" class="text-purple-600 hover:underline">santomet</a>.
                    </p>
                </div>
                <div class="col-span-2 flex flex-col md:items-end justify-center">
                    <div class="flex space-x-6 mb-4 justify-center md:justify-end">
                        <a href="privacy.php" class="text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 text-sm font-medium transition-colors">Privacy Policy</a>
                        <a href="https://github.com/vlastikyoutubeko/sktv-forwarders" class="text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 text-sm font-medium transition-colors">GitHub</a>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 max-w-md">
                        Disclaimer: This project is for educational purposes only. Users must have legitimate access to the resources.
                    </p>
                </div>
            </div>
        </footer>

    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-5 right-5 transform translate-y-20 opacity-0 transition-all duration-300 z-50 pointer-events-none">
        <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-3 rounded-lg shadow-lg flex items-center space-x-3">
            <i class="fas fa-check-circle text-green-400 dark:text-green-600"></i>
            <span class="font-medium text-sm">Copied to clipboard!</span>
        </div>
    </div>

    <script>
        // --- Theme Toggle Logic ---
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        }

        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
        });

        // --- Search Logic ---
        const desktopSearch = document.getElementById('searchInput');
        const mobileSearch = document.getElementById('mobileSearchInput');
        
        function filterChannels(query) {
            query = query.toLowerCase().trim();
            document.querySelectorAll('.channel-card-wrapper').forEach(wrapper => {
                const name = wrapper.querySelector('.channel-name').innerText.toLowerCase();
                const id = wrapper.querySelector('.channel-id').innerText.toLowerCase();
                
                if (name.includes(query) || id.includes(query)) {
                    wrapper.style.display = '';
                } else {
                    wrapper.style.display = 'none';
                }
            });
        }

        [desktopSearch, mobileSearch].forEach(input => {
            input.addEventListener('input', (e) => filterChannels(e.target.value));
        });

        // --- Copy to Clipboard ---
        window.copyToClipboard = function(text) {
            // Modern API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => showToast());
            } else {
                // Fallback for HTTP
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast();
                } catch (err) {
                    console.error('Unable to copy', err);
                }
                document.body.removeChild(textArea);
            }
        }

        function showToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2000);
        }

        // --- Viewer Stats Logic ---
        function updateStats() {
            fetch('stats.php?action=get')
                .then(response => response.json())
                .then(stats => {
                    document.querySelectorAll('[data-channel-id]').forEach(el => {
                        const channelId = el.getAttribute('data-channel-id');
                        const count = stats[channelId] || 0;
                        
                        el.innerHTML = `<i class="fas fa-eye mr-1.5 text-[10px]"></i> ${count}`;
                        
                        // Reset classes
                        el.className = 'viewer-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition-colors duration-500 border ';
                        
                        if (count === 0) {
                            el.className += 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 border-gray-200 dark:border-gray-700';
                        } else if (count < 5) {
                            el.className += 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-900/50';
                        } else if (count < 10) {
                            el.className += 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-900/50';
                        } else {
                            el.className += 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-900/50 animate-pulse';
                        }
                    });
                })
                .catch(err => console.error('Error loading stats:', err));
        }
        
        // Initial load and interval
        updateStats();
        setInterval(updateStats, 5000);
    </script>
</body>
</html>
