# HTTP Headers

## Overview

HTTP headers are key-value pairs sent with HTTP requests and responses. They contain metadata about the communication, control caching, authentication, content type, and more. This chapter covers how to work with headers in PHP.

---

## Table of Contents

1. Header Basics
2. Response Headers
3. Request Headers
4. Custom Headers
5. Header Functions
6. Cache Control
7. Security Headers
8. Complete Examples

---

## Header Basics

### Request vs Response Headers

```
HTTP Request
  GET /page.php HTTP/1.1
  Host: example.com
  User-Agent: Mozilla/5.0
  Accept: text/html
  Cookie: session=abc123

        ↓

HTTP Response
  HTTP/1.1 200 OK
  Content-Type: text/html
  Content-Length: 1234
  Set-Cookie: token=xyz789
  Cache-Control: max-age=3600
  [blank line]
  [response body]
```

### Common Headers

```
Request Headers:
  Host                - Server hostname
  User-Agent          - Client browser/app
  Accept              - Accepted content types
  Accept-Language     - Preferred languages
  Authorization       - Authentication credentials
  Cookie              - Cookie values
  Referer             - Previous page URL
  Content-Type        - Request body format
  Content-Length      - Request body size

Response Headers:
  Content-Type        - Response body format
  Content-Length      - Response body size
  Set-Cookie          - Set cookie values
  Cache-Control       - Caching instructions
  Expires             - Expiration time
  Last-Modified       - Last modification time
  ETag                - Entity tag for caching
  Location            - Redirect destination
  WWW-Authenticate    - Authentication challenge
  Server              - Server software
  Access-Control-*    - CORS headers
```

---

## Response Headers

### Setting Headers

```php
<?php
// MUST be called before any output
// No whitespace before <?php

// Set content type
header('Content-Type: text/html; charset=UTF-8');

// Set response code
header('HTTP/1.1 200 OK');

// Redirect
header('Location: /new-page.php');

// Custom header
header('X-Custom-Header: value');

// Multiple headers
header('Content-Type: application/json');
header('Cache-Control: no-cache');
?>
```

### Checking if Headers Sent

```php
<?php
// Check if headers already sent
if (headers_sent()) {
    echo 'Cannot set headers - already sent!';
    exit;
}

// Get headers sent info
$filename = '';
$linenum = 0;
if (headers_sent($filename, $linenum)) {
    echo "Headers sent in $filename on line $linenum";
}

// Set header safely
if (!headers_sent()) {
    header('Content-Type: application/json');
}
?>
```

### HTTP Status Codes

```php
<?php
// Using http_response_code()

// Success
http_response_code(200);  // OK
http_response_code(201);  // Created
http_response_code(204);  // No Content

// Redirect
http_response_code(301);  // Moved Permanently
http_response_code(302);  // Found (temporary)
http_response_code(304);  // Not Modified

// Client Error
http_response_code(400);  // Bad Request
http_response_code(401);  // Unauthorized
http_response_code(403);  // Forbidden
http_response_code(404);  // Not Found
http_response_code(409);  // Conflict

// Server Error
http_response_code(500);  // Internal Server Error
http_response_code(502);  // Bad Gateway
http_response_code(503);  // Service Unavailable

// Get current code
$code = http_response_code();
echo $code;  // 200
?>
```

### Content-Type

```php
<?php
// HTML
header('Content-Type: text/html; charset=UTF-8');

// JSON
header('Content-Type: application/json; charset=UTF-8');

// Plain text
header('Content-Type: text/plain; charset=UTF-8');

// XML
header('Content-Type: application/xml; charset=UTF-8');

// PDF
header('Content-Type: application/pdf');

// Image
header('Content-Type: image/jpeg');
header('Content-Type: image/png');

// CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="data.csv"');

// Download file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="file.zip"');
?>
```

---

## Request Headers

### Reading Request Headers

```php
<?php
// Request headers are in $_SERVER with HTTP_ prefix

// User-Agent
echo $_SERVER['HTTP_USER_AGENT'];

// Accept
echo $_SERVER['HTTP_ACCEPT'];

// Referer
echo $_SERVER['HTTP_REFERER'];

// Authorization
echo $_SERVER['HTTP_AUTHORIZATION'];

// Custom headers
echo $_SERVER['HTTP_X_CUSTOM'];

// Get all headers
function get_all_headers() {
    $headers = [];
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $header = str_replace('HTTP_', '', $key);
            $headers[$header] = $value;
        }
    }
    return $headers;
}

$headers = get_all_headers();
print_r($headers);
?>
```

### getallheaders()

```php
<?php
// Get all request headers (may not be available in all environments)

if (function_exists('getallheaders')) {
    $headers = getallheaders();
    
    foreach ($headers as $name => $value) {
        echo $name . ': ' . $value . "\n";
    }
}

// Example output:
// Host: example.com
// User-Agent: Mozilla/5.0
// Accept: text/html
?>
```

---

## Custom Headers

### Sending Custom Headers

```php
<?php
// Send custom header (convention: X- prefix, now deprecated but still used)
header('X-Application-Version: 1.0');
header('X-API-Key: required');

// Modern: use standard names without X-
header('Authorization: Bearer token123');
header('API-Version: 2.0');

// Multiple headers
header('X-Powered-By: PHP 8.2');
header('X-Custom-Header: custom-value');
header('X-Robots-Tag: noindex');
?>
```

