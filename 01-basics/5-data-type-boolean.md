# Booleans - True and False Values

## Table of Contents
1. [What are Booleans?](#what-are-booleans)
2. [Boolean Values](#boolean-values)
3. [Type Casting to Boolean](#type-casting-to-boolean)
4. [Truthy and Falsy](#truthy-and-falsy)
5. [Boolean Functions](#boolean-functions)
6. [Logical Operations](#logical-operations)
7. [Common Patterns](#common-patterns)
8. [Common Mistakes](#common-mistakes)

---

## What are Booleans?

A boolean is the simplest data type in PHP. It has only two possible values:

- **true** - Represents truth, yes, or on
- **false** - Represents falsehood, no, or off

```php
<?php
$isLoggedIn = true;
$isAdmin = false;

if ($isLoggedIn) {
    echo "User is logged in";
}
?>
```

---

## Boolean Values

### Creating Booleans

```php
<?php
// Explicit assignment
$active = true;
$deleted = false;

// From comparison
$result = (5 > 3);   // true
$result = (5 < 3);   // false

// From logical operations
$result = (true && true);    // true
$result = (true && false);   // false
?>
```

### Naming Convention

Use descriptive names starting with `is`, `has`, or `can`:

```php
<?php
// ✅ Good names
$isActive = true;
$hasPermission = false;
$canDelete = true;
$isAdmin = false;
$isPremium = true;

// ❌ Poor names
$flag = true;        // Unclear
$active = true;      // Could be int or string
$x = false;          // Meaningless
?>
```

---

## Type Casting to Boolean

Converting other types to boolean:

```php
<?php
// Explicit casting
$bool = (bool)1;         // true
$bool = (bool)0;         // false
$bool = (bool)"text";    // true
$bool = (bool)"";        // false
$bool = (bool)[];        // false
$bool = (bool)[1, 2];    // true

// Using boolval()
$bool = boolval(1);      // true
$bool = boolval(0);      // false
$bool = boolval("0");    // false
$bool = boolval("text"); // true
?>
```

---

## Truthy and Falsy

PHP automatically converts values to boolean in certain contexts.

### Falsy Values (Convert to false)

```php
<?php
// These convert to false:
false           // Explicitly false
0               // Zero
0.0             // Zero float
""              // Empty string
"0"             // String zero
[]              // Empty array
null            // NULL value

// Examples
if (0) { echo "Won't run"; }
if ("") { echo "Won't run"; }
if (null) { echo "Won't run"; }
if ([]) { echo "Won't run"; }
?>
```

### Truthy Values (Convert to true)

```php
<?php
// Everything else is true:
true            // Explicitly true
1, -1, 42, etc. // Non-zero numbers
1.5, -2.3, etc. // Non-zero floats
"0text"         // String with content (even "0text")
" "             // Whitespace
[0]             // Non-empty array (even with falsy values)
new stdClass()  // Objects

// Examples
if (1) { echo "Runs"; }
if ("text") { echo "Runs"; }
if ([1]) { echo "Runs"; }
if (new stdClass()) { echo "Runs"; }
?>
```

### Practical Examples

```php
<?php
// Check if string is empty
$input = "";
if ($input) {
    echo "Has value";
} else {
    echo "Empty string";  // This runs
}

// Check if array has items
$items = [];
if ($items) {
    echo "Has items";
} else {
    echo "Empty array";  // This runs
}

// Check if array has items (explicit)
if (count($items) > 0) {
    echo "Has items";
}

// Check if number is non-zero
$count = 0;
if ($count) {
    echo "Has items";
} else {
    echo "No items";  // This runs
}
?>
```

---

## Boolean Functions

### Checking Boolean Values

```php
<?php
$value = true;

// Check if boolean
is_bool($value);    // true
is_bool(1);         // false
is_bool("true");    // false

// Check other types
is_int(42);         // true
is_float(3.14);     // true
is_string("text");  // true
is_array([]);       // true
is_null(null);      // true
?>
```

### Boolean Comparisons

```php
<?php
// Strict comparison (recommended)
true === true;      // true
true === 1;         // false
false === 0;        // false
false === "";       // false

// Loose comparison (risky)
true == 1;          // true
true == "1";        // true
false == 0;         // true
false == "";        // true
false == "0";       // true
false == null;      // true
?>
```

---

## Logical Operations

### AND Operator (&&)

Both must be true:

```php
<?php
$age = 25;
$hasLicense = true;

if ($age >= 18 && $hasLicense) {
    echo "Can drive";
}
?>
```

### OR Operator (||)

At least one must be true:

```php
<?php
$isWeekend = true;
$isHoliday = false;

if ($isWeekend || $isHoliday) {
    echo "Day off";  // This runs
}
?>
```

### NOT Operator (!)

Reverses the value:

```php
<?php
$isActive = false;

if (!$isActive) {
    echo "Inactive";  // This runs
}

if (!true) { echo "Won't run"; }
if (!false) { echo "Runs"; }
?>
```

---

## Common Patterns

### Validation Pattern

```php
<?php
$email = "user@example.com";
$isValid = false;

// Check all conditions
if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $isValid = true;
}

echo $isValid ? "Valid" : "Invalid";
?>
```

### Toggle Pattern

```php
<?php
$isEnabled = false;

// Toggle value
$isEnabled = !$isEnabled;  // true

// Or:
$isEnabled = !$isEnabled;  // false
?>
```

### Flag Pattern

```php
<?php
$hasErrors = false;

// Check multiple conditions
if (empty($name)) {
    $hasErrors = true;
}
if (empty($email)) {
    $hasErrors = true;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $hasErrors = true;
}

// Report result
if ($hasErrors) {
    echo "Fix errors";
} else {
    echo "Form is valid";
}
?>
```

---

## Common Mistakes

### 1. Confusing Truthy/Falsy

```php
<?php
// ❌ Wrong: assuming "0" is true
if ("0") {
    echo "Runs";
} else {
    echo "Won't run";  // This runs - "0" is falsy!
}

// ✅ Correct: explicit check
if ("0" !== "") {
    echo "Has value";
}
?>
```

### 2. Using == Instead of ===

```php
<?php
// ❌ Risky: loose comparison
if ($value == true) {
    // Matches too many values
}

// ✅ Better: strict comparison
if ($value === true) {
    // Matches only true
}
?>
```

### 3. Unnecessary Ternary

```php
<?php
// ❌ Redundant
$result = $isActive == true ? true : false;

// ✅ Better
$result = $isActive;

// ✅ Or explicitly
$result = (bool)$isActive;
?>
```

### 4. Missing Negation

```php
<?php
$isInactive = true;

// ❌ Confusing variable name with opposite meaning
if ($isInactive) {
    echo "Active";  // Misleading!
}

// ✅ Better: use consistent naming
$isActive = false;
if (!$isActive) {
    echo "Inactive";
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

// User validation
function isValidUser(array $user): bool {
    $hasName = !empty($user['name']);
    $hasEmail = !empty($user['email']);
    $hasAge = isset($user['age']) && $user['age'] >= 0;
    $validEmail = filter_var($user['email'] ?? '', FILTER_VALIDATE_EMAIL);
    
    return $hasName && $hasEmail && $hasAge && $validEmail;
}

// Test cases
$user1 = [
    'name' => 'Alice',
    'email' => 'alice@example.com',
    'age' => 25
];

$user2 = [
    'name' => 'Bob',
    'email' => 'invalid-email',
    'age' => 30
];

$user3 = [
    'name' => '',
    'email' => 'charlie@example.com',
    'age' => 28
];

echo "User 1 valid: " . (isValidUser($user1) ? "Yes" : "No") . "\n";  // Yes
echo "User 2 valid: " . (isValidUser($user2) ? "Yes" : "No") . "\n";  // No
echo "User 3 valid: " . (isValidUser($user3) ? "Yes" : "No") . "\n";  // No
?>
```

**Output:**
```
User 1 valid: Yes
User 2 valid: No
User 3 valid: No
```

---

## Next Steps

✅ Understand booleans  
→ Learn [logical operators](13-operators-logical.md)  
→ Study [conditionals](18-if-statement.md)  
→ Practice [comparisons](12-operators-comparison.md)
