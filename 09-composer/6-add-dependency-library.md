# Adding Dependencies with Composer

## Overview

Adding dependencies is a core Composer function. You can use `composer require` to add packages to your project, specify versions, add development dependencies, and manage complex dependency trees. This chapter covers how to find, add, and manage dependencies effectively.

---

## Table of Contents

1. Finding Packages
2. Adding Dependencies
3. Version Constraints
4. Development Dependencies
5. Removing Dependencies
6. Updating Dependencies
7. Dependency Conflicts
8. Lock Files
9. Complete Examples

---

## Finding Packages

### Searching on Packagist

```bash
# Command line search
composer search logging

# Shows results:
# monolog/monolog        Sends your logs to files, sockets, mails, etc
# psr/log                Common interface for logging libraries
# symfony/console        Symfony Console Component

composer search --only-name logging
# Filter by name only
```

### Searching Online

```
Website: https://packagist.org/
- Browse packages
- View versions
- Read documentation
- Check stats
- See GitHub link
```

### Evaluating Packages

```
Check for:
- Active maintenance (recent commits)
- Community size (downloads, stars)
- Test coverage
- Documentation quality
- License compatibility
- Issue resolution time
- Dependency count (fewer is better)
```

---

## Adding Dependencies

### Basic require Command

```bash
# Add single package
composer require monolog/monolog

# Add multiple packages
composer require symfony/console symfony/var-dumper

# Add specific version
composer require monolog/monolog:^2.0

# Add from GitHub branch
composer require vendor/package:dev-main
```

### What happens

```bash
$ composer require monolog/monolog

Using version ^2.0 for monolog/monolog
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Lock file operations: 0 installs, 1 update, 0 removals

Writing lock file
Installing dependencies from lock file
Package operations: 1 install, 0 updates, 0 removals
  - Installing monolog/monolog (2.8.0)

Writing autoload files
```

### Updating composer.json

```json
// Before
{
    "require": {
        "php": ">=8.0"
    }
}

// After: composer require monolog/monolog:^2.0
{
    "require": {
        "php": ">=8.0",
        "monolog/monolog": "^2.0"
    }
}
```

---

## Version Constraints

### Constraint Types

```
^2.0       Caret (compatible release)
           >=2.0 <3.0

~2.0       Tilde (pessimistic)
           >=2.0 <3.0

>=2.0      Greater than or equal
           >=2.0

<3.0       Less than
           <3.0

2.0.0      Exact version
           Only 2.0.0

dev-main   Development branch
           Latest from main branch

*          Any version
           Latest compatible
```

### Examples

```bash
# Latest 2.x version (recommended)
composer require monolog/monolog:^2.0

# Latest 2.0.x version
composer require monolog/monolog:~2.0

# Any version
composer require monolog/monolog

# Exact version
composer require monolog/monolog:2.8.0

# Development version
composer require vendor/package:dev-main

# Or development version
composer require vendor/package:dev-develop@dev

# Multiple constraints
composer require monolog/monolog:^2.0,!=2.5.0
```

### Semantic Versioning

```
MAJOR.MINOR.PATCH

1.2.3
│ │ └─ Patch (1.2.3 → 1.2.4)
│ │    Bug fixes, no API changes
│
└─┬─── Minor (1.2.0 → 1.3.0)
  │    New features, backward compatible
  │
  └─── Major (1.0.0 → 2.0.0)
       Breaking changes
```

---

## Development Dependencies

### Adding Dev Packages

```bash
# Development packages only (not production)
composer require --dev phpunit/phpunit

# Multiple dev packages
composer require --dev phpunit/phpunit phpstan/phpstan

# Add with version
composer require --dev phpunit/phpunit:^10.0
```

### What is "dev"?

```
require         Production packages
                Needed to run application
                Installed in production

require-dev     Development packages
                Needed for development only
                Testing, linting, code generation
                NOT installed in production
```

### Production vs Development

```bash
# Install with dev dependencies (development)
composer install

# Install without dev dependencies (production)
composer install --no-dev

# Update with dev dependencies
composer update

# Update without dev dependencies
composer update --no-dev
```

### composer.json Example

```json
{
    "require": {
        "php": ">=8.0",
        "symfony/console": "^6.0",
        "monolog/monolog": "^2.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "phpstan/phpstan": "^1.0",
        "squizlabs/php_codesniffer": "^3.7"
    }
}
```

---

## Removing Dependencies

### Remove Package

```bash
# Remove production package
composer remove symfony/console

# Remove dev package
composer remove --dev phpunit/phpunit

# Remove multiple packages
composer remove vendor/pkg1 vendor/pkg2
```

### What happens

