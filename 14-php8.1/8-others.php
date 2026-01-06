<?php

/**
 * PHP 8.1 - Other Feature Updates Summary
 * 
 * This file provides an overview of additional features and improvements
 * introduced in PHP 8.1 that are not covered in other sections.
 */

echo "<h1>PHP 8.1 - Other Features & Improvements</h1>";

// =============================================================================
// 1. Array Unpacking with String Keys
// =============================================================================
echo "<h2>1. Array Unpacking with String Keys</h2>";
echo "<p>PHP 8.1 allows unpacking arrays with string keys using the spread operator.</p>";

$array1 = ['a' => 1, 'b' => 2];
$array2 = ['c' => 3, 'd' => 4];
$result = [...$array1, ...$array2];
echo "<pre>";
print_r($result);
echo "</pre>";

// =============================================================================
// 2. New array_is_list() Function
// =============================================================================
echo "<h2>2. array_is_list() Function</h2>";
echo "<p>Determines whether a given array is a list with sequential integer keys starting from 0.</p>";

$list = [1, 2, 3];
$notList = [1 => 'a', 2 => 'b'];
echo "Is list: " . (array_is_list($list) ? 'Yes' : 'No') . "<br>";
echo "Is not list: " . (array_is_list($notList) ? 'Yes' : 'No') . "<br>";

// =============================================================================
// 3. Final Class Constants
// =============================================================================
echo "<h2>3. Final Class Constants</h2>";
echo "<p>Class constants can now be declared as final to prevent overriding in child classes.</p>";

class ParentClass {
    final public const CONSTANT = 'cannot be overridden';
}

// Uncommenting below will cause an error:
// class ChildClass extends ParentClass {
//     public const CONSTANT = 'trying to override';
// }

echo "Final constant: " . ParentClass::CONSTANT . "<br>";

// =============================================================================
// 4. Explicit Octal Numeral Notation
// =============================================================================
echo "<h2>4. Explicit Octal Numeral Notation</h2>";
echo "<p>New 0o/0O prefix for octal numbers, similar to 0x for hexadecimal and 0b for binary.</p>";

$octal_old = 0755;      // Old style
$octal_new = 0o755;     // New style (PHP 8.1+)
echo "Old style octal: $octal_old<br>";
echo "New style octal: $octal_new<br>";

// =============================================================================
// 5. fsync() and fdatasync() Functions
// =============================================================================
echo "<h2>5. fsync() and fdatasync() Functions</h2>";
echo "<p>New functions to synchronize file data with the storage device.</p>";
echo "<code>fsync()</code> - Syncs changes to file including metadata<br>";
echo "<code>fdatasync()</code> - Syncs only file data, not metadata (faster)<br>";

// =============================================================================
// 6. MurmurHash3 and xxHash Support
// =============================================================================
echo "<h2>6. New Hash Algorithms</h2>";
echo "<p>PHP 8.1 adds support for MurmurHash3 and xxHash algorithms.</p>";

$text = "Hello, PHP 8.1!";
echo "murmur3a: " . hash('murmur3a', $text) . "<br>";
echo "murmur3c: " . hash('murmur3c', $text) . "<br>";
echo "murmur3f: " . hash('murmur3f', $text) . "<br>";
echo "xxh32: " . hash('xxh32', $text) . "<br>";
echo "xxh64: " . hash('xxh64', $text) . "<br>";
echo "xxh3: " . hash('xxh3', $text) . "<br>";
echo "xxh128: " . hash('xxh128', $text) . "<br>";

// =============================================================================
// 7. MYSQLI_REFRESH_REPLICA Constant
// =============================================================================
echo "<h2>7. MySQLi Constant Update</h2>";
echo "<p>MYSQLI_REFRESH_SLAVE renamed to MYSQLI_REFRESH_REPLICA for inclusive terminology.</p>";

// =============================================================================
// 8. Performance Improvements
// =============================================================================
echo "<h2>8. Performance Improvements</h2>";
echo "<ul>";
echo "<li>JIT improvements for ARM64 (Apple Silicon, AWS Graviton)</li>";
echo "<li>Inheritance cache for faster class loading</li>";
echo "<li>Fast class name resolution</li>";
echo "<li>timelib and ext/date improvements</li>";
echo "</ul>";

// =============================================================================
// More Information & Resources
// =============================================================================
echo "<hr>";
echo "<footer>";
echo "<h3>Learn More</h3>";
echo "<p>For complete details about PHP 8.1 features and updates, visit:</p>";
echo "<ul>";
echo "<li><a href='https://www.php.net/releases/8.1/en.php' target='_blank'>Official PHP 8.1 Release Announcement</a></li>";
echo "<li><a href='https://www.php.net/ChangeLog-8.php#8.1.0' target='_blank'>PHP 8.1 Complete Changelog</a></li>";
echo "<li><a href='https://www.php.net/manual/en/migration81.php' target='_blank'>PHP 8.1 Migration Guide</a></li>";
echo "<li><a href='https://www.php.net/manual/en/migration81.new-features.php' target='_blank'>PHP 8.1 New Features Documentation</a></li>";
echo "<li><a href='https://wiki.php.net/rfc#php_81' target='_blank'>PHP 8.1 RFCs (Request for Comments)</a></li>";
echo "</ul>";
echo "</footer>";

?>