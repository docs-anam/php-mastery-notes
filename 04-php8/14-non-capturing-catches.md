# Non-Capturing Catches

## Overview

PHP 8 allows omitting the variable in catch blocks when the exception variable is not used, reducing unnecessary variable assignments and improving code clarity.

---

## Basic Non-Capturing Catch

```php
<?php
// PHP 7 - required variable even if not used
try {
    risky();
} catch (Exception $e) {
    // Exception not used, but variable required
    echo "An error occurred\n";
}

// PHP 8 - variable can be omitted
try {
    risky();
} catch (Exception) {
    // No variable assignment
    echo "An error occurred\n";
}
?>
```

---

## Multiple Catch Blocks

```php
<?php
try {
    processPayment();
} catch (PaymentFailedException) {
    echo "Payment failed\n";
} catch (InvalidCardException) {
    echo "Invalid card\n";
} catch (NetworkException) {
    echo "Network error\n";
} catch (Exception) {
    echo "Unknown error\n";
}
?>
```

---

## Selective Capturing

```php
<?php
try {
    operation();
} catch (ValidationException $e) {
    // Use exception details
    echo "Validation error: " . $e->getMessage() . "\n";
} catch (DatabaseException) {
    // Not using exception details
    echo "Database error\n";
} catch (RuntimeException $e) {
    // Use exception
    $this->logger->error($e);
} catch (Exception) {
    // Ignore completely
    echo "Generic error\n";
}
?>
```

---

## Use Cases

### 1. Logging Specific Exceptions

```php
<?php
class ExceptionHandler {
    public function handle(\Throwable $exception): void {
        try {
            throw $exception;
        } catch (ValidationException) {
            // Log as warning
            echo "Validation warning\n";
        } catch (DatabaseException $e) {
            // Log detailed error
            echo "DB Error: " . $e->getMessage() . "\n";
        } catch (Exception) {
            // Generic handling
            echo "Generic exception\n";
        }
    }
}
?>
```

### 2. HTTP Response Handling

```php
<?php
function sendRequest(string $url): string {
    try {
        return file_get_contents($url);
    } catch (SocketException) {
        return "Connection failed";
    } catch (TimeoutException) {
        return "Request timed out";
    } catch (Exception) {
        return "Unknown error";
    }
}
?>
```

### 3. API Error Responses

```php
<?php
class APIController {
    public function handleRequest(): void {
        try {
            $this->process();
        } catch (NotAuthorizedException) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
        } catch (ForbiddenException) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
        } catch (NotFoundException) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
        } catch (ValidationException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (Exception) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }
    
    private function process() {
        // Implementation
    }
}
?>
```

### 4. Conditional Exception Handling

```php
<?php
class DataProcessor {
    public function processBatch(array $items): array {
        $results = [];
        $errors = [];
        
        foreach ($items as $item) {
            try {
                $results[] = $this->process($item);
            } catch (SkippableException) {
                // Skip this item
                continue;
            } catch (CriticalException $e) {
                // Stop processing
                throw $e;
            } catch (Exception) {
                // Log and continue
                $errors[] = $item;
                continue;
            }
        }
        
        return ['results' => $results, 'errors' => $errors];
    }
    
    private function process($item) {
        // Implementation
    }
}
?>
```

---

## Real-World Examples

### 1. Service Pattern

```php
<?php
class UserService {
    public function create(array $data): User {
        try {
            // Validate
            $this->validate($data);
            
            // Create
            return $this->repository->create($data);
        } catch (ValidationException $e) {
            // Log validation errors
            $this->logger->warning('Validation failed: ' . $e->getMessage());
            throw $e;
        } catch (DuplicateEntryException) {
            // Handle duplicate silently
            throw new InvalidArgumentException('User already exists');
        } catch (DatabaseException) {
            // Log database errors
            throw new RuntimeException('Failed to create user');
        } catch (Exception) {
            // Catch-all for unknown errors
            throw new RuntimeException('Unexpected error creating user');
        }
    }
}
?>
```

### 2. File Operations

```php
<?php
class FileProcessor {
    public function read(string $path): string {
        try {
            return file_get_contents($path);
        } catch (FileNotFoundException) {
            echo "File not found: $path\n";
            return "";
        } catch (PermissionDeniedException) {
            echo "Permission denied\n";
            return "";
        } catch (Exception) {
            echo "Failed to read file\n";
            return "";
        }
    }
}
?>
```

### 3. Cache Wrapper

```php
<?php
class CachedRepository {
    public function get(int $id): ?object {
        try {
            return $this->cache->get("item:$id");
        } catch (CacheException) {
            // Cache failed, continue without it
        }
        
        try {
            $item = $this->database->find($id);
            $this->cache->set("item:$id", $item);
            return $item;
        } catch (DatabaseException $e) {
            $this->logger->error('Database error: ' . $e->getMessage());
            throw new RuntimeException('Failed to retrieve item');
        } catch (Exception) {
            throw new RuntimeException('Unexpected error');
        }
    }
}
?>
```

### 4. Transaction Handling

