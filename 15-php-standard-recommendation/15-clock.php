use DateTimeImmutable;

<?php

/**
 * PSR-20: Clock Interface - Detailed Summary
 * 
 * PSR-20 provides a standardized way to access the current time in PHP applications.
 * This allows for better testability and consistency across different libraries.
 * 
 * KEY CONCEPTS:
 * 
 * 1. ClockInterface
 *    - Single method: now()
 *    - Returns a DateTimeImmutable instance
 *    - Provides current point in time
 * 
 * 2. Benefits:
 *    - Testability: Easy to mock time in unit tests
 *    - Consistency: Standard interface across libraries
 *    - Decoupling: Applications don't directly call time() or new DateTime()
 * 
 * 3. Common Implementations:
 *    - SystemClock: Returns actual current time
 *    - FrozenClock: Returns fixed time (useful for testing)
 *    - OffsetClock: Returns time with offset applied
 * 
 * 4. Use Cases:
 *    - Time-based business logic
 *    - Logging with timestamps
 *    - Event scheduling
 *    - Rate limiting
 *    - Testing time-dependent code
 */

namespace Psr\Clock;


// PSR-20 Interface
interface ClockInterface
{
    /**
     * Returns the current time as a DateTimeImmutable Object
     */
    public function now(): DateTimeImmutable;
}

// Example Implementation: System Clock
class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}

// Example Implementation: Frozen Clock (for testing)
class FrozenClock implements ClockInterface
{
    private DateTimeImmutable $fixedTime;

    public function __construct(DateTimeImmutable $fixedTime)
    {
        $this->fixedTime = $fixedTime;
    }

    public function now(): DateTimeImmutable
    {
        return $this->fixedTime;
    }
}

// Example Implementation: Offset Clock
class OffsetClock implements ClockInterface
{
    private ClockInterface $innerClock;
    private \DateInterval $offset;

    public function __construct(ClockInterface $innerClock, \DateInterval $offset)
    {
        $this->innerClock = $innerClock;
        $this->offset = $offset;
    }

    public function now(): DateTimeImmutable
    {
        return $this->innerClock->now()->add($this->offset);
    }
}

// USAGE EXAMPLES

// 1. Using SystemClock in production
$systemClock = new SystemClock();
echo "Current time: " . $systemClock->now()->format('Y-m-d H:i:s') . "\n";

// 2. Using FrozenClock for testing
$fixedTime = new DateTimeImmutable('2024-01-15 12:00:00');
$frozenClock = new FrozenClock($fixedTime);
echo "Fixed time: " . $frozenClock->now()->format('Y-m-d H:i:s') . "\n";

// 3. Using OffsetClock (e.g., for different timezone)
$offset = new \DateInterval('PT2H'); // 2 hours ahead
$offsetClock = new OffsetClock($systemClock, $offset);
echo "Offset time: " . $offsetClock->now()->format('Y-m-d H:i:s') . "\n";

// 4. Dependency Injection Example
class OrderService
{
    private ClockInterface $clock;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function createOrder(array $items): array
    {
        return [
            'items' => $items,
            'created_at' => $this->clock->now()->format('Y-m-d H:i:s'),
            'status' => 'pending'
        ];
    }
}

// Production usage
$orderService = new OrderService(new SystemClock());
$order = $orderService->createOrder(['item1', 'item2']);
echo "Order created at: " . $order['created_at'] . "\n";

// Testing usage
$testClock = new FrozenClock(new DateTimeImmutable('2024-01-01 10:00:00'));
$testOrderService = new OrderService($testClock);
$testOrder = $testOrderService->createOrder(['test_item']);
echo "Test order created at: " . $testOrder['created_at'] . "\n";

/**
 * BEST PRACTICES:
 * 
 * 1. Always type-hint ClockInterface in constructors
 * 2. Use SystemClock in production code
 * 3. Use FrozenClock in unit tests
 * 4. Avoid calling time(), date(), or new DateTime() directly
 * 5. Inject clock dependencies through constructor
 * 
 * INSTALLATION:
 * composer require psr/clock
 * 
 * RELATED PSRs:
 * - PSR-20: Clock Interface
 * - Works well with PSR-3 (Logging) for timestamps
 * - Compatible with PSR-11 (Container) for DI
 */