# Switch Statement in PHP

## Overview

The switch statement provides a clean way to execute different code blocks based on different conditions. It's especially useful when you have many possible values for a single variable.

## Basic Switch Structure

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
        echo "Wednesday";  // This executes
        break;
    case 4:
        echo "Thursday";
        break;
    default:
        echo "Unknown day";
}
?>
```

### Switch with Default

```php
<?php
$color = "red";

switch ($color) {
    case "blue":
        echo "Sky color";
        break;
    case "green":
        echo "Grass color";
        break;
    default:
        echo "Unknown color: " . $color;
}
?>
```

### Break Statement

```php
<?php
$number = 2;

// WITHOUT break (fall-through)
switch ($number) {
    case 1:
        echo "One ";
    case 2:
        echo "Two ";     // Executes
    case 3:
        echo "Three ";   // Also executes!
    case 4:
        echo "Four ";    // Also executes!
        break;
    default:
        echo "Other";
}
// Output: One Two Three Four

// WITH break (correct)
switch ($number) {
    case 1:
        echo "One";
        break;
    case 2:
        echo "Two";      // Executes
        break;           // Stops here
    case 3:
        echo "Three";
        break;
    default:
        echo "Other";
}
// Output: Two
?>
```

## Multiple Cases

### Combining Cases

```php
<?php
$animal = "cat";

switch ($animal) {
    case "dog":
    case "cat":
    case "bird":
        echo "Pet";
        break;
    case "lion":
    case "tiger":
    case "bear":
        echo "Wild animal";
        break;
    default:
        echo "Unknown";
}
// Output: Pet
?>
```

### Multiple Cases with Logic

```php
<?php
$status = "pending";

switch ($status) {
    case "pending":
    case "processing":
        echo "In progress";
        echo " - please wait";
        break;
    case "completed":
        echo "Finished";
        break;
    case "error":
        echo "Something went wrong";
        break;
    default:
        echo "Unknown status";
}
?>
```

## Comparison in Switch

### Loose Type Comparison

```php
<?php
$value = "5";

switch ($value) {
    case 5:           // Matches! Loose comparison
        echo "Number five";
        break;
    case "5":
        echo "String five";
        break;
    default:
        echo "Other";
}
// Output: Number five (because "5" == 5 in loose comparison)
?>
```

### String Comparison

```php
<?php
$userRole = "editor";

switch ($userRole) {
    case "admin":
        echo "Full access";
        break;
    case "editor":
        echo "Can edit content";
        break;
    case "viewer":
        echo "Read-only access";
        break;
    default:
        echo "No access";
}
// Output: Can edit content
?>
```

## Nested Switch

```php
<?php
$country = "US";
$state = "CA";

switch ($country) {
    case "US":
        switch ($state) {
            case "CA":
                echo "California, USA";
                break;
            case "TX":
                echo "Texas, USA";
                break;
            default:
                echo "Unknown US state";
        }
        break;
    case "UK":
        switch ($state) {
            case "England":
                echo "England, UK";
                break;
            case "Scotland":
                echo "Scotland, UK";
                break;
        }
        break;
    default:
        echo "Unknown country";
}
?>
```

## Practical Examples

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
        case 400:
            return "Bad Request";
        case 401:
            return "Unauthorized";
        case 403:
            return "Forbidden";
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

echo getStatusMessage(404);  // Output: Not Found
?>
```

### User Role Authorization

```php
<?php
function checkPermission($role, $action) {
    switch ($role) {
        case "admin":
            return true;  // Admin can do everything
        
        case "moderator":
            return in_array($action, ["edit", "delete_spam", "ban_user"]);
        
        case "editor":
            return in_array($action, ["create", "edit", "publish"]);
        
        case "subscriber":
            return in_array($action, ["comment", "create_draft"]);
        
        case "guest":
            return in_array($action, ["read", "comment"]);
        
        default:
            return false;
    }
}

echo checkPermission("editor", "publish") ? "Allowed" : "Denied";
?>
```

### Payment Method Handler

```php
<?php
function processPayment($method, $amount) {
    switch ($method) {
        case "credit_card":
            echo "Processing credit card payment of $" . $amount;
            // Charge credit card
            break;
        
        case "paypal":
            echo "Redirecting to PayPal for $" . $amount;
            // Redirect to PayPal
            break;
        
        case "bank_transfer":
            echo "Bank transfer requested for $" . $amount;
            // Generate transfer details
            break;
        
        case "cryptocurrency":
            echo "Generating wallet address for $" . $amount;
            // Create crypto payment
            break;
        
        default:
            echo "Payment method not supported";
    }
}

processPayment("paypal", 99.99);
?>
```