```php
<?php
class OrderService {
    public function place(Order $order): string {
        $transaction = $this->db->beginTransaction();
        
        try {
            $orderId = $this->repository->save($order);
            $this->payment->charge($order->total);
            $transaction->commit();
            return $orderId;
        } catch (PaymentException $e) {
            $transaction->rollback();
            throw new OrderException('Payment failed: ' . $e->getMessage());
        } catch (ValidationException $e) {
            $transaction->rollback();
            throw new OrderException('Invalid order: ' . $e->getMessage());
        } catch (DatabaseException) {
            $transaction->rollback();
            throw new OrderException('Failed to create order');
        } catch (Exception) {
            $transaction->rollback();
            throw new OrderException('Unexpected error');
        }
    }
}
?>
```

---

## Best Practices

### 1. Use When Exception Details Not Needed

```php
<?php
// ✅ Good - we don't use the exception
try {
    $data = json_decode($json);
} catch (JsonException) {
    return null;
}

// ❌ Avoid - unnecessary variable
try {
    $data = json_decode($json);
} catch (JsonException $e) {  // Variable never used
    return null;
}
?>
```

### 2. Capture When You Need Details

```php
<?php
// ✅ Good - need error message
try {
    $user = $this->create($data);
} catch (ValidationException $e) {
    $this->logger->warning($e->getMessage());
}

// ❌ Avoid - losing useful information
try {
    $user = $this->create($data);
} catch (ValidationException) {
    $this->logger->warning('Validation failed');
}
?>
```

### 3. Order from Specific to General

```php
<?php
// ✅ Good - most specific first
try {
    operation();
} catch (NotFoundException $e) {
    // Specific handling with details
    echo "Not found\n";
} catch (DatabaseException) {
    // Specific but don't need details
    echo "DB error\n";
} catch (Exception) {
    // Generic fallback
    echo "Unknown error\n";
}

// ❌ Wrong - generic first (unreachable)
try {
    operation();
} catch (Exception) {
    // Catches everything
} catch (NotFoundException $e) {
    // Never reached
}
?>
```

---

## Common Mistakes

### 1. Attempting to Use Non-Captured Exception

```php
<?php
// ❌ Error - $e not defined
try {
    risky();
} catch (Exception) {
    echo $e->getMessage();  // Undefined variable
}

// ✅ Correct - capture if needed
try {
    risky();
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
```

### 2. Removing Variable When Needed

```php
<?php
// ❌ Wrong - loses information
try {
    process();
} catch (ApplicationException) {
    $this->logger->error('Error occurred'); // Lost details!
}

// ✅ Correct - capture for logging
try {
    process();
} catch (ApplicationException $e) {
    $this->logger->error('Error: ' . $e->getMessage());
}
?>
```

### 3. Over-Generic Catches

```php
<?php
// ❌ Too generic
try {
    $user = createUser($data);
    sendEmail($user->email);
} catch (Exception) {
    echo "Failed\n";  // Which operation failed?
}

// ✅ Better - split operations
try {
    $user = createUser($data);
} catch (ValidationException) {
    throw new InvalidArgumentException('Invalid user data');
} catch (Exception) {
    throw new RuntimeException('Failed to create user');
}

try {
    sendEmail($user->email);
} catch (Exception) {
    $this->logger->warning('Failed to send email');
}
?>
```

---

## Complete Example

```php
<?php
class OrderProcessor {
    public function process(Order $order): ProcessResult {
        try {
            // Validate order
            try {
                $this->validate($order);
            } catch (ValidationException $e) {
                return new ProcessResult(
                    success: false,
                    message: $e->getMessage()
                );
            }
            
            // Reserve inventory
            try {
                $this->inventory->reserve($order->items);
            } catch (InsufficientStockException) {
                return new ProcessResult(
                    success: false,
                    message: 'Insufficient stock'
                );
            }
            
            // Process payment
            try {
                $transaction = $this->payment->charge(
                    amount: $order->total,
                    token: $order->paymentToken
                );
            } catch (PaymentFailedException $e) {
                $this->inventory->release($order->items);
                return new ProcessResult(
                    success: false,
                    message: 'Payment failed: ' . $e->getMessage()
                );
            }
            
            // Create order record
            try {
                $orderId = $this->orders->create($order);
            } catch (DatabaseException) {
                $this->payment->refund($transaction);
                $this->inventory->release($order->items);
                return new ProcessResult(
                    success: false,
                    message: 'Failed to create order'
                );
            }
            
            // Send confirmation
            try {
                $this->mailer->sendConfirmation($order->email);
            } catch (MailException) {
                // Log but don't fail
                $this->logger->warning('Failed to send confirmation email');
            }
            
            return new ProcessResult(
                success: true,
                message: 'Order created',
                orderId: $orderId
            );
        } catch (Exception) {
            return new ProcessResult(
                success: false,
                message: 'Unexpected error'
            );
        }
    }
    
    private function validate(Order $order): void {
        // Validation logic
    }
}

class ProcessResult {
    public function __construct(
        public bool $success,
        public string $message,
        public ?int $orderId = null
    ) {}
}
?>
```

---

## See Also

- Documentation: [Exception Handling](../03-oop/38-exception.md)
- Related: [Try-Catch-Finally](../03-oop/38-exception.md), [Custom Exceptions](../03-oop/38-exception.md)
