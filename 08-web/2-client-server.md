# Client & Server Architecture

## Overview

The client-server model is the foundation of web applications. Understanding how clients and servers communicate is essential for developing effective web applications with PHP.

---

## Table of Contents

1. Client-Server Model Basics
2. HTTP Protocol
3. Request-Response Cycle
4. Client Responsibilities
5. Server Responsibilities
6. Stateless Communication
7. Connection Types
8. Complete Examples

---

## Client-Server Model Basics

### What is Client-Server?

```
        Client                          Server
    ┌─────────────┐               ┌──────────────┐
    │  Browser    │               │ PHP Server   │
    │             │               │              │
    │ - HTML      │──Request──>   │ - Process    │
    │ - CSS       │               │ - Database   │
    │ - JavaScript│<──Response──  │ - Generate   │
    └─────────────┘               └──────────────┘
```

### Key Characteristics

```php
<?php
// Client-Server is a two-tier architecture:

// CLIENT SIDE (Browser)
// - Sends HTTP requests
// - Receives and displays responses
// - Executes JavaScript
// - Stores data temporarily (cookies, localStorage)
// - Does NOT execute PHP code

// SERVER SIDE (PHP)
// - Receives HTTP requests
// - Processes requests
// - Queries databases
// - Generates responses
// - Sends responses back to client
?>
```

---

## HTTP Protocol

### HTTP Methods

```php
<?php
// GET - Retrieve data
// Used for: Fetching pages, searching, filtering
// URL: http://example.com/users?page=1&sort=name
// Safe: Yes (doesn't modify data)

// POST - Submit data
// Used for: Form submissions, creating records
// Data: Sent in request body
// Safe: No (may modify data)

// PUT - Update resource
// Used for: Updating entire resources
// Data: Sent in request body
// Safe: No (modifies data)

// DELETE - Delete resource
// Used for: Removing records
// Safe: No (deletes data)

// HEAD - Like GET but no body
// Used for: Checking if resource exists
// Safe: Yes

// PATCH - Partial update
// Used for: Updating part of resource
// Safe: No (modifies data)

// OPTIONS - Describe options
// Used for: CORS, discovering methods
// Safe: Yes
?>
```

### HTTP Status Codes

```php
<?php
// 1xx Informational
// 100 Continue
// 101 Switching Protocols

// 2xx Success
// 200 OK - Request successful
// 201 Created - Resource created
// 204 No Content - Success but no content
// 206 Partial Content - Partial response

// 3xx Redirection
// 301 Moved Permanently
// 302 Found (Temporary Redirect)
// 304 Not Modified - Use cached version
// 307 Temporary Redirect

// 4xx Client Error
// 400 Bad Request - Invalid request
// 401 Unauthorized - Need authentication
// 403 Forbidden - Access denied
// 404 Not Found - Resource doesn't exist
// 405 Method Not Allowed
// 409 Conflict - Conflict with current state
// 422 Unprocessable Entity - Invalid data

// 5xx Server Error
// 500 Internal Server Error - General error
// 501 Not Implemented
// 502 Bad Gateway
// 503 Service Unavailable
// 504 Gateway Timeout
?>
```

### HTTP Headers

```php
<?php
// REQUEST HEADERS
// Host: example.com
// User-Agent: Mozilla/5.0...
// Accept: text/html, application/json
// Content-Type: application/json
// Authorization: Bearer token123
// Cookie: session_id=abc123

// RESPONSE HEADERS
// Content-Type: text/html; charset=UTF-8
// Content-Length: 1234
// Set-Cookie: session_id=xyz789; Path=/
// Cache-Control: no-cache, must-revalidate
// Expires: Wed, 21 Oct 2025 07:28:00 GMT
// Location: /new-location
// Access-Control-Allow-Origin: *
?>
```

---

## Request-Response Cycle

### Complete Flow

```php
<?php
// STEP 1: Client sends REQUEST
$_SERVER['REQUEST_METHOD'];  // GET, POST, etc.
$_SERVER['REQUEST_URI'];     // /users/123
$_SERVER['HTTP_HOST'];       // example.com
$_GET;                       // Query parameters
$_POST;                      // POST data
$_COOKIE;                    // Cookies
$_SESSION;                   // Session data
$_FILES;                     // Uploaded files

// STEP 2: Server processes REQUEST
// - Route to appropriate handler
// - Process data
// - Query database
// - Perform operations

// STEP 3: Server sends RESPONSE
http_response_code(200);     // Status code
header('Content-Type: application/json');  // Headers
echo json_encode($data);     // Body
?>
```

