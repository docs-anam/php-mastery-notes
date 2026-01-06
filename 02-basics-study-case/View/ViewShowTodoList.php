<?php

require_once __DIR__ . "/../Model/TodoList.php";
require_once __DIR__ . "/../BusinessLogic/ShowTodoList.php";
require_once __DIR__ . "/../View/ViewAddTodoList.php";
require_once __DIR__ . "/../View/ViewRemoveTodoList.php";
require_once __DIR__ . "/../Helper/Input.php";

function viewShowTodoList()
{
    while (true) {
        showTodoList();

        echo "MENU: " . PHP_EOL;
        echo "1. Add Todo" . PHP_EOL;
        echo "2. Remove Todo" . PHP_EOL;
        echo "x. Exit" . PHP_EOL;

        $options = input("Choose");

        if ($options == "1") {
            viewAddTodoList();
        } else if ($options == "2") {
            viewRemoveTodoList();
        } else if ($options == "x") {
            break;
        } else {
            echo "Options is not recognized" . PHP_EOL;
        }
    }

    echo "========================" . PHP_EOL;
    echo "Thank you for using this application" . PHP_EOL;
    echo "See you next time" . PHP_EOL;
}