### Reading Custom Headers

```php
<?php
// Read custom header sent by client
$api_key = $_SERVER['HTTP_API_KEY'] ?? null;
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

// Validate
if (!$api_key || $api_key !== 'secret123') {
    http_response_code(401);
    exit('Unauthorized');
}

echo 'Access granted';
?>
```

---

## Header Functions

### header_remove()

```php
<?php
// Remove a header (if not yet sent)
header('X-Powered-By: PHP');
header_remove('X-Powered-By');  // Removed

// Remove all headers (including status)
header_remove();
?>
```

### setrawcookie()

```php
<?php
// Set cookie via Set-Cookie header
setcookie('name', 'value', [
    'expires' => time() + 3600,
    'path' => '/',
    'domain' => 'example.com',
    'secure' => true,
    'httponly' => true,
]);

// setrawcookie() doesn't encode value
setrawcookie('raw', 'some;raw;value');
?>
```

---

## Cache Control

### Cache Headers

```php
<?php
// No caching
header('Cache-Control: no-cache, no-store, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Cache for 1 hour
header('Cache-Control: public, max-age=3600');

// Cache for 1 day
header('Cache-Control: public, max-age=86400');

// Private cache (browser only, not proxy)
header('Cache-Control: private, max-age=3600');

// Last-Modified for conditional requests
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

// ETag for content verification
$content = 'page content';
$etag = md5($content);
header('ETag: "' . $etag . '"');

// Check If-None-Match
if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    if ($_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        http_response_code(304);  // Not Modified
        exit;
    }
}
?>
```

---

## Security Headers

### HSTS (HTTP Strict Transport Security)

```php
<?php
// Force HTTPS for 1 year
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
?>
```

### X-Frame-Options

```php
<?php
// Prevent clickjacking
header('X-Frame-Options: DENY');           // Don't allow framing
header('X-Frame-Options: SAMEORIGIN');     // Only same site

// Or use CSP
header('Content-Security-Policy: frame-ancestors \'none\';');
?>
```

### X-Content-Type-Options

```php
<?php
// Prevent MIME type sniffing
header('X-Content-Type-Options: nosniff');
?>
```

### Referrer-Policy

```php
<?php
// Control referrer information
header('Referrer-Policy: no-referrer');           // Never send referrer
header('Referrer-Policy: same-origin');           // Only same domain
header('Referrer-Policy: strict-origin-when-cross-origin');
?>
```

### Content Security Policy

```php
<?php
header("Content-Security-Policy: default-src 'self'; script-src 'self' cdn.example.com; style-src 'self' 'unsafe-inline'");
?>
```

### Permission Policy

```php
<?php
// Control browser features
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
?>
```

---

## Complete Examples

### Secure Response Headers

```php
<?php
// Set all recommended security headers

function set_security_headers() {
    // HTTPS enforcement
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    
    // Prevent MIME sniffing
    header('X-Content-Type-Options: nosniff');
    
    // XSS protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer control
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // CSP
    header("Content-Security-Policy: default-src 'self'; script-src 'self' cdn.example.com; style-src 'self' 'unsafe-inline'");
    
    // Remove server info
    header_remove('Server');
    header('Server: Protected');
}

set_security_headers();
?>
```

### JSON API Response

```php
<?php
// JSON API response with headers

header('Content-Type: application/json; charset=UTF-8');
http_response_code(200);

$response = [
    'status' => 'success',
    'data' => [
        'id' => 1,
        'name' => 'John',
        'email' => 'john@example.com'
    ]
];

echo json_encode($response);
?>
```

### Conditional Response

```php
<?php
// Return 304 Not Modified if content hasn't changed

$content = file_get_contents('data.json');
$etag = md5($content);

header('Content-Type: application/json');
header('ETag: "' . $etag . '"');
header('Cache-Control: public, max-age=3600');

// Check if client has same version
if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    if ($_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        http_response_code(304);  // Not Modified
        exit;
    }
}

http_response_code(200);
echo $content;
?>
```

### Download File

```php
<?php
// Send file as download

$file = '/path/to/document.pdf';

if (!file_exists($file)) {
    http_response_code(404);
    exit('File not found');
}

$filename = basename($file);
$filesize = filesize($file);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . $filesize);
header('Cache-Control: no-cache, no-store');

readfile($file);
?>
```

### Redirect

```php
<?php
// Redirect with appropriate header

// Temporary redirect (most common)
header('Location: /new-page', false, 302);
exit;

// Permanent redirect
header('Location: /new-page', false, 301);
exit;

// With query string
$redirect_url = 'https://example.com/thank-you?id=' . $_GET['id'];
header('Location: ' . $redirect_url);
exit;
?>
```

---

## Key Takeaways

**Remember:**

1. ✅ Headers must be set before output
2. ✅ Check `headers_sent()` before setting
3. ✅ Set `Content-Type` for correct rendering
4. ✅ Use appropriate HTTP status codes
5. ✅ Set security headers
6. ✅ Use HTTPS with HSTS
7. ✅ Control caching appropriately
8. ✅ Set `X-Frame-Options` to prevent clickjacking
9. ✅ Implement CSP for XSS protection
10. ✅ Never expose server information

---

## See Also

- [HTTP Status Codes](14-response-code.md)
- [Response Codes in Detail](14-response-code.md)
- [Cookies](16-cookie.md)
- [XSS Prevention](11-xss-cross-site-scripting.md)
