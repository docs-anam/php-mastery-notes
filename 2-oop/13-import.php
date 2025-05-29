<?php
/*
Summary: Import in PHP OOP

In PHP Object-Oriented Programming (OOP), "import" typically refers to including and using classes, functions, or constants from other files or namespaces. PHP uses the following mechanisms for importing:

1. Including Files:
    - Use `require`, `require_once`, `include`, or `include_once` to load class definitions from other files.
    - Example:
      require_once 'User.php';

2. Namespaces and the `use` Statement:
    - Namespaces prevent name conflicts between classes, functions, and constants.
    - The `use` statement imports classes, functions, or constants from a namespace, allowing you to reference them without their full path.
    - Example:
      namespace App\Controllers;
      $user = new User();

3. Aliasing:
    - The `use` statement can alias imported classes, functions, or constants to avoid conflicts or for convenience.
    - Example:
      $user = new AppUser();

4. Autoloading:
    - Modern PHP uses autoloaders (like Composer's autoloader) to automatically load classes when needed, reducing manual includes.
    - Example:
      // composer.json autoload section
      "autoload": {
            "psr-4": {
                 "App\\": "src/"
            }
      }

Best Practices:
- Use namespaces and autoloading for scalable applications.
- Avoid manual includes when using autoloaders.
- Use `use` statements at the top of PHP files for clarity.

Summary Table:

| Mechanism      | Purpose                        | Example Syntax                  |
|----------------|-------------------------------|---------------------------------|
| require_once   | Include class file             | require_once 'User.php';        |
| use            | Import class from namespace    | use App\Models\User;            |
| use ... as ... | Alias imported class           | use App\Models\User as AppUser; |
| use function   | Import function from namespace | use function App\foo;           |
| use const      | Import constant from namespace | use const App\BAR;              |

*/

// Example namespace with class, function, and constant
namespace App\Utils {
    class Helper {
        public static function greet($name) {
            return "Hello, $name!";
        }
    }

    function sayGoodbye($name) {
        return "Goodbye, $name!";
    }

    const APP_VERSION = '1.0.0';
}

// Import class, function, and constant from App\Utils
namespace {
    use App\Utils\Helper;
    use function App\Utils\sayGoodbye;
    use const App\Utils\APP_VERSION;

    echo Helper::greet('Alice') . PHP_EOL;      // Outputs: Hello, Alice!
    echo sayGoodbye('Bob') . PHP_EOL;           // Outputs: Goodbye, Bob!
    echo 'Version: ' . APP_VERSION . PHP_EOL;   // Outputs: Version: 1.0.0
}