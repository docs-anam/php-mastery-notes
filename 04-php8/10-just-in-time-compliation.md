# Just-In-Time Compilation

## Overview

JIT (Just-In-Time) compilation compiles PHP code to machine code at runtime for better performance, significantly speeding up computationally intensive operations while maintaining backward compatibility.

---

## Understanding JIT

```php
<?php
// Without JIT: Code is interpreted
for ($i = 0; $i < 1000000; $i++) {
    $result = $i * 2;
}

// With JIT: Loop is compiled to machine code
// Result: 3-4x faster execution
?>
```

---

## JIT Configuration in php.ini

```ini
[opcache]
opcache.enable=1
opcache.jit=on
opcache.jit_buffer_size=100M
```

Or via command line:

```bash
php -d opcache.jit=on -r 'echo "JIT enabled";'
```

---

## Checking JIT Status

```php
<?php
// Check if JIT is enabled
if (extension_loaded('Zend OPcache')) {
    $config = opcache_get_configuration();
    
    if ($config['directives']['opcache.jit'] !== 'off') {
        echo "JIT is enabled\n";
        echo "JIT buffer size: " . $config['directives']['opcache.jit_buffer_size'] . "\n";
    }
}

// Get JIT statistics
$stats = opcache_get_status();
if (isset($stats['jit'])) {
    print_r($stats['jit']);
}
?>
```

---

## Performance Impact Examples

### 1. Mathematical Operations

```php
<?php
function fibonacci(int $n): int {
    if ($n <= 1) {
        return $n;
    }
    return fibonacci($n - 1) + fibonacci($n - 2);
}

// Without JIT: ~1.5 seconds
// With JIT: ~0.4 seconds
$start = microtime(true);
$result = fibonacci(35);
$time = microtime(true) - $start;

echo "Result: $result\n";
echo "Time: " . number_format($time, 4) . "s\n";
?>
```

### 2. Loop Operations

```php
<?php
function processArray(array $data): array {
    $result = [];
    
    foreach ($data as $item) {
        $result[] = $item * 2 + 1;
    }
    
    return $result;
}

$data = range(1, 100000);

$start = microtime(true);
$result = processArray($data);
$time = microtime(true) - $start;

echo "Processed " . count($result) . " items\n";
echo "Time: " . number_format($time, 4) . "s\n";
?>
```

### 3. String Operations

```php
<?php
function processStrings(array $strings): array {
    $result = [];
    
    foreach ($strings as $str) {
        $result[] = strtoupper($str) . "_processed";
    }
    
    return $result;
}

$strings = array_fill(0, 50000, "hello");

$start = microtime(true);
$result = processStrings($strings);
$time = microtime(true) - $start;

echo "Time: " . number_format($time, 4) . "s\n";
?>
```

---

## JIT Optimization Levels

```ini
[opcache]
; Level 0: JIT disabled
opcache.jit=0

; Level 1: Trace JIT (best for applications)
opcache.jit=on

; Level 2: Function JIT
opcache.jit=2

; Level 3: Both function and trace (uses more memory)
opcache.jit=3

; Level 4: Everything
opcache.jit=4
```

---

## Real-World Performance Scenarios

### 1. Data Processing Framework

```php
<?php
class DataProcessor {
    public function processLargeDataset(array $data, callable $transformer): array {
        $results = [];
        
        // JIT optimizes this loop when running repeatedly
        foreach ($data as $item) {
            $results[] = $transformer($item);
        }
        
        return $results;
    }
}

$processor = new DataProcessor();
$data = range(1, 100000);

$start = microtime(true);
$results = $processor->processLargeDataset($data, function($x) {
    return $x * $x + $x - 1;
});
$time = microtime(true) - $start;

echo "Processed " . count($results) . " items in " . number_format($time, 4) . "s\n";
?>
```

### 2. Scientific Calculations

```php
<?php
class MathOperations {
    public function computeMatrix(int $size): array {
        $matrix = [];
        
        // JIT accelerates nested loops
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $matrix[$i][$j] = ($i * $j) + ($i + $j);
            }
        }
        
        return $matrix;
    }
}

$math = new MathOperations();
$start = microtime(true);
$matrix = $math->computeMatrix(500);
$time = microtime(true) - $start;

echo "Matrix computation: " . number_format($time, 4) . "s\n";
?>
```

### 3. Recursive Algorithms

```php
<?php
class Algorithm {
    public function quickSort(array &$arr, int $low, int $high): void {
        if ($low < $high) {
            $pi = $this->partition($arr, $low, $high);
            $this->quickSort($arr, $low, $pi - 1);
            $this->quickSort($arr, $pi + 1, $high);
        }
    }
    
    private function partition(array &$arr, int $low, int $high): int {
        $pivot = $arr[$high];
        $i = $low - 1;
        
        for ($j = $low; $j < $high; $j++) {
            if ($arr[$j] < $pivot) {
                $i++;
                [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
            }
        }
        
        [$arr[$i + 1], $arr[$high]] = [$arr[$high], $arr[$i + 1]];
        return $i + 1;
    }
}

$algo = new Algorithm();
$data = array_fill(0, 10000, mt_rand(1, 1000));

$start = microtime(true);
$algo->quickSort($data, 0, count($data) - 1);
$time = microtime(true) - $start;

echo "Sort time: " . number_format($time, 4) . "s\n";
?>
```

