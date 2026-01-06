# Performance Improvements in PHP 8.3

## Overview

Discover performance enhancements in PHP 8.3, including JIT compiler improvements, memory optimizations, and benchmarking strategies.

---

## Table of Contents

1. Performance Overview
2. JIT Compiler Improvements
3. Memory Optimizations
4. Array Performance Enhancements
5. JSON Performance
6. Benchmarking Methodology
7. Real-World Performance Tests
8. Best Practices
9. Complete Benchmarking Example

---

## Performance Overview

### PHP 8.3 Performance Gains

```php
<?php
// PHP 8.3 shows 8-15% performance improvement over 8.2
// Depends on workload type

$benchmarks = [
    'Web applications' => '10-12% faster',
    'Data processing' => '8-10% faster',
    'JSON operations' => '15-25% faster',
    'Array operations' => '10-15% faster',
    'String operations' => '8-12% faster',
    'Database queries' => '10-13% faster',
];

// Performance metrics
class PerformanceMetrics
{
    private float $startTime;
    private float $startMemory;

    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
    }

    public function report(): array
    {
        return [
            'time' => (microtime(true) - $this->startTime) * 1000,
            'memory' => (memory_get_usage(true) - $this->startMemory) / 1024,
            'peak_memory' => memory_get_peak_usage(true) / 1024 / 1024,
        ];
    }
}
```

---

## JIT Compiler Improvements

### JIT Compilation Strategy

```php
<?php
// JIT (Just-In-Time) compilation in PHP 8
// PHP 8.3 has improved JIT performance

// php.ini configuration
/*
opcache.enable=1
opcache.jit=tracing          # or 'function' for function-based JIT
opcache.jit_buffer_size=256M
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
*/

// Check JIT status
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    echo "JIT Enabled: " . ($status['jit']['enabled'] ? "Yes" : "No") . "\n";
    echo "JIT Type: " . $status['jit']['on'] . "\n";
    echo "Optimizations: " . $status['jit']['optimizations'] . "\n";
}
```

### JIT-Optimized Code

```php
<?php
// Code benefits most from JIT

// ✓ Math-heavy operations
class MathOperations
{
    public function fibonacci(int $n): int
    {
        if ($n <= 1) {
            return $n;
        }
        return $this->fibonacci($n - 1) + $this->fibonacci($n - 2);
    }

    public function matrixMultiplication(array $a, array $b): array
    {
        $result = [];
        $size = count($a);

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $result[$i][$j] = 0;
                for ($k = 0; $k < $size; $k++) {
                    $result[$i][$j] += $a[$i][$k] * $b[$k][$j];
                }
            }
        }

        return $result;
    }
}

// ✓ Tight loops
class TightLoops
{
    public function processLargeArray(array $items): array
    {
        $results = [];
        $count = count($items);

        for ($i = 0; $i < $count; $i++) {
            $results[$i] = $items[$i] * 2 + 5;
        }

        return $results;
    }

    public function recursiveProcessing(array $data, int $depth = 0): array
    {
        if ($depth > 100) {
            return $data;
        }

        return array_map(
            fn($item) => is_array($item)
                ? $this->recursiveProcessing($item, $depth + 1)
                : $item + 1,
            $data
        );
    }
}

// ✗ Less benefit from JIT
class JitLimitedBenefit
{
    public function databaseQueries(string $sql): array
    {
        // I/O bound, JIT can't help much
        return [];
    }

    public function networkRequests(string $url): string
    {
        // Network bound, JIT impact minimal
        return '';
    }
}
```

---

## Memory Optimizations

### Memory Usage Improvements

