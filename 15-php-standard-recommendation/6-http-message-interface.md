# PSR-7: HTTP Message Interface

## Overview

Learn about PSR-7, the standardized interface for HTTP requests and responses, enabling interoperability between libraries and frameworks.

---

## Table of Contents

1. What is PSR-7
2. HTTP Messages
3. Requests
4. Responses
5. Streams
6. Implementation
7. Common Patterns
8. Real-world Examples
9. Complete Examples

---

## What is PSR-7

### Purpose

```php
<?php
// Before PSR-7: Vendor-specific HTTP handling

// Using Guzzle directly
$client = new GuzzleHttp\Client();
$response = $client->request('GET', 'https://example.com');

// Using Symfony HttpFoundation
$request = Request::createFromGlobals();
$response = new Response('Hello');

// Using custom code
$_POST, $_GET, $_SERVER globals

// Problems:
// - Different APIs for different libraries
// - Can't easily swap implementations
// - Global state coupling
// - Testing difficulties

// Solution: PSR-7 (standardized HTTP messages)

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        // Works with any PSR-7 implementation!

        return new Response(200, [], 'OK');
    }
}

// Benefits:
// ✓ Standard HTTP message interface
// ✓ Library agnostic
// ✓ Testable code
// ✓ Middleware support
```

### Key Interfaces

```php
<?php
// Core interfaces:

// MessageInterface - Base for all HTTP messages
// - getProtocolVersion/withProtocolVersion
// - getHeaders/getHeader/getHeaderLine
// - getBody/withBody

// RequestInterface - HTTP requests
// - getRequestTarget/withRequestTarget
// - getMethod/withMethod
// - getUri/withUri

// ResponseInterface - HTTP responses
// - getStatusCode/withStatus
// - getReasonPhrase

// ServerRequestInterface - Server received requests
// - getServerParams/getCookieParams
// - getQueryParams/getParsedBody
// - getAttributes/getAttribute/withAttribute

// StreamInterface - HTTP message body
// - getContents/read/write
// - isReadable/isWritable/isSeekable
// - seek/tell/rewind/eof
```

---

## HTTP Messages

### Message Structure

```php
<?php
// Every HTTP message has:

// 1. Protocol version (1.0, 1.1, 2.0)
$request->getProtocolVersion();  // "1.1"

// 2. Headers (metadata)
$request->getHeaders();
$request->getHeader('Content-Type');

// 3. Body (content)
$request->getBody()->getContents();

// Example HTTP message structure:
// POST /api/users HTTP/1.1
// Host: example.com
// Content-Type: application/json
// Content-Length: 27
//
// {"name":"John","email":"john@example.com"}
```

### Headers

```php
<?php
use Psr\Http\Message\MessageInterface;

interface MessageInterface
{
    /**
     * Retrieves all message header values
     */
    public function getHeaders(): array;

    /**
     * Checks if a message has a header with the given case-insensitive name
     */
    public function hasHeader(string $name): bool;

    /**
     * Retrieves a message header value by the given case-insensitive name
     */
    public function getHeader(string $name): array;

    /**
     * Retrieves a comma-separated string of the values for a single header
     */
    public function getHeaderLine(string $name): string;

    /**
     * Return an instance with the provided value replacing the specified header
     */
    public function withHeader(string $name, $value): static;

    /**
     * Return an instance with the specified header appended
     */
    public function withAddedHeader(string $name, $value): static;

    /**
     * Return an instance without the specified header
     */
    public function withoutHeader(string $name): static;
}

// Usage
$request = $request
    ->withHeader('Content-Type', 'application/json')
    ->withAddedHeader('Accept', 'application/json')
    ->withoutHeader('X-Debug');
```

### Immutability

```php
<?php
// PSR-7 messages are immutable!

$request = new ServerRequest('GET', '/users');

// Create new instance with modification
$modified = $request->withMethod('POST');

// Original unchanged
echo $request->getMethod();   // "GET"
echo $modified->getMethod();  // "POST"

// Chain modifications
$request = $request
    ->withMethod('POST')
    ->withHeader('Content-Type', 'application/json')
    ->withUri(new Uri('/api/v1/users'));
```

---

## Requests

### ServerRequest

```php
<?php
use Psr\Http\Message\ServerRequestInterface;

interface ServerRequestInterface extends RequestInterface
{
    /**
     * Retrieve server parameters derived from $_SERVER
     */
    public function getServerParams(): array;

    /**
     * Retrieve cookies sent by the client
     */
    public function getCookieParams(): array;

    /**
     * Retrieve query string arguments ($_GET)
     */
    public function getQueryParams(): array;

    /**
     * Retrieve parsed body ($_POST, JSON, etc)
     */
    public function getParsedBody(): null|array|object;

    /**
     * Retrieve attributes derived from the request
     */
    public function getAttributes(): array;

    /**
     * Retrieve a single derived request attribute
     */
    public function getAttribute(string $name, $default = null): mixed;

    /**
     * Return an instance with the specified derived request attribute
     */
    public function withAttribute(string $name, $value): static;

    /**
     * Return an instance that removes the specified derived request attribute
     */
    public function withoutAttribute(string $name): static;
}
```

