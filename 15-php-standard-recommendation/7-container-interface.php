use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

<?php

/**
 * PSR-11: Container Interface
 * 
 * PSR-11 defines a common interface for dependency injection containers.
 * It provides a standardized way to retrieve entries from a container.
 * 
 * MAIN COMPONENTS:
 * 
 * 1. Psr\Container\ContainerInterface
 *    - get(string $id): mixed - Retrieves an entry from the container
 *    - has(string $id): bool - Checks if container can return an entry
 * 
 * 2. Psr\Container\ContainerExceptionInterface
 *    - Base exception interface for container-related errors
 * 
 * 3. Psr\Container\NotFoundExceptionInterface
 *    - Thrown when an entry is not found in the container
 * 
 * KEY CONCEPTS:
 * 
 * - Entry Identifier: A string that uniquely identifies an entry (usually class name)
 * - Service Location: Containers act as service locators
 * - Interoperability: Different DI containers can implement same interface
 * - Type Safety: get() can throw exceptions if entry not found
 * 
 * BENEFITS:
 * - Framework independence
 * - Library reusability across different containers
 * - Standardized error handling
 * - Improved code portability
 */

// Example: Basic Container Interface implementation

class NotFoundException extends Exception implements NotFoundExceptionInterface {}

class SimpleContainer implements ContainerInterface
{
    private array $services = [];

    public function __construct()
    {
        // Register some services
        $this->services['database'] = function() {
            return new PDO('mysql:host=localhost;dbname=test', 'root', '');
        };
        
        $this->services['mailer'] = function() {
            return new class {
                public function send($to, $message) {
                    echo "Sending email to {$to}: {$message}\n";
                }
            };
        };
        
        $this->services['logger'] = function($container) {
            return new class {
                public function log($message) {
                    echo "[LOG] " . date('Y-m-d H:i:s') . ": {$message}\n";
                }
            };
        };
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Service '{$id}' not found in container");
        }

        $service = $this->services[$id];
        
        // If it's a factory (callable), execute it
        if (is_callable($service)) {
            $this->services[$id] = $service($this);
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}

// Example usage
$container = new SimpleContainer();

// Check if service exists
if ($container->has('mailer')) {
    $mailer = $container->get('mailer');
    $mailer->send('user@example.com', 'Hello from PSR-11!');
}

// Using logger
$logger = $container->get('logger');
$logger->log('Application started');

// This will throw NotFoundException
try {
    $nonExistent = $container->get('nonexistent-service');
} catch (NotFoundExceptionInterface $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

/**
 * REAL-WORLD USAGE:
 * 
 * Popular frameworks/libraries implementing PSR-11:
 * - Laravel Container
 * - Symfony DependencyInjection
 * - PHP-DI
 * - Pimple
 * - League Container
 * 
 * BEST PRACTICES:
 * 
 * 1. Use has() before get() to avoid exceptions
 * 2. Prefer constructor injection over service location
 * 3. Use container only at application entry points
 * 4. Keep service identifiers consistent (use class names)
 * 5. Document your container entries
 * 
 * INSTALLATION:
 * composer require psr/container
 */

// Example: Using container with dependency injection
class UserController
{
    private $logger;
    private $mailer;

    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get('logger');
        $this->mailer = $container->get('mailer');
    }

    public function register($email)
    {
        $this->logger->log("New user registration: {$email}");
        $this->mailer->send($email, 'Welcome to our platform!');
    }
}

// Using the controller
$controller = new UserController($container);
$controller->register('newuser@example.com');

?>