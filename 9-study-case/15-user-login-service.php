<?php
/**
 * User Login Service
 * 
 * 1. Create the UserLoginRequest models (app/Model/UserLoginRequest.php) * 
 *  
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Model;
 * class UserLoginRequest
 * {
 *     public ?string $username = null;
 *     public ?string $password = null;
 * }
 * 
 * ?>
 * 
 * 2. Create the UserLoginResponse models (app/Model/UserLoginResponse.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Model;
 * use Mukhoiran\LoginManagement\Domain\User;
 * 
 * class UserLoginResponse
 * {
 *     public ?User $user = null;
 * }
 * ?>
 * 
 * 3. Update the UserService to add the login method (app/Service/UserService.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Service;
 * use Mukhoiran\LoginManagement\Model\UserLoginRequest;
 * use Mukhoiran\LoginManagement\Model\UserLoginResponse;
 * ...
 * public function login(UserLoginRequest $request): UserLoginResponse
 * {
 *     $this->validateUserLoginRequest($request);
 *     $user = $this->userRepository->findById($request->username);
 *     if ($user == null || !password_verify($request->password, $user->password)) {
 *         throw new ValidationException("Username or Password is incorrect");
 *     }
 *    $response = new UserLoginResponse();
 *    $response->user = $user;
 *   return $response;
 * }
 * 
 * private function validateUserLoginRequest(UserLoginRequest $request)
 * {
 *    if ($request->username == null || $request->password == null ||
 *       trim($request->username) == "" || trim($request->password) == "") {
 *       throw new ValidationException("Username and Password can not be empty");
 *   }
 * }
 * ?>
 */