# Require Include

```php
// Detailed Summary: include vs require in PHP

/*
Both include and require are PHP statements used to insert the contents of one PHP file into another before execution.
They are essential for code reusability, modularity, and easier maintenance.

Key Differences:

1. include:
    - Syntax: include 'filename.php';
    - If the specified file is not found, PHP emits a warning (E_WARNING), but the script continues to execute.
    - Use include when the file is not critical to the application’s execution.

2. require:
    - Syntax: require 'filename.php';
    - If the specified file is not found, PHP emits a fatal error (E_COMPILE_ERROR), and the script stops execution immediately.
    - Use require when the file is essential for the application to run.

3. include_once and require_once:
    - These work like include and require, but ensure the file is included only once, preventing redeclaration errors.
    - Syntax: include_once 'filename.php'; or require_once 'filename.php';

Practical Example:

// header.php
```php

// footer.php
```php

// main.php (this file)

echo "Before including header.php<br>";

// Using include (non-critical file)
include 'header.php'; // If header.php is missing, script continues

echo "Between header and footer<br>";

// Using require (critical file)
require 'footer.php'; // If footer.php is missing, script stops

echo "After including footer.php<br>"; // Runs only if footer.php exists

/*
Expected Output if both files exist:
Before including header.php
Header Section
Between header and footer
Footer Section
After including footer.php

If header.php is missing:
Before including header.php
Warning: include(header.php): failed to open stream...
Between header and footer
Footer Section
After including footer.php

If footer.php is missing:
Before including header.php
Header Section
Between header and footer
Fatal error: require(): Failed opening required 'footer.php'...

Best Practices:
- Use require for files that are necessary for the application (e.g., configuration, database connection).
- Use include for optional files (e.g., templates, optional widgets).
- Use *_once variants to avoid multiple inclusions of the same file.
*/
```

