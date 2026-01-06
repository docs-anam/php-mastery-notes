# Installation - Setting Up PHP Development Environment

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Installation Methods](#installation-methods)
3. [macOS Installation](#macos-installation)
4. [Linux Installation](#linux-installation)
5. [Windows Installation](#windows-installation)
6. [Verification](#verification)
7. [Text Editors & IDEs](#text-editors--ides)
8. [Running PHP](#running-php)

---

## Prerequisites

Before installing PHP, ensure you have:

- Administrator/sudo access on your computer
- Basic command-line knowledge
- 500MB+ free disk space
- Internet connection for downloading

---

## Installation Methods

### Option 1: Integrated Packages (Easiest)

All-in-one solutions including PHP, Apache, MySQL:

| Package | Platform | Website | Includes |
|---------|----------|---------|----------|
| **XAMPP** | Windows, macOS, Linux | [apachefriends.org](https://www.apachefriends.org) | Apache, MySQL, PHP, Perl |
| **MAMP** | macOS, Windows | [mamp.info](https://www.mamp.info) | Apache, MySQL, PHP |
| **WAMP** | Windows | [wampserver.com](http://www.wampserver.com) | Apache, MySQL, PHP |
| **Laragon** | Windows | [laragon.org](https://laragon.org) | Apache, MySQL, PHP, Node.js |

**Advantages:**
- Simple installation
- Pre-configured servers
- GUI management tools
- Includes databases

**Best for:**
- Beginners
- Quick setup
- Local development

### Option 2: Package Managers (Recommended)

Install directly using system package managers:

**macOS (Homebrew):**
```bash
brew install php
```

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install php php-cli php-mysql php-curl php-json
```

**Fedora/CentOS:**
```bash
sudo dnf install php php-cli php-mysql php-curl
```

**Advantages:**
- Latest versions
- Easy updates
- Less disk space
- Better control

### Option 3: Docker (Professional)

For containerized development:

```bash
docker run -it php:latest php -v
```

**Advantages:**
- Isolated environments
- Reproducible setups
- Easy version switching
- Production-like environment

---

## macOS Installation

### Method 1: Homebrew (Recommended)

**Step 1: Install Homebrew**
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

**Step 2: Install PHP**
```bash
brew install php
```

**Step 3: Verify Installation**
```bash
php -v
php -m  # List installed modules
```

### Method 2: MAMP

1. Download from [mamp.info](https://www.mamp.info)
2. Drag MAMP to Applications folder
3. Launch MAMP
4. Start servers (Apache & MySQL)
5. Access: http://localhost:8888

### Method 3: MacPorts

```bash
sudo port install php81 +apache2
```

---

## Linux Installation

### Ubuntu/Debian (APT)

**Step 1: Update Package Manager**
```bash
sudo apt-get update
```

**Step 2: Install PHP and Common Modules**
```bash
sudo apt-get install php php-cli php-fpm php-mysql php-curl php-json php-gd php-zip
```

**Step 3: Verify**
```bash
php -v
```

### Fedora/CentOS (DNF)

```bash
sudo dnf install php php-cli php-mysql php-curl
php -v
```

### From Source

For maximum control:

```bash
# Download
curl -O https://www.php.net/downloads.php

# Extract
tar xzf php-*.tar.gz
cd php-*/

# Configure
./configure --prefix=/usr/local/php

# Compile and install
make
make install

# Verify
/usr/local/php/bin/php -v
```

---

## Windows Installation

### Method 1: XAMPP (Easiest)

1. Download from [apachefriends.org](https://www.apachefriends.org)
2. Run installer (`.exe`)
3. Choose installation folder (e.g., `C:\xampp`)
4. Accept default components
5. Complete installation
6. Launch XAMPP Control Panel
7. Start Apache and MySQL

### Method 2: Direct Installation

**Step 1: Download**
- Go to [windows.php.net](https://windows.php.net)
- Download VC Redist (if needed)
- Download PHP zip file

**Step 2: Extract**
- Extract to `C:\php` (or preferred location)
- Add to PATH environment variable

**Step 3: Configure**
- Copy `php.ini-development` to `php.ini`
- Edit `php.ini` for your setup

**Step 4: Verify**
```bash
php -v
```

### Method 3: Chocolatey

```bash
choco install php
php -v
```

---

## Verification

### Check Installation

```bash
# Version
php -v

# Example output:
# PHP 8.2.0 (cli) (built: Dec  7 2022 13:31:26) ( ZTS Visual C++ 2019 x64 )
# Copyright (c) The PHP Group
# Zend Engine v4.2.0, Copyright (c) Zend Technologies

# List loaded modules
php -m

# Display configuration
php -i

# Check specific module
php -m | grep curl  # Check if curl extension loaded
```

### Create Test File

Create `test.php`:
```php
<?php
echo "Hello, World!";
echo "<br>";
echo "PHP Version: " . phpversion();
?>
```

Run it:
```bash
php test.php
```

Expected output:
```
Hello, World!
PHP Version: 8.2.0
```

---

## Text Editors & IDEs

### Lightweight Editors

| Editor | Platform | Website | Best For |
|--------|----------|---------|----------|
| **VS Code** | All | [code.visualstudio.com](https://code.visualstudio.com) | Modern, lightweight, extensions |
| **Sublime Text** | All | [sublimetext.com](https://www.sublimetext.com) | Fast, minimal, powerful |
| **Atom** | All | [atom.io](https://atom.io) | Open-source, customizable |
| **Vim/Neovim** | All | Terminal-based | Expert developers |

### Professional IDEs

| IDE | Platform | Website | Cost | Best For |
|-----|----------|---------|------|----------|
| **PhpStorm** | All | [jetbrains.com](https://www.jetbrains.com/phpstorm) | $$ | Professional development |
| **Visual Studio Code** | All | Free | Free | Modern all-purpose |
| **Eclipse PDT** | All | Free | Free | Enterprise projects |

### VS Code PHP Setup (Recommended)

**Step 1: Install Extensions**
- PHP Intelephense
- PHP Debugger
- PHP Namespace Resolver

**Step 2: Configure Settings**

Create `.vscode/settings.json`:
```json
{
    "php.validate.executablePath": "/usr/local/bin/php",
    "php.suggest.basic": false,
    "intelephense.diagnostics.undefinedMethods": false
}
```

**Step 3: Create Workspace**
- File → Open Folder
- Select your PHP project folder

---

## Running PHP

### Method 1: Command Line

```bash
php filename.php
```

### Method 2: Built-in Server

```bash
# Start server
php -S localhost:8000

# Now visit http://localhost:8000 in browser
```

### Method 3: Apache (XAMPP/WAMP)

1. Place files in `htdocs/` folder
2. Start Apache (via control panel)
3. Visit `http://localhost/your-file.php`

### Method 4: Docker

```bash
docker run -v $(pwd):/app -w /app php:latest php script.php
```

---

## Troubleshooting

### "php command not found"

**macOS/Linux:**
```bash
# Find PHP location
which php

# Add to PATH if needed
export PATH=/usr/local/bin:$PATH
```

**Windows:**
- Add PHP folder to PATH environment variable
- Restart command prompt

### Port Already in Use

```bash
# Use different port
php -S localhost:8080

# Find and kill process on port 8000
lsof -ti:8000 | xargs kill -9
```

### Permission Denied

```bash
# Make file executable
chmod +x script.php

# Run with php explicitly
php script.php
```

### Missing Extensions

```bash
# List installed extensions
php -m

# Install missing extension (Ubuntu example)
sudo apt-get install php-curl

# Or enable in php.ini
# Find line: ;extension=curl
# Remove the semicolon: extension=curl
```

---

## Recommended Setup

For beginners:
1. **VS Code** - Free, modern editor
2. **XAMPP/MAMP** - All-in-one environment
3. **PHP 8.2+** - Latest stable version

For professionals:
1. **PhpStorm** or **VS Code** - IDE/Editor
2. **Homebrew/apt** - Direct installation
3. **Docker** - Containerized environment
4. **Git** - Version control

---

## Quick Start

Once installed, create a file `hello.php`:

```php
<?php
echo "Hello, World!";
?>
```

Run it:
```bash
php hello.php
```

Output:
```
Hello, World!
```

Congratulations! You're ready to code in PHP! 🎉

---

## Next Steps

✅ Install PHP  
→ Learn [Hello World](2-hello-world.md)  
→ Understand [data types](3-data-type-number.md)  
→ Start coding!
