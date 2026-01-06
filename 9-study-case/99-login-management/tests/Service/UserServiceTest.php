<?php

namespace Mukhoiran\LoginManagement\Service;

use PHPUnit\Framework\TestCase;
use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\Domain\User;
use Mukhoiran\LoginManagement\Exception\ValidationException;
use Mukhoiran\LoginManagement\Model\UserRegisterRequest;
use Mukhoiran\LoginManagement\Repository\UserRepository;
use Mukhoiran\LoginManagement\Model\UserLoginRequest;
use Mukhoiran\LoginManagement\Model\UserProfileUpdateRequest;
use Mukhoiran\LoginManagement\Model\UserPasswordUpdateRequest;


class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->username = "Anam";
        $request->password = "Confidential";
        $request->email = "anam@example.com";

        $response = $this->userService->register($request);

        self::assertEquals($request->username, $response->user->username);
        self::assertNotEquals($request->password, $response->user->password);
        self::assertTrue(password_verify($request->password, $response->user->password));
        self::assertEquals($request->email, $response->user->email);
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->username = "";
        $request->password = "";
        $request->email = "";

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->username = "Anam";
        $user->password = "Confidential";
        $user->email = "anam@example.com";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->username = "Anam";
        $request->password = "Confidential";
        $request->email = "anam@example.com";

        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->username = "NotFound";
        $request->password = "NotFound";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->username = "Anam";
        $user->password = password_hash("Confidential", PASSWORD_BCRYPT);
        $user->email = "anam@example.com";
        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->username = "Anam";
        $request->password = "WrongPassword";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->username = "Anam";
        $user->password = password_hash("Confidential", PASSWORD_BCRYPT);
        $user->email = "anam@example.com";
        $this->userRepository->save($user);

        $request = new UserLoginRequest();
        $request->username = "Anam";
        $request->password = "Confidential";
        $response = $this->userService->login($request);
        self::assertEquals($user->username, $response->user->username);
        self::assertEquals($user->password, $response->user->password);
        self::assertEquals($user->email, $response->user->email);
    }

    public function testUpdateProfileSuccess()
    {
        // Siapkan data user awal
        $user = new User();
        $user->username = "Anam";
        $user->password = password_hash("Confidential", PASSWORD_BCRYPT);
        $user->email = "anam@example.com";
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->username = "Anam";
        $request->email = "newemail@example.com";

        $response = $this->userService->updateProfile($request);

        self::assertEquals($request->email, $response->user->email);
    }
    
    public function testUpdateProfileFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->username = "";
        $request->email = "invalidemail";

        $this->userService->updateProfile($request);
    }

    public function testUpdateProfileUserNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->username = "NonExistentUser";
        $request->email = "nonexistent@example.com";
        $this->userService->updateProfile($request);
    }

    public function testUpdatePasswordSuccess()
    {
        // Siapkan data user awal
        $user = new User();
        $user->username = "Anam";
        $user->password = password_hash("OldPassword", PASSWORD_BCRYPT);
        $user->email = "anam@example.com";
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->username = "Anam";
        $request->oldPassword = "OldPassword";
        $request->newPassword = "NewPassword";

        $response = $this->userService->updatePassword($request);

        self::assertTrue(password_verify("NewPassword", $response->user->password));
    }

    public function testUpdatePasswordFailedValidation()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->username = "Anam";
        $request->oldPassword = "";
        $request->newPassword = "NewPassword";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordUserNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->username = "NonExistentUser";
        $request->oldPassword = "OldPassword";
        $request->newPassword = "NewPassword";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordWrongOldPassword()
    {
        // Siapkan data user awal
        $user = new User();
        $user->username = "Anam";
        $user->password = password_hash("OldPassword", PASSWORD_BCRYPT);
        $user->email = "anam@example.com";
        $this->userRepository->save($user);
        $this->expectException(ValidationException::class);
        $request = new UserPasswordUpdateRequest();
        $request->username = "Anam";
        $request->oldPassword = "WrongOldPassword";
        $request->newPassword = "NewPassword";
        $this->userService->updatePassword($request);
    }
}