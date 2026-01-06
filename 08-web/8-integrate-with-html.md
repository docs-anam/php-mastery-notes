# Integrating PHP with HTML

## Overview

PHP and HTML work together to create dynamic web pages. This chapter covers techniques and best practices for properly integrating PHP with HTML to build responsive, maintainable web applications.

---

## Table of Contents

1. PHP in HTML
2. HTML Generation from PHP
3. Template Patterns
4. Form Integration
5. Data Display
6. Dynamic Attributes
7. Common Patterns
8. Complete Examples

---

## PHP in HTML

### Embedding PHP in HTML

```html
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
</head>
<body>
    <h1><?php echo "Welcome"; ?></h1>
    <p><?php echo $welcome_message; ?></p>
</body>
</html>
```

### Short Echo Syntax

```html
<?php
$name = "John";
$age = 30;
?>

<!DOCTYPE html>
<html>
<body>
    <!-- Using short echo syntax -->
    <h1>Welcome <?= $name ?></h1>
    <p>Age: <?= $age ?></p>
    
    <!-- Equivalent to:
    <h1>Welcome <?php echo $name; ?></h1>
    <p>Age: <?php echo $age; ?></p>
    -->
</body>
</html>
```

### Separating PHP Logic and HTML

```php
<?php
// index.php - Business logic

// Database query
$db = new PDO('sqlite:database.db');
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_GET['id']]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit('User not found');
}

// Include template
include 'templates/user.html.php';
?>

<!-- templates/user.html.php - HTML presentation -->
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($user['name']) ?>'s Profile</title>
</head>
<body>
    <h1><?= htmlspecialchars($user['name']) ?></h1>
    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
    <p>Joined: <?= date('Y-m-d', strtotime($user['created_at'])) ?></p>
</body>
</html>
```

---

## HTML Generation from PHP

### Simple HTML Generation

```php
<?php
// Generate list of items
$items = ['Apple', 'Banana', 'Orange'];

echo '<ul>';
foreach ($items as $item) {
    echo '<li>' . htmlspecialchars($item) . '</li>';
}
echo '</ul>';

// Output:
// <ul>
// <li>Apple</li>
// <li>Banana</li>
// <li>Orange</li>
// </ul>
?>
```

### Building Complex HTML

```php
<?php
class HTMLBuilder {
    private $tag;
    private $attributes = [];
    private $content = '';
    private $children = [];
    
    public function __construct($tag) {
        $this->tag = $tag;
    }
    
    public function attr($key, $value) {
        $this->attributes[$key] = $value;
        return $this;
    }
    
    public function text($content) {
        $this->content = htmlspecialchars($content);
        return $this;
    }
    
    public function add(HTMLBuilder $child) {
        $this->children[] = $child;
        return $this;
    }
    
    public function render() {
        $attrs = '';
        foreach ($this->attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        $html = '<' . $this->tag . $attrs . '>';
        
        $html .= $this->content;
        foreach ($this->children as $child) {
            $html .= $child->render();
        }
        
        $html .= '</' . $this->tag . '>';
        
        return $html;
    }
}

// Usage
$div = new HTMLBuilder('div');
$div->attr('class', 'container')
    ->attr('id', 'main');

$h1 = new HTMLBuilder('h1');
$h1->text('Welcome');
$div->add($h1);

$p = new HTMLBuilder('p');
$p->text('This is a paragraph');
$div->add($p);

echo $div->render();
// <div class="container" id="main">
//   <h1>Welcome</h1>
//   <p>This is a paragraph</p>
// </div>
?>
```

---

## Template Patterns

### Simple Template

```html
<?php
// Render product template
$product = [
    'id' => 1,
    'name' => 'Laptop',
    'price' => 999.99,
    'description' => 'High-performance laptop',
    'in_stock' => true,
];
?>

<!DOCTYPE html>
<html>
<body>
    <div class="product">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p class="description">
            <?= htmlspecialchars($product['description']) ?>
        </p>
        <p class="price">
            $<?= number_format($product['price'], 2) ?>
        </p>
        <?php if ($product['in_stock']): ?>
            <button>Add to Cart</button>
        <?php else: ?>
            <p class="out-of-stock">Out of Stock</p>
        <?php endif; ?>
    </div>
</body>
</html>
```

### Template with Loops

```php
<?php
$products = [
    ['id' => 1, 'name' => 'Laptop', 'price' => 999.99],
    ['id' => 2, 'name' => 'Mouse', 'price' => 25.99],
    ['id' => 3, 'name' => 'Keyboard', 'price' => 79.99],
];
?>

<!DOCTYPE html>
<html>
<body>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>$<?= number_format($product['price'], 2) ?></td>
                    <td>
                        <a href="/products/<?= $product['id'] ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
```

### Template with Inheritance

```php
<?php
// base.html.php - Base template

class Template {
    protected $vars = [];
    
    public function set($key, $value) {
        $this->vars[$key] = $value;
        return $this;
    }
    
    protected function block($name) {
        // Override in child classes
    }
    
    public function render() {
        extract($this->vars);
        ob_start();
        include $this->template_file;
        return ob_get_clean();
    }
}

// Example with yield-style blocks
class PageTemplate extends Template {
    private $blocks = [];
    
    public function block($name, $content) {
        $this->blocks[$name] = $content;
    }
    
    public function get_block($name) {
        return $this->blocks[$name] ?? '';
    }
}

// Layout
$page = new PageTemplate();
$page->block('title', 'Home');
$page->block('content', '<p>Welcome home</p>');
?>
```

---

## Form Integration

### HTML Form with PHP Processing

