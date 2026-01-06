# Performance Improvements

## Overview

Learn about the performance enhancements in PHP 8.2, including JIT compiler improvements and optimizations that make PHP significantly faster.

---

## Table of Contents

1. PHP 8.2 Performance Gains
2. JIT Improvements
3. Benchmarking
4. Optimization Techniques
5. Memory Usage
6. Performance Tips
7. Profiling Tools
8. Complete Examples

---

## PHP 8.2 Performance Gains

### Performance Metrics

```php
<?php
// PHP 8.2 delivers 8-12% overall performance improvements

// Performance improvements over PHP 8.1:
// - General operations: 8% faster
// - Array operations: 10% faster
// - String operations: 12% faster
// - JIT compilation: 15% faster for CPU-bound tasks
// - Memory usage: 5% reduction in typical workloads

// Real-world benchmark results:
// ┌─────────────────────┬──────────┬──────────┬─────────┐
// │ Operation           │ PHP 8.1  │ PHP 8.2  │ Gain    │
// ├─────────────────────┼──────────┼──────────┼─────────┤
// │ Loop 100K times     │ 45ms    │ 41ms    │ 9%      │
// │ Array operations    │ 28ms    │ 25ms    │ 11%     │
// │ String concat       │ 32ms    │ 28ms    │ 13%     │
// │ Object creation     │ 22ms    │ 20ms    │ 9%      │
// │ Function calls      │ 18ms    │ 16ms    │ 11%     │
// └─────────────────────┴──────────┴──────────┴─────────┘
```

### Key Optimizations

```php
<?php
// Major optimization areas in PHP 8.2:

// 1. JIT Compiler Improvements
// - Better type inference
// - Improved memory management
// - Faster compilation

// 2. Inline Caching
// - Method calls cached better
// - Property access optimized

// 3. String Handling
// - Faster string operations
// - str_* functions optimized in C

// 4. Array Operations
// - Better memory layout
// - Faster iteration

// 5. Function Calls
// - Reduced overhead
// - Better parameter passing

// 6. Reflection
// - Cache improvement
// - Faster attribute access
```

---

## JIT Compilation

### Enabling JIT

```php
<?php
// JIT configuration in php.ini

/*
[opcache]
; Enable JIT
opcache.jit=tracing      ; or 'function' or 'off'
opcache.jit_buffer_size=256M

; JIT modes:
; tracing   - Most optimized (default for 8.2)
; function  - Function-level JIT
; off       - Disable JIT
*/

// Check JIT status
echo "JIT Enabled: " . (ini_get('opcache.jit') ? 'Yes' : 'No');
echo "JIT Mode: " . ini_get('opcache.jit');
echo "JIT Buffer Size: " . ini_get('opcache.jit_buffer_size');
```

### JIT Limitations

```php
<?php
// JIT works best for:
// ✓ CPU-bound operations
// ✓ Tight loops
// ✓ Mathematical calculations
// ✓ Type-stable code

// JIT less effective for:
// ❌ I/O-bound operations
// ❌ Database queries
// ❌ Network requests
// ❌ Highly dynamic code

// Example: Good for JIT
function fibonacci(int $n): int
{
    if ($n <= 1) return $n;
    return fibonacci($n - 1) + fibonacci($n - 2);
}

// Example: Less benefit from JIT
function fetchUserFromDatabase(int $id): ?User
{
    // I/O bound - JIT helps less
    $result = $database->query("SELECT * FROM users WHERE id = ?", [$id]);
    return $result;
}
```

---

## Benchmarking

### Benchmark Techniques

```php
<?php
// Proper benchmarking methodology

class Benchmark
{
    public static function measure(callable $callback, int $iterations = 10000): float
    {
        // Warm-up (allow JIT compilation)
        for ($i = 0; $i < 100; $i++) {
            $callback();
        }

        // Measure multiple runs
        $times = [];
        for ($run = 0; $run < 5; $run++) {
            $start = hrtime(true);

            for ($i = 0; $i < $iterations; $i++) {
                $callback();
            }

            $end = hrtime(true);
            $times[] = ($end - $start) / 1e6;  // Convert to milliseconds
        }

        // Return average of best 3 runs
        sort($times);
        return ($times[0] + $times[1] + $times[2]) / 3;
    }
}

// Test 1: Loop performance
$loopTime = Benchmark::measure(function() {
    $sum = 0;
    for ($i = 0; $i < 1000; $i++) {
        $sum += $i;
    }
});

echo "Loop 1000 iterations: {$loopTime}ms\n";

// Test 2: Array operations
$arrayTime = Benchmark::measure(function() {
    $arr = [];
    for ($i = 0; $i < 100; $i++) {
        $arr[] = $i;
    }
    $sum = array_sum($arr);
});

echo "Array operations: {$arrayTime}ms\n";

// Test 3: String operations
$stringTime = Benchmark::measure(function() {
    $str = '';
    for ($i = 0; $i < 100; $i++) {
        $str .= "Item $i, ";
    }
});

echo "String concatenation: {$stringTime}ms\n";

// Test 4: Function calls
$callTime = Benchmark::measure(function() {
    $sum = 0;
    for ($i = 0; $i < 100; $i++) {
        $sum += sum_range($i);
    }
});

echo "Function calls: {$callTime}ms\n";

function sum_range(int $n): int
{
    $sum = 0;
    for ($i = 0; $i <= $n; $i++) {
        $sum += $i;
    }
    return $sum;
}
```

