# PSR-17: HTTP Factories

## Overview

Learn about PSR-17, the standardized factory interfaces for creating PSR-7 HTTP messages, enabling framework-agnostic HTTP object construction.

---

## Table of Contents

1. What is PSR-17
2. Core Concepts
3. Factory Interfaces
4. Creating Messages
5. Implementation
6. Common Patterns
7. Real-world Examples
8. Complete Examples

---

## What is PSR-17

### Purpose

```php
<?php
// Before PSR-17: Framework-specific object creation

// Guzzle way
$response = new GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json']);
$request = new GuzzleHttp\Psr7\Request('GET', 'https://example.com');
$uri = new GuzzleHttp\Psr7\Uri('https://example.com/path?query=value');
$stream = new GuzzleHttp\Psr7\Stream(fopen('file.txt', 'r'));

// Symfony way
$response = new Symfony\Component\HttpFoundation\Response();
$request = Symfony\Component\HttpFoundation\Request::create();

// Problems:
// - Different creation methods
// - Framework lock-in
// - Hard to swap implementations
// - Vendor-specific constructors

// Solution: PSR-17 (standardized factories)

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\RequestFactoryInterface;

class ApiClient
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function createRequest(string $method, string $url)
    {
        return $this->requestFactory->createRequest($method, $url);
    }

    public function createErrorResponse(int $code, string $message)
    {
        return $this->responseFactory->createResponse($code);
    }
}

// Benefits:
// ✓ Standard factory methods
// ✓ Framework agnostic
// ✓ Easy to inject
// ✓ Testable
```

### Key Interfaces

```php
<?php
// ResponseFactoryInterface
// - createResponse(statusCode): ResponseInterface

// RequestFactoryInterface
// - createRequest(method, uri): RequestInterface

// ServerRequestFactoryInterface
// - createServerRequest(method, uri, serverParams): ServerRequestInterface

// StreamFactoryInterface
// - createStream(): StreamInterface
// - createStreamFromFile(filename): StreamInterface
// - createStreamFromResource(resource): StreamInterface

// UriFactoryInterface
// - createUri(uri): UriInterface

// UploadedFileFactoryInterface
// - createUploadedFile(stream, size, error, clientFilename, clientMediaType): UploadedFileInterface
```

---

## Core Concepts

### Factory Methods

```php
<?php
// Factories create objects with standard interfaces
// No framework-specific methods
// Simple, predictable APIs

// Standard construction process:
// 1. Call factory method
// 2. Receive PSR-7 object
// 3. Use standard PSR-7 interface
// 4. Works with any implementation
```

### Dependency Injection Pattern

```php
<?php
// Instead of creating objects directly:
$response = new CustomResponse();

// Inject factory:
class Controller
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function action()
    {
        $response = $this->responseFactory->createResponse(200);
        return $response;
    }
}

// Benefits:
// - Can swap implementations
// - Easy to test (mock factory)
// - Framework agnostic
```

---

## Factory Interfaces

### ResponseFactory

```php
<?php
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    /**
     * Create a new response
     */
    public function createResponse(
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface;
}

// Usage
$factory = new ResponseFactory();
$response = $factory->createResponse(200);
$response = $factory->createResponse(404, 'Not Found');
$response = $factory->createResponse(500);
```

### RequestFactory

```php
<?php
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

interface RequestFactoryInterface
{
    /**
     * Create a new request
     */
    public function createRequest(
        string $method,
        UriInterface|string $uri
    ): RequestInterface;
}

// Usage
$factory = new RequestFactory();
$request = $factory->createRequest('GET', 'https://api.example.com/users');
$request = $factory->createRequest('POST', $uri);
```

### ServerRequestFactory

```php
<?php
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ServerRequestFactoryInterface
{
    /**
     * Create a server request
     */
    public function createServerRequest(
        string $method,
        UriInterface|string $uri,
        array $serverParams = []
    ): ServerRequestInterface;
}

// Usage
$factory = new ServerRequestFactory();

$request = $factory->createServerRequest(
    'GET',
    '/users',
    $_SERVER
);

// With URI
$request = $factory->createServerRequest(
    'POST',
    'https://example.com/api/users',
    $_SERVER
);
```

