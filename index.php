<?php
// 1. Load Dependencies
require_once 'src/ChannelRegistry.php';
require_once 'src/StatsManager.php';

// 2. Initialize Logic
try {
    $registry = new ChannelRegistry('config/channels.php');
    $countries = $registry->getCountries();
} catch (Exception $e) {
    die("Error loading configuration: " . $e->getMessage());
}

// 3. Calculate Dynamic Base URL for Clipboard Copying
// This automatically detects if you are in a folder like /sktv/ or at the root.
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
    <title>SKTV Forwarders</title>
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
                        gray: { 850: '#1f2937', 900: '#111827', 950: '#0B0F19' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .glass-header { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .dark .glass-header { background: rgba(17, 24, 39, 0.85); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .code-badge { 
            font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; 
            background-color: rgba(255,255,255,0.6); padding: 0.125rem 0.375rem; 
            border-radius: 0.25rem; color: #6d28d9; border: 1px solid rgba(124, 58, 237, 0.1);
        }
        .dark .code-badge { background-color: rgba(0,0,0,0.3); color: #d8b4fe; border-color: rgba(124, 58, 237, 0.3); }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">

    <nav class="glass-header fixed w-full z-50 top-0">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-gradient-to-br from-purple-600 to-blue-600 text-white p-2 rounded-lg shadow-lg">
                    <i class="fas fa-tower-broadcast text-lg"></i>
                </div>
                <span class="font-bold text-xl tracking-tight">sktv<span class="text-purple-600 dark:text-purple-400">forwarders</span></span>
            </div>
            <!-- Controls -->
            <div class="flex items-center gap-4">
                <button id="themeToggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                    <i class="fas fa-moon dark:hidden"></i><i class="fas fa-sun hidden dark:block"></i>
                </button>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 pt-24 pb-12 sm:pt-28">
        <?php foreach ($countries as $country): ?>
            <div class="mb-12 country-section">
                <!-- Header -->
                <div class="flex items-center mb-6 space-x-3 border-b border-gray-200 dark:border-gray-800 pb-4">
                    <img src="https://flagcdn.com/24x18/<?php echo $country['code']; ?>.png" class="rounded shadow-sm">
                    <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($country['name']); ?></h2>
                </div>

                <!-- Note (Rendered by Registry Logic) -->
                <?php if (!empty($country['note_type'])): ?>
                    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
                        <?php echo $registry->renderNote($country['note_type']); ?>
                    </div>
                <?php endif; ?>

                <!-- Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    <?php foreach ($country['channels'] as $channel): ?>
                        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5 hover:shadow-lg transition-all group">
                            <div class="flex justify-between mb-4">
                                <h3 class="font-bold text-lg group-hover:text-purple-600 transition-colors"><?php echo htmlspecialchars($channel['name']); ?></h3>
                                <span class="text-xs font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-500">
                                    <?php echo htmlspecialchars($channel['id']); ?>
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <a href="stream.php?x=<?php echo $channel['id']; ?>" class="flex-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-2 rounded-lg text-center text-sm font-medium hover:bg-purple-600 dark:hover:bg-purple-400 transition-colors flex items-center justify-center">
                                    <i class="fas fa-play mr-2 text-xs"></i>Watch
                                </a>
                                <!-- Updated Copy Button Logic -->
                                <button 
                                    onclick="copyToClipboard('<?php echo $baseUrl; ?>/stream.php?x=<?php echo $channel['id']; ?>')" 
                                    class="px-3 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors"
                                    title="Copy Stream URL">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-5 right-5 transform translate-y-20 opacity-0 transition-all duration-300 z-50 pointer-events-none">
        <div class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-3 rounded-lg shadow-lg flex items-center space-x-3">
            <i class="fas fa-check-circle text-green-400 dark:text-green-600"></i>
            <span class="font-medium text-sm">Copied to clipboard!</span>
        </div>
    </div>

    <script>
        // Simple Theme Toggle
        const toggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) html.classList.add('dark');
        toggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
        });

        // Copy Logic
        window.copyToClipboard = function(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => showToast());
            } else {
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
                } catch (err) { console.error('Copy failed', err); }
                document.body.removeChild(textArea);
            }
        }

        function showToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => { toast.classList.add('translate-y-20', 'opacity-0'); }, 2000);
        }
    </script>
</body>
</html>