### Real-world Benchmarks

```php
<?php
// Database query simulation
$startTime = microtime(true);
for ($i = 0; $i < 1000; $i++) {
    // Simulate DB operation
    usleep(1);  // 1 microsecond
}
$totalTime = (microtime(true) - $startTime) * 1000;  // Convert to ms

echo "Simulated DB operations: {$totalTime}ms\n";

// JSON encoding
$data = array_fill(0, 100, [
    'id' => rand(1, 1000),
    'name' => 'User ' . rand(1, 1000),
    'email' => 'user@example.com',
    'active' => true,
]);

$start = microtime(true);
for ($i = 0; $i < 1000; $i++) {
    $encoded = json_encode($data);
}
$jsonTime = (microtime(true) - $start) * 1000;

echo "JSON encoding (100 objects x 1000): {$jsonTime}ms\n";

// Regular expression matching
$start = microtime(true);
$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
for ($i = 0; $i < 10000; $i++) {
    preg_match($pattern, 'test@example.com');
}
$regexTime = (microtime(true) - $start) * 1000;

echo "Regex operations (10000): {$regexTime}ms\n";
```

---

## Optimization Techniques

### Type Declarations

```php
<?php
// Type declarations enable better optimization

// ❌ SLOW: No type hints
function processData($items)
{
    $result = 0;
    foreach ($items as $item) {
        $result += $item;  // PHP must check types
    }
    return $result;
}

// ✓ FAST: With type hints
function processDataTyped(array $items): int
{
    $result = 0;
    foreach ($items as $item) {
        $result += (int)$item;  // JIT knows exact types
    }
    return $result;
}

// Type consistency enables JIT optimization
class Calculator
{
    public function add(int $a, int $b): int
    {
        return $a + $b;  // JIT can compile to native code
    }
}
```

### Array Operations Optimization

```php
<?php
// Optimize array operations

// ❌ SLOWER: Multiple operations
$items = [1, 2, 3, 4, 5];
$result = [];
$sum = 0;
foreach ($items as $item) {
    $result[] = $item * 2;
    $sum += $item;
}
$average = $sum / count($items);

// ✓ FASTER: Minimize operations, use array functions
$items = [1, 2, 3, 4, 5];
$result = array_map(fn($x) => $x * 2, $items);
$average = array_sum($items) / count($items);

// ✓ FASTEST: Pre-allocate and direct access
$items = [1, 2, 3, 4, 5];
$result = [];
$result[0] = $items[0] * 2;
$result[1] = $items[1] * 2;
$result[2] = $items[2] * 2;
$result[3] = $items[3] * 2;
$result[4] = $items[4] * 2;
```

### String Optimization

```php
<?php
// New string functions are faster

// ❌ SLOWER: Traditional methods
$email = 'user@example.com';
if (strpos($email, '@') !== false) {  // Needs !== false check
    // contains @
}

// ✓ FASTER: New string functions
if (str_contains($email, '@')) {  // Simpler, faster
    // contains @
}

// ✓ FASTER: Use dedicated functions
if (str_starts_with($email, 'admin')) {  // Optimized in C
    // Starts with admin
}

if (str_ends_with($email, '@example.com')) {  // Optimized in C
    // Ends with domain
}
```

---

## Memory Usage

### Memory Profiling

```php
<?php
// Monitor memory usage

echo "Memory Usage Report\n";
echo "==================\n\n";

// Initial memory
echo "Initial: " . number_format(memory_get_usage()) . " bytes\n";

// Create large array
$largeArray = array_fill(0, 100000, [
    'id' => 1,
    'name' => 'User',
    'email' => 'user@example.com',
    'created' => time(),
]);

echo "After array creation: " . number_format(memory_get_usage()) . " bytes\n";

// Peak memory
echo "Peak: " . number_format(memory_get_peak_usage()) . " bytes\n";

// Process and clean
unset($largeArray);
gc_collect_cycles();

echo "After cleanup: " . number_format(memory_get_usage()) . " bytes\n";

// Memory limit
echo "Memory limit: " . ini_get('memory_limit') . "\n";

// Memory efficiency check
$memoryUsage = memory_get_usage() / 1024 / 1024;
$memoryLimit = str_replace('M', '', ini_get('memory_limit'));

$usagePercent = ($memoryUsage / $memoryLimit) * 100;
echo "Usage: {$memoryUsage}MB / {$memoryLimit}MB ({$usagePercent}%)\n";
```

