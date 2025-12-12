<?php

function getDatabaseConfig(): array
{
    return [
        "database" => [
            "test" => [
                "url" => "mysql:host=127.0.0.1:3306;dbname=login_management_test_db",
                "username" => "root",
                "password" => ""
            ],
            "prod" => [
                "url" => "mysql:host=127.0.0.1:3306;dbname=login_management_db",
                "username" => "root",
                "password" => ""
            ]
        ]
    ];
}