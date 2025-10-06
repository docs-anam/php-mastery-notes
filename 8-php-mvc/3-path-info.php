<?php
/**
 * Summary: PATH_INFO in PHP MVC
 *
 * 1. What is PATH_INFO?
 *    - PATH_INFO is a server variable containing extra path information provided by the client after the script name in the URL.
 *    - Example URL: http://example.com/index.php/user/profile
 *      - "/user/profile" is the PATH_INFO.
 *
 * 2. How is PATH_INFO used in PHP MVC?
 *    - In MVC frameworks, PATH_INFO helps route requests to controllers and actions.
 *    - Enables "pretty URLs" without query strings, e.g., /controller/action/param.
 *
 * 3. Accessing PATH_INFO in PHP:
 *    - Use $_SERVER['PATH_INFO'] to retrieve the value.
 *    - Example:
 *      // $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
 *
 * 4. Typical Usage in Routing:
 *    - The MVC front controller parses PATH_INFO to determine which controller and method to invoke.
 *    - Example:
 *      // $segments = explode('/', trim($path, '/'));
 *      // $controller = $segments[0] ?? 'home';
 *      // $action = $segments[1] ?? 'index';
 *
 * 5. Server Configuration:
 *    - PATH_INFO may require server configuration (e.g., .htaccess for Apache) to redirect all requests to index.php.
 *    - Example .htaccess:
 *      
 *      RewriteEngine On
 *      RewriteCond %{REQUEST_FILENAME} !-f
 *      RewriteRule ^ index.php [QSA,L]
 *      
 * 6. Advantages:
 *    - Clean URLs, better SEO.
 *    - Easier to map URLs to controllers/actions.
 *
 * 7. Limitations:
 *    - Not all servers support PATH_INFO by default.
 *    - May need additional configuration for compatibility.
 *
 * Summary:
 * PATH_INFO is essential for clean URL routing in PHP MVC frameworks. It enables mapping of URL segments to controllers and actions, improving code organization and user experience.
 */