```bash
$ composer remove monolog/monolog

Removing dependencies (including require-dev)
  - Removing monolog/monolog (2.8.0)
  - Removing psr/log (1.1.4)

Writing autoload files
```

---

## Updating Dependencies

### Update All Packages

```bash
# Update all packages respecting constraints
composer update

# Update with lock file (reproducible)
composer install

# Show what would be updated
composer update --dry-run
```

### Update Specific Package

```bash
# Update single package to latest version
composer update vendor/package

# Update to specific version
composer update vendor/package:2.0.0

# Update multiple packages
composer update vendor/pkg1 vendor/pkg2
```

### Update Lock File Only

```bash
# Regenerate lock file without installing
composer lock

# Update lock file
composer update --lock
```

### Update Strategies

```bash
# Conservative: respect constraints (recommended)
composer update

# Aggressive: update to latest
composer update --with-all-dependencies

# Interactive: choose packages
composer update package1 package2

# Show changes
composer update --dry-run
```

---

## Dependency Conflicts

### Conflict Detection

```bash
$ composer require conflicting/package

Your requirements could not be resolved to an installable set of packages.

Problem 1
  - conflicting/package v1.0.0 requires another/package v2.0
  - your-app v1.0 requires another/package v1.0

To fix this you need to allow more versions of another/package
```

### Resolving Conflicts

```bash
# Option 1: Upgrade conflicting package
composer require another/package:^2.0

# Option 2: Use compatible version
composer require conflicting/package:^0.5

# Option 3: Check compatibility
composer require --with-all-dependencies conflicting/package

# Option 4: Update all dependencies
composer update
```

### Diamond Dependency Problem

```
Your App
├── Package A (requires Library v1.0)
└── Package B (requires Library v2.0)

Solution:
- Package A and B must support same Library version
- Or update one of them
- Composer will error if impossible
```

---

## Lock Files

### Understanding Lock Files

```
composer.lock stores:
- Exact versions of all packages
- Dependency tree
- Download URLs
- Checksums

Purpose:
- Reproducible installs
- Team consistency
- Deployment reliability
```

### Using Lock Files

```bash
# First install - creates lock file
composer install

# Subsequent installs - uses lock file
composer install

# Development - update as needed
composer update

# Production - use lock file exactly
composer install --no-dev
```

### Committing Lock Files

```bash
# For applications: ALWAYS commit lock file
git add composer.lock
git commit -m "Update dependencies"

# For libraries: Generally DON'T commit lock file
# (allow flexibility for library consumers)
echo "composer.lock" >> .gitignore
```

---

## Complete Examples

### Example 1: Web Application

```bash
# Create project
composer create-project symfony/skeleton my-app
cd my-app

# Add dependencies
composer require symfony/orm-pack
composer require symfony/form symfony/validator
composer require monolog/monolog

# Add dev dependencies
composer require --dev phpunit/phpunit
composer require --dev squizlabs/php_codesniffer

# View dependencies
composer show
```

### Example 2: Adding to Existing Project

```bash
# Check current dependencies
composer show

# Add logging
composer require monolog/monolog:^2.0

# Add database
composer require doctrine/orm

# Add validation
composer require symfony/validator

# Add testing
composer require --dev phpunit/phpunit:^10.0

# View updated dependencies
composer show
```

### Example 3: Development Setup

```bash
# Install with all dev tools
composer install

# Install specific dev tools
composer require --dev \
    phpunit/phpunit \
    phpstan/phpstan \
    squizlabs/php_codesniffer \
    vimeo/psalm

# Running tools
./vendor/bin/phpunit
./vendor/bin/phpstan analyse
./vendor/bin/phpcbf --standard=PSR12 src/
```

### Example 4: Production Deployment

```bash
# Install only production dependencies
composer install --no-dev --optimize-autoloader

# Results:
# - No dev packages installed
# - Autoloader optimized for production
# - Smaller vendor/ directory
# - Faster autoloading
```

---

## Key Takeaways

**Dependency Management Checklist:**

1. ✅ Search for packages on packagist.org
2. ✅ Use `composer require` to add packages
3. ✅ Understand version constraints (^, ~, etc.)
4. ✅ Use `require-dev` for development tools
5. ✅ Commit composer.lock for applications
6. ✅ Run `composer update` before deployment
7. ✅ Handle conflicts by updating versions
8. ✅ Use `--no-dev` in production
9. ✅ Review dependencies with `composer show`
10. ✅ Keep dependencies up-to-date and secure

---

## See Also

- [Composer Repositories](5-repository.md)
- [Creating Composer Projects](3-create-composer-project.md)
- [Removing Dependencies](13-remove-existing-library.md)
