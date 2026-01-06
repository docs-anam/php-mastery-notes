<?php
/** Setup the database for the login management project
 *
 * 1. Create a new database for the login management project.
 *    Example: login_management_db, login_management_test_db
 * 
 * 2. Create a new user for the database with appropriate privileges.
 *    Example: login_user with password login_pass or use the deafault root user for local development.
 *
 * 3. Create the necessary tables for user management:
 *    - users table to store user information (id, username, password, email, created_at)
 *    - sessions table to manage user sessions (id, user_id, session_token, created_at, expires_at)
 * 
 * Example SQL commands:
 * 
 * CREATE DATABASE login_management_db;
 * CREATE USER 'login_user'@'localhost' IDENTIFIED BY 'login_pass';
 * GRANT ALL PRIVILEGES ON login_management_db.* TO 'login_user'@'localhost';
 * FLUSH PRIVILEGES;
 *
 * USE login_management_db;
 * 
 * CREATE TABLE users (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   username VARCHAR(50) NOT NULL UNIQUE,
 *   password VARCHAR(255) NOT NULL,
 *   email VARCHAR(100) NOT NULL UNIQUE,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 * 
 * CREATE TABLE sessions (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   username VARCHAR(50) NOT NULL,
 *   session_token VARCHAR(255) NOT NULL UNIQUE,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   expires_at TIMESTAMP NULL,
 *   FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
 * );
 * 
 * 4. Update the database configuration in the project to use the new database and user credentials.
 *    - Update config/database.php or equivalent configuration file.
 * 
 */