### Language Selection

```php
<?php
function getWelcomeMessage($language) {
    switch ($language) {
        case "english":
        case "en":
            return "Welcome";
        
        case "spanish":
        case "es":
            return "Bienvenido";
        
        case "french":
        case "fr":
            return "Bienvenue";
        
        case "german":
        case "de":
            return "Willkommen";
        
        case "japanese":
        case "ja":
            return "ようこそ";
        
        default:
            return "Welcome";
    }
}

echo getWelcomeMessage("es");  // Output: Bienvenido
?>
```

### Email Template Selection

```php
<?php
function getEmailTemplate($type) {
    switch ($type) {
        case "welcome":
            return "emails/welcome.html";
        case "password_reset":
            return "emails/password-reset.html";
        case "order_confirmation":
            return "emails/order-confirmation.html";
        case "shipping_notification":
            return "emails/shipping.html";
        case "newsletter":
            return "emails/newsletter.html";
        default:
            return "emails/default.html";
    }
}

$template = getEmailTemplate("welcome");
// Load and send template
?>
```

## Fall-Through Pattern

```php
<?php
// Useful fall-through example
$day = 6;

switch ($day) {
    case 6:         // Saturday
    case 7:         // Sunday
        echo "Weekend";
        break;
    
    case 1:         // Monday
    case 2:         // Tuesday
    case 3:         // Wednesday
    case 4:         // Thursday
    case 5:         // Friday
        echo "Weekday";
        break;
    
    default:
        echo "Unknown day";
}
// Output: Weekend
?>
```

## Switch vs If/Elseif

### When to Use Switch

```php
<?php
// GOOD - Switch for multiple values of one variable
$status = "active";

switch ($status) {
    case "active":
        echo "Active";
        break;
    case "inactive":
        echo "Inactive";
        break;
    case "pending":
        echo "Pending";
        break;
    default:
        echo "Unknown";
}
?>
```

### When to Use If/Elseif

```php
<?php
// GOOD - If/Elseif for different conditions
$age = 25;
$income = 50000;

if ($age < 18) {
    echo "Too young";
} elseif ($income < 30000) {
    echo "Income too low";
} elseif ($income < 100000) {
    echo "Eligible for standard loan";
} else {
    echo "Eligible for premium loan";
}
?>
```

## Common Pitfalls

### Forgetting Break Statement

```php
<?php
$choice = 2;

// BUG: Missing break - falls through
switch ($choice) {
    case 1:
        echo "One ";
    case 2:
        echo "Two ";
    case 3:
        echo "Three ";
    default:
        echo "Default";
}
// Output: Two Three Default (WRONG!)

// FIXED: Add break
switch ($choice) {
    case 1:
        echo "One";
        break;
    case 2:
        echo "Two";
        break;
    case 3:
        echo "Three";
        break;
    default:
        echo "Default";
}
// Output: Two (CORRECT)
?>
```

### Type Confusion

```php
<?php
$value = "5";

// Confusing - loose comparison
switch ($value) {
    case 5:           // This matches! ("5" == 5)
        echo "Number";
        break;
    case "5":
        echo "String";
        break;
}
// Output: Number (because of loose comparison)

// To avoid this, be consistent with types
if ($value === 5) {
    echo "Exactly the number 5";
} elseif ($value === "5") {
    echo "Exactly the string '5'";
}
?>
```

### Missing Default

```php
<?php
$level = "gold";

// Missing handling for "gold"
switch ($level) {
    case "silver":
        echo "10% discount";
        break;
    case "bronze":
        echo "5% discount";
        break;
    default:
        echo "No discount";  // Good practice to have default
}
?>
```

## Best Practices

✓ **Always use break** unless intentional fall-through
✓ **Use default case** for unexpected values
✓ **Keep cases simple** - extract complex logic to functions
✓ **Use switch for single variable** with multiple values
✓ **Use if/elseif for multiple conditions** on different variables
✓ **Group related cases** when they share logic
✓ **Consistent indentation** for readability

## Key Takeaways

✓ **switch** compares value against multiple cases
✓ **case** defines condition to match
✓ **break** stops execution and exits the switch
✓ **default** executes if no cases match
✓ **Fall-through** happens without break (usually unintended)
✓ **Multiple cases** can share same block of code
✓ **Loose comparison** (==) is used unless using strict mode
✓ **Nested switch** possible but should be avoided
✓ **Switch is cleaner** than many elseif statements
✓ **Type coercion** can cause unexpected matches
