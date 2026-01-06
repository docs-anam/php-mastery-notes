# Object Cloning

## Overview

Create independent copies of objects using the `clone` keyword.

---

## Basic Cloning

```php
<?php
class User {
    public $name;
    public $email;
}

$user1 = new User();
$user1->name = 'John';
$user1->email = 'john@example.com';

$user2 = clone $user1;
$user2->name = 'Jane';

echo $user1->name;  // John
echo $user2->name;  // Jane
?>
```

---

## __clone Magic Method

```php
<?php
class Document {
    public $content;
    private $id;
    
    public function __clone() {
        $this->id = null;  // Reset ID for cloned copy
    }
}

$doc1 = new Document();
$doc1->content = 'Original';

$doc2 = clone $doc1;
$doc2->content = 'Cloned';
?>
```

---

## Deep Cloning

```php
<?php
class User {
    public $profile;
}

class Profile {
    public $name;
}

$user1 = new User();
$user1->profile = new Profile();
$user1->profile->name = 'John';

$user2 = clone $user1;
$user2->profile = clone $user1->profile;
$user2->profile->name = 'Jane';

echo $user1->profile->name;  // John
?>
```

---

## Next Steps

→ Learn [comparing objects](33-comparing-object.md)  
→ Study [magic methods](34-magic-function.md)
