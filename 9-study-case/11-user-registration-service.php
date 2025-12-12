<?php
/*
 * User Registration Service
 *
 * This service handles user registration functionality.
 * It validates the registration request, checks for existing users,
 * hashes passwords, and saves new users to the repository.
 * It also manages database transactions to ensure data integrity.
 *
 * 1. Create an Exception class for ValidationException to handle validation errors (Exception/ValidationException.php).
 *
 * <?php
 * namespace Mukhoiran\LoginManagement\Exception;
 * 
 * class ValidationException extends \Exception
 * {
 *
 * }
 * 
 * 2. Create a Model class for UserRegisterRequest to structure the registration request data (Model/UserRegisterRequest.php).
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Model;
 * 
 * class UserRegisterRequest
 * {
 *     public ?string $username = null;
 *     public ?string $password = null;
 *     public ?string $email = null;
 * }
 *
 * 3. Create a Model class for UserRegisterResponse to structure the response data (Model/UserRegisterResponse.php).
 *
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Model;
 * 
 * use Mukhoiran\LoginManagement\Domain\User;
 * 
 * class UserRegisterResponse
 * {
 *     public User $user;
 * }
 *
 * 4. Implement the UserService class to handle the registration logic (Service/UserService.php).
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Service;
 * 
 * use Mukhoiran\LoginManagement\Model\UserRegisterRequest;
 * use Mukhoiran\LoginManagement\Model\UserRegisterResponse;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * use Mukhoiran\LoginManagement\Exception\ValidationException;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Domain\User;
 * 
 * class UserService
 * {
 *     private UserRepository $userRepository;
 * 
 *     public function __construct(UserRepository $userRepository)
 *     {
 *         $this->userRepository = $userRepository;
 *     }
 * 
 *     public function register(UserRegisterRequest $request): UserRegisterResponse
 *     {
 *         $this->validateUserRegistrationRequest($request);
 *
 *         try {
 *             Database::beginTransaction();
 *             $user = $this->userRepository->findById($request->username);
 *             if ($user != null) {
 *                 throw new ValidationException("Username already exists");
 *             }
 *
 *             $user = new User();
 *             $user->username = $request->username;
 *             $user->password = password_hash($request->password, PASSWORD_BCRYPT);
 *             $user->email = $request->email;
 *
 *             $this->userRepository->save($user);
 *
 *             $response = new UserRegisterResponse();
 *             $response->user = $user;
 *
 *             Database::commitTransaction();
 *             return $response;
 *         } catch (\Exception $exception) {
 *             Database::rollbackTransaction();
 *             throw $exception;
 *         }
 *     }
 * 
 *     private function validateUserRegistrationRequest(UserRegisterRequest $request)
 *     {
 *         if ($request->username == null || $request->password == null || $request->email == null ||
 *             trim($request->username) == "" || trim($request->password) == "" || trim($request->email) == "") {
 *             throw new ValidationException("Username, Password, Email can not be empty");
 *         }
 *     }
 * }
 *
 */