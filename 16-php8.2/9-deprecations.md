# Deprecations and Breaking Changes

## Overview

Learn about deprecated features in PHP 8.2 and breaking changes that may affect your code, with migration strategies.

---

## Table of Contents

1. PHP 8.2 Deprecations
2. Breaking Changes
3. Removed Features
4. Migration Guide
5. Compatibility Considerations
6. Upgrade Path
7. Handling Deprecations
8. Complete Examples

---

## PHP 8.2 Deprecations

### Notable Deprecated Features

```php
<?php
// Deprecations in PHP 8.2

// 1. Dynamic properties with strict typing
class User
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        // Deprecated: Adding dynamic property to typed class
        // $this->email = 'test@example.com';  // Emits deprecation notice
    }
}

// 2. Calling non-static methods statically (continues from 8.1)
class Calculator
{
    public function add(int $a, int $b): int
    {
        return $a + $b;
    }
}

// Deprecated:
// $result = Calculator::add(5, 3);  // Emits deprecation notice

// 3. Case-insensitive constants
// define('MY_CONST', 'value', true);  // Deprecated 4th parameter

// 4. Partial numeric strings
// (int)'42abc';  // Emits deprecation notice
// Should use: (int)'42'

// 5. Implicitly nullable function parameters
// function test(string $param = null) {}  // Deprecated
// Should be: function test(?string $param = null) {}
```

### Deprecation Warnings

```php
<?php
// Configure deprecation handling

// In php.ini:
/*
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
display_errors = On
log_errors = On
error_log = /var/log/php_errors.log
*/

// Handle deprecations programmatically
set_error_handler(function($severity, $message, $file, $line) {
    if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
        echo "Deprecation in $file:$line - $message\n";
        return true;  // Suppress further handling
    }
    return false;
});

// Test deprecation detection
function testDeprecation()
{
    trigger_error('This feature is deprecated', E_USER_DEPRECATED);
}

testDeprecation();
```

---

## Breaking Changes

### Removed Features

```php
<?php
// Features removed in PHP 8.2 or deprecated for removal

// 1. PDO::FETCH_SERIALIZE removed
// Code that relied on this will fail
// Deprecated in 8.1, removed in 8.2

// 2. php.ini settings removed
// mbstring.func_overload (removed in 8.0, but effects continue)
// short_open_tags handling (though still works with explicit setting)

// 3. Type behavior changes
// Some type coercions changed:

// Before PHP 8.2:
// (array)null === []
// After: Still true, but stricter in some edge cases

// 4. Object initialization order
// Changes in how constructor arguments are processed
```

### Strict Mode Changes

```php
<?php
// Strict type declarations affect more scenarios

// Function parameter coercion stricter
function processNumber(int $num): void
{
    echo "Number: $num\n";
}

// Before: PHP 8.1
// processNumber('42');  // Works, converts string
// processNumber('42abc');  // Works with warning, converts to 42

// After: PHP 8.2
// processNumber('42');  // Works, converts string to 42
// processNumber('42abc');  // Emits deprecation notice
// In future: Will throw TypeError

// Array key type changes
$array = [];
$array[true] = 'a';    // Converts to 1
$array[false] = 'b';   // Converts to 0
$array[null] = 'c';    // Converts to ''
$array[1.5] = 'd';     // Converts to 1

echo json_encode($array);  // Behavior may differ
```

---

## Migration Guide

### From PHP 8.1 to 8.2

```php
<?php
// Step 1: Identify deprecated features

// Check for dynamic properties
class OldStyle
{
    // No typed properties
    public $data;
}

// Better:
class NewStyle
{
    public mixed $data;  // Or specific type
}

// Step 2: Use explicit nullable types
// ❌ Old way
function process(string $email = null)
{
    // Process email
}

// ✓ New way
function process(?string $email = null): void
{
    if ($email === null) {
        throw new InvalidArgumentException("Email required");
    }
}

// Step 3: Avoid partial numeric strings
// ❌ Old way
$value = (int)'123abc';  // Deprecated

// ✓ New way
$value = (int)'123';
// Or validate first:
if (preg_match('/^\d+/', '123abc', $matches)) {
    $value = (int)$matches[0];
}

// Step 4: Fix static method calls
// ❌ Old way
class Calculator
{
    public function add(int $a, int $b): int
    {
        return $a + $b;
    }
}
$result = Calculator::add(5, 3);  // Wrong: non-static method called statically

// ✓ New way
class CalculatorStatic
{
    public static function add(int $a, int $b): int
    {
        return $a + $b;
    }
}
$result = CalculatorStatic::add(5, 3);  // Correct
```

