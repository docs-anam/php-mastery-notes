# Global Variables and Superglobals

## Overview

PHP provides special predefined variables called superglobals that are automatically available in all scopes. These variables contain information about the server, client, requests, and environment. Understanding superglobals is essential for web development.

---

## Table of Contents

1. What are Superglobals
2. $_GET
3. $_POST
4. $_REQUEST
5. $_SERVER
6. $_COOKIE
7. $_SESSION
8. $_FILES
9. $_ENV
10. $GLOBALS
11. Complete Examples

---

## What are Superglobals

### Available Superglobals

```php
<?php
// Superglobals are automatically available everywhere
// No need to declare global

// $_GET          - URL query parameters
// $_POST         - Form POST data
// $_REQUEST      - Combined GET, POST, COOKIE
// $_SERVER       - Server and environment info
// $_COOKIE       - Cookie values
// $_SESSION      - Session data
// $_FILES        - File upload information
// $_ENV          - Environment variables
// $GLOBALS       - All global variables reference
?>
```

### Scope Availability

```php
<?php
// Superglobals are available in all scopes without 'global' keyword

function test_globals() {
    // Can access $_GET, $_POST, etc. directly
    echo $_GET['id'] ?? 'No ID';
    echo $_POST['name'] ?? 'No name';
}

class MyClass {
    public function test() {
        // Superglobals available in methods too
        echo $_SERVER['REQUEST_METHOD'];
    }
}

test_globals();
$obj = new MyClass();
$obj->test();
?>
```

---

## $_GET

### Query Parameters

```php
<?php
// URL: /page.php?id=123&name=John

// Accessing $_GET
echo $_GET['id'];      // 123
echo $_GET['name'];    // John
echo $_GET['age'] ?? 'Not set'; // Not set

// Check if parameter exists
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

// Alternative: null coalescing
$id = $_GET['id'] ?? null;
?>
```

### Validating GET Parameters

```php
<?php
// ALWAYS validate and sanitize GET data

// Get ID parameter
$id = $_GET['id'] ?? null;

// Validate
if ($id === null) {
    http_response_code(400);
    exit('ID parameter required');
}

if (!is_numeric($id)) {
    http_response_code(400);
    exit('ID must be numeric');
}

$id = (int) $id;

// Now safe to use
echo "User ID: " . $id;
?>
```

### Multiple Values

```php
<?php
// URL: /products?category=electronics&category=books

// $_GET contains array
// $_GET['category'] = ['electronics', 'books']

$categories = $_GET['category'] ?? [];
if (!is_array($categories)) {
    $categories = [$categories];
}

foreach ($categories as $cat) {
    echo htmlspecialchars($cat) . "\n";
}
?>
```

---

## $_POST

### Form Data

```php
<?php
// Posted from HTML form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Process
    echo "Name: " . htmlspecialchars($name);
}
?>

<!-- HTML Form -->
<form method="POST">
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <textarea name="message"></textarea>
    <button type="submit">Submit</button>
</form>
```

### Validating POST Data

```php
<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // Validate
    if (empty($name)) {
        $errors['name'] = 'Name required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email';
    }
    
    // Process if no errors
    if (empty($errors)) {
        // Save to database, send email, etc.
        header('Location: /success');
        exit;
    }
}
?>

<form method="POST">
    <div>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        <?php if (isset($errors['name'])): ?>
            <span class="error"><?= htmlspecialchars($errors['name']) ?></span>
        <?php endif; ?>
    </div>
    
    <div>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <?php if (isset($errors['email'])): ?>
            <span class="error"><?= htmlspecialchars($errors['email']) ?></span>
        <?php endif; ?>
    </div>
    
    <button type="submit">Submit</button>
</form>
```

---

## $_REQUEST

### Combined Data

```php
<?php
// $_REQUEST contains combined GET, POST, and COOKIE data
// NOT RECOMMENDED - prefer $_GET or $_POST for clarity

// GOOD - explicit
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// BAD - ambiguous
if (isset($_REQUEST['search'])) {
    $search = $_REQUEST['search'];
}
?>
```

---

## $_SERVER

### Request Information

