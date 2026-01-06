# Views and Template Rendering

## Overview

Create views for displaying data, using templates, and rendering HTML with proper separation of presentation logic.

---

## Table of Contents

1. What are Views
2. Basic Views
3. View Helpers
4. Template Inheritance
5. Loops and Conditionals
6. Passing Data
7. Complete Examples

---

## What are Views

### Purpose

```
View = Presentation Layer

Responsibilities:
- Render HTML
- Display data
- User interface
- Form rendering
- Template processing
```

### View File Structure

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.php
│   │   ├── header.php
│   │   └── footer.php
│   ├── user/
│   │   ├── index.php
│   │   ├── show.php
│   │   ├── create.php
│   │   └── edit.php
│   ├── product/
│   │   └── show.php
│   └── home/
│       └── index.php
```

---

## Basic Views

### Simple View

```php
<?php
// resources/views/user/show.php

?>
<div class="user-profile">
    <h1><?php echo htmlspecialchars($user->name); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user->email); ?></p>
    <p>Member since: <?php echo $user->created_at; ?></p>
    
    <a href="/users/<?php echo $user->id; ?>/edit">Edit</a>
    <a href="/users">Back</a>
</div>
```

### View with Escaping

```php
<?php
// Unsafe
<h1><?php echo $user->name; ?></h1>

// Safe
<h1><?php echo htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></h1>

// Using helper
<h1><?php echo escape($user->name); ?></h1>

// Helper function
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
```

---

## View Helpers

### Common Helpers

```php
<?php

function view($path, $data = []) {
    // Extract data into variables
    extract($data);
    
    // Start output buffering
    ob_start();
    
    // Include view file
    $viewPath = BASE_PATH . "/resources/views/{$path}.php";
    require $viewPath;
    
    // Return rendered content
    return ob_get_clean();
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function route($name, $params = []) {
    $routes = [
        'user.show' => '/users/{id}',
        'user.edit' => '/users/{id}/edit',
    ];
    
    $url = $routes[$name];
    foreach ($params as $key => $value) {
        $url = str_replace('{' . $key . '}', $value, $url);
    }
    
    return $url;
}

function url($path) {
    return '/' . ltrim($path, '/');
}

function asset($path) {
    return url('/assets/' . $path);
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function old($field, $default = '') {
    return $_POST[$field] ?? $default;
}
```

---

## Template Inheritance

### Layout System

```php
<?php
// resources/views/layouts/app.php

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'My App'; ?></title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main>
        <?php echo $content; ?>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="<?php echo asset('js/app.js'); ?>"></script>
</body>
</html>
```

### View with Layout

```php
<?php
// resources/views/user/show.php

$title = 'User: ' . $user->name;

ob_start();
?>

<div class="user-profile">
    <h1><?php echo escape($user->name); ?></h1>
    <p><?php echo escape($user->email); ?></p>
</div>

<?php
$content = ob_get_clean();

include BASE_PATH . '/resources/views/layouts/app.php';
```

---

## Loops and Conditionals

### Rendering Lists

```php
<?php
// resources/views/user/index.php

?>
<div class="users-list">
    <h1>Users</h1>
    
    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo escape($user->name); ?></td>
                        <td><?php echo escape($user->email); ?></td>
                        <td>
                            <a href="<?php echo route('user.show', ['id' => $user->id]); ?>">View</a>
                            <a href="<?php echo route('user.edit', ['id' => $user->id]); ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
```

### Conditional Rendering

```php
<?php
// resources/views/product/show.php

?>
<div class="product">
    <h1><?php echo escape($product->name); ?></h1>
    
    <p>Price: $<?php echo number_format($product->price, 2); ?></p>
    
    <?php if ($product->stock > 0): ?>
        <button>Add to Cart</button>
    <?php else: ?>
        <p class="out-of-stock">Out of Stock</p>
    <?php endif; ?>
    
    <?php if (!empty($product->description)): ?>
        <h3>Description</h3>
        <p><?php echo escape($product->description); ?></p>
    <?php endif; ?>
</div>
```

---

## Passing Data

### Passing Data from Controller

```php
<?php
// Controller
public function show($id) {
    $user = $this->user->find($id);
    
    return view('user.show', [
        'user' => $user,
        'title' => 'User Profile',
    ]);
}

// View receives $user and $title
<h1><?php echo $title; ?></h1>
<p><?php echo $user->name; ?></p>
```

### Passing Nested Data

```php
<?php
// Controller
public function show($id) {
    $post = $this->post->find($id);
    $author = $post->author();
    $comments = $post->comments();
    
    return view('post.show', [
        'post' => $post,
        'author' => $author,
        'comments' => $comments,
        'total_comments' => count($comments),
    ]);
}

// View
<article>
    <h1><?php echo escape($post->title); ?></h1>
    <p>By <?php echo escape($author->name); ?></p>
    <p><?php echo escape($post->content); ?></p>
    
    <h3>Comments (<?php echo $total_comments; ?>)</h3>
    
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <strong><?php echo escape($comment->author); ?>:</strong>
            <p><?php echo escape($comment->text); ?></p>
        </div>
    <?php endforeach; ?>
</article>
```

---

## Complete Examples

### Example 1: User Management Views

```php
<?php
// resources/views/user/index.php

?>
<div class="container">
    <h1>Users</h1>
    
    <a href="/users/create" class="btn btn-primary">Add User</a>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo escape($message); ?>
        </div>
    <?php endif; ?>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user->id; ?></td>
                    <td><?php echo escape($user->name); ?></td>
                    <td><?php echo escape($user->email); ?></td>
                    <td>
                        <a href="/users/<?php echo $user->id; ?>">View</a>
                        <a href="/users/<?php echo $user->id; ?>/edit">Edit</a>
                        <form method="POST" action="/users/<?php echo $user->id; ?>" style="display:inline;">
                            <input type="hidden" name="method" value="DELETE">
                            <button type="submit" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

### Example 2: Form View

```php
<?php
// resources/views/user/create.php

?>
<div class="container">
    <h1>Create User</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $field => $message): ?>
                    <li><?php echo escape($message); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="/users">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?php echo old('name'); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo old('email'); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="/users" class="btn btn-secondary">Cancel</a>
    </form>
</div>
```

---

## Key Takeaways

**View Checklist:**

1. ✅ Separate presentation from logic
2. ✅ Escape all user input
3. ✅ Use consistent helper functions
4. ✅ Implement layout inheritance
5. ✅ Keep views focused and simple
6. ✅ Pass necessary data from controller
7. ✅ Use meaningful variable names
8. ✅ Handle empty states
9. ✅ Render conditional content
10. ✅ Avoid business logic in views

---

## See Also

- [MVC Basics](0-mvc-basics.md)
- [Controllers](6-controller.md)
- [Models](8-model.md)
