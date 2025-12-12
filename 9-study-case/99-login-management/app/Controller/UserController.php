<?php

namespace Mukhoiran\LoginManagement\Controller;

use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\App\View;
use Mukhoiran\LoginManagement\Exception\ValidationException;
use Mukhoiran\LoginManagement\Model\UserRegisterRequest;
use Mukhoiran\LoginManagement\Repository\UserRepository;
use Mukhoiran\LoginManagement\Service\UserService;
use Mukhoiran\LoginManagement\Model\UserLoginRequest;
use Mukhoiran\LoginManagement\Service\SessionService;
use Mukhoiran\LoginManagement\Repository\SessionRepository;
use Mukhoiran\LoginManagement\Model\UserProfileUpdateRequest;
use Mukhoiran\LoginManagement\Model\UserPasswordUpdateRequest;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register()
    {
        View::render('User/register', [
            'title' => 'Register new User'
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->username = $_POST['username'];
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function login()
    {
        View::render('User/login', [
            "title" => "Login user"
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->username = $_POST['username'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->username);
            // Handle successful login, e.g., redirect or set session
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                "title" => "Login user",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect('/');
    }

    public function updateProfile()
    {
        $user = $this->sessionService->current();

        View::render('User/profile', [
            'title' => 'Update User Profile',
            'user' => $user
        ]);
    }

    public function postUpdateProfile()
    {
        $user = $this->sessionService->current();

        $request = new UserProfileUpdateRequest();
        $request->username = $user->username;
        $request->email = $_POST['email'];

        try {
            $this->userService->updateProfile($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/profile', [
                'title' => 'Update User Profile',
                'user' => $user,
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function updatePassword()
    {
        $user = $this->sessionService->current();

        View::render('User/password', [
            'title' => 'Update User Password',
            'user' => $user
        ]);
    }

    public function postUpdatePassword()
    {
        $user = $this->sessionService->current();

        $request = new UserPasswordUpdateRequest();
        $request->username = $user->username;
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];

        try {
            $this->userService->updatePassword($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/password', [
                'title' => 'Update User Password',
                'user' => $user,
                'error' => $exception->getMessage()
            ]);
        }
    }
}