<?php
/**
 * PHP HTTP Response Codes - Comprehensive Summary
 *
 * HTTP response codes (status codes) are three-digit numbers sent by the server to the client
 * as part of the HTTP response header. They inform the client about the result of its request.
 *
 * Categories of HTTP Response Codes:
 *  - 1xx (Informational): Request received, continuing process.
 *  - 2xx (Success): The request was successfully received, understood, and accepted.
 *  - 3xx (Redirection): Further action needs to be taken to complete the request.
 *  - 4xx (Client Error): The request contains bad syntax or cannot be fulfilled.
 *  - 5xx (Server Error): The server failed to fulfill a valid request.
 *
 * Common HTTP Response Codes:
 *  - 200 OK: Standard response for successful HTTP requests.
 *  - 201 Created: The request has been fulfilled and resulted in a new resource being created.
 *  - 204 No Content: The server successfully processed the request, but is not returning any content.
 *  - 301 Moved Permanently: The resource has been permanently moved to a new URL.
 *  - 302 Found: The resource resides temporarily under a different URL.
 *  - 304 Not Modified: The resource has not been modified since the last request.
 *  - 400 Bad Request: The server cannot process the request due to client error.
 *  - 401 Unauthorized: Authentication is required and has failed or not been provided.
 *  - 403 Forbidden: The server understood the request but refuses to authorize it.
 *  - 404 Not Found: The requested resource could not be found.
 *  - 405 Method Not Allowed: The request method is not supported for the requested resource.
 *  - 500 Internal Server Error: A generic error message for unexpected server conditions.
 *  - 502 Bad Gateway: The server received an invalid response from the upstream server.
 *  - 503 Service Unavailable: The server is currently unavailable (overloaded or down).
 *
 * Setting HTTP Response Codes in PHP:
 * 1. Using header():
 *    - You can set the HTTP status code by sending a specific header before any output.
 *    - Example: header("HTTP/1.1 404 Not Found");
 *    - You can also use the shorthand: header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
 *
 * 2. Using http_response_code() (PHP 5.4+):
 *    - Sets or gets the HTTP response code.
 *    - Example: http_response_code(404);
 *    - To get the current code: $code = http_response_code();
 *
 * 3. Custom Error Pages:
 *    - You can display custom error messages or pages based on the response code.
 *    - Example: if ($code == 404) { include '404.php'; }
 *
 * 4. Redirects:
 *    - For 3xx codes, you can redirect users to another URL.
 *    - Example: header("Location: https://example.com/new-url", true, 301);
 *
 * Best Practices:
 *  - Always set the response code before any output (HTML, echo, etc.).
 *  - Use the correct status code to accurately reflect the result of the request.
 *  - For APIs, response codes are critical for client-side error handling.
 *  - For SEO, proper use of 301/302/404/410 is important.
 *
 * Examples:
 */

// Set a 404 Not Found response using header()
header("HTTP/1.1 404 Not Found");

// Set a 404 Not Found response using http_response_code()
http_response_code(404);

// Get the current response code
$currentCode = http_response_code(); // returns 404

// Redirect with 301 Moved Permanently
// header("Location: https://example.com/new-url", true, 301);

// Custom error page example
if ($currentCode == 404) {
    // include '404.php';
    // echo "Custom 404 error page content";
}

/**
 * Notes:
 * - Headers must be sent before any output.
 * - Some web servers may override PHP's status code if configured.
 * - For REST APIs, use appropriate codes for each endpoint and error condition.
 */
?>