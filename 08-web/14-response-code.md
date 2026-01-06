# HTTP Response Codes

## Overview

HTTP response codes (status codes) indicate the result of an HTTP request. They tell the client whether the request succeeded, failed, or needs further action. This chapter covers all standard HTTP status codes and when to use them.

---

## Table of Contents

1. Response Code Structure
2. 1xx Informational Codes
3. 2xx Success Codes
4. 3xx Redirection Codes
5. 4xx Client Error Codes
6. 5xx Server Error Codes
7. Setting Response Codes in PHP
8. Complete Examples

---

## Response Code Structure

### Format

```
HTTP/1.1 200 OK
         ↑   ↑
        code reason phrase

Code:    3-digit number (100-599)
Reason:  Human-readable description
```

### Categories

```
1xx (100-199)  - Informational
2xx (200-299)  - Success
3xx (300-399)  - Redirection
4xx (400-499)  - Client Error
5xx (500-599)  - Server Error
```

---

## 1xx Informational Codes

### Rarely Used

```
100 Continue        - Client can continue sending request body
101 Switching       - Upgrading protocol (WebSocket)
102 Processing      - Request is processing (WebDAV)
103 Early Hints     - Provide hints before response
```

### When to Use

```php
<?php
// Generally not used in PHP web applications
// Used primarily for protocol upgrades and large uploads
?>
```

---

## 2xx Success Codes

### 200 OK

```php
<?php
// Standard success response
// Used by default if not specified

http_response_code(200);
echo "Request successful";

// Equivalent to:
header('HTTP/1.1 200 OK');
?>
```

### 201 Created

```php
<?php
// New resource created

http_response_code(201);
header('Location: /resource/123');

$response = [
    'status' => 'created',
    'id' => 123,
    'location' => '/resource/123'
];
echo json_encode($response);
?>
```

### 202 Accepted

```php
<?php
// Request accepted but not yet processed
// Used for async operations

http_response_code(202);

$response = [
    'status' => 'accepted',
    'job_id' => 'job123',
    'check_status_at' => '/jobs/job123'
];
echo json_encode($response);
?>
```

### 204 No Content

```php
<?php
// Successful request with no content to return
// Common for DELETE requests

http_response_code(204);
// No body sent

// Or with body (less common)
header('Content-Type: application/json');
http_response_code(200);
echo json_encode(['status' => 'deleted']);
?>
```

### 206 Partial Content

```php
<?php
// Part of resource sent (range request)
// Used for large file downloads

$file_size = 1000000;
$start = 0;
$end = $file_size - 1;

if (isset($_SERVER['HTTP_RANGE'])) {
    preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches);
    $start = intval($matches[1]);
    $end = $matches[2] !== '' ? intval($matches[2]) : $file_size - 1;
}

http_response_code(206);
header('Content-Range: bytes ' . $start . '-' . $end . '/' . $file_size);
?>
```

---

## 3xx Redirection Codes

### 301 Moved Permanently

```php
<?php
// Permanent redirect
// Browsers cache and search engines update links

http_response_code(301);
header('Location: /new-path');
exit;

// Use for:
// - Site restructuring
// - Domain migration
// - URL canonicalization
?>
```

### 302 Found (Temporary Redirect)

```php
<?php
// Temporary redirect
// Most common redirect

http_response_code(302);
header('Location: /temporary-path');
exit;

// Or use helper
header('Location: /page');  // Defaults to 302
exit;

// Use for:
// - Login redirects
// - Temporary URL changes
// - A/B testing
?>
```

### 303 See Other

```php
<?php
// Redirect to different URL
// Always use GET for the new request

http_response_code(303);
header('Location: /result-page');
exit;

// Use for:
// - POST-Redirect-GET pattern
// - Form submissions
?>
```

### 304 Not Modified

```php
<?php
// Resource hasn't changed
// Browser can use cached version

$etag = md5(file_get_contents('data.json'));

if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    if ($_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        http_response_code(304);
        exit;
    }
}

http_response_code(200);
header('ETag: "' . $etag . '"');
echo file_get_contents('data.json');
?>
```

### 307 Temporary Redirect

```php
<?php
// Like 302 but guarantees method preservation
// POST remains POST

http_response_code(307);
header('Location: /new-path');
exit;

// Use for:
// - Method-preserving temporary redirects
?>
```

### 308 Permanent Redirect

```php
<?php
// Like 301 but guarantees method preservation
// POST remains POST

http_response_code(308);
header('Location: /permanent-new-path');
exit;

// Use for:
// - Method-preserving permanent redirects
?>
```

---

## 4xx Client Error Codes

### 400 Bad Request

```php
<?php
// Malformed request syntax

if (empty($_POST['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email parameter required']);
    exit;
}

// Use for:
// - Invalid request format
// - Missing required parameters
// - Invalid parameter values
?>
```

### 401 Unauthorized

```php
<?php
// Missing or invalid authentication

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('WWW-Authenticate: Bearer realm="Application"');
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

// Use for:
// - Missing authentication
// - Invalid credentials
// - Expired token
?>
```

### 403 Forbidden

```php
<?php
// Authenticated but not authorized

session_start();

if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Permission denied']);
    exit;
}

// Use for:
// - Insufficient permissions
// - Forbidden resource
// - Access denied
?>
```

### 404 Not Found

```php
<?php
// Resource doesn't exist

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(404);
    echo 'Page not found';
    exit;
}

$db = new PDO('sqlite:db.sqlite');
$stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    echo 'Post not found';
    exit;
}

// Use for:
// - Resource doesn't exist
// - URL not found
// - Invalid ID
?>
```

### 409 Conflict

