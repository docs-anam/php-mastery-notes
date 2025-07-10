<?php
/*
Detailed Summary: Cookies in PHP Web Development

1. What is a Cookie?
- A cookie is a small piece of data (name-value pair) that a server sends to the user's browser.
- The browser stores the cookie and sends it back with subsequent requests to the same server.
- Cookies are commonly used for session management, personalization, and tracking.

2. Setting a Cookie
- Use setcookie() before any output is sent to the browser.
- Syntax: setcookie(name, value, expire, path, domain, secure, httponly);

Example: Set a cookie named "user" with value "John" that expires in 1 hour.
*/
setcookie("user", "John", time() + 3600, "/"); // Expires in 1 hour

/*
3. Accessing a Cookie
- Use the $_COOKIE superglobal array to read cookies sent by the browser.
- Note: Newly set cookies are available on the next request.

Example: Access the "user" cookie.
*/
if (isset($_COOKIE["user"])) {
    echo "User is: " . htmlspecialchars($_COOKIE["user"]);
} else {
    echo "User cookie is not set.";
}

/*
4. Deleting a Cookie
- To delete a cookie, set its expiration date to a time in the past.

Example: Delete the "user" cookie.
*/
setcookie("user", "", time() - 3600, "/"); // Deletes the cookie

/*
5. Cookie Parameters Explained
- name: (string) The cookie’s name.
- value: (string) The value to store.
- expire: (int) Expiration time as a Unix timestamp. If omitted or 0, the cookie expires at end of session.
- path: (string) Path on the server where the cookie is available (default "/").
- domain: (string) (Optional) Domain the cookie is available to.
- secure: (bool) (Optional) If true, cookie sent only over HTTPS.
- httponly: (bool) (Optional) If true, cookie accessible only via HTTP(S), not JavaScript.

Example: Set a secure, HTTP-only cookie.
*/
setcookie("session_id", "abc123", time() + 3600, "/", "", true, true);

/*
6. Security Considerations
- Do not store sensitive data (like passwords) in cookies.
- Use 'secure' flag to ensure cookies are sent only over HTTPS.
- Use 'httponly' flag to prevent JavaScript access (mitigates XSS attacks).
- Always validate and sanitize cookie data before using it.

Example: Validate cookie data.
*/
if (isset($_COOKIE["user_id"])) {
    $user_id = intval($_COOKIE["user_id"]); // Convert to integer for safety
    // Use $user_id in your application
}

/*
7. Limitations
- Size limit: Each cookie is limited to about 4KB.
- Number of cookies: Browsers limit the number of cookies per domain (usually 20-50).
- Users can disable cookies in their browsers, so always check if cookies are enabled.

8. Practical Example: Set, Read, and Delete a Cookie
*/
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'set') {
        setcookie("demo", "HelloCookie", time() + 600, "/");
        echo "Cookie 'demo' set.<br>";
    } elseif ($_GET['action'] === 'delete') {
        setcookie("demo", "", time() - 3600, "/");
        echo "Cookie 'demo' deleted.<br>";
    }
}
if (isset($_COOKIE["demo"])) {
    echo "Demo cookie value: " . htmlspecialchars($_COOKIE["demo"]);
} else {
    echo "Demo cookie is not set.";
}

/*
References:
- https://www.php.net/manual/en/function.setcookie.php
- https://www.php.net/manual/en/reserved.variables.cookies.php
*/
?>