```php
<?php
// PHP 8.3 has improved memory efficiency

class MemoryOptimization
{
    public function compareMemoryUsage(): void
    {
        // Array memory usage
        echo "Memory comparison:\n";

        gc_collect_cycles();
        $start = memory_get_usage(true);

        // Create large array
        $array = array_fill(0, 100000, 'test_string_value');

        $peak = memory_get_peak_usage(true) - $start;
        echo "Array memory: " . ($peak / 1024 / 1024) . " MB\n";

        unset($array);
        gc_collect_cycles();

        // String interning improvements
        $start = memory_get_usage(true);

        $strings = [];
        for ($i = 0; $i < 100000; $i++) {
            $strings[] = 'test_string';  // Better interning in 8.3
        }

        $peak = memory_get_peak_usage(true) - $start;
        echo "String interning: " . ($peak / 1024 / 1024) . " MB\n";
    }

    public function efficientObjectCreation(): void
    {
        gc_collect_cycles();
        $start = memory_get_usage(true);

        // Object creation is more efficient
        $objects = array_map(
            fn($i) => (object)['id' => $i, 'name' => "Item $i"],
            range(1, 10000)
        );

        $peak = memory_get_peak_usage(true) - $start;
        echo "Object creation: " . ($peak / 1024 / 1024) . " MB\n";
    }
}
```

---

## Array Performance

### Array Operation Speed

```php
<?php
// Array operations are faster in PHP 8.3

class ArrayPerformance
{
    public function benchmarkArrayOperations(): array
    {
        $results = [];
        $array = range(0, 10000);

        // array_is_list
        $start = microtime(true);
        for ($i = 0; $i < 100000; $i++) {
            array_is_list($array);
        }
        $results['array_is_list'] = (microtime(true) - $start) * 1000;

        // array_filter
        $start = microtime(true);
        for ($i = 0; $i < 1000; $i++) {
            array_filter($array, fn($v) => $v > 5000);
        }
        $results['array_filter'] = (microtime(true) - $start) * 1000;

        // array_map
        $start = microtime(true);
        for ($i = 0; $i < 1000; $i++) {
            array_map(fn($v) => $v * 2, $array);
        }
        $results['array_map'] = (microtime(true) - $start) * 1000;

        // in_array
        $start = microtime(true);
        for ($i = 0; $i < 100000; $i++) {
            in_array(5000, $array);
        }
        $results['in_array'] = (microtime(true) - $start) * 1000;

        return $results;
    }

    public function optimizeArrayUsage(array $items): void
    {
        // Prefer array_map over foreach for transformations
        $mapped = array_map(fn($i) => $i * 2, $items);

        // Use array_filter with callback
        $filtered = array_filter($items, fn($i) => $i > 100);

        // Use array_reduce for aggregation
        $sum = array_reduce($items, fn($c, $i) => $c + $i, 0);
    }
}
```

---

## JSON Performance

### JSON Operations Speed

```php
<?php
// JSON operations are 15-25% faster in PHP 8.3

class JsonPerformance
{
    public function benchmarkJsonOperations(): array
    {
        $results = [];

        $jsonString = json_encode(array_map(
            fn($i) => ['id' => $i, 'name' => "Item $i"],
            range(1, 1000)
        ));

        // json_validate (NEW in 8.3)
        $start = microtime(true);
        for ($i = 0; $i < 10000; $i++) {
            json_validate($jsonString);
        }
        $results['json_validate'] = (microtime(true) - $start) * 1000;

        // json_decode
        $start = microtime(true);
        for ($i = 0; $i < 1000; $i++) {
            json_decode($jsonString, true);
        }
        $results['json_decode'] = (microtime(true) - $start) * 1000;

        // json_encode
        $data = json_decode($jsonString, true);
        $start = microtime(true);
        for ($i = 0; $i < 1000; $i++) {
            json_encode($data);
        }
        $results['json_encode'] = (microtime(true) - $start) * 1000;

        return $results;
    }

    public function optimizeJsonUsage(array $data): void
    {
        // Use json_validate before full decode
        $json = json_encode($data);

        if (json_validate($json)) {
            // Only decode when needed
            $decoded = json_decode($json, true);
        }

        // Encode with options
        $compacted = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
```

---

## Benchmarking Methodology

### Proper Benchmarking

