# New String Functions

## Overview

PHP 8 introduces new string functions like `str_contains()`, `str_starts_with()`, and `str_ends_with()` that provide cleaner, more readable alternatives to existing string checking methods.

---

## str_contains() Function

```php
<?php
// PHP 7 - verbose alternative
if (strpos('hello world', 'world') !== false) {
    echo "Found\n";
}

// PHP 8 - clear and readable
if (str_contains('hello world', 'world')) {
    echo "Found\n";
}

// Case-sensitive
var_dump(str_contains('Hello World', 'hello')); // false
var_dump(str_contains('Hello World', 'Hello')); // true
?>
```

---

## str_starts_with() Function

```php
<?php
// PHP 7 - awkward alternatives
if (strpos('https://example.com', 'https://') === 0) {
    echo "HTTPS\n";
}

// PHP 8 - clean and clear
if (str_starts_with('https://example.com', 'https://')) {
    echo "HTTPS\n";
}

// Examples
var_dump(str_starts_with('PHP 8', 'PHP')); // true
var_dump(str_starts_with('PHP 8', 'Python')); // false
var_dump(str_starts_with('prefix-key', 'prefix-')); // true
?>
```

---

## str_ends_with() Function

```php
<?php
// PHP 7 - complex alternatives
if (substr('index.php', -4) === '.php') {
    echo "PHP file\n";
}

// PHP 8 - simple and readable
if (str_ends_with('index.php', '.php')) {
    echo "PHP file\n";
}

// Examples
var_dump(str_ends_with('user@example.com', '@example.com')); // true
var_dump(str_ends_with('document.pdf', '.doc')); // false
?>
```

---

## Real-World Examples

### 1. URL Processing

```php
<?php
class URLValidator {
    public function isSecure(string $url): bool {
        return str_starts_with($url, 'https://');
    }
    
    public function isLocalhost(string $url): bool {
        return str_contains($url, 'localhost') || str_contains($url, '127.0.0.1');
    }
    
    public function isAjaxRequest(string $path): bool {
        return str_contains($path, '/api/');
    }
}

$validator = new URLValidator();
var_dump($validator->isSecure('https://example.com')); // true
var_dump($validator->isLocalhost('http://localhost:8000')); // true
?>
```

### 2. File Type Checking

```php
<?php
class FileValidator {
    private array $allowedExtensions = ['jpg', 'png', 'gif', 'pdf', 'doc'];
    
    public function isValidFile(string $filename): bool {
        // Check extension
        foreach ($this->allowedExtensions as $ext) {
            if (str_ends_with($filename, ".$ext")) {
                return true;
            }
        }
        return false;
    }
    
    public function isImage(string $filename): bool {
        return str_ends_with($filename, '.jpg') ||
               str_ends_with($filename, '.png') ||
               str_ends_with($filename, '.gif');
    }
    
    public function isArchive(string $filename): bool {
        return str_ends_with($filename, '.zip') ||
               str_ends_with($filename, '.tar') ||
               str_ends_with($filename, '.gz');
    }
}

$validator = new FileValidator();
var_dump($validator->isValidFile('document.pdf')); // true
var_dump($validator->isImage('photo.jpg')); // true
?>
```

### 3. Email Processing

```php
<?php
class EmailProcessor {
    private array $blockedDomains = ['spam.com', 'fake.net'];
    
    public function isValidDomain(string $email): bool {
        return str_contains($email, '@') && str_contains($email, '.');
    }
    
    public function isBlocked(string $email): bool {
        foreach ($this->blockedDomains as $domain) {
            if (str_ends_with($email, '@' . $domain)) {
                return true;
            }
        }
        return false;
    }
    
    public function isCompanyEmail(string $email, string $company): bool {
        return str_ends_with($email, '@' . $company);
    }
}

$processor = new EmailProcessor();
var_dump($processor->isValidDomain('user@example.com')); // true
var_dump($processor->isBlocked('spam@spam.com')); // true
?>
```

### 4. API Version Handling

```php
<?php
class APIRouter {
    public function getVersion(string $path): string {
        if (str_starts_with($path, '/api/v1/')) {
            return 'v1';
        } elseif (str_starts_with($path, '/api/v2/')) {
            return 'v2';
        } elseif (str_starts_with($path, '/api/v3/')) {
            return 'v3';
        }
        return 'unknown';
    }
    
    public function isPublicEndpoint(string $path): bool {
        return str_contains($path, '/public/') ||
               str_starts_with($path, '/auth/');
    }
}

$router = new APIRouter();
echo $router->getVersion('/api/v2/users'); // v2
?>
```

### 5. Code Generation

```php
<?php
class CodeGenerator {
    public function shouldEscape(string $code): bool {
        return str_contains($code, '$') || str_contains($code, '{');
    }
    
    public function isComment(string $line): bool {
        return str_starts_with(trim($line), '//') ||
               str_starts_with(trim($line), '#');
    }
    
    public function isString(string $token): bool {
        return str_starts_with($token, '"') ||
               str_starts_with($token, "'");
    }
}
?>
```