### Request Headers

```php
<?php
// Access request headers
echo $_SERVER['REQUEST_METHOD'];      // GET, POST, etc.
echo $_SERVER['REQUEST_URI'];         // /index.php?page=1
echo $_SERVER['SCRIPT_NAME'];         // /index.php
echo $_SERVER['HTTP_HOST'];           // example.com
echo $_SERVER['HTTP_REFERER'] ?? '';  // Previous page
echo $_SERVER['HTTP_USER_AGENT'];     // Browser info

// Custom headers
$headers = getallheaders();
echo $headers['Authorization'] ?? '';
echo $headers['X-Requested-With'] ?? '';
?>
```

### Response Headers

```php
<?php
// Set response headers
header('HTTP/1.1 200 OK');
header('Content-Type: text/html; charset=UTF-8');
header('Content-Length: 1234');
header('Cache-Control: public, max-age=3600');
header('Set-Cookie: user_id=123; Path=/; HttpOnly');
header('X-Custom-Header: value');

// Redirect
header('Location: /new-page');
exit;

// Download file
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="document.pdf"');
?>
```

---

## Client Responsibilities

### Making Requests

```php
<?php
// CLIENT SIDE - The browser makes requests

// 1. User navigates to URL
// Browser -> GET http://example.com/

// 2. User submits form
// Browser -> POST http://example.com/register

// 3. JavaScript AJAX call
// Browser -> GET http://example.com/api/users

// 4. User clicks link
// Browser -> GET http://example.com/about
?>
```

### Processing Responses

```html
<!-- CLIENT SIDE - Browser processes HTML responses -->
<!DOCTYPE html>
<html>
<head>
    <title>Page Title</title>
    <!-- CSS styling -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Display content -->
    <h1>Welcome</h1>
    
    <!-- JavaScript for interactivity -->
    <script>
        // Make requests
        fetch('/api/data')
            .then(response => response.json())
            .then(data => console.log(data));
        
        // Handle events
        document.querySelector('button').addEventListener('click', function() {
            // Handle click
        });
    </script>
</body>
</html>
```

### Request Examples

```javascript
// GET Request
fetch('/users')
    .then(response => response.json())
    .then(data => console.log(data));

// POST Request
fetch('/users', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        name: 'John',
        email: 'john@example.com'
    })
})
.then(response => response.json())
.then(data => console.log(data));

// With Authorization
fetch('/api/protected', {
    method: 'GET',
    headers: {
        'Authorization': 'Bearer token123'
    }
})
.then(response => response.json());
```

---

## Server Responsibilities

### Receiving Requests

```php
<?php
// SERVER SIDE - PHP receives and processes requests

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get request path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Get query parameters
$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';

// Get POST data
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;

// Get JSON body
$input = json_decode(file_get_contents('php://input'), true);

// Get headers
$auth = getallheaders()['Authorization'] ?? '';
?>
```

### Processing Requests

```php
<?php
// SERVER SIDE - Process and generate response

// 1. Validate input
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Invalid email');
}

// 2. Check authentication
if (empty($auth)) {
    http_response_code(401);
    exit('Unauthorized');
}

// 3. Query database
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// 4. Perform operations
if ($user) {
    // User exists
} else {
    // User doesn't exist
}

// 5. Generate response
echo json_encode([
    'success' => true,
    'data' => $user,
]);
?>
```

### Sending Responses

```php
<?php
// SERVER SIDE - Send response back to client

// HTML Response
header('Content-Type: text/html');
echo '<html>...</html>';

// JSON Response
header('Content-Type: application/json');
echo json_encode(['success' => true]);

// Redirect
http_response_code(302);
header('Location: /new-location');
exit;

// Error Response
http_response_code(404);
echo 'Page not found';

// File Download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="document.pdf"');
readfile('document.pdf');
?>
```

---

## Stateless Communication

### Understanding Statelessness

