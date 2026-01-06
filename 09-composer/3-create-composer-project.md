# Creating Composer Projects

## Overview

This chapter covers creating new PHP projects using Composer. We'll explore different methods to create projects, from using `composer init` to cloning existing projects, setting up directory structures, and configuring your first Composer project.

---

## Table of Contents

1. Creating a Project from Scratch
2. Understanding composer.json
3. Directory Structure
4. Project Types
5. Interactive Project Setup
6. Creating from Templates
7. Cloning Existing Projects
8. First Dependencies
9. Running Tests
10. Complete Examples

---

## Creating a Project from Scratch

### Method 1: composer init (Interactive)

```bash
# Navigate to project directory
mkdir my-php-project
cd my-php-project

# Interactive setup
composer init

# Prompts:
# Package name: vendor/package
# Description: My awesome project
# Author name: Your Name <email@example.com>
# Minimum stability: dev
# Package type: library/project
# License: MIT
# Define dependencies: (yes/no)
```

### Method 2: composer create-project

```bash
# Create from template (automatic setup)
composer create-project vendor/template project-name

# Example: Create Symfony project
composer create-project symfony/skeleton my-app

# Example: Create Laravel project
composer create-project laravel/laravel my-app

# Create with specific version
composer create-project symfony/skeleton:^6.0 my-app

# Create in current directory
composer create-project vendor/template . --stability=dev
```

### Method 3: Manual composer.json

```bash
# Create directory
mkdir my-project
cd my-project

# Create composer.json manually
cat > composer.json << 'EOF'
{
    "name": "vendor/my-project",
    "type": "project",
    "description": "My PHP project",
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
    }
}
EOF

# Install dependencies
composer install
```

---

## Understanding composer.json

### Basic Structure

```json
{
    "name": "vendor/project-name",
    "type": "library",
    "description": "A brief description",
    "license": "MIT",
    "authors": [
        {
            "name": "Author Name",
            "email": "author@example.com",
            "role": "Developer"
        }
    ],
    "require": {
        "php": ">=8.0",
        "monolog/monolog": "^2.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "squizlabs/php_codesniffer": "^3.7"
    },
    "autoload": {
        "psr-4": {
            "MyApp\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "MyApp\\Tests\\": "tests/"
        }
    }
}
```

### Key Properties Explained

```json
{
    "name": "vendor/package",
    // Unique package identifier, lowercase, hyphenated
    
    "type": "library|project|metapackage|composer-plugin",
    // library: Package you want to reuse
    // project: Application (root project)
    
    "description": "What does this package do",
    // Short description shown on Packagist
    
    "license": "MIT|Apache-2.0|GPL-3.0",
    // Open source license
    
    "keywords": ["web", "framework"],
    // For searching on Packagist
    
    "homepage": "https://example.com",
    // Project website
    
    "support": {
        "issues": "https://github.com/vendor/package/issues",
        "docs": "https://docs.example.com"
    }
}
```

---

## Directory Structure

### Standard PHP Project Layout

```
my-project/
├── src/                    # Source code
│   ├── Models/
│   ├── Controllers/
│   ├── Services/
│   └── MyApp.php
├── tests/                  # Test files
│   ├── Unit/
│   ├── Feature/
│   └── bootstrap.php
├── config/                 # Configuration
│   ├── app.php
│   └── database.php
├── vendor/                 # Dependencies (auto-generated)
├── public/                 # Web root
│   └── index.php
├── .gitignore
├── composer.json
├── composer.lock           # Lock file (auto-generated)
├── phpunit.xml             # Test configuration
└── README.md
```

### Package/Library Layout

```
my-package/
├── src/                    # Source code
│   └── MyPackage.php
├── tests/                  # Tests
│   └── MyPackageTest.php
├── vendor/                 # Dependencies
├── composer.json
├── composer.lock
├── phpunit.xml
├── .gitignore
└── README.md
```

### Minimal Project Layout

```
my-app/
├── src/
│   └── App.php
├── vendor/                 # Created by composer
├── composer.json
├── composer.lock           # Created by composer
└── .gitignore
```

---

## Project Types

### Library (Package)

```json
{
    "name": "my-vendor/my-library",
    "type": "library",
    "description": "A reusable PHP library",
    "require": {
        "php": ">=8.0"
    },
    "autoload": {
        "psr-4": {
            "MyVendor\\MyLibrary\\": "src/"
        }
    }
}
```

**Use when:** Creating a package to share with others

### Project (Application)

```json
{
    "name": "my-company/my-app",
    "type": "project",
    "description": "My web application",
    "require": {
        "php": ">=8.0",
        "symfony/framework-bundle": "^6.0"
    }
}
```

**Use when:** Building an application (usually the root package)

### Metapackage

```json
{
    "name": "my-vendor/my-metapackage",
    "type": "metapackage",
    "description": "Meta package for common dependencies",
    "require": {
        "symfony/http-foundation": "^6.0",
        "symfony/event-dispatcher": "^6.0",
        "monolog/monolog": "^2.0"
    }
}
```

**Use when:** Grouping multiple packages together

### Composer Plugin

```json
{
    "name": "my-vendor/my-plugin",
    "type": "composer-plugin",
    "require": {
        "composer-plugin-api": "^2.0"
    },
    "extra": {
        "class": "MyVendor\\MyPlugin\\Plugin"
    }
}
```

