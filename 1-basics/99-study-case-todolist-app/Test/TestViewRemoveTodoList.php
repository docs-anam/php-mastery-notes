<?php

require_once __DIR__ . "/../Model/TodoList.php";
require_once __DIR__ . "/../View/ViewRemoveTodoList.php";
require_once __DIR__ . "/../BusinessLogic/AddTodoList.php";
require_once __DIR__ . "/../BusinessLogic/ShowTodoList.php";

addTodoList("Eko");
addTodoList("Kurniwan");
addTodoList("Khannedy");
addTodoList("Programmer");
addTodoList("Zaman");
addTodoList("Now");

showTodoList();

viewRemoveTodoList();

showTodoList();