### StreamFactory

```php
<?php
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

interface StreamFactoryInterface
{
    /**
     * Create a stream from string
     */
    public function createStream(string $content = ''): StreamInterface;

    /**
     * Create a stream from file
     */
    public function createStreamFromFile(
        string $filename,
        string $mode = 'r'
    ): StreamInterface;

    /**
     * Create a stream from resource
     */
    public function createStreamFromResource(
        $resource
    ): StreamInterface;
}

// Usage
$factory = new StreamFactory();

// From string
$stream = $factory->createStream('Hello World');

// From file
$stream = $factory->createStreamFromFile('data.json', 'r');

// From resource
$resource = fopen('large-file.bin', 'r');
$stream = $factory->createStreamFromResource($resource);
```

### UriFactory

```php
<?php
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

interface UriFactoryInterface
{
    /**
     * Create a URI
     */
    public function createUri(string $uri = ''): UriInterface;
}

// Usage
$factory = new UriFactory();

$uri = $factory->createUri('https://api.example.com/users');
$uri = $factory->createUri('https://api.example.com/users?page=2');
$uri = $factory->createUri('/relative/path');
```

---

## Creating Messages

### Request Creation

```php
<?php
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class HttpClient
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function getUsers(): RequestInterface
    {
        return $this->requestFactory->createRequest(
            'GET',
            'https://api.example.com/users'
        );
    }

    public function createUser(array $data): RequestInterface
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            'https://api.example.com/users'
        );

        $stream = $this->streamFactory->createStream(
            json_encode($data)
        );

        return $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);
    }

    public function uploadFile(string $path): RequestInterface
    {
        $stream = $this->streamFactory->createStreamFromFile($path, 'r');

        $request = $this->requestFactory->createRequest(
            'POST',
            'https://api.example.com/upload'
        );

        return $request->withBody($stream);
    }
}
```

### Response Creation

```php
<?php
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ApiHandler
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function handleRequest(RequestInterface $request): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/api/users') {
            return $this->listUsers();
        }

        return $this->notFound();
    }

    private function listUsers(): ResponseInterface
    {
        $users = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
        ];

        $stream = $this->streamFactory->createStream(
            json_encode($users)
        );

        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);
    }

    private function notFound(): ResponseInterface
    {
        $body = $this->streamFactory->createStream('Not Found');

        return $this->responseFactory
            ->createResponse(404)
            ->withBody($body);
    }
}
```

---

## Implementation

### Simple Factories

```php
<?php
declare(strict_types=1);

namespace App\Http\Factory;

use Psr\Http\Message\{
    ResponseFactoryInterface,
    RequestFactoryInterface,
    StreamFactoryInterface,
    UriFactoryInterface,
    ResponseInterface,
    RequestInterface,
    StreamInterface,
    UriInterface,
};
use GuzzleHttp\Psr7\{Response, Request, Stream, Uri};

class ResponseFactory implements ResponseFactoryInterface
{
    public function createResponse(
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface {
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }
}

class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(
        string $method,
        UriInterface|string $uri
    ): RequestInterface {
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }

        return new Request($method, $uri);
    }
}

class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = new Stream(fopen('php://memory', 'r+'));
        $stream->write($content);
        $stream->rewind();

        return $stream;
    }

    public function createStreamFromFile(
        string $filename,
        string $mode = 'r'
    ): StreamInterface {
        $resource = fopen($filename, $mode);

        if (!is_resource($resource)) {
            throw new RuntimeException("Cannot open file: {$filename}");
        }

        return new Stream($resource);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Not a valid resource');
        }

        return new Stream($resource);
    }
}

class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
```

---

## Common Patterns

### Factory Injection

```php
<?php
class Service
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function handleRequest(): ResponseInterface
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            'https://api.example.com/data'
        );

        // Process request...

        $body = $this->streamFactory->createStream(
            json_encode(['status' => 'success'])
        );

        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);
    }
}
```

