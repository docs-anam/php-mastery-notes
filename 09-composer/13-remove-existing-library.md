# Removing Existing Dependencies

## Overview

Managing dependencies includes not just adding them, but also removing them when they're no longer needed. This chapter covers how to remove packages, clean up your project, and avoid common issues when removing dependencies.

---

## Table of Contents

1. Why Remove Dependencies
2. Removing Packages
3. Removing Development Dependencies
4. Verifying Removal
5. Cleanup After Removal
6. Handling Conflicts
7. Dependency Chain
8. Best Practices
9. Complete Examples

---

## Why Remove Dependencies

### Valid Reasons to Remove

```
1. Package no longer needed
   - Feature removed
   - Requirements changed
   - No longer using library

2. Replaced by alternative
   - Better library found
   - Feature integrated elsewhere
   - Performance improvement

3. Deprecated package
   - Library unmaintained
   - Security concerns
   - Better alternative exists

4. Reducing dependencies
   - Fewer dependencies = simpler project
   - Reduced security surface
   - Fewer conflicts
   - Smaller vendor/ directory

5. Consolidation
   - Merge multiple libraries
   - Reduce complexity
   - Improve performance
```

### Invalid Reasons to Remove

```
✗ Don't remove just because:
- Dislike author
- License concerns (use legal advice)
- You're switching languages
- You haven't used it recently
- It's trendy to remove it

Consider:
- Are other packages depending on it?
- Will users need it?
- What's the impact?
```

---

## Removing Packages

### Remove from composer.json

```bash
# Remove production package
composer remove vendor/package

# Remove multiple packages
composer remove vendor/pkg1 vendor/pkg2 vendor/pkg3

# What happens:
# 1. Removes from composer.json
# 2. Updates composer.lock
# 3. Removes from vendor/
# 4. Updates autoloader
```

### Manual Removal Steps

```bash
# Step 1: Edit composer.json (remove require)
{
    "require": {
        "php": ">=8.0",
        "symfony/console": "^6.0"
        // monolog/monolog removed
    }
}

# Step 2: Update dependencies
composer update

# Step 3: Verify removal
composer show

# Step 4: Check for breaks
./vendor/bin/phpunit
```

### Visual Example

```json
// Before removal
{
    "require": {
        "php": ">=8.0",
        "monolog/monolog": "^2.0",
        "symfony/console": "^6.0",
        "doctrine/orm": "^2.0"
    }
}

// After: composer remove monolog/monolog
{
    "require": {
        "php": ">=8.0",
        "symfony/console": "^6.0",
        "doctrine/orm": "^2.0"
    }
}
```

---## Removing Development Dependencies

### Remove Dev Package

```bash
# Remove development package
composer remove --dev phpunit/phpunit

# Remove multiple dev packages
composer remove --dev phpunit/phpunit phpstan/phpstan

# Remove production package
composer remove vendor/package

# Note: Different packages may be in require-dev
```

### Difference: require vs require-dev

```json
{
    "require": {
        "php": ">=8.0",
        "monolog/monolog": "^2.0"  // Production
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",  // Development
        "phpstan/phpstan": "^1.0"    // Development
    }
}

// composer remove monolog/monolog
// Removes from require

// composer remove --dev phpunit/phpunit
// Removes from require-dev
```

### Production vs Development

```
require (Production):
- Used in production
- Must be installed
- composer install --no-dev skips others
- User gets it
- Required for app to work

require-dev (Development):
- Only for development
- Testing, linting, etc.
- composer install --no-dev excludes these
- User doesn't get it
- Not needed for app to work
```

---

## Verifying Removal

### Check Package List

```bash
# Show all packages
composer show

# Show only production packages
composer show --no-dev

# Show only development packages
composer show --dev

# Search for specific package
composer show | grep monolog

# Check if package still there (shouldn't be)
composer show vendor/package
# Returns: package not found (expected)
```

### Check Project Files

```bash
# Search for use of package
grep -r "monolog" src/
grep -r "use Monolog" src/
grep -r "new Monolog" src/

# Search in tests
grep -r "use Monolog" tests/

# If found, code still depends on it
# Need to refactor code before removing
```

### Verify No Broken Code

```bash
# Run tests
./vendor/bin/phpunit

# Run linter/static analysis
./vendor/bin/phpstan analyse

# Run code sniffer
./vendor/bin/phpcs

# Manual testing
php -l src/*.php
```

---

## Cleanup After Removal

### Unused Code Cleanup

```php
// Before removal of monolog/monolog
<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Application {
    private $logger;
    
    public function __construct() {
        $this->logger = new Logger('app');
        $this->logger->pushHandler(new StreamHandler('app.log'));
    }
    
    public function run() {
        $this->logger->info('Application started');
        // ...
    }
}

// After removal - cleanup code
<?php

class Application {
    public function run() {
        // Remove logging if it's the only use
        // Or implement alternative logging
    }
}
```

### Update Autoloader

```bash
# After removing packages, regenerate autoloader
composer dump-autoload

# Or automatically done with:
composer remove vendor/package

# Verify autoloader
composer dump-autoload --optimize
```

### Commit Changes

```bash
# After removal and verification
git add composer.json composer.lock

git commit -m "Remove monolog/monolog - replaced by built-in logging"

# Document removal
# In CHANGELOG.md add:
# - Removed monolog/monolog dependency (replaced by native logging)
```

---

## Handling Conflicts

