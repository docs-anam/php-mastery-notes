<?php
/**
 * Detailed Summary: Configuration in PHPUnit Unit Testing
 *
 * PHPUnit is a widely used framework for testing PHP code. Its configuration system allows you to customize how tests are discovered, executed, and reported. Proper configuration improves test reliability, maintainability, and integration with development workflows.
 *
 * 1. Configuration File (`phpunit.xml` or `phpunit.xml.dist`)
 *    - Located in the project root, this XML file defines global settings for PHPUnit.
 *    - Common elements:
 *      - `<phpunit>`: Root element. Attributes include:
 *          - `bootstrap`: Path to bootstrap file (autoload dependencies, set up environment).
 *          - `colors`: Enable colored output in terminal.
 *          - `verbose`: Show detailed output.
 *          - `stopOnFailure`, `stopOnError`, `stopOnWarning`: Control test execution flow.
 *          - `failOnRisky`, `failOnIncomplete`, `failOnSkipped`: Treat certain test outcomes as failures.
 *      - `<testsuites>`: Organize test files into logical groups (e.g., Unit, Integration).
 *          - `<testsuite name="...">`: Define a suite, specify `<directory>` or `<file>` elements.
 *      - `<php>`: Set PHP ini values and environment variables.
 *          - `<env name="...">`: Set environment variables for tests.
 *          - `<ini name="...">`: Set PHP ini settings (e.g., memory_limit).
 *      - `<coverage>`: Configure code coverage reporting.
 *          - `<include>` / `<exclude>`: Specify which files/directories to include/exclude.
 *      - `<logging>`: Configure output logs.
 *          - `<junit>`: Output results in JUnit XML format.
 *          - `<coverage-html>`, `<coverage-clover>`, `<coverage-xml>`: Output code coverage reports.
 *      - `<filter>`: Fine-tune which files are included/excluded from coverage.
 *      - `<extensions>`: Load custom PHPUnit extensions.
 *      - `<listeners>`: Attach event listeners for custom reporting or hooks.
 *
 * 2. Example `phpunit.xml`:
 *    ```xml
 *    <phpunit bootstrap="vendor/autoload.php"
 *             colors="true"
 *             verbose="true"
 *             stopOnFailure="false"
 *             failOnRisky="true">
 *      <testsuites>
 *        <testsuite name="Unit">
 *          <directory>tests/Unit</directory>
 *        </testsuite>
 *        <testsuite name="Integration">
 *          <directory>tests/Integration</directory>
 *        </testsuite>
 *      </testsuites>
 *      <php>
 *        <env name="APP_ENV" value="testing"/>
 *        <env name="DB_HOST" value="localhost"/>
 *        <ini name="memory_limit" value="512M"/>
 *      </php>
 *      <coverage>
 *        <include>
 *          <directory>src/</directory>
 *        </include>
 *        <exclude>
 *          <directory>src/Legacy/</directory>
 *        </exclude>
 *      </coverage>
 *      <logging>
 *        <junit outputFile="build/logs/junit.xml"/>
 *        <coverage-html outputDirectory="build/coverage"/>
 *      </logging>
 *    </phpunit>
 *    ```
 *
 * 3. Command-Line Configuration
 *    - Override XML settings using CLI options:
 *      - `--bootstrap=path/to/file.php`: Specify bootstrap file.
 *      - `--filter=pattern`: Run only tests matching pattern.
 *      - `--coverage-html=dir`: Generate HTML coverage report.
 *      - `--configuration=phpunit.xml`: Specify config file.
 *      - `--stop-on-failure`: Stop after first failure.
 *    - Useful for CI/CD pipelines or local overrides.
 *
 * 4. Test Case Configuration
 *    - In test classes, use lifecycle methods:
 *      - `setUp()`: Prepare environment before each test (e.g., create objects, set state).
 *      - `tearDown()`: Clean up after each test (e.g., remove files, reset state).
 *      - `setUpBeforeClass()`, `tearDownAfterClass()`: Run once per class.
 *    - Use mock objects, fixtures, and data providers for flexible test setups.
 *
 * 5. Environment Variables and INI Settings
 *    - Set via XML `<php>` section or CLI.
 *    - Control PHP runtime (e.g., error reporting, memory limits).
 *    - Pass secrets or configuration for integration tests.
 *
 * 6. Best Practices
 *    - Use `phpunit.xml.dist` for default settings (committed to version control).
 *    - Override with `phpunit.xml` for local or CI-specific changes.
 *    - Keep configuration files in project root for easy discovery.
 *    - Version control your configuration files to ensure consistency.
 *    - Document custom settings for team members.
 *    - Regularly review and update configuration as project evolves.
 *
 * 7. Running PHPUnit via Composer Script
 *
 * You can define a PHPUnit command as a script in your `composer.json` file for easy execution:
 *
 * Example `composer.json` section:
 * {
 *   "scripts": {
 *     "test": "phpunit --configuration=phpunit.xml"
 *   }
 * }
 *
 * Steps:
 * 1. Add the above "scripts" section to your `composer.json` file.
 * 2. Install PHPUnit via Composer (if not already):
 *    composer require --dev phpunit/phpunit
 * 3. Run your tests using:
 *    composer test
 *
 * This ensures consistent configuration and makes it easy for all team members to run tests.
 *
 * References:
 * - https://phpunit.de/documentation.html
 * - https://phpunit.readthedocs.io/en/latest/configuration.html
 */