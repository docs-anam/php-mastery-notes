Todolist App - Database Setup Instructions
==========================================

## Follow these steps to create the required database and table for the Todolist app.

1. Create the database:

    ```
    CREATE DATABASE todolist_db;
    ```

2. Select the database:

    ```
    USE todolist_db;
    ```

3. Create the table:

    ```
    CREATE TABLE todolist (
         id INT AUTO_INCREMENT PRIMARY KEY,
         todo VARCHAR(255) NOT NULL
    );
    ```

## How to Run `app.php` in Terminal

1. **Open Terminal**  
    Open your terminal application.

2. **Navigate to Project Directory**  
    Change directory to where `app.php` is located:
    ```
    cd /Applications/MAMP/htdocs/php-mastery-notes/1-basics/99-study-case-todolist-app
    ```

3. **Run the App**  
    Execute the following command:
    ```
    php app.php
    ```

## Requirements

- PHP installed on your system (version 8.0 or higher recommended).

## Notes

- Make sure all required files are in the same directory as `app.php`.
- If you encounter permission issues, try running with `sudo` (on Unix-based systems).