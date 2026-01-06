<?php

require_once __DIR__ . "/../Model/TodoList.php";
require_once __DIR__ . "/../BusinessLogic/AddTodoList.php";


addTodoList("Muhammad");
addTodoList("Khoirul");
addTodoList("Anam");

var_dump($todoList);