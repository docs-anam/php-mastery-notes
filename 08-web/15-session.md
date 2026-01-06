# PHP Sessions

## Overview

Sessions allow you to store user data across multiple page requests. A session creates a persistent connection between a user and your application, enabling features like login systems, shopping carts, and user preferences.

---

## Table of Contents

1. Session Basics
2. Starting Sessions
3. Setting Session Variables
4. Session Storage
5. Session Configuration
6. Security Considerations
7. Custom Session Handlers
8. Complete Examples

---

## Session Basics

### How Sessions Work

```
User visits website
  ↓
Server creates session (unique ID)
  ↓
Session ID sent to client (cookie)
  ↓
Client sends session ID with each request
  ↓
Server looks up session data using ID
  ↓
Application can access user data via $_SESSION
```

### Session vs Cookie

```
Session                     Cookie
─────────────────────────────────────
Data stored on server       Data stored on client
Session ID sent to client   Value sent to client
Expires on logout/timeout   Expires at set time
More secure                 Can be modified by user
Better for sensitive data   Better for preferences
```

---

## Starting Sessions

### session_start()

```php
<?php
// Must be called before any output
// Usually first line in your script

session_start();

// Now $_SESSION is available
$_SESSION['user_id'] = 123;
?>
```

### Session ID

```php
<?php
session_start();

// Get current session ID
$session_id = session_id();
echo $session_id;  // 9hf8d7f8d7f8d7f

// Set session ID (before session_start)
session_id('custom_session_id');
session_start();

// Generate new session ID
session_regenerate_id();  // For security after login
?>
```

### Session Name

```php
<?php
// Change session cookie name (default: PHPSESSID)
session_name('MY_APP_SESSION');

// Get session name
$name = session_name();
echo $name;  // MY_APP_SESSION

// Must be called before session_start()
session_name('APP_SESS');
session_start();
?>
```

---

## Setting Session Variables

### Basic Usage

```php
<?php
session_start();

// Set session variable
$_SESSION['user_id'] = 123;
$_SESSION['username'] = 'john';
$_SESSION['email'] = 'john@example.com';

// Read session variable
echo $_SESSION['username'];  // john

// Check if variable exists
if (isset($_SESSION['user_id'])) {
    echo 'User logged in';
}

// Unset single variable
unset($_SESSION['user_id']);

// Unset all variables
$_SESSION = array();

// Destroy entire session
session_destroy();
?>
```

### Arrays in Sessions

```php
<?php
session_start();

// Store array
$_SESSION['cart'] = ['item1', 'item2'];

// Add to cart
$_SESSION['cart'][] = 'item3';

// Store nested data
$_SESSION['user'] = [
    'id' => 123,
    'name' => 'John',
    'email' => 'john@example.com',
    'permissions' => ['read', 'write']
];

// Access nested data
echo $_SESSION['user']['name'];
?>
```

### Objects in Sessions

```php
<?php
session_start();

// Store object (careful - must handle serialization)
class User {
    public $id;
    public $name;
}

$user = new User();
$user->id = 1;
$user->name = 'John';

$_SESSION['user'] = $user;

// Retrieve object
$user = $_SESSION['user'];
echo $user->name;
?>
```

---

## Session Storage

### Default Storage (Files)

```php
<?php
// Sessions stored in files by default
// Location configured in php.ini

session_start();

// Get session save path
$path = session_save_path();
echo $path;  // /var/lib/php/sessions/

// Set custom path (before session_start)
session_save_path('/tmp/my_sessions');
session_start();

// Session files are named like:
// sess_9hf8d7f8d7f8d7f8d7f8d7f8d7f8d7f
?>
```

### Database Storage

```php
<?php
// Custom session handler for database storage

class DatabaseSessionHandler implements SessionHandlerInterface {
    private $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function open(string $path, string $name): bool {
        return true;
    }
    
    public function close(): bool {
        return true;
    }
    
    public function read(string $id): string|false {
        $stmt = $this->db->prepare('
            SELECT data FROM sessions WHERE id = ? AND expires > NOW()
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        return $row ? $row['data'] : '';
    }
    
    public function write(string $id, string $data): bool {
        $stmt = $this->db->prepare('
            INSERT INTO sessions (id, data, expires) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))
            ON DUPLICATE KEY UPDATE data = ?, expires = DATE_ADD(NOW(), INTERVAL 30 MINUTE)
        ');
        
        return $stmt->execute([$id, $data, $data]);
    }
    
    public function destroy(string $id): bool {
        $stmt = $this->db->prepare('DELETE FROM sessions WHERE id = ?');
        return $stmt->execute([$id]);
    }
    
    public function gc(int $max_lifetime): int|false {
        $stmt = $this->db->prepare('DELETE FROM sessions WHERE expires < NOW()');
        return $stmt->execute() ? 1 : false;
    }
}

// Usage
$db = new PDO('sqlite:sessions.db');
$handler = new DatabaseSessionHandler($db);

session_set_save_handler($handler);
session_start();
?>
```

---

## Session Configuration

### php.ini Settings

