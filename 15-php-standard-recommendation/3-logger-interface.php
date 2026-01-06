use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

<?php
/**
 * PSR-3: Logger Interface - Detailed Summary
 * 
 * PSR-3 defines a common interface for logging libraries in PHP, ensuring interoperability
 * between different logging implementations.
 * 
 * KEY CONCEPTS:
 * 
 * 1. LoggerInterface
 *    - Defines 8 methods for RFC 5424 log levels:
 *      * emergency() - System is unusable
 *      * alert()     - Action must be taken immediately
 *      * critical()  - Critical conditions
 *      * error()     - Error conditions
 *      * warning()   - Warning conditions
 *      * notice()    - Normal but significant condition
 *      * info()      - Informational messages
 *      * debug()     - Debug-level messages
 *    - Plus log() method that accepts level as first parameter
 * 
 * 2. Message Placeholders
 *    - Use {key} syntax for context interpolation
 *    - Placeholders MUST correspond to keys in context array
 *    - Example: "User {username} logged in" with ['username' => 'john']
 * 
 * 3. Context Array
 *    - Second parameter for all logging methods
 *    - Can contain any extraneous information
 *    - Exception objects SHOULD go in 'exception' key
 * 
 * 4. Implementation Requirements
 *    - MUST implement Psr\Log\LoggerInterface
 *    - MUST accept strings or objects with __toString()
 *    - MUST NOT throw exceptions unless absolutely necessary
 *    - Context array can contain any type of data
 * 
 * BENEFITS:
 * - Library interoperability
 * - Easy logger swapping without code changes
 * - Consistent logging API across projects
 * - Framework-agnostic logging
 */

// Installation: composer require psr/log


// Example: Custom Logger Implementation
class FileLogger implements LoggerInterface
{
    private string $logFile;

    public function __construct(string $logFile)
    {
        $this->logFile = $logFile;
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $message = $this->interpolate($message, $context);
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }

    private function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }
}

// Usage Examples
$logger = new FileLogger(__DIR__ . '/app.log');

// Basic logging
$logger->info('User logged in');
$logger->error('Database connection failed');

// Logging with context
$logger->info('User {username} logged in from {ip}', [
    'username' => 'john_doe',
    'ip' => '192.168.1.1'
]);

// Logging exceptions
try {
    throw new Exception('Something went wrong');
} catch (Exception $e) {
    $logger->error('Application error: {message}', [
        'message' => $e->getMessage(),
        'exception' => $e
    ]);
}

// Different severity levels
$logger->debug('Debug information for developers');
$logger->notice('Notable event occurred');
$logger->warning('Deprecated function called');
$logger->critical('Database server down');
$logger->alert('Website is down');
$logger->emergency('Entire system is unusable');

/**
 * POPULAR PSR-3 IMPLEMENTATIONS:
 * 
 * - Monolog (most popular)
 * - Analog
 * - KLogger
 * - Log4php
 * 
 * BEST PRACTICES:
 * 
 * 1. Use appropriate log levels
 * 2. Include relevant context information
 * 3. Don't log sensitive data (passwords, tokens)
 * 4. Use placeholders instead of string concatenation
 * 5. Type-hint LoggerInterface for dependency injection
 * 6. Configure different handlers per environment (dev/prod)
 */