# PSR-16: Simple Cache

## Overview

Learn about PSR-16, the simplified caching interface that provides a more straightforward alternative to PSR-6 for common caching scenarios.

---

## Table of Contents

1. What is PSR-16
2. Core Concepts
3. Cache Operations
4. Implementation
5. Common Patterns
6. Real-world Examples
7. Complete Examples
8. PSR-16 vs PSR-6

---

## What is PSR-16

### Purpose

```php
<?php
// Before PSR-16: Complex caching API (PSR-6)

use Psr\Cache\CacheItemPoolInterface;

$pool = $cache;

// Get item
$item = $pool->getItem('user_123');
if ($item->isHit()) {
    $user = $item->get();
} else {
    $user = fetchUser(123);
    $item->set($user)->expiresAfter(3600);
    $pool->save($item);
}

// Too verbose for simple cases!

// Solution: PSR-16 (simple cache interface)

use Psr\SimpleCache\CacheInterface;

$cache = new FileCache();

// Get value
$user = $cache->get('user_123');

// Get with default
$user = $cache->get('user_123', null);

// Set value
$cache->set('user_123', $user, 3600);

// Benefits:
// ✓ Simpler API
// ✓ Fewer method calls
// ✓ Common use cases covered
// ✓ Less boilerplate
```

### Key Differences from PSR-6

```
PSR-6 (Complex)              | PSR-16 (Simple)
═════════════════════════════╪═════════════════════════════
CacheItemPoolInterface       | CacheInterface
Items with state             | Direct values
get/has/set/delete items     | get/set/delete values
Deferred saves               | Immediate operations
isHit() check needed         | Returns default if missing
Complex expiration           | Simple TTL
Iteration support            | No iteration
Many methods                 | 7 core methods
```

---

## Core Concepts

### CacheInterface

```php
<?php
use Psr\SimpleCache\CacheInterface;

interface CacheInterface
{
    /**
     * Get a value from the cache
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Set a value in the cache
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool;

    /**
     * Delete a value from the cache
     */
    public function delete(string $key): bool;

    /**
     * Clear all values from the cache
     */
    public function clear(): bool;

    /**
     * Get multiple values
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable;

    /**
     * Set multiple values
     */
    public function setMultiple(iterable $values, ?int $ttl = null): bool;

    /**
     * Delete multiple values
     */
    public function deleteMultiple(iterable $keys): bool;

    /**
     * Check if key exists and is not expired
     */
    public function has(string $key): bool;
}
```

### Time To Live (TTL)

```php
<?php
// TTL in seconds

$cache->set('key', 'value', 3600);      // 1 hour
$cache->set('key', 'value', 86400);     // 1 day
$cache->set('key', 'value', 2592000);   // 30 days

// No expiration
$cache->set('key', 'value', null);

// Expires immediately
$cache->set('key', 'value', 0);
```

---

## Cache Operations

### Single Value Operations

```php
<?php
use Psr\SimpleCache\CacheInterface;

function cacheExample(CacheInterface $cache): void
{
    // Get value
    $value = $cache->get('my_key');

    // Get with default
    $value = $cache->get('my_key', 'default_value');

    // Set value
    $cache->set('my_key', 'my_value');

    // Set with TTL
    $cache->set('my_key', 'my_value', 3600);

    // Check if exists
    if ($cache->has('my_key')) {
        echo "Key exists";
    }

    // Delete
    $cache->delete('my_key');

    // Clear all
    $cache->clear();
}
```

### Multiple Value Operations

```php
<?php
$cache = new FileCache();

// Get multiple
$values = $cache->getMultiple(['user_1', 'user_2', 'user_3']);
foreach ($values as $key => $value) {
    echo "$key: $value\n";
}

// Get multiple with default
$values = $cache->getMultiple(
    ['config_db', 'config_cache'],
    []
);

// Set multiple
$cache->setMultiple([
    'user_1' => $user1,
    'user_2' => $user2,
    'user_3' => $user3,
], 3600);

// Delete multiple
$cache->deleteMultiple(['user_1', 'user_2', 'user_3']);
```

