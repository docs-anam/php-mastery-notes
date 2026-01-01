# Boolean Data Type

## What is a Boolean?

A boolean is the simplest data type in PHP. It has only two possible values:
- `true` - Represents TRUE or yes
- `false` - Represents FALSE or no

Booleans are primarily used in conditional statements to control the flow of your program.

```php
<?php
$isActive = true;
$isDeleted = false;

if ($isActive) {
    echo "User is active";
}
?>
```

## Creating Booleans

### Direct Assignment

```php
<?php
// Explicit boolean values
$isOnline = true;
$isOffline = false;

// Different cases (all valid)
$yes = TRUE;      // Uppercase
$no = FALSE;      // Uppercase
$maybe = true;    // Lowercase
$maybe_not = false; // Lowercase
?>
```

### From Comparisons

Comparison operations return boolean values:

```php
<?php
// Comparison operators return booleans
$result = (5 > 3);      // true
$result = (5 < 3);      // false
$result = (5 == 5);     // true
$result = (5 != 5);     // false
$result = ("apple" === "apple"); // true

// Store comparison results
$isGreater = (10 > 5);  // true
$isEqual = ("hello" == "hello"); // true
?>
```

## Checking for Boolean

```php
<?php
$isActive = true;

// Check if variable is boolean
if (is_bool($isActive)) {
    echo "It's a boolean!";
}

// Get the value
var_dump($isActive);   // bool(true)
var_dump(false);       // bool(false)
?>
```

## Truth and Falsiness

### Truthy Values

These values are treated as `true` in boolean context:

```php
<?php
// These are truthy:
if (true)          { echo "TRUE"; }       // ✓ Direct true
if (1)             { echo "TRUE"; }       // ✓ Non-zero integer
if (-1)            { echo "TRUE"; }       // ✓ Negative integer
if (3.14)          { echo "TRUE"; }       // ✓ Non-zero float
if ("hello")       { echo "TRUE"; }       // ✓ Non-empty string
if ([0])           { echo "TRUE"; }       // ✓ Non-empty array
?>
```

### Falsy Values

These values are treated as `false` in boolean context:

```php
<?php
// These are falsy:
if (false)         { echo "FALSE"; }      // ✗ Direct false
if (0)             { echo "FALSE"; }      // ✗ Zero integer
if (0.0)           { echo "FALSE"; }      // ✗ Zero float
if ("")            { echo "FALSE"; }      // ✗ Empty string
if ("0")           { echo "FALSE"; }      // ✗ String zero
if (null)          { echo "FALSE"; }      // ✗ NULL value
if ([])            { echo "FALSE"; }      // ✗ Empty array
?>
```

## Using Booleans in Conditionals

### Simple If Statement

```php
<?php
$isRaining = true;

if ($isRaining) {
    echo "Take an umbrella";
}
?>
```

### If-Else Statement

```php
<?php
$age = 18;
$isAdult = ($age >= 18);

if ($isAdult) {
    echo "You can vote";
} else {
    echo "You cannot vote yet";
}
?>
```

### Multiple Conditions

```php
<?php
$age = 25;
$hasLicense = true;

// AND operator - both must be true
if ($age >= 18 && $hasLicense) {
    echo "You can drive";
}

// OR operator - at least one must be true
if ($age < 18 || $age > 65) {
    echo "Special category";
}

// NOT operator - negation
if (!$hasLicense) {
    echo "You need a license";
}
?>
```

## Key Takeaways

✓ Boolean has only two values: `true` and `false`
✓ Comparisons always return boolean values
✓ Use booleans in `if`, `while`, `for` conditionals
✓ **Truthy** values: non-zero numbers, non-empty strings, non-empty arrays
✓ **Falsy** values: false, 0, 0.0, "", "0", null, empty arrays
✓ Logical operators: `&&` (AND), `||` (OR), `!` (NOT)
✓ Use `===` for strict comparison (type + value)
✓ Always test your conditional logic carefully
