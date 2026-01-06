# Creating Your Own Library

## Overview

Creating a reusable PHP library allows you to share code across projects and with the community. This chapter covers structuring a library, writing code for reusability, configuring composer.json, testing, and preparing for distribution.

---

## Table of Contents

1. Library vs Application
2. Planning Your Library
3. Directory Structure
4. Creating composer.json
5. Writing Reusable Code
6. Namespacing
7. Testing Your Library
8. Documentation
9. Complete Examples

---

## Library vs Application

### Differences

```
Application                  Library
- End product               - Reusable component
- Has entry point           - No entry point
- Uses libraries            - Used by applications
- Database/configuration    - No configuration
- User interface            - Pure code
- Deployment               - Distribution

Example App:               Example Library:
- Blog system              - Logging library
- CMS                      - Data validation
- Web server               - HTTP client
```

### Library Considerations

```
Reusability
- Works in any context
- No external dependencies preferred
- Configurable

Stability
- Semantic versioning
- BC (backward compatibility) important
- Careful breaking changes

Quality
- Comprehensive tests
- Full documentation
- Code standards
```

---

## Planning Your Library

### Define Scope

```
Good Library:              Bad Library:
- Single responsibility    - Too many features
- Clear purpose            - Does everything
- Focused API              - Confusing interface
- Easy to learn            - Complex setup

Example:
✓ Slug generator
✓ Email validator
✓ Rate limiter
✓ Cache adapter

✗ Complete framework
✗ Everything in one package
✗ Tightly coupled
```

### Naming Your Library

```
Format: vendor/package

Vendor     = Company or personal identifier
Package    = Lower-case, hyphenated

Examples:
- monolog/monolog          → Monolog logging library
- symfony/console          → Symfony console component
- laravel/framework        → Laravel framework
- yourcompany/slug-builder → Your slug builder

Avoid:
- Generic names (lib, utils)
- Names already taken (check packagist.org)
- Names with underscores (use hyphens)
```

### Research Competitors

```
Before creating, check:
1. Does library already exist?
2. Is existing maintained?
3. Can you improve it?
4. Is there need for alternative?

Tools:
- packagist.org
- GitHub search
- Composer search
```

---

## Directory Structure

### Standard Library Layout

```
my-library/
├── src/                        # Source code
│   └── MyLibrary.php
│   └── Service/
│       └── Calculator.php
│   └── Model/
│       └── Data.php
├── tests/                      # Tests
│   ├── Unit/
│   │   └── CalculatorTest.php
│   └── bootstrap.php
├── docs/                       # Documentation
│   └── index.md
├── examples/                   # Usage examples
│   └── basic-usage.php
├── .github/
│   └── workflows/
│       └── tests.yml           # CI/CD
├── vendor/                     # Dependencies (auto)
├── .gitignore
├── composer.json
├── composer.lock
├── phpunit.xml                 # Test config
├── README.md
├── LICENSE                     # MIT or other
└── CHANGELOG.md
```

### Minimal Library

```
my-library/
├── src/
│   └── Calculator.php
├── tests/
│   └── CalculatorTest.php
├── composer.json
├── README.md
└── LICENSE
```

---

## Creating composer.json

### Library composer.json

```json
{
    "name": "mycompany/my-library",
    "description": "A reusable PHP library for calculating things",
    "type": "library",
    "license": "MIT",
    "authors": [
        {
            "name": "Your Name",
            "email": "you@example.com",
            "role": "Developer"
        }
    ],
    "require": {
        "php": ">=8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "MyCompany\\MyLibrary\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "MyCompany\\MyLibrary\\Tests\\": "tests/"
        }
    },
    "keywords": ["calculation", "math", "utilities"],
    "homepage": "https://github.com/mycompany/my-library",
    "support": {
        "issues": "https://github.com/mycompany/my-library/issues",
        "source": "https://github.com/mycompany/my-library"
    }
}
```

### Key Properties

