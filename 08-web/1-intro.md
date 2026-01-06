# PHP Web Development - Foundation & HTTP Essentials

## Table of Contents
1. [Overview](#overview)
2. [Web Development Fundamentals](#web-development-fundamentals)
3. [Client-Server Architecture](#client-server-architecture)
4. [HTTP Protocol](#http-protocol)
5. [PHP in Web Context](#php-in-web-context)
6. [Request-Response Cycle](#request-response-cycle)
7. [Development Approaches](#development-approaches)
8. [Common Web Concepts](#common-web-concepts)
9. [Learning Path](#learning-path)
10. [Prerequisites](#prerequisites)

---

## Overview

Web development with PHP involves creating dynamic websites and web applications. Unlike static HTML files, PHP allows you to generate content dynamically, interact with databases, manage user sessions, and handle form submissions.

This section covers:
- HTTP protocol basics
- Client-server communication
- Form handling (GET/POST)
- Session management
- Security considerations
- Cookies and state management

## Web Development Fundamentals

### What is a Web Application?

A web application is software that runs on a server and is accessed through a web browser. Unlike desktop applications, web apps:

- **Run on Servers**: Code executes on your server, not user's computer
- **Access via Browser**: Users access through HTTP/HTTPS
- **Stateless HTTP**: Each request is independent (uses sessions for state)
- **Data Persistence**: Store data in databases
- **Multi-User**: Handle multiple users simultaneously

### Components of a Web Stack

```
┌─────────────────────────────────────────┐
│        Frontend (HTML/CSS/JS)           │
│     What user sees and interacts with   │
└─────────────────────────────────────────┘
                    ▲
                    │ HTTP
                    ▼
┌─────────────────────────────────────────┐
│          Backend (PHP)                  │
│     Server-side logic and processing    │
└─────────────────────────────────────────┘
                    ▲
                    │
                    ▼
┌─────────────────────────────────────────┐
│        Database (MySQL/PostgreSQL)      │
│     Persistent data storage             │
└─────────────────────────────────────────┘
```

## Client-Server Architecture

### How It Works

1. **Client**: User's web browser
2. **Server**: Your PHP-enabled web server
3. **Network**: Internet connection (HTTP/HTTPS)

### Request Flow

```
User Actions              Server Processing
     │                           │
     ├─ Types URL ───HTTP───────┤
     │                 GET      ├─ Route request
     │              /index.php   │
     │                           ├─ Execute PHP
     │                           │
     │<───HTTP────┤              │
     │  Response  ├─ Generate HTML
     │  HTML/CSS/ │ Fetch from DB
     │  JavaScript│
     │            │
     ├─ Renders   │
     │  in browser│
```

### Stateless Nature of HTTP

HTTP is stateless - each request is independent:

```php
// Request 1: User logs in
$_POST['username'] = 'john';
// Server processes, returns success page

// Request 2: User browses products  
// Server DOESN'T remember they logged in!
// Solution: Use sessions or cookies
```

## HTTP Protocol

### What is HTTP?

HTTP (HyperText Transfer Protocol) is the protocol used to transfer data between clients and servers.

### HTTP Methods

| Method | Purpose | Safe | Idempotent |
|--------|---------|------|-----------|
| **GET** | Retrieve data | Yes | Yes |
| **POST** | Submit data | No | No |
| **PUT** | Update resource | No | Yes |
| **DELETE** | Delete resource | No | Yes |
| **HEAD** | Like GET but no body | Yes | Yes |
| **PATCH** | Partial update | No | No |

### GET vs POST

**GET Request**
```
GET /search?q=php&limit=10 HTTP/1.1
Host: example.com
```
- Data in URL
- Limited size (~2000 characters)
- Cached by browsers
- Visible in browser history
- Use for: Searching, filtering, getting data

**POST Request**
```
POST /user/register HTTP/1.1
Host: example.com
Content-Type: application/x-www-form-urlencoded

username=john&password=secret&email=john@example.com
```
- Data in request body
- No size limit
- Not cached
- Not in browser history
- Use for: Creating, updating sensitive data

### HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| **2xx** | Success | 200 OK, 201 Created |
| **3xx** | Redirect | 301 Moved, 302 Found |
| **4xx** | Client Error | 400 Bad Request, 404 Not Found |
| **5xx** | Server Error | 500 Internal Error, 503 Unavailable |

## PHP in Web Context

### PHP vs CLI

**CLI (Command Line)**
```bash
php script.php
```
- No web server needed
- Direct file execution
- Good for: Learning, scripts, automation

**Web Server**
```
http://localhost/script.php
```
- Requires web server (Apache, Nginx)
- Handles HTTP requests
- Good for: Web applications

### Request Superglobals

PHP provides special arrays for HTTP data:

```php
// URL Query parameters
$_GET['id']       // from ?id=5

// Form POST data
$_POST['username'] // from form submission

// All GET and POST
$_REQUEST['data']

// Server information
$_SERVER['REQUEST_METHOD']  // GET, POST, etc
$_SERVER['HTTP_HOST']       // example.com
$_SERVER['REQUEST_URI']     // /page.php?id=5

// Cookies
$_COOKIE['user_id']

// Session data
$_SESSION['user_id']

// Files from forms
$_FILES['upload']

// Environment variables
$_ENV['DB_PASSWORD']
```

## Request-Response Cycle

### Complete Flow

```
1. USER ACTION
   └─ Clicks link, submits form, types URL

2. BROWSER CREATES REQUEST
   ├─ Method: GET or POST
   ├─ URL: http://example.com/page.php
   ├─ Headers: User-Agent, Accept, Cookies, etc.
   └─ Body (if POST): Form data

3. REQUEST TRAVELS TO SERVER
   └─ Over internet via HTTP/HTTPS

4. WEB SERVER RECEIVES REQUEST
   ├─ Parses URL
   ├─ Identifies PHP file
   └─ Launches PHP engine

5. PHP EXECUTES
   ├─ Parses PHP code
   ├─ Accesses $_GET, $_POST, $_SESSION
   ├─ Performs business logic
   ├─ Queries database
   └─ Generates HTML/JSON

6. RESPONSE SENT
   ├─ Status code: 200, 404, 500, etc.
   ├─ Headers: Content-Type, Set-Cookie, etc.
   └─ Body: HTML/JSON content

7. BROWSER RECEIVES RESPONSE
   ├─ Parses HTML
   ├─ Downloads CSS, images, JS
   └─ Renders page for user

8. USER SEES RESULT
   └─ Page displays in browser
```

### Example: Form Submission

**HTML Form**
```html
<form method="POST" action="login.php">
    <input type="text" name="username">
    <input type="password" name="password">
    <button type="submit">Login</button>
</form>
```

**PHP Processing** (`login.php`)
```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validate and process
    if (validateCredentials($username, $password)) {
        $_SESSION['user'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>
```

## Development Approaches

### 1. Traditional Approach (Simple Projects)
```
Single .php file
├─ HTML
├─ PHP logic
└─ Database queries
(everything mixed)
```

**Good for**: Learning, small scripts
**Bad for**: Large projects, maintenance

### 2. Separating Concerns
```
Multiple files
├─ template.php (HTML)
├─ process.php (Logic)
└─ database.php (Queries)
```

**Good for**: Medium projects
**Bad for**: Complex requirements

### 3. MVC Pattern (Professional)
```
Model-View-Controller
├─ Model: Database logic
├─ View: HTML templates
└─ Controller: Request handling
```

**Good for**: Large projects, teams, maintenance
**Bad for**: Overkill for simple projects

### 4. Frameworks (Best Practice)
```
Laravel, Symfony, WordPress, etc.
├─ Built-in routing
├─ Database ORM
├─ Template engine
└─ Security features
```

**Good for**: Professional development
**Bad for**: Learning fundamentals

## Common Web Concepts

### Routing

Directing URLs to appropriate handlers:

```php
// Simple routing
if ($_SERVER['REQUEST_URI'] === '/users') {
    include 'views/users.php';
} elseif ($_SERVER['REQUEST_URI'] === '/products') {
    include 'views/products.php';
}
```

### Templating

Separating HTML from logic:

```php
// template.php
<h1><?php echo $title; ?></h1>
<p><?php echo $content; ?></p>

// handler.php
$title = "Welcome";
$content = "Hello, world!";
include 'template.php';
```

### Security

Key concerns:

```php
// SQL Injection: Use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);

// XSS: Escape output
echo htmlspecialchars($_GET['name'], ENT_QUOTES);

// CSRF: Use tokens
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

// Password: Hash properly
$hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
```

## Learning Path

Master web development progressively:

1. **[HTTP Basics](1-intro.md)** - Understand the protocol
2. **[Client-Server](2-client-&-server.md)** - How communication works
3. **[PHP Web](3-php-web.md)** - Running PHP on web
4. **[Development Server](4-php-development-server.md)** - Built-in server
5. **[Hello World Web](5-php-web-hello-world.md)** - First web app
6. **[Global Variables](9-global-variable-server.md)** - $_SERVER, $_GET, etc.
7. **[Query Parameters](10-query-parameter.md)** - URL-based data
8. **[Forms & POST](12-form-post.md)** - Form submission
9. **[Headers](13-header.md)** - HTTP headers
10. **[Sessions](15-session.md)** - Maintaining state
11. **[Cookies](16-cookie.md)** - Client-side storage
12. **[Security](11-xss-cross-site-scripting.md)** - Protecting users

## Prerequisites

Before starting web development, understand:

✅ **From Basics:**
- Variables and data types
- Arrays and loops
- Functions
- String manipulation
- Control structures

✅ **Required Knowledge:**
- Basic HTML structure
- What HTTP is (conceptually)
- How web browsers work

✅ **Recommended Setup:**
- PHP installed locally
- Text editor (VS Code)
- Local web server or PHP's built-in server

## Common Pitfalls

❌ **Don't**: Mix HTML and PHP too much
```php
// Bad
<?php echo "<h1>" . $title . "</h1>"; ?>
```

✅ **Do**: Separate them
```html
<!-- Separate HTML file -->
<h1><?php echo $title; ?></h1>
```

---

❌ **Don't**: Trust user input
```php
// Bad - SQL injection vulnerable!
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];
```

✅ **Do**: Validate and escape
```php
// Good - Prepared statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
```

---

❌ **Don't**: Use GET for sensitive data
```php
// Bad
<a href="login.php?password=secret">Login</a>
```

✅ **Do**: Use POST for forms
```html
<!-- Good -->
<form method="POST" action="login.php">
    <input type="password" name="password">
</form>
```

## Next Steps

1. Start with [HTTP Basics](1-intro.md)
2. Understand the [Client-Server model](2-client-&-server.md)
3. Run your first [Web Hello World](5-php-web-hello-world.md)
4. Practice with [Query Parameters](10-query-parameter.md)
5. Build with [Forms](12-form-post.md)

## Resources

- **HTTP Spec**: [RFC 7230](https://tools.ietf.org/html/rfc7230)
- **MDN Web Docs**: [Web Fundamentals](https://developer.mozilla.org/en-US/docs/Learn)
- **PHP Manual**: [PHP & Web](https://www.php.net/manual/en/security.php)
- **OWASP**: [Web Security](https://owasp.org/www-community/)
