# PSR-14: Event Dispatcher

## Overview

Learn about PSR-14, the standardized event dispatcher interface that enables loose coupling through the Observer pattern and event-driven architecture.

---

## Table of Contents

1. What is PSR-14
2. Core Concepts
3. Events
4. Listeners
5. Dispatch Process
6. Implementation
7. Common Patterns
8. Real-world Examples
9. Complete Examples

---

## What is PSR-14

### Purpose

```php
<?php
// Before PSR-14: Tightly coupled event handling

class UserService
{
    public function register(string $name, string $email): User
    {
        $user = new User($name, $email);

        // Tightly coupled to implementations!
        $emailService = new EmailService();
        $emailService->sendWelcomeEmail($user);

        $auditLog = new AuditLog();
        $auditLog->log("User registered: {$name}");

        $cache = new Cache();
        $cache->invalidate('users_list');

        return $user;
    }
}

// Problems:
// - Tightly coupled
// - Hard to add/remove handlers
// - Difficult to test
// - Logic scattered

// Solution: PSR-14 (standardized event dispatcher)

use Psr\EventDispatcher\EventDispatcherInterface;

class UserService
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function register(string $name, string $email): User
    {
        $user = new User($name, $email);

        // Dispatch event - handlers registered elsewhere
        $this->dispatcher->dispatch(new UserRegistered($user));

        return $user;
    }
}

// Benefits:
// ✓ Loose coupling
// ✓ Easy to extend
// ✓ Testable
// ✓ Clean separation
```

### Key Interfaces

```php
<?php
// EventDispatcherInterface
// - dispatch(object): object

// StoppableEventInterface
// - isPropagationStopped(): bool

// ListenerProviderInterface
// - getListenersForEvent(object): iterable
```

---

## Core Concepts

### Event-Driven Architecture

```php
<?php
// Events represent significant state changes

// Event: User registered
class UserRegistered
{
    public function __construct(public readonly User $user) {}
}

// Event: Article published
class ArticlePublished
{
    public function __construct(public readonly Article $article) {}
}

// Event: Payment processed
class PaymentProcessed
{
    public function __construct(public readonly Payment $payment) {}
}

// Listeners react to events
// - Send welcome email on UserRegistered
// - Update search index on ArticlePublished
// - Send invoice on PaymentProcessed
```

### Dispatcher Flow

```
1. Dispatcher receives event object
2. Dispatcher queries listener provider for handlers
3. For each handler:
   a. Call handler with event
   b. Handler may modify event
   c. Check if propagation stopped
4. Return modified event
```

---

## Events

### Event Object

```php
<?php
// Events are simple objects (DTOs)
// No base class required

class UserRegistered
{
    public function __construct(
        public readonly User $user,
        public readonly DateTime $registeredAt,
    ) {}
}

class OrderPlaced
{
    public function __construct(
        public readonly Order $order,
        private array $items = [],
    ) {}

    public function getItems(): array
    {
        return $this->items;
    }
}

// Events can be immutable or mutable
class NotificationSent
{
    private bool $processed = false;

    public function __construct(
        public readonly string $message,
        public readonly string $recipient,
    ) {}

    public function markAsProcessed(): void
    {
        $this->processed = true;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }
}
```

### Stoppable Events

```php
<?php
use Psr\EventDispatcher\StoppableEventInterface;

class ValidateUser implements StoppableEventInterface
{
    private bool $valid = true;
    private array $errors = [];
    private bool $propagationStopped = false;

    public function __construct(public readonly User $user) {}

    public function addError(string $error): void
    {
        $this->valid = false;
        $this->errors[] = $error;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}

// Usage
$event = new ValidateUser($user);

$dispatcher->dispatch($event);

if (!$event->isValid()) {
    echo "Validation errors: " . implode(', ', $event->getErrors());
}
```

---

## Listeners

### Listener Types

```php
<?php
// Type 1: Callable (function/closure)
$listener = function(UserRegistered $event) {
    $user = $event->user;
    echo "Welcome " . $user->name;
};

// Type 2: Method
class WelcomeHandler
{
    public function onUserRegistered(UserRegistered $event): void
    {
        $user = $event->user;
        echo "Welcome " . $user->name;
    }
}

$handler = new WelcomeHandler();
$listener = [$handler, 'onUserRegistered'];

// Type 3: Invokable class
class SendWelcomeEmail
{
    public function __invoke(UserRegistered $event): void
    {
        $user = $event->user;
        // Send email
    }
}

$listener = new SendWelcomeEmail();
```

### Listener Provider

