<?php

namespace View {

    use Service\TodolistService;
    use Helper\InputHelper;

    class TodolistView
    {

        private TodolistService $todolistService;

        public function __construct(TodolistService $todolistService)
        {
            $this->todolistService = $todolistService;
        }

        function showTodolist(): void
        {
            while (true) {
                $this->todolistService->showTodolist();

                echo "MENU" . PHP_EOL;
                echo "1. Add Todo" . PHP_EOL;
                echo "2. Remove Todo" . PHP_EOL;
                echo "x. Exit" . PHP_EOL;

                $options = InputHelper::input("Choose");

                if ($options == "1") {
                    $this->addTodolist();
                } else if ($options == "2") {
                    $this->removeTodolist();
                } else if ($options == "x") {
                    break;
                } else {
                    echo "Selected options is not valid" . PHP_EOL;
                }
            }

            echo "See you again!" . PHP_EOL;
        }

        function addTodolist(): void
        {
            echo "ADD TODO" . PHP_EOL;

            $todo = InputHelper::input("Todo (x to cancel)");

            if ($todo == "x") {
                echo "Cancel add todo" . PHP_EOL;
            } else {
                $this->todolistService->addTodolist($todo);
            }
        }

        function removeTodolist(): void
        {
            echo "REMOVE TODO" . PHP_EOL;

            $options = InputHelper::input("Nomor (x to cancel)");

            if ($options == "x") {
                echo "Cancel remove todo" . PHP_EOL;
            } else {
                $this->todolistService->removeTodolist($options);
            }
        }

    }

}