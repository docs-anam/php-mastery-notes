<?php

namespace Mukhoiran\LoginManagement\Controller;

use Mukhoiran\LoginManagement\App\View;
use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\Repository\SessionRepository;
use Mukhoiran\LoginManagement\Repository\UserRepository;
use Mukhoiran\LoginManagement\Service\SessionService;

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function index(): void
    {
        $user = $this->sessionService->current();
        if($user != null) {
            $model = [
                'title' => 'Login Management Home',
                'user' => $user->username
            ];
            View::render('Home/dashboard', $model);
            return;
        }else {
            View::redirect('/users/login',[
                'title' => 'Login Management Home'
            ]);
        }
    }
}