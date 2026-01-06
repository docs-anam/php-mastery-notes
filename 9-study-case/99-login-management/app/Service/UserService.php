<?php

namespace Mukhoiran\LoginManagement\Service;

use Mukhoiran\LoginManagement\Model\UserRegisterRequest;
use Mukhoiran\LoginManagement\Model\UserRegisterResponse;
use Mukhoiran\LoginManagement\Model\UserLoginRequest;
use Mukhoiran\LoginManagement\Model\UserLoginResponse;
use Mukhoiran\LoginManagement\Model\UserProfileUpdateRequest;
use Mukhoiran\LoginManagement\Model\UserProfileUpdateResponse;
use Mukhoiran\LoginManagement\Model\UserPasswordUpdateRequest;
use Mukhoiran\LoginManagement\Model\UserPasswordUpdateResponse;
use Mukhoiran\LoginManagement\Repository\UserRepository;
use Mukhoiran\LoginManagement\Exception\ValidationException;
use Mukhoiran\LoginManagement\Config\Database;
use Mukhoiran\LoginManagement\Domain\User;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->username);
            if ($user != null) {
                throw new ValidationException("Username already exists");
            }

            $user = new User();
            $user->username = $request->username;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->email = $request->email;

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->username == null || $request->password == null || $request->email == null ||
            trim($request->username) == "" || trim($request->password) == "" || trim($request->email) == "") {
            throw new ValidationException("Username, Password, Email can not be empty");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->username);
        if ($user == null) {
            throw new ValidationException("Username or password is incorrect");
        }

        if (!password_verify($request->password, $user->password)) {
            throw new ValidationException("Username or password is incorrect");
        }

        $response = new UserLoginResponse();
        $response->user = $user;
        return $response;
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->username == null || $request->password == null ||
            trim($request->username) == "" || trim($request->password) == "") {
            throw new ValidationException("Username and Password can not be empty");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->username);
            if ($user == null) {
                throw new ValidationException("User not found");
            }

            $user->email = $request->email;
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;

        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->username == null || $request->email == null ||
            trim($request->username) == "" || trim($request->email) == "") {
            throw new ValidationException("Username and Email can not be empty");
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->username);
            if ($user == null) {
                throw new ValidationException("User not found");
            }

            if (!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException("Old password is incorrect");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;

        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if ($request->username == null || $request->oldPassword == null || $request->newPassword == null ||
            trim($request->username) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == "") {
            throw new ValidationException("Username, Old Password, and New Password can not be empty");
        }
    }

}