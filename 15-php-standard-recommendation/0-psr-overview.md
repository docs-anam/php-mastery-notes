# PHP Standard Recommendations Overview

## Introduction

Learn about PHP Standards Recommendations (PSRs), which are standardized guidelines and interfaces for PHP development, maintained by the PHP Framework Interoperability Group (PHP-FIG).

---

## Table of Contents

1. What are PSRs
2. Major PSRs Overview
3. PSR Categories
4. Adoption and Community
5. Using PSRs in Projects
6. PSR Evolution
7. Key Decision Points
8. Implementation Guide

---

## What are PSRs

### Purpose

```php
<?php
// Without standards: Everyone implements their own way

// Logger implementation 1
class Logger1 {
    public function log($message) {}
    public function error($message) {}
}

// Logger implementation 2
class Logger2 {
    public function write($msg) {}
    public function writeError($msg) {}
}

// Problem:
// - Different method names
// - Different implementations
// - Cannot swap implementations
// - No interoperability

// Solution: PSRs standardize interfaces and practices
interface PSR3Logger {
    public function log($level, $message, array $context = array());
}

// All implementations follow same contract
class StandardLogger implements PSR3Logger {
    public function log($level, $message, array $context = array()) {
        // Standard implementation
    }
}

// Benefits:
// ✓ Interoperable libraries
// ✓ Swappable implementations
// ✓ Predictable interfaces
// ✓ Community consensus
```

### Who Maintains PSRs

```
PHP Framework Interoperability Group (PHP-FIG)
├─ Founded 2009
├─ Members: Composer, Doctrine, Drupal, Laminas, Laravel, Symfony, etc.
├─ Process: Formal voting on proposals
├─ Monthly meetings
├─ Public PSR registry
└─ Independent from any single framework
```

---

## Major PSRs Overview

### Essential PSRs

```php
<?php
// PSR-1: Basic Coding Standard
// - File formatting
// - Naming conventions
// - Class and method structure

class MyClass {
    public function myMethod() {
        // Method body
    }
}

// PSR-2: Coding Style Guide (superseded by PSR-12)
// - Indentation (4 spaces)
// - Line length
// - Class/method declarations

// PSR-3: Logger Interface
interface LoggerInterface {
    public function log($level, $message, array $context = array());
    public function debug($message, array $context = array());
    public function info($message, array $context = array());
}

// PSR-4: Autoloading Standard
// Namespace\Class maps to directory/Class.php
// Composer auto-loading

// PSR-6: Caching Interface
interface CacheItemPoolInterface {
    public function getItem($key);
    public function saveDeferred(CacheItemInterface $item);
}

// PSR-7: HTTP Message Interface
interface RequestInterface extends MessageInterface {
    public function getRequestTarget();
    public function getMethod();
    public function getUri();
}

// PSR-11: Container Interface
interface ContainerInterface {
    public function get($id);
    public function has($id);
}

// PSR-12: Extended Coding Style Guide
// - Continuation lines
// - Declare statements
// - Type declarations
```

---

## PSR Categories

### Accepted PSRs (Stable)

```
PSR-1 - Basic Coding Standard
PSR-2 - Coding Style Guide (superseded)
PSR-3 - Logger Interface ✓
PSR-4 - Autoloading Standard ✓
PSR-5 - PHPDoc Standard (draft)
PSR-6 - Caching Interface ✓
PSR-7 - HTTP Message Interface ✓
PSR-8 - Huggable Interface (abandoned)
PSR-9 - Security Policy (draft)
PSR-10 - Security Reporting (draft)
PSR-11 - Container Interface ✓
PSR-12 - Extended Coding Style Guide ✓
PSR-13 - Hypermedia Links
PSR-14 - Event Dispatcher
PSR-15 - HTTP Handlers/Middleware
PSR-16 - Simple Cache
PSR-17 - HTTP Factories
PSR-18 - HTTP Client
PSR-20 - Clock Interface
PSR-21 - Serializable Objects (draft)
```

### Status Types

```
Accepted - Stable, ready for production
Review - Under community review
Draft - In development
Deprecated - Replaced by newer PSR
Abandoned - No longer maintained
```

---

## PSR Categories

### Infrastructure PSRs

