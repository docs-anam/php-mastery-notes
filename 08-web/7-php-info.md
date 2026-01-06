# PHP Info and Server Information

## Overview

The `phpinfo()` function displays detailed information about PHP's configuration, installed modules, environment variables, and server settings. This chapter covers how to use `phpinfo()` and other methods to inspect server information.

---

## Table of Contents

1. phpinfo() Function
2. What Information is Displayed
3. Output Formatting
4. Security Considerations
5. Accessing Specific Information
6. Server Information Arrays
7. Version Information
8. Complete Examples

---

## phpinfo() Function

### Basic Usage

```php
<?php
// Simplest usage - display all information
phpinfo();
?>
```

### Output Sections

```php
<?php
// phpinfo() accepts optional parameter to show specific sections
// Available constants:

phpinfo(INFO_GENERAL);        // General information
phpinfo(INFO_CREDITS);        // PHP Credits
phpinfo(INFO_CONFIGURATION);  // Configuration settings
phpinfo(INFO_MODULES);        // Loaded modules
phpinfo(INFO_ENVIRONMENT);    // Environment variables
phpinfo(INFO_VARIABLES);      // Predefined variables
phpinfo(INFO_LICENSE);        // License information
phpinfo(INFO_ALL);            // All information (default)

// You can combine with bitwise OR
phpinfo(INFO_GENERAL | INFO_CONFIGURATION);
?>
```

### Practical Examples

```php
<?php
// Display only general and configuration info
phpinfo(INFO_GENERAL | INFO_CONFIGURATION);
?>
```

---

## What Information is Displayed

### General Information Section

```
PHP Version
System
Build Date
Configure Command
Server API
Virtual Directory Support
Configuration File Path
Loaded Configuration Files
Scan Directory
Additional .ini Files Parsed
```

### Configuration Settings

```
Core Settings
  display_errors = Off
  error_reporting = 32767
  max_execution_time = 30
  max_input_time = 60
  memory_limit = 128M
  upload_max_filesize = 2M
  post_max_size = 8M
  
Session Settings
  session.save_path = /var/lib/php/sessions
  session.auto_start = Off
  session.name = PHPSESSID
  
Date Settings
  date.timezone = UTC
```

### Loaded Modules

```
Standard PHP Modules
  Core
  date
  pcre
  standard
  reflection
  spl
  
Database Extensions
  PDO
  pdo_mysql
  pdo_sqlite
  
Other Extensions
  gd
  json
  curl
  mbstring
```

---

## Output Formatting

### Default HTML Output

```php
<?php
// Default - HTML formatted table
phpinfo();

// Output will be HTML with styling
// Includes tables with gray headers
// Organized in sections
// Clickable collapsible sections
?>
```

### Plain Text Output

```php
<?php
// Get plain text output
// Useful for logging or API responses
ob_start();
phpinfo(INFO_ALL);
$output = ob_get_clean();

// Now $output contains the HTML
// To get truly plain text, use info_print_table()
// Or process the HTML to extract text
?>
```

### Custom Formatted Output

```php
<?php
function get_php_info() {
    // Get phpinfo as string
    ob_start();
    phpinfo(INFO_ALL);
    $info = ob_get_clean();
    
    // Extract values (simplified example)
    $version = phpversion();
    $extensions = get_loaded_extensions();
    $memory = ini_get('memory_limit');
    
    return [
        'version' => $version,
        'extensions' => $extensions,
        'memory_limit' => $memory
    ];
}

$info = get_php_info();
echo json_encode($info, JSON_PRETTY_PRINT);
?>
```

---

## Security Considerations

### Disable in Production

```php
<?php
// CRITICAL - Never expose phpinfo() to public
// Exposes sensitive information:
// - PHP version (attack vectors)
// - Loaded extensions
// - File paths
// - Environment variables
// - Server configuration

// WRONG - Exposed to public
phpinfo();

// RIGHT - Only for local debugging
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    http_response_code(403);
    exit('Access denied');
}
phpinfo();

// BETTER - Use environment variable
if (!getenv('DEBUG_MODE')) {
    http_response_code(404);
    exit('Not found');
}
phpinfo();
?>
```

### Remove from Production

