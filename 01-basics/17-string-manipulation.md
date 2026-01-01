# String Manipulation in PHP

## Overview

String manipulation is a fundamental skill in PHP programming. This section covers practical techniques for working with strings, including concatenation, formatting, searching, and transformation.

## String Concatenation

### Using the Dot (.) Operator

```php
<?php
// Basic concatenation
$first = "Hello";
$second = "World";
$result = $first . " " . $second;
echo $result;  // Output: Hello World

// Concatenation with other types
$name = "John";
$age = 30;
echo "Name: " . $name . ", Age: " . $age;  // Name: John, Age: 30

// Multiple concatenations
$greeting = "Good" . " " . "morning" . ", " . "friend";
echo $greeting;  // Output: Good morning, friend
?>
```

### Using Concatenation Assignment (.=)

```php
<?php
$message = "Hello";
$message .= " World";
echo $message;  // Output: Hello World

// Building strings incrementally
$html = "<div>";
$html .= "<p>Content</p>";
$html .= "</div>";
echo $html;  // Output: <div><p>Content</p></div>

// Log building
$log = "2024-01-01 10:00:00";
$log .= " - User logged in";
$log .= " - IP: 192.168.1.1";
echo $log;
?>
```

## String Interpolation

### Double Quotes (Interpolation)

```php
<?php
$name = "Alice";
$age = 25;

// Variable interpolation
echo "Name: $name";  // Output: Name: Alice
echo "Age: {$age}";  // Output: Age: 25

// Expression interpolation
$x = 5;
$y = 10;
echo "Sum: {$x + $y}";  // Output: Sum: 15

// Array access
$person = ["name" => "Bob", "age" => 30];
echo "He is {$person['name']}";  // Output: He is Bob

// Method calls
$text = new stdClass();
$text->value = "Hello";
echo "{$text->value}";  // Output: Hello
?>
```

### Single Quotes (No Interpolation)

```php
<?php
$name = "Charlie";

// No interpolation in single quotes
echo 'Name: $name';  // Output: Name: $name

// Must concatenate with single quotes
echo 'Name: ' . $name;  // Output: Name: Charlie

// Escape sequences not processed
echo 'Line1\nLine2';   // Output: Line1\nLine2 (literal)

// Better for raw strings
$path = 'C:\Users\name\file.txt';
echo $path;  // No escape sequence issues
?>
```

## String Functions

### Length and Access

```php
<?php
$text = "Hello";

// String length
echo strlen($text);  // Output: 5

// Character access
echo $text[0];       // Output: H
echo $text[1];       // Output: e
echo $text[-1];      // Output: o (last character)

// First and last characters
echo substr($text, 0, 1);   // Output: H
echo substr($text, -1);     // Output: o
?>
```

### Case Conversion

```php
<?php
$text = "Hello World";

// Convert to uppercase
echo strtoupper($text);     // Output: HELLO WORLD

// Convert to lowercase
echo strtolower($text);     // Output: hello world

// Uppercase first character
echo ucfirst("hello");      // Output: Hello

// Uppercase each word
echo ucwords("hello world"); // Output: Hello World
?>
```

### Trimming and Padding

```php
<?php
$text = "  Hello World  ";

// Remove whitespace from both ends
echo trim($text);           // Output: Hello World

// Remove from left only
echo ltrim($text);          // Output: Hello World  

// Remove from right only
echo rtrim($text);          // Output:   Hello World

// Pad string to specific length
echo str_pad("5", 3, "0", STR_PAD_LEFT);  // Output: 005
echo str_pad("5", 3, "0", STR_PAD_RIGHT); // Output: 500
?>
```

### Searching and Replacing

```php
<?php
$text = "Hello World";

// Find position of substring
echo strpos($text, "World");      // Output: 6
echo strpos($text, "o");          // Output: 4 (first occurrence)

// Find last position
echo strrpos($text, "o");         // Output: 7

// Check if contains substring
if (strpos($text, "World") !== false) {
    echo "Contains World";
}

// Replace substring
echo str_replace("World", "PHP", $text);  // Output: Hello PHP

// Replace multiple
$text = "cat bat rat";
echo str_replace(["cat", "bat"], "animal", $text);
// Output: animal animal rat

// Case-insensitive replacement
echo str_ireplace("hello", "hi", "Hello World");  // Output: hi World
?>
```

### Substring Extraction

```php
<?php
$text = "Hello World";

// Extract substring from position
echo substr($text, 0, 5);   // Output: Hello
echo substr($text, 6);      // Output: World
echo substr($text, -5);     // Output: World

// Negative start position
$text = "Learning PHP";
echo substr($text, -3);     // Output: PHP

// With length
echo substr($text, 0, 8);   // Output: Learning
?>
```

