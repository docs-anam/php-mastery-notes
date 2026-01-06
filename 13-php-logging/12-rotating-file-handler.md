# Rotating File Handler

## Overview

Learn about rotating file handlers that automatically manage log file sizes and rotation strategies to prevent disk space issues.

---

## Table of Contents

1. What is Rotating File Handler
2. Rotation Strategies
3. Configuration Options
4. Monolog RotatingFileHandler
5. Custom Rotation Logic
6. Best Practices
7. Complete Examples

---

## What is Rotating File Handler

### Purpose

```
Problem: Log files grow indefinitely

log.txt
├─ 1MB on day 1
├─ 10MB after 1 week
├─ 100MB after 1 month
├─ 1GB after 1 year
└─ Server runs out of space

Solution: Rotate logs automatically
```

### Benefits

```
✓ Prevents disk space issues
✓ Keeps recent logs easily accessible
✓ Archives old logs
✓ Compresses old logs
✓ Automatic cleanup
✓ Better organization
✓ Manageable file sizes
```

### How It Works

```
Write to: app.log (current)
Size reaches 10MB
↓
Rotate: app.log → app-2025-01-06.log
Create: new app.log
↓
Keep 30 days of logs
↓
Delete: app-2024-12-07.log (older than 30 days)
```

---

## Rotation Strategies

### By Size

```php
<?php
// Rotate when file reaches certain size

// File grows to 10MB, then rotates
app.log (10MB) → app-1.log
new app.log starts

// Repeat for each 10MB
app-1.log (10MB)
app-2.log (10MB)
app-3.log (10MB)
...
app-30.log (10MB)  // Keep 30 rotated files
```

### By Date

```php
<?php
// Rotate at specific interval

// Daily rotation
app.log → app-2025-01-06.log
new app.log starts tomorrow

// Weekly rotation
app.log → app-2025-01-06.log
keeps same app.log for 7 days

// Monthly rotation
app.log → app-2025-01-06.log
keeps same app.log for 30 days
```

### By Size and Date

```php
<?php
// Combined strategy

// Daily rotation
app.log created Jan 6
↓
Grows to 50MB, rotate
app-2025-01-06-1.log (50MB)
new app.log continues Jan 6
↓
Reaches 50MB again
app-2025-01-06-2.log (50MB)
new app.log continues Jan 6
↓
Next day
app.log → app-2025-01-07.log (current day format)
```

---

## Configuration Options

### File Naming

```php
<?php
// Different naming conventions

// Sequential: app-1.log, app-2.log, app-3.log
// Pros: Simple, predictable
// Cons: No date info

// Date-based: app-2025-01-06.log, app-2025-01-07.log
// Pros: Clear timeline
// Cons: May have multiple per day

// DateTime: app-2025-01-06-143045.log
// Pros: Precise timestamp
// Cons: Hard to read

// Compressed: app-2025-01-06.log.gz
// Pros: Saves space
// Cons: Need extraction to read
```

### Retention Policy

```php
<?php
// How long to keep logs

// Keep by count: Keep last 30 files
// - Predictable disk usage
// - May delete too early if write heavy
// - May keep too long if write light

// Keep by days: Keep last 30 days
// - Consistent time-based retention
// - Variable disk usage
// - Better for compliance

// Keep by size: Keep last 1GB
// - Bounded disk usage
// - Variable retention
// - Needs monitoring
```

---

## Monolog RotatingFileHandler

### Basic Usage

```php
<?php
use Monolog\Logger;
use Monolog\Handlers\RotatingFileHandler;

// Create handler
$handler = new RotatingFileHandler(
    'logs/app.log',  // Base path
    30              // Max files (30 days of daily rotation)
);

$logger = new Logger('app');
$logger->pushHandler($handler);

$logger->info('Application started');
// Creates: logs/app.log

// Next day
$logger->info('Still logging');
// Creates: logs/app-2025-01-07.log
// Starts: new logs/app.log
```

### Configuration

```php
<?php
use Monolog\Logger;
use Monolog\Handlers\RotatingFileHandler;

$handler = new RotatingFileHandler(
    'logs/app.log',         // Filename
    30,                     // Max files
    Logger::INFO,           // Min level
    true,                   // Bubble
    0644                    // File permissions
);

// Additional configuration
$handler->setFilenameFormat(
    '{filename}-{date}',    // Filename pattern
    'Y-m-d'                 // Date format
);

$logger = new Logger('app');
$logger->pushHandler($handler);
```

### Filename Patterns

```php
<?php
// Control how rotated files are named

$handler = new RotatingFileHandler('logs/app.log');

// Pattern with date
$handler->setFilenameFormat(
    '{filename}-{date}',
    'Y-m-d'  // app-2025-01-06.log
);

// Different date formats
'Y-m-d H:i:s'       // app-2025-01-06 14:30:45.log
'Y-m-d-His'         // app-2025-01-06-143045.log
'Y-W'               // app-2025-01.log (week)
'Y-m'               // app-2025-01.log (month)
'Y'                 // app-2025.log (year)
```

---

## Custom Rotation Logic

### Custom Rotation Handler

