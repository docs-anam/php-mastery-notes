<?php
/**
 * Setup a secure database connection for a Login Management System
 *
 * Summary (detailed) and example implementation for setting up a secure
 * database connection in a login management system.
 *
 * 1. Create folder config / outside webroot to hold configuration files.
 * 2. Create config/database.php to hold DB connection settings.
 *   <?php
 *
 *   function getDatabaseConfig(): array
 *   {
 *       return [
 *           "database" => [
 *               "test" => [
 *                   "url" => "mysql:host=127.0.0.1:3306;dbname=login_management_test_db",
 *                   "username" => "root",
 *                   "password" => ""
 *               ],
 *               "prod" => [
 *                   "url" => "mysql:host=127.0.0.1:3306;dbname=login_management_db",
 *                   "username" => "root",
 *                   "password" => ""
 *               ]
 *           ]
 *       ];
 *  }
 *  ?>
 * 
 * 3. Create a Database class to manage connections in app/Config/Database.php.
 * 4. Use singleton pattern to ensure one PDO instance per request.
 *  <?php
 *  
 *  namespace Mukhoiran\LoginManagement\Config;
 *
 *  class Database
 *  {
 *      private static ?\PDO $pdo = null;
 *
 *      public static function getConnection(string $env = "test"): \PDO{
 *          if(self::$pdo == null){
 *              // create new PDO
 *              require_once __DIR__ . '/../../config/database.php';
 *              $config = getDatabaseConfig();
 *              self::$pdo = new \PDO(
 *                  $config['database'][$env]['url'],
 *                  $config['database'][$env]['username'],
 *                  $config['database'][$env]['password']
 *              );
 *          }
 *
 *          return self::$pdo;
 *      }
 *  ?>
 *
 * The code below shows a small, secure Database class plus a sample
 * UserRepository and login helper demonstrating best practices.
 */