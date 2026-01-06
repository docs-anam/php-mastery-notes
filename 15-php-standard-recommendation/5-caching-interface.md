# PSR-6: Caching Interface

## Overview

Learn about PSR-6, the standardized cache interface that provides a consistent API for interacting with cache implementations.

---

## Table of Contents

1. What is PSR-6
2. Core Concepts
3. Cache Items
4. Cache Pools
5. Implementation
6. Common Patterns
7. Real-world Examples
8. Complete Examples
9. Advanced Topics

---

## What is PSR-6

### Purpose

```php
<?php
// Before PSR-6: Vendor lock-in

// Using Memcached directly
$memcached = new Memcached();
$memcached->addServer('localhost', 11211);
$memcached->set('user_' . $id, $data, 3600);
$value = $memcached->get('user_' . $id);

// Using Redis directly
$redis = new Redis();
$redis->connect('localhost', 6379);
$redis->setex('user_' . $id, 3600, $data);
$value = $redis->get('user_' . $id);

// Using File cache
file_put_contents('cache/user_' . $id . '.cache', $data);

// Problem: Different APIs, can't switch implementations

// Solution: PSR-6 (standardized interface)

use Psr\Cache\CacheItemPoolInterface;

class UserService
{
    public function __construct(private CacheItemPoolInterface $cache) {}

    public function getUser(int $id): ?User
    {
        $item = $this->cache->getItem("user_{$id}");

        if ($item->isHit()) {
            return $item->get();
        }

        $user = $this->fetchFromDatabase($id);

        $item->set($user)->expiresAfter(3600);
        $this->cache->save($item);

        return $user;
    }
}

// Benefits:
// ✓ Standard interface
// ✓ Easy to switch implementations
// ✓ Works with any PSR-6 cache
```

### Key Interfaces

```php
<?php
// Main interfaces:

// CacheItemPoolInterface
// - Manages cache items
// - get/getItems/hasItem
// - getItem/saveDeferred/save
// - deleteItem/clear

// CacheItemInterface
// - Individual cache item
// - get/set/isHit
// - expiresAt/expiresAfter

// CacheException
// - Base exception
// - InvalidArgumentException
```

---

## Core Concepts

### Cache Items

```php
<?php
// A cache item has:
// - Key: unique identifier
// - Value: data to cache
// - Hit: exists and valid
// - Expiration: when to invalidate

use Psr\Cache\CacheItemInterface;

interface CacheItemInterface
{
    /**
     * Returns the key for this cache item
     */
    public function getKey(): string;

    /**
     * Retrieves the value of the item from the cache
     */
    public function get(): mixed;

    /**
     * Confirms if the cache item lookup resulted in a cache hit
     */
    public function isHit(): bool;

    /**
     * Sets the value represented by this cache item
     */
    public function set(mixed $value): static;

    /**
     * Sets the expiration time for this cache item
     */
    public function expiresAt(?DateTimeInterface $expiration): static;

    /**
     * Sets the expiration time to some time in the future
     */
    public function expiresAfter(int|DateInterval|null $time): static;
}
```

### Cache Pools

```php
<?php
// Pool manages multiple cache items

use Psr\Cache\CacheItemPoolInterface;

interface CacheItemPoolInterface
{
    /**
     * Returns a Cache Item representing the specified key
     */
    public function getItem(string $key): CacheItemInterface;

    /**
     * Returns a traversable set of cache items
     */
    public function getItems(array $keys = []): iterable;

    /**
     * Confirms if the cache contains specified cache key
     */
    public function hasItem(string $key): bool;

    /**
     * Deletes and invalidates a specific item
     */
    public function deleteItem(string $key): bool;

    /**
     * Deletes all items in the pool
     */
    public function clear(): bool;

    /**
     * Persists a cache item immediately
     */
    public function save(CacheItemInterface $item): bool;

    /**
     * Sets a cache item to be persisted later
     */
    public function saveDeferred(CacheItemInterface $item): bool;

    /**
     * Persists any deferred cache items
     */
    public function commit(): bool;
}
```

---

## Cache Items

### Working with Items

```php
<?php
use Psr\Cache\CacheItemPoolInterface;

function cacheExample(CacheItemPoolInterface $pool): void
{
    // Get item
    $item = $pool->getItem('user_123');

    // Check if cached
    if ($item->isHit()) {
        echo "Found in cache: " . $item->get();
        return;
    }

    // Not cached, fetch data
    $data = fetchUserFromDB(123);

    // Store in cache
    $item->set($data);
    $item->expiresAfter(3600);  // 1 hour

    $pool->save($item);
    echo "Saved to cache: " . $data;
}
```

### Expiration Options

