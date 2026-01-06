<?php
/**
 * Summary: Session and Cookie Relation in PHP
 *
 * 1. Cookies:
 *    - Cookies are small pieces of data stored on the client's browser.
 *    - They are set using setcookie() in PHP and sent with every HTTP request to the server.
 *    - Cookies can store simple data like user preferences, authentication tokens, etc.
 *    - Example: setcookie("user", "John", time() + 3600);
 *
 * 2. Sessions:
 *    - Sessions store data on the server and associate it with a unique session ID.
 *    - The session ID is usually stored in a cookie (PHPSESSID by default) on the client.
 *    - Sessions are started with session_start() and data is stored in the $_SESSION superglobal.
 *    - Example:
 *        session_start();
 *        $_SESSION['user'] = 'John';
 *
 * 3. Relation between Sessions and Cookies:
 *    - When session_start() is called, PHP generates a session ID.
 *    - This session ID is sent to the client as a cookie (PHPSESSID).
 *    - On subsequent requests, the browser sends the session cookie back to the server.
 *    - The server uses the session ID from the cookie to retrieve the session data.
 *    - If cookies are disabled, session ID can be passed via URL (not recommended for security reasons).
 *
 * 4. Security Considerations:
 *    - Cookies can be manipulated by the client, so sensitive data should not be stored in cookies.
 *    - Sessions are more secure as data is stored on the server.
 *    - Always use secure, HTTP-only cookies for session IDs to prevent theft via XSS.
 *
 * 5. Practical Example:
 *    // Set a cookie
 *    setcookie("theme", "dark", time() + 3600);
 *
 *    // Start a session and store data
 *    session_start();
 *    $_SESSION['username'] = "JohnDoe";
 *
 *    // The session ID is stored in a cookie named PHPSESSID
 *
 * In summary, PHP sessions rely on cookies to maintain state between HTTP requests. Cookies store the session ID, while session data is securely stored on the server.
 */
?>