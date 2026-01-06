# Cookies

## Overview

Cookies are small text files stored on the client's browser and sent with every request to the server. They are used to store user preferences, tracking data, and authentication tokens. Understanding cookie handling is essential for web development.

---

## Table of Contents

1. Cookie Basics
2. Setting Cookies
3. Reading Cookies
4. Deleting Cookies
5. Cookie Options
6. Security Considerations
7. Browser Storage Alternatives
8. Complete Examples

---

## Cookie Basics

### How Cookies Work

```
1. Server sets cookie
   Response Header: Set-Cookie: name=value

2. Browser stores cookie
   Saved locally on client machine

3. Browser sends cookie with requests
   Request Header: Cookie: name=value

4. Server reads cookie
   Available in $_COOKIE
```

### Cookie vs Session

```
Cookie                          Session
─────────────────────────────────────────
Stored on client                Stored on server
Sent with every request         Session ID sent with requests
Limited size (~4KB)             Unlimited size
User can see/modify             User can't see data
Persists after browser closes   Expires on logout
Better for preferences          Better for security
Less secure                      More secure
```

---

## Setting Cookies

### Basic setcookie()

```php
<?php
// MUST be called before any output
// No whitespace before <?php tag

// Simple cookie
setcookie('username', 'john');

// Cookie with expiration (30 days)
setcookie('remember_me', 'token123', time() + (30 * 24 * 60 * 60));

// Expires in 1 hour
setcookie('session_token', 'abc123', time() + 3600);

// Session cookie (expires when browser closes)
setcookie('temp', 'value', 0);  // 0 or omit expires parameter
?>
```

### Modern setcookie() with Options

```php
<?php
// PHP 7.3+ supports array options

setcookie('token', 'abc123', [
    'expires' => time() + 3600,           // 1 hour
    'path' => '/',                        // Available on entire site
    'domain' => '.example.com',           // All subdomains
    'secure' => true,                     // HTTPS only
    'httponly' => true,                   // JavaScript can't access
    'samesite' => 'Strict'                // CSRF protection
]);

// Equivalent old syntax
setcookie(
    'token',
    'abc123',
    time() + 3600,  // expires
    '/',            // path
    '.example.com', // domain
    true,           // secure (HTTPS)
    true            // httponly
);
?>
```

### Cookie Options Explained

```php
<?php
setcookie('name', 'value', [
    // Expiration time
    'expires' => time() + 3600,         // Unix timestamp
    
    // Path where cookie is available
    'path' => '/',                      // Available everywhere
    'path' => '/admin',                 // Only /admin paths
    
    // Domain for cookie
    'domain' => '.example.com',         // example.com and subdomains
    'domain' => 'api.example.com',      // Only api.example.com
    
    // HTTPS only
    'secure' => true,                   // HTTPS connections only
    'secure' => false,                  // HTTP and HTTPS
    
    // JavaScript cannot access
    'httponly' => true,                 // PHP only (more secure)
    'httponly' => false,                // JavaScript can access
    
    // CSRF protection
    'samesite' => 'Strict',             // Strict CSRF protection
    'samesite' => 'Lax',                // Moderate CSRF protection
    'samesite' => 'None'                // No CSRF protection
]);
?>
```

### Multiple Values

```php
<?php
// Single cookie with encoded array
$preferences = [
    'theme' => 'dark',
    'language' => 'en',
    'notifications' => 'on'
];

setcookie('prefs', json_encode($preferences), time() + 86400);

// Or separate cookies
setcookie('theme', 'dark', time() + 86400);
setcookie('language', 'en', time() + 86400);
?>
```

---

## Reading Cookies

### Accessing Cookies

```php
<?php
// Read cookie value
echo $_COOKIE['username'];      // john

// Safe access with null coalescing
echo $_COOKIE['username'] ?? 'guest';

// Check if cookie exists
if (isset($_COOKIE['remember_me'])) {
    echo 'Welcome back!';
}

// Get all cookies
foreach ($_COOKIE as $name => $value) {
    echo "$name => " . htmlspecialchars($value);
}

// Count cookies
echo count($_COOKIE);
?>
```

### Decoding Cookies

```php
<?php
// Read JSON-encoded cookie
$prefs_json = $_COOKIE['prefs'] ?? '{}';
$prefs = json_decode($prefs_json, true);

echo $prefs['theme'];      // dark
echo $prefs['language'];   // en

// Handle decode errors
if (json_last_error() !== JSON_ERROR_NONE) {
    $prefs = [];  // Use defaults
}

// URL-encoded cookie
$cookie_value = $_COOKIE['value'];
$decoded = urldecode($cookie_value);
?>
```