```php
<?php
// Implement custom rotation strategy

use Monolog\Handler\AbstractProcessingHandler;

class CustomRotatingHandler extends AbstractProcessingHandler {
    private $filename;
    private $maxSize;
    private $maxFiles;
    private $dateFormat = 'Y-m-d';
    private $handle;
    
    public function __construct(
        $filename,
        $maxSize = 10485760,  // 10MB
        $maxFiles = 30
    ) {
        parent::__construct();
        $this->filename = $filename;
        $this->maxSize = $maxSize;
        $this->maxFiles = $maxFiles;
    }
    
    protected function write(LogRecord $record): void {
        // Open file
        $this->handle = fopen($this->getFilepath(), 'a');
        
        // Check if rotation needed
        if (filesize($this->getFilepath()) > $this->maxSize) {
            $this->rotate();
        }
        
        // Write record
        fwrite($this->handle, $this->format($record));
        fclose($this->handle);
    }
    
    private function rotate(): void {
        // Get base name and extension
        $info = pathinfo($this->filename);
        $basename = $info['filename'];
        $dirname = $info['dirname'];
        
        // Current date
        $date = date($this->dateFormat);
        
        // Rotate: app.log -> app-2025-01-06.log
        $rotated = "$dirname/$basename-$date.log";
        
        if (file_exists($this->filename)) {
            rename($this->filename, $rotated);
        }
        
        // Cleanup old files
        $this->cleanup($dirname, $basename);
    }
    
    private function cleanup($dirname, $basename): void {
        $files = glob("$dirname/$basename-*.log");
        
        // Sort by date
        rsort($files);
        
        // Delete files beyond max
        for ($i = $this->maxFiles; $i < count($files); $i++) {
            unlink($files[$i]);
        }
    }
    
    private function getFilepath(): string {
        return $this->filename;
    }
    
    private function format($record): string {
        return sprintf(
            "[%s] %s.%s: %s\n",
            date('Y-m-d H:i:s'),
            $record->channel,
            $record->level->name,
            $record->message
        );
    }
}

// Usage
$handler = new CustomRotatingHandler(
    'logs/app.log',
    10485760,  // 10MB
    30         // Keep 30 files
);

$logger = new Logger('app');
$logger->pushHandler($handler);
```

### Compression Handler

```php
<?php
// Automatically compress old logs

class CompressingRotatingHandler extends AbstractProcessingHandler {
    private $filename;
    private $maxSize;
    private $compressThreshold = 7;  // Compress after 7 days
    
    protected function write(LogRecord $record): void {
        $handle = fopen($this->filename, 'a');
        
        if (filesize($this->filename) > $this->maxSize) {
            $this->rotate();
        }
        
        fwrite($handle, $this->formatRecord($record));
        fclose($handle);
        
        // Compress old logs
        $this->compressOldLogs();
    }
    
    private function compressOldLogs(): void {
        $dir = dirname($this->filename);
        $basename = basename($this->filename, '.log');
        
        $files = glob("$dir/$basename-*.log");
        
        foreach ($files as $file) {
            $mtime = filemtime($file);
            $age = time() - $mtime;
            
            // Compress if older than threshold
            if ($age > ($this->compressThreshold * 86400)) {
                if (!file_exists("$file.gz")) {
                    $this->gzipFile($file);
                    unlink($file);  // Delete original
                }
            }
        }
    }
    
    private function gzipFile($filename): void {
        $handle = gzopen("$filename.gz", 'w9');
        $data = file_get_contents($filename);
        gzwrite($handle, $data);
        gzclose($handle);
    }
}
```

---

## Best Practices

### File Permissions

```php
<?php
// Set appropriate permissions

$handler = new RotatingFileHandler('logs/app.log');

// Set file mode (user read/write, group read, others none)
$handler->chmod(0644);

// Create logs directory with proper permissions
mkdir('logs', 0755, true);

// Ensure web server can write
// On Linux:
// chown www-data:www-data logs/
// chmod 755 logs/
```

### Disk Space Management

```php
<?php
// Monitor disk usage

class DiskAwareRotatingHandler extends RotatingFileHandler {
    private $minFreeSpace = 100 * 1024 * 1024;  // 100MB
    
    public function write(LogRecord $record): void {
        $freeSpace = disk_free_space(dirname($this->filename));
        
        if ($freeSpace < $this->minFreeSpace) {
            // Not enough space, clean aggressively
            $this->cleanupAggressively();
        }
        
        parent::write($record);
    }
    
    private function cleanupAggressively(): void {
        // Delete all but most recent files
        // or alert operations team
    }
}
```

### Log Retention Policy

```php
<?php
// Define and enforce retention policy

class RetentionPolicy {
    private $maxDays = 90;
    private $maxSize = 1 * 1024 * 1024 * 1024;  // 1GB
    
    public function cleanup($dir, $basename): void {
        $now = time();
        $files = glob("$dir/$basename-*.log");
        
        $totalSize = 0;
        
        // Sort newest first
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        foreach ($files as $file) {
            $age = $now - filemtime($file);
            $size = filesize($file);
            
            // Delete if too old
            if ($age > ($this->maxDays * 86400)) {
                unlink($file);
                continue;
            }
            
            // Delete if exceeds total size
            $totalSize += $size;
            if ($totalSize > $this->maxSize) {
                unlink($file);
            }
        }
    }
}
```