---

## Comparison with Alternatives

```php
<?php
$haystack = "Hello World";

// str_contains()
echo "--- str_contains() ---\n";
echo str_contains($haystack, 'World') ? "true" : "false";

// Alternatives
echo "--- strpos() ---\n";
echo strpos($haystack, 'World') !== false ? "true" : "false";

echo "--- preg_match() ---\n";
echo preg_match('/World/', $haystack) ? "true" : "false";

echo "--- explode() ---\n";
echo in_array('World', explode(' ', $haystack)) ? "true" : "false";
?>
```

---

## Performance Considerations

```php
<?php
// str_starts_with, str_ends_with, and str_contains are optimized
// and generally faster than alternatives

$start = microtime(true);
for ($i = 0; $i < 100000; $i++) {
    str_starts_with('index.php', 'index');
}
$time1 = microtime(true) - $start;

$start = microtime(true);
for ($i = 0; $i < 100000; $i++) {
    strpos('index.php', 'index') === 0;
}
$time2 = microtime(true) - $start;

echo "str_starts_with: " . number_format($time1, 4) . "s\n";
echo "strpos: " . number_format($time2, 4) . "s\n";
// str_starts_with typically faster
?>
```

---

## Best Practices

### 1. Use for Readability

```php
<?php
// ✅ Good - code is self-documenting
if (str_starts_with($url, 'https://')) {
    $secure = true;
}

// ❌ Avoid - less clear intent
if (strpos($url, 'https://') === 0) {
    $secure = true;
}
?>
```

### 2. Combine for Complex Logic

```php
<?php
// ✅ Good - readable combination
class RequestHandler {
    public function handle(string $method, string $path): void {
        if (str_starts_with($path, '/admin/') && !$this->isAdmin()) {
            throw new ForbiddenException();
        }
        
        if (str_ends_with($path, '.php') && !$this->allowPhpFiles()) {
            throw new BadRequestException();
        }
    }
}
?>
```

### 3. Case Sensitivity

```php
<?php
// These functions are case-sensitive
var_dump(str_contains('Hello', 'hello')); // false
var_dump(str_starts_with('HELLO', 'hel')); // false

// For case-insensitive, convert first
var_dump(str_contains(strtolower('Hello'), 'hello')); // true
var_dump(str_starts_with(strtolower('HELLO'), 'hel')); // true
?>
```

---

## Common Mistakes

### 1. Forgetting Case Sensitivity

```php
<?php
// ❌ Wrong - expects case-insensitive
if (str_starts_with('HTTPS://example.com', 'https://')) {
    echo "Secure"; // Won't execute
}

// ✅ Correct
if (str_starts_with(strtolower('HTTPS://example.com'), 'https://')) {
    echo "Secure"; // Will execute
}
?>
```

### 2. Checking Empty Strings

```php
<?php
// ❌ Won't behave as expected with empty needle
var_dump(str_contains('hello', '')); // true

// ✅ Check for empty first if needed
if (!empty($needle) && str_contains($haystack, $needle)) {
    echo "Found\n";
}
?>
```

---

## Complete Example

```php
<?php
class RequestValidator {
    public function validate(string $method, string $path, string $contentType): bool {
        // Check HTTP method
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            throw new InvalidArgumentException('Invalid method');
        }
        
        // Check path structure
        if (!str_starts_with($path, '/')) {
            throw new InvalidArgumentException('Path must start with /');
        }
        
        // Check for HTTPS
        $secure = str_starts_with($path, '/api/') ? true : false;
        
        // Validate content type
        $validTypes = ['application/json', 'application/x-www-form-urlencoded', 'multipart/form-data'];
        if (!in_array($contentType, $validTypes) && !str_contains($contentType, '+json')) {
            throw new InvalidArgumentException('Invalid content type');
        }
        
        // Block certain paths
        if (str_contains($path, '../') || str_contains($path, '//')) {
            throw new SecurityException('Invalid path');
        }
        
        // Check for API version
        if (str_starts_with($path, '/api/')) {
            $version = 'v1';
            if (str_contains($path, '/v2/')) $version = 'v2';
            if (str_contains($path, '/v3/')) $version = 'v3';
        }
        
        return true;
    }
}

$validator = new RequestValidator();
$validator->validate('POST', '/api/v2/users', 'application/json');
?>
```

---

## See Also

- Documentation: [str_contains()](https://www.php.net/manual/en/function.str-contains.php)
- Documentation: [str_starts_with()](https://www.php.net/manual/en/function.str-starts-with.php)
- Documentation: [str_ends_with()](https://www.php.net/manual/en/function.str-ends-with.php)
- Related: [String Functions](../01-basics/17-string-manipulation.md)
