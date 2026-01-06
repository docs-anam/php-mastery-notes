<?php
/**
 * XSS (Cross-Site Scripting) in PHP - Detailed Summary with Examples
 *
 * What is XSS?
 * ------------
 * XSS is a security vulnerability that allows attackers to inject malicious scripts (usually JavaScript)
 * into web pages viewed by other users. This can lead to session hijacking, data theft, defacement,
 * phishing, and other malicious actions.
 *
 * Types of XSS:
 * -------------
 * 1. Stored XSS:
 *    - The malicious script is permanently stored on the server (e.g., in a database, comment, or forum post).
 *    - When other users view the affected page, the script executes in their browsers.
 *
 * 2. Reflected XSS:
 *    - The malicious script is reflected off the web server, typically via URL or form input.
 *    - The script is part of the request and immediately echoed in the response.
 *
 * 3. DOM-based XSS:
 *    - The vulnerability exists in client-side code (JavaScript), where the DOM is manipulated with untrusted data.
 *    - Example: Reading from window.location and inserting it into the page without sanitization.
 *
 * How XSS Works:
 * --------------
 * - An attacker finds a way to inject JavaScript (or other scripts) into a web page.
 * - When another user visits the page, the script executes in their browser as if it was part of the legitimate site.
 * - The script can steal cookies, session tokens, or perform actions on behalf of the user.
 *
 * Example of Vulnerable PHP Code (Reflected XSS):
 * -----------------------------------------------
 * Save this as xss-demo.php and access it via:
 *   http://localhost/php-mastery-notes/5-web/11-xss-cross-site-scripting.php?name=<script>alert('XSS')</script>
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>XSS Demo</title>
</head>
<body>
    <h2>Vulnerable Example (Do NOT use in production!)</h2>
    <?php
    // VULNERABLE: Directly outputs user input without sanitization
    if (isset($_GET['name'])) {
        echo "Welcome, " . $_GET['name'];
    }
    ?>
    <hr>
    <h2>Secure Example</h2>
    <?php
    // SECURE: Escapes output using htmlspecialchars()
    if (isset($_GET['name'])) {
        echo "Welcome, " . htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8');
    }
    ?>
</body>
</html>
<?php
/**
 * Prevention Techniques:
 * ----------------------
 * 1. Output Encoding:
 *    - Always escape output using htmlspecialchars() or htmlentities().
 *    - Example: htmlspecialchars($input, ENT_QUOTES, 'UTF-8')
 *
 * 2. Use Prepared Statements for Database Queries:
 *    - Prevents stored XSS via SQL injection.
 *
 * 3. Validate and Sanitize Input:
 *    - Use filter_input(), filter_var(), or custom validation.
 *
 * 4. Set HTTP Headers:
 *    - Content-Security-Policy (CSP): Restricts sources of scripts.
 *    - X-XSS-Protection: Enables browser XSS filters (deprecated in modern browsers).
 *
 * 5. Avoid Inline JavaScript:
 *    - Do not use user input inside <script> tags or event handlers.
 *
 * 6. Use Frameworks:
 *    - Modern PHP frameworks (Laravel, Symfony, etc.) have built-in protections.
 *
 * Summary:
 * --------
 * XSS is a critical vulnerability in PHP applications. Always treat user input as untrusted,
 * encode output, and use secure coding practices to prevent XSS attacks.
 */
?>