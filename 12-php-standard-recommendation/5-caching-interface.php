use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

<?php

/**
 * PSR-6: Caching Interface - Detailed Summary
 * 
 * PSR-6 defines a common interface for caching libraries, allowing interoperability
 * between different caching implementations (Redis, Memcached, File, APCu, etc.).
 * 
 * KEY CONCEPTS:
 * 
 * 1. CacheItemPoolInterface - Main cache storage interface
 *    - getItem($key): Retrieves a cache item by key
 *    - getItems(array $keys): Retrieves multiple items
 *    - hasItem($key): Checks if item exists
 *    - clear(): Clears all cache
 *    - deleteItem($key): Deletes specific item
 *    - deleteItems(array $keys): Deletes multiple items
 *    - save(CacheItemInterface $item): Persists item immediately
 *    - saveDeferred(CacheItemInterface $item): Defers persistence for batch operations
 *    - commit(): Persists all deferred items
 * 
 * 2. CacheItemInterface - Represents a single cache entry
 *    - getKey(): Returns the cache key
 *    - get(): Returns the cached value
 *    - isHit(): Checks if item exists in cache
 *    - set($value): Sets the value
 *    - expiresAt($expiration): Sets absolute expiration time
 *    - expiresAfter($time): Sets relative expiration time (seconds or DateInterval)
 * 
 * 3. CacheException - Base exception interface
 * 4. InvalidArgumentException - For invalid cache keys/values
 * 
 * BENEFITS:
 * - Vendor independence - switch cache backends easily
 * - Standardized API across projects
 * - Better testing with mock implementations
 * - Deferred saves for performance optimization
 * 
 * PSR-16: Simple Cache (Cache Interface)
 * - Simpler alternative to PSR-6
 * - Direct get/set operations without CacheItem objects
 * - Methods: get(), set(), delete(), clear(), getMultiple(), setMultiple(), deleteMultiple(), has()
 * - TTL in seconds (simpler than PSR-6)
 */

// Example implementation concept (requires PSR-6 library like Symfony Cache)


// Example usage with PSR-6 compliant cache pool
class UserRepository
{
    private CacheItemPoolInterface $cache;
    
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }
    
    public function getUser(int $id): ?array
    {
        // Get cache item
        $cacheKey = "user_{$id}";
        $item = $this->cache->getItem($cacheKey);
        
        // Check if cache hit
        if ($item->isHit()) {
            return $item->get();
        }
        
        // Cache miss - fetch from database
        $userData = $this->fetchFromDatabase($id);
        
        // Store in cache with 1 hour expiration
        $item->set($userData);
        $item->expiresAfter(3600);
        $this->cache->save($item);
        
        return $userData;
    }
    
    public function invalidateUser(int $id): void
    {
        $this->cache->deleteItem("user_{$id}");
    }
    
    public function warmCache(array $userIds): void
    {
        foreach ($userIds as $id) {
            $userData = $this->fetchFromDatabase($id);
            $item = $this->cache->getItem("user_{$id}");
            $item->set($userData);
            $item->expiresAfter(3600);
            
            // Use deferred save for better performance
            $this->cache->saveDeferred($item);
        }
        
        // Commit all deferred items at once
        $this->cache->commit();
    }
    
    private function fetchFromDatabase(int $id): ?array
    {
        // Simulated database fetch
        return [
            'id' => $id,
            'name' => "User {$id}",
            'email' => "user{$id}@example.com"
        ];
    }
}

// PSR-16 Simple Cache Example (conceptual)
class SimpleUserCache
{
    private $cache; // Psr\SimpleCache\CacheInterface
    
    public function getUser(int $id): ?array
    {
        $key = "user_{$id}";
        
        // Direct get operation
        $user = $this->cache->get($key);
        
        if ($user === null) {
            $user = $this->fetchFromDatabase($id);
            // Direct set with TTL
            $this->cache->set($key, $user, 3600);
        }
        
        return $user;
    }
}

/**
 * INSTALLATION (using Composer):
 * composer require psr/cache (for PSR-6 interfaces)
 * composer require psr/simple-cache (for PSR-16 interfaces)
 * composer require symfony/cache (popular implementation)
 * 
 * COMMON IMPLEMENTATIONS:
 * - Symfony Cache Component
 * - PHP Cache (php-cache.com)
 * - Laravel Cache (supports PSR-6 & PSR-16)
 * - Doctrine Cache
 */