# Cross-Site Scripting (XSS) Prevention

## Overview

Cross-Site Scripting (XSS) is a security vulnerability where attackers inject malicious scripts into web pages viewed by other users. This chapter covers how to understand XSS attacks and implement effective prevention strategies.

---

## Table of Contents

1. What is XSS
2. Types of XSS Attacks
3. Stored XSS
4. Reflected XSS
5. DOM-Based XSS
6. Prevention Techniques
7. Output Encoding
8. Content Security Policy
9. Complete Examples

---

## What is XSS

### How XSS Works

```
Attacker inserts malicious script
        ↓
Script gets stored or reflected
        ↓
User's browser executes script
        ↓
Attacker steals data, session, credentials
```

### Example Attack

```php
<?php
// VULNERABLE CODE - Don't do this!
$comment = $_POST['comment'];  // "<script>alert('XSS')</script>"
echo $comment;  // Renders script tag - executes JavaScript!
?>

<!-- User sees alert box -->
<!-- Attacker could have stolen cookies instead -->
```

### Consequences

```
- Steal session cookies
- Capture user input (passwords, credit cards)
- Redirect to malicious sites
- Deface website content
- Launch attacks on other users
- Spread malware
- Phishing attacks
```

---

## Types of XSS Attacks

### Stored XSS (Persistent)

```
1. Attacker submits malicious script through form
2. Server stores it in database without sanitizing
3. When other users view page, script executes
4. Affects ALL users who view the page
```

### Reflected XSS (Non-Persistent)

```
1. Attacker crafts URL with malicious script
2. User clicks link: /search?q=<script>alert('XSS')</script>
3. Server reflects parameter in page without encoding
4. User's browser executes script
5. Only affects users who click the malicious link
```

### DOM-Based XSS

```
1. JavaScript code in page dynamically uses user input
2. Input not properly validated/sanitized in JavaScript
3. Malicious script injected through DOM manipulation
4. Happens entirely in client-side JavaScript
```

---

## Stored XSS

### Vulnerable Code

```php
<?php
// VULNERABLE - stores without sanitizing

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'];
    
    // Direct storage - DANGEROUS!
    $db = new PDO('sqlite:db.sqlite');
    $stmt = $db->prepare('INSERT INTO comments (content) VALUES (?)');
    $stmt->execute([$comment]);
}

// Retrieve and display
$comments = $db->query('SELECT * FROM comments')->fetchAll();

foreach ($comments as $comment) {
    echo '<p>' . $comment['content'] . '</p>';  // Raw output - DANGEROUS!
}
?>
```

Attack scenario:
```
1. Attacker posts: "<img src=x onerror='alert(\"XSS\")'>"
2. Gets stored in database
3. Displayed to all users without escaping
4. Executes for every user viewing the page
```

### Secure Code

```php
<?php
// SECURE - escaped output

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'];
    
    // Store as-is (don't sanitize input)
    $db = new PDO('sqlite:db.sqlite');
    $stmt = $db->prepare('INSERT INTO comments (content) VALUES (?)');
    $stmt->execute([$comment]);
}

// Retrieve and display with escaping
$comments = $db->query('SELECT * FROM comments')->fetchAll();

foreach ($comments as $comment) {
    echo '<p>' . htmlspecialchars($comment['content']) . '</p>';
}
?>
```

---

## Reflected XSS

### Vulnerable Code

```php
<?php
// VULNERABLE - reflects user input without escaping

$search = $_GET['q'] ?? '';

echo "Search results for: " . $search;  // DANGEROUS!
?>

<!-- URL: /search.php?q=<script>alert('XSS')</script> -->
```

### Secure Code

```php
<?php
// SECURE - escapes reflected output

$search = $_GET['q'] ?? '';

echo "Search results for: " . htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
?>

<!-- URL: /search.php?q=<script>alert('XSS')</script> -->
<!-- Output: Search results for: &lt;script&gt;alert('XSS')&lt;/script&gt; -->
```

---

## DOM-Based XSS

### Vulnerable JavaScript

```javascript
// VULNERABLE - directly uses user input in DOM

var comment = new URLSearchParams(window.location.search).get('comment');
document.getElementById('comments').innerHTML = comment;  // DANGEROUS!
```

Attack URL:
```
/page.html?comment=<img src=x onerror='stealCookies()'>
```

### Secure JavaScript

```javascript
// SECURE - uses textContent for text-only content

var comment = new URLSearchParams(window.location.search).get('comment');
document.getElementById('comments').textContent = comment;

// Or sanitize before using innerHTML
function sanitize(html) {
    var div = document.createElement('div');
    div.textContent = html;
    return div.innerHTML;
}
var safe_comment = sanitize(comment);
document.getElementById('comments').innerHTML = safe_comment;
```

---

## Prevention Techniques

### Input Validation (Not Sufficient Alone!)

```php
<?php
// Input validation helps but is NOT primary defense
// Attack vectors are too numerous to block them all

// Example: Block script tags
$comment = $_POST['comment'];

if (strpos($comment, '<script') !== false) {
    echo 'Invalid input';
    exit;
}

// BYPASS: <Script>, <SCRIPT>, <sCrIpT>
// BYPASS: <iframe>, <img>, <svg>, <body onload=...>
// Too many variants to block!

// BETTER: Output encoding instead
?>
```

### Output Encoding (PRIMARY DEFENSE)

```php
<?php
// Always encode output based on context

// HTML context
$user_input = $_POST['comment'];
echo '<p>' . htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8') . '</p>';

// JavaScript context
$data = json_encode($user_input);
echo '<script>var comment = ' . $data . ';</script>';

// URL context
$redirect = $_GET['url'];
echo '<a href="' . htmlspecialchars($redirect) . '">Link</a>';

// CSS context
$color = $_GET['color'];
echo '<div style="color: ' . preg_replace('/[^a-f0-9#]/', '', $color) . '"></div>';
?>
```

