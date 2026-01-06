# Advanced Composer Features

## Overview

Beyond the basics of installing and managing dependencies, Composer offers many advanced features for optimization, automation, and specialized use cases. This chapter covers scripts, plugins, performance optimization, and other powerful features.

---

## Table of Contents

1. Composer Scripts
2. Custom Commands
3. Plugins
4. Performance Optimization
5. Vendor Patching
6. Lock File Management
7. Security Auditing
8. Advanced Configuration
9. Complete Examples

---

## Composer Scripts

### What are Scripts

```
Scripts = Automation hooks

Run custom commands at specific events:
- Before/after install
- Before/after update
- Before/after autoload generation
- Custom events

Use cases:
- Run tests after install
- Generate documentation
- Compile assets
- Database migrations
- Cleanup tasks
```

### Built-in Events

```
Available events:
- pre-install-cmd
- post-install-cmd
- pre-update-cmd
- post-update-cmd
- pre-autoload-dump
- post-autoload-dump
- pre-package-install
- post-package-install
- pre-package-update
- post-package-update
- pre-package-uninstall
- post-package-uninstall
- post-package-uninstall
- pre-status-cmd
- post-status-cmd
- pre-archive-cmd
- post-archive-cmd
```

### Defining Scripts

```json
{
    "scripts": {
        "test": "phpunit",
        "lint": "phpcs",
        "fix": "phpcbf",
        "post-install-cmd": "php build/setup.php",
        "post-update-cmd": [
            "@test",
            "@lint",
            "php build/cache-clear.php"
        ]
    }
}
```

### Running Scripts

```bash
# Run custom script
composer run-script test

# Or shorthand
composer test

# Run with arguments
composer test -- --coverage

# List all scripts
composer list
```

### Script Examples

```json
{
    "scripts": {
        "test": "phpunit --coverage-text",
        "lint": "phpcs --standard=PSR12 src/",
        "fix": "phpcbf --standard=PSR12 src/",
        "analyse": "phpstan analyse src/",
        "post-install-cmd": [
            "php -r 'echo \"Installation complete!\";'"
        ],
        "post-update-cmd": [
            "@test",
            "@lint"
        ]
    }
}
```

---

## Custom Commands

### Creating Custom Commands

```bash
# Define in composer.json
{
    "scripts": {
        "migrate": "php bin/console migrate",
        "seed": "php bin/console seed",
        "deploy": [
            "@test",
            "@lint",
            "php bin/deploy.php"
        ]
    }
}

# Run custom command
composer run-script migrate
composer migrate  # shorthand
```

### Chaining Commands

```json
{
    "scripts": {
        "build": [
            "@test",
            "@lint",
            "@analyse",
            "php bin/build.php"
        ],
        "test": "phpunit",
        "lint": "phpcs",
        "analyse": "phpstan analyse"
    }
}

# Run chained commands
composer build
# Runs: phpunit, phpcs, phpstan analyse, php bin/build.php
```

---

## Plugins

### What are Composer Plugins

```
Plugins = Extend Composer functionality

Available plugins:
- Monolog (logging)
- Symfony (framework integration)
- Prestissimo (parallel installation)
- Composer Merge Plugin (merge configs)
- Composer Patches (patch packages)

Use cases:
- Modify behavior
- Add commands
- Custom installation logic
- Post-install hooks
```

### Installing Plugins

```bash
# Install plugin
composer require --dev composer/composer-script-plugin

# Plugins automatically loaded
# No additional configuration usually needed

# Find plugins on Packagist
# composer plugins
```

---

## Performance Optimization

### Optimize Autoloader

```bash
# Create optimized autoloader
composer dump-autoload --optimize

# Options:
--classmap-authoritative  # Only use classmap
--optimize               # Optimize PSR-4
--no-dev                # Exclude dev files
--apcu                  # APCu optimization
```

### Production Installation

```bash
# Install for production
composer install --no-dev --optimize-autoloader

# What this does:
# - Skips dev dependencies
# - Optimizes autoloader
# - Smaller vendor/
# - Faster autoloading
```

### Parallel Installation

```bash
# Default: serial installation (one at a time)
composer install

# Using plugin for parallel
composer require --dev prestissimo/prestissimo

# Now installations run in parallel (faster)
```

### Lock File Usage

```bash
# Use lock file (production)
composer install

# Install from lock file exactly (fastest)
# Respects composer.lock precisely

# Development: update as needed
composer update

# Regenerates composer.lock
```

---

## Vendor Patching

### Patching Packages

```
Scenario:
- Library has bug
- Author hasn't fixed
- Can't wait for release
- Need immediate fix

Solution:
- Create patch file
- Apply with composer-patches
- Continue working
- Remove when fixed upstream
```

### Using Composer Patches

