# PHP Installation & Environment Setup

## Overview

PHP is a server-side scripting language that powers millions of websites. To start PHP development, you need to install PHP and set up a proper development environment. There are multiple ways to do this depending on your operating system and preferences.

## Installation Methods

### 1. **Using Integrated Development Environments (Recommended for Beginners)**

#### XAMPP (Cross-Platform)
**Best for**: Windows, macOS, Linux users who want an all-in-one solution

```bash
# Download from: https://www.apachefriends.org

# After installation:
php -v  # Verify PHP installation
```

**What you get:**
- Apache web server
- PHP interpreter
- MySQL/MariaDB database
- PhpMyAdmin (database management)

**Advantages:**
- ✓ Easy installation process
- ✓ All-in-one package (no separate installations)
- ✓ Built-in database and server
- ✓ Free and open-source

#### MAMP (macOS)
**Best for**: macOS developers

```bash
# Download from: https://www.mamp.info
# PRO version offers more features

# Verify installation:
php -v
```

#### WAMP (Windows)
**Best for**: Windows developers

```powershell
# Download from: http://www.wampserver.com
# Follow installation wizard

# Verify:
php -v
```

### 2. **Direct PHP Installation**

#### macOS (Using Homebrew)
```bash
# Install Homebrew first if not already installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP
brew install php

# Verify installation
php -v

# Start PHP's built-in server
php -S localhost:8000
```

#### Ubuntu/Debian (Linux)
```bash
# Update package manager
sudo apt update

# Install PHP and common extensions
sudo apt install php php-cli php-mysql php-xml php-zip php-curl

# Verify installation
php -v

# Check PHP configuration file location
php --ini
```

#### Windows (Direct Installation)
1. Download from: https://www.php.net/downloads
2. Extract to a folder (e.g., `C:\php`)
3. Add to system PATH environment variable
4. Verify: Open Command Prompt and type `php -v`

### 3. **Using Package Managers**

#### Windows (Chocolatey)
```powershell
# Install Chocolatey first if needed
# Then install PHP:
choco install php

php -v  # Verify
```

#### Using Docker (Advanced)
```bash
# Modern approach using containerization
docker pull php:8.2-cli

# Run PHP in container
docker run -it --rm -v $(pwd):/app php:8.2-cli php /app/index.php
```

## Choosing the Right Setup

| Method | Ease | Features | Best For |
|--------|------|----------|----------|
| XAMPP | ⭐⭐⭐⭐⭐ | Full suite | Beginners, local dev |
| MAMP | ⭐⭐⭐⭐ | Full suite | macOS users |
| Direct Install | ⭐⭐⭐ | Customizable | Developers |
| Docker | ⭐⭐ | Isolated, portable | DevOps, production-like |

## Verification & Configuration

### 1. Verify Installation
```bash
# Check PHP version
php -v

# Output example:
# PHP 8.2.0 (cli) (built: Dec  6 2022 15:35:40) ( ZTS Visual C++ 2019 v16 x64 )
```

### 2. Check Installed Extensions
```bash
php -m
```

### 3. View Configuration
```bash
# Find php.ini location
php --ini

# View complete PHP information
php -i

# View configuration for specific setting
php -r "echo ini_get('display_errors');"
```

## Development Tools

### Text Editors (Lightweight)
- **Visual Studio Code** - Free, lightweight, excellent PHP support
  - Install extensions: `PHP Intelephense`, `PHP Debug`
- **Sublime Text** - Fast, customizable
- **Atom** - Community-driven, highly extensible

### IDEs (Feature-Rich)
- **PhpStorm** - Professional IDE by JetBrains
  - $199/year (free trial available)
  - Best-in-class PHP development
- **Visual Studio Code** - Free alternative with extensions
- **Eclipse PDT** - Free, open-source PHP IDE
- **NetBeans** - Free, open-source IDE with PHP support

### Recommended Setup for Beginners
```
1. Install XAMPP or MAMP
2. Use Visual Studio Code as editor
3. Install PHP Intelephense extension
4. Start Apache and MySQL from XAMPP/MAMP
5. Create files in htdocs (XAMPP) or htdocs (MAMP)
6. Access via http://localhost
```

## Creating Your First PHP Project

### Directory Structure
```
my-php-project/
├── index.php
├── config/
│   └── database.php
├── public/
│   └── css/
│   └── js/
└── src/
    └── helpers.php
```

### Testing Your Setup
```bash
# Create a test file
cat > index.php << 'EOF'
<?php
echo "Hello, PHP World!";
echo "<br>";
phpinfo();
?>
EOF

# Run with built-in server
php -S localhost:8000

# Visit: http://localhost:8000
```

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| `php: command not found` | PHP not in PATH. Reinstall or add to PATH |
| Port 8000 already in use | Use different port: `php -S localhost:8001` |
| `php.ini` not found | Check with: `php --ini` |
| Extensions not loading | Enable in php.ini: uncomment extension line |
| Permission denied | On Linux: `chmod +x script.php` |

## Next Steps

- ✓ Installation complete
- → Move to Hello World (`2-hello-world.md`) to write your first script
- → Learn Variables (`6-variable.md`) for storing data
- → Explore Data Types (`3-data-type-number.md`, etc.)

## Resources

- **Official PHP Documentation**: https://www.php.net/docs.php
- **PHP Handbook**: https://www.php.net/manual/en/
- **Laravel (Popular Framework)**: https://laravel.com/docs
- **PHP Standards (PSR)**: https://www.php-fig.org/
