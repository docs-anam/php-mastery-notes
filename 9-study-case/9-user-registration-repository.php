<?php
/**
 * User Registration Repository
 *  
 * 1. Create a new file domain/User.php to define the User class with properties: username, password, and email.
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Domain;
 * 
 * class User
 * {
 *     public string $username;
 *     public string $password;
 *     public string $email;
 * }
 * 
 * 2. Create a new file repository/UserRepository.php to handle user data storage.
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Repository;
 * 
 * use Mukhoiran\LoginManagement\Domain\User;
 * 
 * class UserRepository
 * {
 *     private \PDO $connection;
 * 
 *     public function __construct(\PDO $connection)
 *     {
 *         $this->connection = $connection;
 *     }
 * 
 *     public function save(User $user): User
 *     {
 *         $statement = $this->connection->prepare("INSERT INTO users(username, password, email) VALUES (?, ?, ?)");
 *         $statement->execute([
 *             $user->username, $user->password, $user->email
 *         ]);
 *         return $user;
 *     }
 * 
 *     public function findById(string $username): ?User
 *     {
 *         $statement = $this->connection->prepare("SELECT username, password, email FROM users WHERE username = ?");
 *         $statement->execute([$username]);
 * 
 *         try {
 *             if ($row = $statement->fetch()) {
 *                 $user = new User();
 *                 $user->username = $row['username'];
 *                 $user->password = $row['password'];
 *                 $user->email = $row['email'];
 *                 return $user;
 *             }
 *         } finally {
 *             $statement->closeCursor();
 *         }
 *     }
 * 
 *     public function deleteAll(): void
 *     {
 *         $this->connection->exec("DELETE from users");
 *     }
 * }
 * 
 * The above code snippets define a User class and a UserRepository for handling user registration in a secure manner.
 */