# PHP Development Server

## Overview

The PHP development server is a lightweight, built-in web server that comes with PHP. It's perfect for development and testing without needing to configure Apache or Nginx.

---

## Table of Contents

1. What is PHP Development Server?
2. Installation and Requirements
3. Starting the Server
4. Server Configuration
5. Accessing Applications
6. Debugging and Testing
7. Limitations
8. Complete Examples

---

## What is PHP Development Server?

### Purpose

```php
<?php
// The PHP built-in development server:
//
// - Lightweight web server included with PHP
// - No configuration needed
// - Perfect for development and testing
// - NOT for production use
// - Handles one request at a time
// - Easy to start and stop
?>
```

### When to Use

```php
<?php
// USE for:
// ✓ Local development
// ✓ Testing PHP applications
// ✓ Learning PHP
// ✓ Running simple scripts
// ✓ Quick prototyping

// DON'T use for:
// ✗ Production environments
// ✗ Multiple concurrent users
// ✗ High-traffic applications
// ✗ SSL/TLS in production
?>
```

---

## Installation and Requirements

### System Requirements

```bash
# PHP 5.4+ required
php --version

# Output example:
# PHP 7.4.30 (cli) (built: Dec 21 2021 12:37:23) ( NTS )
```

### On macOS

```bash
# Check if PHP is installed
php --version

# If not installed, install with Homebrew
brew install php

# Verify installation
php -S localhost:8000
```

### On Windows

```bash
# Download PHP from php.net
# Or install with Chocolatey
choco install php

# Or XAMPP/WAMP (includes development server)
# Verify
php -v
```

### On Linux

```bash
# Ubuntu/Debian
sudo apt-get install php

# CentOS/RHEL
sudo yum install php

# Verify
php --version
```

---

## Starting the Server

### Basic Startup

```bash
# Navigate to project directory
cd /path/to/project

# Start server on localhost:8000
php -S localhost:8000

# Output:
# Development Server (http://localhost:8000) started
# Listening on http://localhost:8000
# Document root is /path/to/project
# Press Ctrl+C to quit
```

### Custom Port

```bash
# Use different port
php -S localhost:9000
php -S localhost:3000
php -S localhost:8080

# Listen on all interfaces
php -S 0.0.0.0:8000

# Output will show server running on specified port
```

### Specify Document Root

```bash
# Start with custom document root
php -S localhost:8000 -t /path/to/webroot

# Useful when public/ is your webroot
php -S localhost:8000 -t ./public

# Or for multi-directory project
php -S localhost:8000 -t /home/user/project/web
```

### Router Script

```bash
# Use router script for URL rewriting
php -S localhost:8000 router.php

# This runs router.php for every request
# Great for single-page applications
```

---

## Server Configuration

### Router Script

```php
<?php
// router.php
// Handles routing for all requests

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If requesting actual file/directory, serve it
if (is_file($_SERVER['DOCUMENT_ROOT'] . $uri)) {
    return false;
}

// If requesting directory, serve index.php
if (is_dir($_SERVER['DOCUMENT_ROOT'] . $uri)) {
    $_SERVER['REQUEST_URI'] = $uri . '/index.php';
}

// Route all other requests to index.php
require 'index.php';
?>
```

```bash
# Start server with router
php -S localhost:8000 router.php
```

### .htaccess Alternative

```bash
# Create .htaccess for development
# When using development server with router.php,
# this isn't needed, but useful for Apache

# .htaccess content:
# <IfModule mod_rewrite.c>
#     RewriteEngine On
#     RewriteBase /
#     RewriteRule ^index\.php$ - [L]
#     RewriteCond %{REQUEST_FILENAME} !-f
#     RewriteCond %{REQUEST_FILENAME} !-d
#     RewriteRule . /index.php [L]
# </IfModule>
```

### Environment Variables

```php
<?php
// Set environment for development
putenv('APP_ENV=development');
putenv('DEBUG=true');

// Access in application
$env = getenv('APP_ENV');
$debug = getenv('DEBUG');

// Or use .env file
if (file_exists('.env')) {
    $env_vars = parse_ini_file('.env');
    foreach ($env_vars as $key => $value) {
        putenv("$key=$value");
    }
}
?>
```

---

## Accessing Applications

### Local URLs

```php
<?php
// Access from browser or JavaScript

// Root
http://localhost:8000/

// Specific file
http://localhost:8000/index.php

// Subdirectory
http://localhost:8000/admin/dashboard.php

// Query parameters
http://localhost:8000/search.php?q=php

// API endpoint
http://localhost:8000/api/users.php

// REST with router
http://localhost:8000/users/123
// (router.php routes to index.php)
?>
```

### From Other Machines

```bash
# Start listening on all interfaces
php -S 0.0.0.0:8000

# Then access from other machine
# http://192.168.1.100:8000
# (replace IP with your actual IP)

# Find your IP address:
# macOS/Linux: ifconfig
# Windows: ipconfig
```

