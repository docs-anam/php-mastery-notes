# Include and Require in PHP

## Overview

The `include` and `require` statements allow you to insert the content of one PHP file into another PHP file. This is essential for code organization, reusability, and maintainability. The main difference between them is how they handle errors: `require` halts script execution if the file is not found, while `include` only generates a warning.

## Basic Include/Require Structure

### Simple Include

```php
<?php
// Include a file
include 'header.php';

echo "Main page content\n";

// Include footer
include 'footer.php';

// Include works even if file already included
include 'header.php';  // No error, includes again
?>
```

### Simple Require

```php
<?php
// Require a file
require 'config.php';

// If config.php doesn't exist, script stops here
echo "This won't execute if require fails\n";

// Require works only once
require 'config.php';  // Includes again
?>
```

### Require_once

```php
<?php
// Requires file only once
require_once 'database.php';

// Later in code
require_once 'database.php';  // File NOT included again

// Useful for preventing duplicate definitions
?>
```

### Include_once

```php
<?php
// Includes file only once
include_once 'utils.php';

// Safe to include multiple times - won't duplicate
include_once 'utils.php';
?>
```

## Practical Examples

### Project Structure Organization

```php
// File: /project/config/database.php
<?php
class Database {
    public static $host = 'localhost';
    public static $user = 'root';
    public static $pass = 'password';
}

// File: /project/utils/helpers.php
<?php
function formatDate($date) {
    return date('Y-m-d', strtotime($date));
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// File: /project/index.php
<?php
require_once 'config/database.php';
require_once 'utils/helpers.php';

echo "Database host: " . Database::$host . "\n";
echo "Email check: " . (isValidEmail('test@example.com') ? 'Valid' : 'Invalid') . "\n";
?>
```

### Shared Header and Footer

```php
// File: includes/header.php
<?php
echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head><title>My Site</title></head>\n";
echo "<body>\n";
echo "<header>Welcome to My Site</header>\n";
?>

// File: includes/footer.php
<?php
echo "<footer>© 2024 My Company</footer>\n";
echo "</body>\n";
echo "</html>\n";
?>

// File: pages/home.php
<?php
require_once 'includes/header.php';
echo "<h1>Home Page</h1>\n";
echo "<p>Welcome to our website.</p>\n";
require_once 'includes/footer.php';
?>
```

### Configuration Files

```php
// File: config/settings.php
<?php
return [
    'app_name' => 'My Application',
    'app_version' => '1.0.0',
    'debug' => true,
    'timezone' => 'UTC',
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'myapp'
    ],
    'cache' => [
        'driver' => 'redis',
        'ttl' => 3600
    ]
];

// File: bootstrap.php
<?php
$config = require 'config/settings.php';

if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

date_default_timezone_set($config['timezone']);
?>

// File: index.php
<?php
require 'bootstrap.php';

echo "App: " . $config['app_name'] . "\n";
echo "Version: " . $config['app_version'] . "\n";
?>
```

### Conditional File Inclusion

```php
<?php
// Include different files based on environment
$environment = getenv('APP_ENV') ?: 'development';

if ($environment === 'production') {
    require 'config/production.php';
    require 'security/production-checks.php';
} else {
    require 'config/development.php';
    require 'security/development-checks.php';
}

// Common config for all environments
require_once 'config/common.php';
?>
```

### Template Engine Pattern

```php
// File: templates/user-profile.php
<?php
// Expects $user variable
echo "Profile: " . htmlspecialchars($user['name']) . "\n";
echo "Email: " . htmlspecialchars($user['email']) . "\n";
echo "Bio: " . htmlspecialchars($user['bio']) . "\n";
?>

// File: show-profile.php
<?php
function renderTemplate($file, $variables) {
    // Extract variables into local scope
    extract($variables);
    
    // Start output buffering
    ob_start();
    
    // Include template file
    require $file;
    
    // Get buffered output
    return ob_get_clean();
}

$user = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'bio' => 'Software Developer'
];

echo renderTemplate('templates/user-profile.php', ['user' => $user]);
?>
```

## Include vs Require vs Include_once vs Require_once

### Comparison Table

```php
<?php
// INCLUDE - warning if missing, continues
include 'optional-file.php';
echo "This executes even if file missing\n";

// REQUIRE - fatal error if missing, stops
require 'required-file.php';
echo "This only executes if file found\n";

// INCLUDE_ONCE - includes max once
include_once 'script.php';
include_once 'script.php';  // Won't include again

// REQUIRE_ONCE - requires max once
require_once 'config.php';
require_once 'config.php';  // Won't include again
?>
```

## Return Values

### Including Files with Return

```php
// File: functions.php
<?php
function add($a, $b) {
    return $a + $b;
}

function multiply($a, $b) {
    return $a * $b;
}

// Last statement as return
return [
    'add' => 'add',
    'multiply' => 'multiply'
];

// File: calculate.php
<?php
$functions = require 'functions.php';

echo $functions['add'](5, 3) . "\n";
echo $functions['multiply'](5, 3) . "\n";
?>
```

## Common Pitfalls

### Including Same File Multiple Times

```php
<?php
// BUG - includes multiple times
include 'setup.php';  // First include
include 'setup.php';  // Includes again!

// Can cause issues if file has:
// - Class definitions (redeclaration error)
// - Global variable modifications
// - Side effects (database connections)

// FIXED - use _once
require_once 'setup.php';
require_once 'setup.php';  // Won't include again
?>
```

### Path Issues

```php
<?php
// BUG - relative paths confusing
include 'config.php';  // Where is this file?

// FIXED - use absolute paths
require_once __DIR__ . '/config.php';
require_once dirname(__FILE__) . '/config.php';

// Or from application root
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
?>
```

### Circular Dependencies

```php
<?php
// File: a.php
require 'b.php';
function funcA() { return "A"; }

// File: b.php
require 'a.php';  // BUG - creates circular dependency!
function funcB() { return funcA() . " B"; }

// FIXED - reorganize code
// Or use require_once to prevent infinite loops
require_once 'a.php';
require_once 'b.php';
?>
```

### Exposed Variables

```php
<?php
// File: include_me.php
<?php
$secret = "password123";
echo $secret;  // Gets exposed!

// File: show.php
<?php
include 'include_me.php';  // Now $secret is in global scope

// BETTER - use functions
// File: include_me.php
<?php
function getSecret() {
    return "password123";  // Keep in function scope
}

// File: show.php
<?php
include 'include_me.php';
$secret = getSecret();  // Explicitly get value
?>
```

## Best Practices

✓ **Use require_once/include_once** - prevent duplicates
✓ **Use require** - for essential files (config, database)
✓ **Use include** - for optional files (themes, plugins)
✓ **Use absolute paths** - with __DIR__ or dirname()
✓ **Organize files** - logical directory structure
✓ **Namespace included files** - avoid conflicts
✓ **Keep includes at top** - easier to track dependencies
✓ **Document dependencies** - explain what each file does
✓ **Avoid global variables** - use function parameters
✓ **Consider autoloading** - for large projects (PSR-4)

## Key Takeaways

✓ **include** - includes file, warning if missing
✓ **require** - includes file, fatal error if missing
✓ **include_once** - includes file max once
✓ **require_once** - requires file max once
✓ **Use _once variants** - prevents duplicate inclusions
✓ **Use absolute paths** - with __DIR__ or __FILE__
✓ **Return values** - files can return data
✓ **Avoid circular dependencies** - reorganize code
✓ **Security** - validate/escape included content
✓ **Autoloading** - modern alternative for classes