---

## JIT-Friendly Code Patterns

### 1. Type-Safe Code

```php
<?php
// JIT optimizes better with clear types
class Calculator {
    public function add(int $a, int $b): int {
        return $a + $b; // JIT can optimize this
    }
    
    public function multiply(float $a, float $b): float {
        return $a * $b; // JIT specializes this
    }
}
?>
```

### 2. Loop Optimization

```php
<?php
// JIT optimizes tight loops
function sum(array $numbers): int {
    $total = 0;
    
    foreach ($numbers as $num) {
        $total += $num; // JIT can compile this
    }
    
    return $total;
}
?>
```

### 3. Recursive Functions

```php
<?php
// JIT optimizes recursive calls
class TreeNode {
    public function __construct(
        public int $value,
        public ?TreeNode $left = null,
        public ?TreeNode $right = null
    ) {}
}

function traverseTree(TreeNode $node, int &$sum): void {
    $sum += $node->value;
    
    if ($node->left) {
        traverseTree($node->left, $sum);
    }
    
    if ($node->right) {
        traverseTree($node->right, $sum);
    }
}
?>
```

---

## Benchmarking JIT Performance

```php
<?php
function benchmark(callable $fn, string $name, int $iterations = 1000): void {
    $start = microtime(true);
    
    for ($i = 0; $i < $iterations; $i++) {
        $fn();
    }
    
    $elapsed = microtime(true) - $start;
    $avgMs = ($elapsed / $iterations) * 1000;
    
    echo "$name: " . number_format($elapsed, 4) . "s (" . 
         number_format($avgMs, 4) . "ms per iteration)\n";
}

// Benchmark example
benchmark(function() {
    $sum = 0;
    for ($i = 0; $i < 1000; $i++) {
        $sum += $i;
    }
    return $sum;
}, "Loop sum", 100);

benchmark(function() {
    return array_sum(range(0, 999));
}, "array_sum", 100);
?>
```

---

## Best Practices

### 1. Ensure Stable Configuration

```ini
[opcache]
opcache.enable=1
opcache.jit=on
opcache.jit_buffer_size=100M
opcache.memory_consumption=256
opcache.validate_timestamps=0
opcache.revalidate_freq=0
```

### 2. Monitor JIT Stats

```php
<?php
function reportJITStats(): void {
    $stats = opcache_get_status();
    
    if (!isset($stats['jit'])) {
        echo "JIT not available\n";
        return;
    }
    
    echo "JIT Enabled: " . ($stats['jit']['on'] ? "Yes" : "No") . "\n";
    echo "Compiled Functions: " . $stats['jit']['functions_compiled'] . "\n";
    echo "Compiled Traces: " . $stats['jit']['traces_compiled'] . "\n";
    echo "Root Traces: " . $stats['jit']['root_traces'] . "\n";
}

reportJITStats();
?>
```

### 3. Profile Before and After

```php
<?php
function profileCode(callable $fn, int $iterations = 1000) {
    $memStart = memory_get_usage(true);
    $start = microtime(true);
    
    for ($i = 0; $i < $iterations; $i++) {
        $fn();
    }
    
    $memEnd = memory_get_usage(true);
    $elapsed = microtime(true) - $start;
    
    return [
        'time' => $elapsed,
        'memory' => ($memEnd - $memStart) / 1024 / 1024,
        'perSecond' => $iterations / $elapsed
    ];
}
?>
```

---

## Limitations and Considerations

```php
<?php
// JIT works best with:
// - Tight loops
// - Recursive functions
// - Type-safe code
// - Computational heavy operations

// JIT has less impact on:
// - I/O operations (file, database)
// - Network operations
// - String manipulation (uses C functions)
// - Dynamic code patterns
?>
```

---

## Complete Example

```php
<?php
class JITBenchmark {
    public function runBenchmarks(): void {
        $benchmarks = [
            'fibonacci' => fn() => $this->fibonacci(25),
            'array_sum' => fn() => array_sum(range(0, 10000)),
            'string_ops' => fn() => $this->stringOps(),
            'math_ops' => fn() => $this->mathOps(),
        ];
        
        echo "JIT Performance Benchmark\n";
        echo str_repeat("=", 50) . "\n";
        
        foreach ($benchmarks as $name => $fn) {
            $start = microtime(true);
            for ($i = 0; $i < 100; $i++) {
                $fn();
            }
            $time = microtime(true) - $start;
            
            printf("%-20s: %8.4f ms\n", $name, $time * 10);
        }
    }
    
    private function fibonacci(int $n): int {
        if ($n <= 1) return $n;
        return $this->fibonacci($n - 1) + $this->fibonacci($n - 2);
    }
    
    private function stringOps(): void {
        $str = "Hello";
        for ($i = 0; $i < 1000; $i++) {
            $str = strtoupper($str) . "X";
        }
    }
    
    private function mathOps(): void {
        $result = 0;
        for ($i = 0; $i < 1000; $i++) {
            $result += sqrt($i) * sin($i);
        }
    }
}

$bench = new JITBenchmark();
$bench->runBenchmarks();
?>
```

---

## See Also

- Documentation: [JIT](https://www.php.net/manual/en/opcache.configuration.php#ini.opcache.jit)
- Related: [Performance Optimization](../04-performance/2-optimization.md)