```bash
# Install plugin
composer require --dev cweagans/composer-patches

# Create patch file
# patch/monolog-fix.patch

# Configure in composer.json
{
    "extra": {
        "patches": {
            "monolog/monolog": {
                "Fix critical bug": "patch/monolog-fix.patch"
            }
        }
    }
}

# Install applies patches
composer install
```

---

## Lock File Management

### Understanding composer.lock

```json
{
    "name": "my-project",
    "_readme": [
        "This file is auto-generated. Do not edit.",
        "See composer.json for your requirements."
    ],
    "packages": [
        {
            "name": "vendor/package",
            "version": "1.2.3",
            "source": {
                "type": "git",
                "url": "...",
                "reference": "abc123..."
            },
            "require": {
                "php": ">=7.4"
            }
        }
    ],
    "packages-dev": [...],
    "aliases": [],
    "minimum-stability": "stable",
    "stability-flags": {}
}
```

### Managing Lock Files

```bash
# Update lock file without installing
composer lock

# Lock specific packages
composer update vendor/pkg1 vendor/pkg2 --lock

# Refresh lock file
composer install --lock

# Show lock changes
git diff composer.lock
```

### Committing Lock Files

```
For applications:
git add composer.lock
git commit -m "Update dependencies"

Reason: Reproducible installs
        Team consistency
        Exact versions

For libraries:
Don't commit composer.lock
Reason: Allows flexibility
        Different version constraints
        Dev environment variation
```

---

## Security Auditing

### Check for Vulnerabilities

```bash
# Audit dependencies
composer audit

# Output shows:
# - Known vulnerabilities
# - CVE numbers
# - Affected versions
# - Suggested fixes

# Update to secure version
composer update vendor/package
```

### Security Best Practices

```
✓ Run composer audit regularly
✓ Update dependencies monthly
✓ Monitor security advisories
✓ Subscribe to security alerts
✓ Test updates before deploying
✓ Use composer.lock in production
✓ Regular security reviews
✓ Monitor package maintainers
✓ Use trusted packages only
```

---

## Advanced Configuration

### Composer Config

```bash
# View config
composer config --list

# Set configuration
composer config process-timeout 300
composer config github-oauth.github.com "token"

# Global config
composer config --global github-oauth.github.com "token"

# Config file
~/.config/composer/config.json
```

### Environment Variables

```bash
# Set variables
export COMPOSER_CACHE_DIR=/custom/path
export COMPOSER_AUTH='{"github-oauth": {"github.com": "token"}}'
export COMPOSER_MEMORY_LIMIT=-1
export COMPOSER_PROCESS_TIMEOUT=300

# Use in scripts
composer install
```

---

## Complete Examples

### Example 1: Project with Scripts

```json
{
    "name": "my-project",
    "type": "project",
    "scripts": {
        "test": "phpunit --coverage-text",
        "lint": "phpcs --standard=PSR12 src/ tests/",
        "fix": "phpcbf --standard=PSR12 src/ tests/",
        "analyse": "phpstan analyse src/",
        "post-install-cmd": [
            "php build/setup.php"
        ],
        "post-update-cmd": [
            "@test",
            "@lint"
        ]
    },
    "require": {
        "php": ">=8.0",
        "symfony/console": "^6.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "phpstan/phpstan": "^1.0",
        "squizlabs/php_codesniffer": "^3.7"
    }
}
```

### Example 2: Production Optimized Build

```bash
# Installation script
#!/bin/bash

# Install production dependencies only
composer install \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative

# Run migrations (if any)
php bin/console migrate:latest

# Clear caches
php bin/console cache:clear

echo "Deployment complete"
```

### Example 3: Development Setup

```bash
# Development installation
#!/bin/bash

# Install all dependencies
composer install

# Run tests
composer test

# Run linting
composer lint

# Run static analysis
composer analyse

# Setup development environment
php bin/console setup:dev

echo "Development environment ready"
```

### Example 4: Patching a Package

```json
{
    "name": "my-project",
    "require": {
        "monolog/monolog": "^2.0"
    },
    "require-dev": {
        "cweagans/composer-patches": "^1.7"
    },
    "extra": {
        "patches": {
            "monolog/monolog": {
                "Fix handler bug": "patches/monolog-handler-fix.patch",
                "Performance improvement": "patches/monolog-performance.patch"
            }
        }
    }
}
```

---

## Key Takeaways

**Advanced Features Checklist:**

1. ✅ Use scripts for automation
2. ✅ Define test and lint scripts
3. ✅ Use post-install-cmd for setup
4. ✅ Optimize autoloader for production
5. ✅ Install with --no-dev in production
6. ✅ Run composer audit regularly
7. ✅ Use composer.lock for reproducibility
8. ✅ Consider plugins for enhanced features
9. ✅ Use patches for temporary fixes
10. ✅ Monitor security advisories

---

## See Also

- [Adding Dependencies](6-add-dependency-library.md)
- [Creating Composer Projects](3-create-composer-project.md)
- [Installing Composer](2-install-composer.md)
