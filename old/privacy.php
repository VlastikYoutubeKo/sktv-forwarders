<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy & Terms of Service - SKTV Forwarders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto Mono', monospace;
        }
        .prose h2 {
            color: #667eea;
            font-weight: bold;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .prose h3 {
            color: #764ba2;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .prose code {
            background-color: #f3f4f6;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
        .prose pre {
            background-color: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Header -->
            <div class="mb-8 pb-6 border-b-2 border-purple-600">
                <a href="index.php" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Channels
                </a>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Privacy Policy & Terms of Service</h1>
                <p class="text-gray-600">SKTV Forwarders Revival</p>
                <p class="text-sm text-gray-500 mt-2"><strong>Last Updated:</strong> November 20, 2025</p>
            </div>

            <!-- Content -->
            <div class="prose prose-sm max-w-none">
                <h2><i class="fas fa-info-circle mr-2"></i>1. Overview</h2>
                <p>SKTV Forwarders Revival is an open-source TV streaming proxy service that helps users access publicly available television streams. This service is provided for educational and personal use only.</p>

                <h2><i class="fas fa-database mr-2"></i>2. Data Collection</h2>
                
                <h3>2.1 What We Collect</h3>
                <p>When you use our streaming service, we temporarily store the following information:</p>
                <ul class="list-disc ml-6 space-y-2">
                    <li><strong>Channel ID</strong> - Which channel you're watching (e.g., "STV1", "Nova", "Prima")</li>
                    <li><strong>Session ID</strong> - A randomly generated identifier for your viewing session (PHP session ID)</li>
                    <li><strong>Last Seen Timestamp</strong> - Unix timestamp of your last activity</li>
                </ul>

                <h3>2.2 Storage Method</h3>
                <p>This data is stored in a local SQLite database (<code>viewers.db</code>) on our server.</p>

                <h3>2.3 Data Structure</h3>
                <pre>CREATE TABLE viewers (
    channel TEXT,           -- Channel identifier
    session_id TEXT,        -- PHP session ID
    last_seen INTEGER,      -- Unix timestamp
    PRIMARY KEY (channel, session_id)
);</pre>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 my-4">
                    <p class="font-semibold text-blue-800">Example record:</p>
                    <pre class="bg-blue-100 text-blue-900">channel: "Prima"
session_id: "6a6a88dafda1b2b3017ab4f4bc6b4928"
last_seen: 1732127880</pre>
                </div>

                <h2><i class="fas fa-tasks mr-2"></i>3. How We Use Your Data</h2>
                
                <h3>3.1 Purpose</h3>
                <p>The collected data is used <strong>exclusively</strong> for:</p>
                <ol class="list-decimal ml-6 space-y-2">
                    <li><strong>Live viewer counting</strong> - Displaying how many people are currently watching each channel</li>
                    <li><strong>Session management</strong> - Tracking active streaming sessions</li>
                    <li><strong>Service operation</strong> - Ensuring proper stream delivery</li>
                </ol>

                <h3>3.2 Data Retention</h3>
                <ul class="list-disc ml-6 space-y-2">
                    <li><strong>Active Sessions:</strong> Data is kept while you're watching (last_seen < 30 seconds)</li>
                    <li><strong>Inactive Sessions:</strong> Automatically deleted after 30 seconds of inactivity</li>
                    <li><strong>No Long-term Storage:</strong> We do NOT keep historical viewing records</li>
                </ul>

                <div class="bg-green-50 border-l-4 border-green-500 p-4 my-4">
                    <p class="font-semibold text-green-800">
                        <i class="fas fa-clock mr-2"></i>Automatic Cleanup
                    </p>
                    <p class="text-green-700">Your data is automatically deleted after just 30 seconds of inactivity!</p>
                </div>

                <h2><i class="fas fa-ban mr-2"></i>4. What We DON'T Collect</h2>
                <p>We explicitly <strong>DO NOT</strong> collect:</p>
                <ul class="list-none ml-6 space-y-2">
                    <li>❌ IP addresses</li>
                    <li>❌ User names or personal information</li>
                    <li>❌ Email addresses</li>
                    <li>❌ Viewing history</li>
                    <li>❌ Device information</li>
                    <li>❌ Location data</li>
                    <li>❌ Browser fingerprints</li>
                    <li>❌ Cookies (beyond PHP session)</li>
                    <li>❌ Any personally identifiable information (PII)</li>
                </ul>

                <h2><i class="fas fa-share-alt mr-2"></i>5. Data Sharing</h2>
                
                <h3>5.1 Third Parties</h3>
                <p class="font-bold text-lg">We DO NOT share your data with any third parties. Period.</p>

                <h3>5.2 Public Display</h3>
                <p>The only data made public is:</p>
                <ul class="list-disc ml-6">
                    <li><strong>Aggregate viewer counts</strong> per channel (e.g., "5 people watching Nova")</li>
                    <li>No individual viewer data is ever displayed</li>
                </ul>

                <h2><i class="fas fa-user-shield mr-2"></i>6. Your Rights</h2>
                
                <h3>6.1 Data Deletion</h3>
                <ul class="list-disc ml-6 space-y-2">
                    <li>Automatically deleted after 30 seconds of inactivity</li>
                    <li>Close your browser/player for immediate session termination</li>
                </ul>

                <h3>6.2 Opt-Out</h3>
                <p>To opt out of viewer tracking:</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 my-4">
                    <p class="text-yellow-800">
                        <strong>Use the "Original URL" links</strong> on the main page to bypass our proxy entirely.
                    </p>
                </div>

                <h2><i class="fas fa-gavel mr-2"></i>7. Legal Disclaimer</h2>
                
                <h3>7.1 Educational Purpose</h3>
                <p>This project is provided for <strong>educational purposes only</strong>.</p>

                <h3>7.2 User Responsibility</h3>
                <p>By using this service, you agree that you:</p>
                <ul class="list-disc ml-6 space-y-2">
                    <li>Have legitimate access to the content</li>
                    <li>Comply with all applicable laws</li>
                    <li>Are responsible for your own actions</li>
                </ul>

                <h3>7.3 No Warranty</h3>
                <p>This service is provided "as is" without any warranty.</p>

                <h2><i class="fab fa-github mr-2"></i>8. Open Source</h2>
                <p>This project is open source under AGPL-3.0-or-later license.</p>
                <p><strong>Source Code:</strong> <a href="https://github.com/vlastikyoutubeko/sktv-forwarders" class="text-blue-600 hover:underline">https://github.com/vlastikyoutubeko/sktv-forwarders</a></p>

                <div class="bg-purple-50 border-2 border-purple-500 rounded-lg p-6 my-8">
                    <h3 class="text-xl font-bold text-purple-900 mb-3">
                        <i class="fas fa-clipboard-check mr-2"></i>Summary
                    </h3>
                    <p class="text-purple-800">
                        We collect <strong>minimal data</strong> (channel, session ID, timestamp) solely for live viewer counting. 
                        This data is <strong>automatically deleted after 30 seconds</strong>. We don't collect personal information, 
                        don't share data with anyone, and you can opt out anytime by using original URLs.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-300 text-center">
                <a href="index.php" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Channels
                </a>
            </div>
        </div>
    </div>
</body>
</html>
