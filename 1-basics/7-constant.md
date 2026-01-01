# Constant

```php
// Constants in PHP

// A constant is an identifier (name) for a simple value.
// The value cannot be changed during the script execution.

// Define a constant using define()
define("SITE_NAME", "PHP Mastery Notes");

// Since PHP 7, you can also use the 'const' keyword (only at the top-level scope)
const VERSION = "1.0.0";

// Constants are global and can be accessed anywhere in the script
echo SITE_NAME; // Output: PHP Mastery Notes
echo VERSION;   // Output: 1.0.0

// Constants are case-sensitive by default
define("GREETING", "Hello");
echo GREETING; // Output: Hello

// You cannot unset or reassign a constant
// GREETING = "Hi"; // Error

// Constants do not start with a $ sign
```

