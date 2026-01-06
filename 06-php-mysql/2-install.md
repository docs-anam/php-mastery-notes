# Installing and Setting Up MySQL

## Overview

MySQL is a popular open-source relational database management system. This guide covers installation, configuration, and initial setup for development environments.

---

## Table of Contents

1. Installation on macOS
2. Installation on Windows
3. Installation on Linux
4. Initial Configuration
5. Creating User Accounts
6. Testing the Installation
7. Common Issues and Solutions

---

## Installation on macOS

### Using Homebrew (Recommended)

```bash
# Install MySQL
brew install mysql

# Start MySQL service
brew services start mysql

# Run setup script
mysql_secure_installation

# Verify installation
mysql --version
```

### Using DMG Installer

1. Download from [mysql.com](https://www.mysql.com/downloads/)
2. Run the installer
3. Follow the installation wizard
4. Configure MySQL as a macOS service
5. Start MySQL: `brew services start mysql`

### Verify Installation

```bash
# Connect to MySQL
mysql -u root

# You should see the MySQL prompt
mysql>
```

---

## Installation on Windows

### Using MySQL Installer

1. Download MySQL installer from [mysql.com](https://www.mysql.com/downloads/)
2. Run MySQLInstaller-Community-*.msi
3. Choose setup type:
   - Developer Default (recommended)
   - Server only
   - Custom
4. Select products to install
5. Configure MySQL Server:
   - Port: 3306 (default)
   - MySQL X Protocol Port: 33060
   - Windows Service: Yes (recommended)
6. Create Windows Service
7. Configure MySQL Server as Windows Service
8. Configure MySQL Router (optional)
9. Configure MySQL Workbench (optional)
10. Complete installation

### Verify Installation

```bash
# Open Command Prompt
mysql -u root -p

# Enter root password when prompted
```

### Using Chocolatey

```bash
choco install mysql
```

---

## Installation on Linux (Ubuntu/Debian)

```bash
# Update package list
sudo apt update

# Install MySQL server
sudo apt install mysql-server

# Run security script
sudo mysql_secure_installation

# Verify installation
mysql --version

# Check service status
sudo systemctl status mysql

# Start MySQL service
sudo systemctl start mysql
```

### For CentOS/RHEL

```bash
# Install MySQL
sudo yum install mysql-server

# Start MySQL service
sudo systemctl start mysqld

# Run security script
sudo mysql_secure_installation
```

---

## Initial Configuration

### Secure Installation

```bash
mysql_secure_installation
```

This script helps you:
1. Set root password
2. Remove anonymous users
3. Disable remote root login
4. Remove test databases
5. Reload privilege tables

### Access MySQL

```bash
# Connect as root
mysql -u root -p

# Will prompt for password
# Once connected, you'll see mysql> prompt
```

### Create First Database

```sql
-- Inside MySQL shell
CREATE DATABASE IF NOT EXISTS learning;

-- Show databases
SHOW DATABASES;

-- Select database to use
USE learning;
```

---

## Creating User Accounts

```sql
-- Create new user
CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';

-- Grant all privileges on a database
GRANT ALL PRIVILEGES ON learning.* TO 'username'@'localhost';

-- Grant specific privileges
GRANT SELECT, INSERT, UPDATE ON learning.* TO 'username'@'localhost';

-- Reload privilege tables
FLUSH PRIVILEGES;

-- Show grants for user
SHOW GRANTS FOR 'username'@'localhost';

-- Delete user
DROP USER 'username'@'localhost';
```

---

## Testing the Installation

### Command Line Test

```bash
# Test connection
mysql -u root -p -e "SELECT VERSION();"

# Enter password and should see version number
```

### PHP Connection Test

```php
<?php
try {
    $pdo = new PDO('mysql:host=localhost', 'root', 'password');
    echo "Connected to MySQL successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
```

### Create Test Database

```sql
-- Create test database
CREATE DATABASE test_db;

-- Use database
USE test_db;

-- Create test table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert test data
INSERT INTO users (name, email) VALUES ('John Doe', 'john@example.com');
INSERT INTO users (name, email) VALUES ('Jane Smith', 'jane@example.com');

-- Verify data
SELECT * FROM users;
```

---

## Common Installation Issues

### Issue: "command not found: mysql"

**Solution:**
- Add MySQL bin directory to PATH
- On macOS with Homebrew: `export PATH="/usr/local/mysql/bin:$PATH"` in `.bashrc` or `.zshrc`

### Issue: Access Denied for root

**Solution:**
```bash
# Reset root password
sudo mysql

# Inside MySQL:
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
```

### Issue: Port Already in Use

**Solution:**
```bash
# Check what's using port 3306
lsof -i :3306

# Kill the process
kill -9 <PID>

# Or configure MySQL to use different port
```

### Issue: MySQL Service Won't Start

**Solution:**
```bash
# Check error log
tail -f /var/log/mysql/error.log

# On macOS
brew services stop mysql
rm /usr/local/var/mysql/mysql.sock
brew services start mysql
```

---

## Useful MySQL Commands

```bash
# Start/stop MySQL
sudo systemctl start mysql
sudo systemctl stop mysql
sudo systemctl restart mysql

# Check status
sudo systemctl status mysql

# Login to MySQL
mysql -u root -p

# Execute SQL file
mysql -u root -p < database.sql

# Backup database
mysqldump -u root -p database_name > backup.sql

# Restore database
mysql -u root -p database_name < backup.sql

# Show database size
SELECT SUM(ROUND(((data_length + index_length) / 1024 / 1024), 2)) 
FROM INFORMATION_SCHEMA.TABLES 
WHERE table_schema = "database_name";
```

---

## Best Practices

1. **Always secure MySQL** - Run mysql_secure_installation
2. **Use strong passwords** - Minimum 12 characters with mixed case and numbers
3. **Create specific users** - Don't use root for applications
4. **Backup regularly** - Automate database backups
5. **Monitor space** - Check disk usage regularly
6. **Update regularly** - Keep MySQL updated for security patches
7. **Use configuration files** - Store connection details in config files, not hardcoded

---

## Configuration File Locations

```bash
# macOS (Homebrew)
/usr/local/etc/my.cnf
/usr/local/etc/mysql/my.cnf

# Windows
C:\ProgramData\MySQL\MySQL Server 8.0\my.ini

# Linux
/etc/mysql/mysql.conf.d/mysqld.cnf
/etc/my.cnf
```

---

## Next Steps

→ Learn [Accessing MySQL from PHP](2-access-mysql.md)
