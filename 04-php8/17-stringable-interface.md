# Stringable Interface

## Overview

The Stringable interface allows objects to define how they should be converted to strings via `__toString()` method, providing better type safety and self-documenting code.

---

## Basic Stringable Interface

```php
<?php
class User implements Stringable {
    public function __construct(
        private string $name,
        private string $email
    ) {}
    
    public function __toString(): string {
        return "{$this->name} ({$this->email})";
    }
}

$user = new User("Alice", "alice@example.com");
echo $user; // Alice (alice@example.com)
echo (string)$user; // Alice (alice@example.com)
?>
```

---

## Union with String Type

```php
<?php
interface Presenter {
    public function render(): string|Stringable;
}

class HtmlPresenter implements Presenter, Stringable {
    public function __construct(private string $content) {}
    
    public function render(): string|Stringable {
        return $this;
    }
    
    public function __toString(): string {
        return "<div>{$this->content}</div>";
    }
}

$presenter = new HtmlPresenter("Hello");
echo $presenter->render(); // <div>Hello</div>
?>
```

---

## Type Hinting with Stringable

```php
<?php
function formatValue(string|Stringable $value): string {
    return "Value: " . $value;
}

class Money implements Stringable {
    public function __construct(
        private float $amount,
        private string $currency = "USD"
    ) {}
    
    public function __toString(): string {
        return "{$this->currency} {$this->amount}";
    }
}

echo formatValue(new Money(99.99)); // Value: USD 99.99
echo formatValue("test"); // Value: test
?>
```

---

## Common Implementations

### 1. Value Objects

```php
<?php
class Email implements Stringable {
    private string $address;
    
    public function __construct(string $address) {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email");
        }
        $this->address = $address;
    }
    
    public function __toString(): string {
        return $this->address;
    }
    
    public function getDomain(): string {
        return substr($this->address, strrpos($this->address, '@') + 1);
    }
}

$email = new Email("user@example.com");
echo $email; // user@example.com
?>
```

### 2. Currency/Money

```php
<?php
class Currency implements Stringable {
    private const SYMBOLS = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥'
    ];
    
    public function __construct(
        private float $amount,
        private string $code = 'USD'
    ) {}
    
    public function __toString(): string {
        $symbol = self::SYMBOLS[$this->code] ?? $this->code;
        return sprintf('%s%,.2f', $symbol, $this->amount);
    }
    
    public function add(Currency $other): Currency {
        if ($this->code !== $other->code) {
            throw new InvalidArgumentException("Currency mismatch");
        }
        return new Currency($this->amount + $other->amount, $this->code);
    }
}

$price = new Currency(99.99, 'USD');
echo $price; // $99.99
?>
```

### 3. URLs

```php
<?php
class URL implements Stringable {
    private string $scheme = 'https';
    private string $host;
    private int $port = 443;
    private string $path = '/';
    private array $query = [];
    
    public function __construct(string $host) {
        $this->host = $host;
    }
    
    public function __toString(): string {
        $url = "{$this->scheme}://{$this->host}";
        
        if (($this->scheme === 'https' && $this->port !== 443) ||
            ($this->scheme === 'http' && $this->port !== 80)) {
            $url .= ":{$this->port}";
        }
        
        $url .= $this->path;
        
        if ($this->query) {
            $url .= '?' . http_build_query($this->query);
        }
        
        return $url;
    }
    
    public function withPath(string $path): self {
        $this->path = $path;
        return $this;
    }
    
    public function withQuery(array $query): self {
        $this->query = $query;
        return $this;
    }
}

$url = new URL('example.com');
$url->withPath('/api/users')->withQuery(['page' => 1]);
echo $url; // https://example.com/api/users?page=1
?>
```

### 4. File Paths

```php
<?php
class FilePath implements Stringable {
    private array $segments = [];
    
    public function __construct(string $path = '') {
        $this->segments = array_filter(explode('/', $path));
    }
    
    public function __toString(): string {
        return '/' . implode('/', $this->segments);
    }
    
    public function append(string $segment): self {
        $this->segments[] = $segment;
        return $this;
    }
    
    public function getExtension(): string {
        $last = end($this->segments);
        return substr(strrchr($last, '.'), 1) ?: '';
    }
}

$path = new FilePath('/var/www');
$path->append('html')->append('index.php');
echo $path; // /var/www/html/index.php
?>
```

---

## Real-World Examples

### 1. Log Message

```php
<?php
class LogEntry implements Stringable {
    public function __construct(
        private string $level,
        private string $message,
        private \DateTime $timestamp,
        private array $context = []
    ) {}
    
    public function __toString(): string {
        $time = $this->timestamp->format('Y-m-d H:i:s');
        $msg = $this->message;
        
        if ($this->context) {
            $replacements = array_map(fn($v) => (string)$v, $this->context);
            $msg = str_replace(array_keys($replacements), array_values($replacements), $msg);
        }
        
        return "[$time] {$this->level}: $msg";
    }
}

$entry = new LogEntry('INFO', 'User {user} logged in', new DateTime(), ['{user}' => 'alice']);
echo $entry; // [2024-01-15 10:30:45] INFO: User alice logged in
?>
```

