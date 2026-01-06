# PSR-20: Clock

## Overview

Learn about PSR-20, the standardized clock interface that enables testable time-dependent code and framework-agnostic date/time handling.

---

## Table of Contents

1. What is PSR-20
2. Core Concepts
3. Clock Interface
4. Implementation
5. Testing with Clocks
6. Common Patterns
7. Real-world Examples
8. Complete Examples

---

## What is PSR-20

### Purpose

```php
<?php
// Before PSR-20: Hardcoded time calls

class UserService
{
    public function register(string $name, string $email): User
    {
        $user = new User($name, $email);
        $user->setCreatedAt(new DateTime());  // Hardcoded!

        return $user;
    }

    public function isLocked(User $user): bool
    {
        $lockTime = $user->getLockedAt();
        $now = new DateTime();  // Hardcoded!

        return $now->diff($lockTime)->i < 30;  // Locked for 30 minutes?
    }
}

// Problems:
// - Hard to test (can't control time)
// - Brittle tests
// - Can't test time-dependent behavior
// - Global state dependency

// Solution: PSR-20 (standardized clock interface)

use Psr\Clock\ClockInterface;

class UserService
{
    public function __construct(private ClockInterface $clock) {}

    public function register(string $name, string $email): User
    {
        $user = new User($name, $email);
        $user->setCreatedAt($this->clock->now());  // Testable!

        return $user;
    }

    public function isLocked(User $user): bool
    {
        $lockTime = $user->getLockedAt();
        $now = $this->clock->now();  // Can mock in tests!

        return $now->diff($lockTime)->i < 30;
    }
}

// Benefits:
// ✓ Testable time-dependent code
// ✓ Framework agnostic
// ✓ Inject clock dependencies
// ✓ Easy to mock time in tests
```

### Key Interface

```php
<?php
// ClockInterface
// - now(): DateTimeImmutable
```

---

## Core Concepts

### Clock Pattern

```php
<?php
// Clock represents a point in time
// Always returns DateTimeImmutable
// Can be injected and mocked

use Psr\Clock\ClockInterface;
use DateTimeImmutable;

interface ClockInterface
{
    /**
     * Returns the current time
     */
    public function now(): DateTimeImmutable;
}

// Usage
class Timestamp
{
    public function __construct(private ClockInterface $clock) {}

    public function record(): DateTimeImmutable
    {
        return $this->clock->now();
    }
}
```

### Why DateTimeImmutable

```php
<?php
// DateTimeImmutable is safer:

$dt = new DateTime();
$modified = $dt->modify('+1 day');
// $dt is NOW modified! Dangerous!

// DateTimeImmutable is safe:
$dt = new DateTimeImmutable();
$modified = $dt->modify('+1 day');
// $dt is unchanged, $modified is new instance
```

---

## Clock Interface

### SystemClock

```php
<?php
use Psr\Clock\ClockInterface;
use DateTimeImmutable;

class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}

// Usage
$clock = new SystemClock();
$now = $clock->now();  // Current time
```

### Fixed Clock (for testing)

```php
<?php
class FixedClock implements ClockInterface
{
    private DateTimeImmutable $now;

    public function __construct(DateTimeImmutable $now)
    {
        $this->now = $now;
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }

    public static function fromString(string $time): self
    {
        return new self(new DateTimeImmutable($time));
    }
}

// Usage in tests
$clock = FixedClock::fromString('2024-01-15 10:30:00');
$time1 = $clock->now();  // 2024-01-15 10:30:00
$time2 = $clock->now();  // 2024-01-15 10:30:00 (same!)
```

---

## Implementation

### Various Clock Implementations

