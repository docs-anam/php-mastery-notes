# PHP Best Practice URLs

## Overview

Proper URL structure and naming conventions are essential for SEO, usability, and maintainability. This chapter covers best practices for creating effective URLs in PHP applications.

---

## Table of Contents

1. URL Structure Best Practices
2. SEO-Friendly URLs
3. URL Encoding
4. Query Parameters
5. REST API URLs
6. URL Rewriting
7. Security Considerations
8. Complete Examples

---

## URL Structure Best Practices

### Readable URLs

```php
<?php
// GOOD - Clear, readable URLs
// /blog/getting-started-with-php
// /users/profile/john-doe
// /products/category/electronics/item/laptop

// BAD - Unclear, unreadable URLs
// /page.php?id=123&type=blog
// /user.php?u=456
// /p.php?c=5&i=789
?>
```

### Hierarchy and Structure

```
Root
  /users               - User listing
  /users/123           - Specific user
  /users/123/profile   - User profile
  /users/123/settings  - User settings
  
  /blog                - Blog listing
  /blog/2026           - Posts by year
  /blog/2026/01        - Posts by month
  /blog/getting-started - Specific post
  
  /api/v1              - API version 1
  /api/v1/users        - API endpoint
  /api/v1/users/123    - API specific resource
```

### Consistency

```php
<?php
// Use consistent naming conventions
// GOOD
/users/john
/users/jane
/users/bob

// BAD (inconsistent)
/users/john
/user/jane
/Users/bob

// GOOD
/posts/first-post
/posts/second-post
/posts/third-post

// BAD (inconsistent)
/posts/first-post
/post/second_post
/Post/ThirdPost
?>
```

---

## SEO-Friendly URLs

### Descriptive Keywords

```php
<?php
// GOOD - Contains keywords
// /blog/php-best-practices
// /products/iphone-13-pro-max
// /guides/how-to-use-php-pdo

// BAD - No descriptive keywords
// /blog/post123
// /products/item456
// /guides/article789

// Why it matters:
// - Search engines rank by URL keywords
// - Users can guess content from URL
// - Memorable and shareable
?>
```

### Lowercase and Hyphens

```php
<?php
// GOOD - Lowercase with hyphens
// /blog/getting-started-with-php
// /users/john-doe
// /products/iphone-13-pro

// BAD - Mixed case
// /blog/GettingStartedWithPHP
// /users/JohnDoe
// /products/iPhone13Pro

// BAD - Underscores
// /blog/getting_started_with_php
// /users/john_doe

// Standards: RFC 3986 recommends lowercase and hyphens
?>
```

### Avoid Dynamic Parameters When Possible

```php
<?php
// GOOD - Static-looking URLs
// /products/electronics/laptop
// /users/jane-doe
// /blog/2026/01/getting-started

// BAD - Dynamic parameters
// /products.php?category=5&item=123
// /user.php?id=456
// /blog.php?year=2026&month=01

// Implementation: Use URL rewriting
?>
```

---

## URL Encoding

### Special Characters

```php
<?php
// URLs can only contain certain characters
// Other characters must be encoded

// Examples:
// Space: %20
// #: %23
// $: %24
// &: %26
// %: %25
// +: %2B
// /: %2F
// =: %3D

// PHP functions
echo urlencode("hello world");      // hello%20world
echo urldecode("hello%20world");    // hello world

echo urlencode("john@example.com"); // john%40example.com
echo rawurlencode("hello world");   // hello%20world (stricter)

// Building URLs
$search = "php database";
$url = "/search.php?q=" . urlencode($search);
// /search.php?q=php%20database
?>
```

### Encoding in Links

