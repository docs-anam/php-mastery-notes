# Sessions and Cookies Relationship

## Overview

Sessions and cookies are related but distinct concepts. Sessions use cookies to work, and understanding their relationship is crucial for building secure web applications. This chapter explores how they interact.

---

## Table of Contents

1. How Sessions Use Cookies
2. Session ID Storage
3. Comparison and Use Cases
4. Data Storage Differences
5. Security Implications
6. Choosing Between Them
7. Combined Implementation
8. Complete Examples

---

## How Sessions Use Cookies

### Sessions Need Cookies

```
Session Creation (Server)
  ↓
Generate unique session ID
  ↓
Store session data in server storage (files/database)
  ↓
Send session ID to client via cookie (Set-Cookie header)
  ↓
Client stores cookie
  ↓
Client sends cookie with each request
  ↓
Server reads session ID from cookie
  ↓
Server retrieves session data using ID
```

### Default Behavior

```php
<?php
// When you start a session, PHP:
session_start();

// 1. Checks if PHPSESSID cookie exists
//    (from $_COOKIE or HTTP request headers)

// 2. If exists, retrieves session data from storage
//    (usually /var/lib/php/sessions/)

// 3. If doesn't exist, creates new session
//    Generates random ID
//    Sends Set-Cookie header to client

// 4. Makes $_SESSION array available
$_SESSION['user_id'] = 123;

// 5. When script finishes, session data written to storage
// 6. Session ID cookie sent in response headers
?>
```

---

## Session ID Storage

### Cookie-Based (Default)

```php
<?php
// Session ID stored in cookie
session_start();

// Cookie name: PHPSESSID (by default)
// Can be changed:
session_name('MY_SESSION');
session_start();

// Cookie value: unique identifier
// Example: 9hf8d7f8d7f8d7f8d7f8d7f8d7f8d7f

// Cookie is sent with every request
// Server looks up session data using ID
?>
```

### URL-Based (Not Recommended)

```php
<?php
// Session ID in URL (trans_sid)
// Insecure and deprecated

// php.ini setting
ini_set('session.use_trans_sid', 1);

// Results in URLs like:
// http://example.com/page.php?PHPSESSID=abc123

// PROBLEMS:
// - Visible in URL
// - Shared in referrer headers
// - Exposed in browser history
// - XSS vulnerability
// - SEO problem (duplicate content)

// DON'T USE THIS!
?>
```

---

## Comparison and Use Cases

### Side-by-Side Comparison

```
Feature                    Session             Cookie
─────────────────────────────────────────────────────
Storage Location           Server              Client
Data Size                  Unlimited           ~4KB
How Transmitted            Session ID (cookie) Direct value
User Can View              No                  Yes
User Can Modify            No                  Yes (easy)
Lifetime                   Until logout        Until expiration
Security                   More secure         Less secure
Scalability                Requires storage    Minimal resources
Cross-domain               No (per site)       Can be cross-domain
```

### When to Use Sessions

```
✓ User authentication
✓ Store user ID
✓ Store sensitive data
✓ Shopping carts
✓ Temporary form data
✓ Large data structures
✓ Data that must be private
✓ Server-side access only
```

### When to Use Cookies

```
✓ User preferences
✓ Theme selection
✓ Language preference
✓ Non-sensitive tokens
✓ Analytics data
✓ Remember-me tokens (with server verification)
✓ Client-side data
✓ Small data values
```

---

## Data Storage Differences

### Sessions - Server Storage

```
/var/lib/php/sessions/

File structure:
sess_9hf8d7f8d7f8d7f = "user_id|i:123;username|s:4:"john";"

Contents are serialized $_SESSION array
```

### Cookies - Client Storage

```
Browser storage

Cookie structure:
Name: username
Value: john
Domain: example.com
Path: /
Expires: [date]
Secure: true/false
HttpOnly: true/false
SameSite: Strict/Lax/None
```

---

## Security Implications

### Session Security

```php
<?php
// Sessions are more secure because:
// 1. Data stored on server (not accessible to user)
// 2. Only ID sent to client (small, random string)
// 3. Cannot be easily modified by user

session_start();
$_SESSION['user_id'] = 123;
$_SESSION['is_admin'] = true;

// User cannot change their admin status
// (it's stored on server, user can't modify)

// If attacker steals session ID:
// - Limited value without corresponding server data
// - Session expires after timeout
// - Can be invalidated by regenerating ID
?>
```

### Cookie Security Issues

```php
<?php
// Cookies are less secure because:
// 1. Value stored on client (user can see/modify)
// 2. Sent with every request (larger attack surface)
// 3. Can be stolen via XSS
// 4. Can be edited in browser developer tools

setcookie('is_admin', 'true');
// User can change this in browser!

// Mitigation: Sign/encrypt cookies
$value = 'user_id=123';
$signature = hash_hmac('sha256', $value, 'secret_key');
setcookie('data', $value . '|' . $signature);

// Server verifies signature
list($value, $sig) = explode('|', $_COOKIE['data']);
if (hash_hmac('sha256', $value, 'secret_key') !== $sig) {
    exit('Invalid cookie');
}
?>
```

---

## Choosing Between Sessions and Cookies

### Decision Tree

```
Is the data sensitive?
├─ YES → Use Sessions
│   └─ User authentication
│   └─ User permissions
│   └─ Personal information
│
└─ NO → Can use Cookies or Sessions
    └─ User preferences → Cookies
    └─ Large data → Sessions
    └─ Needs server access every time → Sessions
    └─ Just for display → Cookies
```

### Common Patterns

