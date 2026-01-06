# Named Arguments

## Overview

Named arguments allow you to pass values to a function based on the parameter name rather than position, making code more readable and reducing bugs from argument order mistakes.

---

## Table of Contents

1. Basic Named Arguments
2. Mixing Positional and Named Arguments
3. With Multiple Parameters
4. In Classes and Methods
5. Use Cases and Benefits
6. Best Practices
7. Complete Example

---

## Basic Named Arguments

```php
<?php
function greet(string $name, string $greeting = "Hello") {
    echo "$greeting, $name!\n";
}

// Traditional positional
greet("Alice", "Hi");

// Named arguments
greet(name: "Bob", greeting: "Hey");

// Named but different order
greet(greeting: "Howdy", name: "Charlie");
?>
```

---

## Mixing Positional and Named Arguments

```php
<?php
function createUser(string $username, string $email, string $password, bool $verified = false) {
    echo "User: $username, Email: $email, Verified: $verified\n";
}

// Mix positional and named
createUser("john_doe", "john@example.com", password: "secure123");

// Positional followed by named
createUser("jane_doe", email: "jane@example.com", password: "secret456", verified: true);
?>
```

---

## With Multiple Parameters

```php
<?php
function calculateBill(float $amount, float $taxRate, float $discount = 0, bool $applyShipping = true) {
    $taxAmount = $amount * $taxRate;
    $discountAmount = $amount * $discount;
    $shipping = $applyShipping ? 10 : 0;
    
    return ($amount + $taxAmount - $discountAmount + $shipping);
}

// Clear and readable
$total = calculateBill(
    amount: 100,
    taxRate: 0.1,
    discount: 0.05,
    applyShipping: true
);

echo "Total: $total\n";
?>
```

---

## In Classes and Methods

```php
<?php
class Database {
    public function __construct(
        string $host,
        int $port,
        string $database,
        string $username = "root",
        string $password = ""
    ) {
        echo "Connecting to $database at $host:$port\n";
    }
    
    public function query(string $sql, array $params = [], bool $fetch = true) {
        echo "Executing: $sql with fetch=$fetch\n";
    }
}

// Named arguments in constructor
$db = new Database(
    host: "localhost",
    database: "myapp",
    port: 3306,
    username: "admin",
    password: "secret"
);

// Named arguments in method
$db->query(
    sql: "SELECT * FROM users WHERE id = ?",
    params: [1],
    fetch: true
);
?>
```

---

## With Variadic Functions

```php
<?php
function formatMessage(string $template, string ...$placeholders) {
    foreach ($placeholders as $placeholder) {
        $template = str_replace('{}', $placeholder, $template);
    }
    return $template;
}

// Named arguments don't work well with variadic, but you can name the template
$message = formatMessage(template: "Hello {} from {}", "Alice", "PHP");

echo $message . "\n";
?>
```

---

## Use Cases and Benefits

### 1. Self-Documenting Code

```php
<?php
function sendEmail(string $to, string $subject, string $body, bool $isHtml = false) {
    echo "Email to $to: $subject\n";
}

// Less clear without names
sendEmail("user@example.com", "Welcome", "Hello!", false);

// Crystal clear with names
sendEmail(
    to: "user@example.com",
    subject: "Welcome",
    body: "Hello!",
    isHtml: false
);
?>
```

### 2. Flexible Parameter Order

```php
<?php
class APIClient {
    public function request(string $method, string $url, array $options = [], int $timeout = 30) {
        echo "Request: $method $url\n";
    }
}

$client = new APIClient();

// Skip middle parameters easily
$client->request(
    method: "GET",
    url: "https://api.example.com/users",
    timeout: 60
);
?>
```

### 3. Builder-like Syntax

```php
<?php
function createReport(
    string $title,
    array $data = [],
    string $format = "pdf",
    bool $includeCharts = true,
    string $orientation = "portrait"
) {
    echo "Report: $title ($format, $orientation)\n";
}

// Named arguments provide builder-like clarity
createReport(
    title: "Sales Report",
    data: [1, 2, 3],
    format: "excel",
    includeCharts: true,
    orientation: "landscape"
);
?>
```

---

## Best Practices

### 1. Use Named Arguments for Configuration

```php
<?php
class Logger {
    public function __construct(
        string $filename,
        string $level = "INFO",
        bool $timestamps = true,
        string $format = "json"
    ) {}
}

// Better readability
$logger = new Logger(
    filename: "app.log",
    level: "DEBUG",
    timestamps: true,
    format: "json"
);
?>
```

### 2. Document Named Arguments

```php
<?php
/**
 * Send notification to user
 *
 * @param string $userId User identifier
 * @param string $title Notification title
 * @param string $body Notification body
 * @param bool $urgent Mark as urgent priority
 * @param array $metadata Custom metadata
 */
function notifyUser(
    string $userId,
    string $title,
    string $body,
    bool $urgent = false,
    array $metadata = []
) {
    echo "Notifying $userId\n";
}

notifyUser(
    userId: "user123",
    title: "Important Update",
    body: "Your account needs attention",
    urgent: true
);
?>
```

### 3. Keep Positional for Simple Cases

```php
<?php
// ✅ Good - simple function, positional is clear
strlen("hello");
explode(",", "a,b,c");

// ✅ Also good - named for clarity on complex function
explode(separator: ",", string: "a,b,c");
?>
```

---

## Common Mistakes

### 1. Invalid Parameter Names

```php
<?php
function test(string $param) {}

// ❌ Wrong - parameter name doesn't exist
test(paramName: "value");

// ✅ Correct
test(param: "value");
?>
```

### 2. Positional After Named

```php
<?php
function process(string $a, string $b, string $c) {}

// ❌ Wrong - positional after named
process(a: "first", "second", c: "third");

// ✅ Correct
process(a: "first", b: "second", c: "third");
?>
```

### 3. Duplicate Parameter Names

```php
<?php
function config(string $host, int $port) {}

// ❌ Wrong - duplicate parameters
config(host: "localhost", host: "127.0.0.1", port: 3306);

// ✅ Correct
config(host: "localhost", port: 3306);
?>
```

---

## Complete Example

```php
<?php
class PaymentProcessor {
    public function processPayment(
        string $orderId,
        float $amount,
        string $paymentMethod = "credit_card",
        bool $capture = true,
        array $metadata = [],
        string $currency = "USD"
    ): bool {
        echo "Processing payment:\n";
        echo "  Order: $orderId\n";
        echo "  Amount: $amount $currency\n";
        echo "  Method: $paymentMethod\n";
        echo "  Capture: " . ($capture ? "yes" : "no") . "\n";
        echo "  Metadata: " . json_encode($metadata) . "\n";
        return true;
    }
}

$processor = new PaymentProcessor();

// Clear, self-documenting call
$result = $processor->processPayment(
    orderId: "ORD-2024-001",
    amount: 99.99,
    paymentMethod: "stripe",
    capture: true,
    metadata: ["user_id" => "123", "source" => "web"],
    currency: "USD"
);

if ($result) {
    echo "Payment successful!\n";
}
?>
```

---

## See Also

- Documentation: [Named Arguments](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments)
- Related: [Union Types](5-union-types.md), [Constructor Property Promotion](3-constructor-property-promotion.md)
