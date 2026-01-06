<?php
/**
 * Creating Your Own PHP Library with Composer
 *
 * This guide provides a step-by-step process for creating and managing your own PHP library using Composer.
 *
 * 1. Create a New Project Folder
 * - Start by creating a new directory for your PHP library project.
 *   Example (in terminal):
 *     mkdir own-library
 *     cd own-library
 * - This folder will contain all your library files and Composer configuration.
 * 
 * 2. Initialize Composer in Your Project
 *    - Run `composer init` in your project directory to start a new Composer project.
 *    - Follow the interactive prompts to set package name, description, author, license, and other metadata.
 *    - Composer will generate a `composer.json` file containing your project configuration like below:
 *      {
 *          "name": "mukhoiran/own-library",
 *          "description": "Library Composer Hello",
 *          "type": "library",
 *          "autoload": {
 *              "psr-4": {
 *                  "Mukhoiran\\OwnLibrary\\": "src/"
 *              }
 *          },
 *          "authors": [
 *              {
 *                  "name": "Muhammad Khoirul Anam",
 *                  "email": "mkhoir.anam@gmail.com"
 *              }
 *          ],
 *          "require": {}
 *      }
 * 
 * 3. Run Composer Update
 *    - Run `composer update`
 *    - This will install/update dependencies and regenerate the autoloader.
 *
 * 4. Ensure Library Code Structure
 *    - Make sure your library code is placed in the `src/` directory as specified in the autoload section.
 *    - Use PSR-4 namespaces for your classes, matching the autoload configuration.
 *
 * 5. Write Your Library Classes
 *    - Place your PHP class files inside the `src/` directory.
 *    - Ensure each class uses the namespace defined in your autoload configuration.
 *    - Follow best practices for class structure and documentation.
 *
 * 6. Create Your First Class File
 *    - Inside the `src/` directory, create a file named `People.php`.
 *    - Example content for `src/People.php`:
 *      <?php
 *          namespace Mukhoiran\OwnLibrary;
 *
 *          class People
 *          {
 *              private function __construct(private string $name) {
 *              }
 *
 *              public function sayHello(string $name): string {
 *                  return "Hello $name, my name is {$this->name}!";
 *              }
 *          }
 * 
 * 7. Install Dependencies (Optional)
 *    - If your library depends on other packages, use `composer require vendor/package` to add them.
 *    - Composer will update `composer.json` and install the dependencies in the `vendor/` directory.
 *
 * Notes:
 * - Always follow PSR standards for interoperability and maintainability.
 * - Refer to Composer documentation for advanced features and troubleshooting.
 * - For more information:
 *   - Composer Documentation: https://getcomposer.org/doc/
 *   - PSR-4 Autoloading Standard: https://www.php-fig.org/psr/psr-4/
 */
/*

References:
- Composer Documentation: https://getcomposer.org/doc/
- PSR-4 Autoloading: https://www.php-fig.org/psr/psr-4/
*/