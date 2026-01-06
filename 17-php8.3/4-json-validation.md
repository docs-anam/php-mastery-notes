# JSON Validation

## Overview

Learn about the new json_validate() function in PHP 8.3, which provides efficient JSON validation without the performance overhead of full decoding.

---

## Table of Contents

1. What is json_validate()
2. Basic Syntax
3. Performance Benefits
4. Use Cases
5. Error Handling
6. Practical Examples
7. Integration Patterns
8. Complete Examples

---

## What is json_validate()

### Purpose

```php
<?php
// Before PHP 8.3: Validating JSON required full decode
$json = '{"name":"John","email":"john@example.com"}';

// Old method 1: Check after decode
$data = json_decode($json);
if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    echo "Invalid JSON";
} else {
    // Process data - but already decoded!
}

// Old method 2: Multiple operations
$decoded = json_decode($json);
$valid = $decoded !== null;
$encoded = json_encode($decoded);  // Extra work

// Problems:
// ❌ Full decode overhead
// ❌ Extra memory usage
// ❌ Slower validation
// ❌ Multiple operations needed

// PHP 8.3 Solution: json_validate()
if (json_validate($json)) {
    // Very fast, no decoding yet
    $data = json_decode($json, true);  // Now decode when needed
} else {
    echo "Invalid JSON";
}

// Benefits:
// ✓ Fast validation
// ✓ No unnecessary memory
// ✓ Clean API
// ✓ Perfect for pre-checks
```

### Function Signature

```php
<?php
// Syntax: json_validate(string $json, int $depth = 512, int $flags = 0): bool

// Returns:
// true  - Valid JSON
// false - Invalid JSON

// Parameters:
// $json  - JSON string to validate
// $depth - Maximum recursion depth (default 512)
// $flags - JSON decode flags (JSON_INVALID_UTF8_IGNORE, etc.)

if (json_validate('{"key":"value"}')) {
    echo "Valid";
}
```

---

## Basic Usage

### Simple Validation

```php
<?php
// Validate JSON strings
$validJson = '{"name":"John"}';
$invalidJson = '{invalid json}';
$emptyJson = '';

var_dump(json_validate($validJson));      // true
var_dump(json_validate($invalidJson));    // false
var_dump(json_validate($emptyJson));      // false

// Validate JSON arrays
$validArray = '[1, 2, 3, 4, 5]';
$invalidArray = '[1, 2, 3,]';  // Trailing comma not allowed in JSON

var_dump(json_validate($validArray));     // true
var_dump(json_validate($invalidArray));   // false

// Validate complex structures
$complex = json_encode([
    'user' => [
        'id' => 1,
        'name' => 'John',
        'roles' => ['admin', 'user'],
    ],
]);

var_dump(json_validate($complex));  // true
```

### Depth Parameter

```php
<?php
// Validate with depth constraints
$json = json_encode([
    'level1' => [
        'level2' => [
            'level3' => [
                'level4' => 'value'
            ]
        ]
    ]
]);

// Default depth (512)
var_dump(json_validate($json));           // true

// Limited depth
var_dump(json_validate($json, 2));        // false (too deep)
var_dump(json_validate($json, 5));        // true
var_dump(json_validate($json, 10));       // true

// Use depth to prevent denial-of-service attacks
// Set reasonable limits for untrusted input
if (json_validate($userInput, 10)) {
    $data = json_decode($userInput, true);
}
```

---

## Performance Comparison

### Benchmark Results

```php
<?php
// Performance test: Validation only

$largeJson = json_encode(array_fill(0, 1000, [
    'id' => 1,
    'name' => 'User',
    'email' => 'user@example.com',
]));

echo "JSON size: " . strlen($largeJson) . " bytes\n\n";

// Method 1: Old way (full decode + check)
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    $data = json_decode($largeJson);
    $valid = $data !== null;
}
$oldTime = (microtime(true) - $start) * 1000;
echo "Old method (decode + check): {$oldTime}ms\n";

// Method 2: Using json_validate()
$start = microtime(true);
for ($i = 0; $i < 10000; $i++) {
    $valid = json_validate($largeJson);
}
$newTime = (microtime(true) - $start) * 1000;
echo "json_validate(): {$newTime}ms\n";

// Show improvement
$improvement = (($oldTime - $newTime) / $oldTime) * 100;
echo "Improvement: " . round($improvement, 2) . "%\n";
// Typical: 50-70% faster
```

### Memory Usage

```php
<?php
// Memory comparison
$json = json_encode(array_fill(0, 10000, 'item'));

// Old way: Full decode
$memBefore = memory_get_usage();
$data = json_decode($json);
$memAfter = memory_get_usage();
$decodedMemory = $memAfter - $memBefore;

echo "Full decode memory: " . number_format($decodedMemory) . " bytes\n";

// New way: Just validate
$memBefore = memory_get_usage();
$valid = json_validate($json);
$memAfter = memory_get_usage();
$validatedMemory = $memAfter - $memBefore;

echo "Validate memory: " . number_format($validatedMemory) . " bytes\n";

// Typical: 95%+ memory savings when only validating
```

---

## Practical Use Cases

### API Input Validation

```php
<?php
class JsonApiHandler
{
    public function handleRequest(string $body): array
    {
        // Fast validation first
        if (!json_validate($body, depth: 20)) {
            return ['error' => 'Invalid JSON'];
        }

        // Safe to decode
        $data = json_decode($body, true);

        // Further validation
        if (!$this->validateStructure($data)) {
            return ['error' => 'Invalid structure'];
        }

        return ['success' => true, 'data' => $data];
    }

    private function validateStructure(array $data): bool
    {
        return isset($data['action'], $data['payload']);
    }
}

// Usage
$handler = new JsonApiHandler();
$response = $handler->handleRequest($_POST['json'] ?? '');
```

