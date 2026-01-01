# String Data Type

## Overview

A string is a sequence of characters used to store and manipulate text. In PHP, strings can contain letters, numbers, symbols, and spaces.

## Creating Strings

### Single Quotes

**Single quotes** treat everything literally - no special interpretation:

```php
<?php
$name = 'Alice';
$greeting = 'Hello, world!';
$path = 'C:\\Users\\Documents';  // Note: double backslash

echo $greeting;                    // Hello, world!
echo $name;                        // Alice
?>
```

**Escape sequences in single quotes:**
```php
<?php
$escaped = 'It\'s a beautiful day';  // Escape apostrophe with backslash
echo $escaped;                       // It's a beautiful day
?>
```

**Note:** In single quotes, `\n` is literal (not newline), and variables are NOT interpolated.

### Double Quotes

**Double quotes** support variable interpolation and escape sequences:

```php
<?php
$name = "Alice";
$age = 25;

// Variable interpolation
echo "Hello, $name!";                 // Hello, Alice!
echo "Name: $name, Age: $age";        // Name: Alice, Age: 25

// Escape sequences
echo "Line 1\nLine 2";                // Output on two lines
echo "Path: C:\\Users\\Documents";   // Backslash before backslash
echo "Quote: \"Hello\"";              // Escaped double quote
echo "Tab:\tSeparated";              // Tab character
?>
```

**Common escape sequences:**

| Escape | Meaning |
|--------|---------|
| `\n` | Newline |
| `\t` | Tab |
| `\r` | Carriage return |
| `\\` | Backslash |
| `\"` | Double quote |
| `\$` | Dollar sign |

## String Concatenation

### Using the Dot (.) Operator

```php
<?php
$first = "Hello";
$second = "World";

// Concatenate strings
$message = $first . " " . $second;    // "Hello World"
echo $message;                         // Hello World

// Concatenate with other values
$name = "Alice";
$age = 25;
echo "Name: " . $name . ", Age: " . $age;  // Name: Alice, Age: 25
?>
```

### Using String Interpolation (Alternative)

```php
<?php
$first = "Hello";
$second = "World";

// This is often cleaner
echo "$first $second";                // Hello World
?>
```

## Essential String Functions

### Finding String Length

```php
<?php
$text = "Hello, World!";
echo strlen($text);                    // 13

// With variables
$name = "Alice";
echo strlen($name);                    // 5

// Multibyte characters
echo strlen("café");                   // 5 (bytes, not characters)
echo mb_strlen("café");                // 4 (characters)
?>
```

### Changing Case

```php
<?php
$text = "Hello, World!";

// Convert to uppercase
echo strtoupper($text);                // HELLO, WORLD!

// Convert to lowercase
echo strtolower($text);                // hello, world!

// Capitalize first character
echo ucfirst("hello");                 // Hello

// Capitalize first character of each word
echo ucwords("hello world php");       // Hello World Php
?>
```

### Extracting Substrings

```php
<?php
$text = "Hello, World!";

// Get substring from position
echo substr($text, 0, 5);              // Hello
echo substr($text, 7);                 // World!
echo substr($text, -6);                // World!

// Get substring with length
echo substr($text, 0, 5);              // Hello (5 characters from position 0)
echo substr($text, 7, 5);              // World (5 characters from position 7)
?>
```

### Finding Text Within Strings

```php
<?php
$text = "Hello, World!";

// Find first occurrence position
echo strpos($text, "World");           // 7 (position)
echo strpos($text, "o");               // 4 (first 'o')

// Find last occurrence
echo strrpos($text, "o");              // 8 (last 'o')

// Check if contains (PHP 8.0+)
if (str_contains($text, "World")) {
    echo "Found World!";
}

// Check if starts with (PHP 8.0+)
if (str_starts_with($text, "Hello")) {
    echo "Starts with Hello!";
}

// Check if ends with (PHP 8.0+)
if (str_ends_with($text, "!")) {
    echo "Ends with exclamation!";
}
?>
```

### Replacing Text

