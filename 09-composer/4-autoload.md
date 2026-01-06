# Autoloading in Composer

## Overview

Autoloading is a mechanism that automatically includes PHP files when you need a class, without requiring manual `require` or `include` statements. Composer supports several autoloading standards, primarily PSR-4. This chapter explains autoloading, its importance, and how to configure it in your projects.

---

## Table of Contents

1. What is Autoloading
2. PSR-4 Standard
3. PSR-0 Standard
4. Classmap Autoloading
5. Files Autoloading
6. Configuring Autoload
7. Using Autoload
8. Debugging Autoload
9. Complete Examples

---

## What is Autoloading

### Without Autoloading

```php
// Manual approach - error-prone
require_once 'src/User.php';
require_once 'src/Database.php';
require_once 'src/Repository/UserRepository.php';
require_once 'src/Service/UserService.php';

// Easy to forget files
// Hard to maintain
// Doesn't scale

$user = new User();
$repo = new UserRepository();
```

### With Autoloading

```php
// Autoloading - clean and simple
// Composer registers autoloader once
// Use classes without manual includes

$user = new User();
$repo = new UserRepository();
$service = new UserService();

// Classes are automatically loaded based on namespace/class name
```

### Benefits

```
Without Autoloading:
- Manual require/include statements
- Error-prone (easy to forget files)
- Not scalable
- Hard to maintain
- Performance: Load all files at startup

With Autoloading:
- Automatic file loading on demand
- Clean, readable code
- Scalable to large projects
- Easy to maintain
- Performance: Load only needed files
- PSR compliance
```

---

## PSR-4 Standard

### PSR-4 Overview

PSR-4 (PHP Standards Recommendation 4) maps class namespaces to directory structures.

```
Namespace:    MyApp\Repository\User
Directory:    src/Repository/User.php

MyApp\        → src/
Repository\   → Repository/
User          → User.php
```

### Configuring PSR-4

```json
{
    "autoload": {
        "psr-4": {
            "MyApp\\": "src/",
            "MyApp\\Tests\\": "tests/",
            "Vendor\\Package\\": "lib/"
        }
    }
}
```

### PSR-4 Directory Structure

```
my-project/
├── src/                          (maps to MyApp\)
│   ├── User.php                 → MyApp\User
│   ├── Controllers/
│   │   ├── UserController.php   → MyApp\Controllers\UserController
│   │   └── PostController.php   → MyApp\Controllers\PostController
│   ├── Repository/
│   │   ├── User.php             → MyApp\Repository\User
│   │   └── Post.php             → MyApp\Repository\Post
│   └── Service/
│       └── UserService.php      → MyApp\Service\UserService
├── tests/                        (maps to MyApp\Tests\)
│   ├── Unit/
│   │   └── UserTest.php         → MyApp\Tests\Unit\UserTest
│   └── Feature/
│       └── UserControllerTest.php → MyApp\Tests\Feature\UserControllerTest
└── vendor/
```

### Multiple PSR-4 Mappings

```json
{
    "autoload": {
        "psr-4": {
            "MyCompany\\": "src/",
            "MyCompany\\Tests\\": "tests/",
            "OtherVendor\\Package\\": "vendor/other/package/src/"
        }
    }
}
```

### Using PSR-4 Classes

```php
<?php
// composer.json defines: "MyApp\\" => "src/"

// File: src/User.php
namespace MyApp;

class User {
    public function getName() {
        return 'John Doe';
    }
}

// File: src/Repository/UserRepository.php
namespace MyApp\Repository;

class UserRepository {
    public function getUser($id) {
        // ...
    }
}

// File: public/index.php
require 'vendor/autoload.php';

// Classes are automatically loaded
$user = new \MyApp\User();
$repo = new \MyApp\Repository\UserRepository();
```

---

## PSR-0 Standard (Legacy)

### PSR-0 Overview

PSR-0 is the older standard, now deprecated. It includes underscores in directory structure.

```
Namespace:    MyApp_Repository_User
Directory:    src/MyApp/Repository/User.php

MyApp_        → src/MyApp/
Repository_   → Repository/
User          → User.php
```

### Configuring PSR-0

```json
{
    "autoload": {
        "psr-0": {
            "MyApp_": "src/"
        }
    }
}
```

### PSR-0 vs PSR-4

```
PSR-0 (Legacy):          PSR-0 is deprecated
Namespace: Foo_Bar_Baz   Map to: src/Foo/Bar/Baz.php
Class: Foo_Bar_Baz       Underscores in namespace path

PSR-4 (Modern):          PSR-4 is the standard
Namespace: Foo\Bar\Baz   Map to: src/Bar/Baz.php
Class: Foo\Bar\Baz       Backslashes only
```

---

## Classmap Autoloading

### Classmap Configuration

```json
{
    "autoload": {
        "classmap": [
            "src/",
            "lib/",
            "legacy/OldClass.php"
        ]
    }
}
```

### How Classmap Works

```
1. Composer scans directories
2. Finds all PHP files
3. Extracts class names
4. Creates class -> file mapping
5. Stores in vendor/composer/autoload_classmap.php

Advantage: Very fast (direct lookup)
Disadvantage: Requires regeneration when files added
```

### Regenerating Classmap

```bash
# When you add new files
composer dump-autoload

# Recreates autoload_classmap.php
# Optimizes autoload files
```

---

## Files Autoloading

### Including Files Automatically

```json
{
    "autoload": {
        "files": [
            "src/helpers.php",
            "src/constants.php",
            "config/database.php"
        ]
    }
}
```

### Files Autoload Example

