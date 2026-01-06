# Understanding Log Levels

## Overview

Deep dive into log levels, when to use each level, and strategies for choosing appropriate levels in your application.

---

## Table of Contents

1. Log Level Definitions
2. When to Use Each Level
3. Choosing Levels Strategically
4. Level Configuration
5. Common Mistakes
6. Best Practices
7. Complete Examples

---

## Log Level Definitions

### Emergency (600)

```
Definition: System is unusable
Action Required: Immediate
Recovery: System may need restart

Examples:
- Disk full (cannot write)
- Database completely down
- Critical config missing
- Unrecoverable system failure
```

```php
<?php
// Emergency example

$logger->emergency('Disk full - cannot continue', [
    'free_space' => 0,
    'required' => '50MB',
    'impact' => 'Application stopped',
]);

$logger->emergency('Master database offline', [
    'impact' => 'All operations blocked',
    'recovery' => 'Manual intervention required',
]);
```

### Alert (550)

```
Definition: Action must be taken immediately
Action Required: Within minutes
Recovery: Possible but critical

Examples:
- Security breach
- Resource exhaustion
- Service degradation
- Potential data loss
```

```php
<?php
// Alert example

$logger->alert('Potential security breach detected', [
    'attack_type' => 'SQL injection',
    'source_ip' => '192.168.1.100',
    'url' => '/users?id=1 OR 1=1',
    'action' => 'IP blocked',
]);

$logger->alert('Memory usage critical', [
    'usage' => '95%',
    'threshold' => '90%',
    'action' => 'cache cleared',
]);
```

### Critical (500)

```
Definition: Critical conditions
Action Required: Within hours
Recovery: Expected, but serious

Examples:
- Service failure
- Data corruption detected
- API unavailable
- Important function failed
```

```php
<?php
// Critical example

$logger->critical('Payment gateway unreachable', [
    'gateway' => 'stripe',
    'status' => 'offline',
    'impact' => 'Users cannot checkout',
    'retry_interval' => '5 minutes',
]);

$logger->critical('Cache data corruption detected', [
    'cache_key' => 'user:profile:*',
    'action' => 'cache invalidated',
]);
```

### Error (400)

```
Definition: Error conditions
Action Required: Within day
Recovery: Standard troubleshooting

Examples:
- Operation failed
- Service degraded
- API error
- Database error
```

```php
<?php
// Error example

$logger->error('Database query failed', [
    'query' => 'INSERT INTO users...',
    'error' => 'Unique constraint violation',
    'user_email' => 'existing@example.com',
]);

$logger->error('Failed to send email', [
    'recipient' => 'user@example.com',
    'service' => 'AWS SES',
    'error' => 'Invalid recipient',
]);
```

### Warning (300)

```
Definition: Warning conditions
Action Required: No immediate action needed
Recovery: May resolve automatically

Examples:
- Deprecated usage
- Resource approaching limit
- Unusual but not critical
- Suboptimal performance
```

```php
<?php
// Warning example

$logger->warning('High memory usage', [
    'usage_percent' => 75,
    'threshold' => 80,
    'action' => 'monitoring enabled',
]);

$logger->warning('Deprecated API endpoint used', [
    'endpoint' => '/api/v1/users',
    'replacement' => '/api/v2/users',
    'sunset_date' => '2025-12-31',
]);
```

### Notice (250)

```
Definition: Normal but significant
Action Required: Informational only
Recovery: N/A

Examples:
- Configuration change
- Important milestone
- System behavior change
- Scheduled maintenance
```

```php
<?php
// Notice example

$logger->notice('Application configuration changed', [
    'setting' => 'DEBUG_MODE',
    'old_value' => 'false',
    'new_value' => 'true',
    'changed_by' => 'admin@example.com',
]);

$logger->notice('Database maintenance window started', [
    'duration' => '30 minutes',
    'maintenance_type' => 'index rebuild',
]);
```

### Info (200)

```
Definition: General informational messages
Action Required: No
Recovery: N/A

Examples:
- User actions (login, logout)
- Application events
- Process started/completed
- Configuration loaded
```

```php
<?php
// Info example

$logger->info('User logged in', [
    'user_id' => 123,
    'email' => 'user@example.com',
    'ip' => '192.168.1.1',
]);

$logger->info('Report generated', [
    'report_id' => 456,
    'type' => 'sales',
    'period' => '2025-Q1',
]);
```