```html
<?php
// Properly encode URLs in HTML
$search_term = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';
$next_page = 2;
?>

<!DOCTYPE html>
<html>
<body>
    <!-- Build URL with encoded parameters -->
    <a href="<?= htmlspecialchars('/search.php?q=' . urlencode($search_term) . '&category=' . urlencode($category)) ?>">
        Search Again
    </a>
    
    <!-- Next page link -->
    <a href="/products?page=<?= urlencode($next_page) ?>">Next</a>
    
    <!-- Multiple parameters -->
    <a href="/filter?
        category=<?= urlencode($category) ?>
        &price_min=<?= urlencode($price_min) ?>
        &price_max=<?= urlencode($price_max) ?>
        &sort=<?= urlencode($sort) ?>">
        Filter Results
    </a>
</body>
</html>
```

---

## Query Parameters

### Best Practices

```php
<?php
// Short parameter names
// /search?q=php          (good)
// /search?query=php      (okay)
// /search?search_query=php (too long)

// Meaningful parameter names
// /users?sort=name       (clear)
// /users?s=n             (unclear)

// Consistent naming
// /products?sort=name&filter=category  (consistent)
// /products?sort_by=name&filter_by=category  (consistent but verbose)

// Order doesn't matter in theory, but be consistent
// /search?q=php&page=2
// vs
// /search?page=2&q=php
// (choose one and stick with it)
?>
```

### URL Building Helpers

```php
<?php
class URLBuilder {
    private $base_url;
    private $params = [];
    
    public function __construct($base_url) {
        $this->base_url = $base_url;
    }
    
    public function add($key, $value) {
        $this->params[$key] = $value;
        return $this;
    }
    
    public function build() {
        if (empty($this->params)) {
            return $this->base_url;
        }
        
        $encoded = [];
        foreach ($this->params as $key => $value) {
            $encoded[] = urlencode($key) . '=' . urlencode($value);
        }
        
        return $this->base_url . '?' . implode('&', $encoded);
    }
}

// Usage
$url = new URLBuilder('/products');
$url->add('category', 'electronics')
    ->add('sort', 'price')
    ->add('order', 'asc');

echo $url->build();
// /products?category=electronics&sort=price&order=asc
?>
```

---

## REST API URLs

### RESTful Conventions

```php
<?php
// Resource: /users

// GET /users              - List all users
// POST /users             - Create user
// GET /users/123          - Get user 123
// PUT /users/123          - Update user 123
// DELETE /users/123       - Delete user 123
// PATCH /users/123        - Partial update

// Resource: /posts

// GET /posts              - List all posts
// POST /posts             - Create post
// GET /posts/123          - Get post 123
// PUT /posts/123          - Update post 123
// DELETE /posts/123       - Delete post 123

// Nested resources

// GET /users/123/posts    - Get user's posts
// POST /users/123/posts   - Create post for user
// GET /users/123/posts/456 - Get specific user's post
// PUT /users/123/posts/456 - Update specific user's post
// DELETE /users/123/posts/456 - Delete specific user's post
?>
```

### API URL Examples

```php
<?php
// Pagination
// /users?page=2&per_page=20

// Filtering
// /users?status=active&role=admin

// Searching
// /users?search=john

// Sorting
// /users?sort=name&order=asc

// Including related data
// /users/123?include=posts,comments

// API versioning
// /api/v1/users
// /api/v2/users

// Combining
// /api/v1/users?page=2&sort=created_at&order=desc&status=active
?>
```

---

## URL Rewriting

### Using .htaccess (Apache)

```apache
# .htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Rewrite /users/john to /user.php?name=john
    RewriteRule ^users/([a-zA-Z0-9_-]+)/?$ user.php?name=$1 [QSA,L]
    
    # Rewrite /posts/123 to /post.php?id=123
    RewriteRule ^posts/([0-9]+)/?$ post.php?id=$1 [QSA,L]
    
    # Rewrite /api/v1/... to /api/index.php
    RewriteRule ^api/v([0-9]+)/(.*)$ api/index.php?version=$1&path=$2 [QSA,L]
</IfModule>
```

### Using Router Script (PHP Dev Server)

