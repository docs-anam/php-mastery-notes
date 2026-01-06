<?php
/**
 * Summary: PHP Global Variable $_SERVER
 *
 * The $_SERVER variable is a PHP superglobal array that contains information about headers, paths, and script locations.
 * It is automatically populated by the web server and PHP, and is accessible from anywhere in your script.
 *
 * Common $_SERVER Elements:
 * - $_SERVER['PHP_SELF']: The filename of the currently executing script.
 * - $_SERVER['SERVER_NAME']: The name of the server host under which the script is executing.
 * - $_SERVER['HTTP_HOST']: The Host header from the current request.
 * - $_SERVER['HTTP_USER_AGENT']: The user agent string sent by the browser.
 * - $_SERVER['SCRIPT_FILENAME']: The absolute pathname of the currently executing script.
 * - $_SERVER['REQUEST_METHOD']: The request method used to access the page (e.g., GET, POST).
 * - $_SERVER['QUERY_STRING']: The query string, if any, via which the page was accessed.
 * - $_SERVER['REMOTE_ADDR']: The IP address from which the user is viewing the current page.
 * - $_SERVER['SERVER_PORT']: The port on the server machine being used by the web server for communication.
 * - $_SERVER['REQUEST_URI']: The URI which was given in order to access the page.
 *
 * Usage Example:
 */
echo '<pre>';
print_r($_SERVER);
echo '</pre>';

/**
 * Security Note:
 * - Some $_SERVER values can be manipulated by the client (e.g., HTTP headers).
 * - Always validate and sanitize data from $_SERVER before using it in your application.
 */
?>