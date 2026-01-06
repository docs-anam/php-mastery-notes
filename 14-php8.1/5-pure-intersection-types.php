<?php

/**
 * Pure Intersection Types in PHP 8.1
 * 
 * Intersection types allow you to declare that a value must be of ALL specified types simultaneously.
 * This is denoted using the ampersand (&) symbol between type names.
 * 
 * Key Points:
 * - Intersection types require a value to satisfy ALL types in the intersection
 * - Most commonly used with interfaces and class types
 * - Cannot use built-in scalar types (int, string, etc.) as they can't intersect
 * - Useful for declaring that an object implements multiple interfaces
 * - The order of types doesn't matter: A&B is the same as B&A
 * - Can be used in: parameters, return types, and property types
 */

// Example 1: Basic Intersection Type with Interfaces
interface Loggable {
    public function log(): void;
}

interface Serializable {
    public function serialize(): string;
}

class User implements Loggable, Serializable {
    public function __construct(private string $name) {}
    
    public function log(): void {
        echo "Logging user: {$this->name}\n";
    }
    
    public function serialize(): string {
        return json_encode(['name' => $this->name]);
    }
    
    public function unserialize(string $data): void {
        $decoded = json_decode($data, true);
        $this->name = $decoded['name'] ?? '';
    }
}

// Function accepting intersection type
function processEntity(Loggable&Serializable $entity): void {
    $entity->log();
    echo $entity->serialize() . "\n";
}

$user = new User("John Doe");
processEntity($user); // Works because User implements both interfaces

// Example 2: Intersection Types in Class Properties (PHP 8.1+)
class DataProcessor {
    public function __construct(
        private Loggable&Serializable $entity
    ) {}
    
    public function process(): string {
        $this->entity->log();
        return $this->entity->serialize();
    }
}

// Example 3: Return Type Intersection
interface Countable {
    public function count(): int;
}

interface IterableCollection {
    public function getIterator(): Iterator;
}

class Collection implements Countable, IterableCollection {
    private array $items = [];
    
    public function count(): int {
        return count($this->items);
    }
    
    public function getIterator(): Iterator {
        return new ArrayIterator($this->items);
    }
}

function createCollection(): Countable&IterableCollection {
    return new Collection();
}

// Example 4: Complex Intersection Types
interface Cacheable {
    public function getCacheKey(): string;
}

class Article implements Loggable, Serializable, Cacheable {
    public function __construct(private string $title) {}
    
    public function log(): void {
        echo "Article: {$this->title}\n";
    }
    
    public function serialize(): string {
        return json_encode(['title' => $this->title]);
    }
    
    public function unserialize(string $data): void {
        $decoded = json_decode($data, true);
        $this->title = $decoded['title'] ?? '';
    }
    
    public function getCacheKey(): string {
        return 'article_' . md5($this->title);
    }
}

function cacheEntity(Serializable&Cacheable $entity): void {
    $key = $entity->getCacheKey();
    $data = $entity->serialize();
    echo "Caching $key: $data\n";
}

$article = new Article("PHP 8.1 Features");
cacheEntity($article);

/**
 * Important Notes:
 * 
 * 1. Intersection types are the opposite of Union types (|)
 *    - Union: A|B means "A OR B"
 *    - Intersection: A&B means "A AND B"
 * 
 * 2. You cannot mix union and intersection types without using DNF types (PHP 8.2)
 * 
 * 3. Common use cases:
 *    - Ensuring an object implements multiple interfaces
 *    - Type-safe dependency injection
 *    - Enforcing behavioral contracts
 * 
 * 4. Limitations:
 *    - Cannot intersect incompatible types (e.g., string&int is impossible)
 *    - Primarily useful with object types (classes/interfaces)
 *    - Class types can only intersect with interfaces
 */