```php
<?php
// PATTERN 1: Auth with Sessions + Token Cookies
session_start();

// Critical: in session
$_SESSION['user_id'] = 123;
$_SESSION['roles'] = ['user', 'moderator'];

// Convenience: in cookie
$token = bin2hex(random_bytes(32));
setcookie('remember_token', $token, [
    'expires' => time() + (30 * 86400),
    'httponly' => true
]);

// PATTERN 2: Preferences in Cookies + Auth in Session
session_start();
$_SESSION['authenticated'] = true;

setcookie('theme', 'dark', ['expires' => time() + (365 * 86400)]);
setcookie('language', 'en', ['expires' => time() + (365 * 86400)]);

// PATTERN 3: JWT Token in Cookie
session_start();

$token = jwt_encode([
    'user_id' => 123,
    'exp' => time() + 3600
], 'secret_key');

setcookie('auth_token', $token, [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
?>
```

---

## Combined Implementation

### Complete Session + Cookie Example

```php
<?php
// Secure implementation combining both

session_start();

// Set secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600);

// AUTHENTICATION (in session)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Authenticate user
    $db = new PDO('sqlite:users.db');
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Regenerate session ID (prevent fixation)
        session_regenerate_id(true);
        
        // Store in session (secure, server-side)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['roles'] = json_decode($user['roles']);
        $_SESSION['last_activity'] = time();
        
        // Optional: remember-me cookie
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_me', $token, [
                'expires' => time() + (30 * 86400),
                'path' => '/',
                'httponly' => true,
                'secure' => true,
                'samesite' => 'Strict'
            ]);
            
            // Store token hash in database
            $stmt = $db->prepare('
                INSERT INTO remember_tokens (user_id, token, expires)
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))
            ');
            $stmt->execute([$user['id'], hash('sha256', $token)]);
        }
        
        header('Location: /dashboard');
        exit;
    }
}

// PREFERENCES (in cookies)
$theme = $_COOKIE['theme'] ?? 'light';
$language = $_COOKIE['language'] ?? 'en';

if (isset($_POST['save_preferences'])) {
    $new_theme = $_POST['theme'] ?? 'light';
    
    if (in_array($new_theme, ['light', 'dark'])) {
        setcookie('theme', $new_theme, [
            'expires' => time() + (365 * 86400),
            'path' => '/'
        ]);
    }
}
?>
```

---

## Complete Examples

### Secure Authentication System

```php
<?php
// auth.php - Secure auth with sessions and cookies

class AuthManager {
    private $db;
    private $session_timeout = 3600;  // 1 hour
    
    public function __construct(PDO $db) {
        $this->db = $db;
        $this->configure_session();
    }
    
    private function configure_session() {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', $this->session_timeout);
        session_start();
    }
    
    public function login($username, $password, $remember = false) {
        // Authenticate
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        
        // Regenerate session ID
        session_regenerate_id(true);
        
        // Store in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['last_activity'] = time();
        
        // Optional: remember-me
        if ($remember) {
            $this->set_remember_token($user['id']);
        }
        
        return true;
    }
    
    private function set_remember_token($user_id) {
        $token = bin2hex(random_bytes(32));
        
        setcookie('remember_me', $token, [
            'expires' => time() + (30 * 86400),
            'path' => '/',
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict'
        ]);
        
        $token_hash = hash('sha256', $token);
        $stmt = $this->db->prepare('
            INSERT INTO remember_tokens (user_id, token, expires)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))
        ');
        $stmt->execute([$user_id, $token_hash]);
    }
    
    public function auto_login() {
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
            $token_hash = hash('sha256', $_COOKIE['remember_me']);
            
            $stmt = $this->db->prepare('
                SELECT u.* FROM users u
                JOIN remember_tokens rt ON u.id = rt.user_id
                WHERE rt.token = ? AND rt.expires > NOW()
            ');
            $stmt->execute([$token_hash]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['last_activity'] = time();
                return true;
            }
        }
        return false;
    }
    
    public function check_timeout() {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $this->session_timeout) {
                $this->logout();
                return false;
            }
        }
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    public function is_authenticated() {
        return isset($_SESSION['user_id']);
    }
    
    public function get_user() {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null
        ];
    }
    
    public function logout() {
        // Delete remember token if exists
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600);
        }
        
        // Clear session
        $_SESSION = [];
        session_destroy();
    }
}

// Usage
$db = new PDO('sqlite:users.db');
$auth = new AuthManager($db);

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($auth->login($_POST['username'], $_POST['password'], isset($_POST['remember']))) {
        header('Location: /dashboard');
        exit;
    }
}

// Auto-login with remember token
$auth->auto_login();

// Check timeout
if ($auth->is_authenticated() && !$auth->check_timeout()) {
    header('Location: /login');
    exit;
}

// Protect page
if (!$auth->is_authenticated()) {
    header('Location: /login');
    exit;
}

$user = $auth->get_user();
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Welcome <?= htmlspecialchars($user['username']) ?></h1>
    
    <form method="POST">
        <button name="logout" value="1" type="submit">Logout</button>
    </form>
</body>
</html>
```

---

## Key Takeaways

**Remember:**

1. ✅ Sessions store data on server, cookies on client
2. ✅ Sessions use cookies to transmit session ID
3. ✅ Use sessions for sensitive data (auth)
4. ✅ Use cookies for preferences
5. ✅ Always sign/encrypt important cookies
6. ✅ Set HTTPOnly flag on auth cookies
7. ✅ Implement session timeouts
8. ✅ Regenerate session ID after login
9. ✅ Use HTTPS with secure cookies
10. ✅ Verify remember-me tokens server-side

---

## See Also

- [Sessions](15-session.md)
- [Cookies](16-cookie.md)
- [HTTP Headers](13-header.md)
- [Security](11-xss-cross-site-scripting.md)
