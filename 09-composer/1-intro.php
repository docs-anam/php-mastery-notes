<?php
/**
 * Dependency Management, Composer, and How Composer Works
 *
 * Dependency Management:
 * - In software development, dependency management refers to handling libraries, packages, or modules that your project relies on.
 * - It ensures that the correct versions of dependencies are installed, updated, and compatible with your project.
 * - Manual management is error-prone; automated tools help streamline the process.

 * Composer:
 * - Composer is the standard dependency manager for PHP.
 * - It allows you to declare the libraries your project depends on in a file called `composer.json`.
 * - Composer automatically installs, updates, and autoloads these libraries.

 * How Composer Works:
 * 1. Initialization:
 *    - You create a `composer.json` file specifying your project's dependencies and other metadata.
 * 2. Installation:
 *    - Running `composer install` reads `composer.json`, downloads the required packages from Packagist (the default repository), and places them in the `vendor/` directory.
 *    - Composer also generates a `composer.lock` file to record the exact versions installed.
 * 3. Autoloading:
 *    - Composer provides an autoloader (`vendor/autoload.php`) to automatically load classes from installed packages.
 * 4. Updating:
 *    - Running `composer update` updates dependencies to the latest allowed versions and updates `composer.lock`.
 * 5. Version Constraints:
 *    - Composer supports semantic versioning and allows you to specify version constraints (e.g., `^1.0`, `~2.3`, `>=1.2`).
 * 6. Scripts and Plugins:
 *    - Composer can run custom scripts and supports plugins for extended functionality.

 * Benefits:
 * - Simplifies dependency management.
 * - Ensures consistency across environments.
 * - Facilitates collaboration and deployment.
 */