---

## Deleting Cookies

### Using setcookie()

```php
<?php
// Delete cookie by setting expiration to past
setcookie('username', '', time() - 3600);

// Or set empty value
setcookie('remember_me', '', time() - 3600);

// Set path and domain when deleting (must match original)
setcookie('token', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => '.example.com'
]);
?>
```

### Using unset()

```php
<?php
// Remove from $_COOKIE array (doesn't delete browser's copy)
unset($_COOKIE['username']);

// Still need to use setcookie to remove from browser
setcookie('username', '', time() - 3600);
?>
```

### Logout Example

```php
<?php
// Delete all session cookies on logout

session_start();

// Unset all session variables
$_SESSION = [];

// Delete session cookie
setcookie('PHPSESSID', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'httponly' => true
]);

// Delete remember-me cookie
setcookie('remember_me', '', [
    'expires' => time() - 3600,
    'path' => '/'
]);

// Destroy session
session_destroy();

// Redirect
header('Location: /');
exit;
?>
```

---

## Cookie Options

### Expiration Times

```php
<?php
// Session cookie (browser session only)
setcookie('temp', 'value');  // Expires when browser closes

// 1 hour
setcookie('token', 'value', time() + 3600);

// 1 day
setcookie('preference', 'value', time() + 86400);

// 1 week
setcookie('remember', 'value', time() + (7 * 86400));

// 30 days
setcookie('settings', 'value', time() + (30 * 86400));

// 1 year
setcookie('analytics', 'value', time() + (365 * 86400));

// Helper function
function cookie_expiry($days) {
    return time() + ($days * 86400);
}

setcookie('test', 'value', cookie_expiry(30));
?>
```

### Path and Domain

```php
<?php
// Available everywhere
setcookie('global', 'value', 0, '/');

// Only in /admin paths
setcookie('admin', 'value', 0, '/admin');

// Only in /api/v1 paths
setcookie('api', 'value', 0, '/api/v1');

// Only on example.com
setcookie('exact', 'value', 0, '/', 'example.com');

// On example.com and all subdomains
setcookie('domain', 'value', 0, '/', '.example.com');

// Only on api.example.com
setcookie('api', 'value', 0, '/', 'api.example.com');
?>
```

### Security Flags

```php
<?php
// Secure flag - HTTPS only
setcookie('secure_token', 'value', [
    'secure' => true,  // Only HTTPS
]);

// HTTPOnly flag - JavaScript cannot access
setcookie('session', 'value', [
    'httponly' => true,  // PHP only, not JavaScript
]);

// SameSite attribute - CSRF protection
setcookie('token', 'value', [
    'samesite' => 'Strict',  // No cross-site requests
]);

// All secure options
setcookie('secure_session', 'value', [
    'expires' => time() + 3600,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
?>
```

---

## Security Considerations

### Secure Cookie Handling

```php
<?php
// 1. ALWAYS use HTTPS in production
setcookie('token', 'value', [
    'secure' => true,      // HTTPS only
]);

// 2. Use HTTPOnly flag
setcookie('session', 'value', [
    'httponly' => true,    // Prevent XSS theft
]);

// 3. Use SameSite attribute
setcookie('auth', 'value', [
    'samesite' => 'Strict' // Prevent CSRF
]);

// 4. Don't store sensitive data in cookies
// WRONG: setcookie('password', 'secret123');
// WRONG: setcookie('api_key', 'abc123xyz');

// CORRECT: Use tokens or sessions
setcookie('session_token', base64_encode(random_bytes(32)), [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);

// 5. Sign/encrypt cookie values
$value = 'user_id=123';
$signature = hash_hmac('sha256', $value, 'secret_key');
setcookie('data', $value . '|' . $signature);

// Verify signature
list($value, $signature) = explode('|', $_COOKIE['data']);
if (hash_hmac('sha256', $value, 'secret_key') !== $signature) {
    exit('Invalid cookie');
}
?>
```

### Avoiding Cookie Attacks

```php
<?php
// Session Fixation Prevention
// Don't use cookie-based session IDs that users control

// VULNERABLE
if (isset($_GET['sessionid'])) {
    setcookie('SESSID', $_GET['sessionid']);
}

// SECURE
session_start();
session_regenerate_id();  // Generate new ID on login

// Cookie Injection Prevention
// Don't include user input in cookies

// VULNERABLE
setcookie('user_data', $_GET['data']);

// SECURE
$safe_data = sanitize($_GET['data']);
setcookie('user_data', $safe_data);

// Never trust cookie values
if (isset($_COOKIE['user_id'])) {
    $user_id = (int) $_COOKIE['user_id'];
    // Verify against database
}
?>
```