```php
<?php
declare(strict_types=1);

namespace App\Clock;

use Psr\Clock\ClockInterface;
use DateTimeImmutable;
use DateTimeZone;

// System clock - actual current time
class SystemClock implements ClockInterface
{
    public function __construct(private ?DateTimeZone $timezone = null) {}

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timezone);
    }
}

// Fixed clock - for testing
class FixedClock implements ClockInterface
{
    public function __construct(private DateTimeImmutable $now) {}

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }

    public static function fromString(string $datetime): self
    {
        return new self(new DateTimeImmutable($datetime));
    }

    public static function fromTimestamp(int $timestamp): self
    {
        $dt = (new DateTimeImmutable())->setTimestamp($timestamp);
        return new self($dt);
    }
}

// Offset clock - for testing relative time
class OffsetClock implements ClockInterface
{
    private DateTimeImmutable $base;
    private DateInterval $offset;

    public function __construct(
        ClockInterface $baseClock,
        DateInterval $offset,
    ) {
        $this->base = $baseClock->now();
        $this->offset = $offset;
    }

    public function now(): DateTimeImmutable
    {
        return $this->base->add($this->offset);
    }
}

// Frozen clock - for testing time-dependent behavior
class FrozenClock implements ClockInterface
{
    private DateTimeImmutable $now;

    public function __construct(DateTimeImmutable $now)
    {
        $this->now = $now;
    }

    public function now(): DateTimeImmutable
    {
        return $this->now;
    }

    public function advance(DateInterval $interval): void
    {
        $this->now = $this->now->add($interval);
    }

    public function advanceSeconds(int $seconds): void
    {
        $this->advance(new DateInterval("PT{$seconds}S"));
    }

    public function set(DateTimeImmutable $time): void
    {
        $this->now = $time;
    }
}
```

---

## Testing with Clocks

### Unit Testing

```php
<?php
use PHPUnit\Framework\TestCase;
use App\Clock\FixedClock;

class UserServiceTest extends TestCase
{
    public function testUserCreationTimestamp()
    {
        $clock = FixedClock::fromString('2024-01-15 10:30:00');
        $service = new UserService($clock);

        $user = $service->register('John', 'john@example.com');

        $this->assertEquals(
            '2024-01-15T10:30:00+00:00',
            $user->getCreatedAt()->format('c')
        );
    }

    public function testAccountLockExpiration()
    {
        $now = new DateTimeImmutable('2024-01-15 10:00:00');
        $clock = new FixedClock($now);

        $service = new UserService($clock);
        $user = new User('John', 'john@example.com');
        $user->setLockedAt($now);

        // Still locked at 10:20 (20 minutes later)
        $clock = new FixedClock($now->add(new DateInterval('PT20M')));
        $service = new UserService($clock);

        $this->assertTrue($service->isLocked($user, 30));  // 30 min timeout

        // Not locked at 10:31 (31 minutes later)
        $clock = new FixedClock($now->add(new DateInterval('PT31M')));
        $service = new UserService($clock);

        $this->assertFalse($service->isLocked($user, 30));
    }
}
```

### Frozen Clock Testing

```php
<?php
class SchedulerTest extends TestCase
{
    public function testScheduledTaskExecution()
    {
        $clock = new FrozenClock(
            new DateTimeImmutable('2024-01-15 09:00:00')
        );

        $scheduler = new Scheduler($clock);
        $scheduler->schedule('backup', new DateInterval('PT1H'));

        // Task hasn't run yet
        $this->assertFalse($scheduler->shouldRun('backup'));

        // Advance 30 minutes
        $clock->advanceSeconds(1800);
        $this->assertFalse($scheduler->shouldRun('backup'));

        // Advance to 1 hour
        $clock->advanceSeconds(1800);
        $this->assertTrue($scheduler->shouldRun('backup'));
    }
}
```

---

## Common Patterns

### Timestamps and Expiration

```php
<?php
use Psr\Clock\ClockInterface;
use DateInterval;

class TokenManager
{
    private const TOKEN_TTL = 'PT1H';  // 1 hour

    public function __construct(private ClockInterface $clock) {}

    public function generateToken(): array
    {
        $issuedAt = $this->clock->now();
        $expiresAt = $issuedAt->add(new DateInterval(self::TOKEN_TTL));

        return [
            'token' => bin2hex(random_bytes(32)),
            'issued_at' => $issuedAt,
            'expires_at' => $expiresAt,
        ];
    }

    public function isValid(array $token): bool
    {
        $now = $this->clock->now();
        return $now < $token['expires_at'];
    }

    public function timeUntilExpiry(array $token): DateInterval
    {
        $now = $this->clock->now();
        return $now->diff($token['expires_at']);
    }
}
```

### Rate Limiting

```php
<?php
class RateLimiter
{
    private array $attempts = [];

    public function __construct(
        private ClockInterface $clock,
        private int $maxAttempts = 5,
        private int $windowSeconds = 60,
    ) {}

    public function isAllowed(string $identifier): bool
    {
        $now = $this->clock->now();
        $window = $now->getTimestamp() - $this->windowSeconds;

        // Clean old attempts
        if (isset($this->attempts[$identifier])) {
            $this->attempts[$identifier] = array_filter(
                $this->attempts[$identifier],
                fn($t) => $t > $window
            );
        }

        // Check limit
        $count = count($this->attempts[$identifier] ?? []);

        if ($count >= $this->maxAttempts) {
            return false;
        }

        // Record attempt
        $this->attempts[$identifier][] = $now->getTimestamp();

        return true;
    }
}
```

