# DateTime Class

## Overview

The DateTime class handles date and time operations in PHP.

---

## Creating DateTime Objects

```php
<?php
$now = new DateTime();
echo $now->format('Y-m-d H:i:s');

$specific = new DateTime('2024-01-15 10:30:00');
echo $specific->format('Y-m-d');

$from_timestamp = new DateTime('@' . time());
?>
```

---

## Formatting Dates

```php
<?php
$date = new DateTime('2024-01-15');

echo $date->format('Y-m-d');        // 2024-01-15
echo $date->format('M d, Y');       // Jan 15, 2024
echo $date->format('l, F j');       // Monday, January 15

// Time formatting
echo $date->format('H:i:s');        // 00:00:00
echo $date->format('h:i A');        // 12:00 AM
?>
```

---

## Date Arithmetic

```php
<?php
$date = new DateTime('2024-01-15');

$date->add(new DateInterval('P1D'));     // Add 1 day
echo $date->format('Y-m-d');             // 2024-01-16

$date->sub(new DateInterval('P1W'));     // Subtract 1 week
echo $date->format('Y-m-d');             // 2024-01-09

$date->modify('+3 months');
?>
```

---

## DateInterval

```php
<?php
$start = new DateTime('2024-01-01');
$end = new DateTime('2024-12-31');

$interval = $start->diff($end);
echo $interval->format('%a days');  // Total days in year

echo $interval->y . ' years, ';
echo $interval->m . ' months, ';
echo $interval->d . ' days';
?>
```

---

## Timezones

```php
<?php
$utc = new DateTime('2024-01-15', new DateTimeZone('UTC'));
$est = new DateTime('2024-01-15', new DateTimeZone('America/New_York'));

echo $utc->format('Y-m-d H:i:s');
echo $est->format('Y-m-d H:i:s');
?>
```

---

## Next Steps

→ Learn [exceptions](38-exception.md)  
→ Study [regular expressions](39-regular-expression.md)
