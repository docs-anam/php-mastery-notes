# Match Expression

## Overview

Match expression is a safer and cleaner alternative to switch statements, supporting strict equality comparisons and returning values directly without needing break statements.

---

## Basic Match

```php
<?php
$status = 'active';

$message = match($status) {
    'active' => 'User is active',
    'inactive' => 'User is inactive',
    'banned' => 'User is banned',
    default => 'Unknown status'
};

echo $message; // User is active
?>
```

---

## Comparison with Switch

```php
<?php
// Traditional switch
$code = 200;
switch ($code) {
    case 200:
        $message = "OK";
        break;
    case 404:
        $message = "Not Found";
        break;
    default:
        $message = "Unknown";
}

// Cleaner match
$message = match($code) {
    200 => "OK",
    404 => "Not Found",
    default => "Unknown"
};
?>
```

---

## Multiple Conditions

```php
<?php
$day = 'Saturday';

$type = match($day) {
    'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' => 'Weekday',
    'Saturday', 'Sunday' => 'Weekend',
    default => 'Unknown'
};

echo $type; // Weekend
?>
```

---

## Strict Equality

```php
<?php
$value = '200';

// Switch would match (loose comparison)
switch ($value) {
    case 200:
        echo "Matched in switch\n";
        break;
}

// Match won't match (strict comparison)
$result = match($value) {
    200 => "Matched",
    '200' => "Strict match",
    default => "No match"
};

echo $result; // Strict match
?>
```

---

## Returning Values

```php
<?php
function getStatusCode(string $status): int {
    return match($status) {
        'success' => 200,
        'created' => 201,
        'badrequest' => 400,
        'notfound' => 404,
        'error' => 500,
        default => 503
    };
}

echo getStatusCode('notfound'); // 404
?>
```

---

## With Conditions

```php
<?php
$score = 85;

$grade = match(true) {
    $score >= 90 => 'A',
    $score >= 80 => 'B',
    $score >= 70 => 'C',
    $score >= 60 => 'D',
    default => 'F'
};

echo $grade; // B
?>
```

---

## With Objects

```php
<?php
class Request {
    public function __construct(public string $method) {}
}

$request = new Request('POST');

$action = match($request->method) {
    'GET' => 'retrieve',
    'POST' => 'create',
    'PUT' => 'update',
    'DELETE' => 'delete',
    default => 'unknown'
};

echo $action; // create
?>
```

---

## Nested Match

```php
<?php
$userType = 'admin';
$action = 'delete';

$allowed = match($userType) {
    'admin' => match($action) {
        'create', 'read', 'update', 'delete' => true,
        default => false
    },
    'user' => match($action) {
        'read' => true,
        default => false
    },
    default => false
};

var_dump($allowed); // true
?>
```

---

## Use Cases

### 1. HTTP Status Code Handling

```php
<?php
class Response {
    public function __construct(public int $statusCode) {}
    
    public function getMessage(): string {
        return match($this->statusCode) {
            200, 201, 204 => 'Success',
            400, 422 => 'Validation Error',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500, 502, 503 => 'Server Error',
            default => 'Unknown Status'
        };
    }
}

$response = new Response(404);
echo $response->getMessage(); // Not Found
?>
```

### 2. Routing

```php
<?php
class Router {
    public function route(string $path, string $method): string {
        return match([$method, $path]) {
            ['GET', '/'] => 'home',
            ['GET', '/about'] => 'about',
            ['GET', '/products'] => 'products',
            ['POST', '/products'] => 'create_product',
            ['GET', '/products/:id'] => 'product_detail',
            ['PUT', '/products/:id'] => 'update_product',
            ['DELETE', '/products/:id'] => 'delete_product',
            default => '404'
        };
    }
}
?>
```

### 3. Payment Processing

