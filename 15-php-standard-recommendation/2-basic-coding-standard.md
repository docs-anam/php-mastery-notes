# PSR-1: Basic Coding Standard

## Overview

Learn about PSR-1, which establishes fundamental coding standards for PHP to ensure consistency and interoperability across projects.

---

## Table of Contents

1. Overview of PSR-1
2. Files and Encoding
3. Naming Conventions
4. Class Structure
5. Namespace and Use Declarations
6. Constants
7. PSR-1 in Practice
8. Tools and Enforcement
9. Complete Examples

---

## Overview of PSR-1

### Purpose

```php
<?php
// Without standards: Chaotic code

// Style 1
class UserService{public function getUser($id){return null;}}

// Style 2
class user_service {
    public function get_user($id) {
        return NULL;
    }
}

// Style 3
class USERSERVICE {
    public function GetUser($id) {
        return null;
    }
}

// Problem: Inconsistent, hard to read, not interoperable

// PSR-1 Solution: Standardized format

class UserService
{
    public function getUser($id)
    {
        return null;
    }
}
```

### Benefits

```
✓ Readability - Consistent format
✓ Interoperability - Works with frameworks
✓ Maintainability - Easier to understand
✓ Professional - Industry standard
✓ Tool support - IDEs and tools recognize it
✓ Team alignment - Everyone follows same rules
```

---

## Files and Encoding

### File Format

```php
<?php
// 1. PHP files MUST use UTF-8 encoding (no BOM)
// 2. PHP files MUST end with a single newline
// 3. PHP only files omit closing ?>

// Correct:
<?php
class MyClass {}
// File ends with newline here
(single newline at EOF)

// Incorrect:
<?php
class MyClass {}
?>

// Incorrect:
<?php
class MyClass {}
(no newline at EOF)
```

### Open Tag

```php
<?php
// Correct: Short tag
echo "Hello";

// Also correct in PHP 5.4+
// Short echo tag
<?= "Hello" ?>

// Incorrect: Short tag (if not enabled)
<? echo "Hello"; ?>

// Incorrect: ASP style
<% echo "Hello"; %>
```

### Encoding Declaration

```php
<?php
// No explicit encoding needed (UTF-8 default)
// File should be saved as UTF-8 without BOM

// Text editors: Set encoding to UTF-8
// IDE: Configure project encoding
// Git: Track encoding in .editorconfig

[*.php]
charset = utf-8
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true
```

---

## Naming Conventions

### Classes and Constants

```php
<?php
// Classes: PascalCase (UpperCamelCase)

class UserRepository { }      // ✓ Correct
class User { }                // ✓ Correct
class HTTPHandler { }         // ✓ Correct (all caps acceptable for acronyms)

class user_repository { }     // ✗ Wrong
class userRepository { }      // ✗ Wrong
class USER_REPOSITORY { }     // ✗ Wrong

// Class-level constants: ALL_CAPS with underscores

class UserService
{
    const STATUS_ACTIVE = 1;      // ✓ Correct
    const STATUS_INACTIVE = 0;    // ✓ Correct
    const MAX_RETRIES = 3;        // ✓ Correct

    const statusActive = 1;       // ✗ Wrong
    const status_active = 1;      // ✗ Wrong (lowercase)
}
```

### Properties and Methods

```php
<?php
class UserService
{
    // Properties: camelCase
    private $userId;              // ✓ Correct
    protected $firstName;         // ✓ Correct
    public $email;                // ✓ Correct

    private $user_id;             // ✗ Wrong
    private $UserId;              // ✗ Wrong

    // Methods: camelCase
    public function getUserId() { }      // ✓ Correct
    public function setEmail() { }       // ✓ Correct
    private function validateUser() { }  // ✓ Correct

    public function get_user_id() { }    // ✗ Wrong
    public function GetUserId() { }      // ✗ Wrong
}
```

---

## Class Structure

### File Organization

