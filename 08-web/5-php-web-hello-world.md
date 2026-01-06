# PHP Web Hello World

## Overview

Creating your first PHP web page is simple and foundational. This chapter covers the basics of creating dynamic web pages with PHP.

---

## Table of Contents

1. Basic Hello World
2. HTML Integration
3. Dynamic Content
4. Variables in Web Pages
5. Conditional Output
6. Loops in Templates
7. Forms
8. Complete Examples

---

## Basic Hello World

### Simplest Program

```php
<?php
echo "Hello World";
?>
```

This is the simplest PHP program. When accessed through a web server:

```
Browser Request -> Web Server -> PHP Executes -> "Hello World" -> Browser displays
```

### With HTML

```php
<?php
echo "<h1>Hello World</h1>";
echo "<p>This is my first PHP page</p>";
?>
```

Output in browser:
```
Hello World
This is my first PHP page
```

---

## HTML Integration

### Mixing PHP and HTML

```html
<!DOCTYPE html>
<html>
<head>
    <title>My Page</title>
</head>
<body>
    <h1><?php echo "Hello World"; ?></h1>
    <p>This is a PHP page</p>
    <p><?php echo "Current time: " . date('H:i:s'); ?></p>
</body>
</html>
```

### Short Echo Syntax

```html
<!DOCTYPE html>
<html>
<body>
    <!-- Long form -->
    <h1><?php echo "Title"; ?></h1>
    
    <!-- Short form (recommended) -->
    <h1><?= "Title" ?></h1>
    
    <!-- Variable output -->
    <p><?= $message ?></p>
    
    <!-- Expression output -->
    <p><?= date('Y-m-d') ?></p>
</body>
</html>
```

### Block Structures

```html
<!DOCTYPE html>
<html>
<body>
    <?php
    // PHP block can contain multiple statements
    $name = "John";
    $age = 30;
    $city = "New York";
    ?>
    
    <h1><?= $name ?></h1>
    <p>Age: <?= $age ?></p>
    <p>City: <?= $city ?></p>
</body>
</html>
```

---## Dynamic Content

### Using PHP Variables

```html
<!DOCTYPE html>
<html>
<body>
    <?php
    // Generate dynamic content
    $title = "Welcome to My Page";
    $current_year = date('Y');
    $visitor_count = 1234;
    ?>
    
    <h1><?= $title ?></h1>
    <p>Visitors this year: <?= $visitor_count ?></p>
    <footer>&copy; <?= $current_year ?></footer>
</body>
</html>
```

### Dynamic from User Input

```html
<?php
// Get data from URL
$name = $_GET['name'] ?? 'Guest';
$greeting_type = $_GET['type'] ?? 'Hello';
?>

<!DOCTYPE html>
<html>
<body>
    <h1><?= $greeting_type ?>, <?= htmlspecialchars($name) ?>!</h1>
</body>
</html>
```

Access with:
```
http://localhost:8000/hello.php?name=John&type=Welcome
Output: Welcome, John!
```

---

## Variables in Web Pages

### Simple Variables

```html
<?php
$page_title = "Home";
$author = "John Doe";
$description = "Welcome to my website";
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $page_title ?></title>
</head>
<body>
    <h1><?= $page_title ?></h1>
    <p><?= $description ?></p>
    <p>By <?= $author ?></p>
</body>
</html>
```

### Arrays as Data

```html
<?php
$user = [
    'name' => 'John',
    'email' => 'john@example.com',
    'age' => 30,
];

$skills = ['PHP', 'JavaScript', 'MySQL'];
?>

<!DOCTYPE html>
<html>
<body>
    <h1><?= $user['name'] ?></h1>
    <p>Email: <?= $user['email'] ?></p>
    <p>Age: <?= $user['age'] ?></p>
    
    <h2>Skills:</h2>
    <ul>
        <li><?= $skills[0] ?></li>
        <li><?= $skills[1] ?></li>
        <li><?= $skills[2] ?></li>
    </ul>
</body>
</html>
```

---

## Conditional Output

### If Statements

```html
<?php
$hour = date('H');

if ($hour < 12) {
    $greeting = "Good morning";
} elseif ($hour < 18) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}
?>

<!DOCTYPE html>
<html>
<body>
    <h1><?= $greeting ?></h1>
</body>
</html>
```

### Ternary Operator

```html
<?php
$is_logged_in = false;
$user_status = $is_logged_in ? "Welcome back" : "Please log in";
?>

<!DOCTYPE html>
<html>
<body>
    <p><?= $user_status ?></p>
</body>
</html>
```

### Short Form in HTML

