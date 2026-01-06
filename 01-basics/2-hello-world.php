<!-- PHP Basics and Coding Rules

 1. PHP tags:
    PHP code starts with <?php //and ends with ?>
    Example:
    echo "Hello, World!";

 2. Statements end with a semicolon (;)
    $a = 5;

 3. Comments:
    Single-line: // or #
    Multi-line: /* ... */

 4. Variables:
    Start with $ and are case-sensitive
    $name = "Alice";

 5. Strings:
    Can use single ('') or double ("") quotes
    echo 'Single quotes';
    echo "Double quotes";

 6. Functions:
    Defined with function keyword
    function greet($person) {
        return "Hello, $person!";
    }

 7. Output:
    Use echo or print to display output
    echo greet($name);

 8. Indentation and readability:
    Use consistent indentation and spacing

 9. File extension:
    PHP files use .php extension

 10. No closing PHP tag in pure PHP files:
     Omit the closing ?> tag to avoid accidental output

 11. Running PHP in the terminal:
    Use the command: php filename.php
    Example:
    php 2-hello-world.php
-->

<?php
    echo "Hello, World!";
    // This is a simple PHP script that outputs "Hello, World!" to the screen.
?>