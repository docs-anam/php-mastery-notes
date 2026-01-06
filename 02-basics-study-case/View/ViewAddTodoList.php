<?php

require_once __DIR__ . "/../Model/TodoList.php";
require_once __DIR__ . "/../Helper/Input.php";
require_once __DIR__ . "/../BusinessLogic/AddTodoList.php";

function viewAddTodoList()
{
    $todo = input("Todo (x to cancel)");

    if ($todo == "x") {
        echo "Cancel add todo" . PHP_EOL;
    } else {
        addTodoList($todo);
    }
}