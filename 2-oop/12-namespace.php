<?php
/**
 * Summary: Namespaces in PHP OOP
 *
 * 1. What is a Namespace?
 *    - A namespace is a way to encapsulate and organize code in PHP.
 *    - It helps avoid name collisions between classes, functions, and constants.
 *    - Useful in large applications or when using third-party libraries.
 *
 * 2. Declaring a Namespace:
 *    - Use the `namespace` keyword at the top of your PHP file.
 *    - Example:
 *        namespace MyApp\Models;
 *
 * 3. Defining Classes, Functions, and Constants:
 *    - All code after the namespace declaration belongs to that namespace.
 *    - Example:
 *        namespace MyApp\Models;
 *        class User {}
 *        function getUser() {}
 *        const VERSION = '1.0';
 *
 * 4. Using Namespaced Code:
 *    - Access namespaced classes/functions/constants using their fully qualified name:
 *        $user = new \MyApp\Models\User();
 *    - Or, use the `use` keyword to import:
 *        use MyApp\Models\User;
 *        $user = new User();
 *
 * 5. Sub-namespaces:
 *    - Namespaces can be nested using backslashes:
 *        namespace MyApp\Controllers\Admin;
 *
 * 6. Global Namespace:
 *    - Code without a namespace declaration is in the global namespace.
 *    - Use a leading backslash to refer to global classes/functions/constants.
 *
 * 7. Aliasing:
 *    - Use `as` to create an alias for a class or namespace:
 *        use MyApp\Models\User as UserModel;
 *        $user = new UserModel();
 *
 * 8. Best Practices:
 *    - Use namespaces in all modern PHP projects.
 *    - Follow PSR-4 autoloading standard for file and namespace organization.
 *    - Use meaningful and unique namespace names (often vendor/project/module).
 *
 * 9. Example:
 *    namespace Acme\Blog;
 *    class Post {}
 *    // In another file:
 *    use Acme\Blog\Post;
 *    $post = new Post();
 */

namespace App\Models {
    class User {
        public function __construct(public string $name) {}
        public function greet() {
            return "Hello, {$this->name}!";
        }
    }
}

namespace App\Controllers {
    use App\Models\User;

    class UserController {
        public function show() {
            $user = new User("Alice");
            return $user->greet();
        }
    }
}

// Usage in the global namespace
namespace {
    use App\Models\User;
    use App\Controllers\UserController;
    use App\Models\User as UserModel;

    $controller = new UserController();
    echo $controller->show(); // Output: Hello, Alice!

    // Using alias
    $user = new UserModel("Bob");
    echo $user->greet(); // Output: Hello, Bob!
}