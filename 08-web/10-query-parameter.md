# Query Parameters and URL Handling

## Overview

Query parameters are key-value pairs appended to URLs after the `?` character. They allow clients to pass data to the server through URLs. This chapter covers how to properly handle, parse, and validate query parameters.

---

## Table of Contents

1. Query Parameter Basics
2. Accessing Query Parameters
3. Parsing Query Strings
4. Validating Query Data
5. Building Query Strings
6. Pagination with Query Parameters
7. Filtering and Search
8. Complete Examples

---

## Query Parameter Basics

### URL Structure

```
https://example.com/search?q=php&page=2&sort=date
                           ^\    ^^   ^ ^^^^  ^
                           └─────────────────────┘
                            Query String

Components:
q=php       - First parameter
page=2      - Second parameter
sort=date   - Third parameter
```

### Common Examples

```
/products?category=electronics
/search?q=php&page=1
/users?sort=name&order=asc
/blog?author=john&year=2026
/api/users?filter[status]=active&limit=10
```

---

## Accessing Query Parameters

### Basic Access

```php
<?php
// URL: /page.php?id=123&name=John

// Direct access
echo $_GET['id'];        // 123
echo $_GET['name'];      // John
echo $_GET['missing'];   // Undefined index notice

// Safe access with null coalescing
echo $_GET['id'] ?? 'default';
echo $_GET['name'] ?? 'unknown';

// Check if parameter exists
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if (array_key_exists('page', $_GET)) {
    $page = $_GET['page'];
}
?>
```

### Single vs Multiple Values

```php
<?php
// Single parameter
// URL: /filter?category=electronics
$category = $_GET['category'];  // "electronics"

// Multiple parameters with same name
// URL: /filter?tags=php&tags=web&tags=tutorial
$tags = $_GET['tags'];  // Array: ["php", "web", "tutorial"]

// Check if array
if (is_array($_GET['tags'])) {
    foreach ($_GET['tags'] as $tag) {
        echo htmlspecialchars($tag);
    }
}

// Ensure it's array
$tags = (array) $_GET['tags'];  // Convert to array if needed
?>
```

---

## Parsing Query Strings

### parse_str()

```php
<?php
// Parse query string into variables
$query = "name=John&age=30&city=NYC";
parse_str($query, $output);

echo $output['name'];    // John
echo $output['age'];     // 30
echo $output['city'];    // NYC

// Get existing QUERY_STRING
parse_str($_SERVER['QUERY_STRING'], $params);

foreach ($params as $key => $value) {
    echo "$key => $value\n";
}
?>
```

### parse_url()

```php
<?php
// Parse complete URL
$url = "https://example.com/page?id=123&name=John";

$parts = parse_url($url);
// Array (
//     [scheme] => https
//     [host] => example.com
//     [path] => /page
//     [query] => id=123&name=John
// )

echo $parts['scheme'];   // https
echo $parts['host'];     // example.com
echo $parts['path'];     // /page
echo $parts['query'];    // id=123&name=John

// Get query parameters
parse_str($parts['query'], $params);
print_r($params);
?>
```

### http_build_query()

```php
<?php
// Build query string from array
$params = [
    'id' => 123,
    'name' => 'John Doe',
    'tags' => ['php', 'web'],
];

$query = http_build_query($params);
// id=123&name=John+Doe&tags%5B0%5D=php&tags%5B1%5D=web

$url = "/search?" . $query;
echo $url;

// With custom separator
$query = http_build_query($params, '', ';');
// id=123;name=John+Doe;...

// URL encoding
$query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
?>
```

---

## Validating Query Data

### Type Validation

```php
<?php
// Validate numeric
$id = $_GET['id'] ?? null;

if ($id === null) {
    http_response_code(400);
    exit('ID required');
}

if (!is_numeric($id)) {
    http_response_code(400);
    exit('ID must be numeric');
}

$id = (int) $id;

// Validate string
$name = $_GET['name'] ?? '';

if (empty($name)) {
    http_response_code(400);
    exit('Name required');
}

if (strlen($name) > 100) {
    http_response_code(400);
    exit('Name too long');
}
?>
```

### Whitelist Validation