```php
<?php
// Don't have phpinfo.php in production
// If found by attacker:
// - Reveals PHP version
// - Shows installed modules
// - Exposes file paths
// - Shows enabled functions
// - Reveals server information

// Solution: Use admin panel only
if (!is_admin_user()) {
    exit('Access denied');
}
phpinfo();
?>
```

---

## Accessing Specific Information

### Version Information

```php
<?php
// Get PHP version
echo phpversion();                    // 8.2.0
echo phpversion('json');              // Extension version

// Get detailed version
$version = phpversion();
$parts = explode('.', $version);
$major = $parts[0];                   // 8
$minor = $parts[1];                   // 2
$release = $parts[2];                 // 0

// Check minimum version
if (version_compare(phpversion(), '8.0.0', '<')) {
    exit('PHP 8.0.0 or higher required');
}

// Check extension version
if (!extension_loaded('pdo')) {
    exit('PDO extension required');
}
?>
```

### Configuration Settings

```php
<?php
// Get specific configuration values
$memory_limit = ini_get('memory_limit');     // 128M
$max_upload = ini_get('upload_max_filesize'); // 2M
$display_errors = ini_get('display_errors');  // 0
$timezone = ini_get('date.timezone');         // UTC

// Get all settings
$settings = ini_get_all();

// Check if function exists
if (function_exists('curl_init')) {
    echo "cURL is available";
}

// Get extension info
$extensions = get_extension_funcs('standard');
var_dump($extensions);
?>
```

### Loaded Extensions

```php
<?php
// Get all loaded extensions
$extensions = get_loaded_extensions();
print_r($extensions);
// Array (
//     [0] => Core
//     [1] => date
//     [2] => pcre
//     [3] => standard
//     ...
// )

// Check if extension is loaded
if (extension_loaded('curl')) {
    echo "cURL available";
}

// Get extension functions
$funcs = get_extension_funcs('gd');
print_r($funcs);
// Functions provided by GD extension
?>
```

### System Information

```php
<?php
// Current working directory
echo getcwd();              // /var/www/html

// PHP executable path
echo PHP_EXECUTABLE;         // /usr/bin/php

// Operating system
echo PHP_OS;                // Linux
echo PHP_OS_FAMILY;         // Linux

// Current PHP SAPI
echo php_sapi_name();       // cli (or fpm-fcgi, etc)

// Configuration file location
echo php_ini_loaded_file();  // /etc/php/8.2/cli/php.ini

// Default timezone
echo date_default_timezone_get(); // UTC
?>
```

---

## Server Information Arrays

### $_SERVER Array

```php
<?php
// Important $_SERVER values

// REQUEST INFORMATION
$_SERVER['REQUEST_METHOD'];      // GET, POST, PUT, DELETE
$_SERVER['REQUEST_URI'];         // /path?query=value
$_SERVER['REQUEST_TIME'];        // Timestamp
$_SERVER['QUERY_STRING'];        // query=value

// SERVER INFORMATION
$_SERVER['SERVER_NAME'];         // localhost
$_SERVER['SERVER_PORT'];         // 8000
$_SERVER['SERVER_PROTOCOL'];     // HTTP/1.1
$_SERVER['SERVER_SOFTWARE'];     // PHP 8.2 Development Server

// CLIENT INFORMATION
$_SERVER['REMOTE_ADDR'];         // 127.0.0.1
$_SERVER['REMOTE_PORT'];         // 54321
$_SERVER['HTTP_HOST'];           // localhost:8000

// HTTP HEADERS
$_SERVER['HTTP_USER_AGENT'];     // Mozilla/5.0...
$_SERVER['HTTP_ACCEPT'];         // text/html...
$_SERVER['HTTP_REFERER'];        // Previous page
$_SERVER['HTTP_COOKIE'];         // Cookie values

// HTTPS
$_SERVER['HTTPS'];               // on/off
$_SERVER['SSL_PROTOCOL'];        // TLSv1.2

// FILE INFORMATION
$_SERVER['SCRIPT_FILENAME'];     // /var/www/html/index.php
$_SERVER['SCRIPT_NAME'];         // /index.php
$_SERVER['PHP_SELF'];            // /index.php
?>
```

### Environment Variables

