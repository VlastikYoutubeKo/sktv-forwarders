<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sktv forwarders revival</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto Mono', monospace;
            /* Prostor pro Endora bannery - top i bottom */
            padding-top: 60px;
            padding-bottom: 60px;
        }
        
        /* Responsivní padding pro mobily */
        @media (max-width: 650px) {
            body {
                padding-top: 55px;
                padding-bottom: 55px;
            }
        }
        
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Gradient pozadí */
        .header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Hover efekty na tabulky */
        tbody tr {
            transition: all 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: #f3f4f6;
            transform: translateX(4px);
        }
        
        /* Card design pro sekce */
        .country-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .country-card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        /* Stylizace odkazů */
        a.channel-link {
            position: relative;
            text-decoration: none;
        }
        
        a.channel-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #3b82f6;
            transition: width 0.3s ease;
        }
        
        a.channel-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <!-- Header s gradientem -->
    <div class="header-gradient text-white py-12 mb-8 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center space-x-3 text-5xl font-bold mb-4">
                <h1 class="text-white drop-shadow-lg">sk</h1>
                <h1 class="text-blue-200 drop-shadow-lg">tv</h1>
                <h1 class="drop-shadow-lg">forwarders</h1>
            </div>
            <p class="text-center text-lg opacity-90">
                <i class="fas fa-tv mr-2"></i>
                Open-source TV streaming proxy
            </p>
            <p class="text-center mt-2 text-sm opacity-75">
                <i class="fab fa-github mr-1"></i>
                licensed under AGPL-3.0-or-later, 
                <a href="https://github.com/santomet/sktv-forwarders" class="underline hover:text-blue-200 transition">source available</a>
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 pb-8">
        <?php
        include "channels.inc.php";
        foreach($channels as $i) {
        ?>
        <div class="country-card mb-8 p-6">
            <div class="flex items-center mb-6">
                <i class="fas fa-broadcast-tower text-purple-600 text-2xl mr-3"></i>
                <h2 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($i["name"]); ?></h2>
            </div>
            
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-purple-600 to-blue-600 text-white">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">
                                <i class="fas fa-tv mr-2"></i>Channel
                            </th>
                            <th class="px-6 py-3 text-left font-semibold">
                                <i class="fas fa-users mr-2"></i>Viewers
                            </th>
                            <th class="px-6 py-3 text-left font-semibold">
                                <i class="fas fa-link mr-2"></i>Streaming URL
                            </th>
                            <th class="px-6 py-3 text-left font-semibold">
                                <i class="fas fa-external-link-alt mr-2"></i>Original URL
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        foreach($i["channels"] as $j) {
                        ?>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <?php echo htmlspecialchars($j["name"]); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span data-channel-id="<?php echo htmlspecialchars($j["id"]); ?>" 
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                    <i class="fas fa-circle-notch fa-spin mr-1"></i>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="stream.php?x=<?php echo $j["id"]; ?>" 
                                   class="channel-link text-blue-600 font-mono text-sm hover:text-blue-800">
                                    <?php echo htmlspecialchars($j["id"]); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="<?php echo $j["streamURL"]; ?>" 
                                   class="channel-link text-blue-500 text-sm hover:text-blue-700"
                                   target="_blank">
                                    <?php echo htmlspecialchars(strlen($j["streamURL"]) > 40 ? (substr($j["streamURL"], 0, 37) . "...") : $j["streamURL"]); ?>
                                    <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(!empty($i["note"])) { ?>
            <div class="mt-4 bg-gradient-to-r from-purple-50 to-blue-50 border-l-4 border-purple-500 p-6 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-purple-600 text-xl mt-1 mr-3"></i>
                    <div class="text-sm text-gray-700 flex-1">
                        <?php echo $i["note"]; ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php
        }
        ?>
        
        <!-- Footer -->
        <div class="mt-12 pt-8 border-t border-gray-300">
            <p class="text-center text-sm text-gray-600 mb-2">
                &copy; <?php echo date("Y"); ?> Created originally by an author who doesn't want to be disclosed, 
                now maintained by <a href="https://santomet.eu" class="text-purple-600 hover:underline">santomet</a>
            </p>
            <p class="text-center text-sm text-gray-600 mb-2">
                Redesigned by <a href="https://mxnticek.eu" class="text-blue-600 hover:underline">mxnticek</a> 
                using <a href="https://claude.ai/" class="text-blue-600 hover:underline">Claude</a>
            </p>
            <p class="text-center text-xs text-gray-500 italic max-w-3xl mx-auto mt-4">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Disclaimer: This project is an open-source initiative provided for educational purposes only. 
                The software and scripts contained herein are intended to be used exclusively by individuals who have 
                legitimate access to the resources, such as by completing any required registrations or residing in 
                regions where access is permitted by the content provider.
            </p>
        </div>
    </div>

    <script>
        // Načti a zobraz statistiky
        function updateStats() {
            fetch('stats.php?action=get')
                .then(response => response.json())
                .then(stats => {
                    document.querySelectorAll('[data-channel-id]').forEach(el => {
                        const channelId = el.getAttribute('data-channel-id');
                        const count = stats[channelId] || 0;
                        el.innerHTML = `<i class="fas fa-eye mr-1"></i>${count}`;
                        
                        // Barevné označení podle počtu diváků
                        el.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ';
                        if (count === 0) {
                            el.className += 'bg-gray-200 text-gray-800';
                        } else if (count < 5) {
                            el.className += 'bg-green-100 text-green-800';
                        } else if (count < 10) {
                            el.className += 'bg-blue-100 text-blue-800';
                        } else {
                            el.className += 'bg-red-100 text-red-800';
                        }
                    });
                })
                .catch(err => console.error('Error loading stats:', err));
        }
        
        // Aktualizuj statistiky každých 5 sekund
        updateStats();
        setInterval(updateStats, 5000);
    </script>
</body>
</html>
