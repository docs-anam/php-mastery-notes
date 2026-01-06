# PHP Web Development

## Overview

PHP is a server-side scripting language specifically designed for web development. Understanding how PHP works in a web context is fundamental to building dynamic websites and web applications.

---

## Table of Contents

1. What is PHP?
2. Server-Side Execution
3. PHP Processing Cycle
4. Web Server Integration
5. Dynamic Content Generation
6. PHP in Action
7. Best Practices
8. Complete Examples

---

## What is PHP?

### Definition

```php
<?php
// PHP = Hypertext Preprocessor
// 
// Features:
// - Server-side scripting language
// - Processed on server before sent to browser
// - Open source and free
// - Runs on major web servers (Apache, Nginx)
// - Supports multiple databases (MySQL, PostgreSQL, etc.)
// - Easy to learn and widely used
?>
```

### Client vs Server Code

```html
<!-- CLIENT SIDE - Browser executes -->
<html>
<head>
    <script>
        // JavaScript runs in browser
        var x = 10;
        console.log(x);  // Browser sees this
    </script>
</head>
<body>
    <h1>Hello World</h1>
</body>
</html>

<!-- SERVER SIDE - Not visible in browser -->
<?php
    // PHP runs on server
    $x = 10;
    echo $x;  // Browser never sees this code
    // Browser only sees the OUTPUT
?>
```

---

## Server-Side Execution

### How PHP Works

```
1. Browser sends HTTP request
   |
   v
2. Web server receives request
   |
   v
3. Web server executes PHP file
   |
   v
4. PHP generates output
   |
   v
5. Web server sends output to browser
   |
   v
6. Browser displays output
```

### Simple Example

```php
<?php
// server.php - On server

echo "Current time: " . date('Y-m-d H:i:s');
echo "Random number: " . rand(1, 100);

// Browser receives:
// "Current time: 2026-01-06 10:30:45"
// "Random number: 47"
// 
// Browser NEVER sees the PHP code!
?>
```

### Security Advantage

```php
<?php
// Secret information stays on server
$database_password = 'super_secret_123';  // Never sent to browser
$api_key = 'sk_live_abc123xyz';           // Never sent to browser

// Only results are sent to browser
echo "Database connected successfully";
// Browser sees: "Database connected successfully"
// Browser doesn't see: $database_password
?>
```

---

## PHP Processing Cycle

### Request-Response in Detail

```
USER ACTION
    |
    v
[Browser] --HTTP Request--> [Web Server]
    ^                            |
    |                            v
    |                        [PHP Engine]
    |                            |
    |                            v
    |                        [Database] (if needed)
    |                            |
    |                            v
    +--------<-- HTML Response --+
    
[Browser renders HTML]
```

### Execution Steps

```php
<?php
// Step 1: PHP file is parsed
// Step 2: Variables and functions are evaluated
// Step 3: Database operations execute
// Step 4: Output is generated
// Step 5: Headers are sent
// Step 6: HTML/JSON/XML is sent to client
// Step 7: Connection closes

// Example flow:

// Step 1-2: Parse and evaluate
$name = "John";
$age = 30;

// Step 3: Database operation
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

// Step 4: Generate output
echo "Name: $name";
echo "Age: $age";
foreach ($users as $user) {
    echo $user['email'];
}

// Step 5-7: Headers sent, output sent, connection closes
?>
```

---

## Web Server Integration

### Apache with PHP

```apache
# Apache configuration enables PHP
LoadModule php_module modules/mod_php.so
AddHandler php-script .php
```

```php
<?php
// When Apache sees .php file, it executes with PHP
// Result is sent to browser
?>
```

### Nginx with PHP-FPM

```nginx
# Nginx delegates PHP to PHP-FPM
location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

```php
<?php
// Nginx passes .php requests to PHP-FPM
// PHP-FPM processes and returns result
?>
```

### Web Server Responsibilities

```php
<?php
// Web server:
// 1. Listens for HTTP requests
// 2. Receives request for .php file
// 3. Invokes PHP interpreter
// 4. Passes request details to PHP
// 5. Collects PHP output
// 6. Sends output as HTTP response

// PHP responsibilities:
// 1. Access request data ($_GET, $_POST, etc.)
// 2. Process request
// 3. Generate output
// 4. Send output to web server
?>
```

---

## Dynamic Content Generation

### Static vs Dynamic

```php
<?php
// STATIC (HTML only)
// index.html always shows same content

