<?php
/**
 * PHP Session Detailed Summary
 *
 * 1. What is a Session?
 *    - A session allows you to store user information on the server for use across multiple pages.
 *    - Session data is not accessible to the user and is more secure than cookies.
 *
 * 2. Starting a Session
 *    - Call session_start() at the very top of your script (before any output).
 *    - This function creates or resumes a session using a session ID (usually stored in a cookie).
 *
 *    Example:
 *    session_start();
 *
 * 3. Storing and Accessing Session Data
 *    - Use the $_SESSION superglobal array.
 *    - You can store any serializable data (strings, arrays, objects).
 *
 *    Example:
 *    $_SESSION['username'] = 'john';
 *    echo $_SESSION['username'];
 *
 * 4. Session ID
 *    - Each session is identified by a unique session ID (PHPSESSID by default).
 *    - The session ID is sent to the client as a cookie.
 *
 * 5. Destroying a Session
 *    - Remove all session variables: session_unset();
 *    - Destroy the session: session_destroy();
 *    - To log out a user, unset session variables and destroy the session.
 *
 *    Example:
 *    session_unset();
 *    session_destroy();
 *
 * 6. Security Considerations
 *    - Regenerate session ID after login: session_regenerate_id(true);
 *    - Use HTTPS to protect session cookies.
 *    - Set cookie parameters (httponly, secure, samesite).
 *
 *    Example:
 *    session_set_cookie_params(['httponly' => true, 'secure' => true, 'samesite' => 'Strict']);
 *
 * 7. Session Configuration
 *    - Configure session behavior in php.ini (e.g., session.gc_maxlifetime, session.save_path).
 *
 * 8. Use Cases
 *    - User authentication, shopping carts, storing user preferences, etc.
 *
 * 9. Example Workflow
 *    session_start();
 *    if (!isset($_SESSION['count'])) {
 *        $_SESSION['count'] = 1;
 *    } else {
 *        $_SESSION['count']++;
 *    }
 *    echo "You have visited this page " . $_SESSION['count'] . " times.";
 *
 * 10. Login/Logout Example with Session
 *     - Below is a simple login/logout system using sessions.
 */

// login.php
session_start();
$users = [
    'admin' => 'password123',
    'user' => 'userpass'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (isset($users[$username]) && $users[$username] === $password) {
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!-- login.php HTML -->
<form method="post">
    Username: <input name="username"><br>
    Password: <input name="password" type="password"><br>
    <button type="submit">Login</button>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
</form>

<?php
// dashboard.php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
echo "Welcome, " . htmlspecialchars($_SESSION['username']) . "!<br>";
echo "<a href='logout.php'>Logout</a>";

// logout.php
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit;
?>