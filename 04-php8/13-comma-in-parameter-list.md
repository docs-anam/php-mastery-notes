# Trailing Comma in Parameter List

## Overview

PHP 8 allows trailing commas in function parameter lists and function call arguments, improving code readability and reducing diff noise when adding/removing parameters.

---

## Trailing Comma in Function Declaration

```php
<?php
// PHP 7 - not allowed
function oldWay(
    string $name,
    int $age,
    string $email  // No comma allowed
) {}

// PHP 8 - trailing comma allowed
function newWay(
    string $name,
    int $age,
    string $email,  // Trailing comma allowed
) {}
?>
```

---

## Trailing Comma in Function Calls

```php
<?php
function greet(string $name, int $age, string $email) {
    echo "$name, $age, $email\n";
}

// PHP 7 - not allowed
greet(
    "Alice",
    30,
    "alice@example.com"  // No trailing comma
);

// PHP 8 - trailing comma allowed
greet(
    "Bob",
    25,
    "bob@example.com",  // Trailing comma allowed
);
?>
```

---

## In Method Signatures

```php
<?php
class User {
    // PHP 8 allows trailing comma in class methods
    public function __construct(
        private string $name,
        private string $email,
        private int $age,
    ) {}
    
    public function update(
        ?string $name = null,
        ?string $email = null,
        ?int $age = null,
    ): void {
        if ($name !== null) $this->name = $name;
        if ($email !== null) $this->email = $email;
        if ($age !== null) $this->age = $age;
    }
}
?>
```

---

## In Method Calls

```php
<?php
class DatabaseQuery {
    public function where(string $column, string $operator, mixed $value) {
        // Process where clause
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC') {
        return $this;
    }
}

$query = new DatabaseQuery();
$query
    ->where('age', '>', 18,)  // Trailing comma
    ->where('status', '=', 'active',)  // Trailing comma
    ->orderBy('name', 'ASC',);  // Trailing comma
?>
```

---

## Benefits

### 1. Cleaner Git Diffs

```php
<?php
// Without trailing comma - multiple lines changed
function process(
    string $name,
    int $age,  // Previous last line, now has comma
    string $email  // New line added
) {}

// With trailing comma - only new line appears in diff
function process(
    string $name,
    int $age,
    string $email,  // Clean diff
) {}
?>
```

### 2. Better Code Organization

```php
<?php
function createUser(
    string $name,
    string $email,
    string $phone,
    string $address,
    string $city,
    string $state,
    string $zip,  // Easy to add/remove parameters
) {
    // Implementation
}
?>
```

### 3. Consistent Formatting

```php
<?php
// All parameters formatted consistently
$result = calculateTax(
    $subtotal,
    $taxRate,
    $discountAmount,
    $shippingCost,  // Trailing comma maintains consistency
);
?>
```

---

## Real-World Examples

### 1. API Configuration

```php
<?php
class APIClient {
    public function __construct(
        private string $apiKey,
        private string $baseUrl,
        private int $timeout = 30,
        private bool $debug = false,
    ) {}
    
    public function request(
        string $method,
        string $endpoint,
        ?array $data = null,
        array $headers = [],
    ) {
        // Implementation
    }
}

$client = new APIClient(
    apiKey: 'sk_test_123',
    baseUrl: 'https://api.example.com',
    timeout: 60,
    debug: true,
);

$response = $client->request(
    method: 'POST',
    endpoint: '/users',
    data: ['name' => 'John'],
    headers: ['Authorization' => 'Bearer token'],
);
?>
```

### 2. Database Migration

```php
<?php
class CreateUsersTable {
    public function up(): void {
        Schema::create('users', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();  // Trailing comma
        });
    }
}

// Schema::create with trailing commas
Schema::create('products', function(Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->decimal('price', 8, 2);
    $table->integer('stock');
    $table->timestamps();
});
?>
```

### 3. Model Definition

```php
<?php
class Product {
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public float $price,
        public int $stock,
        public array $attributes = [],
    ) {}
    
    public function update(
        ?string $name = null,
        ?string $description = null,
        ?float $price = null,
        ?int $stock = null,
    ): void {
        if ($name !== null) $this->name = $name;
        if ($description !== null) $this->description = $description;
        if ($price !== null) $this->price = $price;
        if ($stock !== null) $this->stock = $stock;
    }
}
?>
```