```php
<?php
use Psr\EventDispatcher\ListenerProviderInterface;

interface ListenerProviderInterface
{
    /**
     * Returns all listeners for a given event
     *
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable;
}

// Implementation
class SimpleListenerProvider implements ListenerProviderInterface
{
    private array $listeners = [];

    public function addListener(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $class = $event::class;

        return $this->listeners[$class] ?? [];
    }
}

// Usage
$provider = new SimpleListenerProvider();

$provider->addListener(UserRegistered::class, function(UserRegistered $e) {
    echo "User registered: " . $e->user->name;
});

$provider->addListener(UserRegistered::class, function(UserRegistered $e) {
    // Send welcome email
});
```

---

## Dispatch Process

### EventDispatcherInterface

```php
<?php
use Psr\EventDispatcher\EventDispatcherInterface;

interface EventDispatcherInterface
{
    /**
     * Provide all relevant listeners with an event to process
     */
    public function dispatch(object $event): object;
}

// Implementation
class SimpleDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private ListenerProviderInterface $listenerProvider,
    ) {}

    public function dispatch(object $event): object
    {
        $listeners = $this->listenerProvider->getListenersForEvent($event);

        foreach ($listeners as $listener) {
            // Call listener with event
            $listener($event);

            // Check if propagation should stop
            if ($event instanceof StoppableEventInterface
                && $event->isPropagationStopped()
            ) {
                break;
            }
        }

        return $event;
    }
}
```

### Dispatching Events

```php
<?php
// Dispatch and receive modified event
$event = new UserRegistered($user);
$event = $dispatcher->dispatch($event);

// Event may have been modified by listeners

// Dispatch with type hints
$userEvent = $dispatcher->dispatch(new UserRegistered($user));

// Check stoppable event
if ($event instanceof StoppableEventInterface) {
    if ($event->isPropagationStopped()) {
        echo "Event was stopped";
    }
}
```

---

## Implementation

### Full Event System

```php
<?php
declare(strict_types=1);

namespace App\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

// Base event interface (optional)
interface DomainEvent {}

// Concrete events
class UserRegistered implements DomainEvent
{
    public function __construct(public readonly User $user) {}
}

class UserActivated implements DomainEvent
{
    public function __construct(
        public readonly User $user,
        public readonly DateTime $activatedAt,
    ) {}
}

class OrderCreated implements DomainEvent, StoppableEventInterface
{
    private bool $propagationStopped = false;
    private array $errors = [];

    public function __construct(public readonly Order $order) {}

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}

// Listener provider implementation
class SimpleListenerProvider implements ListenerProviderInterface
{
    private array $listeners = [];

    public function on(string $eventClass, callable $listener): self
    {
        $this->listeners[$eventClass][] = $listener;
        return $this;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $class = $event::class;
        return $this->listeners[$class] ?? [];
    }
}

// Dispatcher implementation
class SimpleDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private ListenerProviderInterface $provider,
    ) {}

    public function dispatch(object $event): object
    {
        foreach ($this->provider->getListenersForEvent($event) as $listener) {
            $listener($event);

            if ($event instanceof StoppableEventInterface
                && $event->isPropagationStopped()
            ) {
                break;
            }
        }

        return $event;
    }
}

// Usage
$provider = new SimpleListenerProvider();

$provider->on(UserRegistered::class, function(UserRegistered $event) {
    echo "User registered: " . $event->user->name;
});

$provider->on(UserRegistered::class, function(UserRegistered $event) {
    // Send welcome email
});

$dispatcher = new SimpleDispatcher($provider);

$event = new UserRegistered($user);
$dispatcher->dispatch($event);
```

---

## Common Patterns

### Domain Events

```php
<?php
class UserService
{
    public function __construct(
        private PDO $pdo,
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function register(string $name, string $email): User
    {
        // Create user
        $user = new User($name, $email);
        $user = $this->saveUser($user);

        // Dispatch event for side effects
        $this->dispatcher->dispatch(new UserRegistered($user));

        return $user;
    }

    public function activate(int $id): User
    {
        $user = $this->getUser($id);
        $user->setActive(true);
        $this->saveUser($user);

        $this->dispatcher->dispatch(new UserActivated($user, new DateTime()));

        return $user;
    }

    private function saveUser(User $user): User
    {
        // Persist to database
        return $user;
    }

    private function getUser(int $id): User
    {
        // Fetch from database
        return new User('John', 'john@example.com');
    }
}
```

### Event Listeners