### Compatibility Checker Script

```php
<?php
declare(strict_types=1);

class CompatibilityChecker
{
    private array $issues = [];

    public function checkString(string $content): array
    {
        $this->issues = [];

        // Check for dynamic properties
        if (preg_match('/\$this->\$\w+\s*=/', $content)) {
            $this->issues[] = "Dynamic property assignment detected";
        }

        // Check for partial numeric strings
        if (preg_match('/\(int\)["\']\\d+\w/', $content)) {
            $this->issues[] = "Partial numeric string casting detected";
        }

        // Check for deprecated error control
        if (strpos($content, '@') !== false) {
            $this->issues[] = "Error suppression operator (@) used";
        }

        // Check for implicitly nullable parameters
        if (preg_match('/function\s+\w+\s*\([^)]*\s+(\$\w+)\s*=\s*null/', $content)) {
            $this->issues[] = "Implicitly nullable parameter detected";
        }

        return $this->issues;
    }

    public function checkFile(string $filepath): array
    {
        $content = file_get_contents($filepath);
        return $this->checkString($content);
    }
}

// Usage
$checker = new CompatibilityChecker();
$issues = $checker->checkFile('legacy_code.php');

if (!empty($issues)) {
    echo "PHP 8.2 Compatibility Issues:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
}
```

---

## Handling Deprecations

### Error Handler for Deprecations

```php
<?php
// Graceful deprecation handling

class DeprecationHandler
{
    private array $deprecations = [];

    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
    }

    public function handleError(
        int $severity,
        string $message,
        string $file,
        int $line
    ): bool {
        if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
            $this->deprecations[] = [
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'time' => date('Y-m-d H:i:s'),
            ];

            // Log the deprecation
            error_log("Deprecation: $message in $file:$line");

            // Return true to prevent default handling
            return true;
        }

        return false;
    }

    public function getDeprecations(): array
    {
        return $this->deprecations;
    }

    public function reportDeprecations(): void
    {
        if (empty($this->deprecations)) {
            echo "No deprecation notices.\n";
            return;
        }

        echo "Found " . count($this->deprecations) . " deprecation(s):\n\n";

        foreach ($this->deprecations as $deprecation) {
            echo "  " . $deprecation['time'] . "\n";
            echo "  File: " . $deprecation['file'] . ':' . $deprecation['line'] . "\n";
            echo "  Message: " . $deprecation['message'] . "\n\n";
        }
    }
}

// Usage
$handler = new DeprecationHandler();
$handler->register();

// Run code...

$handler->reportDeprecations();
```

### Upgrade Checklist

