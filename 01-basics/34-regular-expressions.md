# Regular Expressions in PHP

## Overview

Regular expressions (regex) are powerful patterns used for matching, searching, and manipulating text. PHP provides the `preg_*` functions (Perl-compatible regular expressions) for pattern matching and replacement. Regular expressions are essential for input validation, text processing, and data extraction.

## Basic Pattern Syntax

### Simple Patterns

```php
<?php
// Basic matching
$pattern = '/hello/';
$text = "hello world";

if (preg_match($pattern, $text)) {
    echo "Pattern found\n";
} else {
    echo "Pattern not found\n";
}

// Case-insensitive matching
$pattern = '/hello/i';  // 'i' flag for case-insensitive
$text = "HELLO world";

if (preg_match($pattern, $text)) {
    echo "Found (case-insensitive)\n";
}

// Multiple matches
$pattern = '/l/';
$text = "hello";

$count = preg_match_all($pattern, $text, $matches);
echo "Found $count matches\n";
// Output: Found 2 matches
?>
```

### Character Classes

```php
<?php
// Digits
$pattern = '/\d+/';  // One or more digits
preg_match($pattern, "abc123def", $matches);
echo $matches[0] . "\n";  // Output: 123

// Word characters (letters, digits, underscore)
$pattern = '/\w+/';
preg_match($pattern, "hello_world123", $matches);
echo $matches[0] . "\n";  // Output: hello_world123

// Whitespace
$pattern = '/\s+/';  // One or more whitespace
$text = "hello   world";
$result = preg_replace($pattern, " ", $text);
echo $result . "\n";  // Output: hello world

// Any character except newline
$pattern = '/h.llo/';  // . matches any character
preg_match($pattern, "hello", $matches);
echo $matches[0] . "\n";  // Output: hello
?>
```

## Practical Examples

### Email Validation

```php
<?php
function isValidEmail($email) {
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    return preg_match($pattern, $email) === 1;
}

echo isValidEmail("john@example.com") ? "Valid\n" : "Invalid\n";
echo isValidEmail("invalid.email@") ? "Valid\n" : "Invalid\n";
// Output:
// Valid
// Invalid
?>
```

### Phone Number Formatting

```php
<?php
function formatPhoneNumber($phone) {
    // Extract only digits
    $digits = preg_replace('/\D/', '', $phone);
    
    // Format as (XXX) XXX-XXXX
    if (strlen($digits) == 10) {
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $digits);
    }
    
    return $phone;
}

echo formatPhoneNumber("5551234567") . "\n";
echo formatPhoneNumber("555-123-4567") . "\n";
// Output:
// (555) 123-4567
// (555) 123-4567
?>
```

### URL Parsing

```php
<?php
function extractUrlParts($url) {
    $pattern = '/^(https?):\/\/(www\.)?([a-zA-Z0-9.-]+)\/(.*)$/';
    
    if (preg_match($pattern, $url, $matches)) {
        return [
            'protocol' => $matches[1],
            'domain' => $matches[3],
            'path' => $matches[4]
        ];
    }
    return null;
}

$url = "https://www.example.com/path/to/page";
print_r(extractUrlParts($url));
// Output:
// Array ( [protocol] => https [domain] => example.com [path] => path/to/page )
?>
```

### HTML Tag Removal

```php
<?php
function stripHtmlTags($html) {
    // Remove HTML tags
    $text = preg_replace('/<[^>]+>/', '', $html);
    
    // Replace multiple spaces with single
    $text = preg_replace('/\s+/', ' ', $text);
    
    return trim($text);
}

$html = "<p>This   is   <strong>HTML</strong>   text</p>";
echo stripHtmlTags($html) . "\n";
// Output: This is HTML text
?>
```

### Password Validation

```php
<?php
function isStrongPassword($password) {
    $checks = [
        '/[a-z]/' => "at least one lowercase letter",
        '/[A-Z]/' => "at least one uppercase letter",
        '/\d/' => "at least one digit",
        '/[!@#$%^&*]/' => "at least one special character",
        '/.{8,}/' => "at least 8 characters"
    ];
    
    $results = [];
    foreach ($checks as $pattern => $description) {
        $results[$description] = preg_match($pattern, $password) === 1;
    }
    
    return $results;
}

$password = "MyPass123!";
$checks = isStrongPassword($password);

foreach ($checks as $check => $passed) {
    echo $check . ": " . ($passed ? "✓" : "✗") . "\n";
}
?>
```

### Text Search and Replace

```php
<?php
// Case-insensitive replace
function replaceWords($text, $oldWord, $newWord) {
    $pattern = '/' . preg_quote($oldWord, '/') . '/i';
    return preg_replace($pattern, $newWord, $text);
}

$text = "The quick brown Fox jumps";
echo replaceWords($text, "fox", "dog") . "\n";
// Output: The quick brown dog jumps

// Replace only first occurrence
$text = "apple apple apple";
$new = preg_replace('/apple/', 'orange', $text, 1);
echo $new . "\n";
// Output: orange apple apple
?>
```

