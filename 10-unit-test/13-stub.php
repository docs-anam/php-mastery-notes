<?php
/**
 * Stub in Unit Testing (PHP) - Comprehensive Detailed Summary
 *
 * What is a Stub?
 * ----------------
 * A stub is a type of test double used in unit testing to simulate the behavior of real objects. Stubs provide predefined
 * responses to method calls made during the test, allowing you to isolate the code under test from its dependencies.
 * This is especially useful when those dependencies are complex, slow, unreliable, or have side effects (such as database
 * access, network calls, file I/O, etc.).
 *
 * Why Use Stubs?
 * --------------
 * - Isolation: Stubs help isolate the unit of code being tested, ensuring that tests are not affected by external factors.
 * - Control: They allow you to control the inputs and outputs of dependencies, making it easier to test specific scenarios.
 * - Speed: Tests run faster because stubs avoid slow operations like network or database access.
 * - Reliability: Tests become more reliable and repeatable since stubs always return the same predefined data.
 * - Edge Cases: Stubs make it easy to simulate error conditions, exceptions, or unusual data that may be hard to reproduce.
 *
 * Typical Usage Scenarios
 * -----------------------
 * - When the code under test depends on external services (APIs, databases, third-party libraries).
 * - When you want to simulate specific responses (success, failure, exceptions).
 * - When you need to test how your code handles edge cases or error conditions.
 * - When the real dependency is unavailable or difficult to set up in the test environment.
 *
 * How to Create Stubs in PHP (with PHPUnit)
 * -----------------------------------------
 * PHPUnit provides built-in methods for creating stubs:
 * - `createStub(ClassName::class)`: Creates a stub for the given class.
 *
 * You can specify which methods to stub and what values they should return:
 *
 * Example:
 * --------
 * Suppose you have a class `UserRepository` that fetches user data from a database.
 * In your test, you can stub the repository to return a fake user object:
 *
 *     $userRepoStub = $this->createStub(UserRepository::class);
 *     $userRepoStub->method('findUserById')->willReturn(new User('John Doe'));
 *
 *     $service = new UserService($userRepoStub);
 *     $result = $service->getUserName(1);
 *     $this->assertEquals('John Doe', $result);
 *
 * You can also stub multiple methods, return different values based on input, or throw exceptions:
 *
 *     $userRepoStub->method('findUserById')
 *         ->will($this->returnCallback(function($id) {
 *             if ($id === 1) return new User('John Doe');
 *             throw new Exception('User not found');
 *         }));
 *
 * Best Practices for Using Stubs
 * ------------------------------
 * - Only stub methods that are necessary for the test scenario.
 * - Keep stubs simple and predictable; avoid complex logic in stubs.
 * - Use descriptive names for stubbed objects to make tests readable.
 * - Avoid overusing stubs, as excessive stubbing can make tests harder to maintain.
 * - Prefer stubs over real dependencies for fast, reliable, and isolated unit tests.
 *
 * Summary
 * -------
 * Stubs are a fundamental tool in unit testing for PHP. They allow you to replace real dependencies with controlled,
 * predictable substitutes, making your tests faster, more reliable, and focused on the logic you want to verify.
 * By using stubs effectively, you can ensure your unit tests are robust and maintainable.
 */
?>