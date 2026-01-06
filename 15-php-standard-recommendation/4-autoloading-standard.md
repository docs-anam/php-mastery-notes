# PSR-4: Autoloading Standard

## Overview

Learn about PSR-4, the standardized autoloading mechanism that automatically loads PHP classes based on namespace and file path conventions.

---

## Table of Contents

1. What is PSR-4
2. Basic Concept
3. Mapping Rules
4. Namespace to File Path
5. Composer Configuration
6. Implementation
7. Common Patterns
8. Real-world Examples
9. Complete Examples

---

## What is PSR-4

### Purpose

```php
<?php
// Before PSR-4: Manual file inclusion

// index.php
require_once 'src/User/User.php';
require_once 'src/User/UserRepository.php';
require_once 'src/Services/UserService.php';

$user = new User();

// Problems:
// - Manual requires for each file
// - Order matters (dependencies)
// - Easy to forget files
// - Hard to maintain

// Solution: PSR-4 autoloading (Composer)

require_once 'vendor/autoload.php';

use App\User\User;
use App\Services\UserService;

$user = new User();  // Auto-loaded!

// Benefits:
// ✓ No manual requires
// ✓ Classes loaded on demand
// ✓ Namespace-based loading
// ✓ Works with Composer
```

### PSR-4 vs PSR-0

```
PSR-0 (Old - Deprecated)
├─ Underscores treated as directory separators
├─ Complex mapping
└─ Use PSR-4 instead

PSR-4 (Modern - Standard)
├─ Clean namespace to directory mapping
├─ Only backslash separators
├─ Widely adopted
└─ Use this!

// PSR-0 example (avoid):
// namespace App_User;
// class User maps to App/User.php

// PSR-4 example (use):
// namespace App\User;
// class User maps to App/User.php
```

---

## Basic Concept

### Simple Mapping

```php
<?php
// Namespace: App\User\User
// Prefix: App\
// Base directory: src/

// Maps to: src/User/User.php

// Mapping rule:
// Namespace prefix  → Base directory
// ================  → ==============
// App\              → src/
// Vendor\           → vendor/src/
```

### File Organization

```
Project Structure:
├── src/
│   ├── User/
│   │   ├── User.php
│   │   └── UserService.php
│   ├── Article/
│   │   ├── Article.php
│   │   └── ArticleService.php
│   └── Common/
│       └── BaseService.php
├── vendor/
├── composer.json
└── index.php

Namespace mapping:
App\User\User → src/User/User.php
App\User\UserService → src/User/UserService.php
App\Article\Article → src/Article/Article.php
App\Common\BaseService → src/Common/BaseService.php
```

---

## Mapping Rules

### Basic Rules

```php
<?php
// Rule 1: Namespace backslash = directory separator
namespace App\User\Profile;
// Maps to: App/User/Profile/

// Rule 2: Class name becomes filename
class UserProfile { }
// File: App/User/Profile/UserProfile.php

// Rule 3: Trailing backslash on prefix
// Correct: "App\" not "App"

// Rule 4: Case-sensitive namespace and class
namespace app\user;  // Different from App\User
class user { }       // Different from User
```

### Multiple Prefixes

```php
<?php
// Multiple namespace prefixes can share base directory
// Multiple prefixes can map to different directories

// Config example:
// "autoload": {
//     "psr-4": {
//         "App\\": "src/",
//         "App\\Test\\": "tests/",
//         "Vendor\\": "vendor/src/",
//     }
// }

// Mapping:
// App\User\User → src/User/User.php
// App\Test\TestUser → tests/TestUser.php
// Vendor\Helper\StringHelper → vendor/src/Helper/StringHelper.php
```

---

## Namespace to File Path

### Conversion Process

```php
<?php
// Given:
// - Namespace: App\User\Repository\UserRepository
// - Prefix: App\
// - Base: src/

// Step 1: Remove prefix
// App\User\Repository\UserRepository
// Remove App\ → User\Repository\UserRepository

// Step 2: Convert backslashes to slashes
// User\Repository\UserRepository → User/Repository/UserRepository

// Step 3: Add .php extension
// User/Repository/UserRepository.php

// Step 4: Prepend base directory
// src/User/Repository/UserRepository.php
```

### Examples

```php
<?php
// Example 1
Namespace: App\User\User
Prefix: App\
Base: src/
Result: src/User/User.php

// Example 2
Namespace: Symfony\Component\Console\Command\Command
Prefix: Symfony\Component\
Base: vendor/symfony/console/
Result: vendor/symfony/console/Command/Command.php

// Example 3
Namespace: MyApp\Services\Mailer\SwiftMailer
Prefix: MyApp\Services\
Base: app/services/
Result: app/services/Mailer/SwiftMailer.php

// Example 4
Namespace: Tests\Unit\User\UserServiceTest
Prefix: Tests\Unit\
Base: tests/unit/
Result: tests/unit/User/UserServiceTest.php
```

---

## Composer Configuration

### Basic Setup

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

