# SKTV Forwarders V2 📺

<div align="center">
  <p>An open-source proxy and stream forwarder for Czech and Slovak television channels, designed to bypass DRM and geoblocking using a minimal-bandwidth approach.</p>
</div>

> **Important:** This project is intended for educational purposes. It does not host any streams. It merely acts as a proxy for the official public APIs of various TV stations.

## 🚀 Features (V2 Update)
* **Zero-Bandwidth Streaming:** V2 parses the manifest using a proxy and returns an HTTP 302 redirect with `#EXTVLCOPT:http-referrer` headers. Your player connects directly to the CDN!
* **Robust Geoblock Bypass:** Integrates deeply with residential ISP proxy pools (`config.php`) to bypass Cloudflare challenges and `cra.cz` CDN geoblocking for platforms like Markíza and Nova.
* **Dual-Mode Interface:**
  * Open the `/new/` directory in a browser to see a premium Glassmorphism UI with live viewer counts.
  * Open the `/new/stream.php` URL directly in VLC or MPV to immediately receive a generated M3U8 playlist.
* **Viewer Tracking:** Real-time channel tracking utilizing a lightweight SQLite database (retained for 1 hour).
* **Bot Friendly:** Detects Discord, Twitter, and Telegram scrapers to show rich metadata embeds instead of wasting proxy bandwidth.

## 🛠️ Installation & Setup

### 1. Clone the repository
```bash
git clone https://github.com/vlastikyoutubeko/sktv-forwarders.git
cd sktv-forwarders/new
```

### 2. Setup Proxies
Copy the example configuration and insert your proxy servers. This is **required** to bypass geoblocking for CZ/SK channels.
```bash
cp config.example.php config.php
```
Edit `config.php` and define your proxy IPs (e.g. `user:pass@ip:port`).

### 3. Permissions
Ensure your web server (e.g., `www-data`) has write access to the `/new/` directory so it can create the `viewers.db` SQLite database:
```bash
chmod 777 . 
# Or chown www-data:www-data .
```

### 4. Web Server Configuration
Point your web server (Nginx, Apache, or Caddy) to the repository. No special URL rewriting is strictly required. Standard PHP-FPM works out of the box.

## 📡 Adding Channels
Channels are mapped inside `new/channels.php`. You can add new ones by matching their API IDs and assigning them to a fetcher function (e.g., `nova_fetcher`, `prima_fetcher`).

See [CONTRIBUTING.md](CONTRIBUTING.md) for more details on submitting pull requests or requesting new channels.

## 📜 Changelog

### v2.1.0 - Residential Geoblock Hotfix
* **Fixed:** Resolved an issue where Nova TN Live and Markíza TN Live skipped or returned 403 Forbidden for international viewers.
* **Enhanced:** `proxy.php` now automatically intercepts `.ts` segment downloads and tunnels them through `config.php` residential proxies if the CDN requires a CZ/SK IP (e.g., `cra.cz`, `nova-ott`, `cmesk-ott`).
* **Changed:** Forced `proxy=1` mode to be active by default for all streaming requests to prevent playback failures.

### v2.0.0 - The V2 Overhaul
* **Added:** Premium Glassmorphism Web UI (`index.php`).
* **Added:** Real-time viewer count utilizing SQLite (`viewers.db`).
* **Enhanced:** Reduced server bandwidth usage by up to 99% using HTTP 302 VLC referrer forwarding.
* **Fixed:** Resolved DRM token expiry issues by creating dynamic API hash retrieval in `fetcher.php`.

## ⚖️ Privacy & Disclaimer
This software does not log user IP addresses during stream generation. It only retains a temporary PHP session hash and channel name for 1 hour to display "live viewer" statistics.

**Do not use this for commercial purposes.** The code is open-source under the AGPL-3.0 license.
