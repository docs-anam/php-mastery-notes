<?php
namespace Mukhoiran\LoginManagement\Tests\Service;

use PHPUnit\Framework\TestCase;
use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\Domain\Session;
use Mukhoiran\LoginManagement\Domain\User;
use Mukhoiran\LoginManagement\Repository\SessionRepository;
use Mukhoiran\LoginManagement\Repository\UserRepository;

class SessionServiceTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->username = "Anam";
        $user->password = "anam";
        $user->email = "anam@example.com";
        $this->userRepository->save($user);
    }

    public function testCreateSessionSuccess()
    {
        $session = new Session();
        $session->session_token = uniqid();
        $session->username = "Anam";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->session_token);
        self::assertEquals($session->session_token, $result->session_token);
        self::assertEquals($session->username, $result->username);
    }

    public function testDestroySessionSuccess()
    {
        $session = new Session();
        $session->session_token = uniqid();
        $session->username = "Anam";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->session_token);
        self::assertEquals($session->session_token, $result->session_token);
        self::assertEquals($session->username, $result->username);

        $this->sessionRepository->deleteById($session->session_token);

        $result = $this->sessionRepository->findById($session->session_token);
        self::assertNull($result);
    }

    public function testGetSessionNotFound()
    {
        $result = $this->sessionRepository->findById("not-found");
        self::assertNull($result);
    }
}

