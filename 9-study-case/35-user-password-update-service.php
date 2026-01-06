<?php
/**
 * 
 * User Password Update Service
 * 
 * 1. Create UserPasswordUpdateRequest model (app/Model/UserPasswordUpdateRequest.php)
 *  
 * <?php
 * namespace Mukhoiran\LoginManagement\Model;
 * 
 * class UserPasswordUpdateRequest
 * {
 *     public ?string $username = null;
 *     public ?string $oldPassword = null;
 *     public ?string $newPassword = null;
 * }
 * ?>
 *  
 * 2. Create UserPasswordUpdateResponse model (app/Model/UserPasswordUpdateResponse.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Model;
 * 
 * use Mukhoiran\LoginManagement\Domain\User;
 * 
 * class UserPasswordUpdateResponse
 * {
 *     public ?User $user = null;
 * }
 * ?>
 * 
 * 3. Create UserService::updatePassword method (app/Service/UserService.php)
 * 
 * <?php
 * 
 * ...
 * use Mukhoiran\LoginManagement\Model\UserPasswordUpdateRequest;
 * use Mukhoiran\LoginManagement\Model\UserPasswordUpdateResponse;
 * ...
 * 
 *     public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
 *     {
 *         $this->validateUserPasswordUpdateRequest($request);
 * 
 *         try {
 *             Database::beginTransaction();
 * 
 *             $user = $this->userRepository->findById($request->username);
 *             if ($user == null) {
 *                 throw new ValidationException("User not found");
 *             }
 * 
 *             if (!password_verify($request->oldPassword, $user->password)) {
 *                 throw new ValidationException("Old password is incorrect");
 *             }
 * 
 *             $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
 *             $this->userRepository->update($user);
 * 
 *             Database::commitTransaction();
 * 
 *             $response = new UserPasswordUpdateResponse();
 *             $response->user = $user;
 *             return $response;
 *
 *         } catch (\Exception $exception) {
 *             Database::rollbackTransaction();
 *             throw $exception;
 *         }
 *     }
 * 
 *     private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
 *     {
 *         if ($request->username == null || $request->oldPassword == null || $request->newPassword == null ||
 *             trim($request->username) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == "") {
 *             throw new ValidationException("Username, Old Password, and New Password can not be empty");
 *         }
 *     }
 * ?>
 */