**Use when:** Extending Composer's functionality

---

## Interactive Project Setup

### Full composer init Example

```bash
$ composer init

  Welcome to the Composer Config Generator  

This command will guide you through creating your composer.json config.

Package name (<vendor>/<name>) [user/my-project]: acme/my-app
Description []: My awesome application
Author [Author Name <author@example.com>, n to skip]: John Doe <john@example.com>
Minimum Stability []: dev
Package Type (e.g. library, project, metapackage, composer-plugin) []: project
License []: MIT

Define your dependencies.

Would you like to define your dependencies (require) interactively [yes]? yes
Search for a package []: monolog/monolog

Found 4 packages matching monolog/monolog

   [0] monolog/monolog ^3.0
   [1] monolog/monolog ^2.9
   [2] monolog/monolog ^1.27
   [3] monolog/monolog dev-main

Enter package number or abort for none []: 0
Enter the version constraint (or leave blank to use the latest version) []: ^2.0
Search for a package []: 

Would you like to define your dev dependencies (require-dev) interactively [yes]? yes
Search for a package []: phpunit/phpunit

Found 7 packages matching phpunit/phpunit

   [0] phpunit/phpunit ^10.0
   [1] phpunit/phpunit ^9.6
   ...

Enter package number or abort for none []: 0
Search for a package []: 

Generated composer.json file.
{
    "name": "acme/my-app",
    "type": "project",
    "description": "My awesome application",
    "license": "MIT",
    "authors": [
        {
            "name": "John Doe",
            "email": "john@example.com"
        }
    ],
    "require": {
        "php": ">=5.3.0",
        "monolog/monolog": "^2.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    }
}

Do you confirm generation [yes]? yes
```

---

## Creating from Templates

### Using Symfony Project

```bash
# Create Symfony project
composer create-project symfony/skeleton my-app
cd my-app

# Install Web Framework Bundle
composer require symfony/web-framework-bundle

# Verify structure
ls -la
# src/, public/, config/, templates/, tests/, etc.
```

### Using Laminas Project

```bash
# Create Laminas project
composer create-project laminas/laminas-mvc-skeleton my-app
cd my-app

# Project structure
tree
# config/, module/, public/, vendor/, composer.json, etc.
```

### Creating Custom Template

```json
{
    "name": "my-company/php-starter-template",
    "type": "project",
    "description": "Starter template for PHP projects",
    "require": {
        "php": ">=8.0",
        "monolog/monolog": "^2.0",
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "MyCompany\\": "src/"
        }
    }
}
```

---

## Cloning Existing Projects

### Clone and Install

```bash
# Clone repository
git clone https://github.com/user/php-project.git
cd php-project

# Install dependencies (from composer.lock)
composer install

# Verify installation
composer show
```

### Install vs Update

```bash
# composer install (Production)
# - Uses composer.lock exactly
# - Reproducible, deterministic
# - Same packages every time
composer install

# composer update (Development)
# - Respects version constraints
# - Updates packages as needed
# - Updates composer.lock
composer update

# Update specific package
composer update vendor/package
```

---

## First Dependencies

### Add Your First Dependency

```bash
# Add monolog
composer require monolog/monolog

# Add with specific version
composer require monolog/monolog:^2.0

# Add multiple dependencies
composer require monolog/monolog symfony/var-dumper

# Add as development dependency
composer require --dev phpunit/phpunit
```

### Using the Dependency

```php
// src/App.php
<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('name');
$log->pushHandler(new StreamHandler('app.log', Logger::WARNING));

$log->warning('Foo');
$log->error('Bar');
```

---

## Running Tests

### Setup PHPUnit

```bash
# Install PHPUnit as dev dependency
composer require --dev phpunit/phpunit

# Create phpunit.xml
cat > phpunit.xml << 'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         bootstrap="tests/bootstrap.php"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.0/phpunit.xsd">
    <testsuites>
        <testsuite name="unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
EOF

# Run tests
./vendor/bin/phpunit
```

---

## Complete Examples

### Creating a Web Application Project

```bash
# Create project
composer create-project --prefer-dist symfony/skeleton my-web-app
cd my-web-app

# Add dependencies
composer require symfony/orm-pack
composer require symfony/form symfony/validator

# Create src structure
mkdir -p src/{Entity,Repository,Form}

# Verify
composer show --all
```

### Creating a Library Package

```bash
# Initialize
mkdir my-library && cd my-library
composer init

# Answers:
# - Name: vendor/my-library
# - Type: library
# - Description: My reusable library
# - License: MIT

# Create src directory
mkdir -p src

# Add test dependency
composer require --dev phpunit/phpunit

# Create phpunit.xml
vendor/bin/phpunit --generate-configuration

# Your library is ready
```

---

## Key Takeaways

**Project Creation Checklist:**

1. ✅ Choose creation method (init, create-project, manual)
2. ✅ Define project name (vendor/project)
3. ✅ Set appropriate type (library, project, etc.)
4. ✅ Configure autoload paths
5. ✅ Add dependencies
6. ✅ Create proper directory structure
7. ✅ Set up testing framework
8. ✅ Verify with `composer show`

---

## See Also

- [Installing Composer](2-install-composer.md)
- [Autoload Configuration](4-autoload.md)
- [Adding Dependencies](6-add-dependency-library.md)