### Factory Container

```php
<?php
class HttpFactoryProvider
{
    private ResponseFactoryInterface $responseFactory;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private UriFactoryInterface $uriFactory;

    public function __construct()
    {
        $this->responseFactory = new ResponseFactory();
        $this->requestFactory = new RequestFactory();
        $this->streamFactory = new StreamFactory();
        $this->uriFactory = new UriFactory();
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    public function getRequestFactory(): RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    public function getStreamFactory(): StreamFactoryInterface
    {
        return $this->streamFactory;
    }

    public function getUriFactory(): UriFactoryInterface
    {
        return $this->uriFactory;
    }
}
```

---

## Real-world Examples

### HTTP API Client

```php
<?php
class ApiClient
{
    private const API_URL = 'https://api.example.com';

    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private HttpClient $httpClient,
    ) {}

    public function getUser(int $id): ?array
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            self::API_URL . "/users/{$id}"
        );

        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        return null;
    }

    public function createUser(string $name, string $email): ?array
    {
        $data = ['name' => $name, 'email' => $email];

        $body = $this->streamFactory->createStream(
            json_encode($data)
        );

        $request = $this->requestFactory->createRequest('POST', self::API_URL . '/users')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);

        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() === 201) {
            return json_decode($response->getBody()->getContents(), true);
        }

        return null;
    }

    public function uploadFile(string $path): bool
    {
        $body = $this->streamFactory->createStreamFromFile($path, 'r');

        $request = $this->requestFactory->createRequest('POST', self::API_URL . '/upload')
            ->withBody($body);

        $response = $this->httpClient->sendRequest($request);

        return $response->getStatusCode() === 200;
    }
}
```

---

## Complete Examples

### Full Application with Factories

```php
<?php
declare(strict_types=1);

namespace App;

use Psr\Http\Message\{
    ResponseFactoryInterface,
    RequestFactoryInterface,
    StreamFactoryInterface,
};

class Application
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        return match ($path) {
            '/' => $this->home(),
            '/api/users' => $this->listUsers(),
            '/api/health' => $this->health(),
            default => $this->notFound(),
        };
    }

    private function home(): ResponseInterface
    {
        $html = '<h1>Welcome</h1>';
        $body = $this->streamFactory->createStream($html);

        return $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', 'text/html')
            ->withBody($body);
    }

    private function listUsers(): ResponseInterface
    {
        $users = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
        ];

        $body = $this->streamFactory->createStream(
            json_encode($users)
        );

        return $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);
    }

    private function health(): ResponseInterface
    {
        $health = ['status' => 'ok'];
        $body = $this->streamFactory->createStream(
            json_encode($health)
        );

        return $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);
    }

    private function notFound(): ResponseInterface
    {
        $body = $this->streamFactory->createStream('Not Found');

        return $this->responseFactory->createResponse(404)
            ->withBody($body);
    }
}

// Bootstrap
$responseFactory = new ResponseFactory();
$requestFactory = new RequestFactory();
$streamFactory = new StreamFactory();

$app = new Application(
    $responseFactory,
    $requestFactory,
    $streamFactory,
);

$request = ServerRequest::fromGlobals();
$response = $app->handleRequest($request);

// Send response
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    header("$name: " . implode(', ', $values));
}
echo $response->getBody();
```

---

## Key Takeaways

**PSR-17 HTTP Factories Checklist:**

1. ✅ Inject factory interfaces
2. ✅ Use factories to create objects
3. ✅ Never instantiate directly
4. ✅ Chain builder methods
5. ✅ Handle streams properly
6. ✅ Set appropriate headers
7. ✅ Test with mock factories
8. ✅ Support multiple implementations

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [HTTP Message Interface (PSR-7)](6-http-message-interface.md)
- [HTTP Handlers (PSR-15)](11-http-handlers.md)
- [HTTP Client (PSR-18)](14-http-client.md)
