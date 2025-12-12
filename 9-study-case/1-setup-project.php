<?php
/** Generate a new project from the previous php-mvc project
 *
 *
 * 1. Create a new folder for the login management project.
 *    Example: /path/to/php-mastery-notes/9-study-case/99-login-management
 * 
 * 2. Initialize a new Composer project:
 *    $ composer init
 *    - Set the project name, description, author, and license.
 *    - Add "php": "^8.0" as a requirement.
 *    - Add "phpunit/phpunit" as a dev requirement.
 * 3. Install dependencies:
 *    $ composer install
 * 4. Set up PSR-4 autoloading in composer.json:
 *  "autoload": {
 *    "psr-4": {
 *      "Mukhoiran\\LoginManagement\\": "app/"
 *   }
 * }
 * 5. Run composer dump-autoload to generate the autoload files:
 *   $ composer dump-autoload
 * 
 * 6. Copy the necessary folder structure from the previous project:
 *   - Create /app, /public, and /tests folders.
 * 
 * 7. Move the relevant files from the previous project to the new project:
 *  - Move controllers, middleware, and app classes to /app.
 * - Move test files to /tests.
 * - Move index.php to /public.
 * 
 * 8. Update namespaces in all PHP files to reflect the new project structure:
 * - Change namespaces from Mukhoiran\MVCProject to Mukhoiran\LoginManagement
 * 
 * 9. Update the autoload paths in composer.json if necessary.
 * 
 * 10. Test the project:
 * - Run a local PHP server to access the public/index.php:
 *  $ php -S localhost:8000 -t public
 * - Open http://localhost:8000 in your browser to ensure everything is working.
 * - Run PHPUnit tests to verify functionality:
 * $ ./vendor/bin/phpunit --testdox
 * 
 * 11. Make sure the /app and /tests folders are not directly accessible via the web. 
 *   Only the /public folder should be web-accessible.
 * 
 */
?>