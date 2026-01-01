# NULL Data Type

## What is NULL?

NULL is a special data type in PHP that represents a variable with no value. It indicates the absence of a value.

A variable is considered NULL if:
1. It has been explicitly assigned the constant `NULL`
2. It has not been set yet (undefined)
3. It has been unset with `unset()`

```php
<?php
$empty = null;           // Explicitly set to NULL
$undefined;              // Not set, is NULL
$removed = "value";
unset($removed);         // Now NULL after unset

var_dump($empty);        // NULL
var_dump($undefined);    // NULL
var_dump($removed);      // NULL
?>
```

## Creating NULL Values

### Explicit Assignment

```php
<?php
$var1 = null;      // Lowercase null
$var2 = NULL;      // Uppercase NULL (same thing)
$var3 = Null;      // Mixed case Null (all work)

var_dump($var1);   // NULL
var_dump($var2);   // NULL
var_dump($var3);   // NULL
?>
```

**Important:** NULL is case-insensitive in PHP.

### Unsetting Variables

```php
<?php
$value = 100;
echo $value;       // 100

unset($value);     // Remove from memory
// echo $value;    // ERROR! Undefined variable

// Check if unset
if (!isset($value)) {
    echo "Variable is not set";
}
?>
```

## Checking for NULL

### is_null() Function

```php
<?php
$nothing = null;
$something = "value";

if (is_null($nothing)) {
    echo "Variable is NULL";  // This executes
}

if (is_null($something)) {
    echo "Variable is NULL";  // This does NOT execute
}
?>
```

### isset() Function

```php
<?php
$var = null;
$undefined_var;

// isset() returns false if variable is NULL or not set
var_dump(isset($var));             // false (is NULL)
var_dump(isset($undefined_var));   // false (not set)

// isset() returns true if variable has a value
$filled = "value";
var_dump(isset($filled));          // true
?>
```

### empty() Function

```php
<?php
// empty() considers these as empty:
empty(null);       // true
empty("");         // true
empty(0);          // true
empty("0");        // true
empty([]);         // true
empty(false);      // true

// empty() considers these as NOT empty:
empty("hello");    // false
empty(1);          // false
empty([1, 2]);     // false
empty(true);       // false
?>
```

## Difference Between NULL and Other Values

### NULL vs False

```php
<?php
$null_var = null;
$false_var = false;

// These are NOT the same!
if ($null_var === null) {
    echo "Null";        // This executes
}

if ($false_var === false) {
    echo "False";       // This executes
}

// Loose comparison (==) treats them the same
$null_var == $false_var;    // true (loose)
$null_var === $false_var;   // false (strict)
?>
```

### NULL vs Empty String

```php
<?php
$null_str = null;
$empty_str = "";

var_dump($null_str);       // NULL
var_dump($empty_str);      // string(0) ""

// Strict comparison shows difference
$null_str === $empty_str;  // false

// But they're both empty:
empty($null_str);          // true
empty($empty_str);         // true
?>
```

### NULL vs Zero

```php
<?php
$null_val = null;
$zero_val = 0;

var_dump($null_val);       // NULL
var_dump($zero_val);       // int(0)

// Different in strict comparison
$null_val === $zero_val;   // false

// Both falsy
if (!$null_val) { echo "Falsy"; }   // Executes
if (!$zero_val) { echo "Falsy"; }   // Executes
?>
```

## Practical Use Cases

### Optional Function Parameters

```php
<?php
function greet($name = null, $greeting = "Hello") {
    if ($name === null) {
        echo "$greeting, Guest!";
    } else {
        echo "$greeting, $name!";
    }
}

greet();                    // Hello, Guest!
greet("Alice");             // Hello, Alice!
greet("Bob", "Hi");         // Hi, Bob!
?>
```

### Checking Database Results

```php
<?php
// Simulating a database query
function findUser($id) {
    // Return null if user not found
    $users = [1 => "Alice", 2 => "Bob"];
    return $users[$id] ?? null;
}

$user = findUser(3);  // Returns null

if ($user === null) {
    echo "User not found";
} else {
    echo "User: $user";
}
?>
```

### Handling Missing Configuration

```php
<?php
class Config {
    private $settings = ['debug' => true];
    
    public function get($key) {
        return $this->settings[$key] ?? null;
    }
}

$config = new Config();
$timeout = $config->get('timeout');  // Returns null

if ($timeout === null) {
    $timeout = 30;  // Use default
}

echo "Timeout: $timeout";  // Timeout: 30
?>
```

### Null Coalescing Operator

PHP 7+ provides a convenient way to handle NULL values:

```php
<?php
$name = null;

// Null Coalescing Operator (??)
// Returns first non-null value
$displayName = $name ?? "Guest";
echo $displayName;  // Guest

// Can chain multiple
$first = null;
$second = null;
$third = "value";
echo $first ?? $second ?? $third ?? "default";  // value
?>
```

### Null Safe Operator (PHP 8+)

```php
<?php
class User {
    public $profile = null;
}

$user = new User();

// Old way: Check each level
if ($user && $user->profile && $user->profile->name) {
    echo $user->profile->name;
}

// PHP 8+ Null Safe Operator
echo $user?->profile?->name;  // Safely checks each level
?>
```

## Common Mistakes

### Mistake 1: Confusing isset() and is_null()

```php
<?php
$var = null;

// isset() checks if variable is set AND not null
isset($var);       // false (is NULL)

// is_null() checks if value is exactly NULL
is_null($var);     // true

// These are opposite!
isset($var) === !is_null($var);  // true
?>
```

### Mistake 2: Not Checking for NULL Before Using

```php
<?php
// WRONG: No NULL check
$result = getUser(999);
echo $result['name'];  // ERROR if $result is NULL!

// CORRECT: Check first
$result = getUser(999);
if ($result !== null) {
    echo $result['name'];
} else {
    echo "User not found";
}

// Or use Null Coalescing
echo $result['name'] ?? "User not found";
?>
```

### Mistake 3: Using == Instead of ===

```php
<?php
$null_val = null;
$zero_val = 0;

// WRONG: Loose comparison treats them the same
if ($null_val == $zero_val) {
    echo "Same";  // This executes!
}

// CORRECT: Strict comparison
if ($null_val === $zero_val) {
    echo "Same";  // This does NOT execute
}
?>
```

## Best Practices

1. **Use Strict Comparison** (`===` and `!==`) when checking for NULL
2. **Use Null Coalescing Operator** (`??`) for default values
3. **Use Null Safe Operator** (`?->`) in PHP 8+ for safe navigation
4. **Always validate inputs** that might be NULL
5. **Document which functions return NULL** for missing values
6. **Use type hints** to indicate nullable values (PHP 7.1+):

```php
<?php
// Function that might return null
function find(?string $id): ?string {
    return $id === null ? null : "Found";
}

// Parameter that can be null
function process(?User $user): void {
    // $user might be null
}
?>
```

## Key Takeaways

✓ NULL represents the absence of a value
✓ A variable is NULL if explicitly assigned, undefined, or unset
✓ NULL is case-insensitive: `null`, `NULL`, `Null` all work
✓ Use `is_null()` to check if a value is NULL
✓ Use `isset()` to check if a variable is set and not NULL
✓ Use empty() to check if a value is falsy
✓ NULL differs from false, 0, "", and []
✓ Use `===` for strict comparison with NULL
✓ Use `??` (Null Coalescing) for default values
✓ NULL is useful for optional parameters and missing data