### Production and Development

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "App\\": "app/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/",
            "Feature\\": "tests/feature/"
        }
    }
}
```

### Real-world Example

```json
{
    "name": "myproject/myapp",
    "description": "My awesome application",
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "Database\\": "database/",
            "Console\\": "console/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/",
            "Tests\\Feature\\": "tests/feature/",
            "Tests\\Unit\\": "tests/unit/"
        }
    },
    "require": {
        "php": ">=8.1"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "squizlabs/php_codesniffer": "^3.7"
    }
}
```

### Classmap (For Libraries)

```json
{
    "autoload": {
        "psr-4": {
            "MyLibrary\\": "src/"
        },
        "classmap": [
            "legacy/",
            "src/LegacyClass.php"
        ]
    }
}
```

---

## Implementation

### Composer Autoloader

```php
<?php
// Bootstrap: index.php
require_once 'vendor/autoload.php';

// Now any PSR-4 mapped class is available
use App\User\User;
use App\Services\UserService;

$user = new User();  // Auto-loaded from src/User/User.php
$service = new UserService();  // Auto-loaded from src/Services/UserService.php
```

### Manual Implementation

```php
<?php
// Example: DIY PSR-4 loader (for learning)

class PSR4Autoloader
{
    private array $prefixes = [];

    public function register(string $prefix, string $basePath): void
    {
        // Ensure trailing backslash and slash
        $prefix = trim($prefix, '\\') . '\\';
        $basePath = trim($basePath, '/\\') . '/';

        $this->prefixes[$prefix] = $basePath;

        spl_autoload_register([$this, 'load']);
    }

    public function load(string $className): bool
    {
        foreach ($this->prefixes as $prefix => $basePath) {
            if (strpos($className, $prefix) === 0) {
                // Remove prefix
                $relative = substr($className, strlen($prefix));

                // Convert namespace to path
                $path = $basePath . str_replace('\\', '/', $relative) . '.php';

                if (file_exists($path)) {
                    require_once $path;
                    return true;
                }
            }
        }

        return false;
    }
}

// Usage
$loader = new PSR4Autoloader();
$loader->register('App\\', 'src/');
$loader->register('Tests\\', 'tests/');
```

---

## Common Patterns

### Monolithic Application

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

```
src/
├── Controllers/
│   ├── UserController.php
│   └── ArticleController.php
├── Models/
│   ├── User.php
│   └── Article.php
├── Services/
│   ├── UserService.php
│   └── ArticleService.php
└── Middleware/
    ├── AuthMiddleware.php
    └── CorsMiddleware.php

Namespaces:
App\Controllers\UserController
App\Models\User
App\Services\UserService
App\Middleware\AuthMiddleware
```

### Modular Application

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "Modules\\User\\": "modules/user/src/",
            "Modules\\Article\\": "modules/article/src/"
        }
    }
}
```

```
src/
├── Core/
│   └── Kernel.php
modules/
├── user/
│   └── src/
│       ├── User.php
│       └── UserService.php
└── article/
    └── src/
        ├── Article.php
        └── ArticleService.php

Namespaces:
App\Core\Kernel
Modules\User\User
Modules\User\UserService
Modules\Article\Article
```

---

## Real-world Examples

### Framework-Style Project

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "App\\Console\\": "app/console/",
            "App\\Http\\Controllers\\": "app/http/controllers/",
            "App\\Http\\Middleware\\": "app/http/middleware/",
            "App\\Models\\": "app/models/",
            "App\\Providers\\": "app/providers/",
            "App\\Services\\": "app/services/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/",
            "Tests\\Feature\\": "tests/feature/",
            "Tests\\Unit\\": "tests/unit/"
        }
    }
}
```

### Library Project

```json
{
    "name": "myvendor/mylib",
    "autoload": {
        "psr-4": {
            "MyVendor\\MyLib\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "MyVendor\\MyLib\\Tests\\": "tests/"
        }
    }
}
```

---

## Complete Examples

### Example 1: Project Setup

```bash
# 1. Create composer.json
cat > composer.json << 'EOF'
{
    "name": "myproject/app",
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    }
}
EOF

# 2. Create directory structure
mkdir -p src/{Models,Services,Controllers}
mkdir -p tests/{Unit,Feature}

# 3. Create example classes
cat > src/Models/User.php << 'EOF'
<?php
declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        private int $id,
        private string $email,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
EOF

# 4. Run composer install
composer install

# 5. Test autoloading
php -r "require 'vendor/autoload.php'; echo class_exists('App\Models\User') ? 'OK' : 'FAIL';"
```

### Example 2: Using Autoloaded Classes

```php
<?php
// File: index.php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Services\UserService;

// Classes are auto-loaded!
$user = new User(1, 'john@example.com');
echo $user->getEmail();  // john@example.com

$service = new UserService();
$service->displayUser($user);
```

### Example 3: Tests with PSR-4

```php
<?php
// File: tests/Unit/Models/UserTest.php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $user = new User(1, 'john@example.com');

        $this->assertEquals(1, $user->getId());
        $this->assertEquals('john@example.com', $user->getEmail());
    }
}
```

---

## Key Takeaways

**PSR-4 Autoloading Checklist:**

1. ✅ Configure autoload in composer.json
2. ✅ Use backslash in namespaces
3. ✅ One class per file
4. ✅ Class name matches filename
5. ✅ Directory structure matches namespace
6. ✅ Include vendor/autoload.php
7. ✅ Run composer dump-autoload after changes
8. ✅ Test autoloading works

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Basic Coding Standard (PSR-1)](2-basic-coding-standard.md)
- [Container Interface (PSR-11)](7-container-interface.md)