### Efficient Caching Pattern

```php
<?php
function getCachedUser(CacheInterface $cache, int $id): ?User
{
    $key = "user_{$id}";

    // Try to get from cache
    $user = $cache->get($key);
    if ($user !== null) {
        return $user;
    }

    // Not in cache, fetch from database
    $user = fetchUserFromDatabase($id);
    if ($user === null) {
        return null;
    }

    // Store in cache for 1 hour
    $cache->set($key, $user, 3600);

    return $user;
}

function getCachedUsers(CacheInterface $cache, array $ids): array
{
    // Get from cache
    $cached = $cache->getMultiple($ids, null);

    $missing = [];
    $results = [];

    foreach ($ids as $id) {
        if ($cached[$id] !== null) {
            $results[$id] = $cached[$id];
        } else {
            $missing[] = $id;
        }
    }

    if (!empty($missing)) {
        // Fetch missing from database
        $fetched = fetchUsersFromDatabase($missing);

        // Store in cache
        $cache->setMultiple($fetched, 3600);

        $results = array_merge($results, $fetched);
    }

    return $results;
}
```

---

## Implementation

### File-based Cache

```php
<?php
declare(strict_types=1);

namespace App\Cache;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class FileCache implements CacheInterface
{
    public function __construct(private string $path = 'cache/') {}

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));

        // Check expiration
        if ($data['expires'] !== null && $data['expires'] < time()) {
            unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->validateKey($key);

        $file = $this->getFilePath($key);
        $dir = dirname($file);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $data = [
            'value' => $value,
            'expires' => $ttl !== null ? time() + $ttl : null,
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    public function delete(string $key): bool
    {
        $this->validateKey($key);
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }

    public function clear(): bool
    {
        $files = glob($this->path . '*.cache');

        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    public function setMultiple(iterable $values, ?int $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                return false;
            }
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                return false;
            }
        }

        return true;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    private function getFilePath(string $key): string
    {
        return $this->path . md5($key) . '.cache';
    }

    private function validateKey(string $key): void
    {
        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $key)) {
            throw new InvalidArgumentException("Invalid cache key: $key");
        }
    }
}

// Exception
class InvalidArgumentException extends \InvalidArgumentException
    implements \Psr\SimpleCache\InvalidArgumentException {}
```

### Array Cache

```php
<?php
class ArrayCache implements CacheInterface
{
    private array $data = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->data[$key] = $value;
        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->data[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->data = [];
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMultiple(iterable $values, ?int $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }
}
```

---

## Common Patterns

### Service with Cache

```php
<?php
use Psr\SimpleCache\CacheInterface;

class UserService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private PDO $pdo,
        private CacheInterface $cache,
    ) {}

    public function getUser(int $id): ?User
    {
        $key = "user_{$id}";

        // Try cache first
        $user = $this->cache->get($key);
        if ($user !== null) {
            return $user;
        }

        // Query database
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User($row);
            // Cache for future use
            $this->cache->set($key, $user, self::CACHE_TTL);
            return $user;
        }

        return null;
    }

    public function getUsers(array $ids): array
    {
        $keys = array_map(fn($id) => "user_{$id}", $ids);

        // Get from cache
        $cached = $this->cache->getMultiple($keys, null);

        $missing = [];
        $results = [];

        foreach ($ids as $id) {
            $key = "user_{$id}";
            if ($cached[$key] !== null) {
                $results[$id] = $cached[$key];
            } else {
                $missing[] = $id;
            }
        }

        // Fetch missing from database
        if (!empty($missing)) {
            $placeholders = implode(',', array_fill(0, count($missing), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id IN ({$placeholders})");
            $stmt->execute($missing);

            $toCache = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new User($row);
                $results[$user->id] = $user;
                $toCache["user_{$user->id}"] = $user;
            }

            // Store in cache
            $this->cache->setMultiple($toCache, self::CACHE_TTL);
        }

        return $results;
    }

    public function updateUser(int $id, array $data): User
    {
        // Update database
        // ...

        // Invalidate cache
        $this->cache->delete("user_{$id}");
        $this->cache->delete("users_list");

        return $user;
    }
}
```