```php
<?php
use Psr\Cache\CacheItemInterface;

$item = $pool->getItem('key');

// Option 1: Expire after seconds
$item->expiresAfter(3600);

// Option 2: Expire at specific time
$item->expiresAt(new DateTime('2025-12-31 23:59:59'));

// Option 3: Expire using DateInterval
$item->expiresAfter(new DateInterval('PT1H'));

// Option 4: Never expire (null)
$item->expiresAfter(null);
```

### Multiple Items

```php
<?php
$pool = $cache;

// Get multiple items at once
$items = $pool->getItems(['user_1', 'user_2', 'user_3']);

foreach ($items as $key => $item) {
    if ($item->isHit()) {
        echo "Found: $key\n";
    } else {
        echo "Missing: $key\n";
    }
}

// Check if item exists
if ($pool->hasItem('user_123')) {
    echo "Cached!";
}

// Delete item
$pool->deleteItem('user_123');

// Clear all
$pool->clear();
```

---

## Cache Pools

### Available Implementations

```php
<?php
// Popular PSR-6 implementations:

// Symfony Cache
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
$pool = new FilesystemAdapter();

// Illustrated
use Illustrated\Cache\Adapter\RedisAdapter;
$pool = new RedisAdapter($redisClient);

// Monolog
use Monolog\Handler\CacheHandler;
// (Monolog uses PSR-6)

// Stash (legacy)
use Stash\Pool;
$pool = new Pool();

// Doctrine
use Doctrine\Common\Cache\ArrayCache;
// (Doctrine uses PSR-6 interface)
```

### Creating Your Own

```php
<?php
declare(strict_types=1);

namespace App\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class FileCachePool implements CacheItemPoolInterface
{
    private string $cacheDir;
    private array $deferred = [];

    public function __construct(string $cacheDir = 'cache')
    {
        $this->cacheDir = $cacheDir;
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
    }

    public function getItem(string $key): CacheItemInterface
    {
        $this->validateKey($key);

        $file = $this->getFilePath($key);
        $item = new FileCacheItem($key, $file);

        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file));

            if ($data['expires'] === null || $data['expires'] > time()) {
                $item->set($data['value']);
                $item->setHit(true);
            } else {
                unlink($file);
            }
        }

        return $item;
    }

    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }
        return $items;
    }

    public function hasItem(string $key): bool
    {
        return $this->getItem($key)->isHit();
    }

    public function clear(): bool
    {
        foreach (scandir($this->cacheDir) as $file) {
            if ($file !== '.' && $file !== '..') {
                unlink($this->cacheDir . '/' . $file);
            }
        }
        return true;
    }

    public function deleteItem(string $key): bool
    {
        $this->validateKey($key);
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }

    public function save(CacheItemInterface $item): bool
    {
        $file = $this->getFilePath($item->getKey());

        $data = [
            'value' => $item->get(),
            'expires' => $this->getExpiration($item),
        ];

        return file_put_contents($file, serialize($data)) > 0;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[$item->getKey()] = $item;
        return true;
    }

    public function commit(): bool
    {
        foreach ($this->deferred as $item) {
            $this->save($item);
        }
        $this->deferred = [];
        return true;
    }

    private function getFilePath(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }

    private function validateKey(string $key): void
    {
        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $key)) {
            throw new InvalidArgumentException("Invalid cache key: $key");
        }
    }

    private function getExpiration(CacheItemInterface $item): ?int
    {
        // Implementation would extract expiration time
        return null;
    }
}

class FileCacheItem implements CacheItemInterface
{
    private mixed $value = null;
    private bool $hit = false;
    private ?int $expiresAt = null;

    public function __construct(private string $key, private string $file) {}

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->hit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function expiresAt(?DateTimeInterface $expiration): static
    {
        $this->expiresAt = $expiration?->getTimestamp();
        return $this;
    }

    public function expiresAfter(int|DateInterval|null $time): static
    {
        if ($time === null) {
            $this->expiresAt = null;
        } elseif ($time instanceof DateInterval) {
            $this->expiresAt = (new DateTime())->add($time)->getTimestamp();
        } else {
            $this->expiresAt = time() + $time;
        }

        return $this;
    }

    public function setHit(bool $hit): void
    {
        $this->hit = $hit;
    }
}
```

---

## Implementation

### Dependency Injection

```php
<?php
// Service using cache

namespace App\Services;

use Psr\Cache\CacheItemPoolInterface;

class UserService
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {}

    public function getUser(int $id): User
    {
        $item = $this->cache->getItem("user_{$id}");

        if ($item->isHit()) {
            return $item->get();
        }

        $user = $this->fetchFromDatabase($id);

        $item->set($user)->expiresAfter(3600);
        $this->cache->save($item);

        return $user;
    }

    private function fetchFromDatabase(int $id): User
    {
        // Database query
        return new User($id, 'John Doe');
    }
}
```

### Container Configuration

