<?php
/**
 * LOGGING IN PHP - INTRODUCTION
 * 
 * What is Logging?
 * ================
 * Logging is the process of recording events, errors, and informational messages
 * that occur during application execution. It helps developers:
 * - Debug issues in production
 * - Monitor application health
 * - Track user activities
 * - Audit security events
 * - Analyze performance bottlenecks
 * 
 * Log Levels (PSR-3 Standard):
 * ============================
 * - EMERGENCY: System is unusable
 * - ALERT: Action must be taken immediately
 * - CRITICAL: Critical conditions
 * - ERROR: Runtime errors that don't require immediate action
 * - WARNING: Exceptional occurrences that are not errors
 * - NOTICE: Normal but significant events
 * - INFO: Interesting events (user login, SQL logs)
 * - DEBUG: Detailed debug information
 * 
 * Logging Flow Diagram:
 * =====================
 * 
 *  ┌─────────────┐
 *  │ Application │ (Generates log entries)
 *  └──────┬──────┘
 *         │
 *         ▼
 *  ┌─────────────┐
 *  │  Log File   │ (error.log, app.log, access.log)
 *  └──────┬──────┘
 *         │
 *         ▼
 *  ┌─────────────────┐
 *  │ Log Aggregator  │ (Logstash, Fluentd, Filebeat)
 *  └──────┬──────────┘
 *         │
 *         ▼
 *  ┌─────────────────┐
 *  │  Log Database   │ (Elasticsearch, MongoDB, MySQL)
 *  └──────┬──────────┘
 *         │
 *         ▼
 *  ┌─────────────────────┐
 *  │  Log Management     │ (Kibana, Grafana, Splunk)
 *  │  & Visualization    │ (Search, Filter, Alert, Dashboard)
 *  └─────────────────────┘
 * 
 * Common PHP Logging Solutions:
 * ==============================
 * - error_log() - Built-in PHP function
 * - Monolog - Popular PSR-3 compliant library
 * - syslog() - System-level logging
 * - Custom file handlers
 * 
 * Best Practices:
 * ===============
 * 1. Use appropriate log levels
 * 2. Include contextual information (user ID, IP, timestamp)
 * 3. Don't log sensitive data (passwords, credit cards)
 * 4. Implement log rotation to manage file sizes
 * 5. Use structured logging (JSON format)
 * 6. Monitor and alert on critical errors
 */

// Example: Basic logging
error_log("Application started", 0);

// Example: Log to specific file
error_log("User login attempt: user@example.com\n", 3, "/var/log/app.log");

// Example: Log with context
$message = sprintf(
    "[%s] ERROR: Database connection failed - Host: %s, User: %s",
    date('Y-m-d H:i:s'),
    'localhost',
    'app_user'
);
error_log($message);