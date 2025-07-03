<?php

namespace Config {

    class Database
    {

        static function getConnection(): \PDO
        {
            $host = "127.0.0.1";
            $port = 3306;
            $database = "todolist_db"; // Ensure this matches the database created in the readme
            $username = "root";
            $password = "";

            return new \PDO("mysql:host=$host:$port;dbname=$database", $username, $password);
        }

    }

}