# Composer - PHP Dependency Management & Package Manager

## Table of Contents
1. [Overview](#overview)
2. [What is Composer?](#what-is-composer)
3. [Installation](#installation)
4. [Core Concepts](#core-concepts)
5. [Getting Started](#getting-started)
6. [Dependency Management](#dependency-management)
7. [Creating Packages](#creating-packages)
8. [Learning Path](#learning-path)
9. [Best Practices](#best-practices)

---

## Overview

Composer is the standard **dependency manager** for PHP. It allows you to:
- Install and manage third-party libraries
- Organize project dependencies
- Ensure version compatibility
- Autoload classes automatically
- Share your own packages

### Why Composer?

**Without Composer:**
```
Download library → Extract → Copy to project → Manual updates → Broken dependencies
```

**With Composer:**
```
composer require vendor/package → Automatic installation & updates → Version control
```

## What is Composer?

### Dependency Manager

Composer resolves dependencies and their dependencies:

```
Your Project
    └── library-a (v2.0)
            └── library-b (v1.5)
                  └── library-c (v3.0)
    └── library-d (v1.0)
            └── library-c (v3.0)  ← Same version!
```

Composer ensures all libraries get the right version of their dependencies.

### Package Manager

Composer installs packages from **Packagist** (central repository):

```
Packagist.org (500,000+ packages)
    ↓
composer.json (Define requirements)
    ↓
composer.lock (Lock specific versions)
    ↓
vendor/ folder (Installed packages)
```

## Installation

### Install Composer

**macOS:**
```bash
brew install composer

# Or download and run installer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**Linux:**
```bash
sudo apt-get install composer

# Or download
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**Windows:**
- Download installer from [getcomposer.org](https://getcomposer.org)
- Run installer (adds to PATH automatically)

### Verify Installation

```bash
composer --version
# Composer version 2.x.x
```

## Core Concepts

### composer.json

Configuration file defining your project and dependencies:

```json
{
    "name": "mycompany/myproject",
    "description": "My awesome PHP project",
    "type": "library",
    "require": {
        "php": ">=8.0",
        "laravel/framework": "^10.0",
        "symfony/http-client": "^6.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "squizlabs/php_codesniffer": "^3.7"
    },
    "autoload": {
        "psr-4": {
            "MyApp\\": "src/"
        }
    }
}
```

**Sections:**
- `name`: Package name
- `require`: Production dependencies
- `require-dev`: Development-only dependencies
- `autoload`: Class loading configuration

### composer.lock

Lock file ensuring reproducible installations:

```
Generated automatically when you run composer update
Contains exact versions of installed packages
Commit to version control
Ensures team uses same versions
```

**Why important:**
- Dev machine installs version 1.0
- Lock file records version 1.0
- Production installs version 1.0 (same!)
- Prevents "works on my machine" issues

### Semantic Versioning

Version format: `MAJOR.MINOR.PATCH`

```
1.2.3
│ │ │
│ │ └── PATCH: Bug fixes
│ └──── MINOR: New features, backward compatible
└────── MAJOR: Breaking changes
```

### Version Constraints

```bash
composer require vendor/package:1.0.0   # Exact version
composer require vendor/package:^1.2.3  # ^1.2.3, >=1.2.3 <2.0.0
composer require vendor/package:~1.2.3  # ~1.2.3, >=1.2.3 <1.3.0
composer require vendor/package:>=1.0   # Any version >= 1.0
composer require vendor/package:>=1.0 <2.0
```

### Autoloading

Automatically load classes without `require` statements:

```php
// Without Composer
require_once 'src/User.php';
require_once 'src/Product.php';
require_once 'vendor/package/Class.php';
$user = new User();

// With Composer (PSR-4 autoloading)
require_once 'vendor/autoload.php';
$user = new User();  // Automatically loaded!
$product = new Product();  // Automatically loaded!
```

## Getting Started

### 1. Create Project

```bash
# Create new project directory
mkdir my-project
cd my-project

# Initialize composer
composer init

# Answer questions:
# - Package name: mycompany/my-project
# - Description: My project
# - Author: Your Name <your@email.com>
# - License: MIT
# - Dependencies: none yet
```

This creates `composer.json`.

### 2. Require Packages

```bash
# Install a package
composer require monolog/monolog

# This:
# - Downloads package from Packagist
# - Downloads dependencies
# - Creates vendor/ folder
# - Updates composer.json
# - Creates composer.lock
```

### 3. Use Packages

```php
<?php
require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

// Create logger
$log = new Logger('name');
$log->pushHandler(new StreamHandler('app.log'));

// Use it
$log->info('User logged in');
```

### 4. Share Package Changes

```bash
# Update all packages
composer update

# Install from lock file (reproducible)
composer install

# Remove package
composer remove vendor/package

# Show installed packages
composer show
```

## Dependency Management

### Production Dependencies

Packages needed for your application to run:

```bash
composer require laravel/framework
composer require symfony/http-client
composer require doctrine/orm
```

### Development Dependencies

Packages only needed during development:

```bash
composer require --dev phpunit/phpunit
composer require --dev squizlabs/php_codesniffer
composer require --dev phpstan/phpstan
```

Benefits:
- Production installations skip dev packages
- Smaller production deployments
- Separated concerns

### Publishing to Packagist

Make your package available to others:

```bash
# 1. Push code to GitHub
git push origin main

# 2. Visit packagist.org
# 3. Submit repository URL
# 4. Your package is now available!

composer require username/my-package
```

## Creating Packages

### Package Structure

```
my-package/
├── src/
│   ├── MyClass.php
│   └── AnotherClass.php
├── tests/
│   └── MyClassTest.php
├── composer.json
├── README.md
└── LICENSE
```

### composer.json for Package

```json
{
    "name": "myvendor/my-package",
    "description": "My awesome package",
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
            "MyVendor\\MyPackage\\": "src/"
        }
    }
}
```

### PSR-4 Autoloading

Map namespaces to directories:

```json
"autoload": {
    "psr-4": {
        "MyVendor\\MyPackage\\": "src/",
        "MyVendor\\MyPackage\\Admin\\": "src/admin/"
    }
}
```

## Learning Path

Master Composer progressively:

1. **Installation** - Set up Composer
2. **Project Initialization** - Create composer.json
3. **Adding Dependencies** - Install packages
4. **Using Packages** - Import and use code
5. **Managing Versions** - Semantic versioning
6. **Development Dependencies** - Separate dev tools
7. **Autoloading** - Automatic class loading
8. **Creating Packages** - Build libraries
9. **Publishing** - Share packages
10. **Advanced Configuration** - Custom repositories

## Best Practices

### 1. Commit composer.lock

```bash
git add composer.lock
git commit -m "Update dependencies"
```

**Why:** Ensures everyone uses the same versions

### 2. Use Version Constraints

```json
{
    "require": {
        "monolog/monolog": "^2.0",
        "symfony/console": "^6.0"
    }
}
```

**Benefits:**
- Get bug fixes automatically (patches)
- Prevent breaking changes (major)
- Stay flexible

### 3. Separate Development Tools

```json
{
    "require": {
        "laravel/framework": "^10.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "squizlabs/php_codesniffer": "^3.7"
    }
}
```

### 4. Use Official Packages

Check [packagist.org](https://packagist.org) for:
- Downloads count (popularity)
- Recent updates
- GitHub stars
- Issue count
- Test coverage

### 5. Keep Vendor Folder Out of Version Control

```gitignore
/vendor/
composer.lock  # Only for libraries, not applications
```

Regenerate with:
```bash
composer install
```

## Common Commands

```bash
# Initialize new project
composer init

# Install/update dependencies
composer install          # From lock file
composer update           # Get latest versions

# Add/remove packages
composer require vendor/package
composer require --dev vendor/package
composer remove vendor/package

# List information
composer show             # Installed packages
composer show vendor/package  # Package details
composer outdated         # Outdated packages
composer validate         # Validate composer.json

# Scripts
composer update           # Update all dependencies
composer update vendor/package  # Update specific package

# Clear cache
composer clear-cache
```

## Typical Workflow

```
1. Initialize project
   composer init

2. Add dependencies
   composer require laravel/framework

3. Use in code
   require 'vendor/autoload.php';
   use Framework\App;

4. Update dependencies
   composer update

5. Deploy
   composer install --no-dev

6. Commit
   git add composer.json composer.lock
   git commit
```

## Prerequisites

Before using Composer:

✅ **Required:**
- PHP installed
- Command line comfort
- Basic understanding of packages/libraries
- Git for version control

✅ **Helpful:**
- Understanding of package managers (npm, pip)
- Semantic versioning knowledge

## Troubleshooting

### Issue: `composer: command not found`

```bash
# Check installation
which composer

# Add to PATH
export PATH="$PATH:~/.composer/bin"
```

### Issue: Version conflicts

```bash
# See what versions are available
composer search vendor/package

# Try different constraint
composer require vendor/package:^1.0

# Check conflicts
composer diagnose
```

### Issue: Memory limit exceeded

```bash
composer update -vvv --memory-limit=2G
```

## Resources

- **Official Site**: [getcomposer.org](https://getcomposer.org)
- **Documentation**: [getcomposer.org/doc](https://getcomposer.org/doc/)
- **Packagist**: [packagist.org](https://packagist.org)
- **PSR-4 Autoloading**: [PHP-FIG PSR-4](https://www.php-fig.org/psr/psr-4/)
- **Semantic Versioning**: [semver.org](https://semver.org/)
