# String Manipulation

```php
// Existing greeting
$greeting = "Hello, world!";

// Accessing the first character
$firstChar = $greeting[0]; // "H"

// Getting words as an array
$words = explode(' ', $greeting); // ['Hello,', 'world!']

// Accessing the first word
$firstWord = $words[0]; // "Hello,"

// Example variable
$var = "cat";

// Combining variable inside string using curly braces
$combined = "This is {$var}s";

// String to integer conversion
$numString = "123";
$intVal = (int)$numString; // 123

// String to float conversion
$floatString = "45.67";
$floatVal = (float)$floatString; // 45.67

// Concatenating strings
$word = "world";
$combinedString = "Hello, " . $word . "!";

// Using variables inside double-quoted strings
$name = "Alice";
$greetMsg = "Hi, $name!";

// String length
$length = strlen($greeting); // 13

// Substring
$sub = substr($greeting, 0, 5); // "Hello"

// Changing case
$upper = strtoupper($greeting); // "HELLO, WORLD!"
$lower = strtolower($greeting); // "hello, world!"

// Finding position of a substring
$pos = strpos($greeting, "world"); // 7

// Replacing part of a string
$replaced = str_replace("world", "PHP", $greeting); // "Hello, PHP!"

// Trimming whitespace
$withSpaces = "  padded string  ";
$trimmed = trim($withSpaces); // "padded string"

// Left and right trim
$ltrimmed = ltrim($withSpaces); // "padded string  "
$rtrimmed = rtrim($withSpaces); // "  padded string"

// Splitting string into characters
$chars = str_split($greeting); // ['H','e','l','l','o',',',' ','w','o','r','l','d','!']

// Joining array into string
$joined = implode('-', $words); // "Hello,-world!"

// Checking if string contains a substring
$contains = strpos($greeting, "Hello") !== false; // true

// Comparing strings
$cmp = strcmp("abc", "ABC"); // >0 (case-sensitive)
$icmp = strcasecmp("abc", "ABC"); // 0 (case-insensitive)

// Repeating a string
$repeated = str_repeat("ha", 3); // "hahaha"

// Reversing a string
$reversed = strrev($greeting); // "!dlrow ,olleH"

// HTML special characters
$html = "<b>bold</b>";
$safeHtml = htmlspecialchars($html); // "&lt;b&gt;bold&lt;/b&gt;"

// Word count
$wordCount = str_word_count($greeting); // 2

// Pad a string
$padded = str_pad("42", 5, "0", STR_PAD_LEFT); // "00042"

// Output
echo "First character: $firstChar\n";
echo "First word: $firstWord\n";
echo "$combined\n";
echo "String to int: $intVal\n";
echo "String to float: $floatVal\n";
echo "Concatenated string: $combinedString\n";
echo "Greeting message: $greetMsg\n";
echo "String length: $length\n";
echo "Substring: $sub\n";
echo "Uppercase: $upper\n";
echo "Lowercase: $lower\n";
echo "Position of 'world': $pos\n";
echo "Replaced string: $replaced\n";
echo "Trimmed: '$trimmed'\n";
echo "Left trimmed: '$ltrimmed'\n";
echo "Right trimmed: '$rtrimmed'\n";
echo "Characters: " . implode(',', $chars) . "\n";
echo "Joined words: $joined\n";
echo "Contains 'Hello': " . ($contains ? "true" : "false") . "\n";
echo "String compare (abc vs ABC): $cmp\n";
echo "Case-insensitive compare (abc vs ABC): $icmp\n";
echo "Repeated: $repeated\n";
echo "Reversed: $reversed\n";
echo "HTML safe: $safeHtml\n";
echo "Word count: $wordCount\n";
echo "Padded: $padded\n";
```