```php
<?php
// HTTP is STATELESS - Server doesn't remember previous requests
// Each request is independent

// Request 1: User logs in
// POST /login with email=john@example.com, password=123
// Server validates and returns response

// Request 2: User views profile
// GET /profile
// Server doesn't remember the login!
// How does it know who the user is?

// Solution: Use sessions or tokens
?>
```

### Maintaining State with Sessions

```php
<?php
// SERVER SIDE - Use sessions to maintain state

session_start();

// Login: Store user in session
$_SESSION['user_id'] = 123;
$_SESSION['user_name'] = 'John';

// Later request: Access session data
if (isset($_SESSION['user_id'])) {
    echo "Logged in as " . $_SESSION['user_name'];
} else {
    echo "Please log in";
}

// Logout: Clear session
session_destroy();
?>
```

### Maintaining State with Tokens

```php
<?php
// SERVER SIDE - Use tokens (JWT) for stateless auth

// Generate token on login
$token = jwt_encode(['user_id' => 123], 'secret_key');
// Send token to client

// CLIENT SIDE - Send token with requests
// Authorization: Bearer eyJhbGc...

// SERVER SIDE - Verify token
$token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? '');
$payload = jwt_decode($token, 'secret_key');
$userId = $payload['user_id'];
?>
```

---

## Connection Types

### HTTP (Stateless)

```php
<?php
// Standard HTTP
// - Stateless
// - Request-response model
// - Each connection closes
// - Simple, reliable

// Browser initiates connection
// Server responds
// Connection closes

// Next request creates new connection
?>
```

### Keep-Alive (Persistent)

```php
<?php
// HTTP Keep-Alive
// - Connection persists for multiple requests
// - Reduces overhead
// - Improves performance

// Set in headers
header('Connection: keep-alive');
header('Keep-Alive: timeout=5, max=100');

// Browser keeps connection open
// Multiple requests use same connection
// Connection closes after timeout or max requests
?>
```

### WebSockets (Bidirectional)

```php
<?php
// WebSockets
// - Persistent connection
// - Server can send data to client anytime
// - Real-time communication
// - Used for chat, notifications, live data

// JavaScript (Client)
// const ws = new WebSocket('ws://example.com:8080');
// ws.onmessage = (event) => {
//     console.log('Server sent:', event.data);
// };
// ws.send('Hello Server');

// Note: Requires WebSocket server, not standard PHP
?>
```

---

## Complete Example

### Simple Web Application Flow

```php
<?php
// index.php - Main entry point
session_start();

// Route request
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/') {
    showHomePage();
} elseif ($path === '/login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handleLogin();
    } else {
        showLoginForm();
    }
} elseif ($path === '/dashboard') {
    requireLogin();
    showDashboard();
} else {
    http_response_code(404);
    echo 'Page not found';
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}

function showHomePage() {
    header('Content-Type: text/html');
    echo '<h1>Welcome</h1>';
}

function showLoginForm() {
    header('Content-Type: text/html');
    echo '
        <form method="POST" action="/login">
            <input name="email" type="email" required>
            <input name="password" type="password" required>
            <button type="submit">Login</button>
        </form>
    ';
}

function handleLogin() {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    
    // Validate
    if (!$email || !$password) {
        http_response_code(400);
        echo 'Missing credentials';
        return;
    }
    
    // Authenticate (simplified)
    if ($email === 'user@example.com' && $password === 'password') {
        $_SESSION['user_id'] = 1;
        $_SESSION['email'] = $email;
        header('Location: /dashboard');
    } else {
        http_response_code(401);
        echo 'Invalid credentials';
    }
}

function showDashboard() {
    header('Content-Type: text/html');
    echo '<h1>Dashboard</h1>';
    echo 'Welcome ' . htmlspecialchars($_SESSION['email']);
}
?>
```

---

## Key Takeaways

1. **Client initiates**: Browsers send requests
2. **Server responds**: PHP processes and sends responses
3. **Stateless by default**: Each request is independent
4. **Sessions/Tokens**: Maintain state across requests
5. **Headers critical**: Control caching, security, content type
6. **Error codes matter**: Indicate request success or failure
7. **Always validate**: Never trust client input

---

## See Also

- [PHP Web Development](3-php-web.md)
- [Global Variables](9-global-variable-server.md)
- [HTTP Headers](13-header.md)
- [Response Codes](14-response-code.md)
