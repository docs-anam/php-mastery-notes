# Log Formatters and Output Formatting

## Overview

Learn how to format log output for different purposes using built-in formatters, custom formatters, and handling various output formats.

---

## Table of Contents

1. What are Formatters
2. Built-in Formatters
3. Custom Formatters
4. Output Formats
5. Formatter Configuration
6. Multi-Format Logging
7. Complete Examples

---

## What are Formatters

### Purpose

```
Formatter = Format output before writing

Raw Record:
{
  "level": "INFO",
  "message": "User logged in",
  "context": {"user_id": 123}
}

Formatted Output:
[2025-01-06 14:30:45] app.INFO: User logged in {"user_id": 123}

or JSON:
{"message":"User logged in","level":"INFO","context":{"user_id":123}}
```

### Formatter Chain

```
Logger Entry
  ↓
Processors (enrich data)
  ↓
Handler (choose destination)
  ↓
Formatter (format output)
  ↓
Output
```

---

## Built-in Formatters

### LineFormatter

```php
<?php
// Human-readable single-line format

use Monolog\Handlers\StreamHandler;
use Monolog\Formatters\LineFormatter;

$handler = new StreamHandler('logs/app.log');

$formatter = new LineFormatter(
    "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
    "Y-m-d H:i:s"
);

$handler->setFormatter($formatter);
$logger->pushHandler($handler);

// Output:
// [2025-01-06 14:30:45] app.INFO: User logged in {"user_id":123} []
```

### JsonFormatter

```php
<?php
// Machine-readable JSON format

use Monolog\Formatters\JsonFormatter;

$handler = new StreamHandler('logs/app.log');
$formatter = new JsonFormatter();
$handler->setFormatter($formatter);
$logger->pushHandler($handler);

// Output:
// {"message":"User logged in","context":{"user_id":123},"level":200,"level_name":"INFO","channel":"app","datetime":"2025-01-06T14:30:45.123456+00:00"}
```

### HtmlFormatter

```php
<?php
// HTML table format for browser viewing

use Monolog\Formatters\HtmlFormatter;

$handler = new StreamHandler('logs/app.html');
$formatter = new HtmlFormatter();
$handler->setFormatter($formatter);

// Output: HTML table with formatted rows
```

### ChromePHPFormatter

```php
<?php
// Send logs to Chrome console via headers

use Monolog\Handlers\ChromePHPHandler;

$handler = new ChromePHPHandler();
$logger->pushHandler($handler);

$logger->info('Debug info');
// Appears in Chrome DevTools Console
```

---

## Custom Formatters

### Basic Custom Formatter

```php
<?php
// Implement FormatterInterface

use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class CustomFormatter implements FormatterInterface {
    public function format(LogRecord $record): string {
        return sprintf(
            "[%s] %s.%s: %s\n",
            $record->datetime->format('Y-m-d H:i:s'),
            $record->channel,
            $record->level->name,
            $record->message
        );
    }
    
    public function formatBatch(array $records): string {
        $formatted = '';
        foreach ($records as $record) {
            $formatted .= $this->format($record);
        }
        return $formatted;
    }
}

// Usage
$handler = new StreamHandler('logs/app.log');
$handler->setFormatter(new CustomFormatter());
```

### Advanced Custom Formatter

```php
<?php
// Complex formatting with context

class AdvancedFormatter implements FormatterInterface {
    private $dateFormat;
    
    public function __construct($dateFormat = 'Y-m-d H:i:s') {
        $this->dateFormat = $dateFormat;
    }
    
    public function format(LogRecord $record): string {
        $timestamp = $record->datetime->format($this->dateFormat);
        $level = str_pad($record->level->name, 8);
        $channel = str_pad($record->channel, 12);
        
        $line = "$timestamp | $level | $channel | {$record->message}";
        
        // Add context if present
        if (!empty($record->context)) {
            $line .= " | " . json_encode($record->context);
        }
        
        // Add extra if present
        if (!empty($record->extra)) {
            $line .= " | Extra: " . json_encode($record->extra);
        }
        
        return $line . "\n";
    }
    
    public function formatBatch(array $records): string {
        return implode('', array_map([$this, 'format'], $records));
    }
}
```

### Context-Aware Formatter

