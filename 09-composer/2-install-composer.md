# Installing Composer

## Overview

Composer is a dependency management tool for PHP. It allows you to declare the libraries your project depends on and it will manage (install/update) them for you. This chapter covers how to install Composer on different operating systems and verify the installation.

---

## Table of Contents

1. What is Composer
2. Installation Methods
3. macOS Installation
4. Windows Installation
5. Linux Installation
6. Verifying Installation
7. Updating Composer
8. Global Installation
9. Complete Examples

---

## What is Composer

### Why Composer?

```
Before Composer:
- Manually download libraries
- Copy files to project
- Manage versions yourself
- Track dependencies manually
- Complex upgrade process

With Composer:
- Declare dependencies in composer.json
- Run: composer install
- Automatic version management
- Dependency resolution
- Easy updates
- Lock file for reproducibility
```

### Composer vs Other Tools

```
Composer       PHP dependency manager
npm            JavaScript dependency manager
pip            Python package manager
Maven          Java build tool
bundler        Ruby dependency manager

Composer is the standard for PHP projects
```

---

## Installation Methods

### Official Installer vs Manual

```bash
# Official Installer (Recommended)
# - Handles path setup
# - Verifies environment
# - Creates global command

# Manual Download
# - More control
# - More setup required
# - Not recommended for beginners
```

---

## macOS Installation

### Using Homebrew (Easiest)

```bash
# Install Composer via Homebrew
brew install composer

# Verify installation
composer --version
# Composer 2.x.x ...

# Check installation location
which composer
# /opt/homebrew/bin/composer (M1/M2 Macs)
# /usr/local/bin/composer (Intel Macs)
```

### Manual Installation

```bash
# Download installer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Verify SHA-384
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b86f933f68e7d46a5734871a3e073e75a9cee6b46c9e443e0d3d7cf3f15a0e4c2d9e83a44d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); }"

# Install
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verify
composer --version

# Clean up
php -r "unlink('composer-setup.php');"
```

### With M1/M2 Mac Considerations

```bash
# Homebrew handles this automatically
brew install composer

# If using manual installation on Apple Silicon:
# Make sure you're using native PHP, not Intel-emulated

# Check architecture
php -r "echo PHP_OS_FAMILY;"

# For native support
which php
# Should not show /usr/bin/php (Intel)
```

---

## Windows Installation

### Using Windows Installer (Easiest)

```
1. Download: https://getcomposer.org/Composer-Setup.exe
2. Run installer
3. Choose PHP installation (or let installer find it)
4. Proceed with installation
5. Click Finish

Composer is now available in command line (cmd or PowerShell)
```

### Manual Installation (Advanced)

```batch
# Download installer (PowerShell as Administrator)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Verify checksum (from getcomposer.org)
certutil -hashfile composer-setup.php SHA384

# Install
php composer-setup.php --install-dir=C:\tools --filename=composer

# Add to PATH
# System Properties > Environment Variables > Path > Add C:\tools

# Verify (new terminal)
composer --version
```

### Using Scoop (Windows Package Manager)

```bash
# If you have Scoop installed
scoop install composer

# Verify
composer --version
```

---

## Linux Installation

### Ubuntu/Debian

```bash
# Method 1: Using APT (simpler but may be outdated)
sudo apt update
sudo apt install composer

# Verify
composer --version

# Method 2: Manual installation (recommended)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b86f933f68e7d46a5734871a3e073e75a9cee6b46c9e443e0d3d7cf3f15a0e4c2d9e83a44d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); }"

sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

php -r "unlink('composer-setup.php');"

# Verify
composer --version
```

### CentOS/RHEL

```bash
# Using manual installation
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Verify and install
php -r "if (hash_file('sha384', 'composer-setup.php') === 'HASH_HERE') { echo 'Verified'; } else { echo 'Invalid'; unlink('composer-setup.php'); }"

sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verify
composer --version
```

### Arch Linux

```bash
# Using pacman
sudo pacman -S composer

# Verify
composer --version
```

---

## Verifying Installation

### Check Version

```bash
composer --version
# Composer version 2.x.x ...
```

### Check Installation Location

```bash
# macOS/Linux
which composer

# Windows (PowerShell)
where.exe composer
```

### Run Diagnostics

```bash
composer diagnose
# Composer 2.4.x
# ...
# All checks passed
```

### Check PHP Configuration

```bash
# Composer needs specific PHP extensions
composer diagnose

# Checks for:
# - PHP version
# - Required extensions
# - Writeable directories
# - Network connectivity
# - Git availability
```

