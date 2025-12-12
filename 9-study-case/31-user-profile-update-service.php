<?php
/**
 * 
 * User Profile Update Request and Response Models
 * 
 * 1. Create UserProfileUpdateRequest model (app/Model/UserProfileUpdateRequest.php)
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Model;
 * 
 * class UserProfileUpdateRequest
 * {
 *      public ?string $username = null;
 *      public ?string $email = null;
 * }
 * 
 * ?>
 * 
 * 2. Create UserProfileUpdateResponse model (app/Model/UserProfileUpdateResponse.php)
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Model;
 * 
 * use Mukhoiran\LoginManagement\Domain\User;
 * 
 * class UserProfileUpdateResponse
 * {
 *      public ?User $user = null;
 * }
 * 
 * ?>
 * 
 * 3. Update UserService to handle profile update (app/Service/UserService.php)
 * 
 * <?php
 * 
 * ...
 *     public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
 *     {
 *         $this->validateUserProfileUpdateRequest($request);
 * 
 *         try {
 *              Database::beginTransaction();
 * 
 *             $user = $this->userRepository->findById($request->username);
 *             if ($user == null) {
 *                 throw new ValidationException("User not found");
 *             }
 * 
 *             $user->email = $request->email;
 *             $this->userRepository->update($user);
 *
 *             Database::commitTransaction();
 *
 *             $response = new UserProfileUpdateResponse();
 *             $response->user = $user;
 *             return $response;
 *
 *        } catch (\Exception $exception) {
 *            Database::rollbackTransaction();
 *            throw $exception;
 *        }
 *     }
 * 
 *     private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
 *     {
 *         if ($request->username == null || $request->email == null ||
 *             trim($request->username) == "" || trim($request->email) == "") {
 *             throw new ValidationException("Username and Email can not be empty");
 *         }
 *     }
 * 
 */