```json
{
    "name": "vendor/package",
    // Unique identifier (vendor/lowercase-name)
    
    "type": "library",
    // Must be "library" for reusable package
    
    "license": "MIT",
    // License for distribution
    
    "require": {
        "php": ">=8.0"
    },
    // Minimum dependencies
    
    "autoload": {
        "psr-4": {
            "MyCompany\\MyLibrary\\": "src/"
        }
    }
    // Namespace and directory mapping
}
```

---

## Writing Reusable Code

### No Global State

```php
// ✓ GOOD - Reusable
class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
}

$calc = new Calculator();
$result = $calc->add(5, 3);

// ✗ BAD - Not reusable (global state)
class Calculator {
    private static $result = 0;
    
    public static function add($a, $b) {
        self::$result = $a + $b;
        return self::$result;
    }
}
```

### Dependency Injection

```php
// ✓ GOOD - Dependencies injected
class Logger {
    private $handler;
    
    public function __construct(Handler $handler) {
        $this->handler = $handler;
    }
    
    public function log($message) {
        $this->handler->handle($message);
    }
}

$handler = new FileHandler('app.log');
$logger = new Logger($handler);
$logger->log('Application started');

// ✗ BAD - Hardcoded dependencies
class Logger {
    public function log($message) {
        $file = new FileHandler('app.log');
        $file->handle($message);
    }
}
```

### Configuration Over Code

```php
// ✓ GOOD - Configurable
class HttpClient {
    private $timeout;
    private $baseUrl;
    
    public function __construct($timeout = 30, $baseUrl = '') {
        $this->timeout = $timeout;
        $this->baseUrl = $baseUrl;
    }
    
    public function get($path) {
        // Use $this->timeout and $this->baseUrl
    }
}

$client = new HttpClient(60, 'https://api.example.com');

// ✗ BAD - Hardcoded configuration
class HttpClient {
    private $timeout = 30;
    private $baseUrl = 'https://api.example.com';
    
    public function get($path) {
        // Uses hardcoded values
    }
}
```

### Clear Interfaces

```php
// ✓ GOOD - Clear public interface
class Validator {
    /**
     * Validate an email address
     * @param string $email Email to validate
     * @return bool True if valid, false otherwise
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate a phone number
     * @param string $phone Phone number
     * @return bool True if valid
     */
    public function validatePhone($phone) {
        return preg_match('/^\+?[0-9\s\-()]+$/', $phone) > 0;
    }
}

// ✗ BAD - Unclear interface
class Validator {
    public function validate($thing, $type = 'email') {
        if ($type === 'email') {
            // email validation
        } elseif ($type === 'phone') {
            // phone validation
        }
        // etc
    }
}
```

---

## Namespacing

### Proper Namespace Structure

```php
// src/Calculator.php
<?php

namespace MyCompany\MyLibrary;

class Calculator {
    public function add($a, $b) {
        return $a + $b;
    }
}

// src/Service/EmailValidator.php
<?php

namespace MyCompany\MyLibrary\Service;

class EmailValidator {
    public function validate($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

// Usage in application
<?php

require 'vendor/autoload.php';

use MyCompany\MyLibrary\Calculator;
use MyCompany\MyLibrary\Service\EmailValidator;

$calc = new Calculator();
$validator = new EmailValidator();

echo $calc->add(5, 3);
$validator->validate('test@example.com');
```

### Namespace Conventions

```
MyCompany\             // Your company or vendor
    MyLibrary\         // Library name
        Service\       // Category/type
            Service.php  // Class name

Mapping in composer.json:
"MyCompany\\MyLibrary\\" => "src/"
```

---

## Testing Your Library

### Basic Test Structure

```bash
# Create test directory
mkdir -p tests/Unit tests/Feature

# Create bootstrap
cat > tests/bootstrap.php << 'EOF'
<?php

require __DIR__ . '/../vendor/autoload.php';
EOF

# Create test
cat > tests/Unit/CalculatorTest.php << 'EOF'
<?php

namespace MyCompany\MyLibrary\Tests\Unit;

use MyCompany\MyLibrary\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase {
    public function testAddition() {
        $calc = new Calculator();
        $result = $calc->add(5, 3);
        $this->assertEquals(8, $result);
    }
}
EOF
```

### Running Tests

