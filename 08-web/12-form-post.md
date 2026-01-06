# POST Form Handling

## Overview

POST requests are used to submit data from HTML forms to the server. This chapter covers how to properly handle POST data, validate inputs, and process form submissions securely.

---

## Table of Contents

1. POST vs GET
2. HTML Form Basics
3. Accessing POST Data
4. Form Validation
5. Processing Form Data
6. File Upload Forms
7. CSRF Protection
8. Complete Examples

---

## POST vs GET

### Comparison

```
                GET                         POST
─────────────────────────────────────────────────────────
URL Parameters  Visible in URL              Hidden in body
Data Size       Limited (~2KB)              Large (MB+)
Security        Less secure                 More secure
Caching         Cached by browsers          Not cached
Idempotent      Yes                         No
Use Case        Retrieving data             Submitting data
History         Saved in browser            Not saved
Encoding        URL encoding                multipart/form-data
```

### When to Use Each

```php
<?php
// GET - Retrieve data, filtering, searching
// /products?category=electronics&sort=price

// POST - Submit sensitive data
// Passwords, payment info, comments

// Rule of Thumb:
// GET = Safe, read-only operations
// POST = Data modification, sensitive data
?>
```

---

## HTML Form Basics

### Basic Form Structure

```html
<form method="POST" action="/process.php">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <input type="email" name="email" placeholder="Email">
    <textarea name="message"></textarea>
    <button type="submit">Submit</button>
</form>
```

### Form Attributes

```html
<!-- method: POST or GET -->
<!-- action: URL to submit to (defaults to current page) -->
<!-- enctype: multipart/form-data for file uploads -->

<form method="POST" action="/login.php" enctype="application/x-www-form-urlencoded">
    <!-- Standard form data -->
</form>

<form method="POST" action="/upload.php" enctype="multipart/form-data">
    <!-- For file uploads -->
    <input type="file" name="upload">
</form>
```

### Input Types

```html
<input type="text" name="username">          <!-- Text input -->
<input type="password" name="password">      <!-- Password (masked) -->
<input type="email" name="email">            <!-- Email validation -->
<input type="number" name="age">             <!-- Number -->
<input type="checkbox" name="agree">         <!-- Checkbox -->
<input type="radio" name="gender" value="m"> <!-- Radio button -->
<select name="country">                       <!-- Dropdown -->
    <option value="us">USA</option>
    <option value="ca">Canada</option>
</select>
<textarea name="message"></textarea>         <!-- Multi-line text -->
```

---

## Accessing POST Data

### Basic Access

```php
<?php
// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Access POST data
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
}
?>
```

### Safe Access

```php
<?php
// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Get POST data with defaults
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';

// Check if specific field exists
if (!isset($_POST['email'])) {
    echo 'Email field missing';
}

// Check if multiple fields present
if (empty($_POST['username']) || empty($_POST['password'])) {
    echo 'Username and password required';
}
?>
```

### All POST Data

```php
<?php
// Get all POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        echo htmlspecialchars($key) . ' => ' . htmlspecialchars($value);
    }
}

// Array of all fields
$all_data = $_POST;

// Filter POST data
$allowed_fields = ['username', 'email', 'message'];
$safe_data = array_filter($_POST, function($k) use ($allowed_fields) {
    return in_array($k, $allowed_fields);
}, ARRAY_FILTER_USE_KEY);
?>
```

---

## Form Validation

### Type Validation

```php
<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Email validation
    $email = $_POST['email'] ?? '';
    if (empty($email)) {
        $errors['email'] = 'Email required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    // Username validation
    $username = $_POST['username'] ?? '';
    if (empty($username)) {
        $errors['username'] = 'Username required';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Username can only contain letters, numbers, underscore';
    }
    
    // Password validation
    $password = $_POST['password'] ?? '';
    if (empty($password)) {
        $errors['password'] = 'Password required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }
    
    // If no errors, process form
    if (empty($errors)) {
        // Save to database
        echo 'Form submitted successfully';
    }
}
?>

<!-- Display errors -->
<?php if (!empty($errors)): ?>
    <div class="errors">
        <?php foreach ($errors as $field => $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
```

### Filter Functions

```php
<?php
// Using PHP's filter functions

$email = $_POST['email'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Invalid email';
}

$url = $_POST['website'] ?? '';
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo 'Invalid URL';
}

$ip = $_POST['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    echo 'Invalid IP address';
}

$int = $_POST['age'] ?? '';
$age = filter_var($int, FILTER_VALIDATE_INT);
if ($age === false) {
    echo 'Invalid integer';
}

// With options
$float = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT, [
    'options' => ['decimal' => '.']
]);
?>
```

### Custom Validation

