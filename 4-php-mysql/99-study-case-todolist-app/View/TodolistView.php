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

                $option = InputHelper::input("Choose");

                if ($option == "1") {
                    $this->addTodolist();
                } else if ($option == "2") {
                    $this->removeTodolist();
                } else if ($option == "x") {
                    break;
                } else {
                    echo "Option $option not available" . PHP_EOL;
                }
            }

            echo "Thank you for using the Todolist App" . PHP_EOL;
        }

        function addTodolist(): void
        {
            echo "ADD TODO" . PHP_EOL;

            $todo = InputHelper::input("Todo (x to cancel)");

            if ($todo == "x") {
                echo "Cancel adding todo" . PHP_EOL;
            } else {
                $this->todolistService->addTodolist($todo);
            }
        }

        function removeTodolist(): void
        {
            echo "REMOVE TODO" . PHP_EOL;

            $option = InputHelper::input("Nomor (x for cancel)");

            if ($option == "x") {
                echo "Cancel removing todo" . PHP_EOL;
            } else {
                $this->todolistService->removeTodolist($option);
            }
        }

    }

}