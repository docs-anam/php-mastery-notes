# 🚀 PHP Mastery Notes

A comprehensive personal learning repository covering PHP from fundamentals to advanced concepts, including modern PHP 8.x features, OOP, databases, web development, testing, and real-world application development.

## 📚 Table of Contents

- [About](#-about)
- [Topics Covered](#-topics-covered)
- [Prerequisites](#-prerequisites)
- [How to Use](#-how-to-use)
- [Repository Structure](#-repository-structure)
- [Learning Path](#-learning-path)
- [Contributing](#-contributing)

## 💡 About

This repository serves as a structured learning path and reference guide for mastering PHP development. It covers everything from basic syntax to advanced design patterns, modern PHP 8.x features, and real-world application development with testing and best practices.

## 🎯 Topics Covered

### 1. **Basics** (`1-basics/`)
- Installation & setup
- Data types (numbers, booleans, strings, arrays, null)
- Variables and constants
- Operators (arithmetic, assignment, comparison, logical, array)
- Control structures (if, switch, ternary, null coalescing)
- Loops (for, while, do-while, foreach)
- Functions and built-in functions
- String manipulation
- Regular expressions
- Variable scope and references
- Include/require statements
- Practical: Todo List Application

### 2. **Object-Oriented Programming** (`2-oop/`)
- Classes and objects
- Constructors and destructors
- Properties and methods
- Visibility (public, private, protected)
- Inheritance and polymorphism
- Namespaces and imports
- Abstract classes and interfaces
- Traits and final classes
- Static members
- Anonymous classes
- Type checking and casting
- Getters and setters
- stdClass

### 3. **PHP 8.0 Features** (`3-php8/`)
- Named arguments
- Constructor property promotion
- Attributes
- Union types
- Match expressions
- Nullsafe operator
- JIT compilation
- String to number comparison improvements
- Throw expressions
- Stringable interface
- And more modern features

### 4. **PHP & MySQL** (`4-php-mysql/`)
- MySQL installation and setup
- PHP Data Objects (PDO)
- Database connections
- Executing SQL queries
- Prepared statements
- SQL injection prevention
- Fetching data
- Auto-increment
- Database transactions
- Repository pattern
- Practical: Todo List Application

### 5. **Web Development** (`5-web/`)
- Client-server architecture
- PHP web fundamentals
- Development server setup
- HTML integration
- Global variables ($_SERVER, etc.)
- Query parameters
- Form handling (POST)
- Headers and response codes
- Sessions and cookies
- File uploads and downloads
- XSS prevention
- Best practices for URLs

### 6. **Composer** (`6-composer/`)
- Composer installation
- Project initialization
- Autoloading
- Dependency management
- Creating libraries
- Publishing to GitHub
- Publishing to Packagist
- Version management
- Repository configuration

### 7. **Unit Testing** (`7-unit-test/`)
- Software testing fundamentals
- PHPUnit introduction
- Writing test cases
- Assertions
- Test attributes
- Test dependencies
- Data providers
- Testing exceptions
- Fixtures and sharing
- Stubs and mock objects
- Configuration and test suites
- Test coverage

### 8. **PHP MVC Pattern** (`8-php-mvc/`)
- MVC architecture
- Routing systems
- Controllers
- Models
- Views and templates
- Path variables
- Middleware
- Local domain setup
- Practical: MVC Project

### 9. **Study Case: Login Management** (`9-study-case/`)
Complete application development including:
- Project setup and architecture
- Database design and connection
- User registration (repository, service, controller)
- User login and logout
- Session management
- Password hashing
- Profile management
- Middleware authentication
- Comprehensive unit testing
- Manual testing
- Complete login management system

### 10. **PHP Logging** (`10-php-logging/`)
- Logging fundamentals
- Monolog library
- Logger configuration
- Handlers and processors
- Log levels
- Context information
- Formatters
- Rotating file handlers
- Practical: MVC Project with logging

### 11. **PHP 8.1 Features** (`11-php8.1/`)
- Enumerations
- Readonly properties
- First-class callable syntax
- New in initializer
- Pure intersection types
- Never return type
- Final class constants

### 12. **PHP Standard Recommendation** (`12-php-standard-recommendation/`)
- PSR introduction and overview
- PSR-1: Basic Coding Standard
- PSR-3: Logger Interface
- PSR-4: Autoloading Standard
- PSR-6: Caching Interface
- PSR-7: HTTP Message Interface
- PSR-11: Container Interface
- PSR-12: Extended Coding Style Guide
- PSR-13: Hypermedia Links
- PSR-14: Event Dispatcher
- PSR-15: HTTP Handlers (Middleware)
- PSR-16: Simple Cache
- PSR-17: HTTP Factories
- PSR-18: HTTP Client
- PSR-20: Clock Interface

### 13. **PHP 8.2 Features** (`13-php8.2/`)
- Latest PHP 8.2 improvements

### 14. **PHP 8.3 Features** (`14-php8.3/`)
- Latest PHP 8.3 improvements

### 15. **PHP 8.4 Features** (`15.php8.4/`)
- Latest PHP 8.4 improvements

### 16. **PHP 8.5 Features** (`16-php8.5/`)
- Upcoming PHP 8.5 features

### Sample Projects (`99-sample-projects/`)
- Real-world application examples

## 📋 Prerequisites

- PHP 7.4 or higher (PHP 8.x recommended)
- MAMP, XAMPP, or similar local server environment
- Composer for dependency management
- MySQL or MariaDB for database examples
- Basic understanding of programming concepts

## 🚀 How to Use

### Clone the Repository
```bash
git clone [your-repo-url]
cd php-mastery-notes
```

### Navigate and Run Examples
```bash
# Navigate to a topic folder
cd 1-basics/

# Run individual PHP files
php 2-hello-world.php

# Or use MAMP/XAMPP and access via browser
# http://localhost:8888/php-mastery-notes/1-basics/2-hello-world.php
```

### For Projects with Composer
```bash
cd 6-composer/
composer install
php -f app.php
```

### Running Tests
```bash
cd 7-unit-test/
composer install
vendor/bin/phpunit
```

## 📁 Repository Structure

Each section is organized numerically for progressive learning:
- Each `.php` file contains working code examples with comments
- Folders with `99-` prefix contain practical projects and case studies
- Projects include both source code (`src/`) and tests (`tests/`)

## 🎓 Learning Path

**Recommended progression for beginners:**

1. Start with **Basics** (1-basics/) - Build fundamental understanding
2. Master **OOP** (2-oop/) - Object-oriented programming concepts
3. Learn **PHP 8.x features** (3-php8/, 11-php8.1/) - Modern PHP syntax
4. Study **PHP & MySQL** (4-php-mysql/) - Database integration
5. Explore **Web Development** (5-web/) - HTTP, sessions, forms
6. Setup **Composer** (6-composer/) - Dependency management
7. Practice **Unit Testing** (7-unit-test/) - TDD principles
8. Build **MVC Applications** (8-php-mvc/) - Design patterns
9. Complete **Study Case** (9-study-case/) - Full application
10. Implement **Logging** (10-php-logging/) - Production practices

## 🤝 Contributing

Contributions are welcome! If you find any issues or have suggestions:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/improvement`)
3. Commit your changes (`git commit -m 'Add some improvement'`)
4. Push to the branch (`git push origin feature/improvement`)
5. Open a Pull Request

**Guidelines:**
- Ensure code follows PSR standards
- Add comments explaining complex concepts
- Include practical examples
- Test code before submitting

## 📝 License

This project is open source and available for educational purposes.

## 🙏 Acknowledgments

Created as a learning resource for PHP developers at all levels. Feel free to use these notes for your own learning journey!

---

**Happy Coding! 🎉**  