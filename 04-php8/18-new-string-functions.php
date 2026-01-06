<?php
// PHP 8.0 introduced several new string functions. Here are the main ones with examples:

echo "1. str_contains()\n";
// Checks if a string contains a given substring (case-sensitive)
$haystack = "Hello, PHP 8!";
$needle = "PHP";
if (str_contains($haystack, $needle)) {
    echo "'$haystack' contains '$needle'\n";
} else {
    echo "'$haystack' does not contain '$needle'\n";
}

echo "\n2. str_starts_with()\n";
// Checks if a string starts with a given substring (case-sensitive)
$start = "Hello";
if (str_starts_with($haystack, $start)) {
    echo "'$haystack' starts with '$start'\n";
} else {
    echo "'$haystack' does not start with '$start'\n";
}

echo "\n3. str_ends_with()\n";
// Checks if a string ends with a given substring (case-sensitive)
$end = "8!";
if (str_ends_with($haystack, $end)) {
    echo "'$haystack' ends with '$end'\n";
} else {
    echo "'$haystack' does not end with '$end'\n";
}

/*
Summary:
- str_contains(string $haystack, string $needle): bool
  Returns true if $needle is found in $haystack.

- str_starts_with(string $haystack, string $needle): bool
  Returns true if $haystack starts with $needle.

- str_ends_with(string $haystack, string $needle): bool
  Returns true if $haystack ends with $needle.

All these functions are case-sensitive and return a boolean value.
*/
?>