### 4. Configuration Array

```php
<?php
$config = [
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'mydb',
        'user' => 'root',
        'password' => '',
    ],
    'cache' => [
        'driver' => 'redis',
        'ttl' => 3600,
    ],
];

// PHP 8 - trailing comma allowed even with multi-line formatting
function setupApplication(
    array $config,
    string $environment,
    bool $debug,
) {
    // Implementation
}

setupApplication(
    config: $config,
    environment: 'production',
    debug: false,
);
?>
```

---

## Best Practices

### 1. Use Consistent Formatting

```php
<?php
// ✅ Good - consistent with all parameters on new lines
function process(
    string $input,
    int $count,
    array $options,
) {
    // Implementation
}

// ✅ Good - all on one line, no comma needed
function simple(string $a, int $b) {}

// ❌ Inconsistent - mixed formatting
function inconsistent(
    string $a, int $b,
    array $c) {}
?>
```

### 2. Align Readability

```php
<?php
// ✅ Readable with trailing comma
function complexFunction(
    string $firstName,
    string $lastName,
    string $email,
    string $phone,
    string $address,
) {
    // Implementation
}

// Less readable
function compactFunction($firstName, $lastName, $email, $phone, $address) {}
?>
```

### 3. Document Parameters

```php
<?php
/**
 * Create a new user
 *
 * @param string $name User's full name
 * @param string $email User's email address
 * @param int $age User's age
 * @param array $preferences User preferences
 */
function createUser(
    string $name,
    string $email,
    int $age,
    array $preferences = [],
) {
    // Implementation
}
?>
```

---

## Common Scenarios

### 1. Before/After Parameter Addition

```php
<?php
// Before - no trailing comma
function sendEmail(
    string $to,
    string $subject,
    string $body
) {}

// After adding new parameter - easy change
function sendEmail(
    string $to,
    string $subject,
    string $body,
    array $attachments = [],  // New parameter added easily
) {}

// With trailing comma from start - minimal changes
function sendMessage(
    string $to,
    string $subject,
    string $body,  // Always had comma
) {}

// Adding parameter - just one new line
function sendMessage(
    string $to,
    string $subject,
    string $body,
    array $attachments = [],  // Just add this
) {}
?>
```

### 2. Multi-level Function Calls

```php
<?php
$result = someFunction(
    firstParam: [
        'nested' => 'value',
        'another' => 'item',  // Trailing comma
    ],
    secondParam: anotherFunction(
        paramA: 'value1',
        paramB: 'value2',  // Trailing comma
    ),
);
?>
```

---

## Complete Example

```php
<?php
class UserRepository {
    public function __construct(
        private DatabaseConnection $db,
        private Cache $cache,
        private Logger $logger,
    ) {}
    
    public function findWithFilters(
        ?string $name = null,
        ?string $email = null,
        ?int $age = null,
        ?array $roles = null,
        int $limit = 50,
        int $offset = 0,
    ): array {
        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if ($name !== null) {
            $query .= " AND name LIKE ?";
            $params[] = "%$name%";
        }
        
        if ($email !== null) {
            $query .= " AND email = ?";
            $params[] = $email;
        }
        
        if ($age !== null) {
            $query .= " AND age = ?";
            $params[] = $age;
        }
        
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->query(
            query: $query,
            params: $params,
        );
    }
    
    public function createBulk(
        array $users,
        bool $skipValidation = false,
    ): int {
        $count = 0;
        
        foreach ($users as $userData) {
            if ($this->create(
                name: $userData['name'],
                email: $userData['email'],
                age: $userData['age'] ?? null,
            )) {
                $count++;
            }
        }
        
        return $count;
    }
    
    private function create(
        string $name,
        string $email,
        ?int $age = null,
    ): bool {
        $this->logger->info('Creating user', [
            'name' => $name,
            'email' => $email,
        ]);
        
        return true;
    }
}
?>
```

---

## See Also

- Documentation: [Trailing Commas](https://www.php.net/manual/en/language.types.array.php)
- Related: [Named Arguments](2-named-argument.md), [Constructor Property Promotion](3-constructor-property-promotion.md)
