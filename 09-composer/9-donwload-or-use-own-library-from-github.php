<?php

/*
Detailed Guide: Using a PHP Library from GitHub in Your Project

1. Locate the Library:
    - Visit https://github.com and search for the PHP library you need.
    - Check the README for features, usage, and requirements.
    - Review the license to ensure you can use it in your project.
    - Confirm compatibility with your PHP version.

2. Check Composer Support:
    - Most PHP libraries use Composer for installation.
    - Look for a composer.json file in the repository.
    - Check the README for Composer installation instructions.

3. Install the Library via Composer:
    a. If the library is on Packagist (the default Composer repository):
        - Open your terminal in your project directory.
        - Run: composer require vendor/package-name
        - Composer will download the library and update composer.json and composer.lock.
    b. If the library is NOT on Packagist:
        - Edit your project's composer.json file.
        - Add a "repositories" section with the GitHub URL:
          {
            "repositories": [
              {
                "type": "vcs",
                "url": "https://github.com/mukhoiran/sayhello-library.git"
              }
            ],
            "require": {
              "mukhoiran/own-library": "1.0.0" //name of library
            }
          }
        - Save composer.json.
        - Run: composer update
        - Run: composer composer dump-autoload
        - Composer will fetch the library directly from GitHub.

4. Autoload the Library:
    - Composer creates an autoloader at vendor/autoload.php.
    - At the top of your PHP script, include the autoloader:
      require __DIR__ . '/vendor/autoload.php';
    - This enables automatic loading of library classes.

5. Use the Library:
    - Refer to the library's documentation for usage examples.
    - Typically, you instantiate classes or call static methods:
      $object = new \LibraryNamespace\ClassName();
      $object->method();
    - Some libraries provide global functions or helpers.

6. Update the Library:
    - To get the latest version, run:
      composer update vendor/package-name
    - For GitHub libraries, run:
      composer update username/repository
    - Composer will fetch updates and resolve dependencies.

7. Troubleshooting:
    - If you get errors:
        - Check PHP version compatibility in the library's README.
        - Ensure all required dependencies are installed.
        - Look for issues or solutions on the library's GitHub Issues page.
        - Run composer diagnose for Composer-related problems.

Best Practices:
    - Always read the library's README and documentation before use.
    - Respect the library's license terms and conditions.
    - Pin specific versions in composer.json for production stability:
      "vendor/package-name": "1.2.3"
    - Test the library in a development environment before deploying.
    - Keep your dependencies updated, but review changelogs for breaking changes.

*/

require __DIR__ . '/vendor/autoload.php';

$people = new \Mukhoiran\OwnLibrary\People("Anam");
echo $people->sayHello() . PHP_EOL; // Output: Hello Khoirul, my name is Khoirul Anam!