### htmlspecialchars()

```php
<?php
// Convert special characters to HTML entities

$user_input = '<script>alert("XSS")</script>';

// Basic
echo htmlspecialchars($user_input);
// &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;

// With ENT_QUOTES (encode both double and single quotes)
echo htmlspecialchars($user_input, ENT_QUOTES);
// &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;

// With charset
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// Recommended: Always use this combination
function safe_html($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

echo safe_html('<script>alert("XSS")</script>');
?>
```

### strip_tags()

```php
<?php
// Remove HTML/PHP tags (allows some tags)

$text = '<p>Hello <b>world</b><script>alert("XSS")</script></p>';

// Remove all tags
echo strip_tags($text);
// Hello worldalert("XSS")  -- Note: script content still visible!

// Allow certain tags
echo strip_tags($text, '<p><b>');
// <p>Hello <b>world</b>alert("XSS")</p>

// CAUTION: strip_tags is not sufficient for XSS prevention
// Use htmlspecialchars() instead
?>
```

---

## Content Security Policy

### CSP Headers

```php
<?php
// Set CSP header to restrict what scripts can run

header("Content-Security-Policy: default-src 'self'");
header("Content-Security-Policy: script-src 'self' cdn.example.com");
header("Content-Security-Policy: style-src 'self' 'unsafe-inline'");

// Strict policy - only same-origin
header("Content-Security-Policy: default-src 'self'");

// Allow specific domains
header("Content-Security-Policy: 
    script-src 'self' https://cdn.example.com;
    style-src 'self' https://fonts.googleapis.com;
    img-src 'self' https:;
    font-src 'self' https://fonts.gstatic.com;
    connect-src 'self' https://api.example.com;
");

// Report violations to endpoint
header("Content-Security-Policy: default-src 'self'; report-uri /csp-report.php");
?>
```

### CSP Meta Tag

```html
<meta http-equiv="Content-Security-Policy" content="default-src 'self'">
```

---

## Complete Examples

### Comment System (Secure)

```php
<?php
// comment.php - Secure comment submission

session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Must be logged in');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and validate input
    $comment = $_POST['comment'] ?? '';
    $post_id = $_POST['post_id'] ?? null;
    
    // Validate
    if (empty($comment)) {
        $error = 'Comment required';
    } elseif (strlen($comment) > 5000) {
        $error = 'Comment too long';
    } elseif (empty($post_id) || !is_numeric($post_id)) {
        $error = 'Invalid post';
    } else {
        // Store in database (comment is stored as-is)
        $db = new PDO('sqlite:db.sqlite');
        $stmt = $db->prepare('
            INSERT INTO comments (post_id, user_id, content, created_at)
            VALUES (?, ?, ?, NOW())
        ');
        
        $stmt->execute([
            (int) $post_id,
            $_SESSION['user_id'],
            $comment  // Store original
        ]);
        
        header('Location: /posts/' . (int) $post_id . '#comments');
        exit;
    }
}

// Display comments (always escape output)
$comments = $db->query('
    SELECT c.*, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    ORDER BY c.created_at DESC
')->fetchAll();
?>

<!DOCTYPE html>
<html>
<body>
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="comments">
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <strong><?= htmlspecialchars($comment['username']) ?></strong>
                <time><?= htmlspecialchars($comment['created_at']) ?></time>
                <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    
    <form method="POST">
        <textarea name="comment" required></textarea>
        <input type="hidden" name="post_id" value="<?= htmlspecialchars($_GET['id']) ?>">
        <button type="submit">Submit Comment</button>
    </form>
</body>
</html>
```

### Search Results (Secure)

```php
<?php
// search.php - Secure search display

$query = $_GET['q'] ?? '';

// Validate
if (empty($query)) {
    http_response_code(400);
    exit('Search query required');
}

if (strlen($query) > 200) {
    http_response_code(400);
    exit('Search query too long');
}

// Search database with prepared statement
$db = new PDO('sqlite:db.sqlite');
$stmt = $db->prepare('
    SELECT * FROM posts 
    WHERE title LIKE ? OR content LIKE ?
    LIMIT 50
');

$search_term = '%' . $query . '%';
$stmt->execute([$search_term, $search_term]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'">
</head>
<body>
    <h1>
        Search Results for:
        <strong><?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?></strong>
    </h1>
    
    <p><?= htmlspecialchars(count($results)) ?> results found</p>
    
    <div class="results">
        <?php foreach ($results as $result): ?>
            <article>
                <h2>
                    <a href="/posts/<?= htmlspecialchars($result['id']) ?>">
                        <?= htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h2>
                <p><?= htmlspecialchars(substr($result['content'], 0, 200)) ?></p>
                <small>
                    <?= htmlspecialchars(date('Y-m-d', strtotime($result['created_at']))) ?>
                </small>
            </article>
        <?php endforeach; ?>
    </div>
</body>
</html>
```

---

## Key Takeaways

**Prevention Checklist:**

1. ✅ Always encode output using `htmlspecialchars()`
2. ✅ Use prepared statements for database queries
3. ✅ Implement Content Security Policy headers
4. ✅ Never trust user input
5. ✅ Validate and sanitize data at entry points
6. ✅ Use security headers (`X-Frame-Options`, etc.)
7. ✅ Keep frameworks and libraries updated
8. ✅ Use HTTPS only
9. ✅ Set secure cookie flags
10. ✅ Regular security testing

---

## See Also

- [Form POST Handling](12-form-post.md)
- [Global Variables](9-global-variable-server.md)
- [Sessions](15-session.md)
- [HTTP Headers](13-header.md)
