<?php

namespace Mukhoiran\LoginManagement\Middleware;

use Mukhoiran\LoginManagement\App\View;
use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\Repository\SessionRepository;
use Mukhoiran\LoginManagement\Repository\UserRepository;
use Mukhoiran\LoginManagement\Service\SessionService;

class MustLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before(): void
    {
        $session = $this->sessionService->current();
        if ($session == null) {
            View::redirect('/users/login');
        }
    }
}