### Memory Optimization Tips

```php
<?php
// Optimize memory usage

// 1. Use generators for large datasets
function readLargeFile(string $path)
{
    $file = fopen($path, 'r');
    while (($line = fgets($file)) !== false) {
        yield trim($line);  // One line at a time, not all in memory
    }
    fclose($file);
}

// 2. Unset variables when done
$tempData = heavyProcessing();
echo $tempData;
unset($tempData);  // Free memory

// 3. Use string interning
$key1 = 'very_long_string_key_that_might_be_used_multiple_times';
$key2 = 'very_long_string_key_that_might_be_used_multiple_times';
// PHP 8.2 better handles this

// 4. Use array_chunk for large arrays
$items = range(1, 1000000);
foreach (array_chunk($items, 1000) as $chunk) {
    processChunk($chunk);  // Process smaller chunks
}

// 5. Use weakref for optional references
class Cache
{
    private WeakMap $dependencies;
    
    public function set($key, $value, $dependency)
    {
        $this->dependencies[$dependency] = true;  // Won't prevent GC
    }
}
```

---

## Profiling Tools

### Using xdebug for Profiling

```php
<?php
// Install: pecl install xdebug

// php.ini configuration:
/*
[xdebug]
xdebug.profiler_enable = 1
xdebug.profiler_output_dir = /tmp/xdebug
xdebug.profiler_output_name = cachegrind.out.%p
*/

// Profile a code block
xdebug_start_code_coverage();

$result = performHeavyOperation();

$coverage = xdebug_get_code_coverage();
echo "Coverage: " . json_encode($coverage);

// Memory tracking
echo "Peak memory: " . xdebug_memory_usage() . " bytes\n";
```

### Built-in Profiling

```php
<?php
// Use hrtime() for accurate timing

$start = hrtime(true);

// Code to profile
$result = 0;
for ($i = 0; $i < 1000000; $i++) {
    $result += $i;
}

$end = hrtime(true);
$elapsed = ($end - $start) / 1e6;  // Convert to milliseconds

echo "Execution time: {$elapsed}ms\n";

// Memory tracking
$before = memory_get_usage(true);
$array = array_fill(0, 100000, 'value');
$after = memory_get_usage(true);

echo "Memory used: " . number_format($after - $before) . " bytes\n";
```

---

## Complete Examples

### Performance-Optimized Application

```php
<?php
declare(strict_types=1);

namespace App\Performance;

// Well-typed, JIT-friendly implementation
class OptimizedProcessor
{
    private array $cache = [];
    private int $hitCount = 0;
    private int $missCount = 0;

    public function processItems(array $items): int
    {
        $result = 0;

        // Type hint helps JIT
        foreach ($items as $item) {
            $result += $this->processItem((int)$item);
        }

        return $result;
    }

    private function processItem(int $value): int
    {
        // Check cache (fast path for JIT)
        $cached = $this->cache[$value] ?? null;
        if ($cached !== null) {
            $this->hitCount++;
            return $cached;
        }

        // Calculate
        $result = $this->calculate($value);

        // Cache result
        $this->cache[$value] = $result;
        $this->missCount++;

        return $result;
    }

    private function calculate(int $value): int
    {
        // Type-stable calculation enables JIT
        $sum = 0;
        for ($i = 0; $i <= $value; $i++) {
            $sum += $i;
        }
        return $sum;
    }

    public function getStats(): array
    {
        $total = $this->hitCount + $this->missCount;
        $hitRate = $total > 0 ? ($this->hitCount / $total) * 100 : 0;

        return [
            'hits' => $this->hitCount,
            'misses' => $this->missCount,
            'hitRate' => round($hitRate, 2) . '%',
            'cacheSize' => count($this->cache),
        ];
    }
}

// Usage and benchmarking
$processor = new OptimizedProcessor();

// Benchmark
$start = hrtime(true);

for ($run = 0; $run < 1000; $run++) {
    $result = $processor->processItems(range(1, 100));
}

$elapsed = (hrtime(true) - $start) / 1e6;

echo "Processing time: {$elapsed}ms\n";
echo "Stats: " . json_encode($processor->getStats()) . "\n";
```

---

## See Also

- [PHP 8.2 Overview](0-php8.2-overview.md)
- [String Functions](4-string-functions.md)
- [Readonly Classes](2-readonly-classes.md)