```php
<?php
// Using Symfony DI

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use App\Services\UserService;

$container = new ContainerBuilder();

// Register cache pool
$container->register('cache', FilesystemAdapter::class)
    ->addArgument('app');

// Register service with cache injection
$container->register(UserService::class)
    ->addArgument(new Reference('cache'));

// Get service
$service = $container->get(UserService::class);
```

---

## Common Patterns

### Cache-Aside Pattern

```php
<?php
function getCachedData(
    CacheItemPoolInterface $cache,
    string $key,
    callable $loader,
    int $ttl = 3600
): mixed {
    $item = $cache->getItem($key);

    if ($item->isHit()) {
        return $item->get();
    }

    $data = $loader();

    $item->set($data)->expiresAfter($ttl);
    $cache->save($item);

    return $data;
}

// Usage
$user = getCachedData(
    $cache,
    'user_123',
    fn() => User::find(123),
    3600
);
```

### Batch Operations

```php
<?php
function cacheMultiple(
    CacheItemPoolInterface $cache,
    array $keys,
    callable $loader,
    int $ttl = 3600
): array {
    $items = $cache->getItems($keys);
    $missing = [];
    $results = [];

    foreach ($items as $key => $item) {
        if ($item->isHit()) {
            $results[$key] = $item->get();
        } else {
            $missing[$key] = $item;
        }
    }

    if ($missing) {
        $data = $loader(array_keys($missing));

        foreach ($data as $key => $value) {
            $item = $missing[$key];
            $item->set($value)->expiresAfter($ttl);
            $cache->save($item);
            $results[$key] = $value;
        }
    }

    return $results;
}

// Usage
$users = cacheMultiple(
    $cache,
    ['user_1', 'user_2', 'user_3'],
    fn($ids) => User::findMany($ids),
    3600
);
```

---

## Real-world Examples

### Database Query Caching

```php
<?php
use Psr\Cache\CacheItemPoolInterface;

class ArticleRepository
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private PDO $pdo,
    ) {}

    public function findById(int $id): ?Article
    {
        $cacheKey = "article_{$id}";
        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return $item->get();
        }

        $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $article = new Article($data);

        $item->set($article)->expiresAfter(86400);
        $this->cache->save($item);

        return $article;
    }

    public function findAll(): array
    {
        $item = $this->cache->getItem('all_articles');

        if ($item->isHit()) {
            return $item->get();
        }

        $stmt = $this->pdo->query('SELECT * FROM articles');
        $articles = array_map(
            fn($row) => new Article($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );

        $item->set($articles)->expiresAfter(3600);
        $this->cache->save($item);

        return $articles;
    }

    public function invalidate(int $id): void
    {
        $this->cache->deleteItem("article_{$id}");
        $this->cache->deleteItem('all_articles');
    }
}
```

### API Response Caching

```php
<?php
use Psr\Cache\CacheItemPoolInterface;

class ApiClient
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private HttpClientInterface $http,
    ) {}

    public function getWeather(string $city): array
    {
        $cacheKey = "weather_" . md5($city);
        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return $item->get();
        }

        $response = $this->http->request('GET', "https://api.weather.com/forecast", [
            'query' => ['city' => $city]
        ]);

        $data = json_decode($response->getContent(), true);

        $item->set($data)->expiresAfter(1800);  // 30 minutes
        $this->cache->save($item);

        return $data;
    }
}
```

---

## Complete Examples

### Full Application Example

```php
<?php
// bootstrap.php
require_once 'vendor/autoload.php';

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use App\Services\UserService;

// Create cache pool
$cache = new FilesystemAdapter();

// Inject into service
$userService = new UserService($cache);

// Use service
$user = $userService->getUser(123);
echo "User: " . $user->getName();

// File: src/Services/UserService.php
namespace App\Services;

use Psr\Cache\CacheItemPoolInterface;

class UserService
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {}

    public function getUser(int $id): User
    {
        $item = $this->cache->getItem("user_{$id}");

        if ($item->isHit()) {
            return $item->get();
        }

        $user = new User($id, "User $id");

        $item->set($user)->expiresAfter(3600);
        $this->cache->save($item);

        return $user;
    }
}

class User
{
    public function __construct(private int $id, private string $name) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
}
```

---

## Key Takeaways

**PSR-6 Caching Checklist:**

1. ✅ Use CacheItemPoolInterface for injection
2. ✅ Check isHit() before using cached data
3. ✅ Set expiration times appropriately
4. ✅ Save items to persist changes
5. ✅ Use batch operations for efficiency
6. ✅ Implement cache invalidation strategy
7. ✅ Handle cache misses gracefully
8. ✅ Test cache behavior thoroughly

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [Container Interface (PSR-11)](7-container-interface.md)
- [Simple Cache (PSR-16)](12-simple-cache.md)