### JavaScript Fetch

```javascript
// Fetch from development server
fetch('http://localhost:8000/api/users')
    .then(response => response.json())
    .then(data => console.log(data));

// POST request
fetch('http://localhost:8000/api/users', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        name: 'John',
        email: 'john@example.com'
    })
})
.then(response => response.json());
```

---

## Debugging and Testing

### Error Reporting

```php
<?php
// php.ini settings for development
error_reporting(E_ALL);
display_errors = On;
display_startup_errors = On;

// Or in PHP code
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');

// Log errors
error_log("Debug message");
error_log("Variable value: " . print_r($var, true));
?>
```

### Access Logs

```bash
# Development server shows requests in terminal

# GET /index.php HTTP/1.1  200 [1234 B in 123 ms]
# POST /api/users HTTP/1.1  201 [5678 B in 456 ms]

# Check terminal for request details
# Useful for debugging routing and requests
```

### Console Output

```php
<?php
// Output debug info to browser console
error_log("Debug info", 3, "error.log");

// Or to terminal
echo "[DEBUG] Important message\n";
fwrite(STDERR, "Error occurred\n");

// Using var_dump
var_dump($data);  // Shows in browser

// Using print_r
print_r($array);  // Shows in browser

// Development helper
function debug($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
?>
```

### Testing APIs

```bash
# Test with curl
curl http://localhost:8000/api/users

# POST request
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@example.com"}'

# With authorization
curl -H "Authorization: Bearer token123" \
  http://localhost:8000/api/protected
```

---

## Limitations

### Single-Threaded

```php
<?php
// Server handles one request at a time
// Slow operations block other requests

// PROBLEM: This blocks the server
sleep(5);  // Server won't handle other requests for 5 seconds

// Solution for development:
// - Use threading library in production
// - Keep operations fast in development
?>
```

### No Built-In SSL

```bash
# Development server doesn't support SSL by default
# http://localhost:8000 works
# https://localhost:8000 does NOT work

# For SSL testing in development:
# - Use tunneling tool (ngrok)
# - Use Apache/Nginx
# - Use Docker
?>
```

### Limited Features

```php
<?php
// Development server doesn't support:
// - .htaccess files
// - Multiple virtual hosts
// - URL rewriting (use router.php instead)
// - Load balancing
// - Process management
// - Caching headers (use router.php)

// These features are for production servers
?>
```

---

## Complete Examples

### Simple Application

```bash
# Create project directory
mkdir my-app
cd my-app

# Start server
php -S localhost:8000

# Create index.php
echo '<?php echo "Hello World"; ?>' > index.php

# Visit http://localhost:8000 in browser
```

### Development Setup

```bash
# project structure
project/
├── public/           # Document root
│   ├── index.php
│   ├── css/
│   └── js/
├── src/              # Application code
│   ├── Database.php
│   └── UserService.php
├── config.php        # Configuration
└── router.php        # Router script

# Start server
php -S localhost:8000 -t public router.php
```

### Router Script Example

```php
<?php
// router.php
// Handle all requests

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/';  // If in subdirectory, adjust this

// Remove base path from URI
$request_uri = substr($request_uri, strlen($base_path));

// Serve static files
if (is_file($file = $_SERVER['DOCUMENT_ROOT'] . '/' . $request_uri)) {
    return false;  // Let server serve the file
}

// Serve directories
if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $request_uri)) {
    $_SERVER['REQUEST_URI'] = $request_uri . '/index.php';
    include 'index.php';
    return;
}

// Route everything else to index.php
$_SERVER['REQUEST_URI'] = '/index.php';
include 'index.php';
?>
```

### Full Development Server Setup

```bash
#!/bin/bash
# start-dev.sh

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PORT=8000
DOCUMENT_ROOT="./public"
ROUTER="router.php"

echo -e "${YELLOW}Starting PHP Development Server...${NC}"
echo -e "${YELLOW}Port: $PORT${NC}"
echo -e "${YELLOW}Document Root: $DOCUMENT_ROOT${NC}"
echo -e "${GREEN}Access: http://localhost:$PORT${NC}"
echo -e "${YELLOW}Press Ctrl+C to stop${NC}\n"

php -S localhost:$PORT -t $DOCUMENT_ROOT $ROUTER
```

```bash
# Make script executable
chmod +x start-dev.sh

# Run it
./start-dev.sh
```

---

## Pro Tips

1. **Use Router Script** for single-page applications
2. **Check Terminal** for server output and errors
3. **Use Different Ports** for multiple projects
4. **Set Error Reporting** for development
5. **Keep Operations Fast** to avoid blocking
6. **Test APIs** with curl before using in frontend
7. **Use Logging** instead of relying on console output

---

## See Also

- [PHP Web Development](3-php-web.md)
- [Integrating with HTML](8-integrate-with-html.md)
- [Global Variables](9-global-variable-server.md)