```php
<?php
// Pre-upgrade checklist for PHP 8.2

class UpgradeChecklist
{
    public function getItems(): array
    {
        return [
            // Code quality
            [
                'item' => 'Run PHP compatibility checker',
                'tool' => 'PHPStan with PHP 8.2 rules',
                'status' => 'pending',
            ],
            [
                'item' => 'Update all dependencies',
                'tool' => 'composer update',
                'status' => 'pending',
            ],
            [
                'item' => 'Check framework compatibility',
                'description' => 'Verify framework and library versions',
                'status' => 'pending',
            ],

            // Code fixes
            [
                'item' => 'Fix deprecated features',
                'description' => 'Remove/replace deprecated calls',
                'status' => 'pending',
            ],
            [
                'item' => 'Add type declarations',
                'description' => 'Add missing type hints',
                'status' => 'pending',
            ],
            [
                'item' => 'Fix nullable parameters',
                'description' => 'Convert implicit to explicit nullable',
                'status' => 'pending',
            ],

            // Testing
            [
                'item' => 'Run full test suite',
                'tool' => 'PHPUnit',
                'status' => 'pending',
            ],
            [
                'item' => 'Verify in staging',
                'description' => 'Deploy to staging environment',
                'status' => 'pending',
            ],

            // Deployment
            [
                'item' => 'Update php.ini settings',
                'description' => 'Review and update configuration',
                'status' => 'pending',
            ],
            [
                'item' => 'Plan deployment',
                'description' => 'Schedule upgrade window',
                'status' => 'pending',
            ],
        ];
    }

    public function printChecklist(): void
    {
        echo "PHP 8.2 Upgrade Checklist\n";
        echo "=========================\n\n";

        foreach ($this->getItems() as $item) {
            $checkbox = $item['status'] === 'completed' ? '[✓]' : '[ ]';
            echo "$checkbox {$item['item']}\n";

            if (isset($item['description'])) {
                echo "    {$item['description']}\n";
            }
            if (isset($item['tool'])) {
                echo "    Tool: {$item['tool']}\n";
            }

            echo "\n";
        }
    }
}

// Usage
$checklist = new UpgradeChecklist();
$checklist->printChecklist();
```

---

## Compatibility Considerations

### Version-Specific Code

```php
<?php
// Handle version-specific behavior

class VersionCompat
{
    public static function isPhp82OrHigher(): bool
    {
        return version_compare(PHP_VERSION, '8.2.0', '>=');
    }

    public static function useDateTimeImmutable(): bool
    {
        // PHP 8.2 improves DateTime handling
        if (self::isPhp82OrHigher()) {
            return true;
        }
        return false;
    }

    public static function useNewStringFunctions(): bool
    {
        // str_contains, str_starts_with, str_ends_with
        return self::isPhp82OrHigher() || PHP_VERSION_ID >= 80100;
    }
}

// Usage
if (VersionCompat::isPhp82OrHigher()) {
    // Use PHP 8.2 features
    $result = str_contains($string, 'search');
} else {
    // Fallback for older versions
    $result = strpos($string, 'search') !== false;
}
```

---

## Complete Migration Example

```php
<?php
declare(strict_types=1);

namespace App\Upgrade;

// ❌ OLD CODE (PHP 8.1)
class OldUserController
{
    public $errors;  // Implicit typing

    public function processUser($data)  // No type hints
    {
        // Implicit nullable
        if ($data['email'] == null) {
            $this->errors[] = 'Email required';
        }

        return $data;
    }

    public function validate($email = null)  // Implicitly nullable
    {
        return true;
    }

    // Non-static method called as static in other code
    public function calculate($a, $b)
    {
        return $a + $b;
    }
}

// ✓ NEW CODE (PHP 8.2)
class NewUserController
{
    private array $errors = [];  // Explicit type

    public function processUser(array $data): array  // Type hints
    {
        // Explicit nullable check
        if (empty($data['email'] ?? null)) {
            $this->errors[] = 'Email required';
        }

        return $data;
    }

    public function validate(?string $email = null): bool  // Explicitly nullable
    {
        return $email !== null && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Static method for static calls
    public static function calculate(int $a, int $b): int
    {
        return $a + $b;
    }

    // Instance method wrapper if needed
    public function calculateInstance(int $a, int $b): int
    {
        return self::calculate($a, $b);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

// Migration testing
$oldController = new OldUserController();
$newController = new NewUserController();

// Test data
$userData = [
    'email' => 'john@example.com',
    'name' => 'John Doe',
];

// Old way (still works but with deprecations)
$oldResult = $oldController->processUser($userData);

// New way (PHP 8.2 compliant)
$newResult = $newController->processUser($userData);

// Old static call (deprecated)
// $result = OldUserController::calculate(5, 3);  // Error!

// New static call (correct)
$result = NewUserController::calculate(5, 3);  // Works!

echo "Migration successful!\n";
echo "Old errors: " . count($oldController->errors ?? []) . "\n";
echo "New errors: " . count($newController->getErrors()) . "\n";
```

---

## See Also

- [PHP 8.2 Overview](0-php8.2-overview.md)
- [Readonly Classes](2-readonly-classes.md)
- [DNF Types](3-dnf-types.md)