---

## Browser Storage Alternatives

### localStorage (JavaScript)

```javascript
// Client-side only (JavaScript)
localStorage.setItem('theme', 'dark');
console.log(localStorage.getItem('theme'));

// Accessible to JavaScript (XSS risk!)
// Persists until manually deleted
// ~5-10MB storage
```

### sessionStorage (JavaScript)

```javascript
// Client-side only (JavaScript)
sessionStorage.setItem('data', 'value');

// Cleared when browser tab closes
// Same XSS risk as localStorage
```

### When to Use Each

```
Cookies:
  ✓ Need to send to server with requests
  ✓ Cross-domain tracking
  ✓ HTTPOnly flag for security
  ✗ Limited size (~4KB)
  
localStorage:
  ✓ Large storage (~5-10MB)
  ✓ Persistent storage
  ✗ XSS vulnerability (JavaScript accessible)
  ✗ Not sent to server automatically
  
sessionStorage:
  ✓ Temporary storage per tab
  ✓ Not shared across tabs
  ✗ Same XSS vulnerability
  
Sessions:
  ✓ Server-side security
  ✓ Unlimited size
  ✓ Cannot be accessed by client
  ✓ Better for sensitive data
```

---

## Complete Examples

### Remember Me Functionality

```php
<?php
// remember_me.php - Remember me cookie implementation

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Authenticate
    $db = new PDO('sqlite:users.db');
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Set remember-me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            
            // Store token in database
            $stmt = $db->prepare('
                INSERT INTO remember_tokens (user_id, token, expires)
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))
            ');
            $stmt->execute([$user['id'], hash('sha256', $token)]);
            
            // Set cookie
            setcookie('remember_me', $token, [
                'expires' => time() + (30 * 86400),
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
        
        header('Location: /dashboard');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}

// Auto-login with remember-me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $db = new PDO('sqlite:users.db');
    $token_hash = hash('sha256', $_COOKIE['remember_me']);
    
    $stmt = $db->prepare('
        SELECT u.* FROM users u
        JOIN remember_tokens rt ON u.id = rt.user_id
        WHERE rt.token = ? AND rt.expires > NOW()
    ');
    $stmt->execute([$token_hash]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: /dashboard');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <label>
            <input type="checkbox" name="remember">
            Remember me
        </label>
        <button type="submit">Login</button>
    </form>
</body>
</html>
```

### Preference Storage

```php
<?php
// preferences.php - Store user preferences in cookies

$preferences = [
    'theme' => $_COOKIE['pref_theme'] ?? 'light',
    'language' => $_COOKIE['pref_language'] ?? 'en',
    'notifications' => $_COOKIE['pref_notifications'] ?? 'on'
];

// Update preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['theme'] ?? 'light';
    $language = $_POST['language'] ?? 'en';
    
    // Whitelist validation
    $allowed_themes = ['light', 'dark'];
    $allowed_languages = ['en', 'es', 'fr'];
    
    if (in_array($theme, $allowed_themes)) {
        $preferences['theme'] = $theme;
        setcookie('pref_theme', $theme, [
            'expires' => time() + (365 * 86400),
            'path' => '/'
        ]);
    }
    
    if (in_array($language, $allowed_languages)) {
        $preferences['language'] = $language;
        setcookie('pref_language', $language, [
            'expires' => time() + (365 * 86400),
            'path' => '/'
        ]);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body.dark {
            background: #333;
            color: #fff;
        }
    </style>
</head>
<body class="<?= htmlspecialchars($preferences['theme']) ?>">
    <h1>Preferences</h1>
    
    <form method="POST">
        <label>Theme:</label>
        <select name="theme">
            <option value="light" <?= ($preferences['theme'] === 'light') ? 'selected' : '' ?>>Light</option>
            <option value="dark" <?= ($preferences['theme'] === 'dark') ? 'selected' : '' ?>>Dark</option>
        </select>
        
        <label>Language:</label>
        <select name="language">
            <option value="en" <?= ($preferences['language'] === 'en') ? 'selected' : '' ?>>English</option>
            <option value="es" <?= ($preferences['language'] === 'es') ? 'selected' : '' ?>>Spanish</option>
            <option value="fr" <?= ($preferences['language'] === 'fr') ? 'selected' : '' ?>>French</option>
        </select>
        
        <button type="submit">Save Preferences</button>
    </form>
</body>
</html>
```

---

## See Also

- [Sessions](15-session.md)
- [Session & Cookie Relationship](17-session-cookie-relation.md)
- [HTTP Headers](13-header.md)
- [Security](11-xss-cross-site-scripting.md)
