# Nullsafe Operator

## Overview

The nullsafe operator (?->) allows safely accessing methods and properties on potentially null objects without explicitly checking for null, preventing null pointer exceptions.

---

## Basic Nullsafe Operator

```php
<?php
class User {
    public function getProfile(): ?Profile {
        return null;
    }
}

class Profile {
    public function getName(): string {
        return "John";
    }
}

$user = new User();

// Traditional approach
if ($user !== null && $user->getProfile() !== null) {
    echo $user->getProfile()->getName();
}

// Nullsafe operator
echo $user->getProfile()?->getName(); // Returns null safely
?>
```

---

## Avoiding Nested Nulls

```php
<?php
class Company {
    public function __construct(public ?User $ceo) {}
}

class User {
    public function __construct(public ?Profile $profile) {}
}

class Profile {
    public function __construct(public string $name) {}
}

$company = new Company(null);

// Traditional - verbose
$ceoName = null;
if ($company !== null && $company->ceo !== null && $company->ceo->profile !== null) {
    $ceoName = $company->ceo->profile->name;
}

// Nullsafe - clean
$ceoName = $company->ceo?->profile?->name;

echo $ceoName ?? "No CEO"; // No CEO
?>
```

---

## With Method Calls

```php
<?php
class Logger {
    public function log(string $message): bool {
        echo "Logged: $message\n";
        return true;
    }
}

class Application {
    public function __construct(private ?Logger $logger) {}
    
    public function run() {
        // Safe method call - null if logger is null
        $result = $this->logger?->log("Application started");
        
        if ($result === null) {
            echo "No logger available\n";
        }
    }
}

$app = new Application(null);
$app->run();
?>
```

---

## Property Access

```php
<?php
class Post {
    public function __construct(public ?Author $author) {}
}

class Author {
    public string $name = "Anonymous";
    public string $email = "noreply@example.com";
}

$post = new Post(new Author());

// Nullsafe property access
echo $post->author?->name ?? "Unknown Author"; // Anonymous

$emptyPost = new Post(null);
echo $emptyPost->author?->name ?? "Unknown Author"; // Unknown Author
?>
```

---

## Array Access with Nullsafe

```php
<?php
class Response {
    public function __construct(
        public ?array $data
    ) {}
    
    public function getData() {
        // Safe array access with nullsafe
        return $this->data?->items ?? [];
    }
}

$response = new Response(null);
var_dump($response->getData()); // array

$validResponse = new Response(['items' => [1, 2, 3]]);
var_dump($validResponse->getData()); // Won't work with nullsafe on array
?>
```

---

## Real-World Examples

### 1. API Response Handling

```php
<?php
class APIResponse {
    public function __construct(
        public ?object $user
    ) {}
}

class User {
    public function __construct(public string $name = "") {}
}

function getUserName(APIResponse $response): string {
    // Instead of nested checks
    return $response->user?->name ?? "Guest";
}

$response = new APIResponse(new User("John"));
echo getUserName($response); // John

$emptyResponse = new APIResponse(null);
echo getUserName($emptyResponse); // Guest
?>
```

### 2. Configuration Access

```php
<?php
class ConfigManager {
    private ?object $config = null;
    
    public function get(string $key): string {
        // Safe chain of property access
        $value = $this->config?->database?->host;
        
        return $value ?? "default_value";
    }
}
?>
```

### 3. User Permission Checking

```php
<?php
class AuthService {
    public function canDelete(User $user, Post $post): bool {
        // Check ownership safely
        return $user->id === $post->author?->id;
    }
}

class User {
    public function __construct(public int $id) {}
}

class Post {
    public function __construct(
        public ?Author $author
    ) {}
}

class Author {
    public function __construct(public int $id) {}
}

$post = new Post(null);
$user = new User(1);

$auth = new AuthService();
var_dump($auth->canDelete($user, $post)); // false (safely)
?>
```

### 4. Logging with Optional Components