### Debug (100)

```
Definition: Detailed debugging information
Action Required: No
Recovery: N/A

Examples:
- Variable values
- Loop iterations
- Function entry/exit
- Detailed state information
```

```php
<?php
// Debug example

$logger->debug('Processing item', [
    'item_id' => 789,
    'status' => 'active',
    'value' => $item->value,
]);

$logger->debug('Query executed', [
    'sql' => 'SELECT * FROM users WHERE active = 1',
    'rows' => 1542,
    'time_ms' => 45.2,
]);
```

---

## When to Use Each Level

### Decision Tree

```
Does it require immediate action?
  ├─ YES, system unusable?
  │  └─ EMERGENCY
  ├─ YES, within minutes?
  │  └─ ALERT
  ├─ YES, within hours?
  │  └─ CRITICAL
  └─ NO, is it an error?
     ├─ YES, operation failed?
     │  └─ ERROR
     ├─ YES, potential problem?
     │  └─ WARNING
     └─ NO, is it significant?
        ├─ YES, unusual event?
        │  └─ NOTICE
        └─ YES, normal event?
           ├─ INFO
           └─ DEBUG (low-level details)
```

### Real-World Scenarios

```php
<?php
// Scenario 1: Login attempt

$logger->info('Login attempt', [
    'email' => 'user@example.com',
    'ip' => '192.168.1.1',
]);

// Wrong level:
// $logger->debug() - Too low, loses visibility
// $logger->warning() - Too high, not a problem
// $logger->error() - Too high, not failed yet
```

```php
<?php
// Scenario 2: Login failed

$logger->warning('Login failed', [
    'email' => 'user@example.com',
    'reason' => 'Invalid password',
    'attempts' => 3,
]);

// Wrong levels:
// $logger->debug() - Too low, loses visibility
// $logger->info() - Missing context of failure
// $logger->error() - Too high, not a system error
```

```php
<?php
// Scenario 3: Payment failed

$logger->error('Payment processing failed', [
    'order_id' => 123,
    'gateway' => 'stripe',
    'error' => 'Insufficient funds',
]);

// Wrong levels:
// $logger->warning() - Too low, business impact
// $logger->critical() - Too high, not system failure
```

---

## Choosing Levels Strategically

### Impact vs. Urgency

```
        High Urgency   Low Urgency
High Impact  CRITICAL    ERROR
Low Impact   WARNING     INFO
```

### By Operation Type

```
CRUD Operations:
- Create: INFO ("User created")
- Read:  DEBUG ("Finding user")
- Update: INFO ("User updated") or WARNING (important change)
- Delete: WARNING ("User deleted")

API Operations:
- Success: INFO ("API call succeeded")
- Client Error (4xx): WARNING ("Invalid request")
- Server Error (5xx): ERROR ("API failed")

User Actions:
- Login: INFO
- Logout: INFO
- Failed Auth: WARNING
- Permission Denied: WARNING
- Sensitive Action: WARNING or NOTICE
```

---

## Level Configuration

### Per-Handler Configuration

```php
<?php
// Different handlers for different levels

$logger = new Logger('app');

// DEBUG + INFO to file
$fileHandler = new StreamHandler('logs/all.log');
$fileHandler->setLevel(Logger::DEBUG);
$logger->pushHandler($fileHandler);

// ERROR and above to Slack
$slackHandler = new SlackHandler($webhookUrl);
$slackHandler->setLevel(Logger::ERROR);
$logger->pushHandler($slackHandler);

// CRITICAL to email
$emailHandler = new NativeMailerHandler('ops@example.com');
$emailHandler->setLevel(Logger::CRITICAL);
$logger->pushHandler($emailHandler);
```

### Environment-Based Levels

```php
<?php
// Different levels for different environments

class LoggerFactory {
    public static function create($env = 'production') {
        $logger = new Logger('app');
        
        switch ($env) {
            case 'development':
                // All levels in development
                $handler = new StreamHandler('php://stdout');
                $handler->setLevel(Logger::DEBUG);
                break;
            
            case 'staging':
                // INFO and above
                $handler = new StreamHandler('logs/app.log');
                $handler->setLevel(Logger::INFO);
                break;
            
            case 'production':
                // WARNING and above for critical systems
                $handler = new StreamHandler('logs/app.log');
                $handler->setLevel(Logger::WARNING);
                break;
        }
        
        $logger->pushHandler($handler);
        return $logger;
    }
}
```

