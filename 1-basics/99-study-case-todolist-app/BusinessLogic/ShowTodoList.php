<?php

/**
 * Show the todo list
 */
function showTodoList()
{
    global $todoList;

    foreach ($todoList as $number => $value) {
        echo "$number. $value" . PHP_EOL;
    }
}