```php
<?php
// Format based on context data

class ContextAwareFormatter implements FormatterInterface {
    public function format(LogRecord $record): string {
        $context = $record->context;
        
        // Build context string
        $contextParts = [];
        if (isset($context['user_id'])) {
            $contextParts[] = "User: {$context['user_id']}";
        }
        if (isset($context['request_id'])) {
            $contextParts[] = "Request: {$context['request_id']}";
        }
        if (isset($context['order_id'])) {
            $contextParts[] = "Order: {$context['order_id']}";
        }
        
        $contextStr = !empty($contextParts) 
            ? ' [' . implode(', ', $contextParts) . ']'
            : '';
        
        return sprintf(
            "[%s] %s: %s%s\n",
            $record->datetime->format('H:i:s'),
            $record->level->name,
            $record->message,
            $contextStr
        );
    }
    
    public function formatBatch(array $records): string {
        return implode('', array_map([$this, 'format'], $records));
    }
}
```

---

## Output Formats

### Text Format

```php
<?php
// Simple text output

$formatter = new LineFormatter(
    "%level_name%: %message%\n"
);

// Output:
// INFO: User logged in
// ERROR: Connection failed
```

### Structured Text Format

```php
<?php
// Key-value pairs

class StructuredTextFormatter implements FormatterInterface {
    public function format(LogRecord $record): string {
        $parts = [
            "timestamp={$record->datetime->getTimestamp()}",
            "level={$record->level->name}",
            "message={$record->message}",
        ];
        
        foreach ($record->context as $key => $value) {
            $parts[] = "$key=" . json_encode($value);
        }
        
        return implode(" ", $parts) . "\n";
    }
    
    public function formatBatch(array $records): string {
        return implode('', array_map([$this, 'format'], $records));
    }
}

// Output:
// timestamp=1704541845 level=INFO message="User logged in" user_id=123
```

### CSV Format

```php
<?php
// CSV output

class CsvFormatter implements FormatterInterface {
    public function format(LogRecord $record): string {
        $row = [
            $record->datetime->format('Y-m-d H:i:s'),
            $record->level->name,
            $record->channel,
            $record->message,
            json_encode($record->context),
        ];
        
        return implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
    }
    
    public function formatBatch(array $records): string {
        $csv = '"timestamp","level","channel","message","context"' . "\n";
        foreach ($records as $record) {
            $csv .= $this->format($record);
        }
        return $csv;
    }
}

// Output:
// timestamp,level,channel,message,context
// "2025-01-06 14:30:45","INFO","app","User logged in","{""user_id"":123}"
```

### JSON Lines Format

```php
<?php
// One JSON object per line (JSONL)

class JsonLinesFormatter implements FormatterInterface {
    public function format(LogRecord $record): string {
        $data = [
            'timestamp' => $record->datetime->toIso8601String(),
            'level' => $record->level->name,
            'channel' => $record->channel,
            'message' => $record->message,
            'context' => $record->context,
            'extra' => $record->extra,
        ];
        
        return json_encode($data) . "\n";
    }
    
    public function formatBatch(array $records): string {
        return implode('', array_map([$this, 'format'], $records));
    }
}

// Output:
// {"timestamp":"2025-01-06T14:30:45Z","level":"INFO","message":"User logged in",...}
// {"timestamp":"2025-01-06T14:30:46Z","level":"ERROR","message":"Failed",...}
```

---

## Formatter Configuration

### Per-Handler Formatters

```php
<?php
// Different formatters for different handlers

$logger = new Logger('app');

// File: JSON format
$fileHandler = new StreamHandler('logs/app.log');
$fileHandler->setFormatter(new JsonFormatter());
$logger->pushHandler($fileHandler);

// Slack: Text format
$slackHandler = new SlackHandler($webhookUrl);
$slackHandler->setFormatter(new LineFormatter('%level_name%: %message%'));
$logger->pushHandler($slackHandler);

// Email: HTML format
$emailHandler = new NativeMailerHandler('ops@example.com');
$emailHandler->setFormatter(new HtmlFormatter());
$logger->pushHandler($emailHandler);
```

### Dynamic Formatter Selection

```php
<?php
// Choose formatter based on environment

class SmartFormatter implements FormatterInterface {
    private $env;
    private $formatter;
    
    public function __construct($env = 'production') {
        $this->env = $env;
        $this->selectFormatter();
    }
    
    private function selectFormatter() {
        switch ($this->env) {
            case 'development':
                // Human-readable
                $this->formatter = new LineFormatter(
                    "%level_name% %channel%: %message%\n"
                );
                break;
            
            case 'production':
                // Structured JSON
                $this->formatter = new JsonFormatter();
                break;
            
            case 'test':
                // Minimal
                $this->formatter = new LineFormatter('%message%');
                break;
        }
    }
    
    public function format(LogRecord $record): string {
        return $this->formatter->format($record);
    }
    
    public function formatBatch(array $records): string {
        return $this->formatter->formatBatch($records);
    }
}
```

