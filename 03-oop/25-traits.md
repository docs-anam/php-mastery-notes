# Traits in PHP

## Table of Contents
1. [Overview](#overview)
2. [Trait Basics](#trait-basics)
3. [Using Traits](#using-traits)
4. [Multiple Traits](#multiple-traits)
5. [Trait Properties](#trait-properties)
6. [Trait Methods](#trait-methods)
7. [Practical Examples](#practical-examples)
8. [Common Mistakes](#common-mistakes)
9. [Complete Working Example](#complete-working-example)
10. [Cross-References](#cross-references)

---

## Overview

Traits solve the problem of code reuse in single inheritance languages by allowing horizontal code sharing. A trait is a mechanism to reuse sets of methods in multiple classes without using inheritance. Traits can contain concrete methods and properties. Unlike interfaces, traits provide implementations. A class can use multiple traits, solving the limitation of single inheritance while avoiding diamond problem complexity.

**Key Concepts:**
- Reusable method collections
- Support multiple composition
- Contain concrete methods and properties
- Reduce code duplication
- Horizontal reuse (not vertical)
- Can be combined in classes
- Enable mixin-like functionality

---

## Trait Basics

### Simple Trait

```php
<?php
// Define a trait
trait Logger {
    public function log($message) {
        echo "[LOG] $message\n";
    }
    
    public function error($message) {
        echo "[ERROR] $message\n";
    }
}

// Use trait in class
class Database {
    use Logger;
    
    public function connect() {
        $this->log("Connecting to database");
    }
    
    public function query($sql) {
        $this->log("Executing: $sql");
    }
}

class API {
    use Logger;
    
    public function handleRequest() {
        $this->log("Handling API request");
    }
}

// Both classes have Logger methods
$db = new Database();
$db->connect();      // [LOG] Connecting to database
$db->error("Failed"); // [ERROR] Failed

$api = new API();
$api->log("API started");
?>
```

### Trait with State

```php
<?php
trait Timestamped {
    protected $createdAt;
    protected $updatedAt;
    
    public function setCreatedAt($date) {
        $this->createdAt = $date;
    }
    
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    public function setUpdatedAt($date) {
        $this->updatedAt = $date;
    }
    
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    public function touch() {
        $this->updatedAt = date('Y-m-d H:i:s');
        return $this;
    }
}

class Document {
    use Timestamped;
    
    private $title;
    
    public function __construct($title) {
        $this->title = $title;
        $this->setCreatedAt(date('Y-m-d H:i:s'));
        $this->setUpdatedAt(date('Y-m-d H:i:s'));
    }
    
    public function getTitle() {
        return $this->title;
    }
}

$doc = new Document("My Document");
echo $doc->getTitle() . "\n";
echo $doc->getCreatedAt() . "\n";
$doc->touch();
echo $doc->getUpdatedAt() . "\n";
?>
```

---

## Using Traits

### Basic Trait Usage

```php
<?php
trait Comparable {
    public function equals($other) {
        return $this == $other;
    }
    
    public function greaterThan($other) {
        return $this > $other;
    }
    
    public function lessThan($other) {
        return $this < $other;
    }
}

trait Printable {
    public function toString() {
        return json_encode($this);
    }
}

// Single trait
class Number {
    use Comparable;
    
    public $value;
    
    public function __construct($value) {
        $this->value = $value;
    }
}

// Multiple traits
class Document {
    use Comparable, Printable;
    
    public $content;
    public $pages;
    
    public function __construct($content, $pages) {
        $this->content = $content;
        $this->pages = $pages;
    }
}

$num1 = new Number(5);
$num2 = new Number(10);
echo $num1->lessThan($num2) ? "5 < 10\n" : "";

$doc = new Document("Hello", 10);
echo $doc->toString() . "\n";
?>
```

### Trait Inheritance

```php
<?php
trait Base {
    public function baseMethod() {
        return "Base";
    }
}

// Trait extending trait
trait Extended extends Base {  // Invalid - traits don't extend
    // Traits can only "use" other traits
}

// Correct: Use another trait
trait Logging {
    public function log($msg) {
        echo "[LOG] $msg\n";
    }
}

trait Validation {
    use Logging;  // Trait using another trait
    
    public function validate($data) {
        $this->log("Validating data");
        return !empty($data);
    }
}

class Form {
    use Validation;
}

$form = new Form();
$form->validate(['name' => 'John']);
?>
```

---

## Multiple Traits

### Using Multiple Traits

```php
<?php
trait HasTimestamps {
    protected $createdAt;
    protected $updatedAt;
    
    public function timestamps() {
        $this->createdAt = time();
        $this->updatedAt = time();
    }
}

trait HasAuthor {
    protected $author;
    
    public function setAuthor($author) {
        $this->author = $author;
    }
    
    public function getAuthor() {
        return $this->author;
    }
}

trait HasMetadata {
    protected $metadata = [];
    
    public function setMeta($key, $value) {
        $this->metadata[$key] = $value;
    }
    
    public function getMeta($key) {
        return $this->metadata[$key] ?? null;
    }
}

// Use multiple traits
class BlogPost {
    use HasTimestamps, HasAuthor, HasMetadata;
    
    private $title;
    
    public function __construct($title) {
        $this->title = $title;
        $this->timestamps();
    }
    
    public function getTitle() {
        return $this->title;
    }
}

$post = new BlogPost("My Post");
$post->setAuthor("John");
$post->setMeta('category', 'PHP');

echo $post->getTitle() . "\n";
echo $post->getAuthor() . "\n";
echo $post->getMeta('category') . "\n";
?>
```

### Trait Precedence

```php
<?php
trait ALogger {
    public function log($msg) {
        echo "[A] $msg\n";
    }
}

trait BLogger {
    public function log($msg) {
        echo "[B] $msg\n";
    }
}

// First trait in list takes precedence
class Service1 {
    use ALogger, BLogger;
}

class Service2 {
    use BLogger, ALogger;
}

$s1 = new Service1();
$s1->log("test");  // [A] test

$s2 = new Service2();
$s2->log("test");  // [B] test

// Class method overrides trait method
class Service3 {
    use ALogger;
    
    public function log($msg) {
        echo "[CLASS] $msg\n";
    }
}

$s3 = new Service3();
$s3->log("test");  // [CLASS] test
?>
```

---

## Trait Properties

### Properties in Traits

```php
<?php
trait Configuration {
    protected $config = [];
    
    public function set($key, $value) {
        $this->config[$key] = $value;
        return $this;
    }
    
    public function get($key, $default = null) {
        return $this->config[$key] ?? $default;
    }
    
    public function getAll() {
        return $this->config;
    }
}

class Application {
    use Configuration;
    
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
        $this->config['app_name'] = $name;
    }
    
    public function getName() {
        return $this->name;
    }
}

$app = new Application("MyApp");
$app
    ->set('debug', true)
    ->set('timezone', 'UTC');

echo $app->get('debug') ? "Debug on\n" : "Debug off\n";
print_r($app->getAll());
?>
```

### Property Visibility

```php
<?php
trait Cacheable {
    private $cache = [];
    protected $cacheEnabled = true;
    
    public function cache($key, $value) {
        if ($this->cacheEnabled) {
            $this->cache[$key] = $value;
        }
    }
    
    public function getFromCache($key) {
        return $this->cache[$key] ?? null;
    }
    
    protected function clearCache() {
        $this->cache = [];
    }
}

class DataStore {
    use Cacheable;
    
    public function getData($id) {
        if ($cached = $this->getFromCache("data_$id")) {
            return $cached;
        }
        
        // Fetch from source
        $data = ['id' => $id, 'value' => 'data'];
        $this->cache("data_$id", $data);
        return $data;
    }
    
    public function disableCache() {
        $this->cacheEnabled = false;
    }
}

$store = new DataStore();
$data = $store->getData(1);
$data = $store->getData(1);  // From cache
?>
```

---

## Trait Methods

### Method Types in Traits

```php
<?php
trait Math {
    // Public method
    public function add($a, $b) {
        return $a + $b;
    }
    
    // Protected method - only accessible in class
    protected function multiply($a, $b) {
        return $a * $b;
    }
    
    // Private method - only in trait
    private function divide($a, $b) {
        return $b != 0 ? $a / $b : 0;
    }
    
    public function calculate($a, $b) {
        return $this->multiply($a, $b);
    }
}

class Calculator {
    use Math;
}

$calc = new Calculator();
echo $calc->add(5, 3) . "\n";              // 8
echo $calc->calculate(5, 3) . "\n";        // 15
// $calc->multiply(5, 3);  // Error - protected
?>
```

### Abstract Methods in Traits

```php
<?php
trait SaveableValidator {
    // Abstract method - must be implemented by class
    abstract public function validate();
    
    public function saveIfValid($data) {
        if ($this->validate()) {
            return $this->save($data);
        }
        return false;
    }
    
    abstract protected function save($data);
}

class User {
    use SaveableValidator;
    
    public function validate() {
        return true;  // Implement abstract method
    }
    
    protected function save($data) {
        echo "User saved\n";
        return true;
    }
}

$user = new User();
$user->saveIfValid(['name' => 'John']);
?>
```

---

## Practical Examples

### Utility Traits

```php
<?php
trait ArrayHelper {
    public function arrayToString(array $arr, $glue = ', ') {
        return implode($glue, array_map(fn($v) => json_encode($v), $arr));
    }
    
    public function findInArray(array $arr, $needle) {
        return array_search($needle, $arr);
    }
    
    public function filterArray(array $arr, callable $callback) {
        return array_filter($arr, $callback);
    }
}

trait StringHelper {
    public function slugify($string) {
        return strtolower(preg_replace('/[^a-z0-9]+/', '-', $string));
    }
    
    public function camelize($string) {
        return preg_replace_callback('/_([a-z])/', fn($m) => strtoupper($m[1]), $string);
    }
}

class DataProcessor {
    use ArrayHelper, StringHelper;
}

$processor = new DataProcessor();
echo $processor->slugify("My First Post") . "\n";
echo $processor->camelize("user_name") . "\n";
?>
```

---

## Common Mistakes

### 1. Conflicting Method Names

```php
<?php
// ❌ Wrong: Conflicting methods cause fatal error
trait Logger1 {
    public function log($msg) {
        echo "[L1] $msg\n";
    }
}

trait Logger2 {
    public function log($msg) {
        echo "[L2] $msg\n";
    }
}

// class Service {
//     use Logger1, Logger2;  // Fatal error - conflicting
// }

// ✓ Correct: Use insteadof to resolve
class Service {
    use Logger1, Logger2 {
        Logger1::log insteadof Logger2;
        Logger2::log as log2;
    }
}

$service = new Service();
$service->log("message");   // [L1] message
// $service->log2("message");  // [L2] message
?>
```

### 2. Trait Not Used

```php
<?php
// ❌ Wrong: Define trait but don't use
trait Unused {
    public function unused() {
        return "Not used";
    }
}

class MyClass {
    // Forgot to use Unused trait
}

// ✓ Correct: Actually use the trait
class MyClass {
    use Unused;
}
?>
```

---

## Complete Working Example

```php
<?php
// Blog System with Traits

trait Timestamped {
    protected $createdAt;
    protected $updatedAt;
    
    public function setTimestamps() {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        return $this;
    }
    
    public function touch() {
        $this->updatedAt = new DateTime();
        return $this;
    }
}

trait Publishable {
    protected $published = false;
    protected $publishedAt;
    
    public function publish() {
        $this->published = true;
        $this->publishedAt = new DateTime();
        return $this;
    }
    
    public function unpublish() {
        $this->published = false;
        return $this;
    }
    
    public function isPublished() {
        return $this->published;
    }
}

trait Taggable {
    protected $tags = [];
    
    public function addTag($tag) {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
        return $this;
    }
    
    public function getTags() {
        return $this->tags;
    }
}

trait Searchable {
    public function search(array $items, $query) {
        return array_filter($items, function($item) use ($query) {
            return stripos($item->getContent(), $query) !== false;
        });
    }
}

class BlogPost {
    use Timestamped, Publishable, Taggable;
    
    private $title;
    private $content;
    
    public function __construct($title, $content) {
        $this->title = $title;
        $this->content = $content;
        $this->setTimestamps();
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function update($content) {
        $this->content = $content;
        $this->touch();
        return $this;
    }
}

// Usage
$post = new BlogPost("PHP Traits", "Traits provide code reuse...");
$post
    ->addTag('php')
    ->addTag('oop')
    ->publish();

echo "Title: " . $post->getTitle() . "\n";
echo "Published: " . ($post->isPublished() ? "Yes" : "No") . "\n";
echo "Tags: " . implode(", ", $post->getTags()) . "\n";

$post->update("Updated content");
echo "Updated at: " . $post->updatedAt->format('Y-m-d H:i:s') . "\n";
?>
```

---

## Cross-References

- **Related Topic: [Classes](2-class.md)** - Using traits in classes
- **Related Topic: [Inheritance](11-inheritance.md)** - Alternative to inheritance
- **Related Topic: [Interfaces](23-interfaces.md)** - Traits vs interfaces
- **Related Topic: [Abstract Classes](20-abstract-classes.md)** - Abstract methods in traits
