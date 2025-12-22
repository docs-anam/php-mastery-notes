use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

<?php

/**
 * PSR-14: Event Dispatcher
 * 
 * PSR-14 defines a standard way to dispatch and listen to events in PHP applications.
 * It promotes loose coupling between components by allowing them to communicate through events.
 * 
 * KEY COMPONENTS:
 * ===============
 * 
 * 1. Event
 *    - Any PHP object can be an event
 *    - Contains data relevant to what happened
 *    - Can be stoppable (implements StoppableEventInterface)
 * 
 * 2. Listener
 *    - Any PHP callable
 *    - Receives the event object
 *    - Can modify the event if it's mutable
 * 
 * 3. Event Dispatcher (EventDispatcherInterface)
 *    - Receives an event and dispatches it to all relevant listeners
 *    - Returns the same event (possibly modified by listeners)
 * 
 * 4. Listener Provider (ListenerProviderInterface)
 *    - Responsible for determining which listeners are relevant for a given event
 *    - Returns an iterable of callables
 * 
 * INTERFACES:
 * ===========
 */


/**
 * EXAMPLE 1: Basic Event Class
 */
class UserRegisteredEvent
{
    public function __construct(
        private string $email,
        private int $userId,
        private \DateTimeImmutable $registeredAt
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }
}

/**
 * EXAMPLE 2: Stoppable Event
 * Allows listeners to stop propagation to other listeners
 */
class OrderProcessingEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function __construct(
        private int $orderId,
        private float $amount
    ) {}

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}

/**
 * EXAMPLE 3: Simple Listener Provider Implementation
 */
class SimpleListenerProvider implements ListenerProviderInterface
{
    private array $listeners = [];

    public function addListener(string $eventType, callable $listener): void
    {
        $this->listeners[$eventType][] = $listener;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $eventType = get_class($event);
        return $this->listeners[$eventType] ?? [];
    }
}

/**
 * EXAMPLE 4: Simple Event Dispatcher Implementation
 */
class SimpleEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private ListenerProviderInterface $listenerProvider
    ) {}

    public function dispatch(object $event): object
    {
        $listeners = $this->listenerProvider->getListenersForEvent($event);

        foreach ($listeners as $listener) {
            // Check if event is stoppable and propagation has been stopped
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }
}

/**
 * USAGE EXAMPLES:
 * ===============
 */

// Create listener provider
$provider = new SimpleListenerProvider();

// Register listeners
$provider->addListener(UserRegisteredEvent::class, function (UserRegisteredEvent $event) {
    echo "Sending welcome email to: {$event->getEmail()}\n";
});

$provider->addListener(UserRegisteredEvent::class, function (UserRegisteredEvent $event) {
    echo "Creating user profile for ID: {$event->getUserId()}\n";
});

$provider->addListener(UserRegisteredEvent::class, function (UserRegisteredEvent $event) {
    echo "Logging registration at: {$event->getRegisteredAt()->format('Y-m-d H:i:s')}\n";
});

// Create dispatcher
$dispatcher = new SimpleEventDispatcher($provider);

// Dispatch event
echo "=== User Registration Event ===\n";
$event = new UserRegisteredEvent('user@example.com', 123, new \DateTimeImmutable());
$dispatcher->dispatch($event);

echo "\n";

/**
 * EXAMPLE WITH STOPPABLE EVENT:
 */
$provider->addListener(OrderProcessingEvent::class, function (OrderProcessingEvent $event) {
    echo "Listener 1: Processing order #{$event->getOrderId()}\n";
    
    if ($event->getAmount() > 1000) {
        echo "Order amount too high! Stopping propagation.\n";
        $event->stopPropagation();
    }
});

$provider->addListener(OrderProcessingEvent::class, function (OrderProcessingEvent $event) {
    echo "Listener 2: This will not execute if propagation is stopped\n";
});

echo "=== Order Processing Event ===\n";
$orderEvent = new OrderProcessingEvent(456, 1500.00);
$dispatcher->dispatch($orderEvent);

/**
 * BENEFITS OF PSR-14:
 * ===================
 * 
 * 1. Decoupling: Components don't need to know about each other
 * 2. Flexibility: Easy to add/remove listeners without changing core code
 * 3. Testability: Events and listeners can be tested independently
 * 4. Reusability: Standard interface allows library interoperability
 * 5. Maintainability: Clear separation of concerns
 * 
 * COMMON USE CASES:
 * =================
 * 
 * - User authentication/registration workflows
 * - Order processing in e-commerce
 * - Logging and monitoring
 * - Cache invalidation
 * - Sending notifications
 * - Plugin/extension systems
 * - Domain events in DDD (Domain-Driven Design)
 * 
 * POPULAR IMPLEMENTATIONS:
 * ========================
 * 
 * - Symfony EventDispatcher
 * - Laravel Events
 * - League Event
 * - Laminas EventManager (with PSR-14 adapter)
 */