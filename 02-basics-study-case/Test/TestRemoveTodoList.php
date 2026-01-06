<?php

require_once __DIR__ . "/../Model/TodoList.php";
require_once __DIR__ . "/../BusinessLogic/AddTodoList.php";
require_once __DIR__ . "/../BusinessLogic/ShowTodoList.php";
require_once __DIR__ . "/../BusinessLogic/RemoveTodoList.php";


addTodoList("Muhammad");
addTodoList("Khoirul");
addTodoList("Anam");
addTodoList("Jhon");
addTodoList("Jackie");

showTodoList();

removeTodoList(3);

showTodoList();

removeTodoList(2);

showTodoList();

$success = removeTodoList(4);
var_dump($success);

showTodoList();