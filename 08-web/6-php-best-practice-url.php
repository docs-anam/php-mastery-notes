<?php
/**
 * PHP Best Practices: Generating URLs
 *
 * Generating URLs in PHP is a common task, especially in web applications.
 * Proper URL generation ensures maintainability, security, and flexibility.
 *
 * 1. Use Built-in Functions
 *    - Use functions like http_build_query() to build query strings safely.
 *    - Use urlencode() or rawurlencode() to encode URL components.
 *
 * 2. Avoid Hardcoding URLs
 *    - Use configuration files or constants for base URLs.
 *    - Example:
 *        define('BASE_URL', 'https://example.com/');
 *        $profileUrl = BASE_URL . 'user/profile.php?id=' . urlencode($userId);
 *
 * 3. Use Routing Libraries or Frameworks
 *    - Frameworks like Laravel, Symfony, or Slim provide helpers for URL generation.
 *    - Example (Laravel): route('profile', ['id' => $userId]);
 *
 * 4. Generate Query Strings Safely
 *    - Use http_build_query() to avoid manual concatenation.
 *    - Example:
 *        $params = ['id' => $userId, 'ref' => 'newsletter'];
 *        $url = BASE_URL . 'user/profile.php?' . http_build_query($params);
 *
 * 5. Handle HTTPS and HTTP Properly
 *    - Detect protocol dynamically if needed:
 *        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
 *        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/path";
 *
 * 6. Prevent XSS and Injection
 *    - Always escape URLs when outputting in HTML:
 *        <a href="<?php echo htmlspecialchars($url); ?>">Profile</a>
 *
 * 7. Use Relative URLs When Possible
 *    - Relative URLs are easier to maintain and work across environments.
 *
 * Example: Generating a URL with query parameters
 */
define('BASE_URL', 'https://example.com/');

function generateProfileUrl($userId, $ref = null) {
    $params = ['id' => $userId];
    if ($ref) {
        $params['ref'] = $ref;
    }
    return BASE_URL . 'user/profile.php?' . http_build_query($params);
}

// Usage
$userId = 123;
echo generateProfileUrl($userId, 'newsletter');
?>