<?php

/**
 * Final Class Constants in PHP 8.1
 * 
 * PHP 8.1 introduced the ability to mark class constants as 'final'.
 * This prevents child classes from overriding the constant value.
 * 
 * Key Points:
 * - Prevents constant overriding in child classes
 * - Works similar to final methods
 * - Useful for maintaining contract integrity
 * - Only applies to class constants (not global constants)
 * - Can be combined with visibility modifiers (public, protected, private)
 */

// Example 1: Basic final constant
class ParentClass
{
    final public const STATUS = 'active';
    public const TYPE = 'standard';
}

class ChildClass extends ParentClass
{
    // This will cause a fatal error - cannot override final constant
    // public const STATUS = 'inactive'; // Fatal error!
    
    // This is allowed - TYPE is not final
    public const TYPE = 'premium'; // Works fine
}

// Example 2: Final with different visibility modifiers
class Configuration
{
    final public const APP_NAME = 'MyApp';
    final protected const SECRET_KEY = 'xyz123';
    private const INTERNAL_CODE = 'abc'; // Private constants cannot be final
    
    public const VERSION = '1.0'; // Not final, can be overridden
}

// Example 3: Practical use case
abstract class Database
{
    // These constants should never change in child classes
    final public const FETCH_ASSOC = 1;
    final public const FETCH_NUM = 2;
    final public const FETCH_OBJ = 3;
    
    // These can be customized per implementation
    protected const DEFAULT_CHARSET = 'utf8mb4';
}

class MySQLDatabase extends Database
{
    // Cannot override final constants
    // final public const FETCH_ASSOC = 10; // Fatal error!
    
    // Can override non-final constants
    protected const DEFAULT_CHARSET = 'latin1'; // Works
}

// Example 4: Interface constants remain overridable
interface PaymentInterface
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
}

class Payment implements PaymentInterface
{
    // Interface constants can still be overridden
    public const STATUS_PENDING = 'waiting';
}

// Benefits of final constants:
// 1. Prevents accidental overriding of critical values
// 2. Ensures API contract consistency
// 3. Makes code intent clearer
// 4. Useful for enum-like constants that shouldn't change

echo "Final class constants prevent child classes from overriding important values.\n";
echo "ParentClass::STATUS = " . ParentClass::STATUS . "\n";
echo "ChildClass::STATUS = " . ChildClass::STATUS . "\n";
echo "ChildClass::TYPE = " . ChildClass::TYPE . " (overridden)\n";