```php
<?php
class PaymentProcessor {
    public function process(string $method, float $amount): array {
        return match($method) {
            'credit_card' => [
                'gateway' => 'stripe',
                'fee' => $amount * 0.029 + 0.3
            ],
            'paypal' => [
                'gateway' => 'paypal',
                'fee' => $amount * 0.034 + 0.3
            ],
            'bank_transfer' => [
                'gateway' => 'wise',
                'fee' => $amount * 0.01
            ],
            'crypto' => [
                'gateway' => 'coinbase',
                'fee' => $amount * 0.01
            ],
            default => throw new InvalidArgumentException('Unknown method')
        };
    }
}
?>
```

### 4. Type Determination

```php
<?php
function getTypeName(mixed $value): string {
    return match(true) {
        is_bool($value) => 'boolean',
        is_int($value) => 'integer',
        is_float($value) => 'float',
        is_string($value) => 'string',
        is_array($value) => 'array',
        is_object($value) => get_class($value),
        is_null($value) => 'null',
        default => 'unknown'
    };
}

echo getTypeName(true); // boolean
echo getTypeName(new DateTime()); // DateTime
?>
```

---

## Best Practices

### 1. Use Exhaustive Matching

```php
<?php
enum Status: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}

// ✅ Good - all cases covered
$message = match($status) {
    Status::ACTIVE => 'Active',
    Status::INACTIVE => 'Inactive',
    Status::BANNED => 'Banned',
};
?>
```

### 2. Group Related Cases

```php
<?php
// ✅ Good - grouped logically
$category = match($statusCode) {
    200, 201, 204 => 'success',
    400, 401, 403, 404 => 'client_error',
    500, 502, 503 => 'server_error',
    default => 'unknown'
};
?>
```

### 3. Throw Exceptions

```php
<?php
$result = match($value) {
    'valid' => true,
    'invalid' => false,
    default => throw new InvalidArgumentException("Unknown value: $value")
};
?>
```

---

## Common Mistakes

### 1. Forgetting Strict Comparison

```php
<?php
// ❌ Wrong - expects strict equality
if ('0' == 0) echo "Loose match";

$result = match('0') {
    0 => "Won't match",  // '0' !== 0
    '0' => "Will match",
};
?>
```

### 2. Not Handling All Cases

```php
<?php
// ❌ Wrong - unhandled case throws exception
$status = 'unknown';
$message = match($status) {
    'active' => 'Active',
    'inactive' => 'Inactive'
    // Missing 'unknown' case
};

// ✅ Correct
$message = match($status) {
    'active' => 'Active',
    'inactive' => 'Inactive',
    default => 'Unknown'
};
?>
```

### 3. Complex Logic in Match

```php
<?php
// ❌ Avoid complex logic
$result = match($value) {
    $value > 10 && $value < 20 => "In range"
};

// ✅ Better
$result = match(true) {
    $value > 10 && $value < 20 => "In range",
    default => "Out of range"
};
?>
```

---

## Complete Example

```php
<?php
class RequestHandler {
    public function handle(string $method, string $path, array $data = []): array {
        return match([$method, $this->normalizePath($path)]) {
            ['GET', '/'] => ['controller' => 'Home', 'action' => 'index'],
            ['GET', '/users'] => ['controller' => 'User', 'action' => 'list'],
            ['GET', '/users/:id'] => ['controller' => 'User', 'action' => 'show'],
            ['POST', '/users'] => ['controller' => 'User', 'action' => 'create'],
            ['PUT', '/users/:id'] => ['controller' => 'User', 'action' => 'update'],
            ['DELETE', '/users/:id'] => ['controller' => 'User', 'action' => 'delete'],
            ['GET', '/products'] => ['controller' => 'Product', 'action' => 'list'],
            ['POST', '/products'] => ['controller' => 'Product', 'action' => 'create'],
            default => throw new Exception("Route not found: $method $path")
        };
    }
    
    private function normalizePath(string $path): string {
        return strtolower(trim($path, '/'));
    }
}

$handler = new RequestHandler();
$route = $handler->handle('POST', '/users');
print_r($route);
?>
```

---

## See Also

- Documentation: [Match Expression](https://www.php.net/manual/en/control-structures.match.php)
- Related: [Union Types](5-union-types.md), [Enums](../14-php8.1/8-enums.md)
