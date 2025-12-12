<?php
/**
 * Unit Test for Session Repository
 * 
 * 1. Create a SessionRepositoryTest class (tests/Repository/SessionRepositoryTest.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Tests\Repository;
 * 
 * use PHPUnit\Framework\TestCase;
 * use Mukhoiran\LoginManagement\Config\Database;
 * use Mukhoiran\LoginManagement\Domain\Session;
 * use Mukhoiran\LoginManagement\Domain\User;
 * use Mukhoiran\LoginManagement\Repository\SessionRepository;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * 
 * class SessionRepositoryTest extends TestCase
 * {
 *     private SessionRepository $sessionRepository;
 *     private UserRepository $userRepository;
 * 
 *     protected function setUp():void
 *     {
 *         $this->userRepository = new UserRepository(Database::getConnection());
 *         $this->sessionRepository = new SessionRepository(Database::getConnection());
 * 
 *         $this->sessionRepository->deleteAll();
 *         $this->userRepository->deleteAll();
 * 
 *         $user = new User();
 *         $user->username = "Anam";
 *         $user->password = "anam";
 *         $user->email = "anam@example.com";
 *         $this->userRepository->save($user);
 *     }
 * 
 *     public function testSaveSuccess()
 *     {
 *         $session = new Session();
 *         $session->session_token = uniqid();
 *         $session->username = "Anam";
 * 
 *         $this->sessionRepository->save($session);
 * 
 *         $result = $this->sessionRepository->findById($session->session_token);
 *         self::assertEquals($session->session_token, $result->session_token);
 *         self::assertEquals($session->username, $result->username);
 *     }
 * 
 *     public function testDeleteByIdSuccess()
 *     {
 *         $session = new Session();
 *         $session->session_token = uniqid();
 *         $session->username = "Anam";
 * 
 *         $this->sessionRepository->save($session);
 * 
 *         $result = $this->sessionRepository->findById($session->session_token);
 *         self::assertEquals($session->session_token, $result->session_token);
 *         self::assertEquals($session->username, $result->username);
 *     }
 * 
 *     public function testDeleteByIdSuccess()
 *     {
 *         $session = new Session();
 *         $session->session_token = uniqid();
 *         $session->username = "Anam";
 *     public function testFindByIdNotFound()
 *     {
 *         $result = $this->sessionRepository->findById('notfound');
 *         self::assertNull($result);
 *     }
 * }
 * 
 * ?>
 * 
 * 2. Run the test using PHPUnit to ensure all test cases pass.
 * vendor/bin/phpunit tests/Repository/SessionRepositoryTest.php
 * 
 */ 