<?php
/**
 * 
 * User Repository with Update Method
 * 
 * 1. Add update method to UserRepository (app/Repository/UserRepository.php)
 * <?php
 * ...
 *      public function update(User $user): User
 *     {
 *         $statement = $this->connection->prepare("UPDATE users SET password = ?, email = ? WHERE username = ?");
 *         $statement->execute([
 *             $user->password, $user->email, $user->username
 *         ]);
 *         return $user;
 *     }
 * ...
 * ?>
 */