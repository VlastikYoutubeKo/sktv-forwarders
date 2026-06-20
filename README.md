# SKTV Forwarders V2 📺

An open-source proxy and stream forwarder for Czech and Slovak television channels, designed to bypass DRM and geoblocking using a minimal-bandwidth approach. 

> **Important:** This project is intended for educational purposes. It does not host any streams. It merely acts as a proxy for the official public APIs of various TV stations.

## 🚀 Features (V2 Update)
*   **Zero-Bandwidth Streaming:** Instead of proxying every single `.ts` chunk (which consumes terabytes of server traffic), V2 parses the manifest using a proxy and returns an HTTP 302 redirect with `#EXTVLCOPT:http-referrer` headers. Your player connects directly to the CDN!
*   **Residential Proxy Support:** Avoids datacenter CDN blocks by routing API requests through predefined residential or ISP proxy pools.
*   **Dual-Mode Interface:** 
    *   Open the `/new/` directory in a browser to see a premium Glassmorphism UI with live viewer counts.
    *   Open the `/new/` directory directly in VLC or MPV to immediately receive a generated M3U8 playlist.
*   **Viewer Tracking:** Real-time channel tracking utilizing a lightweight SQLite database (retained for 1 hour).

## 🛠️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/vlastikyoutubeko/sktv-forwarders.git
   cd sktv-forwarders/new
   ```

2. **Setup Proxies:**
   Copy the example config and add your proxy servers:
   ```bash
   cp config.example.php config.php
   ```
   Edit `config.php` to include your proxy servers (e.g. `user:pass@ip:port` or `ip:port`).

3. **Permissions:**
   Ensure your web server (e.g., `www-data`) has write access to the `/new/` directory so it can create the `viewers.db` SQLite database:
   ```bash
   chmod 777 . 
   # Or chown www-data:www-data .
   ```

4. **Web Server Configuration:**
   Point your web server (Nginx, Apache, or Caddy) to the repository. No special URL rewriting is strictly required, standard PHP-FPM works out of the box.

## 📡 Adding Channels

Channels are mapped inside `new/channels.php`. You can add new ones by matching their API IDs and assigning them to a fetcher function (e.g., `nova_fetcher`, `prima_fetcher`).

## ⚖️ Privacy & Disclaimer

This software does not log user IP addresses during stream generation (ensure your web server access logs are disabled if you want complete anonymity). It only retains a temporary PHP session hash and channel name for 1 hour to display "live viewer" statistics.

**Do not use this for commercial purposes.** The code is open-source under the AGPL-3.0 license.
