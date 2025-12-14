<?php

/**
 * PHP Logging - Create Project with Monolog
 * 
 * SUMMARY:
 * --------
 * This demonstrates setting up a logging system using Monolog in a PHP MVC project.
 * 
 * PROJECT SETUP:
 * 1. Copy the MVC project from 8-php-mvc/99-mvc-project
 * 2. Install Monolog via Composer:
 *    composer require monolog/monolog
 * 
 * MONOLOG FEATURES:
 * - PSR-3 compliant logging library
 * - Multiple handlers (file, database, email, etc.)
 * - Different log levels (DEBUG, INFO, WARNING, ERROR, CRITICAL, etc.)
 * - Formatters for customizing log output
 * - Processors for adding extra context
 * 
 * BASIC USAGE:
 * - Create Logger instance with channel name
 * - Add handlers (StreamHandler for files, etc.)
 * - Log messages at appropriate levels
 * 
 * INTEGRATION WITH MVC:
 * - Add logger to dependency injection container
 * - Inject logger into controllers/services
 * - Log errors, user actions, and application events
 * - Configure different handlers for different environments
 * 
 * INSTALLATION COMMAND:
 * composer require monolog/monolog
 * 
 * This creates a robust logging infrastructure for production applications.
 */
?>