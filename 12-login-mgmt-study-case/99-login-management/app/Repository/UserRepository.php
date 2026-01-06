<?php

namespace Mukhoiran\LoginManagement\Repository;

use Mukhoiran\LoginManagement\Domain\User;

class UserRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $statement = $this->connection->prepare("INSERT INTO users(username, password, email) VALUES (?, ?, ?)");
        $statement->execute([
            $user->username, $user->password, $user->email
        ]);
        return $user;
    }

    public function update(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE users SET password = ?, email = ? WHERE username = ?");
        $statement->execute([
            $user->password, $user->email, $user->username
        ]);
        return $user;
    }

    public function findById(string $username): ?User
    {
        $statement = $this->connection->prepare("SELECT username, password, email FROM users WHERE username = ?");
        $statement->execute([$username]);

        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->username = $row['username'];
                $user->password = $row['password'];
                $user->email = $row['email'];   
                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE from users");
    }
}