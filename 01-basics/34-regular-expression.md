# Regular Expressions (Regex)

## Table of Contents
1. [Overview](#overview)
2. [Basic Pattern Syntax](#basic-pattern-syntax)
3. [Character Classes](#character-classes)
4. [Quantifiers](#quantifiers)
5. [Anchors](#anchors)
6. [PHP Regex Functions](#php-regex-functions)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)

---

## Overview

Regular expressions (regex) are patterns for matching text.

**PHP regex functions:**
- `preg_match()` - Find pattern match
- `preg_match_all()` - Find all matches
- `preg_replace()` - Replace pattern matches
- `preg_split()` - Split by pattern

**Delimiters:** `/pattern/`, `#pattern#`, `~pattern~`

---

## Basic Pattern Syntax

### Simple Match

```php
<?php
// Literal character match
$pattern = '/hello/';
$text = 'hello world';

if (preg_match($pattern, $text)) {
    echo "Match found";
}

// Case-insensitive (i modifier)
$pattern = '/HELLO/i';
if (preg_match($pattern, 'hello')) {
    echo "Match found";
}

// Escaped special characters
$pattern = '/\./';  // Match literal dot
$text = 'end.';
if (preg_match($pattern, $text)) {
    echo "Found dot";
}
?>
```

### Wildcards

```php
<?php
// . matches any character except newline
$pattern = '/h.llo/';
if (preg_match($pattern, 'hello')) {
    echo "Match";  // e matches .
}
if (preg_match($pattern, 'hallo')) {
    echo "Match";  // a matches .
}

// Match any single character
$pattern = '/gr.y/';
preg_match($pattern, 'grey');   // Match
preg_match($pattern, 'gray');   // Match
preg_match($pattern, 'grby');   // Match
?>
```

---

## Character Classes

### Defined Classes

```php
<?php
// [abc] - Match a, b, or c
$pattern = '/[abc]/';
preg_match($pattern, 'b');  // Match

// [^abc] - NOT a, b, or c
$pattern = '/[^abc]/';
preg_match($pattern, 'd');  // Match
preg_match($pattern, 'a');  // No match

// [a-z] - Range
$pattern = '/[a-z]/';
preg_match($pattern, 'x');  // Match

// [0-9] - Digits
$pattern = '/[0-9]/';
preg_match($pattern, '5');  // Match

// [a-zA-Z0-9] - Letters and digits
$pattern = '/[a-zA-Z0-9]/';
preg_match($pattern, 'Q');  // Match
?>
```

### Shorthand Classes

```php
<?php
// \d - Digit [0-9]
$pattern = '/\d/';
preg_match($pattern, '5');  // Match

// \D - Non-digit [^0-9]
$pattern = '/\D/';
preg_match($pattern, 'a');  // Match

// \w - Word character [a-zA-Z0-9_]
$pattern = '/\w/';
preg_match($pattern, 'x');  // Match

// \W - Non-word character
$pattern = '/\W/';
preg_match($pattern, ' ');  // Match

// \s - Whitespace
$pattern = '/\s/';
preg_match($pattern, ' ');  // Match
preg_match($pattern, '\t'); // Match

// \S - Non-whitespace
$pattern = '/\S/';
preg_match($pattern, 'a');  // Match
?>
```

---

## Quantifiers

### Basic Quantifiers

```php
<?php
// * - Zero or more
$pattern = '/hel*o/';
preg_match($pattern, 'heo');    // Match (zero l)
preg_match($pattern, 'hello');  // Match (two l)

// + - One or more
$pattern = '/hel+o/';
preg_match($pattern, 'heo');    // No match
preg_match($pattern, 'hello');  // Match

// ? - Zero or one
$pattern = '/hel?o/';
preg_match($pattern, 'heo');    // Match
preg_match($pattern, 'hello');  // Match

// {n} - Exactly n times
$pattern = '/\d{3}/';
preg_match($pattern, '123');  // Match
preg_match($pattern, '12');   // No match

// {n,m} - Between n and m times
$pattern = '/\d{2,4}/';
preg_match($pattern, '12');    // Match (2)
preg_match($pattern, '1234');  // Match (4)
?>
```

### Greedy vs Non-greedy

```php
<?php
// Greedy (default) - match as much as possible
$pattern = '/<.*>/';
$text = '<tag>content</tag>';
preg_match($pattern, $text, $matches);
echo $matches[0];  // <tag>content</tag> (entire match)

// Non-greedy - match as little as possible
$pattern = '/<.*?>/';
$text = '<tag>content</tag>';
preg_match($pattern, $text, $matches);
echo $matches[0];  // <tag>
?>
```

---

## Anchors

### Position Anchors

```php
<?php
// ^ - Start of string
$pattern = '/^hello/';
preg_match($pattern, 'hello world');  // Match
preg_match($pattern, 'say hello');    // No match

// $ - End of string
$pattern = '/world$/';
preg_match($pattern, 'hello world');  // Match
preg_match($pattern, 'world hello');  // No match

// ^ and $
$pattern = '/^hello$/';
preg_match($pattern, 'hello');     // Match
preg_match($pattern, ' hello');    // No match (space)
?>
```

### Word Boundaries

```php
<?php
// \b - Word boundary
$pattern = '/\bhello\b/';
preg_match($pattern, 'hello world');    // Match
preg_match($pattern, 'say hello');      // Match
preg_match($pattern, 'helloworld');     // No match

// \B - Non-word boundary
$pattern = '/\Bhello/';
preg_match($pattern, 'helloworld');     // Match
preg_match($pattern, 'hello world');    // No match
?>
```

---

## PHP Regex Functions

### preg_match()

```php
<?php
// Basic match
if (preg_match('/\d+/', 'abc123def')) {
    echo "Number found";
}

// With matches array
$pattern = '/(\d+)-(\d+)-(\d+)/';
$text = 'Date: 2024-01-15';

if (preg_match($pattern, $text, $matches)) {
    echo $matches[0];  // 2024-01-15 (full match)
    echo $matches[1];  // 2024 (group 1)
    echo $matches[2];  // 01 (group 2)
    echo $matches[3];  // 15 (group 3)
}

// With offset
$text = '123abc456';
preg_match('/\d+/', $text, $matches, PREG_OFFSET_CAPTURE, 3);
print_r($matches);  // Starts search from position 3
?>
```

### preg_match_all()

```php
<?php
// Find all matches
$pattern = '/\d+/';
$text = 'a1b22c333';

preg_match_all($pattern, $text, $matches);
print_r($matches[0]);  // [1, 22, 333]

// With capturing groups
$pattern = '/(\w+)=(\d+)/';
$text = 'x=10 y=20 z=30';

preg_match_all($pattern, $text, $matches);
print_r($matches[0]);  // Full matches
print_r($matches[1]);  // First groups
print_r($matches[2]);  // Second groups

// Return matches count
$count = preg_match_all($pattern, $text);
echo "Found $count matches";
?>
```

### preg_replace()

```php
<?php
// Basic replacement
$pattern = '/\d+/';
$replacement = 'NUMBER';
$text = 'a1b22c333';

echo preg_replace($pattern, $replacement, $text);
// a NUMBERb NUMBERc NUMBER

// Replace with captured groups
$pattern = '/(\d+)-(\d+)-(\d+)/';
$replacement = '$3/$2/$1';  // Reverse date
$text = '2024-01-15';

echo preg_replace($pattern, $replacement, $text);
// 15/01/2024

// Replace all or limit
$text = 'aaa bbb ccc';
echo preg_replace('/a/', 'X', $text);           // Replace all
echo preg_replace('/a/', 'X', $text, 2);        // Replace first 2
?>
```

### preg_split()

```php
<?php
// Split by pattern
$pattern = '/,\s*/';  // Comma with optional space
$text = 'apple, banana , cherry';

$parts = preg_split($pattern, $text);
print_r($parts);  // [apple, banana, cherry]

// Split with limit
$parts = preg_split($pattern, $text, 2);
print_r($parts);  // [apple, banana , cherry]

// Capture delimiters
$pattern = '/[,;]/';
$parts = preg_split($pattern, 'a,b;c', -1, PREG_SPLIT_DELIM_CAPTURE);
print_r($parts);  // [a, ',', b, ';', c]
?>
```

---

## Practical Examples

### Email Validation

```php
<?php
function validateEmail($email) {
    // Basic email pattern
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    
    return preg_match($pattern, $email) ? true : false;
}

echo validateEmail('john@example.com');     // true
echo validateEmail('invalid.email');        // false
echo validateEmail('user+tag@example.co.uk'); // true

// Better: Use PHP filter
var_dump(filter_var('john@example.com', FILTER_VALIDATE_EMAIL));
?>
```

### Phone Number Extraction

```php
<?php
function extractPhones($text) {
    // US phone number
    $pattern = '/\b\d{3}[-.]?\d{3}[-.]?\d{4}\b/';
    
    preg_match_all($pattern, $text, $matches);
    
    return $matches[0];
}

$text = 'Call 123-456-7890 or 987.654.3210 today';
$phones = extractPhones($text);
print_r($phones);  // [123-456-7890, 987.654.3210]
?>
```

### URL Parsing

```php
<?php
function parseUrl($url) {
    $pattern = '/^(https?):\/\/([^\/]+)(\/.*)$/';
    
    if (preg_match($pattern, $url, $matches)) {
        return [
            'protocol' => $matches[1],
            'domain' => $matches[2],
            'path' => $matches[3],
        ];
    }
    
    return null;
}

$url = 'https://example.com/path/to/page';
$parsed = parseUrl($url);
print_r($parsed);
// [protocol => https, domain => example.com, path => /path/to/page]
?>
```

### HTML Tag Removal

```php
<?php
function removeHtmlTags($text) {
    return preg_replace('/<[^>]+>/', '', $text);
}

$html = '<p>Hello <strong>world</strong></p>';
echo removeHtmlTags($html);  // Hello world

// Better: Use strip_tags()
echo strip_tags($html);  // Hello world
?>
```

### String Formatting

```php
<?php
function formatPhoneNumber($phone) {
    // Extract digits only
    $digits = preg_replace('/\D/', '', $phone);
    
    // Format as (123) 456-7890
    if (strlen($digits) === 10) {
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $digits);
    }
    
    return $phone;
}

echo formatPhoneNumber('1234567890');    // (123) 456-7890
echo formatPhoneNumber('123-456-7890');  // (123) 456-7890
?>
```

### Slug Generation

```php
<?php
function generateSlug($text) {
    // Convert to lowercase
    $text = strtolower($text);
    
    // Remove non-alphanumeric (except hyphen)
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    
    // Remove multiple hyphens
    $text = preg_replace('/-+/', '-', $text);
    
    // Trim hyphens
    $text = trim($text, '-');
    
    return $text;
}

echo generateSlug('Hello World! How Are You?');
// hello-world-how-are-you
?>
```

---

## Common Mistakes

### 1. Forgetting Delimiters

```php
<?php
// ❌ Wrong: No delimiters
$pattern = '\d+';  // Treated as literal string

// ✓ Correct: Use delimiters
$pattern = '/\d+/';
?>
```

### 2. Not Escaping Special Characters

```php
<?php
// ❌ Wrong: . matches any character
$pattern = '/file.txt/';
preg_match($pattern, 'filextxt');  // Matches!

// ✓ Correct: Escape special character
$pattern = '/file\.txt/';
preg_match($pattern, 'file.txt');  // Match
preg_match($pattern, 'filextxt');  // No match
?>
```

### 3. Greedy Matching

```php
<?php
// ❌ Problem: Too greedy
$pattern = '/<.*>/';
$html = '<tag>content</tag>';
preg_match($pattern, $html, $matches);
echo $matches[0];  // <tag>content</tag> (includes closing tag)

// ✓ Solution: Use non-greedy
$pattern = '/<.*?>/';
preg_match($pattern, $html, $matches);
echo $matches[0];  // <tag>
?>
```

### 4. Wrong Anchors

```php
<?php
// ❌ Wrong: No anchors (matches anywhere)
$pattern = '/hello/';
preg_match($pattern, 'say hello there');  // Matches

// ✓ Correct: Use anchors for exact match
$pattern = '/^hello$/';
preg_match($pattern, 'hello');           // Match
preg_match($pattern, 'say hello');       // No match
?>
```

### 5. Case Sensitivity

```php
<?php
// ❌ Wrong: Case mismatch
$pattern = '/hello/';
preg_match($pattern, 'HELLO');  // No match

// ✓ Correct: Use i modifier
$pattern = '/hello/i';
preg_match($pattern, 'HELLO');  // Match
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class TextAnalyzer {
    public function extractEmails(string $text): array {
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        
        preg_match_all($pattern, $text, $matches);
        return $matches[0];
    }
    
    public function extractUrls(string $text): array {
        $pattern = '~https?://[^\s]+~';
        
        preg_match_all($pattern, $text, $matches);
        return $matches[0];
    }
    
    public function extractHashtags(string $text): array {
        $pattern = '/#\w+/';
        
        preg_match_all($pattern, $text, $matches);
        return $matches[0];
    }
    
    public function replaceUrls(string $text): string {
        $pattern = '~https?://[^\s]+~';
        return preg_replace($pattern, '[LINK]', $text);
    }
    
    public function capitalizeWords(string $text): string {
        // Capitalize first letter of each word
        return preg_replace_callback('/\b\w/', function($matches) {
            return strtoupper($matches[0]);
        }, $text);
    }
    
    public function highlightNumbers(string $text): string {
        return preg_replace('/\d+/', '<<$0>>', $text);
    }
}

// Usage
$analyzer = new TextAnalyzer();
$text = "Contact john@example.com or jane@test.org. Visit https://example.com #hashtag #web";

echo "Emails: ";
print_r($analyzer->extractEmails($text));

echo "URLs: ";
print_r($analyzer->extractUrls($text));

echo "Hashtags: ";
print_r($analyzer->extractHashtags($text));

echo "Replaced: " . $analyzer->replaceUrls($text) . "\n";

echo "Capitalized: " . $analyzer->capitalizeWords("hello world test") . "\n";

echo "Highlighted: " . $analyzer->highlightNumbers("Items: 10, Price: 25.99") . "\n";
?>
```

---

## Summary

| Feature | Syntax | Example |
|---------|--------|---------|
| Literal | `/text/` | `/hello/` |
| Any char | `/.` | `/h.llo/` |
| Character class | `/[abc]/` | `/[a-z]/` |
| Negation | `/[^abc]/` | `/[^0-9]/` |
| Digit | `/\d/` | `/\d{2,4}/` |
| Quantifier | `*, +, ?` | `/a+/, /b?/` |
| Anchor | `^, $` | `/^start$/, /^end/` |

---

## Next Steps

✅ Understand regular expressions  
→ Study [functions](28-functions.md)  
→ Learn [string manipulation](17-string-manipulation.md)  
→ Explore [data processing](../2-data-structure/)