## Common Regular Expression Patterns

### Reference Table

```php
<?php
// Anchors
^       // Start of string
$       // End of string
\b      // Word boundary

// Quantifiers
*       // 0 or more
+       // 1 or more
?       // 0 or 1
{n}     // Exactly n
{n,}    // n or more
{n,m}   // Between n and m

// Character classes
[abc]       // a, b, or c
[^abc]      // Not a, b, or c
[a-z]       // a to z
[0-9]       // Digit
\d          // Digit [0-9]
\D          // Non-digit
\w          // Word character [a-zA-Z0-9_]
\W          // Non-word character
\s          // Whitespace
\S          // Non-whitespace

// Special characters
(abc)       // Capture group
(a|b)       // Alternation (a or b)
a\.b        // Literal dot
a\\b        // Literal backslash
?>
```

## Preg Functions

### preg_match - Find First Match

```php
<?php
$pattern = '/(\d{3})-(\d{4})/';
$text = "Call me at 555-1234";

if (preg_match($pattern, $text, $matches)) {
    print_r($matches);
}
// Output:
// Array (
//     [0] => 555-1234
//     [1] => 555
//     [2] => 1234
// )
?>
```

### preg_match_all - Find All Matches

```php
<?php
$pattern = '/\b\w{4}\b/';  // Words with 4 letters
$text = "This is a test with some good words";

preg_match_all($pattern, $text, $matches);
print_r($matches);
// Output:
// Array ( [0] => Array ( [0] => This [1] => test [2] => some [3] => good [4] => word ) )
?>
```

### preg_replace - Replace Matches

```php
<?php
// Simple replacement
$text = "Hello 2024, goodbye 2023";
$new = preg_replace('/\d{4}/', 'XXXX', $text);
echo $new . "\n";
// Output: Hello XXXX, goodbye XXXX

// Using captured groups
$text = "John Doe, Jane Smith";
$pattern = '/(\w+)\s(\w+)/';
$new = preg_replace($pattern, '$2, $1', $text);
echo $new . "\n";
// Output: Doe, John Smith, Jane
?>
```

### preg_split - Split by Pattern

```php
<?php
// Split by any whitespace
$text = "apple  banana,orange;grape";
$items = preg_split('/[\s,;]+/', $text);
print_r($items);
// Output:
// Array ( [0] => apple [1] => banana [2] => orange [3] => grape )
?>
```

## Common Pitfalls

### Not Escaping Special Characters

```php
<?php
// BUG - . has special meaning
$pattern = '/price.00/';  // Matches "price500" too!

// FIXED - escape the dot
$pattern = '/price\.00/';  // Matches "price.00" exactly
?>
```

### Forgetting Delimiters

```php
<?php
// ERROR - missing delimiters
preg_match('[a-z]+', $text);  // Error!

// CORRECT - delimiters required
preg_match('/[a-z]+/', $text);  // OK
preg_match('#[a-z]+#', $text);  // OK
?>
```

### Greedy vs Non-Greedy Matching

```php
<?php
// Greedy - matches as much as possible
$pattern = '/<.*>/';
$text = '<div>Hello</div>';
preg_match($pattern, $text, $matches);
echo $matches[0] . "\n";  // Output: <div>Hello</div>

// Non-greedy - matches as little as possible
$pattern = '/<.*?>/';
preg_match($pattern, $text, $matches);
echo $matches[0] . "\n";  // Output: <div>
?>
```

## Best Practices

✓ **Use raw strings** - prefix with r or use single quotes
✓ **Test patterns thoroughly** - edge cases matter
✓ **Escape special characters** - use preg_quote()
✓ **Use meaningful delimiters** - / or # commonly used
✓ **Document patterns** - regex is hard to read
✓ **Use named groups** - for complex patterns
✓ **Validate input** - don't trust user regex
✓ **Test performance** - complex patterns can be slow
✓ **Use built-in functions** - filter_var() for validation
✓ **Keep patterns simple** - split complex patterns

## Key Takeaways

✓ **Regular expressions** - powerful text pattern matching
✓ **preg_match()** - find first match
✓ **preg_match_all()** - find all matches
✓ **preg_replace()** - find and replace
✓ **preg_split()** - split string by pattern
✓ **Character classes** - \d digits, \w words, \s spaces
✓ **Anchors** - ^ start, $ end, \b word boundary
✓ **Quantifiers** - * + ? {n} for repetition
✓ **Capture groups** - () to extract parts
✓ **Flags** - i case-insensitive, m multiline
