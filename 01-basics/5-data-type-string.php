<?php

// A string is a sequence of characters, used to store and manipulate text.

// Strings can be created using single or double quotes:
$singleQuoted = 'Hello, world!';
$doubleQuoted = "Hello, world!";

// Double quotes allow variable interpolation and escape sequences:
$name = "Alice";
$greeting = "Hello, $name!\n"; // Outputs: Hello, Alice!

// Concatenation uses the dot (.) operator:
$first = "PHP";
$second = "String";
$combined = $first . " " . $second; // "PHP String"

// String functions:
$length = strlen($combined); // Get string length
$upper = strtoupper($combined); // Convert to uppercase
$lower = strtolower($combined); // Convert to lowercase
$substring = substr($combined, 4, 6); // Get substring

// Heredoc syntax: behaves like double quotes, supports variables and escape sequences
$heredoc = <<<EOD
This is a heredoc string.
Hello, $name!
New line is supported.
EOD;

// Nowdoc syntax: behaves like single quotes, no variable interpolation or escape sequences
$nowdoc = <<<'EOD'
This is a nowdoc string.
Hello, $name!
New line is supported.
EOD;

// Output examples
echo $greeting;
echo $combined . "\n";
echo "Length: $length\n";
echo "Upper: $upper\n";
echo "Lower: $lower\n";
echo "Substring: $substring\n";
echo "Heredoc:\n$heredoc\n";
echo "Nowdoc:\n$nowdoc\n";
?>