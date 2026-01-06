<?php

namespace Mukhoiran\LoginManagement\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\Domain\User;
use Mukhoiran\LoginManagement\Repository\SessionRepository;
use Mukhoiran\LoginManagement\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{

    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        
        $this->userRepository->deleteAll();
        $this->sessionRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->username = "Anam";
        $user->password = "Confidential";
        $user->email = "anam@example.com";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->username);

        self::assertEquals($user->username, $result->username);
        self::assertEquals($user->password, $result->password);
        self::assertEquals($user->email, $result->email);
    }

    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById("notfound");
        self::assertNull($user);
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->username = "Anam";
        $user->password = "Confidential";
        $user->email = "anam@example.com";

        $this->userRepository->save($user);

        $user->password = "NewPassword";
        $user->email = "newanam@example.com";

        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->username);

        self::assertEquals($user->username, $result->username);
        self::assertEquals($user->password, $result->password);
        self::assertEquals($user->email, $result->email);
    }
}