```html
<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (empty($name) || empty($email)) {
        $error = 'All fields required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        // Process form
        // save to database, send email, etc.
        $success = 'Form submitted successfully';
    }
}
?>

<!DOCTYPE html>
<html>
<body>
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div>
            <label>Name:</label>
            <input type="text" name="name" 
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        
        <div>
            <label>Email:</label>
            <input type="email" name="email" 
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        
        <button type="submit">Submit</button>
    </form>
</body>
</html>
```

### Dynamic Form Fields

```html
<?php
$countries = ['USA', 'Canada', 'Mexico', 'UK', 'Australia'];
$selected_country = $_POST['country'] ?? '';
?>

<form method="POST">
    <label>Country:</label>
    <select name="country">
        <option value="">-- Select --</option>
        <?php foreach ($countries as $country): ?>
            <option value="<?= htmlspecialchars($country) ?>"
                    <?= ($selected_country === $country) ? 'selected' : '' ?>>
                <?= htmlspecialchars($country) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Submit</button>
</form>
```

---

## Data Display

### Lists

```html
<?php
$users = [
    ['id' => 1, 'name' => 'John', 'role' => 'Admin'],
    ['id' => 2, 'name' => 'Jane', 'role' => 'User'],
    ['id' => 3, 'name' => 'Bob', 'role' => 'User'],
];
?>

<ul class="users">
    <?php foreach ($users as $user): ?>
        <li>
            <strong><?= htmlspecialchars($user['name']) ?></strong>
            (<?= htmlspecialchars($user['role']) ?>)
        </li>
    <?php endforeach; ?>
</ul>
```

### Cards

```html
<?php
$posts = [
    ['title' => 'First Post', 'excerpt' => 'Hello world', 'date' => '2026-01-15'],
    ['title' => 'Second Post', 'excerpt' => 'More content', 'date' => '2026-01-20'],
];
?>

<div class="posts">
    <?php foreach ($posts as $post): ?>
        <div class="card">
            <h3><?= htmlspecialchars($post['title']) ?></h3>
            <p><?= htmlspecialchars($post['excerpt']) ?></p>
            <small><?= htmlspecialchars($post['date']) ?></small>
        </div>
    <?php endforeach; ?>
</div>
```

---

## Dynamic Attributes

### Building Attributes

```html
<?php
function build_attr($attrs) {
    $result = '';
    foreach ($attrs as $key => $value) {
        if ($value === true) {
            $result .= ' ' . $key;
        } else if ($value !== false && $value !== null) {
            $result .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
    }
    return $result;
}

// Usage
$button_attrs = [
    'type' => 'submit',
    'class' => 'btn btn-primary',
    'disabled' => !$form_valid,
    'data-action' => 'submit',
];
?>

<button<?= build_attr($button_attrs) ?>>Submit</button>
```

### CSS Classes

```html
<?php
$is_active = true;
$has_error = false;

function classes(...$names) {
    return implode(' ', array_filter($names, function($n) { 
        return !empty($n); 
    }));
}

// Usage
$css_class = classes(
    'btn',
    'btn-lg',
    $is_active ? 'btn-active' : '',
    $has_error ? 'btn-error' : ''
);
?>

<button class="<?= $css_class ?>">Click me</button>
<!-- class="btn btn-lg btn-active" -->
```

---

## Complete Examples

### Blog Post Display

```html
<?php
// Fetch post from database
$post = [
    'title' => 'Getting Started with PHP',
    'author' => 'John Doe',
    'date' => '2026-01-15',
    'content' => 'PHP is a server-side scripting language...',
    'tags' => ['php', 'web', 'tutorial'],
];
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($post['title']) ?></title>
</head>
<body>
    <article class="post">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        
        <div class="meta">
            <span class="author">By <?= htmlspecialchars($post['author']) ?></span>
            <span class="date"><?= htmlspecialchars($post['date']) ?></span>
        </div>
        
        <div class="content">
            <?php 
            // Escape HTML in content unless you trust the source
            echo nl2br(htmlspecialchars($post['content'])); 
            ?>
        </div>
        
        <div class="tags">
            <?php foreach ($post['tags'] as $tag): ?>
                <a href="/posts/tag/<?= urlencode($tag) ?>" class="tag">
                    <?= htmlspecialchars($tag) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </article>
</body>
</html>
```

### Product Listing Page

```html
<?php
// Controller logic
$page = $_GET['page'] ?? 1;
$per_page = 10;

// Get products from database
$products = [
    ['id' => 1, 'name' => 'Laptop', 'price' => 999.99, 'in_stock' => true],
    ['id' => 2, 'name' => 'Mouse', 'price' => 25.99, 'in_stock' => true],
    ['id' => 3, 'name' => 'Keyboard', 'price' => 79.99, 'in_stock' => false],
];

$total = count($products);
$pages = ceil($total / $per_page);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <style>
        .product { border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
        .product.out-of-stock { opacity: 0.6; }
        .pagination a { margin: 0 5px; }
    </style>
</head>
<body>
    <h1>Products</h1>
    
    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product <?= $product['in_stock'] ? '' : 'out-of-stock' ?>">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p>$<?= number_format($product['price'], 2) ?></p>
                <?php if ($product['in_stock']): ?>
                    <button>Add to Cart</button>
                <?php else: ?>
                    <p>Out of Stock</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?page=<?= $i ?>" 
               class="<?= ($i === (int)$page) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</body>
</html>
```

---

## See Also

- [PHP Web Development](3-php-web.md)
- [Hello World & Basics](5-php-web-hello-world.md)
- [Global Variables & $_SERVER](9-global-variable-server.md)