```php
<?php
// PSR-4: Autoloading
// Composer loads classes based on namespace

// src/User/UserRepository.php
namespace App\User;
class UserRepository {}

// Configured in composer.json:
// "autoload": {
//     "psr-4": {
//         "App\\": "src/"
//     }
// }

// PSR-11: Container/Dependency Injection
interface ContainerInterface {
    public function get(string $id);
    public function has(string $id);
}

// Standardizes how dependencies are resolved
```

### Interface PSRs

```php
<?php
// PSR-3: Logger Interface
interface LoggerInterface {
    public function log($level, $message, array $context = []);
}

// PSR-7: HTTP Messages
interface RequestInterface {}
interface ResponseInterface {}

// PSR-6: Cache
interface CacheItemPoolInterface {}

// All libraries implement these interfaces
// Applications can swap implementations
```

### Style PSRs

```php
<?php
// PSR-1: Basic Coding Standard
// - UTF-8 encoding
// - 4 spaces indentation
// - Consistent naming

class MyClass
{
    public function myMethod()
    {
        // 4 space indentation
    }
}

// PSR-12: Extended Style
// - Type hints
// - Return types
// - Declare statements
```

---

## Adoption and Community

### Industry Adoption

```
Framework Support:
✓ Symfony - Follows PSRs (3, 4, 6, 7, 11, 12, 15, 18)
✓ Laravel - Mostly follows PSRs
✓ Laminas - Full PSR support
✓ Drupal - Adopting PSRs gradually
✓ Composer - PSR-4 standard

Libraries:
✓ Monolog - Implements PSR-3
✓ Guzzle - Implements PSR-7, PSR-18
✓ Doctrine - Implements PSR-4
✓ PHPUnit - Uses PSR-1, PSR-12
```

### Tools and Enforcement

```php
<?php
// PHP-CS-Fixer: Auto-format to PSR-12
// composer require --dev friendsofphp/php-cs-fixer
// php-cs-fixer fix src/

// PHPCodeSniffer: Check PSR compliance
// composer require --dev squizlabs/php_codesniffer
// phpcs --standard=PSR12 src/

// IDEs: Built-in PSR support
// - PhpStorm: Configurable PSR profiles
// - VS Code: PHP extensions with PSR detection
```

---

## Using PSRs in Projects

### Setup Composer

```json
{
    "require": {
        "psr/log": "^3.0",
        "psr/cache": "^3.0",
        "psr/container": "^2.0",
        "psr/http-message": "^2.0",
        "psr/http-client": "^1.0",
        "psr/http-factory": "^1.0"
    },
    "require-dev": {
        "friendsofphp/php-cs-fixer": "^3.0",
        "squizlabs/php_codesniffer": "^3.7"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

### Configure Code Style

```php
<?php
// .php-cs-fixer.dist.php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'strict_param' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('src')
            ->in('tests')
    );
```

### Implement Standards

```php
<?php
// src/Services/Logger.php

declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    public function log($level, $message, array $context = []): void
    {
        // PSR-3 compliant
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }
}
```

---

## PSR Evolution

### Timeline

```
2009 - PHP-FIG Founded
2012 - PSR-1, PSR-2 (Basics)
2012 - PSR-3 (Logger) ✓
2013 - PSR-4 (Autoloading) ✓
2015 - PSR-6 (Cache) ✓
2015 - PSR-7 (HTTP) ✓
2017 - PSR-11 (Container) ✓
2019 - PSR-12 (Extended Style) ✓
2020 - PSR-13, 14, 15 (HTTP)
2021 - PSR-16 (Simple Cache) ✓
2022 - PSR-18 (HTTP Client) ✓
2023 - PSR-20 (Clock) ✓
```

### Why Supersession

```
PSR-2 → PSR-12
├─ PSR-2: Basic coding style
├─ PSR-12: Extended with modern features
│   ├─ Type hints
│   ├─ Return types
│   ├─ Declare statements
│   └─ Arrow functions
└─ Action: Use PSR-12, ignore PSR-2

Deprecation doesn't break code:
✓ Old code still works
✓ Tools auto-upgrade
✓ New projects use new PSR
```

---

## Key Decision Points

### Which PSRs to Use

```php
<?php
// Mandatory for most projects:
// ✓ PSR-1: Basic Coding Standard
// ✓ PSR-4: Autoloading
// ✓ PSR-12: Extended Coding Style

// Recommended for libraries:
// ✓ PSR-3: Logging (if logging needed)
// ✓ PSR-6: Caching (if caching needed)
// ✓ PSR-11: Container (if DI needed)