```bash
# Install PHPUnit
composer require --dev phpunit/phpunit

# Run tests
./vendor/bin/phpunit

# With coverage
./vendor/bin/phpunit --coverage-html coverage/

# Specific test
./vendor/bin/phpunit tests/Unit/CalculatorTest.php
```

### Test Coverage

```bash
# Generate coverage report
./vendor/bin/phpunit --coverage-text

# HTML coverage report
./vendor/bin/phpunit --coverage-html build/coverage

# Minimum coverage (CI/CD)
./vendor/bin/phpunit --coverage-clover coverage.xml
```

---

## Documentation

### README.md

```markdown
# My Library

Description of what the library does.

## Installation

composer require vendor/package

## Usage

Basic usage examples

### Example 1: Simple Usage

Code example

### Example 2: Advanced Usage

More examples

## Configuration

How to configure the library

## API Reference

Method documentation

## Testing

How to run tests

## License

License info
```

### Inline Documentation

```php
/**
 * Calculate the sum of two numbers
 *
 * @param int|float $a First number
 * @param int|float $b Second number
 * 
 * @return int|float The sum
 * 
 * @throws InvalidArgumentException If arguments are not numeric
 * 
 * @example
 * $calc = new Calculator();
 * echo $calc->add(5, 3); // 8
 */
public function add($a, $b) {
    if (!is_numeric($a) || !is_numeric($b)) {
        throw new \InvalidArgumentException('Arguments must be numeric');
    }
    return $a + $b;
}
```

---

## Complete Examples

### Example: Simple Slug Library

```
slug-builder/
├── src/
│   └── SlugBuilder.php
├── tests/
│   ├── SlugBuilderTest.php
│   └── bootstrap.php
├── composer.json
├── README.md
└── LICENSE
```

```json
{
    "name": "mycompany/slug-builder",
    "description": "Simple URL slug generator",
    "type": "library",
    "license": "MIT",
    "authors": [
        {
            "name": "Your Name",
            "email": "you@example.com"
        }
    ],
    "require": {
        "php": ">=8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "MyCompany\\SlugBuilder\\": "src/"
        }
    }
}
```

```php
<?php
// src/SlugBuilder.php

namespace MyCompany\SlugBuilder;

class SlugBuilder {
    public function build($text, $separator = '-') {
        // Convert to lowercase
        $slug = strtolower($text);
        
        // Remove special characters
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Replace spaces with separator
        $slug = preg_replace('/\s+/', $separator, trim($slug));
        
        return $slug;
    }
}
```

### Example: Multi-class Library

```php
<?php
// src/Validation/EmailValidator.php
namespace MyCompany\Validators\Validation;

class EmailValidator {
    public function validate($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

// src/Validation/PhoneValidator.php
namespace MyCompany\Validators\Validation;

class PhoneValidator {
    public function validate($phone) {
        return preg_match('/^\+?[0-9\s\-()]+$/', $phone) > 0;
    }
}

// src/Validator.php
namespace MyCompany\Validators;

use MyCompany\Validators\Validation\EmailValidator;
use MyCompany\Validators\Validation\PhoneValidator;

class Validator {
    private $emailValidator;
    private $phoneValidator;
    
    public function __construct() {
        $this->emailValidator = new EmailValidator();
        $this->phoneValidator = new PhoneValidator();
    }
    
    public function email($email) {
        return $this->emailValidator->validate($email);
    }
    
    public function phone($phone) {
        return $this->phoneValidator->validate($phone);
    }
}
```

---

## Key Takeaways

**Library Creation Checklist:**

1. ✅ Plan scope (single responsibility)
2. ✅ Choose vendor and package name
3. ✅ Create proper directory structure
4. ✅ Write composer.json with metadata
5. ✅ Use PSR-4 namespacing
6. ✅ Implement dependency injection
7. ✅ Write comprehensive tests
8. ✅ Document with README and docblocks
9. ✅ Test locally before publishing
10. ✅ Prepare for version 1.0 release

---

## See Also

- [Uploading to GitHub](8-upload-own-library-to-github.md)
- [Submitting to Packagist](11-submit-own-library-to-packagist.md)
- [Creating Composer Projects](3-create-composer-project.md)