```html
<!DOCTYPE html>
<html>
<body>
    <h1>
        <?php if ($is_admin): ?>
            Admin Dashboard
        <?php else: ?>
            User Dashboard
        <?php endif; ?>
    </h1>
    
    <?php if ($has_messages): ?>
        <div class="alert">You have new messages</div>
    <?php endif; ?>
</body>
</html>
```

---

## Loops in Templates

### foreach Loop

```html
<?php
$articles = [
    ['title' => 'Article 1', 'date' => '2026-01-06'],
    ['title' => 'Article 2', 'date' => '2026-01-05'],
    ['title' => 'Article 3', 'date' => '2026-01-04'],
];
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Articles</h1>
    <ul>
        <?php foreach ($articles as $article): ?>
            <li>
                <h2><?= htmlspecialchars($article['title']) ?></h2>
                <p>Published: <?= $article['date'] ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
```

### for Loop

```html
<?php
$count = 5;
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Countdown</h1>
    <ul>
        <?php for ($i = $count; $i >= 1; $i--): ?>
            <li><?= $i ?></li>
        <?php endfor; ?>
    </ul>
</body>
</html>
```

---

## Forms

### Simple Form

```html
<?php
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    if (!empty($name)) {
        $message = "Hello, " . htmlspecialchars($name) . "!";
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Contact Form</h1>
    
    <?php if ($message): ?>
        <div class="success"><?= $message ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <label>
            Name:
            <input type="text" name="name" required>
        </label>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
```

### Form with Validation

```html
<?php
$errors = [];
$data = ['name' => '', 'email' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name'] = $_POST['name'] ?? '';
    $data['email'] = $_POST['email'] ?? '';
    $data['message'] = $_POST['message'] ?? '';
    
    // Validate
    if (empty($data['name'])) {
        $errors[] = "Name is required";
    }
    
    if (empty($data['email'])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($data['message'])) {
        $errors[] = "Message is required";
    }
    
    // If no errors, process
    if (empty($errors)) {
        // Send email, save to database, etc.
        $success = "Message sent successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <h1>Contact Us</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php else: ?>
        <form method="POST" action="">
            <label>
                Name:
                <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
            </label>
            
            <label>
                Email:
                <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
            </label>
            
            <label>
                Message:
                <textarea name="message" required><?= htmlspecialchars($data['message']) ?></textarea>
            </label>
            
            <button type="submit">Send</button>
        </form>
    <?php endif; ?>
</body>
</html>
```

---

## Complete Examples

### Basic Web Page

```html
<?php
// page.php
$page = [
    'title' => 'Welcome',
    'heading' => 'Welcome to My Website',
    'content' => 'This is the home page',
];
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($page['title']) ?></title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 0 auto; }
        header { background: #333; color: white; padding: 20px; }
        h1 { margin: 0; }
        main { padding: 20px; }
        footer { background: #f0f0f0; padding: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($page['heading']) ?></h1>
    </header>
    
    <main>
        <p><?= htmlspecialchars($page['content']) ?></p>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> My Website</p>
    </footer>
</body>
</html>
```

### Blog Post Page

```html
<?php
// blog_post.php

// Get post ID from URL
$post_id = $_GET['id'] ?? null;

// Fetch post (simplified - would use database)
$posts = [
    1 => [
        'title' => 'Getting Started with PHP',
        'author' => 'John Doe',
        'date' => '2026-01-06',
        'content' => 'PHP is a powerful server-side language...',
    ],
    2 => [
        'title' => 'PHP Best Practices',
        'author' => 'Jane Smith',
        'date' => '2026-01-05',
        'content' => 'Follow these practices for clean code...',
    ],
];

$post = $posts[$post_id] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($post['title'] ?? 'Post Not Found') ?></title>
</head>
<body>
    <?php if ($post): ?>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p>By <?= htmlspecialchars($post['author']) ?> on <?= $post['date'] ?></p>
        <article>
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </article>
    <?php else: ?>
        <h1>Post Not Found</h1>
        <p>The requested post does not exist.</p>
    <?php endif; ?>
</body>
</html>
```

---

## Key Takeaways

1. **PHP runs on server** - output sent to browser
2. **Mix HTML and PHP** - use `<?=` for variables
3. **Escape output** - use `htmlspecialchars()`
4. **Use short tags** - `<?=` for cleaner code
5. **Conditional output** - `<?php if (): ... endif; ?>`
6. **Loop templates** - `<?php foreach (): ... endforeach; ?>`
7. **Always validate** - check user input before using

---

## See Also

- [Integrating with HTML](8-integrate-with-html.md)
- [Global Variables](9-global-variable-server.md)
- [Query Parameters](10-query-parameter.md)
