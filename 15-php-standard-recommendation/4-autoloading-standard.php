<?php

/**
 * PSR-4: Autoloading Standard - Detailed Summary
 * 
 * PSR-4 is the recommended autoloading standard that maps namespaces to file paths.
 * It replaces the older PSR-0 standard with a simpler and more efficient approach.
 * 
 * KEY CONCEPTS:
 * 
 * 1. NAMESPACE TO DIRECTORY MAPPING
 *    - Each namespace prefix maps to a base directory
 *    - Namespace separators (\) correspond to directory separators
 *    - Class names map directly to .php files
 * 
 * 2. STRUCTURE RULES
 *    - Fully qualified class name: \Vendor\Package\ClassName
 *    - Namespace prefix: Vendor\Package
 *    - Base directory: /path/to/src/
 *    - Resulting file: /path/to/src/ClassName.php
 * 
 * 3. REQUIREMENTS
 *    - Class files MUST use <?php tag
 *    - Class files MUST use UTF-8 encoding without BOM
 *    - File names MUST match the case of the class name
 *    - Namespaces MUST be case-sensitive
 * 
 * 4. BENEFITS
 *    - Eliminates need for require/include statements
 *    - Improves performance (loads only needed classes)
 *    - Standardizes project structure
 *    - Works seamlessly with Composer
 */

// Example 1: Basic PSR-4 Autoloader Implementation
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'App\\';
    
    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/src/';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Move to the next registered autoloader
        return;
    }
    
    // Get the relative class name
    $relativeClass = substr($class, $len);
    
    // Replace namespace separators with directory separators
    // Add .php extension
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Example 2: Multiple Namespace Prefixes
class Psr4Autoloader
{
    protected $prefixes = [];
    
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }
    
    public function addNamespace($prefix, $baseDir, $prepend = false)
    {
        // Normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';
        
        // Normalize base directory with trailing separator
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';
        
        // Initialize the namespace prefix array
        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = [];
        }
        
        // Retain or prepend the base directory
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $baseDir);
        } else {
            array_push($this->prefixes[$prefix], $baseDir);
        }
    }
    
    public function loadClass($class)
    {
        // Current namespace prefix
        $prefix = $class;
        
        // Work backwards through namespace names to find mapped file
        while (false !== $pos = strrpos($prefix, '\\')) {
            // Retain the trailing namespace separator
            $prefix = substr($class, 0, $pos + 1);
            
            // The rest is the relative class name
            $relativeClass = substr($class, $pos + 1);
            
            // Try to load a mapped file
            $mappedFile = $this->loadMappedFile($prefix, $relativeClass);
            if ($mappedFile) {
                return $mappedFile;
            }
            
            // Remove trailing separator for next iteration
            $prefix = rtrim($prefix, '\\');
        }
        
        return false;
    }
    
    protected function loadMappedFile($prefix, $relativeClass)
    {
        // Are there any base directories for this prefix?
        if (!isset($this->prefixes[$prefix])) {
            return false;
        }
        
        // Look through base directories
        foreach ($this->prefixes[$prefix] as $baseDir) {
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            
            if ($this->requireFile($file)) {
                return $file;
            }
        }
        
        return false;
    }
    
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}

// Example 3: Usage with Multiple Namespaces
$loader = new Psr4Autoloader();
$loader->addNamespace('App\\Controllers\\', __DIR__ . '/app/controllers');
$loader->addNamespace('App\\Models\\', __DIR__ . '/app/models');
$loader->addNamespace('App\\Views\\', __DIR__ . '/app/views');
$loader->addNamespace('Vendor\\Library\\', __DIR__ . '/vendor/library/src');
$loader->register();

// Example 4: Composer's PSR-4 Configuration (composer.json)
/*
{
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "App\\Controllers\\": "app/controllers/",
            "Tests\\": "tests/"
        }
    }
}

After configuration, run: composer dump-autoload
*/

// Example 5: Directory Structure Example
/*
project/
├── src/
│   ├── Database/
│   │   └── Connection.php      (App\Database\Connection)
│   ├── Models/
│   │   └── User.php            (App\Models\User)
│   └── Controllers/
│       └── UserController.php  (App\Controllers\UserController)
├── vendor/
│   └── autoload.php            (Composer autoloader)
└── index.php
*/

// Example 6: Using Composer Autoloader
// require __DIR__ . '/vendor/autoload.php';
// use App\Models\User;
// use App\Controllers\UserController;
// $user = new User();
// $controller = new UserController();

/**
 * PSR-4 vs PSR-0 DIFFERENCES:
 * 
 * PSR-0 (Deprecated):
 * - Class: Vendor_Package_Class → Vendor/Package/Class.php
 * - Underscores in class names become directory separators
 * 
 * PSR-4 (Current):
 * - Class: Vendor\Package\Class → Vendor/Package/Class.php
 * - Only namespace separators become directory separators
 * - Underscores in class names remain as underscores
 * - More efficient and cleaner structure
 * 
 * BEST PRACTICES:
 * 1. Use Composer for autoloading in production
 * 2. Follow one class per file convention
 * 3. Match file names exactly with class names (case-sensitive)
 * 4. Use meaningful namespace structures
 * 5. Keep vendor code separate from application code
 */