```php
<?php
// Only allow certain values
$sort = $_GET['sort'] ?? 'name';
$allowed_sorts = ['name', 'date', 'popularity'];

if (!in_array($sort, $allowed_sorts)) {
    http_response_code(400);
    exit('Invalid sort parameter');
}

// Use safe value
echo "Sorting by: $sort";

// Multiple values
$categories = $_GET['category'] ?? [];
if (!is_array($categories)) {
    $categories = [$categories];
}

$allowed_categories = ['electronics', 'books', 'clothing'];
$filtered = array_intersect($categories, $allowed_categories);

foreach ($filtered as $cat) {
    echo htmlspecialchars($cat);
}
?>
```

### Filter Functions

```php
<?php
// Using filter functions
$email = $_GET['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Invalid email';
}

// Integer
$page = $_GET['page'] ?? 1;
$page = filter_var($page, FILTER_VALIDATE_INT);
if ($page === false || $page < 1) {
    $page = 1;
}

// URL
$url = $_GET['url'] ?? '';
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo 'Invalid URL';
}

// IP address
$ip = $_GET['ip'] ?? '';
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    echo 'Invalid IP';
}

// Custom regex
$code = $_GET['code'] ?? '';
if (!filter_var($code, FILTER_VALIDATE_REGEXP, 
    ['options' => ['regexp' => '/^[A-Z0-9]{8}$/']))) {
    echo 'Invalid code';
}
?>
```

---

## Building Query Strings

### URL Building

```php
<?php
// Simple concatenation
$base = '/products';
$query = http_build_query(['category' => 'electronics']);
$url = $base . '?' . $query;
// /products?category=electronics

// With existing parameters
$current_page = 1;
$sort = 'price';
$url = '/products?' . http_build_query([
    'page' => $current_page,
    'sort' => $sort,
]);
?>
```

### Helper Function

```php
<?php
class QueryString {
    private $params = [];
    
    public function __construct($query = '') {
        if ($query) {
            parse_str($query, $this->params);
        } else {
            $this->params = $_GET;
        }
    }
    
    public function get($key, $default = null) {
        return $this->params[$key] ?? $default;
    }
    
    public function set($key, $value) {
        $this->params[$key] = $value;
        return $this;
    }
    
    public function remove($key) {
        unset($this->params[$key]);
        return $this;
    }
    
    public function build($base_url = '') {
        $query = http_build_query($this->params);
        if ($base_url) {
            return $base_url . '?' . $query;
        }
        return $query;
    }
}

// Usage
$qs = new QueryString($_SERVER['QUERY_STRING']);
$qs->set('page', 2)->remove('sort');
echo $qs->build('/products');
?>
```

---

## Pagination with Query Parameters

### Page Parameter

```php
<?php
// Get page from query string
$page = $_GET['page'] ?? 1;

// Validate
if (!is_numeric($page) || $page < 1) {
    $page = 1;
}

$page = (int) $page;

// Database query
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total count
$total = 100;  // From database
$total_pages = ceil($total / $per_page);

// Validate page number
if ($page > $total_pages) {
    http_response_code(404);
    exit('Page not found');
}

// Build pagination links
for ($i = 1; $i <= $total_pages; $i++) {
    $url = '/products?page=' . $i;
    echo '<a href="' . htmlspecialchars($url) . '">' . $i . '</a>';
}
?>
```

### Preserving Other Parameters

```php
<?php
// Keep other parameters when paginating
$current_page = $_GET['page'] ?? 1;
$sort = $_GET['sort'] ?? 'name';
$category = $_GET['category'] ?? '';

// Build URL with preserved parameters
$params = [
    'page' => $current_page + 1,
    'sort' => $sort,
];

if ($category) {
    $params['category'] = $category;
}

$next_url = '/products?' . http_build_query($params);
echo '<a href="' . htmlspecialchars($next_url) . '">Next</a>';
?>
```

---

## Filtering and Search

### Search Implementation