```php
<?php
class FormValidator {
    private $data;
    private $errors = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function required($field) {
        if (empty($this->data[$field] ?? '')) {
            $this->errors[$field] = ucfirst($field) . ' is required';
        }
        return $this;
    }
    
    public function email($field) {
        if (!empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = 'Invalid email';
            }
        }
        return $this;
    }
    
    public function min_length($field, $length) {
        if (!empty($this->data[$field])) {
            if (strlen($this->data[$field]) < $length) {
                $this->errors[$field] = "Minimum $length characters required";
            }
        }
        return $this;
    }
    
    public function max_length($field, $length) {
        if (!empty($this->data[$field])) {
            if (strlen($this->data[$field]) > $length) {
                $this->errors[$field] = "Maximum $length characters allowed";
            }
        }
        return $this;
    }
    
    public function is_valid() {
        return empty($this->errors);
    }
    
    public function get_errors() {
        return $this->errors;
    }
}

// Usage
$validator = new FormValidator($_POST);
$validator
    ->required('username')
    ->required('email')
    ->email('email')
    ->min_length('password', 8)
    ->max_length('bio', 500);

if (!$validator->is_valid()) {
    $errors = $validator->get_errors();
    // Display errors
}
?>
```

---

## Processing Form Data

### Database Storage

```php
<?php
// Receive and validate
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // Validate
    if (empty($name) || empty($email)) {
        exit('Name and email required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit('Invalid email');
    }
    
    // Store in database (with prepared statement)
    $db = new PDO('sqlite:users.db');
    $stmt = $db->prepare('
        INSERT INTO users (name, email, created_at)
        VALUES (?, ?, NOW())
    ');
    
    $stmt->execute([$name, $email]);
    
    // Redirect on success
    header('Location: /success.php');
    exit;
}
?>
```

### Email Notification

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit('Invalid email');
    }
    
    // Send email
    $to = 'admin@example.com';
    $subject = 'Contact Form Submission';
    $body = "Email: " . htmlspecialchars($email) . "\n";
    $body .= "Message: " . htmlspecialchars($message);
    
    $headers = 'From: noreply@example.com';
    
    if (mail($to, $subject, $body, $headers)) {
        echo 'Email sent successfully';
    } else {
        echo 'Error sending email';
    }
}
?>
```

---

## File Upload Forms

### Basic File Upload

```html
<!-- HTML -->
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="photo">
    <button type="submit">Upload</button>
</form>
```

### Processing File Uploads

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo 'Upload error: ' . $file['error'];
        exit;
    }
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        exit('Invalid file type');
    }
    
    $max_size = 5 * 1024 * 1024;  // 5MB
    if ($file['size'] > $max_size) {
        exit('File too large');
    }
    
    // Move to permanent location
    $upload_dir = '/uploads/';
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        echo 'File uploaded: ' . htmlspecialchars($filename);
    } else {
        echo 'Failed to move file';
    }
}
?>
```

---

## CSRF Protection

### Token Generation

```php
<?php
// Start session
session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include token in form
$token = $_SESSION['csrf_token'];
?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
    <input type="text" name="username">
    <button type="submit">Submit</button>
</form>
```

### Token Validation

```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    $token = $_POST['csrf_token'] ?? '';
    
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('CSRF token invalid');
    }
    
    // Token valid, process form
    $username = $_POST['username'] ?? '';
    // ... process
}
?>
```

---

## Complete Examples

### Login Form

```php
<?php
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        // Get input
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate
        if (empty($username) || empty($password)) {
            $error = 'Username and password required';
        } else {
            // Check credentials (simplified)
            $db = new PDO('sqlite:users.db');
            $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /dashboard');
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        }
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<body>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        
        <div>
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
```

### Contact Form

```php
<?php
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and validate
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate
    if (empty($name)) {
        $errors['name'] = 'Name required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email';
    }
    
    if (empty($subject)) {
        $errors['subject'] = 'Subject required';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Message required';
    }
    
    // If no errors, send email
    if (empty($errors)) {
        $to = 'contact@example.com';
        $headers = "From: " . htmlspecialchars($email) . "\r\n";
        
        if (mail($to, htmlspecialchars($subject), htmlspecialchars($message), $headers)) {
            $success = 'Message sent successfully. Thank you!';
            $_POST = [];  // Clear form
        } else {
            $errors['email'] = 'Error sending email';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
</head>
<body>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        
        <div>
            <label>Subject:</label>
            <input type="text" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
        </div>
        
        <div>
            <label>Message:</label>
            <textarea name="message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        </div>
        
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
```

---

## See Also

- [Global Variables & $_POST](9-global-variable-server.md)
- [XSS Prevention](11-xss-cross-site-scripting.md)
- [HTTP Headers](13-header.md)
- [File Upload](18-upload-file.md)