---

## Real-world Examples

### Configuration Caching

```php
<?php
class ConfigManager
{
    public function __construct(private CacheInterface $cache) {}

    public function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = "config_{$key}";

        $value = $this->cache->get($cacheKey);
        if ($value !== null) {
            return $value;
        }

        $value = $this->loadFromFile($key) ?? $default;

        // Cache for 1 day
        $this->cache->set($cacheKey, $value, 86400);

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $this->saveToFile($key, $value);

        // Invalidate cache
        $this->cache->delete("config_{$key}");
    }

    private function loadFromFile(string $key): mixed
    {
        // Load configuration
        return null;
    }

    private function saveToFile(string $key, mixed $value): void
    {
        // Save configuration
    }
}
```

---

## Complete Examples

### Full Caching Application

```php
<?php
declare(strict_types=1);

namespace App;

use Psr\SimpleCache\CacheInterface;

class Repository
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private PDO $pdo,
        private CacheInterface $cache,
    ) {}

    public function find(int $id): ?array
    {
        $key = "record_{$id}";

        $record = $this->cache->get($key);
        if ($record !== null) {
            return $record;
        }

        $stmt = $this->pdo->prepare('SELECT * FROM records WHERE id = ?');
        $stmt->execute([$id]);

        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            $this->cache->set($key, $record, self::CACHE_TTL);
        }

        return $record ?: null;
    }

    public function findAll(array $ids): array
    {
        $keys = array_map(fn($id) => "record_{$id}", $ids);

        $cached = $this->cache->getMultiple($keys, null);

        $missing = [];
        $results = [];

        foreach ($ids as $id) {
            $key = "record_{$id}";
            if ($cached[$key] !== null) {
                $results[$id] = $cached[$key];
            } else {
                $missing[] = $id;
            }
        }

        if (!empty($missing)) {
            $placeholders = implode(',', array_fill(0, count($missing), '?'));
            $stmt = $this->pdo->prepare("SELECT * FROM records WHERE id IN ({$placeholders})");
            $stmt->execute($missing);

            $toCache = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[$row['id']] = $row;
                $toCache["record_{$row['id']}"] = $row;
            }

            $this->cache->setMultiple($toCache, self::CACHE_TTL);
        }

        return $results;
    }

    public function save(int $id, array $data): void
    {
        // Update database
        $stmt = $this->pdo->prepare('UPDATE records SET data = ? WHERE id = ?');
        $stmt->execute([json_encode($data), $id]);

        // Invalidate cache
        $this->cache->delete("record_{$id}");
        $this->cache->delete("all_records");
    }
}
```

---

## PSR-16 vs PSR-6

### When to Use PSR-16

```php
<?php
// Use PSR-16 for:
// - Simple get/set/delete operations
// - No need for complex item state
// - Cache is optional (getters return defaults)
// - Common use cases
// - Less boilerplate code

$cache->set('key', 'value', 3600);
$value = $cache->get('key');
$cache->delete('key');
```

### When to Use PSR-6

```php
<?php
// Use PSR-6 for:
// - Complex cache operations
// - Deferred writes
// - Item introspection
// - Advanced patterns
// - Full cache control

$item = $cache->getItem('key');
$item->set('value')->expiresAfter(3600);
$cache->save($item);
```

---

## Key Takeaways

**PSR-16 Simple Cache Checklist:**

1. ✅ Inject CacheInterface
2. ✅ Use get() with defaults
3. ✅ Set TTL in seconds
4. ✅ Use batch operations for efficiency
5. ✅ Implement invalidation strategy
6. ✅ Handle missing keys gracefully
7. ✅ Test cache behavior
8. ✅ Monitor cache hit rates

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Caching Interface (PSR-6)](5-caching-interface.md)
- [Container Interface (PSR-11)](7-container-interface.md)