// For HTTP projects:
// ✓ PSR-7: HTTP Messages
// ✓ PSR-15: HTTP Handlers
// ✓ PSR-17: HTTP Factories
// ✓ PSR-18: HTTP Client
```

### Implementation Strategies

```php
<?php
// Strategy 1: Strict Adherence
// - Follow all PSRs
// - Use standard packages
// - Maximum interoperability

require 'vendor/autoload.php';

$logger = new SomeLogger();  // Implements PSR-3
$cache = new SomeCache();    // Implements PSR-6
$container = new SomeContainer();  // Implements PSR-11

// Strategy 2: Selective Adoption
// - Use PSRs for public interfaces
// - Internal code may vary
// - Balance between standards and practicality

interface InternalService extends PSR3Logger {
    // Custom methods + standard interface
}

// Strategy 3: Gradual Migration
// - Start with PSR-4 and PSR-12
// - Add interfaces over time
// - Refactor existing code
```

---

## Implementation Guide

### Starting New Project

```bash
# 1. Create composer.json
composer init

# 2. Add PSR packages
composer require psr/log psr/cache psr/container

# 3. Add dev tools
composer require --dev friendsofphp/php-cs-fixer squizlabs/php_codesniffer

# 4. Configure autoloading
# Edit composer.json with PSR-4 config

# 5. Create directory structure
mkdir -p src/Services src/Repository tests/Unit

# 6. Run Composer
composer update
```

### Converting Existing Project

```php
<?php
// Before: Non-standard code
class MyLogger {
    public function write_log($msg) {
        file_put_contents('log.txt', $msg);
    }
}

// After: PSR-compliant
declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    public function log($level, $message, array $context = []): void
    {
        // Implementation
    }
}

// Steps:
// 1. Fix code style: php-cs-fixer fix src/
// 2. Add type hints: Add return types, parameter types
// 3. Implement PSR interfaces
// 4. Update dependencies
// 5. Run tests
```

### CI/CD Integration

```bash
#!/bin/bash
# scripts/check.sh

# PSR-12 checking
vendor/bin/phpcs --standard=PSR12 src/

# PSR-12 fixing
vendor/bin/php-cs-fixer fix src/ --dry-run --diff

# Type checking
vendor/bin/phpstan analyze src/

# Testing
vendor/bin/phpunit

# All must pass for merge
```

---

## Complete PSR Reference

### Quick Reference Table

```
PSR  | Name                           | Status   | Focus
-----|--------------------------------|----------|------------------
1    | Basic Coding Standard          | Accepted | Code format
2    | Coding Style Guide             | Superseded | Style (use PSR-12)
3    | Logger Interface               | Accepted | Logging
4    | Autoloading Standard           | Accepted | Class loading
5    | PHPDoc Standard                | Draft    | Documentation
6    | Caching Interface              | Accepted | Caching
7    | HTTP Message Interface         | Accepted | HTTP abstraction
11   | Container Interface            | Accepted | Dependency injection
12   | Extended Coding Style Guide    | Accepted | Extended style
13   | Hypermedia Links               | Accepted | Link format
14   | Event Dispatcher               | Accepted | Event handling
15   | HTTP Handlers/Middleware       | Accepted | Middleware
16   | Simple Cache                   | Accepted | Simple caching
17   | HTTP Factories                 | Accepted | HTTP creation
18   | HTTP Client                    | Accepted | HTTP requests
20   | Clock Interface                | Accepted | Time abstraction
```

---

## Key Takeaways

**PHP Standards Recommendations Checklist:**

1. ✅ Understand PSR purpose and benefits
2. ✅ Use PSR-4 for autoloading
3. ✅ Follow PSR-12 for code style
4. ✅ Implement PSR-3 for logging
5. ✅ Use PSR-6/16 for caching
6. ✅ Implement PSR-7 for HTTP
7. ✅ Use tools to enforce standards
8. ✅ Follow in team/projects

---

## See Also

- [Basic Coding Standard (PSR-1)](2-basic-coding-standard.md)
- [Logger Interface (PSR-3)](3-logger-interface.md)
- [Autoloading Standard (PSR-4)](4-autoloading-standard.md)
- [Caching Interface (PSR-6)](5-caching-interface.md)
- [HTTP Message Interface (PSR-7)](6-http-message-interface.md)
- [Container Interface (PSR-11)](7-container-interface.md)
