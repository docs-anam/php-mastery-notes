<?php

namespace Mukhoiran\LoginManagement\Repository;

use Mukhoiran\LoginManagement\Domain\Session;

class SessionRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Session $session): Session
    {
        $statement = $this->connection->prepare("INSERT INTO sessions(session_token, username) VALUES (?, ?)");
        $statement->execute([$session->session_token, $session->username]);
        return $session;
    }

    public function findById(string $id): ?Session
    {
        $statement = $this->connection->prepare("SELECT session_token, username from sessions WHERE session_token = ?");
        $statement->execute([$id]);

        try {
            if($row = $statement->fetch()){
                $session = new Session();
                $session->session_token = $row['session_token'];
                $session->username = $row['username'];
                return $session;
            }else{
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteById(string $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM sessions WHERE session_token = ?");
        $statement->execute([$id]);
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM sessions");
    }

}