```php
<?php
// URL: /search?q=php&page=1

$query = $_GET['q'] ?? '';
$page = $_GET['page'] ?? 1;

// Validate
if (empty($query)) {
    exit('Search query required');
}

if (!is_numeric($page) || $page < 1) {
    $page = 1;
}

// Sanitize for display (not database)
$safe_query = htmlspecialchars($query);

// Search in database (with prepared statements!)
$db = new PDO('sqlite:db.sqlite');
$stmt = $db->prepare('SELECT * FROM posts WHERE title LIKE ? OR content LIKE ?');
$search_term = '%' . $query . '%';
$stmt->execute([$search_term, $search_term]);
$results = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<body>
    <form method="GET">
        <input type="text" name="q" value="<?= $safe_query ?>">
        <button type="submit">Search</button>
    </form>
    
    <div class="results">
        <?php foreach ($results as $result): ?>
            <div class="post">
                <h3><?= htmlspecialchars($result['title']) ?></h3>
                <p><?= htmlspecialchars(substr($result['content'], 0, 200)) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
```

### Multiple Filters

```php
<?php
// URL: /products?category=electronics&brand=Sony&price_min=100&price_max=1000

$filters = [
    'category' => $_GET['category'] ?? '',
    'brand' => $_GET['brand'] ?? '',
    'price_min' => $_GET['price_min'] ?? 0,
    'price_max' => $_GET['price_max'] ?? 9999,
];

// Validate filters
if (!empty($filters['price_min']) && !is_numeric($filters['price_min'])) {
    http_response_code(400);
    exit('Invalid price_min');
}

if (!empty($filters['price_max']) && !is_numeric($filters['price_max'])) {
    http_response_code(400);
    exit('Invalid price_max');
}

// Build SQL query
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($filters['category'])) {
    $sql .= " AND category = ?";
    $params[] = $filters['category'];
}

if (!empty($filters['brand'])) {
    $sql .= " AND brand = ?";
    $params[] = $filters['brand'];
}

if ($filters['price_min'] > 0) {
    $sql .= " AND price >= ?";
    $params[] = (int) $filters['price_min'];
}

if ($filters['price_max'] < 9999) {
    $sql .= " AND price <= ?";
    $params[] = (int) $filters['price_max'];
}

// Execute
$db = new PDO('sqlite:db.sqlite');
$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>
```

---

## Complete Examples

### Product Filter Page

```php
<?php
// Validate and get filters
$category = $_GET['category'] ?? 'all';
$sort = $_GET['sort'] ?? 'name';
$page = $_GET['page'] ?? 1;

$allowed_categories = ['all', 'electronics', 'books', 'clothing'];
$allowed_sorts = ['name', 'price', 'popularity'];

if (!in_array($category, $allowed_categories)) {
    $category = 'all';
}

if (!in_array($sort, $allowed_sorts)) {
    $sort = 'name';
}

if (!is_numeric($page) || $page < 1) {
    $page = 1;
}

$page = (int) $page;

// Sample data
$products = [
    ['name' => 'Laptop', 'category' => 'electronics', 'price' => 999],
    ['name' => 'Book', 'category' => 'books', 'price' => 15],
    ['name' => 'Shirt', 'category' => 'clothing', 'price' => 25],
];

// Filter
if ($category !== 'all') {
    $products = array_filter($products, function($p) use ($category) {
        return $p['category'] === $category;
    });
}

// Sort
usort($products, function($a, $b) use ($sort) {
    if ($sort === 'price') {
        return $a['price'] - $b['price'];
    }
    return strcasecmp($a['name'], $b['name']);
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>
    <h1>Products</h1>
    
    <div class="filters">
        <form method="GET">
            <select name="category">
                <?php foreach ($allowed_categories as $cat): ?>
                    <option value="<?= $cat ?>" 
                            <?= ($category === $cat) ? 'selected' : '' ?>>
                        <?= ucfirst($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="sort">
                <option value="name" <?= ($sort === 'name') ? 'selected' : '' ?>>Name</option>
                <option value="price" <?= ($sort === 'price') ? 'selected' : '' ?>>Price</option>
                <option value="popularity" <?= ($sort === 'popularity') ? 'selected' : '' ?>>Popularity</option>
            </select>
            
            <button type="submit">Filter</button>
        </form>
    </div>
    
    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p>$<?= number_format($product['price'], 2) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
```

---

## See Also

- [Global Variables & $_GET](9-global-variable-server.md)
- [URL Best Practices](6-php-best-practice-url.md)
- [HTTP Headers](13-header.md)
