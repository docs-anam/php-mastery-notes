<?php
// Summary: The self keyword in PHP OOP

/*
The self keyword in PHP is used to access static properties and methods from within the same class context. It refers to the current class, not an instance of the class.

Key points:
- Use self:: to access static properties or methods inside the class where they are declared.
- self does not work with inheritance the way $this or parent does; it always refers to the class in which it is used.
- Commonly used in static methods or for constants.

Example:
*/

class Example {
    const VERSION = '1.0';
    public static $count = 0;

    public static function increment() {
        self::$count++;
        echo "Count: " . self::$count . "\n";
        echo "Version: " . self::VERSION . "\n";
    }
}

Example::increment(); // Output: Count: 1, Version: 1.0

/*
In summary, use self when you need to reference static members or constants from within the same class.
*/
?>