```php
<?php
// Access environment variables

// Using $_SERVER
echo $_SERVER['USER'];           // Current user
echo $_SERVER['PATH'];           // System PATH

// Using getenv()
echo getenv('USER');             // Current user
echo getenv('HOME');             // Home directory
echo getenv('PATH');             // System PATH

// Custom environment variables
echo getenv('APP_ENV');          // development
echo getenv('APP_DEBUG');        // true

// Set environment variable
putenv('APP_ENV=production');
echo getenv('APP_ENV');          // production

// Using .env file
// Don't use putenv() in production
// Use .env files with libraries like vlucas/phpdotenv
?>
```

---

## Version Information

### Display Version Information

```php
<?php
// Create version display function
function display_version_info() {
    return [
        'php_version' => phpversion(),
        'php_sapi' => php_sapi_name(),
        'os' => PHP_OS_FAMILY,
        'server_api' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        'extensions' => [
            'pdo' => extension_loaded('pdo'),
            'curl' => extension_loaded('curl'),
            'gd' => extension_loaded('gd'),
            'mbstring' => extension_loaded('mbstring'),
            'json' => extension_loaded('json'),
        ],
        'settings' => [
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ],
        'timezone' => date_default_timezone_get(),
    ];
}

$info = display_version_info();
echo json_encode($info, JSON_PRETTY_PRINT);
?>
```

---

## Complete Examples

### Admin Dashboard

```php
<?php
// admin/dashboard.php
session_start();

// Check if admin
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: /login');
    exit;
}

// Get server info
$info = [
    'version' => phpversion(),
    'sapi' => php_sapi_name(),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'extensions' => get_loaded_extensions(),
    'remote_addr' => $_SERVER['REMOTE_ADDR'],
    'server_software' => $_SERVER['SERVER_SOFTWARE'],
    'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; }
        .info-table { border-collapse: collapse; width: 100%; }
        .info-table th { background: #333; color: white; padding: 10px; }
        .info-table td { padding: 10px; border: 1px solid #ddd; }
        .info-table tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    
    <h2>Server Information</h2>
    <table class="info-table">
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>PHP Version</td>
            <td><?= $info['version'] ?></td>
        </tr>
        <tr>
            <td>SAPI</td>
            <td><?= $info['sapi'] ?></td>
        </tr>
        <tr>
            <td>Memory Limit</td>
            <td><?= $info['memory_limit'] ?></td>
        </tr>
        <tr>
            <td>Max Execution Time</td>
            <td><?= $info['max_execution_time'] ?> seconds</td>
        </tr>
        <tr>
            <td>Remote Address</td>
            <td><?= htmlspecialchars($info['remote_addr']) ?></td>
        </tr>
        <tr>
            <td>Server Software</td>
            <td><?= htmlspecialchars($info['server_software']) ?></td>
        </tr>
        <tr>
            <td>Request Time</td>
            <td><?= $info['request_time'] ?></td>
        </tr>
    </table>
    
    <h2>Loaded Extensions</h2>
    <ul>
        <?php foreach ($info['extensions'] as $ext): ?>
            <li><?= htmlspecialchars($ext) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
```

### API Endpoint

```php
<?php
// api/info.php
header('Content-Type: application/json');

// Only allow from localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Return server information
$info = [
    'status' => 'ok',
    'timestamp' => time(),
    'php' => [
        'version' => phpversion(),
        'sapi' => php_sapi_name(),
        'os' => PHP_OS_FAMILY,
    ],
    'limits' => [
        'memory' => ini_get('memory_limit'),
        'execution_time' => ini_get('max_execution_time'),
        'upload_size' => ini_get('upload_max_filesize'),
    ],
    'extensions' => array_values(get_loaded_extensions()),
    'loaded' => [
        'pdo' => extension_loaded('pdo'),
        'curl' => extension_loaded('curl'),
        'gd' => extension_loaded('gd'),
        'mbstring' => extension_loaded('mbstring'),
    ],
];

echo json_encode($info, JSON_PRETTY_PRINT);
?>
```

---

## See Also

- [Global Variables & $_SERVER](9-global-variable-server.md)
- [PHP Web Development](3-php-web.md)
- [Client & Server](2-client-server.md)
