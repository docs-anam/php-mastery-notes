<?php
/**
 * 
 * Unit Test for User Profile Update Repository
 * 
 * 1. Update UserRepositoryTest to test update method (tests/Repository/UserRepositoryTest.php)
 * <?php
 * ...
 * use Mukhoiran\LoginManagement\Repository\SessionRepository;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * 
 * class UserRepositoryTest extends TestCase
 * {
 *      private UserRepository $userRepository;
 *      private SessionRepository $sessionRepository;
 *      
 *      protected function setUp(): void
 *      {
 *          $this->userRepository = new UserRepository(Database::getConnection());
 *          $this->sessionRepository = new SessionRepository(Database::getConnection());
 *          $this->userRepository->deleteAll();
 *          $this->sessionRepository->deleteAll();
 *      }
 *   ...
 *      public function testUpdateSuccess()
 *      {
 *          $user = new User();
 *          $user->username = "Anam";
 *          $user->password = "Confidential";
 *          $user->email = "anam@example.com";
 *          $this->userRepository->save($user);
 * 
 *          $user->password = "NewPassword";
 *          $user->email = "newanam@example.com";
 *          $this->userRepository->update($user);
 * 
 *          $result = $this->userRepository->findById($user->username);
 * 
 *          self::assertEquals($user->username, $result->username);
 *          self::assertEquals($user->password, $result->password);
 *          self::assertEquals($user->email, $result->email);
 *      }
 * ?>
 * 
 * 2. Run the test to ensure the update method works correctly
 * vendor/bin/phpunit tests/Repository/UserRepositoryTest.php
 * 
 */ 