```php
<?php
$text = "Hello, World!";

// Replace text
echo str_replace("World", "PHP", $text);      // Hello, PHP!

// Replace multiple occurrences
$text = "cat dog cat bird cat";
echo str_replace("cat", "lion", $text);       // lion dog lion bird lion

// Case-insensitive replacement
echo str_ireplace("hello", "Hi", "Hello World");  // Hi World
?>
```

### Trimming Whitespace

```php
<?php
// Remove whitespace from both ends
$text = "  Hello, World!  ";
echo trim($text);                      // "Hello, World!"

// Remove from left only
echo ltrim("  Hello");                 // "Hello"

// Remove from right only
echo rtrim("Hello  ");                 // "Hello"

// Remove specific characters
echo trim("##Hello##", "#");           // "Hello"
?>
```

### Splitting and Joining

```php
<?php
// Split string into array
$csv = "apple,banana,orange";
$fruits = explode(",", $csv);
print_r($fruits);
// Array ( [0] => apple [1] => banana [2] => orange )

// Join array into string
$items = ["apple", "banana", "orange"];
$text = implode(", ", $items);
echo $text;                            // apple, banana, orange

// Limit splits
$parts = explode(",", $csv, 2);        // Maximum 2 parts
print_r($parts);
// Array ( [0] => apple [1] => banana,orange )
?>
```

## Heredoc and Nowdoc Syntax

### Heredoc (Like Double Quotes)

Heredoc allows multi-line strings with variable interpolation:

```php
<?php
$name = "Alice";
$age = 25;

$text = <<<EOD
Hello $name!
You are $age years old.
This supports variable interpolation.
And escape sequences like \n work fine.
EOD;

echo $text;
// Output:
// Hello Alice!
// You are 25 years old.
// This supports variable interpolation.
// And escape sequences like newlines work fine.
?>
```

### Nowdoc (Like Single Quotes)

Nowdoc is similar to heredoc but WITHOUT variable interpolation:

```php
<?php
$name = "Alice";

$text = <<<'EOD'
Hello $name!
This does NOT interpolate variables.
You see the literal text: $name
EOD;

echo $text;
// Output:
// Hello $name!
// This does NOT interpolate variables.
// You see the literal text: $name
?>
```

## Checking String Type

```php
<?php
$text = "Hello";
$number = 42;

// Check if string
if (is_string($text)) {
    echo "It's a string!";
}

// Check if numeric string
if (is_numeric("42")) {
    echo "It's a numeric string!";
}
?>
```

## Practical Examples

### Validate Email

```php
<?php
$email = "user@example.com";

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Valid email";
} else {
    echo "Invalid email";
}
?>
```

### Extract Domain from URL

```php
<?php
$url = "https://www.example.com/path?page=1";

$host = parse_url($url, PHP_URL_HOST);
echo $host;  // www.example.com

// Or get all parts
$parts = parse_url($url);
print_r($parts);
?>
```

### Create Slug from Title

```php
<?php
function createSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

$title = "Hello World! This is PHP";
echo createSlug($title);               // hello-world-this-is-php
?>
```

### Format Username

```php
<?php
function formatUsername($name) {
    // Trim whitespace
    $name = trim($name);
    
    // Check if empty
    if (strlen($name) === 0) {
        return "Anonymous";
    }
    
    // Convert to lowercase with first letter uppercase
    return ucfirst(strtolower($name));
}

echo formatUsername("  JOHN DOE  ");   // John doe
?>
```

## Key Takeaways

✓ **Single quotes** for literal strings (no interpolation)
✓ **Double quotes** for strings with variables and escape sequences
✓ Use **dot operator (.)** to concatenate strings
✓ **strlen()** gets string length
✓ **substr()** extracts portions of strings
✓ **str_replace()** finds and replaces text
✓ **explode()** and **implode()** convert between strings and arrays
✓ **Heredoc/Nowdoc** for multi-line strings
✓ Always **trim()** user input to remove extra whitespace
✓ Use **filter_var()** for validating strings