```php
<?php
// Request conflicts with current state
// Often used with PUT for version conflicts

$id = $_POST['id'];
$version = $_POST['version'];

// Check version
if ($version != $current_version) {
    http_response_code(409);
    echo json_encode(['error' => 'Resource version conflict']);
    exit;
}

// Use for:
// - Duplicate entries
// - Version conflicts
// - State conflicts
?>
```

### 410 Gone

```php
<?php
// Resource permanently deleted
// Difference from 404: 410 means it WAS there

http_response_code(410);
echo 'Resource no longer available';
exit;

// Use for:
// - Permanently deleted resources
// - Deprecated endpoints
?>
```

### 422 Unprocessable Entity

```php
<?php
// Request is well-formed but contains semantic errors
// Common in APIs

$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode([
        'error' => 'Validation failed',
        'details' => ['email' => 'Invalid email format']
    ]);
    exit;
}

// Use for:
// - Validation errors
// - Semantic errors
// - Invalid field values
?>
```

### 429 Too Many Requests

```php
<?php
// Rate limiting

$ip = $_SERVER['REMOTE_ADDR'];
$cache_key = "rate_limit_" . $ip;

if (check_rate_limit($ip)) {
    http_response_code(429);
    header('Retry-After: 60');
    echo json_encode(['error' => 'Too many requests. Try again later.']);
    exit;
}

// Use for:
// - Rate limiting
// - Throttling
?>
```

---

## 5xx Server Error Codes

### 500 Internal Server Error

```php
<?php
// Unexpected server error

try {
    $result = risky_operation();
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo 'An error occurred';
    exit;
}

// Use for:
// - Uncaught exceptions
// - Server errors
// - Database errors (don't expose details!)
?>
```

### 501 Not Implemented

```php
<?php
// Method not implemented

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    http_response_code(501);
    echo 'PATCH method not implemented';
    exit;
}

// Use for:
// - Unimplemented features
// - Unsupported methods
?>
```

### 502 Bad Gateway

```php
<?php
// Invalid response from upstream server
// Usually from proxy/load balancer, not PHP

// Rare in PHP code itself
// But can happen in curl calls

$ch = curl_init('http://upstream.example.com');
$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo 'Bad gateway';
    exit;
}
?>
```

### 503 Service Unavailable

```php
<?php
// Server temporarily unavailable (maintenance, overloaded)

if ($in_maintenance_mode) {
    http_response_code(503);
    header('Retry-After: 3600');
    echo 'Service temporarily unavailable';
    exit;
}

// Use for:
// - Maintenance mode
// - Server overload
// - Temporary unavailability
?>
```

---

## Setting Response Codes in PHP

### http_response_code()

```php
<?php
// Set response code
http_response_code(200);

// Get current code
$code = http_response_code();
echo $code;  // 200

// Common usage
http_response_code(404);
echo 'Not found';
?>
```

### header()

```php
<?php
// Alternative way using header()
header('HTTP/1.1 200 OK');

// Various formats
header('HTTP/1.1 404 Not Found');
header('HTTP/1.0 500 Internal Server Error');
header('Status: 200 OK');  // For CGI

// Modern approach (PHP 5.4+):
http_response_code(200);
?>
```

---

## Complete Examples

### Error Handler

```php
<?php
// Custom error handler with appropriate status codes

function handle_error($code, $message) {
    switch ($code) {
        case 'not_found':
            http_response_code(404);
            $response = ['error' => 'Resource not found'];
            break;
            
        case 'unauthorized':
            http_response_code(401);
            $response = ['error' => 'Authentication required'];
            break;
            
        case 'forbidden':
            http_response_code(403);
            $response = ['error' => 'Permission denied'];
            break;
            
        case 'validation_error':
            http_response_code(422);
            $response = ['error' => 'Validation failed', 'details' => $message];
            break;
            
        case 'server_error':
            http_response_code(500);
            $response = ['error' => 'Internal server error'];
            break;
            
        default:
            http_response_code(400);
            $response = ['error' => 'Bad request'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Usage
if (!$user) {
    handle_error('not_found', null);
}
?>
```

### API Responses

```php
<?php
// Consistent API response format

function api_response($data = null, $code = 200, $message = 'OK') {
    http_response_code($code);
    header('Content-Type: application/json');
    
    echo json_encode([
        'status' => ($code >= 200 && $code < 300) ? 'success' : 'error',
        'code' => $code,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Usage
if (!$user) {
    api_response(null, 404, 'User not found');
}

$users = get_users();
api_response(['users' => $users], 200, 'Users retrieved');

// Validation errors
api_response(['errors' => $validation_errors], 422, 'Validation failed');

// Created
api_response(['id' => $user_id], 201, 'User created');
?>
```

### Conditional Responses

```php
<?php
// Return different codes based on conditions

$resource = get_resource($_GET['id']);

if (!$resource) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

if (!user_can_access($resource)) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

if (!is_valid($resource)) {
    http_response_code(422);
    echo 'Invalid resource';
    exit;
}

http_response_code(200);
echo json_encode($resource);
?>
```

---

## Quick Reference Table

| Code | Reason | Use Case |
|------|--------|----------|
| 200 | OK | Successful request |
| 201 | Created | Resource created |
| 204 | No Content | Success with no body |
| 301 | Moved Permanently | Permanent redirect |
| 302 | Found | Temporary redirect |
| 304 | Not Modified | Cached version valid |
| 400 | Bad Request | Invalid request |
| 401 | Unauthorized | Missing authentication |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable | Validation error |
| 429 | Too Many Requests | Rate limited |
| 500 | Server Error | Unexpected error |
| 503 | Unavailable | Maintenance/overload |

---

## See Also

- [HTTP Headers](13-header.md)
- [REST API Design](10-query-parameter.md)
- [Error Handling](9-global-variable-server.md)