### Request Details

```php
<?php
// HTTP method
$method = $request->getMethod();  // GET, POST, PUT, DELETE, etc.

// URI components
$uri = $request->getUri();
$path = $uri->getPath();           // /api/users
$query = $uri->getQuery();         // id=1&name=john
$host = $uri->getHost();           // example.com
$scheme = $uri->getScheme();       // http or https

// Query parameters
$params = $request->getQueryParams();  // Parsed query string

// Request body
$body = $request->getBody();
$content = $body->getContents();  // Raw body content

// Parsed body (form data, JSON, etc)
$data = $request->getParsedBody();

// Headers
$contentType = $request->getHeaderLine('Content-Type');
$authorization = $request->getHeaderLine('Authorization');

// Server info
$serverParams = $request->getServerParams();  // $_SERVER equivalent

// Cookies
$cookies = $request->getCookieParams();

// Custom attributes (set by middleware)
$userId = $request->getAttribute('user_id');
```

### Creating Requests

```php
<?php
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;

// Client request (for HTTP calls)
$request = new Request('GET', 'https://api.example.com/users');

// Server request (for handling received HTTP)
$serverRequest = new ServerRequest('POST', '/api/users', ['Content-Type' => 'application/json']);

// With body
$request = new Request(
    'POST',
    'https://api.example.com/users',
    ['Content-Type' => 'application/json'],
    json_encode(['name' => 'John'])
);
```

---

## Responses

### ResponseInterface

```php
<?php
use Psr\Http\Message\ResponseInterface;

interface ResponseInterface extends MessageInterface
{
    /**
     * Gets the response status code
     */
    public function getStatusCode(): int;

    /**
     * Return an instance with the specified status code
     */
    public function withStatus(int $code, string $reasonPhrase = ''): static;

    /**
     * Gets the response reason phrase
     */
    public function getReasonPhrase(): string;
}
```

### Status Codes

```php
<?php
use GuzzleHttp\Psr7\Response;

// Success responses
$response = new Response(200);        // OK
$response = new Response(201);        // Created
$response = new Response(204);        // No Content

// Redirect responses
$response = new Response(301);        // Moved Permanently
$response = new Response(302);        // Found
$response = new Response(304);        // Not Modified

// Client error responses
$response = new Response(400);        // Bad Request
$response = new Response(401);        // Unauthorized
$response = new Response(403);        // Forbidden
$response = new Response(404);        // Not Found

// Server error responses
$response = new Response(500);        // Internal Server Error
$response = new Response(503);        // Service Unavailable
```

### Response Details

```php
<?php
// With status and headers
$response = new Response(
    200,
    [
        'Content-Type' => 'application/json',
        'Cache-Control' => 'no-cache',
    ],
    json_encode(['status' => 'ok'])
);

// Status code
$code = $response->getStatusCode();      // 200
$phrase = $response->getReasonPhrase();  // "OK"

// Modify status
$response = $response->withStatus(201, 'Created');

// Headers
$contentType = $response->getHeaderLine('Content-Type');

// Body
$body = $response->getBody();
echo $body->getContents();
```

---

## Streams

### StreamInterface

```php
<?php
use Psr\Http\Message\StreamInterface;

interface StreamInterface
{
    /**
     * Returns the remaining contents in a string
     */
    public function getContents(): string;

    /**
     * Returns the current position of the file read/write pointer
     */
    public function tell(): int;

    /**
     * Returns whether or not the stream is readable
     */
    public function isReadable(): bool;

    /**
     * Returns whether or not the stream is writable
     */
    public function isWritable(): bool;

    /**
     * Returns whether or not the stream is seekable
     */
    public function isSeekable(): bool;

    /**
     * Seek to a position in the stream
     */
    public function seek(int $offset, int $whence = SEEK_SET): void;

    /**
     * Seek to the beginning of the stream
     */
    public function rewind(): void;

    /**
     * Returns true if the stream is at end-of-file
     */
    public function eof(): bool;

    /**
     * Returns the size of the stream if known
     */
    public function getSize(): ?int;

    /**
     * Read data from the stream
     */
    public function read(int $length): string;

    /**
     * Writes data to the stream
     */
    public function write(string $data): int;
}
```

### Working with Streams

```php
<?php
use GuzzleHttp\Psr7\Stream;

// Create from string
$stream = new Stream('Hello World');
echo $stream->getContents();  // "Hello World"

// Create from file
$resource = fopen('file.txt', 'r');
$stream = new Stream($resource);

// Read data
$chunk = $stream->read(1024);

// Seek and read
$stream->seek(0);
$stream->rewind();  // Back to start

// Check position
echo $stream->tell();  // Current position

// Check EOF
while (!$stream->eof()) {
    $chunk = $stream->read(1024);
    // Process chunk
}

// Write
$stream->write('data');

// Clone/copy stream
$copy = clone $stream;
```