---

## Complete Examples

### Example 1: Production Rotating Setup

```php
<?php
// Production-grade rotating file setup

use Monolog\Logger;
use Monolog\Handlers\RotatingFileHandler;
use Monolog\Formatters\JsonFormatter;

function createProductionLogger() {
    $logger = new Logger('production');
    
    // Application logs
    $appHandler = new RotatingFileHandler(
        'logs/app.log',
        30,                 // Keep 30 daily files
        Logger::INFO
    );
    $appHandler->setFormatter(new JsonFormatter());
    $logger->pushHandler($appHandler);
    
    // Error logs (separate, longer retention)
    $errorHandler = new RotatingFileHandler(
        'logs/errors.log',
        90,                 // Keep 90 daily files
        Logger::ERROR
    );
    $errorHandler->setFormatter(new JsonFormatter());
    $logger->pushHandler($errorHandler);
    
    // Debug logs (development only)
    if (getenv('APP_DEBUG')) {
        $debugHandler = new RotatingFileHandler(
            'logs/debug.log',
            7,                 // Keep 7 daily files
            Logger::DEBUG
        );
        $logger->pushHandler($debugHandler);
    }
    
    return $logger;
}

$logger = createProductionLogger();
```

### Example 2: Size-Based Rotation

```php
<?php
// Rotate by size instead of date

class SizeBasedRotatingHandler extends AbstractProcessingHandler {
    private $filename;
    private $maxSize;
    private $fileIndex = 0;
    
    public function __construct(
        $filename,
        $maxSize = 10485760  // 10MB
    ) {
        parent::__construct();
        $this->filename = $filename;
        $this->maxSize = $maxSize;
    }
    
    protected function write(LogRecord $record): void {
        $filepath = $this->getCurrentFile();
        
        // Check if rotation needed
        if (file_exists($filepath) && filesize($filepath) >= $this->maxSize) {
            $this->rotate();
            $filepath = $this->getCurrentFile();
        }
        
        $handle = fopen($filepath, 'a');
        fwrite($handle, $this->formatRecord($record));
        fclose($handle);
    }
    
    private function getCurrentFile(): string {
        $info = pathinfo($this->filename);
        
        // Find next available index
        $this->fileIndex = 1;
        while (file_exists(
            "{$info['dirname']}/{$info['filename']}-{$this->fileIndex}.{$info['extension']}"
        )) {
            $this->fileIndex++;
        }
        
        // For current file, check if it exists
        $current = $this->filename;
        if (file_exists($current)) {
            return $current;
        }
        
        return $current;
    }
    
    private function rotate(): void {
        $info = pathinfo($this->filename);
        
        // Rename: app.log -> app-1.log
        $rotated = "{$info['dirname']}/{$info['filename']}-1.{$info['extension']}";
        
        if (file_exists($this->filename)) {
            rename($this->filename, $rotated);
        }
    }
}
```

### Example 3: Multi-Channel Rotating Logs

```php
<?php
// Different rotating handlers per channel

class MultiChannelLogger {
    private $loggers = [];
    
    public function __construct() {
        $this->setupChannels();
    }
    
    private function setupChannels(): void {
        // Application logs (30 days)
        $this->loggers['app'] = $this->createLogger(
            'app',
            'logs/app.log',
            30
        );
        
        // Payment logs (90 days for compliance)
        $this->loggers['payments'] = $this->createLogger(
            'payments',
            'logs/payments.log',
            90
        );
        
        // Security logs (365 days for audit)
        $this->loggers['security'] = $this->createLogger(
            'security',
            'logs/security.log',
            365
        );
        
        // Database logs (14 days)
        $this->loggers['database'] = $this->createLogger(
            'database',
            'logs/database.log',
            14
        );
    }
    
    private function createLogger($name, $path, $maxDays) {
        $logger = new Logger($name);
        
        $handler = new RotatingFileHandler($path, $maxDays);
        $handler->setFormatter(new JsonFormatter());
        
        $logger->pushHandler($handler);
        return $logger;
    }
    
    public function getLogger($channel) {
        return $this->loggers[$channel] ?? $this->loggers['app'];
    }
}

// Usage
$loggers = new MultiChannelLogger();

$loggers->getLogger('app')->info('User action');
$loggers->getLogger('payments')->info('Payment processed');
$loggers->getLogger('security')->warning('Suspicious activity');
$loggers->getLogger('database')->debug('Query executed');

// Each channel has its own rotation policy
```

---

## Key Takeaways

**Rotating File Handler Checklist:**

1. ✅ Choose rotation strategy (size or date)
2. ✅ Set appropriate retention policy
3. ✅ Configure file permissions
4. ✅ Monitor disk space
5. ✅ Automate cleanup
6. ✅ Test rotation
7. ✅ Plan for compression
8. ✅ Document policy

---

## See Also

- [Handlers](5-handler.md)
- [Logging Libraries](2-logging-library.md)
- [Project Setup](3-create-project.md)