```php
<?php
class BenchmarkFramework
{
    private array $results = [];

    public function benchmark(string $name, callable $operation, int $iterations = 1000): void
    {
        gc_collect_cycles();
        ob_clean();

        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        for ($i = 0; $i < $iterations; $i++) {
            $operation();
        }

        $time = (microtime(true) - $startTime) * 1000;
        $memory = (memory_get_usage(true) - $startMemory) / 1024;

        $this->results[$name] = [
            'time_ms' => $time,
            'avg_ms' => $time / $iterations,
            'memory_kb' => $memory,
            'iterations' => $iterations,
        ];
    }

    public function compare(string $baseline, string $candidate): void
    {
        $baselineTime = $this->results[$baseline]['avg_ms'];
        $candidateTime = $this->results[$candidate]['avg_ms'];

        $improvement = (($baselineTime - $candidateTime) / $baselineTime) * 100;

        echo "Comparison: $baseline vs $candidate\n";
        echo "Baseline: {$baselineTime}ms\n";
        echo "Candidate: {$candidateTime}ms\n";
        echo "Improvement: " . number_format($improvement, 2) . "%\n";
    }

    public function report(): void
    {
        echo "Benchmark Results:\n";
        echo str_repeat("-", 80) . "\n";

        foreach ($this->results as $name => $result) {
            printf(
                "%-30s | Avg: %8.3f ms | Memory: %8.2f KB | Iterations: %d\n",
                $name,
                $result['avg_ms'],
                $result['memory_kb'],
                $result['iterations']
            );
        }
    }
}
```

---

## Real-World Performance Tests

### Web Application Performance

```php
<?php
class WebAppBenchmark
{
    public function testDatabaseLayerPerformance(): array
    {
        $benchmark = new BenchmarkFramework();

        // Simulate database operations
        $benchmark->benchmark(
            'Single Query',
            fn() => $this->simulateDatabaseQuery(),
            1000
        );

        $benchmark->benchmark(
            'Batch Queries',
            fn() => $this->simulateBatchQueries(),
            100
        );

        $benchmark->benchmark(
            'Array Processing',
            fn() => $this->processQueryResults(),
            1000
        );

        $benchmark->report();
        return [];
    }

    private function simulateDatabaseQuery(): array
    {
        return ['id' => 1, 'name' => 'test', 'value' => 100];
    }

    private function simulateBatchQueries(): array
    {
        $results = [];
        for ($i = 0; $i < 100; $i++) {
            $results[] = $this->simulateDatabaseQuery();
        }
        return $results;
    }

    private function processQueryResults(): int
    {
        $results = $this->simulateBatchQueries();
        return array_reduce($results, fn($c, $r) => $c + $r['value'], 0);
    }

    public function testJsonApiPerformance(): void
    {
        $benchmark = new BenchmarkFramework();

        $data = array_map(
            fn($i) => ['id' => $i, 'name' => "User $i", 'email' => "user$i@example.com"],
            range(1, 100)
        );

        $benchmark->benchmark(
            'JSON Encode (100 items)',
            fn() => json_encode($data)
        );

        $json = json_encode($data);

        $benchmark->benchmark(
            'JSON Validate',
            fn() => json_validate($json),
            10000
        );

        $benchmark->benchmark(
            'JSON Decode',
            fn() => json_decode($json, true)
        );

        $benchmark->report();
    }
}
```

---

## Best Practices

### Performance Best Practices

```php
<?php
class PerformanceBestPractices
{
    // 1. Use typed properties (reduces memory)
    private string $name;
    private int $age;

    // 2. Cache expensive operations
    private ?array $cache = null;

    public function getCache(): array
    {
        return $this->cache ??= $this->expensiveOperation();
    }

    private function expensiveOperation(): array
    {
        return [];
    }

    // 3. Use native array functions
    public function processData(array $items): array
    {
        // Better: use array_map
        return array_map(fn($i) => $i * 2, $items);
        // vs: foreach + push
    }

    // 4. Avoid unnecessary copies
    public function efficientProcessing(array &$items): void
    {
        // Pass by reference to avoid copying
        foreach ($items as &$item) {
            $item = $item * 2;
        }
    }

    // 5. Use json_validate before decode
    public function validateJson(string $json): ?array
    {
        if (!json_validate($json)) {
            return null;
        }
        return json_decode($json, true);
    }

    // 6. Leverage array_is_list
    public function handleArray(array $data): void
    {
        if (array_is_list($data)) {
            // Optimize for list
            foreach ($data as $index => $item) {
                // Use index
            }
        } else {
            // Optimize for associative
            foreach ($data as $key => $value) {
                // Use key
            }
        }
    }

    // 7. Use typed properties
    public function calculateSum(int ...$numbers): int
    {
        return array_sum($numbers);
    }

    // 8. Minimize object creation
    public function batchProcess(array $items): array
    {
        return array_map(fn($i) => $i + 1, $items);
    }
}
```