---

## Implementation

### Using Guzzle PSR-7

```php
<?php
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;

// Creating responses
$response = new Response(
    200,
    ['Content-Type' => 'application/json'],
    json_encode(['status' => 'ok'])
);

// Creating requests
$request = new ServerRequest(
    'POST',
    '/api/users',
    ['Content-Type' => 'application/json']
);

// With stream body
$stream = new Stream(fopen('file.json', 'r'));
$response = new Response(200, [], $stream);
```

### Middleware Pattern

```php
<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoggingMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        // Log incoming request
        echo "Request: " . $request->getMethod() . " " . $request->getUri()->getPath();

        // Pass to next handler
        $response = $handler->handle($request);

        // Log outgoing response
        echo "Response: " . $response->getStatusCode();

        return $response;
    }
}

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');

        if (!$token) {
            return new Response(401, [], 'Unauthorized');
        }

        // Add user attribute to request
        $request = $request->withAttribute('user_id', $this->parseToken($token));

        return $handler->handle($request);
    }

    private function parseToken(string $token): int
    {
        // Parse and validate token
        return 123;
    }
}
```

---

## Common Patterns

### Request Handler

```php
<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface;

class UserHandler implements RequestHandlerInterface
{
    public function handle(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        return match ([$method, $path]) {
            ['GET', '/users'] => $this->listUsers($request),
            ['POST', '/users'] => $this->createUser($request),
            ['GET', '/users/{id}'] => $this->getUser($request),
            ['PUT', '/users/{id}'] => $this->updateUser($request),
            ['DELETE', '/users/{id}'] => $this->deleteUser($request),
            default => new Response(404, [], 'Not Found'),
        };
    }

    private function listUsers(Request $request): Response
    {
        $users = [['id' => 1, 'name' => 'John']];
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($users));
    }

    private function createUser(Request $request): Response
    {
        $data = $request->getParsedBody();
        // Create user...
        return new Response(201, ['Content-Type' => 'application/json'], json_encode(['id' => 2]));
    }

    private function getUser(Request $request): Response
    {
        $user = ['id' => 1, 'name' => 'John'];
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($user));
    }

    private function updateUser(Request $request): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode(['updated' => true]));
    }

    private function deleteUser(Request $request): Response
    {
        return new Response(204);
    }
}
```

---

## Real-world Examples

### JSON API Endpoint

```php
<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ArticleApi
{
    public function handle(Request $request): Response
    {
        if ($request->getMethod() !== 'GET') {
            return $this->jsonResponse(405, ['error' => 'Method not allowed']);
        }

        $id = $request->getQueryParams()['id'] ?? null;

        if (!$id) {
            return $this->jsonResponse(400, ['error' => 'Missing article id']);
        }

        $article = $this->getArticle((int)$id);

        if (!$article) {
            return $this->jsonResponse(404, ['error' => 'Article not found']);
        }

        return $this->jsonResponse(200, $article);
    }

    private function jsonResponse(int $code, array $data): Response
    {
        return new Response(
            $code,
            [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache',
            ],
            json_encode($data)
        );
    }

    private function getArticle(int $id): ?array
    {
        // Fetch from database
        return ['id' => $id, 'title' => 'Article', 'body' => 'Content'];
    }
}
```

---

## Complete Examples

### Simple HTTP Server

```php
<?php
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;

// Create request from globals
$request = ServerRequest::fromGlobals();

// Handle request
$path = $request->getUri()->getPath();
$method = $request->getMethod();

$response = match ([$method, $path]) {
    ['GET', '/'] => new Response(200, [], 'Hello World'),
    ['GET', '/users'] => new Response(200, ['Content-Type' => 'application/json'], json_encode([
        ['id' => 1, 'name' => 'John'],
        ['id' => 2, 'name' => 'Jane'],
    ])),
    default => new Response(404, [], 'Not Found'),
};

// Send response
http_response_code($response->getStatusCode());

foreach ($response->getHeaders() as $name => $values) {
    header("$name: " . implode(', ', $values));
}

echo $response->getBody();
```

---

## Key Takeaways

**PSR-7 HTTP Messages Checklist:**

1. ✅ Use ServerRequestInterface for received requests
2. ✅ Use ResponseInterface for sending responses
3. ✅ Remember messages are immutable (withX methods)
4. ✅ Properly set status codes and headers
5. ✅ Use streams for body content
6. ✅ Parse request bodies appropriately
7. ✅ Implement middleware for cross-cutting concerns
8. ✅ Test request/response handling thoroughly

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [HTTP Handlers (PSR-15)](11-http-handlers.md)
- [HTTP Client (PSR-18)](14-http-client.md)
- [HTTP Factories (PSR-17)](13-http-factories.md)
