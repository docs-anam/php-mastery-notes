use Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use Vendor\Package\SomeNamespace\ClassD as D;
use function Vendor\Package\{functionA, functionB, functionC};
use const Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};
use FirstTrait;
use SecondTrait;
use ThirdTrait {

<?php

/**
 * PSR-12: Extended Coding Style Guide
 * 
 * PSR-12 extends and replaces PSR-2, providing a comprehensive coding style guide
 * for PHP code. It aims to reduce cognitive friction when scanning code from
 * different authors.
 * 
 * KEY RULES AND GUIDELINES:
 * ========================
 */

// 1. GENERAL RULES
// - Code MUST follow PSR-1 (Basic Coding Standard)
// - Files MUST use only UTF-8 without BOM for PHP code
// - Files SHOULD either declare symbols OR cause side-effects, but SHOULD NOT do both

// 2. FILES
// - All PHP files MUST use Unix LF (linefeed) line ending only
// - All PHP files MUST end with a non-blank line, terminated with a single LF
// - The closing ?> tag MUST be omitted from files containing only PHP

// 3. LINES
// - There MUST NOT be a hard limit on line length
// - The soft limit on line length MUST be 120 characters
// - Lines SHOULD NOT be longer than 80 characters
// - There MUST NOT be trailing whitespace at the end of lines
// - Blank lines MAY be added to improve readability

// 4. INDENTING
// - Code MUST use 4 spaces for indenting, not tabs

namespace Vendor\Package;


// 5. KEYWORDS AND TYPES
// - PHP keywords MUST be in lower case
// - Short form of type keywords MUST be used (bool instead of boolean, int instead of integer)

class ExtendedCodingStyleExample extends ParentClass implements
    \ArrayAccess,
    \Countable,
    \Serializable
{
    // 6. DECLARE STATEMENTS
    // - When present, there MUST be one space after declare keyword
    // - When present, declare statements MUST have no spaces between parentheses
    
        ThirdTrait::bigTalk insteadof SecondTrait;
        SecondTrait::smallTalk as talk;
    }
    
    // 7. PROPERTIES
    // - Visibility MUST be declared on all properties
    // - The var keyword MUST NOT be used to declare a property
    // - There MUST NOT be more than one property declared per statement
    // - Property names MUST NOT be prefixed with underscore to indicate protected/private
    
    public string $publicProperty;
    protected int $protectedProperty = 10;
    private ?array $privateProperty = null;
    
    // Type declarations
    public static int $staticProperty;
    
    // 8. CONSTANTS
    // - Visibility MUST be declared on all constants if supported by PHP version
    
    public const PUBLIC_CONSTANT = 'public';
    protected const PROTECTED_CONSTANT = 'protected';
    private const PRIVATE_CONSTANT = 'private';
    
    // 9. METHODS AND FUNCTIONS
    // - Visibility MUST be declared on all methods
    // - Method names MUST NOT be prefixed with underscore to indicate protected/private
    // - Opening brace MUST go on its own line
    // - Closing brace MUST go on next line after body
    // - There MUST NOT be space after method name
    
    public function methodName(
        int $arg1,
        string &$arg2,
        array $arg3 = []
    ): void {
        // method body
    }
    
    // Abstract and final declarations MUST precede visibility
    // Static MUST come after visibility
    
    final public static function finalStaticMethod(): void
    {
        // method body
    }
    
    // 10. METHOD AND FUNCTION ARGUMENTS
    // - Argument lists MAY be split across multiple lines (one argument per line)
    // - When doing so, first item MUST be on next line
    // - When doing so, closing parenthesis and opening brace MUST be on same line
    
    public function aVeryLongMethodName(
        ClassTypeHint $arg1,
        &$arg2,
        array $arg3 = []
    ): void {
        // method body
    }
    
    // Return types with nullable
    public function nullableReturnType(): ?string
    {
        return null;
    }
    
    // 11. CONTROL STRUCTURES
    // - There MUST be one space after control structure keyword
    // - There MUST NOT be space after opening parenthesis
    // - There MUST NOT be space before closing parenthesis
    // - There MUST be one space between closing parenthesis and opening brace
    // - Structure body MUST be indented once
    // - Opening brace MUST be on same line as control structure
    // - Closing brace MUST be on next line after body
    
    public function controlStructures($condition, $array): void
    {
        // if, elseif, else
        if ($condition) {
            // code here
        } elseif ($condition) {
            // code here
        } else {
            // code here
        }
        
        // switch, case
        switch ($condition) {
            case 1:
                // code here
                break;
            case 2:
            case 3:
                // code here
                return;
            default:
                // code here
                break;
        }
        
        // while, do while
        while ($condition) {
            // code here
        }
        
        do {
            // code here
        } while ($condition);
        
        // for
        for ($i = 0; $i < 10; $i++) {
            // code here
        }
        
        // foreach
        foreach ($array as $key => $value) {
            // code here
        }
        
        // try, catch, finally
        try {
            // code here
        } catch (FirstThrowableType $e) {
            // code here
        } catch (OtherThrowableType | AnotherThrowableType $e) {
            // code here
        } finally {
            // code here
        }
    }
    
    // 12. OPERATORS
    // - Unary operators MUST NOT have space
    // - Binary operators MUST be surrounded by at least one space
    
    public function operators(): void
    {
        $i = 0;
        $i++;
        ++$i;
        
        $result = $i + 10;
        $result = $i * 2;
        
        // Ternary operators
        $variable = $condition ? 'true' : 'false';
        
        // When split across multiple lines
        $variable = $condition
            ? 'true'
            : 'false';
    }
    
    // 13. CLOSURES
    // - MUST be declared with space after function keyword
    // - MUST be declared with space before and after use keyword
    
    public function closures(): void
    {
        $closureWithArgs = function ($arg1, $arg2) {
            // body
        };
        
        $closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
            // body
        };
        
        $closureWithArgsVarsAndReturn = function ($arg1, $arg2) use ($var1, $var2): bool {
            // body
            return true;
        };
        
        // Multi-line arguments
        $longArgs_noVars = function (
            $longArgument,
            $longerArgument,
            $muchLongerArgument
        ) {
            // body
        };
        
        $noArgs_longVars = function () use (
            $longVar1,
            $longerVar2,
            $muchLongerVar3
        ) {
            // body
        };
        
        // Used as function argument
        $foo->bar(
            $arg1,
            function ($arg2) use ($var1) {
                // body
            },
            $arg3
        );
    }
    
    // 14. ANONYMOUS CLASSES
    // - Follow same guidelines as closures and classes
    
    public function anonymousClasses(): void
    {
        $instance = new class extends \Exception implements \ArrayAccess {
            public function offsetExists($offset): bool
            {
                return false;
            }
            
            public function offsetGet($offset): mixed
            {
                return null;
            }
            
            public function offsetSet($offset, $value): void
            {
                // code
            }
            
            public function offsetUnset($offset): void
            {
                // code
            }
        };
    }
}

/**
 * KEY BENEFITS OF PSR-12:
 * ======================
 * 1. Consistency across codebases
 * 2. Reduced cognitive friction when reading others' code
 * 3. Better collaboration in teams
 * 4. Easier code reviews
 * 5. Improved code maintainability
 * 6. Industry-standard compliance
 * 
 * TOOLS FOR ENFORCEMENT:
 * =====================
 * - PHP_CodeSniffer (phpcs) - Detection
 * - PHP-CS-Fixer - Automatic fixing
 * - PHPStan - Static analysis
 * - IDE configurations (PhpStorm, VS Code)
 */

// Note: The closing PHP tag is omitted intentionally per PSR-12