<?php

/*
Repository Pattern in PDO PHP - Detailed Summary with Executable Samples

The Repository Pattern abstracts the data access layer, separating it from business logic. In PHP, using PDO, this pattern helps organize code for maintainability, testability, and reusability.

Key Concepts:
1. **Repository**: Encapsulates logic for accessing data sources (e.g., UserRepository).
2. **Entity/Model**: Represents a business object or table (e.g., User).
3. **Data Mapper**: Optionally maps between database rows and objects.

Benefits:
- Decouples business logic from data access.
- Centralizes and organizes data access code.
- Facilitates unit testing (repositories can be mocked).
- Supports multiple data sources.

Typical Structure:
- `UserRepository`: Handles all DB operations for User.
- `User`: Represents user data.
- `Database`: PDO connection, injected into repository.

--------------------------
**Example**
--------------------------
*/

// 1. Entity Class
class User {
    public $id;
    public $name;
    public $email;

    public function __construct($id = null, $name = '', $email = '') {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
}

// 2. Repository Class
class UserRepository {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll() {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row['id'], $row['name'], $row['email']);
        }
        return $users;
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row['id'], $row['name'], $row['email']);
        }
        return null;
    }

    public function save(User $user) {
        if ($user->id) {
            // Update
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            return $stmt->execute([$user->name, $user->email, $user->id]);
        } else {
            // Insert
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            $result = $stmt->execute([$user->name, $user->email]);
            if ($result) {
                $user->id = $this->pdo->lastInsertId();
            }
            return $result;
        }
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

// 3. Usage Example

// Setup PDO (use your own DB credentials)
$pdo = new PDO('mysql:host=localhost;dbname=testdb', 'root', 'root');

// Create table for demo (run once)
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
)");

// Instantiate repository
$userRepo = new UserRepository($pdo);

// Insert new user
$newUser = new User(null, 'Alice', 'alice@example.com');
$userRepo->save($newUser);

// Fetch all users
$users = $userRepo->findAll();
foreach ($users as $user) {
    echo "{$user->id}: {$user->name} ({$user->email})\n";
}

// Find user by ID
$user = $userRepo->findById($newUser->id);
if ($user) {
    echo "Found user: {$user->name}\n";
}

// Update user
$user->name = 'Alice Smith';
$userRepo->save($user);

// Delete user
$userRepo->delete($user->id);

/*
--------------------------
Summary:
- The repository pattern keeps data access logic in one place.
- Entities represent data.
- Repositories handle CRUD operations.
- PDO is injected for DB access.
- This pattern improves code organization and testability.
*/

?>