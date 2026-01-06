# Constructor Property Promotion

## Overview

Constructor property promotion allows declaring and initializing class properties directly in the constructor parameter list, reducing boilerplate code and improving readability.

---

## Table of Contents

1. Basic Promotion
2. With Visibility Modifiers
3. With Type Declarations
4. Mixed Properties
5. With Default Values
6. Use Cases
7. Best Practices
8. Complete Example

---

## Basic Promotion

```php
<?php
// Traditional way
class UserTraditional {
    private string $name;
    private string $email;
    
    public function __construct(string $name, string $email) {
        $this->name = $name;
        $this->email = $email;
    }
}

// PHP 8 promoted properties
class UserPromoted {
    public function __construct(
        private string $name,
        private string $email
    ) {}
}

$user = new UserPromoted("Alice", "alice@example.com");
?>
```

---

## With Visibility Modifiers

```php
<?php
class Product {
    public function __construct(
        public string $name,
        public float $price,
        private string $sku,
        protected int $stockLevel
    ) {}
    
    public function getSku(): string {
        return $this->sku;
    }
}

$product = new Product("Laptop", 999.99, "SKU-001", 10);
echo $product->name; // Public - accessible
echo $product->getSku(); // Private - via method
?>
```

---

## With Type Declarations

```php
<?php
class Article {
    public function __construct(
        public string $title,
        public string $content,
        public int $views = 0,
        public array $tags = [],
        public ?string $author = null
    ) {}
}

$article = new Article(
    "PHP 8 Features",
    "Content here",
    100,
    ["php", "web"],
    "John Doe"
);

echo $article->title;
echo count($article->tags);
?>
```

---

## Mixed Properties

```php
<?php
class Configuration {
    private string $appName;
    
    public function __construct(
        private string $host,
        private int $port,
        string $appName,
        public bool $debug = false
    ) {
        $this->appName = strtoupper($appName);
    }
    
    public function getAppName(): string {
        return $this->appName;
    }
}

$config = new Configuration("localhost", 3306, "myapp", true);
echo $config->host; // Promoted property
echo $config->getAppName(); // Non-promoted property
?>
```

---

## Inheritance with Promoted Properties

```php
<?php
class BaseEntity {
    public function __construct(
        public int $id,
        public string $createdAt
    ) {}
}

class User extends BaseEntity {
    public function __construct(
        int $id,
        string $createdAt,
        public string $name,
        public string $email
    ) {
        parent::__construct($id, $createdAt);
    }
}

$user = new User(1, date('Y-m-d'), "Alice", "alice@example.com");
echo $user->id; // From parent
echo $user->name; // From child
?>
```

---

## Use Cases

### 1. Data Transfer Objects (DTOs)

```php
<?php
class CreateUserDTO {
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $phone = "",
        public bool $active = true
    ) {}
}

$dto = new CreateUserDTO(
    name: "John",
    email: "john@example.com",
    password: "secret"
);
?>
```

### 2. Value Objects

```php
<?php
class Money {
    public function __construct(
        public float $amount,
        public string $currency = "USD"
    ) {}
    
    public function format(): string {
        return "$this->currency " . number_format($this->amount, 2);
    }
}

$price = new Money(99.99, "EUR");
echo $price->format(); // EUR 99.99
?>
```

### 3. Database Models

```php
<?php
class Post {
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public int $userId,
        public \DateTime $createdAt,
        public ?\DateTime $updatedAt = null
    ) {}
    
    public function getSlug(): string {
        return strtolower(str_replace(' ', '-', $this->title));
    }
}
?>
```

---

## Best Practices

### 1. Use with Value Objects

```php
<?php
class Address {
    public function __construct(
        public string $street,
        public string $city,
        public string $state,
        public string $zipCode
    ) {}
}

$address = new Address(
    street: "123 Main St",
    city: "New York",
    state: "NY",
    zipCode: "10001"
);
?>
```

### 2. Combine with Validation

```php
<?php
class Email {
    public function __construct(
        private string $email
    ) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email");
        }
    }
    
    public function getValue(): string {
        return $this->email;
    }
}

$email = new Email("user@example.com");
?>
```

### 3. Document Promoted Properties

```php
<?php
/**
 * @param string $name User's full name
 * @param string $email User's email address
 * @param int $age User's age
 */
class Person {
    public function __construct(
        public string $name,
        public string $email,
        public int $age
    ) {}
}
?>
```

---

## Common Mistakes

### 1. Not Using Visibility Modifiers

```php
<?php
// ❌ Wrong - all properties are public
class DataContainer {
    public function __construct(
        string $id,
        string $secret
    ) {}
}

// ✅ Correct - control visibility
class SecureContainer {
    public function __construct(
        public string $id,
        private string $secret
    ) {}
}
?>
```

### 2. Mixing with Complex Logic

```php
<?php
// ❌ Wrong - too complex for promotion
class User {
    public function __construct(
        public string $email,
        string $password
    ) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }
}

// ✅ Better
class User {
    private string $password;
    
    public function __construct(
        public string $email,
        string $rawPassword
    ) {
        $this->password = password_hash($rawPassword, PASSWORD_BCRYPT);
    }
}
?>
```

### 3. Over-relying on Getters

```php
<?php
// ❌ Unnecessary - make it public if needed
class Config {
    public function __construct(
        private string $value
    ) {}
    
    public function getValue(): string {
        return $this->value;
    }
}

// ✅ Simpler
class Config {
    public function __construct(
        public string $value
    ) {}
}
?>
```

---

## Complete Example

```php
<?php
class BlogPost {
    private string $slug;
    
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public string $author,
        public array $tags = [],
        public bool $published = false,
        public \DateTime $createdAt = new \DateTime(),
        public ?\DateTime $publishedAt = null
    ) {
        $this->slug = $this->generateSlug();
    }
    
    private function generateSlug(): string {
        return strtolower(
            preg_replace('/[^a-z0-9]+/', '-', $this->title)
        );
    }
    
    public function getSlug(): string {
        return $this->slug;
    }
    
    public function publish(): void {
        $this->published = true;
        $this->publishedAt = new \DateTime();
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'author' => $this->author,
            'tags' => $this->tags,
            'published' => $this->published,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'publishedAt' => $this->publishedAt?->format('Y-m-d H:i:s')
        ];
    }
}

$post = new BlogPost(
    id: 1,
    title: "Getting Started with PHP 8",
    content: "PHP 8 introduces several new features...",
    author: "Jane Doe",
    tags: ["php", "tutorial"],
    published: true
);

$post->publish();
print_r($post->toArray());
?>
```

---

## See Also

- Documentation: [Constructor Property Promotion](https://www.php.net/manual/en/language.oop5.property-promotion.php)
- Related: [Named Arguments](2-named-argument.md), [Union Types](5-union-types.md)