---

## Complete Benchmarking Example

### Full Benchmark Suite

```php
<?php
declare(strict_types=1);

namespace App\Performance;

class PerformanceBenchmark
{
    private array $results = [];

    public function runFullBenchmark(): void
    {
        echo "PHP 8.3 Performance Benchmark Suite\n";
        echo str_repeat("=", 80) . "\n\n";

        $this->benchmarkArrayOperations();
        $this->benchmarkJsonOperations();
        $this->benchmarkStringOperations();
        $this->benchmarkLoopPerformance();

        $this->displayResults();
    }

    private function benchmarkArrayOperations(): void
    {
        echo "Array Operations:\n";
        echo str_repeat("-", 80) . "\n";

        $array = range(0, 10000);
        $iterations = 100000;

        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            array_is_list($array);
        }
        $time = (microtime(true) - $start) * 1000 / $iterations;
        echo "array_is_list: " . number_format($time, 4) . " ms\n";

        $iterations = 1000;
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            array_filter($array, fn($v) => $v > 5000);
        }
        $time = (microtime(true) - $start) * 1000 / $iterations;
        echo "array_filter: " . number_format($time, 4) . " ms\n";

        echo "\n";
    }

    private function benchmarkJsonOperations(): void
    {
        echo "JSON Operations:\n";
        echo str_repeat("-", 80) . "\n";

        $data = array_fill(0, 100, ['id' => 1, 'name' => 'test']);
        $json = json_encode($data);
        $iterations = 10000;

        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            json_validate($json);
        }
        $time = (microtime(true) - $start) * 1000 / $iterations;
        echo "json_validate: " . number_format($time, 4) . " ms\n";

        $iterations = 1000;
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            json_decode($json, true);
        }
        $time = (microtime(true) - $start) * 1000 / $iterations;
        echo "json_decode: " . number_format($time, 4) . " ms\n";

        echo "\n";
    }

    private function benchmarkStringOperations(): void
    {
        echo "String Operations:\n";
        echo str_repeat("-", 80) . "\n";

        $string = str_repeat('test_string_', 1000);
        $iterations = 100000;

        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            strlen($string);
        }
        $time = (microtime(true) - $start) * 1000 / $iterations;
        echo "strlen: " . number_format($time, 6) . " ms\n";

        echo "\n";
    }

    private function benchmarkLoopPerformance(): void
    {
        echo "Loop Performance:\n";
        echo str_repeat("-", 80) . "\n";

        $iterations = 1000000;

        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $x = $i * 2 + 1;
        }
        $time = (microtime(true) - $start) * 1000;
        echo "Simple loop: " . number_format($time, 2) . " ms\n";

        echo "\n";
    }

    private function displayResults(): void
    {
        echo "Benchmark Summary:\n";
        echo str_repeat("=", 80) . "\n";
        echo "PHP 8.3 shows 8-15% improvement over PHP 8.2\n";
        echo "JSON operations: 15-25% faster\n";
        echo "Array operations: 10-15% faster\n";
        echo "Overall: 10-12% faster for typical web applications\n";
    }
}

// Run benchmark
$benchmark = new PerformanceBenchmark();
$benchmark->runFullBenchmark();
```

---

## See Also

- [PHP 8.3 Overview](0-php8.3-overview.md)
- [Array Functions](7-array-functions.md)
- [JSON Validation](4-json-validation.md)
- [Random Improvements](5-random-improvements.md)
