<?php
/**
 * Detailed Summary: PHP OOP DateTime Class
 *
 * The DateTime class in PHP provides an object-oriented interface for date and time manipulation.
 * It supports creation, formatting, modification, comparison, and timezone management.
 *
 * Key Features:
 *
 * 1. Instantiation:
 *    - Create a DateTime object for the current moment or a specific date/time string.
 *      $dt = new DateTime(); // Current date and time
 *      $dt = new DateTime('2024-06-01 12:00:00'); // Specific date/time
 *      $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo')); // With timezone
 *
 * 2. Formatting:
 *    - Use format() to output the date/time in a custom format.
 *      echo $dt->format('Y-m-d H:i:s'); // 2024-06-01 12:00:00
 *      echo $dt->format(DateTime::ATOM); // ISO 8601 format
 *
 * 3. Modification:
 *    - Change the date/time using modify(), add(), or sub().
 *      $dt->modify('+1 day'); // Add 1 day
 *      $dt->add(new DateInterval('P1M')); // Add 1 month
 *      $dt->sub(new DateInterval('P10D')); // Subtract 10 days
 *
 * 4. Timezones:
 *    - Manage timezones with DateTimeZone.
 *      $tz = new DateTimeZone('Europe/London');
 *      $dt = new DateTime('now', $tz);
 *      $dt->setTimezone(new DateTimeZone('America/New_York'));
 *
 * 5. Comparison:
 *    - Compare DateTime objects using comparison operators or diff().
 *      $dt1 = new DateTime('2024-06-01');
 *      $dt2 = new DateTime('2024-07-01');
 *      $interval = $dt1->diff($dt2); // DateInterval object
 *      echo $interval->days; // Number of days difference
 *      if ($dt1 < $dt2) { echo 'dt1 is earlier'; }
 *
 * 6. Static Methods:
 *    - DateTime::createFromFormat() for custom parsing.
 *      $dt = DateTime::createFromFormat('d/m/Y', '01/06/2024');
 *      if ($dt === false) { echo 'Invalid format'; }
 *
 * 7. Immutability:
 *    - DateTimeImmutable provides the same API but returns new objects on modification.
 *      $dt = new DateTimeImmutable('2024-06-01');
 *      $dt2 = $dt->modify('+1 day'); // $dt unchanged, $dt2 is new object
 *
 * 8. Useful Methods:
 *    - getTimestamp(): Returns Unix timestamp.
 *    - setTimestamp(int $timestamp): Sets the date/time by timestamp.
 *    - getTimezone(): Gets the DateTimeZone object.
 *    - setDate($year, $month, $day): Sets the date.
 *    - setTime($hour, $minute, $second): Sets the time.
 *    - getOffset(): Gets the timezone offset in seconds.
 *    - format($format): Formats the date/time.
 *
 * Example (executable):
 */
echo "<pre>";

// 1. Instantiation
$dt = new DateTime('2024-06-01 10:00:00', new DateTimeZone('UTC'));
echo "Original: " . $dt->format('Y-m-d H:i:s T') . "\n";

// 2. Modification
$dt->add(new DateInterval('P2D')); // Add 2 days
echo "After add 2 days: " . $dt->format('Y-m-d H:i:s T') . "\n";

$dt->modify('+3 hours'); // Add 3 hours
echo "After add 3 hours: " . $dt->format('Y-m-d H:i:s T') . "\n";

// 3. Timezone
$dt->setTimezone(new DateTimeZone('Asia/Tokyo'));
echo "In Tokyo timezone: " . $dt->format('Y-m-d H:i:s T') . "\n";

// 4. Comparison
$dt1 = new DateTime('2024-06-01');
$dt2 = new DateTime('2024-07-01');
$interval = $dt1->diff($dt2);
echo "Difference between 2024-06-01 and 2024-07-01: " . $interval->days . " days\n";

// 5. Static method
$dt3 = DateTime::createFromFormat('d/m/Y', '15/06/2024');
if ($dt3 !== false) {
    echo "Parsed from custom format: " . $dt3->format('Y-m-d') . "\n";
}

// 6. Immutability
$dtImmutable = new DateTimeImmutable('2024-06-01');
$dtImmutable2 = $dtImmutable->modify('+1 day');
echo "Immutable original: " . $dtImmutable->format('Y-m-d') . "\n";
echo "Immutable after modify: " . $dtImmutable2->format('Y-m-d') . "\n";

// 7. Useful methods
echo "Timestamp: " . $dt->getTimestamp() . "\n";
echo "Timezone offset (seconds): " . $dt->getOffset() . "\n";

echo "</pre>";

// Documentation: https://www.php.net/manual/en/class.datetime.php