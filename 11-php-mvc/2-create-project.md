# Setting Up an MVC Project

## Overview

Create a complete MVC project structure with proper organization, autoloading, and initial configuration.

---

## Table of Contents

1. Project Structure
2. Composer Setup
3. Autoloading Configuration
4. Directory Creation
5. Initial Files
6. Configuration
7. Complete Examples

---

## Project Structure

### Recommended Layout

```
project/
├── public/
│   ├── index.php              # Entry point
│   ├── css/
│   ├── js/
│   └── images/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   ├── Middleware/
│   ├── Routes/
│   └── Services/
├── resources/
│   └── views/
├── config/
│   ├── app.php
│   ├── database.php
│   └── routes.php
├── database/
│   ├── migrations/
│   └── seeds/
├── storage/
│   ├── logs/
│   └── cache/
├── tests/
├── vendor/
├── .env
├── .gitignore
├── composer.json
└── README.md
```

### Public Directory

```php
<?php
// public/index.php - Single entry point

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load environment
require BASE_PATH . '/config/app.php';

// Create application
$app = new Application();

// Run application
$response = $app->run($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

// Send response
echo $response;
```

---

## Composer Setup

### Package Management

```json
{
    "name": "user/myproject",
    "description": "My MVC Application",
    "type": "project",
    "require": {
        "php": ">=8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\": "database/",
            "Tests\\": "tests/"
        }
    }
}
```

### Installation

```bash
# Create new project
composer create-project user/myproject myapp

# Install dependencies
composer install

# Update dependencies
composer update

# Dump autoloader
composer dump-autoload
```

---

## Autoloading Configuration

### PSR-4 Autoloading

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Resources\\": "resources/",
            "Database\\": "database/"
        },
        "files": [
            "app/helpers.php",
            "app/routes.php"
        ]
    }
}
```

### Namespace Usage

```php
<?php
// app/Controllers/UserController.php
namespace App\Controllers;

use App\Models\User;

class UserController {
    public function __construct(private User $user) {}
}

// app/Models/User.php
namespace App\Models;

class User {
    // Model code
}

// public/index.php
use App\Controllers\UserController;

$controller = new UserController(new User());
```

---

## Directory Creation

### Creating Directories

```bash
# Create directory structure
mkdir -p app/{Controllers,Models,Services,Middleware}
mkdir -p resources/views/{user,product,home}
mkdir -p public/{css,js,images}
mkdir -p config
mkdir -p database/{migrations,seeds}
mkdir -p storage/{logs,cache}
mkdir -p tests/{Unit,Feature}
```

### Initial Files

```bash
# Create initial files
touch public/index.php
touch app/helpers.php
touch config/app.php
touch config/database.php
touch .env
touch .gitignore
```

---

## Configuration

### Application Config

```php
<?php
// config/app.php

return [
    'name' => 'My Application',
    'debug' => getenv('APP_DEBUG') === 'true',
    'timezone' => 'UTC',
    'locale' => 'en',
    
    'providers' => [
        \App\Providers\RouteServiceProvider::class,
        \App\Providers\ViewServiceProvider::class,
    ],
];
```

### Database Config

```php
<?php
// config/database.php

return [
    'default' => getenv('DB_DRIVER') ?? 'mysql',
    
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => getenv('DB_HOST') ?? 'localhost',
            'port' => getenv('DB_PORT') ?? 3306,
            'database' => getenv('DB_NAME') ?? 'myapp',
            'username' => getenv('DB_USER') ?? 'root',
            'password' => getenv('DB_PASSWORD') ?? '',
            'charset' => 'utf8mb4',
        ],
        
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => BASE_PATH . '/database/app.db',
        ],
    ],
];
```

### Environment File

```bash
# .env

APP_NAME=MyApp
APP_DEBUG=true
APP_ENV=local

DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=myapp
DB_USER=root
DB_PASSWORD=
```

---

## Complete Examples

### Example 1: Basic Project Setup

```bash
# Step 1: Create project directory
mkdir my_mvc_app
cd my_mvc_app

# Step 2: Initialize Composer
composer init

# Step 3: Create directory structure
mkdir -p app/{Controllers,Models}
mkdir -p resources/views
mkdir -p public
mkdir -p config

# Step 4: Create entry point
cat > public/index.php << 'EOF'
<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

echo "MVC Application Running!";
EOF

# Step 5: Run
php -S localhost:8000 -t public
```

### Example 2: Docker-Based Setup

```dockerfile
# Dockerfile
FROM php:8.1-apache

RUN docker-php-ext-install pdo_mysql

COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
```

### Example 3: Development Server

```php
<?php
// scripts/serve.php

$host = 'localhost';
$port = 8000;

echo "Starting server at http://$host:$port\n";

exec("php -S $host:$port -t public");
```

---

## Key Takeaways

**Project Setup Checklist:**

1. ✅ Create organized directory structure
2. ✅ Setup Composer with PSR-4 autoloading
3. ✅ Create public entry point
4. ✅ Configure environment variables
5. ✅ Setup database configuration
6. ✅ Create config files
7. ✅ Initialize routes
8. ✅ Create .gitignore
9. ✅ Setup development server
10. ✅ Test basic routing

---

## See Also

- [MVC Basics](0-mvc-basics.md)
- [Routing](5-route.md)
- [Controllers](6-controller.md)
