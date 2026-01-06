<?php
/**
 * PHP Development Server - Detailed Summary
 *
 * The PHP built-in development server is a lightweight web server designed for development and testing purposes.
 * It was introduced in PHP 5.4 and is not intended for production use.
 *
 * Key Features:
 * - No configuration required: Start serving PHP files instantly without setting up Apache, Nginx, or other web servers.
 * - Supports static files: Serves HTML, CSS, JS, images, and other static assets.
 * - Handles PHP scripts: Executes PHP files as expected.
 * - Custom router: Allows specifying a router script to handle requests (useful for single-page applications or frameworks).
 *
 * Usage:
 * Run the following command in your project directory:
 *   php -S localhost:8000
 * This starts the server at http://localhost:8000, serving files from the current directory.
 *
 * Custom Router Example:
 *   php -S localhost:8000 router.php
 * The router.php script can be used to control request routing.
 *
 * Limitations:
 * - Not suitable for production: Lacks advanced features, security, and performance optimizations.
 * - Single-threaded: Handles one request at a time.
 * - No .htaccess support: Apache-specific configurations are ignored.
 *
 * Typical Use Cases:
 * - Rapid prototyping and testing
 * - Running demos or tutorials
 * - Developing APIs or web applications locally
 *
 * Documentation:
 * - Official PHP Manual: https://www.php.net/manual/en/features.commandline.webserver.php
 */