---

## Multi-Format Logging

### Multiple Formats Same Logs

```php
<?php
// Send to multiple destinations with different formats

$logger = new Logger('app');

// 1. JSON to file (for aggregation)
$fileHandler = new RotatingFileHandler('logs/app.json');
$fileHandler->setFormatter(new JsonFormatter());
$logger->pushHandler($fileHandler);

// 2. Text to console (for development)
$consoleHandler = new StreamHandler('php://stdout');
$consoleHandler->setFormatter(new LineFormatter(
    "%level_name%: %message% %context%\n"
));
$logger->pushHandler($consoleHandler);

// 3. CSV to another file (for reporting)
$csvHandler = new StreamHandler('logs/app.csv');
$csvHandler->setFormatter(new CsvFormatter());
$logger->pushHandler($csvHandler);

// 4. Text to Slack (for humans)
$slackHandler = new SlackHandler($webhookUrl);
$slackHandler->setFormatter(new LineFormatter('%message%'));
$slackHandler->setLevel(Logger::WARNING);
$logger->pushHandler($slackHandler);

// Single log produces:
// - JSON in logs/app.json
// - Text in stdout
// - CSV in logs/app.csv
// - Slack message (if warning+)
```

---

## Complete Examples

### Example 1: Comprehensive Formatter Setup

```php
<?php
// Production-ready formatter configuration

class FormatterFactory {
    public static function createJsonFormatter() {
        return new JsonFormatter();
    }
    
    public static function createTextFormatter() {
        return new LineFormatter(
            "[%datetime%] %level_name%: %message% %context% %extra%\n",
            "Y-m-d H:i:s.u"
        );
    }
    
    public static function createCompactFormatter() {
        return new LineFormatter(
            "%level_name%: %message%\n"
        );
    }
    
    public static function createDetailedFormatter() {
        return new LineFormatter(
            "[%datetime%] [%channel%] %level_name%: %message%\n" .
            "Context: %context%\n" .
            "Extra: %extra%\n\n",
            "Y-m-d H:i:s"
        );
    }
}

// Usage
$logger = new Logger('app');

// Add handlers with formatters
$fileHandler = new RotatingFileHandler('logs/app.log');
$fileHandler->setFormatter(FormatterFactory::createJsonFormatter());
$logger->pushHandler($fileHandler);

$consoleHandler = new StreamHandler('php://stdout');
$consoleHandler->setFormatter(FormatterFactory::createCompactFormatter());
$logger->pushHandler($consoleHandler);
```

### Example 2: Custom Application Formatter

```php
<?php
// Application-specific formatter

class ApplicationFormatter implements FormatterInterface {
    public function format(LogRecord $record): string {
        $timestamp = $record->datetime->format('Y-m-d H:i:s');
        $level = str_pad(strtoupper($record->level->name), 9);
        
        // Build message
        $message = $record->message;
        
        // Add request context if available
        if (isset($record->extra['request_id'])) {
            $message = "[{$record->extra['request_id']}] $message";
        }
        
        // Add user context if available
        if (isset($record->extra['user_id'])) {
            $message .= " (User: {$record->extra['user_id']})";
        }
        
        // Build line
        $line = "$timestamp $level $message";
        
        // Add context if present
        if (!empty($record->context)) {
            $line .= "\n  Context: " . $this->formatContext($record->context);
        }
        
        return $line . "\n";
    }
    
    private function formatContext(array $context, $indent = 2) {
        $lines = [];
        foreach ($context as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            $lines[] = str_repeat(' ', $indent) . "$key: $value";
        }
        return implode("\n", $lines);
    }
    
    public function formatBatch(array $records): string {
        return implode('', array_map([$this, 'format'], $records));
    }
}

// Usage
$handler = new StreamHandler('logs/app.log');
$handler->setFormatter(new ApplicationFormatter());
$logger->pushHandler($handler);

// Output:
// 2025-01-06 14:30:45 INFO     [req_abc] User logged in (User: 123)
//   Context: email: user@example.com
//            ip: 192.168.1.1
```

---

## Key Takeaways

**Formatter Checklist:**

1. ✅ Choose appropriate formatter
2. ✅ Use JSON for aggregation
3. ✅ Use text for humans
4. ✅ Set per-handler formatters
5. ✅ Include context in output
6. ✅ Consider log size
7. ✅ Test formatter output
8. ✅ Document format

---

## See Also

- [Handlers](5-handler.md)
- [Processors](9-processor.md)
- [Logging Libraries](2-logging-library.md)
