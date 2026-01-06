# String Manipulation

## Table of Contents
1. [Overview](#overview)
2. [String Functions](#string-functions)
3. [Searching and Replacing](#searching-and-replacing)
4. [Splitting and Joining](#splitting-and-joining)
5. [Formatting Strings](#formatting-strings)
6. [Advanced Manipulation](#advanced-manipulation)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)

---

## Overview

String manipulation involves working with strings to extract, modify, combine, and format text data.

---

## String Functions

### Case Conversion

```php
<?php
// Convert to uppercase
echo strtoupper("hello");      // HELLO
echo mb_strtoupper("café");    // CAFÉ (multibyte)

// Convert to lowercase
echo strtolower("HELLO");      // hello
echo mb_strtolower("CAFÉ");    // café

// Capitalize first character
echo ucfirst("hello");         // Hello

// Capitalize each word
echo ucwords("hello world");   // Hello World
echo ucwords("john doe");      // John Doe
?>
```

### String Length and Access

```php
<?php
// Get length
$text = "Hello";
echo strlen($text);            // 5
echo mb_strlen("café");        // 4 (multibyte)

// Get character at position
echo $text[0];                 // H
echo $text[1];                 // e
echo $text[-1];                // o (last character)

// Substring
echo substr($text, 0, 2);      // He
echo substr($text, 1);         // ello
echo substr($text, -2);        // lo
?>
```

### String Comparison

```php
<?php
// Exact match
strcmp("abc", "abc") === 0;    // true
strcmp("abc", "abd") !== 0;    // true

// Case-insensitive match
strcasecmp("ABC", "abc") === 0;  // true

// Starts with
strpos("hello", "he") === 0;   // true
str_starts_with("hello", "he"); // true (PHP 8.0+)

// Ends with
substr_compare("hello", "lo", -2) === 0;  // true
str_ends_with("hello", "lo");  // true (PHP 8.0+)

// Contains
strpos("hello", "ll") !== false;  // true
str_contains("hello", "ll");   // true (PHP 8.0+)
?>
```

---

## Searching and Replacing

### Finding Substrings

```php
<?php
// Find position
$pos = strpos("hello world", "o");  // 4 (first occurrence)
$pos = strrpos("hello world", "o"); // 7 (last occurrence)

// Case-insensitive
$pos = stripos("HELLO", "he");      // 0

// Check if exists
if (strpos("email@example.com", "@") !== false) {
    echo "Contains @";
}

// Multiple searches
$text = "PHP is great, PHP is powerful";
$count = substr_count($text, "PHP");  // 2
?>
```

### String Replacement

```php
<?php
// Simple replacement
echo str_replace("world", "PHP", "hello world");
// Output: hello PHP

// Case-insensitive
echo str_ireplace("WORLD", "PHP", "hello world");
// Output: hello PHP

// Multiple replacements
$replacements = [
    'hello' => 'hi',
    'world' => 'PHP',
];
echo strtr("hello world", $replacements);
// Output: hi PHP

// Limit replacements
echo str_replace("l", "L", "hello", $count = null, 2);
// First 2 'l' replaced: heLLo
?>
```

### Regex Replacement

```php
<?php
// Pattern replacement
echo preg_replace('/[0-9]+/', 'X', "abc123def456");
// Output: abcXdefX

// Multiple patterns
echo preg_replace(['/[0-9]/', '/[aeiou]/'], '', "hello123");
// Output: hll

// Replacement count
$count = 0;
preg_replace('/a/', 'A', "banana", -1, $count);
// $count = 3
?>
```

---

## Splitting and Joining

### Explode (Split)

```php
<?php
// Split by delimiter
$parts = explode(",", "apple,banana,cherry");
// Array: ['apple', 'banana', 'cherry']

// Limit parts
$parts = explode("-", "2024-01-06", 2);
// Array: ['2024', '01-06']

// Split string into characters
$chars = str_split("hello");
// Array: ['h', 'e', 'l', 'l', 'o']
?>
```

### Implode (Join)

```php
<?php
// Join array into string
$items = ['apple', 'banana', 'cherry'];
echo implode(", ", $items);
// Output: apple, banana, cherry

// With empty delimiter
echo implode("", ['h', 'e', 'l', 'l', 'o']);
// Output: hello

// Multiple delimiter
echo implode(" - ", ['start', 'middle', 'end']);
// Output: start - middle - end
?>
```

### Word Functions

```php
<?php
// Count words
echo str_word_count("hello world PHP");  // 3

// Get words as array
$words = str_word_count("hello world", 1);
// Array: ['hello', 'world']

// Split by whitespace
$parts = preg_split('/\s+/', "hello   world  php");
// Array: ['hello', 'world', 'php']
?>
```

---

## Formatting Strings

### Trimming

```php
<?php
// Remove whitespace from both sides
echo trim("  hello  ");         // "hello"

// From left only
echo ltrim("  hello");          // "hello  "

// From right only
echo rtrim("hello  ");          // "hello"

// Trim specific characters
echo trim("...hello...", ".");  // "hello"
?>
```

### Padding

```php
<?php
// Pad to width
echo str_pad("5", 3, "0", STR_PAD_LEFT);
// Output: 005

echo str_pad("hello", 10, ".");
// Output: hello.....

// Pad both sides
echo str_pad("*", 5, "-", STR_PAD_BOTH);
// Output: --*--
?>
```

### Repeating and Reversing

```php
<?php
// Repeat string
echo str_repeat("ha", 3);      // hahaha
echo str_repeat("-", 10);      // ----------

// Reverse string
echo strrev("hello");          // olleh
echo strrev("12345");          // 54321
?>
```

---

## Advanced Manipulation

### String Parsing

```php
<?php
// Parse URL
$url = "https://example.com:8080/path?id=123";
$parts = parse_url($url);
// [
//   'scheme' => 'https',
//   'host' => 'example.com',
//   'port' => 8080,
//   'path' => '/path',
//   'query' => 'id=123'
// ]

// Parse query string
$query = "name=john&age=30&city=NYC";
parse_str($query, $output);
// $output: ['name' => 'john', 'age' => '30', 'city' => 'NYC']
?>
```

### Regular Expressions

```php
<?php
// Match pattern
if (preg_match('/\d+/', "abc123def")) {
    echo "Contains digits";
}

// Find all matches
$matches = [];
preg_match_all('/[0-9]+/', "a1b2c3", $matches);
// $matches[0]: ['1', '2', '3']

// Split by pattern
$parts = preg_split('/[,;]/', "a,b;c,d");
// Array: ['a', 'b', 'c', 'd']
?>
```

### Number Formatting

```php
<?php
// Format number
echo number_format(1234.5678);         // 1,235
echo number_format(1234.5678, 2);      // 1,234.57
echo number_format(1234.5678, 2, '.', ',');
// Output: 1,234.57

// Pad numbers
echo sprintf("%03d", 5);               // 005
echo sprintf("%.2f", 3.14159);         // 3.14
?>
```

---

## Practical Examples

### Email Validation and Formatting

```php
<?php
function validateEmail($email) {
    // Basic validation
    if (!strpos($email, '@')) {
        return false;
    }
    
    $parts = explode('@', $email);
    if (count($parts) !== 2) {
        return false;
    }
    
    list($local, $domain) = $parts;
    
    return strlen($local) > 0 && strlen($domain) > 0;
}

function formatEmail($email) {
    // Trim whitespace
    $email = trim($email);
    
    // Convert to lowercase
    $email = strtolower($email);
    
    return $email;
}

$email = "  JOHN@EXAMPLE.COM  ";
$formatted = formatEmail($email);
echo $formatted;  // john@example.com
?>
```

### Text Processing

```php
<?php
function processText($text) {
    // Remove extra whitespace
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Trim
    $text = trim($text);
    
    // Capitalize sentences
    $text = ucfirst($text);
    
    // Replace common abbreviations
    $replacements = [
        ' u ' => ' you ',
        ' r ' => ' are ',
        ' ur ' => ' your ',
    ];
    $text = strtr($text, $replacements);
    
    return $text;
}

$input = "  hello   world    u r awesome  ";
echo processText($input);
// Output: "Hello world you are awesome"
?>
```

### URL and Path Handling

```php
<?php
function createUrl($domain, $path, $params = []) {
    // Remove trailing slashes
    $domain = rtrim($domain, '/');
    $path = '/' . ltrim($path, '/');
    
    // Build URL
    $url = $domain . $path;
    
    // Add query string
    if (!empty($params)) {
        $query = http_build_query($params);
        $url .= '?' . $query;
    }
    
    return $url;
}

$url = createUrl('https://example.com', 'users/profile', ['id' => 123]);
echo $url;
// Output: https://example.com/users/profile?id=123
?>
```

---

## Common Mistakes

### 1. Not Checking for False Return

```php
<?php
// ❌ Wrong: strpos returns 0 for position 0
if (strpos("hello", "h")) {
    echo "Found";  // Won't execute!
}

// ✓ Correct: check !== false
if (strpos("hello", "h") !== false) {
    echo "Found";
}

// ✓ Or use newer functions
if (str_starts_with("hello", "h")) {
    echo "Found";
}
?>
```

### 2. String vs Array Confusion

```php
<?php
// ❌ Explode returns array, not string
$result = explode(",", "a,b,c");
echo $result;  // Error: can't echo array

// ✓ Correct: implode to join back
$text = implode("-", $result);
echo $text;  // a-b-c
?>
```

### 3. Case Sensitivity Issues

```php
<?php
// ❌ Case matters by default
if (strpos("Hello", "hello") !== false) {
    echo "Found";  // Won't execute
}

// ✓ Use case-insensitive function
if (stripos("Hello", "hello") !== false) {
    echo "Found";  // Executes
}

// ✓ Or convert case
if (strpos(strtolower("Hello"), "hello") !== false) {
    echo "Found";
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class TextFormatter {
    public function formatUserInput(string $input): string {
        // Remove extra whitespace
        $input = preg_replace('/\s+/', ' ', $input);
        
        // Trim edges
        $input = trim($input);
        
        // Capitalize first letter
        $input = ucfirst($input);
        
        // Replace slang
        $slang = [
            'u' => 'you',
            'ur' => 'your',
            'thx' => 'thanks',
        ];
        foreach ($slang as $short => $long) {
            $input = preg_replace('/\b' . $short . '\b/i', $long, $input);
        }
        
        return $input;
    }
    
    public function extractUrls(string $text): array {
        $urls = [];
        preg_match_all(
            '/(https?:\/\/[^\s]+)/',
            $text,
            $matches
        );
        
        return $matches[1] ?? [];
    }
    
    public function generateSlug(string $text): string {
        // Lowercase
        $text = strtolower($text);
        
        // Replace spaces with hyphens
        $text = str_replace(' ', '-', $text);
        
        // Remove non-alphanumeric (except hyphens)
        $text = preg_replace('/[^a-z0-9-]/', '', $text);
        
        // Remove consecutive hyphens
        $text = preg_replace('/-+/', '-', $text);
        
        // Remove leading/trailing hyphens
        $text = trim($text, '-');
        
        return $text;
    }
}

// Usage
$formatter = new TextFormatter();

$text = "Hello World! Check out https://example.com for more info";
echo $formatter->formatUserInput($text) . "\n";

$urls = $formatter->extractUrls($text);
print_r($urls);

$slug = $formatter->generateSlug("The Quick Brown Fox");
echo $slug;  // the-quick-brown-fox
?>
```

---

## Next Steps

✅ Understand string manipulation  
→ Learn [strings data type](5-data-type-string.md)  
→ Study [regular expressions](33-regular-expression.md)  
→ Master [text processing](../02-basics-study-case/)