```php
<?php
// router.php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Match pattern /users/john
if (preg_match('#^/users/([a-zA-Z0-9_-]+)$#', $uri, $matches)) {
    $_GET['name'] = $matches[1];
    require 'user.php';
    exit;
}

// Match pattern /posts/123
if (preg_match('#^/posts/([0-9]+)$#', $uri, $matches)) {
    $_GET['id'] = $matches[1];
    require 'post.php';
    exit;
}

// Fall back to actual files
if (is_file($_SERVER['DOCUMENT_ROOT'] . $uri)) {
    return false;
}

// Default to index.php
require 'index.php';
?>
```

### Using Nginx

```nginx
# nginx configuration
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

---

## Security Considerations

### Input Validation

```php
<?php
// Always validate URL parameters
$user_id = $_GET['id'] ?? null;

// Validate is numeric
if (!is_numeric($user_id)) {
    http_response_code(400);
    exit('Invalid user ID');
}

$user_id = (int) $user_id;

// Or whitelist allowed values
$category = $_GET['category'] ?? 'all';
$allowed_categories = ['electronics', 'books', 'clothing', 'all'];

if (!in_array($category, $allowed_categories)) {
    http_response_code(400);
    exit('Invalid category');
}
?>
```

### Avoid Information Disclosure

```php
<?php
// GOOD - Generic error messages
http_response_code(404);
echo "Resource not found";

// BAD - Exposes structure
http_response_code(404);
echo "User with ID 123 not found in table users";

// GOOD - No file extensions visible
// /users/john    (could be any backend)

// BAD - Exposes technology
// /users.php?id=123    (reveals PHP)
// /users.aspx?id=123   (reveals .NET)
?>
```

### Prevent URL Injection

```php
<?php
// BAD - Allows injection
$url = "/api/users/" . $_GET['id'];
// URL: /api/users/123; DELETE FROM users

// GOOD - Validate strictly
$id = $_GET['id'] ?? null;
if (!preg_match('#^[0-9]+$#', $id)) {
    exit('Invalid ID');
}
$url = "/api/users/" . $id;
?>
```

---

## Complete Examples

### SEO-Friendly Blog

```php
<?php
// router.php - Route blog URLs

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// /blog/2026/01/getting-started-php
if (preg_match('#^/blog/([0-9]{4})/([0-9]{2})/([a-z0-9-]+)/?$#i', $uri, $matches)) {
    $_GET['year'] = $matches[1];
    $_GET['month'] = $matches[2];
    $_GET['slug'] = $matches[3];
    require 'blog_post.php';
    exit;
}

// /blog/2026/01
if (preg_match('#^/blog/([0-9]{4})/([0-9]{2})/?$#', $uri, $matches)) {
    $_GET['year'] = $matches[1];
    $_GET['month'] = $matches[2];
    require 'blog_archive.php';
    exit;
}

// /blog
if ($uri === '/blog' || $uri === '/blog/') {
    require 'blog.php';
    exit;
}

// Fall back
require 'index.php';
?>
```

### REST API

```php
<?php
// api/router.php

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = '/api/v1';
$path = substr($uri, strlen($base));

// GET /api/v1/users
if ($method === 'GET' && $path === '/users') {
    require 'handlers/users_list.php';
    exit;
}

// POST /api/v1/users
if ($method === 'POST' && $path === '/users') {
    require 'handlers/users_create.php';
    exit;
}

// GET /api/v1/users/123
if ($method === 'GET' && preg_match('#^/users/([0-9]+)$#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    require 'handlers/users_show.php';
    exit;
}

// PUT /api/v1/users/123
if ($method === 'PUT' && preg_match('#^/users/([0-9]+)$#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    require 'handlers/users_update.php';
    exit;
}

// DELETE /api/v1/users/123
if ($method === 'DELETE' && preg_match('#^/users/([0-9]+)$#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    require 'handlers/users_delete.php';
    exit;
}

// 404
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not found']);
?>
```

---

## See Also

- [Query Parameters](10-query-parameter.md)
- [PHP Web Development](3-php-web.md)
- [Client & Server](2-client-server.md)