### 2. HTTP Request

```php
<?php
class Request implements Stringable {
    public function __construct(
        private string $method,
        private string $path,
        private array $headers = []
    ) {}
    
    public function __toString(): string {
        $request = "{$this->method} {$this->path} HTTP/1.1\r\n";
        $request .= "Host: " . ($this->headers['Host'] ?? 'localhost') . "\r\n";
        
        foreach ($this->headers as $name => $value) {
            $request .= "$name: $value\r\n";
        }
        
        return $request;
    }
}

$request = new Request('GET', '/api/users', ['Authorization' => 'Bearer token']);
echo $request;
?>
```

### 3. Person Record

```php
<?php
class Person implements Stringable {
    public function __construct(
        private string $firstName,
        private string $lastName,
        private \DateTime $birthDate
    ) {}
    
    public function __toString(): string {
        $age = (new \DateTime())->diff($this->birthDate)->y;
        return "{$this->firstName} {$this->lastName}, age {$age}";
    }
}

$person = new Person('John', 'Doe', new DateTime('1990-01-15'));
echo $person; // John Doe, age 34
?>
```

---

## Best Practices

### 1. Implement Properly

```php
<?php
// ✅ Good - implements Stringable interface
class Status implements Stringable {
    public function __toString(): string {
        return $this->status;
    }
}

// ❌ Avoid - just __toString without interface
class OldStatus {
    public function __toString(): string {
        return $this->status;
    }
}

// Benefits: Better type safety with string|Stringable
function display(string|Stringable $value) {
    echo $value;
}
?>
```

### 2. Document String Representation

```php
<?php
/**
 * Represents a user in the system
 * 
 * When converted to string, returns the user's name and email.
 * Example: "Alice (alice@example.com)"
 */
class User implements Stringable {
    public function __toString(): string {
        return "{$this->name} ({$this->email})";
    }
}
?>
```

### 3. Handle Edge Cases

```php
<?php
class Product implements Stringable {
    public function __toString(): string {
        $name = htmlspecialchars($this->name, ENT_QUOTES);
        $price = number_format($this->price, 2);
        
        return "{$name} - \${$price}";
    }
}
?>
```

---

## Common Mistakes

### 1. Not Handling Special Characters

```php
<?php
// ❌ Wrong - unescaped output
class HtmlElement implements Stringable {
    public function __toString(): string {
        return "<div>{$this->content}</div>"; // Could be XSS
    }
}

// ✅ Correct - escaped output
class SafeHtmlElement implements Stringable {
    public function __toString(): string {
        return "<div>" . htmlspecialchars($this->content) . "</div>";
    }
}
?>
```

### 2. Throwing Exceptions

```php
<?php
// ❌ Wrong - exceptions in __toString cause fatal error
class BadValue implements Stringable {
    public function __toString(): string {
        if ($this->invalid) {
            throw new RuntimeException('Invalid!'); // Fatal!
        }
        return (string)$this->value;
    }
}

// ✅ Correct - handle errors gracefully
class SafeValue implements Stringable {
    public function __toString(): string {
        return $this->isValid() ? $this->getValue() : '[Invalid Value]';
    }
}
?>
```

---

## Complete Example

```php
<?php
class OrderSummary implements Stringable {
    private array $items = [];
    private float $subtotal = 0;
    private float $taxRate = 0.1;
    
    public function addItem(string $name, float $price, int $quantity = 1): self {
        $this->items[] = ['name' => $name, 'price' => $price, 'qty' => $quantity];
        $this->subtotal += $price * $quantity;
        return $this;
    }
    
    public function __toString(): string {
        $summary = "=== Order Summary ===\n";
        
        foreach ($this->items as $item) {
            $total = $item['price'] * $item['qty'];
            $summary .= sprintf(
                "%-30s %d x $%.2f = $%.2f\n",
                $item['name'],
                $item['qty'],
                $item['price'],
                $total
            );
        }
        
        $tax = $this->subtotal * $this->taxRate;
        $total = $this->subtotal + $tax;
        
        $summary .= str_repeat("-", 50) . "\n";
        $summary .= sprintf("Subtotal:  $%.2f\n", $this->subtotal);
        $summary .= sprintf("Tax (10%%):  $%.2f\n", $tax);
        $summary .= sprintf("Total:     $%.2f\n", $total);
        
        return $summary;
    }
}

$order = new OrderSummary();
$order->addItem('Widget', 19.99, 2)
      ->addItem('Gadget', 29.99, 1)
      ->addItem('Doohickey', 9.99, 3);

echo $order;
?>
```

---

## See Also

- Documentation: [Stringable Interface](https://www.php.net/manual/en/class.stringable.php)
- Related: [Magic Methods](../03-oop/34-magic-function.md), [Union Types](5-union-types.md)
