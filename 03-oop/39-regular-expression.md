# Regular Expressions (Advanced OOP)

## Overview

Advanced regular expression techniques using PHP's regex functions in OOP context.

---

## preg_match in Classes

```php
<?php
class Validator {
    public function isEmail(string $email): bool {
        return preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email) === 1;
    }
    
    public function isPhone(string $phone): bool {
        return preg_match('/^[0-9\-\+\(\)\s]{10,}$/', $phone) === 1;
    }
}

$validator = new Validator();
var_dump($validator->isEmail('test@example.com'));
?>
```

---

## preg_replace in Classes

```php
<?php
class TextProcessor {
    public function sanitize(string $text): string {
        return preg_replace('/<[^>]+>/', '', $text);
    }
    
    public function highlight(string $text, string $search): string {
        return preg_replace("/$search/i", "<mark>$0</mark>", $text);
    }
}

$processor = new TextProcessor();
echo $processor->sanitize('<p>Hello</p>');
?>
```

---

## preg_split in Classes

```php
<?php
class Parser {
    public function parseCsv(string $csv): array {
        return preg_split('/,\s*/', $csv);
    }
    
    public function parseTags(string $tags): array {
        return preg_split('/[\s,;]+/', $tags, -1, PREG_SPLIT_NO_EMPTY);
    }
}

$parser = new Parser();
print_r($parser->parseCsv('apple, banana, cherry'));
?>
```

---

## Next Steps

→ Learn [reflection](40-reflection.md)