---

## Common Mistakes

### Mistake 1: Wrong Level for Impact

```php
<?php
// WRONG: Payment failure as debug
$logger->debug('Payment failed');

// CORRECT: Payment failure as error
$logger->error('Payment processing failed', [
    'order_id' => 123,
    'reason' => 'Declined',
]);
```

### Mistake 2: Too Many Critical Logs

```php
<?php
// WRONG: Everything is critical
$logger->critical('User logged in');
$logger->critical('Email sent');
$logger->critical('Cache hit');

// CORRECT: Appropriate levels
$logger->info('User logged in');
$logger->info('Email sent');
$logger->debug('Cache hit');
```

### Mistake 3: Not Enough Context

```php
<?php
// WRONG: Missing context
$logger->error('Error occurred');

// CORRECT: Full context
$logger->error('Database query failed', [
    'query' => 'SELECT * FROM users',
    'error' => 'Connection timeout',
    'connection' => 'primary',
]);
```

---

## Best Practices

### Consistent Leveling

```php
<?php
// Be consistent across codebase

// All user logins at INFO
$logger->info('User login', ['user_id' => 123]);

// All payment errors at ERROR
$logger->error('Payment failed', ['order_id' => 456]);

// All deprecations at WARNING
$logger->warning('Deprecated method used', ['method' => 'old_api']);
```

### Clear Thresholds

```php
// Document your level strategy:

/*
 * Logging Levels:
 * DEBUG   - Detailed flow and variable values
 * INFO    - User actions and process milestones
 * NOTICE  - Important system events
 * WARNING - Unexpected but recoverable issues
 * ERROR   - Failures that impact operations
 * CRITICAL - System reliability threatened
 * ALERT   - Immediate attention required
 * EMERGENCY - System unusable
 */
```

---

## Complete Examples

### Example 1: E-Commerce Application

```php
<?php
// Logging levels for e-commerce

class ProductService {
    public function __construct(
        private LoggerInterface $logger
    ) {}
    
    public function getProduct($id) {
        $this->logger->debug('Fetching product', ['id' => $id]);
        
        $product = $this->repository->find($id);
        
        if (!$product) {
            $this->logger->notice('Product not found', ['id' => $id]);
            return null;
        }
        
        return $product;
    }
    
    public function createProduct($data) {
        $this->logger->info('Creating product', [
            'name' => $data['name'],
            'sku' => $data['sku'],
        ]);
        
        try {
            $product = $this->repository->create($data);
            
            $this->logger->info('Product created', [
                'id' => $product->id,
                'name' => $product->name,
            ]);
            
            return $product;
        } catch (ValidationException $e) {
            $this->logger->warning('Product validation failed', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (Exception $e) {
            $this->logger->error('Failed to create product', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
    
    public function deleteProduct($id) {
        $this->logger->warning('Deleting product', ['id' => $id]);
        
        try {
            $this->repository->delete($id);
            
            $this->logger->info('Product deleted', ['id' => $id]);
        } catch (Exception $e) {
            $this->logger->error('Failed to delete product', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

class PaymentService {
    public function process($order) {
        $this->logger->info('Processing payment', [
            'order_id' => $order->id,
        ]);
        
        try {
            $result = $this->gateway->charge($order);
            
            if ($result->success) {
                $this->logger->info('Payment successful', [
                    'order_id' => $order->id,
                    'transaction_id' => $result->id,
                ]);
                return true;
            } else {
                $this->logger->warning('Payment declined', [
                    'order_id' => $order->id,
                    'reason' => $result->error,
                ]);
                return false;
            }
        } catch (GatewayException $e) {
            $this->logger->error('Payment gateway error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (Exception $e) {
            $this->logger->critical('Unexpected payment error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
```

---

## Key Takeaways

**Log Level Checklist:**

1. ✅ Use consistent levels
2. ✅ Match level to impact
3. ✅ Document your strategy
4. ✅ Don't over-log critical
5. ✅ Use debug for details
6. ✅ Use info for actions
7. ✅ Use warning for problems
8. ✅ Use error for failures

---

## See Also

- [Logging Basics](0-logging-basics.md)
- [Basic Logging](6-logging.md)
- [Handlers](5-handler.md)