```php
<?php
// One class per file (best practice, not strictly required)
// File named same as class

// File: UserService.php
class UserService
{
    // Implementation
}

// Properties, then methods
class User
{
    // Properties first
    private $id;
    private $name;

    // Constructor
    public function __construct() { }

    // Public methods
    public function getName() { }

    // Protected methods
    protected function validate() { }

    // Private methods
    private function hash() { }
}
```

### Visibility

```php
<?php
// PSR-1 requires visibility declaration

class Correct
{
    public $public;        // ✓ Public
    protected $protected;  // ✓ Protected
    private $private;      // ✓ Private

    public function method() { }      // ✓ Public
    protected function helper() { }   // ✓ Protected
    private function internal() { }   // ✓ Private
}

class Incorrect
{
    var $property;            // ✗ Wrong (no visibility)
    function method() { }     // ✗ Wrong (no visibility)
}
```

---

## Namespace and Use Declarations

### Namespace Declaration

```php
<?php
// File: src/Services/UserService.php

// Correct: Namespace must come first
namespace App\Services;

use Psr\Log\LoggerInterface;

class UserService
{
    // Implementation
}

// Incorrect order:
echo "Code";

namespace App\Services;  // ✗ Namespace must be first
class UserService { }
```

### Use Declarations

```php
<?php
// Correct: Use comes after namespace

namespace App\Services;

use Psr\Log\LoggerInterface;
use Psr\Cache\CacheItemPoolInterface;
use App\Models\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(
        private LoggerInterface $logger,
        private CacheItemPoolInterface $cache,
    ) {}
}

// Incorrect: Use before namespace
use Psr\Log\LoggerInterface;

namespace App\Services;  // ✗ Wrong order
```

---

## Constants

### Naming

```php
<?php
// Class constants: ALL_CAPS with underscores

class DatabaseConfig
{
    const DEFAULT_HOST = 'localhost';          // ✓ Correct
    const DEFAULT_PORT = 5432;                 // ✓ Correct
    const MAX_CONNECTIONS = 100;               // ✓ Correct
    const RETRY_ATTEMPTS = 3;                  // ✓ Correct

    const defaultHost = 'localhost';           // ✗ Wrong
    const default_host = 'localhost';          // ✗ Wrong
}

// Const vs define()
const ALLOWED = true;           // ✓ Preferred (PSR-1 era)
define('ALLOWED', true);        // OK, but const preferred
```

### Visibility in Constants

```php
<?php
// PHP 7.1+: Constants can have visibility

class Config
{
    public const PUBLIC_KEY = 'value';         // ✓ Public
    protected const PROTECTED_KEY = 'value';   // ✓ Protected
    private const PRIVATE_KEY = 'value';       // ✓ Private
}

// Visibility required in modern code
class OldStyle
{
    const VALUE = 'no visibility';  // Still valid
}
```

---

## PSR-1 in Practice

### Real-world Example

```php
<?php
// File: src/User/UserRepository.php

declare(strict_types=1);

namespace App\User;

use Psr\Log\LoggerInterface;

class UserRepository
{
    private LoggerInterface $logger;
    private $database;

    const CACHE_KEY = 'users';
    const CACHE_TTL = 3600;

    public function __construct(
        LoggerInterface $logger,
        $database
    ) {
        $this->logger = $logger;
        $this->database = $database;
    }

    public function findById(int $id): ?array
    {
        $this->logger->info('Finding user', ['id' => $id]);
        
        // Implementation
        return [];
    }

    private function validateId(int $id): bool
    {
        return $id > 0;
    }
}
```

### Multi-class File (discouraged but allowed)

```php
<?php
// src/Utils.php

// Multiple classes in one file (not recommended)
class StringUtils
{
    public function format() { }
}

class ArrayUtils
{
    public function filter() { }
}

// Better: Separate files
// src/Utils/StringUtils.php
// src/Utils/ArrayUtils.php
```

---

## Tools and Enforcement

### PHP-CodeSniffer