```ini
; Session name
session.name = PHPSESSID

; Session path
session.save_path = "/var/lib/php/sessions"

; Session lifetime (seconds)
session.gc_maxlifetime = 1440  ; 24 minutes

; Cookie lifetime (0 = browser session)
session.cookie_lifetime = 0

; Cookie path
session.cookie_path = "/"

; Cookie domain
session.cookie_domain = ""

; HTTPS only
session.cookie_secure = 0

; JavaScript cannot access
session.cookie_httponly = 1

; SameSite attribute
session.cookie_samesite = "Lax"
```

### Runtime Configuration

```php
<?php
// Set before session_start()

// Session lifetime
ini_set('session.gc_maxlifetime', 3600);

// Cookie settings
ini_set('session.cookie_lifetime', 0);      // Browser session
ini_set('session.cookie_httponly', 1);      // No JavaScript access
ini_set('session.cookie_secure', 1);        // HTTPS only
ini_set('session.cookie_samesite', 'Strict');

session_start();
?>
```

---

## Security Considerations

### Secure Session Handling

```php
<?php
// Best practices for secure sessions

// 1. Always use HTTPS
ini_set('session.cookie_secure', 1);

// 2. HTTPOnly flag prevents JavaScript access
ini_set('session.cookie_httponly', 1);

// 3. SameSite prevents CSRF
ini_set('session.cookie_samesite', 'Strict');

// 4. Regenerate ID after login (prevent fixation)
session_start();

if (!isset($_SESSION['user_id']) && user_is_authenticated()) {
    session_regenerate_id(true);  // true = delete old session
    $_SESSION['user_id'] = 123;
}

// 5. Validate session data
session_start();

if (isset($_SESSION['user_id']) && !is_numeric($_SESSION['user_id'])) {
    session_destroy();
    exit('Invalid session');
}

// 6. Timeout inactive sessions
$timeout = 30 * 60;  // 30 minutes
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
$_SESSION['last_activity'] = time();

// 7. Fingerprint validation
function get_fingerprint() {
    return md5($_SERVER['HTTP_USER_AGENT']);
}

session_start();
if (!isset($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = get_fingerprint();
}

if ($_SESSION['fingerprint'] !== get_fingerprint()) {
    session_destroy();
    exit('Session invalid');
}
?>
```

### Session Fixation Prevention

```php
<?php
// Don't use pre-set session ID
session_start();

// VULNERABLE: Accepts session ID from client
if (isset($_GET['sid'])) {
    session_id($_GET['sid']);
}

// SECURE: Always regenerate after login
if ($user_authenticated) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
}
?>
```

---

## Complete Examples

### Login System

```php
<?php
// login.php - Secure login implementation

session_start();

// Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Username and password required';
    } else {
        // Authenticate (simplified example)
        $db = new PDO('sqlite:users.db');
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Regenerate session ID
            session_regenerate_id(true);
            
            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['last_activity'] = time();
            $_SESSION['fingerprint'] = md5($_SERVER['HTTP_USER_AGENT']);
            
            $success = 'Login successful';
            header('Location: /dashboard');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
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
        <button type="submit">Login</button>
    </form>
</body>
</html>
```

### Shopping Cart

```php
<?php
// cart.php - Shopping cart using sessions

session_start();

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        
        if ($product_id > 0 && $quantity > 0) {
            // Add or update item
            $_SESSION['cart'][$product_id] = ($SESSION['cart'][$product_id] ?? 0) + $quantity;
        }
    } elseif ($_POST['action'] === 'remove') {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$product_id]);
    } elseif ($_POST['action'] === 'clear') {
        $_SESSION['cart'] = [];
    }
    
    header('Location: /cart');
    exit;
}

// Display cart
$total = 0;
$items = [];

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = get_product($product_id);
    $item_total = $product['price'] * $quantity;
    $total += $item_total;
    
    $items[] = [
        'product' => $product,
        'quantity' => $quantity,
        'total' => $item_total
    ];
}
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Shopping Cart</h1>
    
    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product']['name']) ?></td>
                <td>$<?= number_format($item['product']['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>$<?= number_format($item['total'], 2) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="product_id" value="<?= $item['product']['id'] ?>">
                        <button type="submit">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <p>Total: $<?= number_format($total, 2) ?></p>
    
    <form method="POST" style="display:inline;">
        <input type="hidden" name="action" value="clear">
        <button type="submit">Clear Cart</button>
    </form>
</body>
</html>
```

---

## Key Functions

```php
<?php
// Session management functions

session_start()              // Start/resume session
session_id()                 // Get/set session ID
session_name()               // Get/set session name
session_save_path()          // Get/set session save path
session_status()             // Get session status (PHP_SESSION_ACTIVE, etc)
session_regenerate_id()      // Generate new ID (prevent fixation)
session_destroy()            // Destroy session
session_write_close()        // Write session and close
session_set_save_handler()   // Set custom storage handler
?>
```

---

## See Also

- [Cookies](16-cookie.md)
- [Session & Cookie Relationship](17-session-cookie-relation.md)
- [Form POST Handling](12-form-post.md)
- [Security](11-xss-cross-site-scripting.md)
