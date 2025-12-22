<?php

/**
 * PSR-1: Basic Coding Standard - Detailed Summary
 * 
 * PSR-1 establishes fundamental coding standards for PHP code to ensure
 * a high level of technical interoperability between shared PHP code.
 */

// ============================================================================
// 1. FILES
// ============================================================================

// 1.1 PHP Tags
// - Files MUST use only <?php and <?= tags
// - MUST NOT use other tag variations like <? or <%

// Correct: Standard PHP opening tag
echo "Hello World";

<?= $variable ?>
// Correct: Short echo tag (allowed)

// 1.2 Character Encoding
// - Files MUST use only UTF-8 without BOM for PHP code

// 1.3 Side Effects
// - A file SHOULD declare symbols (classes, functions, constants)
//   OR cause side-effects (generate output, change settings)
//   but SHOULD NOT do both

// Bad Example - Mixed declarations and side effects:
ini_set('display_errors', 1);
class MyClass {} // Don't mix these

// Good Example - Separate files:
// config.php - only side effects
// MyClass.php - only declarations


// ============================================================================
// 2. NAMESPACE AND CLASS NAMES
// ============================================================================

// 2.1 Namespaces and Classes
// - MUST follow PSR-0/PSR-4 autoloading standard
// - Each class must be in its own file
// - Must be in a namespace of at least one level (vendor name)

namespace Vendor\Model;

class User
{
    // Class implementation
}

// 2.2 Class Names
// - MUST be declared in StudlyCaps (PascalCase)

class MyClassName {} // Correct
class UserProfile {} // Correct
class HTTPRequest {} // Correct


// ============================================================================
// 3. CLASS CONSTANTS, PROPERTIES, AND METHODS
// ============================================================================

// 3.1 Constants
// - MUST be declared in UPPER_CASE with underscore separators

class Config
{
    const VERSION = '1.0';
    const DATE_APPROVED = '2012-01-01';
    const MAX_UPLOAD_SIZE = 1024;
}

// 3.2 Properties
// - Can use $StudlyCaps, $camelCase, or $under_score
// - MUST be consistent within a scope (vendor, package, class, method)
// - Recommendation: use $camelCase

class Example
{
    public $userName;      // camelCase (recommended)
    protected $firstName;
    private $lastName;
}

// 3.3 Methods
// - MUST be declared in camelCase()

class Calculator
{
    public function addNumbers($a, $b)
    {
        return $a + $b;
    }
    
    protected function validateInput($input)
    {
        return is_numeric($input);
    }
    
    private function logOperation($operation)
    {
        // Log the operation
    }
}


// ============================================================================
// 4. COMPLETE EXAMPLE FOLLOWING PSR-1
// ============================================================================

namespace Vendor\Package;

/**
 * Example class demonstrating PSR-1 compliance
 */
class ExampleClass
{
    // Constants in UPPER_CASE
    const DEFAULT_STATUS = 'active';
    const MAX_RETRY_COUNT = 3;
    
    // Properties in camelCase
    public $publicProperty;
    protected $protectedProperty;
    private $privateProperty;
    
    // Methods in camelCase
    public function exampleMethod($param1, $param2)
    {
        return $param1 + $param2;
    }
    
    protected function helperMethod()
    {
        // Implementation
    }
    
    private function internalMethod()
    {
        // Implementation
    }
}

/**
 * KEY POINTS SUMMARY:
 * 
 * 1. Files:
 *    - Use <?php or <?= tags only
 *    - UTF-8 without BOM
 *    - Avoid mixing declarations and side effects
 * 
 * 2. Naming Conventions:
 *    - Classes: StudlyCaps (PascalCase)
 *    - Methods: camelCase()
 *    - Constants: UPPER_CASE
 *    - Properties: camelCase (recommended)
 * 
 * 3. Structure:
 *    - One class per file
 *    - Use namespaces (minimum one level)
 *    - Follow autoloading standards
 * 
 * 4. Compliance ensures:
 *    - Better interoperability
 *    - Easier code sharing
 *    - Consistent coding style
 *    - Improved maintainability
 */