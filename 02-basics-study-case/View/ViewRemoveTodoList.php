<?php

require_once __DIR__ . "/../Helper/Input.php";
require_once __DIR__ . "/../BusinessLogic/RemoveTodoList.php";

function viewRemoveTodoList()
{
    $options = input("Number (x to cancel)");

    if ($options == "x") {
        echo "Cancel remove todo" . PHP_EOL;
    } else {
        $success = removeTodoList($options);

        if ($success) {
            echo "Success remove todo with number $options" . PHP_EOL;
        } else {
            echo "Failed remove todo with number $options" . PHP_EOL;
        }
    }
}