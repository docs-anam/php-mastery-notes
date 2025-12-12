<?php
/**
 * User Registration Repository Unit Test
 * 
 * 1. Create a unit test for the UserRepository to ensure user registration functionality works as expected.
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Tests\Repository;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Domain\User;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * 
 * class UserRepositoryTest extends TestCase
 * {
 *     private UserRepository $userRepository;
 *     protected function setUp(): void
 *     {
 *         $this->userRepository = new UserRepository(Database::getConnection());
 *         $this->userRepository->deleteAll();
 *     }
 * 
 *     public function testSaveSuccess()                    
 *     {
 *         $user = new User();
 *         $user->username = "Anam";
 *         $user->password = "Confidential";
 *         $user->email = "anam@example.com";
 *
 *         $this->userRepository->save($user);
 *
 *         $result = $this->userRepository->findById($user->username);
 *
 *         self::assertEquals($user->username, $result->username);
 *         self::assertEquals($user->password, $result->password);
 *         self::assertEquals($user->email, $result->email);
 *     }
 * 
 *    public function testFindByIdNotFound()
 *    {
 *        $user = $this->userRepository->findById("notfound");
 *        self::assertNull($user);
 *    }
 * }
 * 2. Run the tests using PHPUnit.
 *   vendor/bin/phpunit tests/Repository/UserRepositoryTest.php
 * 
 * 3. Verify that the tests pass, confirming the user registration repository works correctly.
 * This ensures that the user registration functionality is reliable and ready for use in the login management system.
 */