<?php
/**
 * Session Service
 * 
 * 1. Create a SessionService class (app/Service/SessionService.php)
 * 
 * <?php
 * 
 * namespace Mukhoiran\LoginManagement\Repository;
 * 
 * use Mukhoiran\LoginManagement\Domain\Session;
 * use Mukhoiran\LoginManagement\Domain\User;
 * use Mukhoiran\LoginManagement\Repository\SessionRepository;
 * use Mukhoiran\LoginManagement\Repository\UserRepository;
 * 
 * class SessionService
 * {
 *
 *     public static string $COOKIE_NAME = "X-MUKHOIRAN-SESSION";
 *     private SessionRepository $sessionRepository;
 *     private UserRepository $userRepository;
 * 
 *     public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
 *     {
 *         $this->sessionRepository = $sessionRepository;
 *         $this->userRepository = $userRepository;
 *     }
 * 
 *     public function create(string $username): Session
 *     {
 *         $session = new Session();
 *         $session->session_token = uniqid();
 *         $session->username = $username;
 *         
 *         $this->sessionRepository->save($session);
 *
 *         setcookie(self::$COOKIE_NAME, $session->session_token, time() + (60 * 60 * 24 * 30), "/");
 *         return $session;
 *     }
 * 
 *     public function destroy()
 *     {
 *         $sessionToken = $_COOKIE[self::$COOKIE_NAME] ?? '';
 *         $this->sessionRepository->deleteById($sessionToken);
 * 
 *         setcookie(self::$COOKIE_NAME, '', 1, "/");
 *     }
 * 
 *     public function current(): ?User
 *     {
 *         $sessionToken = $_COOKIE[self::$COOKIE_NAME] ?? '';
 * 
 *         $session = $this->sessionRepository->findById($sessionToken);
 *         if($session == null){
 *             return null;
 *         }
 * 
 *         return $this->userRepository->findById($session->username);
 *     }
 * 
 * }
 * 
 */