---

## Real-world Examples

### Session Management

```php
<?php
class SessionManager
{
    private const SESSION_TIMEOUT = 'PT30M';  // 30 minutes

    public function __construct(private ClockInterface $clock) {}

    public function createSession(int $userId): array
    {
        $now = $this->clock->now();
        $expiresAt = $now->add(new DateInterval(self::SESSION_TIMEOUT));

        return [
            'session_id' => bin2hex(random_bytes(16)),
            'user_id' => $userId,
            'created_at' => $now,
            'expires_at' => $expiresAt,
            'last_activity' => $now,
        ];
    }

    public function isActive(array $session): bool
    {
        $now = $this->clock->now();
        return $now < $session['expires_at'];
    }

    public function refreshActivity(array &$session): void
    {
        $session['last_activity'] = $this->clock->now();
    }
}
```

### Audit Logging

```php
<?php
class AuditLog
{
    public function __construct(private ClockInterface $clock) {}

    public function log(
        string $action,
        string $resource,
        int $userId,
        array $changes = []
    ): void {
        $entry = [
            'timestamp' => $this->clock->now(),
            'action' => $action,
            'resource' => $resource,
            'user_id' => $userId,
            'changes' => $changes,
        ];

        $this->store($entry);
    }

    public function getLogsInRange(
        DateTimeImmutable $start,
        DateTimeImmutable $end
    ): array {
        return $this->query([
            'timestamp' => ['$gte' => $start, '$lte' => $end],
        ]);
    }

    private function store(array $entry): void
    {
        // Store in database
    }

    private function query(array $criteria): array
    {
        // Query from database
        return [];
    }
}
```

---

## Complete Examples

### Full Application with Clock

```php
<?php
declare(strict_types=1);

namespace App;

use Psr\Clock\ClockInterface;
use DateInterval;
use DateTimeImmutable;

class PasswordResetService
{
    private const RESET_TOKEN_TTL = 'PT1H';  // 1 hour

    public function __construct(
        private ClockInterface $clock,
        private PDO $pdo,
    ) {}

    public function generateResetToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = $this->clock->now()->add(
            new DateInterval(self::RESET_TOKEN_TTL)
        );

        $stmt = $this->pdo->prepare(
            'INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            $token,
            $expiresAt->format('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    public function validateToken(string $token): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT user_id, expires_at FROM password_resets WHERE token = ?'
        );
        $stmt->execute([$token]);

        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $expiresAt = new DateTimeImmutable($row['expires_at']);

            if ($this->clock->now() < $expiresAt) {
                return $row['user_id'];
            }

            // Token expired
            $this->deleteToken($token);
        }

        return null;
    }

    public function resetPassword(string $token, string $password): bool
    {
        $userId = $this->validateToken($token);

        if ($userId === null) {
            return false;
        }

        // Update password
        $stmt = $this->pdo->prepare(
            'UPDATE users SET password = ? WHERE id = ?'
        );
        $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $userId,
        ]);

        // Delete token
        $this->deleteToken($token);

        return true;
    }

    private function deleteToken(string $token): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM password_resets WHERE token = ?');
        $stmt->execute([$token]);
    }
}

// Test
$clock = new FixedClock(new DateTimeImmutable('2024-01-15 10:00:00'));
$service = new PasswordResetService($clock, $pdo);

$token = $service->generateResetToken(1);

// Token is valid now
$this->assertSame(1, $service->validateToken($token));

// Advance time 61 minutes
$clock = new FixedClock($clock->now()->add(new DateInterval('PT61M')));
$service = new PasswordResetService($clock, $pdo);

// Token is now expired
$this->assertNull($service->validateToken($token));
```

---

## Key Takeaways

**PSR-20 Clock Checklist:**

1. ✅ Inject ClockInterface
2. ✅ Use clock->now() for current time
3. ✅ Use DateTimeImmutable (always)
4. ✅ Test with FixedClock
5. ✅ Test time-dependent behavior
6. ✅ Use DateInterval for durations
7. ✅ Implement proper time zones
8. ✅ Document time requirements

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Container Interface (PSR-11)](7-container-interface.md)
- [Simple Cache (PSR-16)](12-simple-cache.md)
