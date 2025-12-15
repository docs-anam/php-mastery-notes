<?php

/**
 * New in Initializer - PHP 8.1
 * 
 * PHP 8.1 introduced the ability to use 'new' expressions in parameter default values,
 * attribute arguments, and other initializer contexts where previously only constant
 * expressions were allowed.
 * 
 * Key Features:
 * 1. Use 'new' in default parameter values
 * 2. Use 'new' in attribute arguments
 * 3. Use 'new' in static variable initialization
 * 4. Use 'new' in global constant initialization
 * 5. Use 'new' in class constant initialization
 */

// ============================================================================
// 1. Default Parameter Values
// ============================================================================

class Logger
{
    public function __construct(public string $name = 'default') {}
}

// Before PHP 8.1: Not possible
// After PHP 8.1: You can use 'new' in default parameters
function processData(Logger $logger = new Logger('app'))
{
    echo "Using logger: {$logger->name}\n";
}

processData(); // Uses default Logger with name 'app'
processData(new Logger('custom')); // Uses custom Logger

// ============================================================================
// 2. Attribute Arguments
// ============================================================================

class Route
{
    public function __construct(
        public string $path,
        public array $methods = []
    ) {}
}

#[Route(new Route('/api/users', ['GET', 'POST']))]
class UserController
{
    // Controller implementation
}

// ============================================================================
// 3. Static Variable Initialization
// ============================================================================

class Counter
{
    public function __construct(public int $value = 0) {}
    
    public function increment(): int
    {
        return ++$this->value;
    }
}

function getCounter(): Counter
{
    static $counter = new Counter(100);
    return $counter;
}

echo getCounter()->increment() . "\n"; // 101
echo getCounter()->increment() . "\n"; // 102

// ============================================================================
// 4. Class Constants with New
// ============================================================================

class Config
{
    public function __construct(public string $env = 'production') {}
}

class Application
{
    private static ?Config $defaultConfig = null;
    
    public static function getConfig(): Config
    {
        if (self::$defaultConfig === null) {
            self::$defaultConfig = new Config('development');
        }
        return self::$defaultConfig;
    }
}

echo Application::getConfig()->env . "\n"; // development

// ============================================================================
// 5. Promoted Properties with Default Objects
// ============================================================================

class Database
{
    public function __construct(
        public string $host = 'localhost',
        public int $port = 3306
    ) {}
}

class Service
{
    public function __construct(
        private Database $db = new Database('127.0.0.1', 5432)
    ) {}
    
    public function getDbInfo(): string
    {
        return "{$this->db->host}:{$this->db->port}";
    }
}

$service = new Service();
echo $service->getDbInfo() . "\n"; // 127.0.0.1:5432

// ============================================================================
// 6. Practical Example: Dependency Injection
// ============================================================================

class CacheInterface
{
    public function __construct(public int $ttl = 3600) {}
}

class FileCache extends CacheInterface {}

class UserRepository
{
    public function __construct(
        private CacheInterface $cache = new FileCache(7200)
    ) {}
    
    public function getCacheTtl(): int
    {
        return $this->cache->ttl;
    }
}

$repo = new UserRepository();
echo "Cache TTL: " . $repo->getCacheTtl() . " seconds\n"; // 7200

// ============================================================================
// Important Notes:
// ============================================================================

/**
 * Limitations:
 * - The object is created once per function/method call (for parameters)
 * - For static variables and constants, the object is created once
 * - Cannot use variables or expressions that depend on runtime values
 * - The class must be available at compile time
 * 
 * Benefits:
 * - Cleaner code with fewer null checks
 * - Better default value semantics
 * - Simplified dependency injection patterns
 * - More expressive attribute usage
 */

?>