```bash
# Project structure
project/
├── src/
│   ├── helpers.php        # Helper functions
│   ├── constants.php      # App constants
│   └── functions.php      # Utility functions
├── vendor/
└── composer.json
```

```json
{
    "autoload": {
        "files": [
            "src/constants.php",
            "src/helpers.php",
            "src/functions.php"
        ]
    }
}
```

```php
<?php
// src/helpers.php

function dd($var) {
    var_dump($var);
    die;
}

function dump($var) {
    var_dump($var);
}

function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}
```

```php
<?php
// public/index.php

require 'vendor/autoload.php';

// Functions are available without requiring helpers.php
dd(['user' => 'John']);
dump(env('APP_NAME'));
```

---

## Configuring Autoload

### Complete composer.json Example

```json
{
    "name": "my-company/my-app",
    "type": "project",
    "autoload": {
        "psr-4": {
            "MyApp\\": "src/"
        },
        "files": [
            "src/helpers.php"
        ],
        "classmap": [
            "legacy/"
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "MyApp\\Tests\\": "tests/"
        }
    }
}
```

### Autoload-Dev Configuration

```json
{
    "autoload": {
        "psr-4": {
            "MyApp\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "MyApp\\Tests\\": "tests/"
        },
        "files": [
            "tests/fixtures.php"
        ]
    }
}
```

### Regenerating Autoloader

```bash
# After modifying composer.json
composer dump-autoload

# Optimized autoload (production)
composer dump-autoload --optimize

# Force regeneration
composer dump-autoload --no-dev

# Classmap only
composer dump-autoload --classmap-authoritative
```

---

## Using Autoload

### Loading the Autoloader

```php
<?php
// This must be first in your application
require_once 'vendor/autoload.php';

// Now all classes are available
$user = new MyApp\User();
$service = new MyApp\Service\UserService();
```

### Where to Include

```
Public Entry Points:
- public/index.php
- tests/bootstrap.php
- cli/console.php

Do NOT include in:
- Library src/ files (included by autoloader)
- Individual model classes
- Reusable components
```

### Common Include Points

```php
<?php
// public/index.php - Web application
require __DIR__ . '/../vendor/autoload.php';

// tests/bootstrap.php - Test suite
require __DIR__ . '/../vendor/autoload.php';

// cli/commands.php - CLI application
require __DIR__ . '/../vendor/autoload.php';
```

---

## Debugging Autoload

### Viewing Autoload Configuration

```bash
# Show autoload information
composer diagnose

# List autoloaded namespaces
composer show -a

# Show PSR-4 mappings
grep -A 5 "psr-4" vendor/composer/installed.json

# View generated autoload files
ls -la vendor/composer/
```

### Finding Class Files

```bash
# Search where a class would be autoloaded from
find src -name "*.php" | grep -i classname

# Check if class exists
grep -r "class ClassName" src/

# List all classes in namespace
find src -name "*.php" -exec grep "^class " {} +
```

### Debugging Autoload Issues

```php
<?php
// Check what's loaded
require 'vendor/autoload.php';

$loader = require 'vendor/autoload.php';

// Get all PSR-4 prefixes
$prefixes = $loader->getPrefixesPsr4();
var_dump(array_keys($prefixes));

// Get all classmap entries (count)
$classmap = require 'vendor/composer/autoload_classmap.php';
echo "Classmap entries: " . count($classmap);

// Check if class would load
if (method_exists('MyApp\User', '__construct')) {
    echo "MyApp\User loaded successfully";
}
```

---

## Complete Examples

### Example 1: Simple Application

```json
{
    "name": "my-vendor/my-app",
    "require": {
        "php": ">=8.0"
    },
    "autoload": {
        "psr-4": {
            "MyApp\\": "src/"
        },
        "files": [
            "src/helpers.php"
        ]
    }
}
```

```php
<?php
// src/User.php
namespace MyApp;

class User {
    public function getName() {
        return 'John';
    }
}

// src/Repository/UserRepository.php
namespace MyApp\Repository;

class UserRepository {
    public function findById($id) {
        return new \MyApp\User();
    }
}

// src/helpers.php
<?php

function cache($key, $default = null) {
    // Cache implementation
}

// public/index.php
<?php
require 'vendor/autoload.php';

$user = new \MyApp\User();
$repo = new \MyApp\Repository\UserRepository();

echo $user->getName();
```

### Example 2: Library with Tests

```json
{
    "name": "my-vendor/my-library",
    "type": "library",
    "autoload": {
        "psr-4": {
            "MyVendor\\MyLibrary\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "MyVendor\\MyLibrary\\Tests\\": "tests/"
        }
    }
}
```

### Example 3: Complex Project

```json
{
    "name": "my-company/project",
    "autoload": {
        "psr-4": {
            "MyCompany\\": "src/",
            "MyCompany\\Legacy\\": "legacy/"
        },
        "classmap": [
            "legacy/ancient/"
        ],
        "files": [
            "src/helpers.php",
            "src/constants.php"
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "MyCompany\\Tests\\": "tests/"
        },
        "files": [
            "tests/bootstrap.php"
        ]
    }
}
```

---

## Key Takeaways

**Autoloading Checklist:**

1. ✅ Use PSR-4 for new projects (modern standard)
2. ✅ Map namespaces to directory structure
3. ✅ Include vendor/autoload.php in entry points only
4. ✅ Use autoload-dev for test files
5. ✅ Regenerate autoload after modifying composer.json
6. ✅ Use files autoload for helper functions
7. ✅ Don't manually require/include class files
8. ✅ Test autoload with `composer diagnose`

---

## See Also

- [Creating Composer Projects](3-create-composer-project.md)
- [Installing Composer](2-install-composer.md)
- [Repository Configuration](5-repository.md)
