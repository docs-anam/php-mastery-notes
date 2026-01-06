<?php
/**
 * Comprehensive Guide: Submitting and Using Your Own PHP Library on Packagist
 *
 * 1. Preparing Your Library
 *    - Create a public Git repository (e.g., GitHub, GitLab, Bitbucket).
 *    - Add a `composer.json` file with the following required fields:
 *        - `name`: Package name in the format `vendor/package`.
 *        - `description`: Brief summary of your library.
 *        - `type`: Usually "library".
 *        - `license`: SPDX license identifier (e.g., "MIT").
 *        - `authors`: Array of author details.
 *        - `require`: List of dependencies.
 *        - `autoload`: Recommended to use PSR-4 for class autoloading.
 *    - Write a clear `README.md` with installation, usage, and contribution instructions.
 *    - Tag a release (e.g., `git tag v1.0.0` and `git push --tags`).
 *
 * 2. Ensuring Composer Compatibility
 *    - Validate your `composer.json` using `composer validate`.
 *    - Ensure your code follows PSR standards for autoloading.
 *    - Test installation locally using `composer require vendor/package`.
 *
 * 3. Creating a Packagist Account
 *    - Register for a free account at https://packagist.org.
 *
 * 4. Submitting Your Package
 *    - Click "Submit" on Packagist.
 *    - Enter your repository URL (e.g., https://github.com/yourname/yourlibrary).
 *    - Packagist will automatically fetch your `composer.json` and metadata.
 *    - Confirm submission and review the package page for correctness.
 *
 * 5. Managing Your Package
 *    - Packagist will auto-update on new tags/releases pushed to your repository.
 *    - You can manually trigger updates from the Packagist package page if needed.
 *    - Update your library by pushing new tags and maintaining documentation.
 *    - Respond to issues and keep your package up-to-date.
 *
 * 6. Best Practices
 *    - Use semantic versioning (e.g., v1.0.0, v1.1.0, v2.0.0).
 *    - Maintain clear documentation and changelogs.
 *    - Write unit tests and provide usage examples.
 *    - Engage with the community and respond to feedback.
 *
 * 7. How to Use Your Uploaded Library
 *    - To install your library, users should run:
 *        composer require vendor/package
 *    - To use the library in their project:
 *        - Ensure Composer's autoloader is included: `require 'vendor/autoload.php';`
 *        - Instantiate and use classes as documented in your README.
 *    - Example usage:
 *        use Vendor\Package\ClassName;
 *        $object = new ClassName();
 *        $object->method();
 *
 * References:
 * - Packagist Submission Guide: https://packagist.org/about
 * - Composer Schema Documentation: https://getcomposer.org/doc/04-schema.md
 * - Composer Autoloading: https://getcomposer.org/doc/01-basic-usage.md#autoloading
 */

require __DIR__ . '/vendor/autoload.php';

$people = new \Mukhoiran\OwnLibrary\People("Anam");
echo $people->sayHello("Khoirul") . PHP_EOL;
echo $people->sayHello() . PHP_EOL; // Output: Hello Khoirul, my name is Khoirul Anam!