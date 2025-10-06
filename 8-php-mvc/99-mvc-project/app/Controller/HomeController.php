<?php

namespace Mukhoiran\MVCProject\Controller;

use Mukhoiran\MVCProject\App\View;

class HomeController
{
    public function index(): void
    {
        $model = [
            'title' => 'Home Page',
            'content' => 'Welcome to the Home Page!'
        ];

        View::render('Home/index', $model);
    }
    
    public function hello(): void
    {
        echo "HomeController.hello()";
    }

    public function world(): void
    {
        echo "HomeController.world()";
    }

    public function about(): void
    {
        echo "HomeController.about()";
    }
}