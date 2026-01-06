<?php
/**
 * PHP Header Function - Detailed Summary and Executable Examples
 *
 * The `header()` function in PHP is used to send raw HTTP headers to the client before any output is sent.
 * HTTP headers control how browsers and clients interpret the response.
 *
 * Common Uses:
 * 1. Redirecting Users
 * 2. Setting Content-Type
 * 3. Controlling Caching
 * 4. Forcing File Downloads
 * 5. Custom Headers
 * 6. Removing Headers
 *
 * Important Notes:
 * - Call `header()` before any output (no echo, print, HTML, or whitespace before it).
 * - Use `exit;` after redirects to stop further script execution.
 * - Multiple headers can be sent by calling `header()` multiple times.
 * - Use `header_remove()` to remove a previously set header.
 *
 * --------------------------
 * 1. Redirecting Users
 * --------------------------
 * Redirect to another page:
 */
if (isset($_GET['redirect'])) {
    header('Location: https://www.example.com');
    exit;
}

/**
 * --------------------------
 * 2. Setting Content-Type
 * --------------------------
 * Set response as JSON:
 */
if (isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'This is JSON']);
    exit;
}

/**
 * Set response as plain text:
 */
if (isset($_GET['text'])) {
    header('Content-Type: text/plain');
    echo "This is plain text output.";
    exit;
}

/**
 * --------------------------
 * 3. Controlling Caching
 * --------------------------
 * Prevent browser caching:
 */
if (isset($_GET['nocache'])) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    echo "Caching is disabled for this response.";
    exit;
}

/**
 * --------------------------
 * 4. Forcing File Downloads
 * --------------------------
 * Force download of a text file:
 */
if (isset($_GET['download'])) {
    $filename = "example.txt";
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen("Download this content!"));
    echo "Download this content!";
    exit;
}

/**
 * --------------------------
 * 5. Custom Headers
 * --------------------------
 * Add a custom header:
 */
if (isset($_GET['custom'])) {
    header('X-Custom-Header: MyValue');
    echo "Custom header sent. Check response headers.";
    exit;
}

/**
 * --------------------------
 * 6. Removing Headers
 * --------------------------
 * Remove a previously set header:
 */
if (isset($_GET['remove'])) {
    header('X-Remove-Me: ToBeRemoved');
    header_remove('X-Remove-Me');
    echo "Header 'X-Remove-Me' was set and then removed.";
    exit;
}

/**
 * --------------------------
 * 7. Getting Headers
 * --------------------------
 * read HTTP headers sent by the client (browser), use the `getallheaders()` function (works in Apache, FastCGI, etc):
 */
if (isset($_GET['showheaders'])) {
    $headers = getallheaders();
    echo "<pre>";
    print_r($headers);
    echo "</pre>";
    exit;
}

/**
 * Reference: https://www.php.net/manual/en/function.header.php
 */
?>
<!--
Try accessing this script with different query parameters:
?redirect
?json
?text
?nocache
?download
?custom
?remove