### Splitting and Joining

```php
<?php
// Split string into array
$csv = "apple,banana,cherry";
$fruits = explode(",", $csv);
// Result: ["apple", "banana", "cherry"]

// Join array into string
$words = ["Hello", "World", "PHP"];
echo implode(" ", $words);  // Output: Hello World PHP

// Split by multiple delimiters
$text = "apple;banana,cherry";
$items = preg_split('/[;,]/', $text);
// Result: ["apple", "banana", "cherry"]

// Split into characters
$letters = str_split("Hello");
// Result: ["H", "e", "l", "l", "o"]
?>
```

## Practical String Examples

### Email Validation

```php
<?php
function validateEmail($email) {
    // Simple validation
    if (strpos($email, "@") === false) {
        return false;
    }
    if (strpos($email, ".") === false) {
        return false;
    }
    
    // Check format
    return preg_match("/^[^\s@]+@[^\s@]+\.[^\s@]+$/", $email) > 0;
}

echo validateEmail("john@example.com") ? "Valid" : "Invalid";
?>
```

### URL Slug Creation

```php
<?php
function createSlug($text) {
    // Convert to lowercase
    $text = strtolower($text);
    
    // Replace spaces with hyphens
    $text = str_replace(" ", "-", $text);
    
    // Remove special characters
    $text = preg_replace("/[^a-z0-9-]/", "", $text);
    
    // Remove multiple hyphens
    $text = preg_replace("/-+/", "-", $text);
    
    // Remove leading/trailing hyphens
    $text = trim($text, "-");
    
    return $text;
}

echo createSlug("Hello World PHP");  // Output: hello-world-php
?>
```

### CSV Parsing

```php
<?php
$csv = "John,30,Engineer\nAlice,25,Designer";
$lines = explode("\n", $csv);

foreach ($lines as $line) {
    $fields = explode(",", $line);
    $name = $fields[0];
    $age = $fields[1];
    $job = $fields[2];
    
    echo "$name is $age and works as $job\n";
}
?>
```

### Template String Replacement

```php
<?php
$template = "Hello {name}, you have {count} messages";

$data = [
    "{name}" => "John",
    "{count}" => 5
];

$result = strtr($template, $data);
echo $result;  // Output: Hello John, you have 5 messages

// Alternative using str_replace
$result = str_replace(
    ["{name}", "{count}"],
    ["John", 5],
    $template
);
echo $result;
?>
```

## Useful String Functions Summary

| Function | Purpose | Example |
|----------|---------|---------|
| strlen() | String length | strlen("Hello") = 5 |
| strtoupper() | Convert to uppercase | strtoupper("hello") = "HELLO" |
| strtolower() | Convert to lowercase | strtolower("HELLO") = "hello" |
| ucfirst() | Uppercase first char | ucfirst("hello") = "Hello" |
| ucwords() | Uppercase each word | ucwords("hello world") = "Hello World" |
| trim() | Remove whitespace | trim(" hello ") = "hello" |
| str_replace() | Find and replace | str_replace("a", "b", "cat") = "cbt" |
| substr() | Extract substring | substr("hello", 0, 3) = "hel" |
| strpos() | Find position | strpos("hello", "l") = 2 |
| explode() | Split into array | explode(",", "a,b,c") = ["a","b","c"] |
| implode() | Join array | implode("-", ["a","b"]) = "a-b" |
| str_repeat() | Repeat string | str_repeat("ab", 3) = "ababab" |
| str_reverse() | Reverse string | str_reverse("abc") = "cba" |

## Common Pitfalls

### Forgetting String Type

```php
<?php
$number = 123;
echo $number . "!";  // Works: concatenates
echo $number + "!";  // Wrong: tries to add

// Always ensure string operations
$result = (string)$number . "!";
?>
```

### Case Sensitivity in Searches

```php
<?php
$text = "Hello World";

// Case-sensitive (returns false)
echo strpos($text, "hello");    // false

// Case-insensitive
echo stripos($text, "hello");   // 0 (position)
?>
```

### Off-by-One Errors

```php
<?php
$text = "Hello";

// First character (position 0)
echo $text[0];  // H

// Not $text[1]
echo substr($text, 0, 1);  // H
?>
```

## Key Takeaways

✓ **Concatenation (.)** joins strings together
✓ **Interpolation** works only in double quotes
✓ **strlen()** gets string length
✓ **substr()** extracts portions of strings
✓ **strpos()** finds substring position
✓ **str_replace()** replaces content
✓ **explode()** splits strings into arrays
✓ **implode()** joins arrays into strings
✓ **Case functions** convert string case
✓ **trim()** removes whitespace from ends
