# Null Coalescing Operator

```php
// Null Coalescing Operator (??) in PHP

// The null coalescing operator returns its first operand if it exists and is not null;
// otherwise, it returns its second operand.

// Example usage:
$username = $_GET['user'] ?? 'guest';

echo $username; // Outputs the value of $_GET['user'] if set, otherwise 'guest'

// Equivalent to:
$username = isset($_GET['user']) ? $_GET['user'] : 'guest';

// Chaining example:
$value = $a ?? $b ?? $c ?? 'default';

// The operator is available since PHP 7.0
```