```php
<?php
class UserEventListeners
{
    public function __construct(
        private EmailService $emailService,
        private NotificationService $notifications,
        private SearchIndex $searchIndex,
    ) {}

    public function registerListeners(ListenerProviderInterface $provider): void
    {
        $provider->on(UserRegistered::class, $this->onUserRegistered(...));
        $provider->on(UserActivated::class, $this->onUserActivated(...));
        $provider->on(OrderCreated::class, $this->onOrderCreated(...));
    }

    private function onUserRegistered(UserRegistered $event): void
    {
        $user = $event->user;

        // Send welcome email
        $this->emailService->send(
            $user->email,
            'Welcome to Our Platform',
            'welcome_email'
        );

        // Add to search index
        $this->searchIndex->add('users', $user->id, ['name' => $user->name]);

        // Send notification
        $this->notifications->notify(
            'admin',
            "New user registered: {$user->name}"
        );
    }

    private function onUserActivated(UserActivated $event): void
    {
        $user = $event->user;

        // Send activation confirmation
        $this->emailService->send(
            $user->email,
            'Account Activated',
            'account_activated'
        );

        // Create welcome package
        // $this->giftService->createWelcomePackage($user);
    }

    private function onOrderCreated(OrderCreated $event): void
    {
        $order = $event->order;

        // Validate order
        if (!$this->validateOrder($order)) {
            $event->addError('Order validation failed');
            $event->stopPropagation();
            return;
        }

        // Send confirmation email
        $this->emailService->send(
            $order->customer->email,
            'Order Confirmation',
            'order_confirmation',
            ['order' => $order]
        );
    }

    private function validateOrder(Order $order): bool
    {
        return $order->items->count() > 0;
    }
}
```

---

## Real-world Examples

### Application Event System

```php
<?php
class Application
{
    private EventDispatcherInterface $dispatcher;
    private ListenerProviderInterface $listenerProvider;

    public function __construct()
    {
        $this->listenerProvider = new SimpleListenerProvider();
        $this->dispatcher = new SimpleDispatcher($this->listenerProvider);

        $this->setupEventListeners();
    }

    private function setupEventListeners(): void
    {
        // User events
        $this->listenerProvider->on(UserRegistered::class, function(UserRegistered $e) {
            echo "Sending welcome email to {$e->user->email}\n";
            // Send email
        });

        $this->listenerProvider->on(UserRegistered::class, function(UserRegistered $e) {
            echo "Logging user registration\n";
            // Log event
        });

        // Order events
        $this->listenerProvider->on(OrderCreated::class, function(OrderCreated $e) {
            echo "Creating invoice for order {$e->order->id}\n";
            // Create invoice
        });

        $this->listenerProvider->on(OrderCreated::class, function(OrderCreated $e) {
            echo "Updating inventory\n";
            // Update stock
        });
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }
}

// Usage
$app = new Application();
$dispatcher = $app->getDispatcher();

$user = new User('John', 'john@example.com');
$dispatcher->dispatch(new UserRegistered($user));
```

---

## Complete Examples

### Full Event-Driven System

```php
<?php
declare(strict_types=1);

namespace App;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

// Events
class UserCreated
{
    public function __construct(public readonly int $userId) {}
}

class UserUpdated
{
    public function __construct(public readonly int $userId) {}
}

// Listener provider
class EventListenerProvider implements ListenerProviderInterface
{
    private array $listeners = [];

    public function subscribe(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function getListenersForEvent(object $event): iterable
    {
        return $this->listeners[$event::class] ?? [];
    }
}

// Dispatcher
class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private ListenerProviderInterface $provider,
    ) {}

    public function dispatch(object $event): object
    {
        foreach ($this->provider->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        return $event;
    }
}

// Service using events
class UserService
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {}

    public function createUser(string $name, string $email): void
    {
        $userId = 123;  // Assume created in DB

        $this->dispatcher->dispatch(new UserCreated($userId));
    }

    public function updateUser(int $id, string $name): void
    {
        // Update logic

        $this->dispatcher->dispatch(new UserUpdated($id));
    }
}

// Setup
$provider = new EventListenerProvider();

$provider->subscribe(UserCreated::class, function(UserCreated $event) {
    echo "User created: {$event->userId}\n";
});

$provider->subscribe(UserCreated::class, function(UserCreated $event) {
    echo "Sending welcome email\n";
});

$provider->subscribe(UserUpdated::class, function(UserUpdated $event) {
    echo "User updated: {$event->userId}\n";
});

$dispatcher = new EventDispatcher($provider);
$userService = new UserService($dispatcher);

// Use
$userService->createUser('John', 'john@example.com');
$userService->updateUser(1, 'John Doe');
```

---

## Key Takeaways

**PSR-14 Event Dispatcher Checklist:**

1. ✅ Create event objects for state changes
2. ✅ Implement EventDispatcherInterface
3. ✅ Register listeners in provider
4. ✅ Dispatch events from services
5. ✅ Use stoppable events when needed
6. ✅ Keep listeners focused
7. ✅ Test event handlers
8. ✅ Document available events

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Container Interface (PSR-11)](7-container-interface.md)
- [Log Interface (PSR-3)](3-logger-interface.md)
