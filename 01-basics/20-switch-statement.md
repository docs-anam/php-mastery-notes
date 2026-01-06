# Switch Statement

## Table of Contents
1. [Overview](#overview)
2. [Basic Syntax](#basic-syntax)
3. [Switch vs If/Else](#switch-vs-ifelse)
4. [Breaking Execution](#breaking-execution)
5. [Practical Examples](#practical-examples)
6. [Common Mistakes](#common-mistakes)

---

## Overview

The `switch` statement executes different code blocks based on different conditions.

```php
switch (expression) {
    case value1:
        // Code if expression == value1
        break;
    case value2:
        // Code if expression == value2
        break;
    default:
        // Code if no cases match
}
```

---

## Basic Syntax

### Simple Switch

```php
<?php
$day = 3;

switch ($day) {
    case 1:
        echo "Monday";
        break;
    case 2:
        echo "Tuesday";
        break;
    case 3:
        echo "Wednesday";  // Executes
        break;
    case 4:
        echo "Thursday";
        break;
    default:
        echo "Invalid day";
}
?>
```

### With Default

```php
<?php
$grade = 'F';

switch ($grade) {
    case 'A':
        echo "Excellent";
        break;
    case 'B':
        echo "Good";
        break;
    case 'C':
        echo "Average";
        break;
    default:
        echo "Below average";  // Executes
}
?>
```

### With Variable Expression

```php
<?php
$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'create':
        echo "Creating new item";
        break;
    case 'read':
        echo "Reading item";
        break;
    case 'update':
        echo "Updating item";
        break;
    case 'delete':
        echo "Deleting item";
        break;
    default:
        echo "Unknown action";
}
?>
```

---

## Switch vs If/Else

### Using If/Else

```php
<?php
$status = 'pending';

if ($status == 'pending') {
    echo "Processing...";
} elseif ($status == 'completed') {
    echo "Done!";
} elseif ($status == 'failed') {
    echo "Error occurred";
} else {
    echo "Unknown status";
}
?>
```

### Using Switch

```php
<?php
$status = 'pending';

switch ($status) {
    case 'pending':
        echo "Processing...";
        break;
    case 'completed':
        echo "Done!";
        break;
    case 'failed':
        echo "Error occurred";
        break;
    default:
        echo "Unknown status";
}
?>
```

### When to Use Each

**Use `if/else` for:**
- Range checks: `if ($age > 18 && $age < 65)`
- Complex conditions: `if ($is_admin || $is_moderator)`
- Boolean checks: `if (isset($var))`

**Use `switch` for:**
- Exact value matching
- Multiple values for same action
- Simpler code readability
- Menu/navigation options

---

## Breaking Execution

### The break Statement

```php
<?php
$level = 2;

switch ($level) {
    case 1:
        echo "Level 1 accessed ";
        break;  // Stop here
    case 2:
        echo "Level 2 accessed ";
        break;  // Stop here
    case 3:
        echo "Level 3 accessed ";
        break;
}
// Output: "Level 2 accessed "
?>
```

### Fall-Through (without break)

```php
<?php
$level = 1;

switch ($level) {
    case 1:
        echo "Level 1 ";
        // No break - fall through!
    case 2:
        echo "Level 2 ";
        // No break - fall through!
    case 3:
        echo "Level 3 ";
        break;  // Stop here
}
// Output: "Level 1 Level 2 Level 3"
?>
```

### Intentional Fall-Through

```php
<?php
$file_extension = 'jpg';

switch ($file_extension) {
    case 'jpg':
    case 'jpeg':
    case 'png':
    case 'gif':
        echo "Valid image format";
        break;
    case 'pdf':
    case 'doc':
    case 'docx':
        echo "Valid document format";
        break;
    default:
        echo "Unknown format";
}
// Output: "Valid image format"
?>
```

---

## Practical Examples

### User Role Authorization

```php
<?php
function getPermissions($role) {
    switch ($role) {
        case 'admin':
            return ['create', 'read', 'update', 'delete'];
        case 'editor':
            return ['create', 'read', 'update'];
        case 'viewer':
            return ['read'];
        case 'guest':
            return [];
        default:
            return null;
    }
}

$permissions = getPermissions('editor');
print_r($permissions);
// [0 => 'create', 1 => 'read', 2 => 'update']
?>
```

### HTTP Status Codes

```php
<?php
function getStatusMessage($code) {
    switch ($code) {
        case 200:
            return "OK";
        case 201:
            return "Created";
        case 204:
            return "No Content";
        case 301:
            return "Moved Permanently";
        case 400:
            return "Bad Request";
        case 401:
            return "Unauthorized";
        case 404:
            return "Not Found";
        case 500:
            return "Internal Server Error";
        case 503:
            return "Service Unavailable";
        default:
            return "Unknown Status";
    }
}

echo getStatusMessage(404);  // Not Found
?>
```

### Day of Week

```php
<?php
$day_number = date('N');  // 1 = Monday, 7 = Sunday

switch ($day_number) {
    case 1:
    case 2:
    case 3:
    case 4:
    case 5:
        echo "Weekday";
        break;
    case 6:
    case 7:
        echo "Weekend";
        break;
}
?>
```

### Form Handler

```php
<?php
$action = $_POST['action'] ?? null;

switch ($action) {
    case 'login':
        authenticate($_POST);
        break;
    case 'register':
        createAccount($_POST);
        break;
    case 'logout':
        destroySession();
        break;
    case 'reset_password':
        resetPassword($_POST['email']);
        break;
    default:
        echo "Invalid action";
}
?>
```

---

## Common Mistakes

### 1. Forgetting break

```php
<?php
// ❌ Bug: falls through to next case
$status = 'pending';

switch ($status) {
    case 'pending':
        $message = "Processing...";
        // Missing break!
    case 'completed':
        $message = "Done!";
        break;
}

echo $message;  // "Done!" - oops!

// ✅ Correct
$status = 'pending';

switch ($status) {
    case 'pending':
        $message = "Processing...";
        break;  // Stop here
    case 'completed':
        $message = "Done!";
        break;
}

echo $message;  // "Processing..."
?>
```

### 2. Using == Instead of ===

```php
<?php
// ❌ Type juggling issues
$user_id = "123";

switch ($user_id) {
    case 123:
        echo "User 123";  // Executes! "123" == 123
        break;
}

// ✅ PHP uses == in switch, but be aware
// Better: ensure types match or use strict comparison elsewhere

$user_id = 123;
switch ($user_id) {
    case 123:
        echo "User 123";
        break;
}
?>
```

### 3. Not Handling All Cases

```php
<?php
// ❌ Missing default
$payment_method = 'cryptocurrency';

switch ($payment_method) {
    case 'credit':
        processCredit();
        break;
    case 'debit':
        processDebit();
        break;
    // No default for unknown payment methods
}

// ✅ Always add default
switch ($payment_method) {
    case 'credit':
        processCredit();
        break;
    case 'debit':
        processDebit();
        break;
    default:
        echo "Unknown payment method";
}
?>
```

### 4. Complex Expressions in Case

```php
<?php
// ❌ This doesn't work as expected
$score = 85;

switch (true) {  // Note: switch on true
    case ($score >= 90):
        echo "A";
        break;
    case ($score >= 80):
        echo "B";  // Executes
        break;
    case ($score >= 70):
        echo "C";
        break;
}

// For complex logic, use if/else instead
if ($score >= 90) {
    echo "A";
} elseif ($score >= 80) {
    echo "B";
} elseif ($score >= 70) {
    echo "C";
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class PaymentProcessor {
    public function processPayment($method, $amount) {
        switch ($method) {
            case 'credit_card':
                return $this->processCard($amount);
            
            case 'paypal':
                return $this->processPayPal($amount);
            
            case 'bitcoin':
                return $this->processCrypto($amount);
            
            case 'wire':
                return $this->processWire($amount);
            
            default:
                return ['success' => false, 'error' => 'Unknown payment method'];
        }
    }
    
    private function processCard($amount): array {
        // Simulate card processing
        return ['success' => true, 'method' => 'credit_card', 'amount' => $amount];
    }
    
    private function processPayPal($amount): array {
        // Simulate PayPal processing
        return ['success' => true, 'method' => 'paypal', 'amount' => $amount];
    }
    
    private function processCrypto($amount): array {
        // Simulate crypto processing
        return ['success' => true, 'method' => 'bitcoin', 'amount' => $amount];
    }
    
    private function processWire($amount): array {
        // Simulate wire processing
        return ['success' => true, 'method' => 'wire', 'amount' => $amount];
    }
}

// Usage
$processor = new PaymentProcessor();

$result = $processor->processPayment('credit_card', 99.99);
print_r($result);
// Array ( [success] => 1 [method] => credit_card [amount] => 99.99 )

$result = $processor->processPayment('paypal', 49.99);
print_r($result);
// Array ( [success] => 1 [method] => paypal [amount] => 49.99 )

$result = $processor->processPayment('check', 25.00);
print_r($result);
// Array ( [success] => [error] => Unknown payment method )
?>
```

---

## Next Steps

✅ Understand switch statements  
→ Learn [if/else conditionals](18-if-statement.md)  
→ Study [ternary operator](20-ternary-operator.md)  
→ Master [logical operators](13-operators-logical.md)
