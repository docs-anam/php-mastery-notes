# Exception Handling

## Overview

Exceptions are used to handle errors and exceptional situations in OOP code.

---

## Try-Catch

```php
<?php
try {
    if (!file_exists('file.txt')) {
        throw new Exception('File not found');
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

---

## Custom Exceptions

```php
<?php
class ValidationException extends Exception {}

class FileNotFoundException extends Exception {}

try {
    throw new FileNotFoundException('File not found');
} catch (FileNotFoundException $e) {
    echo "File error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Other error: " . $e->getMessage();
}
?>
```

---

## Finally Block

```php
<?php
$file = null;

try {
    $file = fopen('data.txt', 'r');
    if (!$file) {
        throw new Exception('Cannot open file');
    }
    $content = fread($file, 1000);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    if ($file) {
        fclose($file);
    }
}
?>
```

---

## Exception Methods

```php
<?php
try {
    throw new Exception('Something went wrong', 500);
} catch (Exception $e) {
    echo $e->getMessage();      // Exception message
    echo $e->getCode();         // Exception code
    echo $e->getFile();         // File where thrown
    echo $e->getLine();         // Line number
    echo $e->getTraceAsString(); // Stack trace
}
?>
```

---

## Next Steps

→ Learn [regular expressions](39-regular-expression.md)  
→ Study [reflection](40-reflection.md)