### Configuration File Validation

```php
<?php
class ConfigValidator
{
    public function loadConfig(string $filepath): array
    {
        // Read file
        if (!file_exists($filepath)) {
            throw new Exception("Config file not found");
        }

        $content = file_get_contents($filepath);

        // Validate JSON format first
        if (!json_validate($content)) {
            throw new Exception("Invalid JSON in config file");
        }

        // Parse safely
        $config = json_decode($content, true);

        // Validate content
        if (!$this->validateConfigContent($config)) {
            throw new Exception("Invalid config structure");
        }

        return $config;
    }

    private function validateConfigContent(array $config): bool
    {
        return isset($config['app'], $config['database']);
    }
}

// Usage
$validator = new ConfigValidator();
$config = $validator->loadConfig('config.json');
```

### Stream Processing

```php
<?php
class JsonStreamProcessor
{
    public function processStream($stream): void
    {
        while (!feof($stream)) {
            $line = fgets($stream);

            // Skip invalid JSON lines
            if (!json_validate($line)) {
                continue;
            }

            // Process valid JSON
            $data = json_decode($line, true);
            $this->handle($data);
        }
    }

    private function handle(array $data): void
    {
        // Process data
    }
}

// Usage: Process JSONL (JSON Lines) format
$processor = new JsonStreamProcessor();
$processor->processStream(fopen('data.jsonl', 'r'));
```

### Batch Processing

```php
<?php
class BatchProcessor
{
    public function processBatch(array $items): array
    {
        $results = [];

        foreach ($items as $index => $json) {
            // Fast validation
            if (!json_validate($json)) {
                $results[$index] = ['status' => 'invalid'];
                continue;
            }

            // Only decode valid JSON
            $data = json_decode($json, true);
            $results[$index] = $this->processItem($data);
        }

        return $results;
    }

    private function processItem(array $data): array
    {
        return ['status' => 'processed', 'data' => $data];
    }
}

// Usage
$batch = [
    '{"id":1}',
    'invalid json',
    '{"id":2}',
    '{"id":3}',
];

$processor = new BatchProcessor();
$results = $processor->processBatch($batch);
```

---

## Error Handling

### Checking Validation

```php
<?php
// Simple check
if (json_validate($json)) {
    $data = json_decode($json, true);
} else {
    // Handle invalid JSON
}

// With error details (if needed, use json_decode)
$data = json_decode($json, true);
if ($data === null) {
    $error = json_last_error_msg();
    echo "Error: $error";
}

// Best practice: Use validate first, then decode
function safeJsonDecode(string $json, bool $assoc = true): mixed
{
    if (!json_validate($json)) {
        return null;
    }

    return json_decode($json, $assoc);
}

$data = safeJsonDecode($json);
```

### Depth Limiting

```php
<?php
class SecureJsonHandler
{
    private int $maxDepth = 10;

    public function validate(string $json): bool
    {
        // Prevent stack overflow attacks
        return json_validate($json, depth: $this->maxDepth);
    }

    public function decode(string $json): ?array
    {
        if (!$this->validate($json)) {
            return null;
        }

        return json_decode($json, true);
    }
}

// Usage
$handler = new SecureJsonHandler();
$data = $handler->decode($untrustedJson);
```

---

## Complete Example

### Full API Handler

```php
<?php
declare(strict_types=1);

namespace App\Http;

class JsonRequestHandler
{
    private int $maxDepth = 20;
    private int $maxSize = 10_485_760;  // 10MB

    public function handle(string $body): Response
    {
        // 1. Check size
        if (strlen($body) > $this->maxSize) {
            return new Response(400, 'Payload too large');
        }

        // 2. Validate JSON
        if (!json_validate($body, depth: $this->maxDepth)) {
            return new Response(400, 'Invalid JSON');
        }

        // 3. Decode
        $data = json_decode($body, true);
        if (!is_array($data)) {
            return new Response(400, 'Expected JSON object');
        }

        // 4. Process
        return $this->processRequest($data);
    }

    private function processRequest(array $data): Response
    {
        // Validate required fields
        if (!isset($data['action'], $data['payload'])) {
            return new Response(400, 'Missing required fields');
        }

        try {
            $result = match ($data['action']) {
                'create' => $this->create($data['payload']),
                'update' => $this->update($data['payload']),
                'delete' => $this->delete($data['payload']),
                default => throw new Exception('Unknown action'),
            };

            return new Response(200, $result);
        } catch (Exception $e) {
            return new Response(500, $e->getMessage());
        }
    }

    private function create(array $payload): array
    {
        // Create operation
        return ['id' => 1, 'created' => true];
    }

    private function update(array $payload): array
    {
        return ['updated' => true];
    }

    private function delete(array $payload): array
    {
        return ['deleted' => true];
    }
}

class Response
{
    public function __construct(
        private int $status,
        private mixed $body,
    ) {}

    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: application/json');
        echo json_encode(['status' => $this->status, 'body' => $this->body]);
    }
}

// Usage
$handler = new JsonRequestHandler();
$response = $handler->handle(file_get_contents('php://input'));
$response->send();
```

---

## See Also

- [PHP 8.3 Overview](0-php8.3-overview.md)
- [Array Functions](7-array-functions.md)
- [Random Improvements](5-random-improvements.md)
