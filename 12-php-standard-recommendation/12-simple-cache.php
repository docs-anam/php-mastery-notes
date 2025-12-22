use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

<?php

/**
 * PSR-16: Simple Cache Interface - Detailed Summary
 * 
 * PSR-16 defines a simple caching interface for common use cases.
 * It provides a simpler alternative to PSR-6 (Caching Interface) for
 * basic cache operations.
 * 
 * KEY CONCEPTS:
 * 
 * 1. CacheInterface
 *    - Main interface that cache implementations must follow
 *    - Provides simple methods for basic cache operations
 * 
 * 2. Key Features:
 *    - get($key, $default = null) - Retrieve a value from cache
 *    - set($key, $value, $ttl = null) - Store a value in cache
 *    - delete($key) - Delete a value from cache
 *    - clear() - Clear all cache entries
 *    - getMultiple($keys, $default = null) - Get multiple values
 *    - setMultiple($values, $ttl = null) - Set multiple values
 *    - deleteMultiple($keys) - Delete multiple values
 *    - has($key) - Check if key exists in cache
 * 
 * 3. TTL (Time To Live):
 *    - Can be an integer (seconds) or DateInterval object
 *    - null means cache forever (if supported)
 *    - 0 or negative values mean immediate expiration
 * 
 * 4. Cache Keys:
 *    - Must be strings
 *    - Cannot contain: {}()/\@:
 *    - UTF-8 encoded, max 64 characters recommended
 * 
 * 5. Exceptions:
 *    - CacheException - Base interface for all exceptions
 *    - InvalidArgumentException - For invalid cache keys/values
 */

// Example implementation using PSR-16


// Basic usage example
class SimpleCacheExample
{
    private CacheInterface $cache;
    
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
    
    // Single item operations
    public function basicOperations(): void
    {
        // Set a cache value (expires in 1 hour)
        $this->cache->set('user_123', ['name' => 'John', 'email' => 'john@example.com'], 3600);
        
        // Get a cache value
        $user = $this->cache->get('user_123');
        
        // Get with default value if not found
        $config = $this->cache->get('config', ['theme' => 'default']);
        
        // Check if key exists
        if ($this->cache->has('user_123')) {
            echo "User found in cache\n";
        }
        
        // Delete a cache entry
        $this->cache->delete('user_123');
        
        // Clear all cache
        $this->cache->clear();
    }
    
    // Multiple items operations
    public function multipleOperations(): void
    {
        // Set multiple values at once
        $values = [
            'user_1' => ['name' => 'Alice'],
            'user_2' => ['name' => 'Bob'],
            'user_3' => ['name' => 'Charlie']
        ];
        $this->cache->setMultiple($values, 3600);
        
        // Get multiple values at once
        $users = $this->cache->getMultiple(['user_1', 'user_2', 'user_3']);
        
        // Delete multiple values
        $this->cache->deleteMultiple(['user_1', 'user_2']);
    }
    
    // TTL examples
    public function ttlExamples(): void
    {
        // Cache for 60 seconds
        $this->cache->set('temp_data', 'value', 60);
        
        // Cache with DateInterval (1 day)
        $this->cache->set('daily_data', 'value', new \DateInterval('P1D'));
        
        // Cache forever
        $this->cache->set('permanent_data', 'value', null);
        
        // Immediate expiration
        $this->cache->set('expired_data', 'value', 0);
    }
}

/**
 * BENEFITS OF PSR-16:
 * 
 * 1. Simplicity - Easier to use than PSR-6 for basic caching
 * 2. Interoperability - Switch cache implementations easily
 * 3. Multiple operations - Efficient batch operations
 * 4. Flexible TTL - Supports multiple TTL formats
 * 5. Type safety - Clear method signatures
 * 
 * COMMON IMPLEMENTATIONS:
 * 
 * - symfony/cache (Symfony Cache component)
 * - phpfastcache/phpfastcache
 * - cache/array-adapter
 * - cache/filesystem-adapter
 * - cache/memcached-adapter
 * - cache/redis-adapter
 * 
 * WHEN TO USE PSR-16 vs PSR-6:
 * 
 * Use PSR-16 when:
 * - Simple caching needs
 * - Don't need cache pools
 * - Don't need deferred saves
 * - Want simpler API
 * 
 * Use PSR-6 when:
 * - Need advanced features
 * - Need cache pools
 * - Need deferred saves
 * - Need cache tags/metadata
 */

// Installation via Composer:
// composer require psr/simple-cache

?>