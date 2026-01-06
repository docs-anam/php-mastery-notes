<?php
/*
==========================
DETAILED NOTES: COMPOSER AUTOLOADING IN PHP
==========================

1. What is Autoloading?
-----------------------
Autoloading is a PHP feature that automatically loads classes, interfaces, traits, or functions when they are first used, without manual `require` or `include` statements. Composer, the standard PHP dependency manager, provides a robust autoloading system based on PSR-4 (recommended) or PSR-0 standards.

2. How Composer Autoload Works:
-------------------------------
- When you run `composer install` or `composer dump-autoload`, Composer generates a file: `vendor/autoload.php`.
- Including this file (`require 'vendor/autoload.php';`) registers an autoloader that maps namespaces to file paths.
- When you use a class or function from a mapped namespace, Composer automatically loads the corresponding file.

3. Creating Your Own Dependency (Library):
------------------------------------------
a. Create a PHP file for your library, e.g., `src/MyFunctions.php`:
    ```php
    <?php
    namespace MyLib;

    function sayHello($name) {
        return "Hello, $name!";
    }
    ```
b. Configure autoloading in `composer.json`:
    ```json
    {
      "autoload": {
        "psr-4": {
          "MyLib\\": "src/"
        }
      }
    }
    ```
c. Run `composer dump-autoload` in your project directory to update the autoloader.

4. Using Autoload to Access Your Function:
------------------------------------------
- In your main file (e.g., `4-autoload.php`), include Composer's autoloader and use your function:
    ```php
    require __DIR__ . '/vendor/autoload.php';

    // Import the function from your namespace
    use function MyLib\sayHello;

    echo sayHello('World'); // Output: Hello, World!
    ```

5. Other Details:
-----------------
- Composer autoload supports classes, interfaces, traits, and functions (PHP 5.6+).
- You can autoload multiple namespaces and directories by adding more entries in `composer.json`.
- Third-party packages installed via Composer are autoloaded automatically.
- For production, use `composer dump-autoload -o` for optimized (classmap) autoloading.
- You can also autoload files directly (not recommended for libraries):
    ```json
    {
      "autoload": {
        "files": ["src/helpers.php"]
      }
    }
    ```

==========================
EXECUTABLE SAMPLE
==========================

Assuming you have followed steps above and created `src/MyFunctions.php` and updated `composer.json`:

*/

// Include Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Import the function from your namespace
use Mukhoiran\Data\People;

// Use the class
$people = new People("Anam");
echo $people->sayHello("Khoirul") . PHP_EOL; // Output: Hello Khoirul, My Name is Anam

/*
==========================
SUMMARY
==========================
Composer autoloading streamlines dependency management and code organization. By defining your own dependencies and configuring Composer's autoload, you can easily use functions and classes across your project without manual file inclusion.
*/