```php
<?php
// $_SERVER contains server and request environment information

// HTTP Method
echo $_SERVER['REQUEST_METHOD'];     // GET, POST, PUT, DELETE

// Request details
echo $_SERVER['REQUEST_URI'];        // /page.php?id=123
echo $_SERVER['QUERY_STRING'];       // id=123
echo $_SERVER['HTTP_HOST'];          // example.com

// Server info
echo $_SERVER['SERVER_NAME'];        // localhost
echo $_SERVER['SERVER_PORT'];        // 8000
echo $_SERVER['SERVER_SOFTWARE'];    // PHP 8.2 Development Server

// Client info
echo $_SERVER['REMOTE_ADDR'];        // 127.0.0.1
echo $_SERVER['REMOTE_PORT'];        // 54321

// HTTP headers become $_SERVER keys
echo $_SERVER['HTTP_USER_AGENT'];    // Browser info
echo $_SERVER['HTTP_ACCEPT'];        // MIME types
echo $_SERVER['HTTP_REFERER'];       // Previous page

// Script info
echo $_SERVER['SCRIPT_NAME'];        // /index.php
echo $_SERVER['SCRIPT_FILENAME'];    // /var/www/html/index.php
echo $_SERVER['PHP_SELF'];           // /index.php

// Timing
echo $_SERVER['REQUEST_TIME'];       // Unix timestamp
echo $_SERVER['REQUEST_TIME_FLOAT'];  // Microtime
?>
```

### Checking Request Method

```php
<?php
// Check HTTP method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET
    echo "Handling GET request";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST
    echo "Handling POST request";
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Handle PUT
    echo "Handling PUT request";
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Handle DELETE
    echo "Handling DELETE request";
}
?>
```

### HTTPS and Protocol

```php
<?php
// Check if secure HTTPS connection
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    echo "Secure connection";
} else {
    echo "Insecure connection";
}

// Get protocol
echo $_SERVER['SERVER_PROTOCOL'];    // HTTP/1.1

// Build full URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$full_url = $protocol . $host . $uri;

echo $full_url;  // http://example.com/page.php?id=123
?>
```

---

## $_COOKIE

### Reading Cookies

```php
<?php
// Cookies sent in request headers

// Read cookie
$user_id = $_COOKIE['user_id'] ?? null;
$preferences = $_COOKIE['preferences'] ?? null;

if (isset($_COOKIE['user_id'])) {
    echo "User ID: " . htmlspecialchars($_COOKIE['user_id']);
}

// Check if cookie exists
if (array_key_exists('theme', $_COOKIE)) {
    echo "Theme: " . $_COOKIE['theme'];
}
?>
```

### Setting Cookies

```php
<?php
// MUST be called before any output
// See session and cookie chapter for details

// Set simple cookie
setcookie('user_id', '123');

// Set with expiration
setcookie('token', 'abc123', time() + (7 * 24 * 60 * 60)); // 7 days

// Set with options
setcookie('preferences', 'dark_mode', [
    'expires' => time() + (30 * 24 * 60 * 60),
    'path' => '/',
    'domain' => '.example.com',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
?>
```

---

## $_SESSION

### Session Variables

```php
<?php
// Start session (must be called first)
session_start();

// Set session data
$_SESSION['user_id'] = 123;
$_SESSION['username'] = 'john';
$_SESSION['cart'] = ['item1', 'item2'];

// Read session data
echo $_SESSION['user_id'] ?? 'Not logged in';

// Check if set
if (isset($_SESSION['user_id'])) {
    echo "User logged in";
}

// Unset session variable
unset($_SESSION['user_id']);

// Destroy entire session
session_destroy();
?>
```

### Session Example

```php
<?php
session_start();

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Authenticate (simplified)
    if ($username === 'john' && $password === 'secret') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'john';
        header('Location: /dashboard');
        exit;
    }
}

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    // Show login form
} else {
    // Show dashboard
    echo "Welcome " . htmlspecialchars($_SESSION['username']);
}
?>
```

---

## $_FILES

### File Upload Information

```php
<?php
// File upload from form

// $_FILES structure:
// $_FILES['filename']['name']     - Original filename
// $_FILES['filename']['type']     - MIME type
// $_FILES['filename']['size']     - File size in bytes
// $_FILES['filename']['tmp_name'] - Temporary location
// $_FILES['filename']['error']    - Error code

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    
    echo "Name: " . $file['name'];
    echo "Type: " . $file['type'];
    echo "Size: " . $file['size'];
    echo "Temp: " . $file['tmp_name'];
    echo "Error: " . $file['error'];
}
?>

<!-- Form -->
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="upload">
    <button type="submit">Upload</button>
</form>
```

