# Data Types - Strings

## Table of Contents
1. [Overview](#overview)
2. [Creating Strings](#creating-strings)
3. [String Syntax](#string-syntax)
4. [Accessing Characters](#accessing-characters)
5. [String Operations](#string-operations)
6. [String Functions](#string-functions)
7. [Working with Strings](#working-with-strings)
8. [Common Mistakes](#common-mistakes)

---

## Overview

A string is a sequence of characters. Strings are the most common data type for text data.

```php
<?php
$greeting = "Hello, World!";  // String
$name = 'John Doe';           // Also a string
$message = <<<EOT
Heredoc syntax
for multiline strings
EOT;
?>
```

---

## Creating Strings

### Double Quotes

```php
<?php
// Variables are interpolated
$name = "Alice";
$message = "Hello, $name!";
echo $message;  // Hello, Alice!

// Expression interpolation
$items = 5;
echo "You have $items items.";

// Can include escaped characters
$path = "C:\\Users\\Documents\\file.txt";
echo $path;
?>
```

### Single Quotes

```php
<?php
// Variables are NOT interpolated
$name = "Alice";
$message = 'Hello, $name!';
echo $message;  // Hello, $name! (literal)

// No escape sequences (except \' and \\)
$path = 'C:\Users\Documents\file.txt';
echo $path;  // Works as expected

// Use for literal strings
$literal = 'This is exactly what you see';
?>
```

### Heredoc

```php
<?php
// Multiline with interpolation (like double quotes)
$name = "Alice";
$message = <<<EOT
Hello, $name!
Welcome to our system.
Your account is ready.
EOT;

echo $message;
?>
```

### Nowdoc

```php
<?php
// Multiline without interpolation (like single quotes)
$name = "Alice";
$message = <<<'EOT'
Hello, $name!
This is literal text.
No variable interpolation.
EOT;

echo $message;
?>
```

---

## String Syntax

### Escape Sequences

```php
<?php
// Common escapes in double-quoted strings
echo "Line 1\nLine 2";        // Newline
echo "Tab\there";              // Tab
echo "Quote: \"Hello\"";        // Double quote
echo "Backslash: \\";          // Backslash
echo "Dollar: \$5.00";         // Dollar sign

// In single quotes, only \' and \\ work
echo 'Can\'t do this';         // Apostrophe
echo 'Backslash: \\';          // Backslash
?>
```

### Complex Variable Interpolation

```php
<?php
// Simple interpolation
$name = "John";
echo "Hello $name";  // Hello John

// Curly braces for complex expressions
$data = ['name' => 'Alice'];
echo "Hello {$data['name']}";  // Hello Alice

// Array access
$arr = ['first', 'second'];
echo "Item: {$arr[0]}";        // Item: first

// Object properties
echo "Email: {$user->email}";  // Email: user@example.com
?>
```

---

## Accessing Characters

### String Indexing

```php
<?php
$text = "Hello";

// Access by index (0-based)
echo $text[0];   // H
echo $text[1];   // e
echo $text[4];   // o
echo $text[-1];  // o (last character)
echo $text[-2];  // l (second to last)

// Check character
if ($text[0] === 'H') {
    echo "Starts with H";
}
?>
```

### Loop Through Characters

```php
<?php
$word = "PHP";

// Loop using index
for ($i = 0; $i < strlen($word); $i++) {
    echo $word[$i] . " ";  // P H P
}

// Loop through each character
$reversed = "";
for ($i = strlen($word) - 1; $i >= 0; $i--) {
    $reversed .= $word[$i];
}
echo $reversed;  // PHP (reversed)
?>
```

---

## String Operations

### Concatenation

```php
<?php
// Using dot operator
$greeting = "Hello" . " " . "World";
echo $greeting;  // Hello World

// Building strings
$message = "Dear ";
$message .= "John";
$message .= "!";
echo $message;  // Dear John!

// In double quotes
$first = "John";
$last = "Doe";
$full = "$first $last";  // John Doe
?>
```

### Length

```php
<?php
$text = "Hello";
echo strlen($text);  // 5

// Count characters
if (strlen($password) < 8) {
    echo "Password too short";
}

// Empty string
$empty = "";
echo strlen($empty);  // 0
?>
```

---

## String Functions

### Case Conversion

```php
<?php
// Uppercase
echo strtoupper("hello");  // HELLO
echo ucfirst("hello");     // Hello
echo ucwords("hello world");  // Hello World

// Lowercase
echo strtolower("HELLO");  // hello
echo lcfirst("HELLO");     // hELLO
?>
```

### Finding and Replacing

```php
<?php
$text = "The quick brown fox jumps over the lazy dog";

// Find position
$pos = strpos($text, "fox");  // 16
$pos = strpos($text, "cat");  // false

// Check if contains
if (strpos($text, "fox") !== false) {
    echo "Text contains 'fox'";
}

// Replace
$new = str_replace("fox", "cat", $text);
// "The quick brown cat jumps over the lazy dog"
?>
```

### Trimming

```php
<?php
$text = "  Hello World  ";

echo trim($text);   // "Hello World" (both sides)
echo ltrim($text);  // "Hello World  " (left)
echo rtrim($text);  // "  Hello World" (right)

// Trim specific characters
$text = "...Hello...";
echo trim($text, ".");  // "Hello"
?>
```

### Splitting and Joining

```php
<?php
// Split string into array
$csv = "apple,banana,cherry";
$fruits = explode(",", $csv);
// ['apple', 'banana', 'cherry']

// Join array into string
$fruits = ['apple', 'banana', 'cherry'];
$csv = implode(",", $fruits);
// "apple,banana,cherry"

// Split with limit
$parts = explode(" ", "one two three four", 2);
// ['one', 'two three four']
?>
```

### Substring

```php
<?php
$text = "Hello World";

// Get substring
echo substr($text, 0, 5);    // Hello
echo substr($text, 6);       // World
echo substr($text, -5);      // World (last 5)
echo substr($text, 0, -6);   // Hello (all but last 6)

// Replace substring
$new = substr_replace($text, "PHP", 6);
// "Hello PHP"
?>
```

---

## Working with Strings

### Validation

```php
<?php
// Check if string is numeric
if (is_numeric("123")) {
    echo "Is numeric";  // true
}

if (is_numeric("123abc")) {
    echo "Is numeric";  // false
}

// Check if empty
$value = "";
if (empty($value)) {
    echo "Empty";
}

// String length check
if (strlen($email) > 254) {
    echo "Email too long";
}
?>
```

### Formatting

```php
<?php
// Number formatting
$price = 19.95;
echo number_format($price, 2);  // 19.95

// Padding
echo str_pad("5", 3, "0", STR_PAD_LEFT);  // 005

// Repeat
echo str_repeat("*", 10);  // **********

// Reverse
echo strrev("Hello");  // olleH
?>
```

### Comparison

```php
<?php
// Case-sensitive
"hello" == "HELLO";  // true (loose)
"hello" === "HELLO"; // false (strict)

// Case-insensitive
strcasecmp("hello", "HELLO") === 0;  // true
strtolower("HELLO") === strtolower("hello");  // true

// String comparison
"apple" < "banana";  // true (alphabetically)
?>
```

---

## Common Mistakes

### 1. String to Number Conversion

```php
<?php
// ❌ Surprise: PHP converts strings to numbers
$value = "10 apples";
$num = $value + 5;
echo $num;  // 15 (string converted to 10)

// ✅ Explicit conversion
$value = "10 apples";
$num = (int)$value + 5;  // 15

// ✅ Better validation
if (is_numeric($value)) {
    $num = (int)$value;
}
?>
```

### 2. Index Out of Bounds

```php
<?php
// ❌ No error, but unexpected behavior
$text = "Hi";
echo $text[5];  // Empty (no error)
echo $text[-5]; // Empty (no error)

// ✅ Check length first
$text = "Hi";
if (isset($text[5])) {
    echo $text[5];
} else {
    echo "Index out of bounds";
}
?>
```

### 3. Case Sensitivity in Functions

```php
<?php
// ❌ Case-sensitive by default
$text = "Hello World";
if (strpos($text, "world") !== false) {
    echo "Found";  // Won't execute (lowercase not found)
}

// ✅ Use case-insensitive version
if (stripos($text, "world") !== false) {
    echo "Found";  // Executes
}

// ✅ Or convert case first
if (strpos(strtolower($text), "world") !== false) {
    echo "Found";
}
?>
```

### 4. Null or Undefined in String Context

```php
<?php
// ❌ Generates warning in PHP 8
$value = null;
echo "Value: $value";  // Generates notice

// ✅ Check first
$value = null;
echo "Value: " . ($value ?? 'N/A');  // Value: N/A

// ✅ Use string function
echo "Value: " . (string)$value;  // Value: (empty)
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class TextProcessor {
    public function analyzeText(string $text): array {
        return [
            'length' => strlen($text),
            'characters' => str_split($text),
            'words' => str_word_count($text),
            'unique_chars' => count(array_unique(str_split($text))),
            'uppercase' => strlen($text) - strlen(strtolower($text)),
            'lowercase' => strlen(strtolower($text)) - 
                          strlen(strtolower(preg_replace('/[a-z]/', '', $text))),
        ];
    }
    
    public function formatText(string $text, int $width): string {
        $words = explode(' ', $text);
        $lines = [];
        $current_line = '';
        
        foreach ($words as $word) {
            if (strlen($current_line . $word) + 1 <= $width) {
                $current_line .= ($current_line ? ' ' : '') . $word;
            } else {
                if ($current_line) {
                    $lines[] = $current_line;
                }
                $current_line = $word;
            }
        }
        
        if ($current_line) {
            $lines[] = $current_line;
        }
        
        return implode("\n", $lines);
    }
    
    public function sanitize(string $text): string {
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        // Trim
        $text = trim($text);
        // Remove special characters
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
        return $text;
    }
}

// Usage
$processor = new TextProcessor();

$text = "The quick brown fox jumps over the lazy dog";
$analysis = $processor->analyzeText($text);
print_r($analysis);

$formatted = $processor->formatText($text, 20);
echo "\nFormatted:\n" . $formatted;
?>
```

---

## Next Steps

✅ Understand strings  
→ Learn [string manipulation](17-string-manipulation.md)  
→ Study [variables](6-variable.md)  
→ Master [arrays](9-data-type-array.md)
