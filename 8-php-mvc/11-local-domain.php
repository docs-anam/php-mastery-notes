<?php

/**
 * Detailed Summary: Local Domain and Apache HTTPD
 *
 * 1. Local Domain:
 *    - A local domain is a custom domain name (e.g., mysite.local) used to access web projects on your local machine.
 *    - It helps developers simulate production environments for realistic development and testing.
 *    - Local domains are mapped to the local server IP (127.0.0.1) via the system's hosts file and are not accessible from the internet.
 *
 * 2. Hosts File:
 *    - The hosts file maps hostnames to IP addresses on your system.
 *    - Locations:
 *        - macOS/Linux: /etc/hosts
 *        - Windows: C:\Windows\System32\drivers\etc\hosts
 *    - To add a local domain, edit the hosts file with admin privileges:
 *        Example: 127.0.0.1   mysite.local
 *    - Multiple domains can be mapped for different projects.
 *
 * 3. Apache HTTPD:
 *    - Apache HTTPD is a popular open-source web server for serving web pages and APIs.
 *    - Used for local development, staging, and production.
 *    - Key configuration files:
 *        - httpd.conf: Main server configuration.
 *        - httpd-vhosts.conf: Virtual hosts configuration.
 *        - .htaccess: Per-directory configuration (if AllowOverride is enabled).
 *    - Can be managed via command line or GUI tools (e.g., MAMP, XAMPP).
 *
 * 4. Virtual Hosts:
 *    - Virtual Hosts allow Apache to serve multiple domains/sites from one server.
 *    - Each Virtual Host has its own domain, document root, and settings.
 *    - Example configuration:
 *        <VirtualHost *:80>
 *            ServerName mysite.local
 *            DocumentRoot "/Applications/MAMP/htdocs/mysite"
 *            <Directory "/Applications/MAMP/htdocs/mysite">
 *                AllowOverride All
 *                Require all granted
 *            </Directory>
 *            ErrorLog "/Applications/MAMP/logs/mysite-error.log"
 *            CustomLog "/Applications/MAMP/logs/mysite-access.log" common
 *        </VirtualHost>
 *    - Steps:
 *        1. Define ServerName (local domain).
 *        2. Set DocumentRoot (project folder).
 *        3. Configure <Directory> permissions.
 *        4. Optionally set log files for debugging.
 *    - Restart Apache after editing to apply changes.
 *
 * 5. Workflow:
 *    - Step 1: Add local domain to hosts file (e.g., 127.0.0.1 mysite.local).
 *    - Step 2: Configure Apache Virtual Host for the domain.
 *    - Step 3: Place project files in the DocumentRoot.
 *    - Step 4: Restart Apache server.
 *    - Step 5: Access your project via http://mysite.local in the browser.
 *
 * Benefits:
 *    - Organizes multiple projects with unique domains.
 *    - Simulates production-like environment for accurate testing.
 *    - Enables use of cookies, sessions, and redirects as on live sites.
 *    - Cleaner, memorable URLs (no need for localhost:port).
 *    - Supports SSL/TLS for HTTPS development (with extra configuration).
 *
 * Troubleshooting:
 *    - Ensure hosts file changes are saved and Apache is restarted.
 *    - Check for typos in domain names and paths.
 *    - Verify Apache configuration syntax.
 *    - Clear browser cache if domain changes are not reflected.
 */