```php
<?php
class RequestLogger {
    public function logRequest(?Request $request): void {
        $method = $request?->method ?? "UNKNOWN";
        $path = $request?->path ?? "/";
        $ip = $request?->getClientIp() ?? "unknown";
        
        echo "[$method] $path from $ip\n";
    }
}

class Request {
    public function __construct(
        public string $method,
        public string $path
    ) {}
    
    public function getClientIp(): string {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}

$logger = new RequestLogger();
$logger->logRequest(null); // [UNKNOWN] / from unknown
$logger->logRequest(new Request('GET', '/api/users'));
?>
```

---

## Combining with Other Operators

```php
<?php
// Nullsafe with null coalescing
$value = $object?->property ?? "default";

// Nullsafe with null coalescing assignment
$object?->cache ??= expensive_computation();

// Nullsafe in ternary
$status = $user?->profile?->verified ?? false ? "verified" : "unverified";
?>
```

---

## Best Practices

### 1. Use for Optional Dependencies

```php
<?php
class Service {
    public function __construct(
        private ?Logger $logger,
        private ?Cache $cache
    ) {}
    
    public function process($data) {
        $this->logger?->info("Processing started");
        $cached = $this->cache?->get('key');
        
        // Process...
        
        $this->logger?->info("Processing complete");
    }
}
?>
```

### 2. Document Optional Properties

```php
<?php
/**
 * @param ?DatabaseConnection $db Optional database connection
 */
class Repository {
    public function __construct(
        private ?DatabaseConnection $db
    ) {}
    
    public function save($data): bool {
        return $this->db?->insert('table', $data) ?? false;
    }
}
?>
```

### 3. Provide Default Values

```php
<?php
// ✅ Always provide defaults
$userId = $session?->user?->id ?? 0;
$role = $user?->role ?? 'guest';
$email = $user?->getEmail() ?? 'noreply@example.com';
?>
```

---

## Common Mistakes

### 1. Chaining Too Many Nullsafes

```php
<?php
// ❌ Too many - suggests design issue
$value = $a?->b?->c?->d?->e?->f?->g;

// ✅ Better - refactor the object structure
// Use composition over deep nesting
$value = $a?->getFinalValue();
?>
```

### 2. Not Providing Defaults

```php
<?php
// ❌ Returns null, but you need a value
$count = $collection?->count();

// ✅ Always provide default
$count = $collection?->count() ?? 0;
?>
```

### 3. Using with instanceof Check

```php
<?php
// ❌ Redundant - nullsafe already handles null
if ($object !== null && $object instanceof MyClass) {}

// ✅ Better
if ($object instanceof MyClass) {}
?>
```

---

## Complete Example

```php
<?php
class UserRepository {
    public function getUserWithSettings(?int $userId): ?array {
        $user = $this->fetchUser($userId);
        
        return [
            'id' => $user?->id,
            'name' => $user?->name ?? 'Guest',
            'email' => $user?->email,
            'verified' => $user?->isVerified() ?? false,
            'theme' => $user?->settings?->theme ?? 'light',
            'notifications' => $user?->settings?->notifications ?? true,
            'timezone' => $user?->profile?->timezone ?? 'UTC'
        ];
    }
    
    private function fetchUser(?int $userId): ?object {
        if ($userId === null) {
            return null;
        }
        
        // Simulate database fetch
        return (object)[
            'id' => $userId,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'settings' => (object)[
                'theme' => 'dark',
                'notifications' => true
            ],
            'profile' => null
        ];
    }
    
    public function isVerified(?object $user): bool {
        return $user?->isVerified() ?? false;
    }
}

$repo = new UserRepository();
$user = $repo->getUserWithSettings(1);
print_r($user);

$nullUser = $repo->getUserWithSettings(null);
print_r($nullUser);
?>
```

---

## See Also

- Documentation: [Nullsafe Operator](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.nullsafe)
- Related: [Union Types](5-union-types.md), [Match Expression](6-match-expression.md)
