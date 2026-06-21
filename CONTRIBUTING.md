# Contributing to SKTV Forwarders 📺

First of all, thank you for considering contributing to SKTV Forwarders! It's people like you that make this tool better for everyone.

This document provides guidelines and instructions for contributing.

## How Can I Contribute?

### 1. Reporting Bugs
If you find a bug or a stream that is skipping or failing:
- Open an issue on GitHub.
- Provide the channel name (e.g., *Nova TN Live* or *Markíza TN Live*).
- Describe the expected behavior and what actually happens.
- Include any relevant console errors, HTTP status codes (like 403 Forbidden), or VLC log output.

### 2. Suggesting Enhancements
Have an idea for a new feature or want to add a new television channel?
- Open an issue explaining your idea.
- If suggesting a new channel, please provide details about the broadcaster's website or streaming API if you know it.

### 3. Submitting Pull Requests
We welcome pull requests for bug fixes, new fetchers, UI improvements, and more!

**Development Workflow:**
1. Fork the repository.
2. Create a new branch for your feature or bug fix: `git checkout -b feature/new-channel` or `git checkout -b fix/proxy-error`.
3. Make your changes in the `/new/` directory (the active V2 codebase).
4. If you are adding a new channel, ensure you update `channels.php` and add the corresponding logic in `fetcher.php`.
5. Test your changes locally to ensure the M3U8 proxy generation works.
6. Commit your changes with clear, descriptive commit messages.
7. Push to your fork and submit a Pull Request to the `main` branch.

## Code Structure & Architecture

When contributing, please follow the existing architecture:
- **`fetcher.php`**: Contains the logic for reaching out to broadcaster APIs (e.g., Nova, Markíza, Prima) and retrieving the raw M3U8 manifests. If you are adding a new CDN, be sure to update the `$proxy_region` detection rules inside `curl_fetch()` if the CDN utilizes Geoblocking.
- **`stream.php`**: The main entry point for stream generation. Handles M3U8 rewriting and viewer statistics tracking.
- **`proxy.php`**: Highly optimized chunk proxy. It rewrites and downloads `.ts` segments through residential proxies (based on `config.php`) if the CDN requires a CZ or SK IP address.
- **`channels.php`**: The configuration dictionary mapping channel names to their internal API IDs and fetcher functions.

## Development Setup

To run the project locally, you need a web server with PHP (8.0+ recommended) and the `php-curl` and `php-sqlite3` extensions.
1. Clone the repo.
2. Copy `config.example.php` to `config.php` and insert your proxy credentials.
3. Serve the directory. 

*Note: For testing Geoblocked streams locally, you must provide valid residential proxy credentials in `config.php`.*

## License

By contributing to this project, you agree that your contributions will be licensed under the project's AGPL-3.0 License.
