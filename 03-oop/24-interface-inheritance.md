# Interface Inheritance in PHP

## Table of Contents
1. [Overview](#overview)
2. [Extending Single Interface](#extending-single-interface)
3. [Extending Multiple Interfaces](#extending-multiple-interfaces)
4. [Interface Hierarchies](#interface-hierarchies)
5. [Contract Extension](#contract-extension)
6. [Type Compatibility](#type-compatibility)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Interface inheritance allows interfaces to extend other interfaces, inheriting their method contracts and adding new ones. An interface can extend one or multiple interfaces, creating an interface hierarchy. Classes implementing a child interface must implement all methods from the parent interfaces as well. This enables building sophisticated contract hierarchies that define progressively more specific requirements.

**Key Concepts:**
- Interface can extend other interfaces
- Inherit method contracts
- Add new methods to contract
- Multiple interface inheritance supported
- Classes implement entire hierarchy
- Build contract hierarchies
- Enable progressive specialization

---

## Extending Single Interface

### Basic Interface Extension

```php
<?php
// Base interface
interface Writer {
    public function write($content);
}

// Extended interface
interface Logger extends Writer {
    public function log($message, $level);
}

// Class must implement all methods from entire hierarchy
class ConsoleLogger implements Logger {
    // From Writer interface
    public function write($content) {
        echo $content . "\n";
    }
    
    // From Logger interface
    public function log($message, $level) {
        $this->write("[$level] $message");
    }
}

$logger = new ConsoleLogger();
$logger->write("Direct write");
$logger->log("Error message", "ERROR");
?>
```

### Progressive Specialization

```php
<?php
// Level 1: Basic storage
interface Storage {
    public function put($key, $value);
    public function get($key);
    public function has($key);
}

// Level 2: Storage with time-to-live
interface CachedStorage extends Storage {
    public function putWithTTL($key, $value, $ttl);
    public function isExpired($key);
}

// Level 3: Distributed storage
interface DistributedStorage extends CachedStorage {
    public function replicate($server);
    public function sync();
}

class RedisStorage implements DistributedStorage {
    private $cache = [];
    private $ttl = [];
    
    public function put($key, $value) {
        $this->cache[$key] = $value;
        return $this;
    }
    
    public function get($key) {
        if ($this->isExpired($key)) {
            $this->cache[$key] = null;
        }
        return $this->cache[$key] ?? null;
    }
    
    public function has($key) {
        return isset($this->cache[$key]) && !$this->isExpired($key);
    }
    
    public function putWithTTL($key, $value, $ttl) {
        $this->cache[$key] = $value;
        $this->ttl[$key] = time() + $ttl;
        return $this;
    }
    
    public function isExpired($key) {
        if (!isset($this->ttl[$key])) {
            return false;
        }
        return time() > $this->ttl[$key];
    }
    
    public function replicate($server) {
        echo "Replicating to $server\n";
        return $this;
    }
    
    public function sync() {
        echo "Syncing data\n";
        return $this;
    }
}

$redis = new RedisStorage();
$redis->putWithTTL('session', 'data123', 3600);
echo $redis->get('session') . "\n";
?>
```

---

## Extending Multiple Interfaces

### Multiple Interface Inheritance

```php
<?php
interface Identifiable {
    public function getId();
}

interface Timestamped {
    public function getCreatedAt();
    public function getUpdatedAt();
}

interface Authorable {
    public function getAuthor();
}

// Extend multiple interfaces
interface BlogPost extends Identifiable, Timestamped, Authorable {
    public function getTitle();
    public function getContent();
}

class Post implements BlogPost {
    private $id;
    private $title;
    private $content;
    private $author;
    private $createdAt;
    private $updatedAt;
    
    public function __construct($id, $title, $author) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function setContent($content) {
        $this->content = $content;
        $this->updatedAt = date('Y-m-d H:i:s');
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    public function getAuthor() {
        return $this->author;
    }
}

$post = new Post(1, "My First Post", "John Doe");
$post->setContent("Post content here");

echo "ID: " . $post->getId() . "\n";
echo "Title: " . $post->getTitle() . "\n";
echo "Author: " . $post->getAuthor() . "\n";
echo "Created: " . $post->getCreatedAt() . "\n";
?>
```

### Diamond Problem Resolution

```php
<?php
// Although PHP doesn't have "diamond problem" like C++
// due to method naming, here's the concept:

interface Named {
    public function getName();
}

interface Describable {
    public function getDescription();
}

// Both extend Named - no conflict because interface names resolve uniquely
interface Entity extends Named, Describable {
    public function validate();
}

class Product implements Entity {
    private $name;
    private $description;
    
    public function __construct($name, $description) {
        $this->name = $name;
        $this->description = $description;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function validate() {
        return !empty($this->name) && !empty($this->description);
    }
}

$product = new Product("Laptop", "High-performance computer");
echo $product->getName() . "\n";
echo $product->getDescription() . "\n";
?>
```

---

## Interface Hierarchies

### Three-Level Hierarchy

```php
<?php
// Level 1: Base interface
interface DataAccessor {
    public function read($id);
}

// Level 2: Extends Level 1
interface DataModifier extends DataAccessor {
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}

// Level 3: Extends Level 2
interface FullRepository extends DataModifier {
    public function findAll();
    public function findByQuery($query);
}

// Implementation must satisfy entire hierarchy
class UserRepository implements FullRepository {
    private $users = [];
    
    // From DataAccessor
    public function read($id) {
        return $this->users[$id] ?? null;
    }
    
    // From DataModifier
    public function create(array $data) {
        $data['id'] = count($this->users) + 1;
        $this->users[$data['id']] = $data;
        return $data['id'];
    }
    
    public function update($id, array $data) {
        if (isset($this->users[$id])) {
            $this->users[$id] = array_merge($this->users[$id], $data);
            return true;
        }
        return false;
    }
    
    public function delete($id) {
        if (isset($this->users[$id])) {
            unset($this->users[$id]);
            return true;
        }
        return false;
    }
    
    // From FullRepository
    public function findAll() {
        return $this->users;
    }
    
    public function findByQuery($query) {
        $results = [];
        foreach ($this->users as $user) {
            if (stripos(json_encode($user), $query) !== false) {
                $results[] = $user;
            }
        }
        return $results;
    }
}

$repo = new UserRepository();
$id = $repo->create(['name' => 'John', 'email' => 'john@example.com']);
$repo->update($id, ['name' => 'John Doe']);
print_r($repo->read($id));
?>
```

### Complex Hierarchy

```php
<?php
// Infrastructure interfaces
interface Serializable {
    public function serialize();
}

interface Deserializable {
    public function deserialize($data);
}

interface Formattable {
    public function format();
}

// Domain interfaces
interface Model extends Serializable, Deserializable {
    public function validate();
}

interface ViewModel extends Model, Formattable {
    public function prepare();
}

// Implementation
class UserModel implements ViewModel {
    private $data = [];
    
    public function serialize() {
        return json_encode($this->data);
    }
    
    public function deserialize($data) {
        $this->data = json_decode($data, true);
    }
    
    public function validate() {
        return !empty($this->data['email']);
    }
    
    public function format() {
        return "User: {$this->data['name']}";
    }
    
    public function prepare() {
        // Prepare for display
        $this->data['name'] = ucfirst($this->data['name']);
    }
}
?>
```

---

## Contract Extension

### Adding Requirements

```php
<?php
// Original contract
interface Vehicle {
    public function start();
    public function stop();
}

// Extended contract - adds more requirements
interface ModernVehicle extends Vehicle {
    public function isElectric();
    public function getRange();
}

class Car implements ModernVehicle {
    private $isRunning = false;
    private $fuel = 50;
    
    public function start() {
        $this->isRunning = true;
        echo "Car started\n";
    }
    
    public function stop() {
        $this->isRunning = false;
        echo "Car stopped\n";
    }
    
    public function isElectric() {
        return false;
    }
    
    public function getRange() {
        return $this->fuel * 5;  // 5 km per liter
    }
}

class ElectricCar implements ModernVehicle {
    private $isRunning = false;
    private $batteryLevel = 100;
    
    public function start() {
        $this->isRunning = true;
        echo "Electric car started\n";
    }
    
    public function stop() {
        $this->isRunning = false;
        echo "Electric car stopped\n";
    }
    
    public function isElectric() {
        return true;
    }
    
    public function getRange() {
        return $this->batteryLevel * 2;  // 2 km per percent
    }
}

// Old code accepting Vehicle still works
function inspectVehicle(Vehicle $vehicle) {
    $vehicle->start();
    $vehicle->stop();
}

// New code can require ModernVehicle for newer features
function inspectModernVehicle(ModernVehicle $vehicle) {
    $vehicle->start();
    echo "Electric: " . ($vehicle->isElectric() ? "Yes" : "No") . "\n";
    echo "Range: " . $vehicle->getRange() . " km\n";
    $vehicle->stop();
}

$car = new Car();
inspectVehicle($car);

$eCar = new ElectricCar();
inspectModernVehicle($eCar);
?>
```

---

## Type Compatibility

### Working with Interface Hierarchy

```php
<?php
interface Animal {
    public function move();
}

interface Mammal extends Animal {
    public function nurse();
}

interface Dog extends Mammal {
    public function bark();
}

class Bulldog implements Dog {
    public function move() {
        return "Running";
    }
    
    public function nurse() {
        return "Nursing puppies";
    }
    
    public function bark() {
        return "Woof!";
    }
}

$bulldog = new Bulldog();

// All these work - type compatibility through hierarchy
echo ($bulldog instanceof Dog) ? "Is Dog\n" : "";      // true
echo ($bulldog instanceof Mammal) ? "Is Mammal\n" : "";  // true
echo ($bulldog instanceof Animal) ? "Is Animal\n" : "";  // true

// Functions accepting parent interface accept child classes
function makeAnimalMove(Animal $animal) {
    echo $animal->move() . "\n";
}

function makeMammalNurse(Mammal $mammal) {
    echo $mammal->nurse() . "\n";
}

function makeDogBark(Dog $dog) {
    echo $dog->bark() . "\n";
}

makeAnimalMove($bulldog);
makeMammalNurse($bulldog);
makeDogBark($bulldog);
?>
```

---

## Practical Examples

### API Versioning

```php
<?php
// Version 1 API
interface APIv1 {
    public function getUser($id);
    public function listUsers();
}

// Version 2 extends Version 1
interface APIv2 extends APIv1 {
    public function searchUsers($query);
    public function getUsersByRole($role);
}

// Version 3 extends Version 2
interface APIv3 extends APIv2 {
    public function getPaginatedUsers($page, $limit);
    public function filterUsers(array $filters);
}

class UserAPI implements APIv3 {
    private $users = [
        ['id' => 1, 'name' => 'John', 'role' => 'admin'],
        ['id' => 2, 'name' => 'Jane', 'role' => 'user'],
        ['id' => 3, 'name' => 'Bob', 'role' => 'user'],
    ];
    
    // APIv1 methods
    public function getUser($id) {
        return $this->users[$id - 1] ?? null;
    }
    
    public function listUsers() {
        return $this->users;
    }
    
    // APIv2 methods
    public function searchUsers($query) {
        return array_filter($this->users, function($user) use ($query) {
            return stripos($user['name'], $query) !== false;
        });
    }
    
    public function getUsersByRole($role) {
        return array_filter($this->users, fn($u) => $u['role'] === $role);
    }
    
    // APIv3 methods
    public function getPaginatedUsers($page, $limit) {
        $offset = ($page - 1) * $limit;
        return array_slice($this->users, $offset, $limit);
    }
    
    public function filterUsers(array $filters) {
        $results = $this->users;
        
        if (isset($filters['role'])) {
            $results = array_filter($results, fn($u) => $u['role'] === $filters['role']);
        }
        
        return $results;
    }
}

// Can use any version based on requirements
$api = new UserAPI();

// V1 functionality
print_r($api->getUser(1));
print_r($api->listUsers());

// V2 functionality
print_r($api->searchUsers('John'));

// V3 functionality
print_r($api->getPaginatedUsers(1, 2));
?>
```

---

## Common Mistakes

### 1. Circular Inheritance

```php
<?php
// ❌ Wrong: Circular interface inheritance (causes fatal error)
// interface A extends B {}
// interface B extends A {}

// ✓ Correct: Linear or proper hierarchy
interface A {}
interface B extends A {}
interface C extends B {}
?>
```

### 2. Missing Implementation

```php
<?php
// ❌ Wrong: Missing methods from parent interfaces
interface Base {
    public function required();
}

interface Extended extends Base {
    public function moreRequired();
}

class Implementation implements Extended {
    public function required() {}
    // Missing moreRequired() from Extended
}

// ✓ Correct: Implement all methods in hierarchy
class Implementation implements Extended {
    public function required() {}
    public function moreRequired() {}
}
?>
```

---

## Complete Working Example

```php
<?php
// CMS Content Management Hierarchy

interface Publishable {
    public function publish();
    public function unpublish();
}

interface Auditable {
    public function getCreatedBy();
    public function getLastModifiedBy();
}

interface Taggable {
    public function addTag($tag);
    public function getTags();
}

interface Searchable {
    public function getSearchContent();
    public function getSearchKeywords();
}

// Article interface extends multiple
interface Article extends Publishable, Auditable, Taggable, Searchable {
    public function getTitle();
    public function getContent();
}

class BlogArticle implements Article {
    private $id;
    private $title;
    private $content;
    private $published = false;
    private $createdBy;
    private $lastModifiedBy;
    private $tags = [];
    
    public function __construct($title, $content, $author) {
        $this->title = $title;
        $this->content = $content;
        $this->createdBy = $author;
        $this->lastModifiedBy = $author;
    }
    
    // Publishable
    public function publish() {
        $this->published = true;
        echo "Article published\n";
    }
    
    public function unpublish() {
        $this->published = false;
        echo "Article unpublished\n";
    }
    
    // Auditable
    public function getCreatedBy() {
        return $this->createdBy;
    }
    
    public function getLastModifiedBy() {
        return $this->lastModifiedBy;
    }
    
    // Taggable
    public function addTag($tag) {
        $this->tags[] = $tag;
        return $this;
    }
    
    public function getTags() {
        return $this->tags;
    }
    
    // Searchable
    public function getSearchContent() {
        return $this->title . ' ' . $this->content;
    }
    
    public function getSearchKeywords() {
        return array_merge([$this->title], $this->tags);
    }
    
    // Article
    public function getTitle() {
        return $this->title;
    }
    
    public function getContent() {
        return $this->content;
    }
}

// Usage
$article = new BlogArticle("PHP OOP Guide", "Complete guide to OOP...", "John Doe");
$article
    ->addTag('php')
    ->addTag('oop')
    ->addTag('programming')
    ->publish();

echo "Title: " . $article->getTitle() . "\n";
echo "Author: " . $article->getCreatedBy() . "\n";
echo "Tags: " . implode(", ", $article->getTags()) . "\n";
echo "Keywords: " . implode(", ", $article->getSearchKeywords()) . "\n";
?>
```

---

## Cross-References

- **Related Topic: [Interfaces](23-interfaces.md)** - Basic interface concepts
- **Related Topic: [Polymorphism](18-polymorphism.md)** - Polymorphic behavior
- **Related Topic: [Type Checking](19-type-checking-casting.md)** - Type compatibility
- **Related Topic: [Abstract Classes](20-abstract-classes.md)** - Similar concept for classes