// DYNAMIC (PHP)
// index.php shows different content based on:

// - User input
$search = $_GET['q'] ?? '';
echo "Search results for: $search";

// - Current date/time
echo "Current time: " . date('Y-m-d H:i:s');

// - Database content
$stmt = $pdo->query("SELECT * FROM articles");
foreach ($stmt as $article) {
    echo $article['title'];
}

// - User authentication
if (isset($_SESSION['user_id'])) {
    echo "Welcome back, " . $_SESSION['name'];
} else {
    echo "Please log in";
}

// - Random content
echo "Random number: " . rand(1, 100);
?>
```

### Template Example

```php
<?php
// Dynamic template
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
</head>
<body>
    <h1><?php echo $heading; ?></h1>
    
    <?php foreach ($items as $item): ?>
        <div class="item">
            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
            <p><?php echo htmlspecialchars($item['description']); ?></p>
        </div>
    <?php endforeach; ?>
    
    <?php if ($error_message): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
</body>
</html>
```

---

## PHP in Action

### Simple Web Page

```php
<?php
// index.php
$current_user = "John Doe";
$articles = [
    ['title' => 'Article 1', 'date' => '2026-01-06'],
    ['title' => 'Article 2', 'date' => '2026-01-05'],
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Blog</title>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($current_user); ?></h1>
    </header>
    
    <main>
        <h2>Latest Articles</h2>
        <?php foreach ($articles as $article): ?>
            <article>
                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                <p>Published: <?php echo htmlspecialchars($article['date']); ?></p>
            </article>
        <?php endforeach; ?>
    </main>
    
    <footer>
        <p>Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
```

### Form Processing

```php
<?php
// process_form.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    
    // Validate
    if (empty($name) || empty($email)) {
        $error = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email";
    } else {
        // Process form
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
        $success = "Thank you for contacting us!";
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php else: ?>
        <form method="POST" action="">
            <input name="name" required>
            <input name="email" type="email" required>
            <button type="submit">Submit</button>
        </form>
    <?php endif; ?>
</body>
</html>
```

### REST API

```php
<?php
// api/users.php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all users
        $stmt = $pdo->query("SELECT id, name, email FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $users]);
        break;
    
    case 'POST':
        // Create user
        $input = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$input['name'], $input['email']]);
        
        echo json_encode([
            'id' => $pdo->lastInsertId(),
            'message' => 'User created'
        ]);
        break;
    
    case 'PUT':
        // Update user
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            return;
        }
        
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$input['name'], $input['email'], $id]);
        
        echo json_encode(['message' => 'User updated']);
        break;
    
    case 'DELETE':
        // Delete user
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['message' => 'User deleted']);
        break;
    
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
```

---

## Best Practices

### 1. Security

```php
<?php
// Always escape output
echo htmlspecialchars($user_input);
echo htmlentities($user_input);

// Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

// Don't expose sensitive info
$password = 'secret';  // Never echo this
error_log($error_message);  // Log errors server-side
?>
```

### 2. Performance

```php
<?php
// Cache results
$cache_file = 'cache/users.json';
if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600) {
    $users = json_decode(file_get_contents($cache_file), true);
} else {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll();
    file_put_contents($cache_file, json_encode($users));
}

// Use indexes on frequently queried columns
// Limit queries with LIMIT and OFFSET

// Use static resources (HTML, CSS, JS) when possible
?>
```

### 3. Maintainability

```php
<?php
// Separate concerns
// - Models: Database operations
// - Controllers: Business logic
// - Views: HTML templates

// Use functions and classes
function getUserById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Follow naming conventions
// Use consistent indentation
// Add comments for complex logic
?>
```

---

## Complete Application Structure

```
website/
├── index.php              # Entry point
├── config.php             # Configuration
├── functions.php          # Helper functions
├── templates/             # HTML templates
│   ├── header.php
│   ├── footer.php
│   └── home.php
├── api/                   # API endpoints
│   ├── users.php
│   └── articles.php
├── css/                   # Static CSS
├── js/                    # Static JavaScript
└── uploads/              # User uploads
```

---

## See Also

- [Client & Server Architecture](2-client-server.md)
- [PHP Development Server](4-php-development-server.md)
- [Integrating with HTML](8-integrate-with-html.md)
- [Global Variables](9-global-variable-server.md)