### File Upload Errors

```php
<?php
$errors = [
    UPLOAD_ERR_OK => 'No error',
    UPLOAD_ERR_INI_SIZE => 'File exceeds php.ini limit',
    UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
    UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
    UPLOAD_ERR_NO_FILE => 'No file uploaded',
    UPLOAD_ERR_NO_TMP_DIR => 'Temporary directory missing',
    UPLOAD_ERR_CANT_WRITE => 'Cannot write file',
    UPLOAD_ERR_EXTENSION => 'Upload halted by extension',
];

if (isset($_FILES['upload'])) {
    $error_code = $_FILES['upload']['error'];
    echo $errors[$error_code] ?? 'Unknown error';
}
?>
```

---

## $_ENV

### Environment Variables

```php
<?php
// Environment variables
echo $_ENV['USER'] ?? 'unknown';      // Current user
echo $_ENV['HOME'] ?? 'unknown';      // Home directory
echo $_ENV['PATH'] ?? 'unknown';      // System PATH

// Custom environment variables
echo $_ENV['APP_ENV'] ?? 'development';
echo $_ENV['DB_HOST'] ?? 'localhost';
echo $_ENV['API_KEY'] ?? 'not set';

// Using getenv() is equivalent
echo getenv('USER');
echo getenv('APP_ENV');

// Check if variable exists
if (array_key_exists('APP_DEBUG', $_ENV)) {
    $debug = $_ENV['APP_DEBUG'];
}
?>
```

---

## $GLOBALS

### Global Variables Reference

```php
<?php
// $GLOBALS is a reference to all global scope variables

$name = 'John';
$age = 30;

echo $GLOBALS['name'];    // John
echo $GLOBALS['age'];     // 30

// Modify through $GLOBALS
$GLOBALS['name'] = 'Jane';
echo $name;               // Jane

// In functions
function modify_global() {
    $GLOBALS['name'] = 'Bob';
}

modify_global();
echo $name;               // Bob

// Less commonly used than 'global' keyword
function test1() {
    global $name;  // Preferred
    echo $name;
}

function test2() {
    echo $GLOBALS['name'];  // Alternative
}
?>
```

---

## Complete Examples

### Request Handler

```php
<?php
class RequestHandler {
    private $method;
    private $data;
    private $headers;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->parse_data();
        $this->parse_headers();
    }
    
    private function parse_data() {
        if ($this->method === 'GET') {
            $this->data = $_GET;
        } elseif ($this->method === 'POST') {
            $this->data = $_POST;
        } else {
            parse_str(file_get_contents('php://input'), $this->data);
        }
    }
    
    private function parse_headers() {
        $this->headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace('HTTP_', '', $key);
                $this->headers[$header] = $value;
            }
        }
    }
    
    public function get_method() {
        return $this->method;
    }
    
    public function get_data($key) {
        return $this->data[$key] ?? null;
    }
    
    public function get_header($name) {
        return $this->headers[$name] ?? null;
    }
}

$request = new RequestHandler();
echo "Method: " . $request->get_method();
echo "Data: " . $request->get_data('name');
?>
```

### Server Information Display

```php
<?php
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Server Information</h1>
    
    <h2>Request Details</h2>
    <table>
        <tr>
            <td>Method:</td>
            <td><?= $_SERVER['REQUEST_METHOD'] ?></td>
        </tr>
        <tr>
            <td>URI:</td>
            <td><?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></td>
        </tr>
        <tr>
            <td>Host:</td>
            <td><?= htmlspecialchars($_SERVER['HTTP_HOST']) ?></td>
        </tr>
        <tr>
            <td>Remote IP:</td>
            <td><?= $_SERVER['REMOTE_ADDR'] ?></td>
        </tr>
    </table>
    
    <h2>Query Parameters</h2>
    <pre><?php var_export($_GET); ?></pre>
    
    <h2>POST Data</h2>
    <pre><?php var_export($_POST); ?></pre>
    
    <h2>Cookies</h2>
    <pre><?php var_export($_COOKIE); ?></pre>
</body>
</html>
```

---

## See Also

- [Query Parameters](10-query-parameter.md)
- [Form POST Handling](12-form-post.md)
- [Sessions](15-session.md)
- [Cookies](16-cookie.md)
- [File Uploads](18-upload-file.md)
