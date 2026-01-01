# Regular Expression

```php
// 33-regular-expression.php

// Regular expressions (regex) are patterns used to match character combinations in strings.
// In PHP, regex functions are based on the PCRE (Perl Compatible Regular Expressions) library.

// Common PHP regex functions:
// - preg_match(): Perform a regular expression match
// - preg_match_all(): Perform a global regular expression match
// - preg_replace(): Perform a regular expression search and replace
// - preg_split(): Split string by a regular expression

// 1. Basic pattern matching with preg_match()
$pattern = "/php/i"; // 'i' flag for case-insensitive
$text = "I love PHP programming!";
if (preg_match($pattern, $text)) {
    echo "Match found!\n";
} else {
    echo "No match found.\n";
}

// 2. Extracting data with capturing groups
$email = "user@example.com";
$pattern = "/([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/";
if (preg_match($pattern, $email, $matches)) {
    echo "Username: " . $matches[1] . "\n";
    echo "Domain: " . $matches[2] . "\n";
}

// 3. Finding all matches with preg_match_all()
$text = "The price is $5, $10, and $15.";
$pattern = "/\$\d+/";
preg_match_all($pattern, $text, $matches);
print_r($matches[0]); // Array of all prices

// 4. Replacing text with preg_replace()
$text = "Visit our website at http://example.com or https://example.com";
$pattern = "/https?:\/\/[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/";
$replacement = "[LINK]";
echo preg_replace($pattern, $replacement, $text) . "\n";

// 5. Splitting a string with preg_split()
$text = "apple, orange;banana|grape";
$pattern = "/[,\;|]/";
$fruits = preg_split($pattern, $text);
print_r($fruits);

// 6. Common regex patterns
// - \d : digit
// - \w : word character (alphanumeric + _)
// - \s : whitespace
// - .  : any character except newline
// - ^  : start of string
// - $  : end of string
// - +  : one or more
// - *  : zero or more
// - ?  : zero or one
// - [] : character class
// - () : capturing group

// Example: Validate a phone number (e.g., 123-456-7890)
$phone = "123-456-7890";
$pattern = "/^\d{3}-\d{3}-\d{4}$/";
if (preg_match($pattern, $phone)) {
    echo "Valid phone number.\n";
} else {
    echo "Invalid phone number.\n";
}

// For more details, see the PHP manual: https://www.php.net/manual/en/book.pcre.php
```