### Dependency Chains

```
Your Project
├── symfony/console v6.0
│   └── (requires) symfony/process v6.0
│
├── laravel/framework v10.0
│   └── (requires) symfony/console v^6.0
│
└── (no direct use of symfony/process)

Remove: symfony/console?
Issue: laravel/framework needs it

Solution: Don't remove
         Let package manager handle dependencies
         Or remove laravel/framework too
```

### Finding Dependent Packages

```bash
# Show why a package is installed
composer why vendor/package

# Example output:
# laravel/framework v10.0 requires symfony/process (^6.0)
# symfony/console v6.0 requires symfony/process (~6.0)

# This package is required by:
# - laravel/framework
# - symfony/console
```

### Removing When Dependencies Exist

```bash
# Try to remove
composer remove vendor/package

# If error about dependencies:
# "symfony/process is required by laravel/framework"

# Options:
# 1. Remove dependent package first
composer remove laravel/framework

# 2. Use --with-all-dependencies
composer remove vendor/package --with-all-dependencies

# 3. Update dependent to not need it
composer update --with-all-dependencies
```

---

## Dependency Chain

### Understanding Dependency Trees

```bash
# Show dependency tree
composer show --tree

# Example:
# symfony/console v6.4.0 (v6.4.0)
# ├── symfony/deprecation-contracts ^2.1
# ├── symfony/polyfill-intl-grapheme ^1.0
# ├── symfony/polyfill-intl-normalizer ^1.0
# ├── symfony/polyfill-mbstring ^1.0
# └── symfony/process ^6.4

# symfony/var-dumper v6.4.0
# ├── symfony/console ^5.4|^6.0
# └── ...
```

### Removing from Dependency Chain

```bash
# Remove intermediate package
composer remove symfony/process

# If other packages need it:
Error: symfony/console v6.4.0 requires symfony/process (^6.4)

# Must remove symfony/console first (or update it)
composer remove symfony/console symfony/process

# Then reinstall if needed
composer require symfony/console:^6.0
```

---

## Best Practices

### Before Removing

```
Checklist:
1. ✓ Confirm package not needed
2. ✓ Search codebase for usage
3. ✓ Check dependent packages
4. ✓ Run tests to ensure no breaks
5. ✓ Have plan for replacement (if needed)
6. ✓ Document reason for removal
```

### Removal Process

```
1. Identify package to remove
   composer show

2. Search codebase for usage
   grep -r "vendor/package" src/

3. Remove all usages (refactor code)
   Update/remove code that uses package

4. Run tests
   ./vendor/bin/phpunit

5. Remove package
   composer remove vendor/package

6. Verify removal
   composer show
   Tests should pass

7. Commit
   git commit -m "Remove package reason"
```

### After Removal

```
1. Run tests again
   ./vendor/bin/phpunit

2. Run code analysis
   ./vendor/bin/phpstan analyse

3. Check code sniffer
   ./vendor/bin/phpcs

4. Check autoloader
   composer dump-autoload

5. Update documentation
   Update README if needed

6. Update CHANGELOG
   Document removal

7. Commit changes
```

---

## Complete Examples

### Example 1: Simple Removal

```bash
# Project uses monolog but switches to custom logging

# Step 1: Check usage
grep -r "Monolog" src/
# Found in: src/Logger.php

# Step 2: Refactor code
# src/Logger.php - replace Monolog\Logger with custom Logger

# Step 3: Remove from composer.json
composer remove monolog/monolog

# Step 4: Verify
composer show
./vendor/bin/phpunit

# Step 5: Commit
git add -A
git commit -m "Remove monolog/monolog - use custom logger"
```

### Example 2: Removing Dev Dependency

```bash
# No longer need specific test framework

# Step 1: Identify
composer show --dev
# Found: phpspec/phpspec

# Step 2: Check usage
grep -r "phpspec" .
# Only in tests/

# Step 3: Migrate tests to PHPUnit
# Convert spec files to test files

# Step 4: Remove
composer remove --dev phpspec/phpspec

# Step 5: Test
./vendor/bin/phpunit

# Step 6: Commit
git add -A
git commit -m "Remove phpspec - migrated to PHPUnit"
```

### Example 3: Handling Dependency Conflicts

```bash
# Want to remove old logger (woodling/logger)
# But doctrine/orm depends on it

# Step 1: Check dependency
composer why woodling/logger
# doctrine/orm v2.15 requires woodling/logger (^1.0)

# Step 2: Update doctrine/orm
composer update doctrine/orm

# (New version might not need woodling/logger)

# Step 3: Try removal again
composer remove woodling/logger

# Success! Removed

# Step 4: Run tests
./vendor/bin/phpunit

# Step 5: Commit
git add -A
git commit -m "Remove woodling/logger (doctrine/orm updated)"
```

---

## Key Takeaways

**Package Removal Checklist:**

1. ✅ Confirm package is not needed
2. ✅ Search codebase for usage
3. ✅ Check dependent packages
4. ✅ Remove code that uses package
5. ✅ Run tests to verify
6. ✅ Use `composer remove vendor/package`
7. ✅ Verify with `composer show`
8. ✅ Update documentation
9. ✅ Commit changes with clear message
10. ✅ Monitor for issues after removal

---

## See Also

- [Adding Dependencies](6-add-dependency-library.md)
- [Composer Repositories](5-repository.md)
- [Creating Composer Projects](3-create-composer-project.md)