---

## Updating Composer

### Update to Latest Version

```bash
# macOS (Homebrew)
brew upgrade composer

# Windows (Installer or Scoop)
scoop update composer

# All systems (self-update)
composer self-update

# Update to specific version
composer self-update 2.4.0

# Check for updates
composer self-update --check
```

### Version Checking

```bash
# Get current version
composer --version

# List installed extensions
composer diagnose | grep -i "extension"

# Check PHP version requirement
composer self-update --check
```

---

## Global Installation

### Global Composer Commands

```bash
# Install package globally
composer global require phpunit/phpunit

# Run globally installed packages
phpunit --version

# Global packages location
# macOS/Linux: ~/.composer/
# Windows: %APPDATA%\Composer\

# Add to PATH
# Most systems do this automatically
# Verify: which phpunit
```

### Global composer.json

```bash
# Edit global composer.json
composer global config --list

# Add global dependencies
composer global require symfony/console

# Update global packages
composer global update

# Show global packages
composer global show
```

---

## Complete Examples

### First-Time Setup on macOS

```bash
#!/bin/bash
# install_composer_macos.sh

echo "Installing Composer on macOS..."

# Check if Homebrew is installed
if ! command -v brew &> /dev/null; then
    echo "Homebrew not found. Installing Homebrew..."
    /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
fi

# Install PHP (if needed)
if ! command -v php &> /dev/null; then
    echo "Installing PHP..."
    brew install php
fi

# Install Composer
echo "Installing Composer..."
brew install composer

# Verify installation
echo "Verifying installation..."
composer --version

# Run diagnostics
echo "Running diagnostics..."
composer diagnose

echo "Composer installation complete!"
```

### First-Time Setup on Ubuntu

```bash
#!/bin/bash
# install_composer_ubuntu.sh

echo "Installing Composer on Ubuntu..."

# Update package list
sudo apt update

# Install PHP (if needed)
if ! command -v php &> /dev/null; then
    echo "Installing PHP..."
    sudo apt install -y php php-cli php-json php-curl php-mbstring
fi

# Download and install Composer
echo "Installing Composer..."
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Note: You should verify the checksum from https://getcomposer.org/
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

rm composer-setup.php

# Verify installation
echo "Verifying installation..."
composer --version

# Run diagnostics
echo "Running diagnostics..."
composer diagnose

echo "Composer installation complete!"
```

### First-Time Setup on Windows

```batch
@echo off
REM install_composer_windows.bat
REM Run as Administrator

echo Installing Composer on Windows...

REM Check if PHP is installed
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo PHP not found. Please install PHP first.
    exit /b 1
)

REM Download installer
echo Downloading Composer installer...
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

REM Verify and install
echo Installing Composer...
php composer-setup.php --install-dir=C:\tools --filename=composer

REM Delete installer
del composer-setup.php

REM Add to PATH (requires user to restart or use setx)
setx PATH "%PATH%;C:\tools"

REM Verify installation (in new terminal)
echo Installation complete!
echo Please open a new terminal and run: composer --version
```

---

## Troubleshooting

### Common Issues

```bash
# "command not found: composer"
# Solution: Check PATH or reinstall

# "The system cannot find the path specified" (Windows)
# Solution: Add Composer directory to PATH

# Permission denied
# Solution: chmod +x /usr/local/bin/composer

# SSL certificate problem
# Temporary fix: composer config -g secure-http false
# Better: Update PHP certificates

# Composer out of memory
# Solution: php -d memory_limit=-1 composer.phar install

# PHP version too old
# Solution: Update PHP or use compatible Composer version
```

### Getting Help

```bash
# Show help
composer help

# Help for specific command
composer help require

# List all available commands
composer list

# Verbose output (debugging)
composer -v require vendor/package

# Very verbose output
composer -vv require vendor/package
```

---

## Key Takeaways

**Installation Checklist:**

1. ✅ Install from official source (https://getcomposer.org)
2. ✅ Verify checksum after download
3. ✅ Place in /usr/local/bin (Unix) or system PATH (Windows)
4. ✅ Verify with `composer --version`
5. ✅ Run `composer diagnose` to check setup
6. ✅ Keep Composer updated with `composer self-update`
7. ✅ Use Homebrew on macOS for easiest installation
8. ✅ Ensure PHP is installed with required extensions

---

## See Also

- [Create Composer Project](3-create-composer-project.md)
- [Autoload](4-autoload.md)
- [Repository Configuration](5-repository.md)