```bash
# Install
composer require --dev squizlabs/php_codesniffer

# Check PSR-1
vendor/bin/phpcs --standard=PSR1 src/

# Check with report
vendor/bin/phpcs --standard=PSR1 --report=full src/

# Fix automatically (limited)
vendor/bin/phpcbf --standard=PSR1 src/
```

### PHP-CS-Fixer

```bash
# Install
composer require --dev friendsofphp/php-cs-fixer

# Apply rules
vendor/bin/php-cs-fixer fix src/ --rules=@PSR1

# Check without applying
vendor/bin/php-cs-fixer fix src/ --rules=@PSR1 --dry-run
```

### IDE Configuration

```json
// .editorconfig
root = true

[*.php]
charset = utf-8
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true
indent_style = space
indent_size = 4

[*.{json,yml}]
indent_size = 2
```

---

## Complete Examples

### Example 1: Compliant Project Structure

```
project/
├── src/
│   ├── User/
│   │   ├── User.php
│   │   ├── UserService.php
│   │   └── UserRepository.php
│   ├── Article/
│   │   ├── Article.php
│   │   └── ArticleService.php
│   └── Common/
│       └── BaseService.php
├── tests/
│   ├── Unit/
│   │   └── UserServiceTest.php
│   └── Feature/
│       └── UserApiTest.php
└── composer.json
```

### Example 2: Compliant Class

```php
<?php
// File: src/User/User.php

declare(strict_types=1);

namespace App\User;

class User
{
    private int $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private int $status;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public function __construct(
        int $id,
        string $email,
        string $firstName,
        string $lastName
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->status = self::STATUS_ACTIVE;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    private function validate(): bool
    {
        return !empty($this->email) && filter_var(
            $this->email,
            FILTER_VALIDATE_EMAIL
        );
    }
}
```

### Example 3: Compliant Service

```php
<?php
// File: src/User/UserService.php

declare(strict_types=1);

namespace App\User;

use Psr\Log\LoggerInterface;

class UserService
{
    private LoggerInterface $logger;
    private UserRepository $repository;

    const DEFAULT_PAGE_SIZE = 20;
    const MAX_PAGE_SIZE = 100;

    public function __construct(
        LoggerInterface $logger,
        UserRepository $repository
    ) {
        $this->logger = $logger;
        $this->repository = repository;
    }

    public function getAllUsers(
        int $page = 1,
        int $pageSize = self::DEFAULT_PAGE_SIZE
    ): array {
        $this->validatePageSize($pageSize);

        $this->logger->info(
            'Fetching users',
            ['page' => $page, 'size' => $pageSize]
        );

        return $this->repository->findAll($page, $pageSize);
    }

    public function createUser(
        string $email,
        string $firstName,
        string $lastName
    ): User {
        $this->validateEmail($email);

        $user = new User(
            id: 0,
            email: $email,
            firstName: $firstName,
            lastName: $lastName
        );

        $this->repository->save($user);

        $this->logger->info('User created', ['email' => $email]);

        return $user;
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
    }

    private function validatePageSize(int $pageSize): void
    {
        if ($pageSize > self::MAX_PAGE_SIZE) {
            throw new InvalidArgumentException('Page size too large');
        }
    }
}
```

---

## Key Takeaways

**PSR-1 Compliance Checklist:**

1. ✅ UTF-8 encoding without BOM
2. ✅ Single newline at EOF
3. ✅ Close tag omitted for PHP-only files
4. ✅ Classes in PascalCase
5. ✅ Methods/properties in camelCase
6. ✅ Constants in UPPER_SNAKE_CASE
7. ✅ Explicit visibility declarations
8. ✅ One class per file (recommended)
9. ✅ Namespace before use declarations
10. ✅ Use code quality tools

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Extended Coding Style Guide (PSR-12)](8-extended-coding-style-guide.md)
- [Autoloading Standard (PSR-4)](4-autoloading-standard.md)
