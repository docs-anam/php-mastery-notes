<?php
/*
MySQL is an open-source relational database management system (RDBMS) widely used for web applications. It stores data in tables and supports SQL (Structured Query Language) for querying and managing data. MySQL is known for its reliability, performance, and ease of use, making it a popular choice for PHP-based applications.

Key Features of MySQL:
- Open-source and free to use.
- Supports large databases.
- Compatible with many programming languages, especially PHP.
- Provides security features like user management and access control.
- Offers replication, clustering, and backup options.

How to Install MySQL:

1. Using MAMP (Recommended for macOS):
    - Download MAMP from https://www.mamp.info/
    - Install MAMP and launch the application.
    - MySQL server is included and can be started/stopped from the MAMP control panel.
    - Default MySQL credentials: 
      - Host: localhost
      - User: root
      - Password: root

2. Manual Installation (macOS, Windows, Linux):
    - Download MySQL Community Server from https://dev.mysql.com/downloads/mysql/
    - Run the installer and follow the setup instructions.
    - Set a root password during installation.
    - Optionally, install MySQL Workbench for a graphical interface.

3. Using Package Managers:
    - macOS (Homebrew): 
      - Run `brew install mysql`
      - Start server: `brew services start mysql`
    - Ubuntu/Debian:
      - Run `sudo apt update`
      - Run `sudo apt install mysql-server`
      - Secure installation: `sudo mysql_secure_installation`
    - Windows (Chocolatey):
      - Run `choco install mysql`

4. Verifying Installation:
    - Open terminal/command prompt.
    - Run `mysql -u root -p` and enter your password.
    - You should see the MySQL prompt.

After installation, you can connect to MySQL from PHP using the `mysqli` or `PDO` extensions.
*/