<?php
/**
 * Summary: Creating a PHP Project with MVP Concept using Composer
 *
 * Detailed Steps:
 *
 * 1. Initialize Composer for your project:
 *     $ composer init
 *
 * 2. Set PHP 8 as a requirement in composer.json:
 *     "require": {
 *         "php": "^8.0"
 *     }
 *
 * 3. Add PHPUnit for development testing:
 *     $ composer require --dev phpunit/phpunit
 *
 * 4. Install all dependencies:
 *     $ composer install
 *
 * 5. Create main application and test folders:
 *     /app
 *     /tests
 *
 * 6. Configure PSR-4 autoloading in composer.json:
 *     "autoload": {
 *         "psr-4": {
 *             "App\\": "app/"
 *         }
 *     }
 *
 * 7. Regenerate Composer autoload files:
 *     $ composer dump-autoload
 *
 * 8. Create a public folder for web entry point:
 *     /public

 * 9. Add index.php inside the public folder:
 *     /public/index.php
 * 
 * 10. Test access:
 *     - Run a local PHP server to access index.php:
 *         $ php -S localhost:8000 -t public
 *     - Open http://localhost:8000 in your browser.
 *     - Ensure /app and /tests are not directly accessible via web.
 * 
 * Best Practices:
 * - Use Composer for dependency management and autoloading.
 * - Separate public web entry from application logic for security.
 * - Organize code using MVP (Model-View-Presenter) for maintainability.
 * - Write tests in /tests and run with PHPUnit.
 *
 * This structure provides a clean foundation for scalable PHP projects following modern standards.
 */