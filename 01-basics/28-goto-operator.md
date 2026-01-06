# Goto Operator

## Table of Contents
1. [Overview](#overview)
2. [Goto Syntax](#goto-syntax)
3. [Use Cases](#use-cases)
4. [Limitations](#limitations)
5. [Practical Examples](#practical-examples)
6. [Best Practices](#best-practices)
7. [Common Mistakes](#common-mistakes)

---

## Overview

The `goto` statement jumps execution to a labeled location in code.

**Syntax:**
```php
goto label;
// ... code ...
label:
// Execution continues here
```

⚠️ **Warning**: Goto is considered harmful in most cases. Use with extreme caution.

---

## Goto Syntax

### Basic Goto

```php
<?php
// Jump to label
goto end;

echo "This is skipped";

end:
echo "This executes";
// Output: This executes
?>
```

### Forward and Backward Jumps

```php
<?php
// Forward jump
$i = 0;
goto skip;

echo "Skipped\n";

skip:
echo "After skip\n";

// Backward jump (loop)
$count = 0;
loop:
echo $count . " ";
$count++;
if ($count < 3) {
    goto loop;
}
// Output: 0 1 2
?>
```

### Labels with Different Names

```php
<?php
$action = 'process';

if ($action === 'skip') {
    goto skip_processing;
} else {
    goto process_data;
}

skip_processing:
echo "Processing skipped\n";
goto end;

process_data:
echo "Processing data\n";

end:
echo "Done\n";
// Output:
// Processing data
// Done
?>
```

---

## Use Cases

### 1. Error Handling (Before Better Methods)

```php
<?php
// Old pattern (NOT RECOMMENDED anymore)
$file = fopen('file.txt', 'r');
if (!$file) {
    goto error;
}

$content = fread($file, 1000);
if (!$content) {
    goto error;
}

echo "File content: $content";
goto done;

error:
echo "Error reading file";

done:
fclose($file);
?>
```

### 2. State Machine

```php
<?php
// Simple state machine
$state = 'start';

start:
echo "Starting...\n";
$state = 'process';
goto $state;

process:
echo "Processing...\n";
$state = 'done';
goto $state;

done:
echo "Done!\n";
// Output:
// Starting...
// Processing...
// Done!
?>
```

### 3. Early Loop Exit with Cleanup

```php
<?php
// Jump out of nested loops with cleanup
for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
        if ($i === 1 && $j === 1) {
            goto cleanup;
        }
        echo "$i-$j ";
    }
}

cleanup:
echo "\nCleaning up...";
// Output:
// 0-0 0-1 0-2 1-0
// Cleaning up...
?>
```

---

## Limitations

### Cannot Jump Into/Out of Functions

```php
<?php
// ❌ INVALID: Cannot jump into function
goto inside_func;

function test() {
    inside_func:
    echo "Inside";
}
test();
// Fatal error!
?>
```

### Cannot Jump Out of Loop Context

```php
<?php
// ❌ INVALID: Cannot jump into loop from outside
goto loop_label;

for ($i = 0; $i < 3; $i++) {
    loop_label:
    echo $i;
}
// Error in some contexts
?>
```

### Label Rules

```php
<?php
// Labels must be unique in scope
label:
echo "First";

// ❌ INVALID: Duplicate label
label:
echo "Second";

// ✓ VALID: Different scope (different function)
function func1() {
    label:
    echo "In func1";
}

function func2() {
    label:
    echo "In func2";
}
?>
```

---

## Practical Examples

### File Processing with Cleanup

```php
<?php
$handle = null;
$data = null;

// Open file
$handle = fopen('data.txt', 'r');
if (!$handle) {
    goto handle_error;
}

// Read file
$data = fread($handle, 10000);
if ($data === false) {
    goto read_error;
}

// Process data
echo "Processing: " . strlen($data) . " bytes";
goto cleanup;

read_error:
echo "Failed to read file";

handle_error:
echo "Failed to open file";

cleanup:
if ($handle) {
    fclose($handle);
}
?>
```

### Configuration Switch

```php
<?php
function configureEnvironment($env) {
    $config = [];
    
    switch ($env) {
        case 'dev':
            $config['debug'] = true;
            $config['log_level'] = 'DEBUG';
            break;
        case 'prod':
            $config['debug'] = false;
            $config['log_level'] = 'ERROR';
            break;
        default:
            echo "Unknown environment";
            return null;
    }
    
    return $config;
}

$env = configureEnvironment('dev');
// Better than goto!
?>
```

---

## Best Practices

### 1. Prefer Modern Control Structures

```php
<?php
// ❌ Goto version
$success = true;
if ($success) {
    goto process;
} else {
    goto error;
}

process:
echo "Success";
goto end;

error:
echo "Error";

end:
// Done

// ✓ Better: Use if/else
if ($success) {
    echo "Success";
} else {
    echo "Error";
}
?>
```

### 2. Use Exceptions Instead

```php
<?php
// ❌ Goto for error handling
$file = @fopen('missing.txt', 'r');
if (!$file) {
    goto file_error;
}
// ...
file_error:
echo "File not found";

// ✓ Better: Use try/catch
try {
    if (!file_exists('missing.txt')) {
        throw new Exception('File not found');
    }
    $file = fopen('missing.txt', 'r');
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
```

### 3. Use Functions and Classes

```php
<?php
// ❌ Goto spaghetti code
$result = null;
goto validation;

validation:
if (empty($data)) {
    goto error;
}

goto processing;

processing:
$result = process($data);
goto done;

error:
$result = null;

done:
return $result;

// ✓ Better: Clear function structure
function processUserInput($data) {
    if (empty($data)) {
        return null;
    }
    return process($data);
}
?>
```

---

## Common Mistakes

### 1. Creating Unreadable Code

```php
<?php
// ❌ Unreadable: Jumps everywhere
$i = 0;
start:
if ($i > 5) goto end;
echo $i . " ";
$i++;
if ($i % 2 == 0) goto even;
echo "(odd) ";
goto increment;
even:
echo "(even) ";
increment:
goto start;

end:
echo "\nDone";

// ✓ Clear: Use loops
for ($i = 0; $i <= 5; $i++) {
    echo $i . " ";
    if ($i % 2 == 0) {
        echo "(even) ";
    } else {
        echo "(odd) ";
    }
}
echo "\nDone";
?>
```

### 2. Forward References

```php
<?php
// ❌ Confusing: Label used before definition
goto mystep;

mystep:
echo "This jumps to...";

// ✓ Better: Define label first
goto mystep;

mystep:
echo "Clear order";
?>
```

### 3. Goto in Nested Scopes

```php
<?php
// ❌ Problematic in loops
foreach ($items as $item) {
    if ($item === 'skip') {
        goto next_item;  // Confusing
    }
    echo $item . "\n";
    next_item:
}

// ✓ Better: Use continue
foreach ($items as $item) {
    if ($item === 'skip') {
        continue;  // Clear intent
    }
    echo $item . "\n";
}
?>
```

---

## Complete Example

```php
<?php
declare(strict_types=1);

class ConfigLoader {
    private $config = [];
    
    public function loadConfig(string $env): bool {
        // Validate environment
        if (!in_array($env, ['dev', 'staging', 'prod'])) {
            return false;
        }
        
        // Load configuration (better with switch, not goto)
        $config_file = $this->getConfigFile($env);
        
        if (!file_exists($config_file)) {
            return false;
        }
        
        $this->config = include $config_file;
        return true;
    }
    
    public function getConfig(string $key): mixed {
        return $this->config[$key] ?? null;
    }
    
    private function getConfigFile(string $env): string {
        $files = [
            'dev' => __DIR__ . '/config/dev.php',
            'staging' => __DIR__ . '/config/staging.php',
            'prod' => __DIR__ . '/config/prod.php',
        ];
        return $files[$env];
    }
}

// Usage - Note: Not using goto!
$loader = new ConfigLoader();
if ($loader->loadConfig('dev')) {
    echo "Config loaded: " . $loader->getConfig('debug');
} else {
    echo "Failed to load config";
}
?>
```

---

## Summary

| Aspect | Rating | Notes |
|--------|--------|-------|
| Readability | ❌ Poor | Code flow becomes hard to follow |
| Maintainability | ❌ Poor | Difficult to debug and modify |
| Performance | ✓ Good | Direct jump (rarely matters) |
| Safety | ❌ Poor | Easy to create bugs |

**Bottom Line**: Avoid goto. Modern PHP offers better alternatives:
- Use `if/else` for conditions
- Use loops (`for`, `foreach`, `while`) for iteration
- Use `try/catch` for error handling
- Use functions and classes for code organization

---

## Next Steps

✅ Understand goto (avoid using it!)  
→ Study [break and continue](25-break-and-continue.md)  
→ Learn [functions](28-functions.md)  
→ Explore [variable scope](31-variable-scope.md)
