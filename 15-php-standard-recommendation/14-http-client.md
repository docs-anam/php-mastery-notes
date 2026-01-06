# PSR-18: HTTP Client

## Overview

Learn about PSR-18, the standardized HTTP client interface that enables framework-agnostic HTTP requests to external services and APIs.

---

## Table of Contents

1. What is PSR-18
2. Core Concepts
3. Client Interface
4. Requests and Responses
5. Implementation
6. Common Patterns
7. Real-world Examples
8. Complete Examples

---

## What is PSR-18

### Purpose

```php
<?php
// Before PSR-18: Framework-specific HTTP clients

// Using Guzzle
$client = new GuzzleHttp\Client();
$response = $client->request('GET', 'https://api.example.com/users');

// Using cURL
$ch = curl_init('https://api.example.com/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

// Using Symfony
$client = HttpClient::create();
$response = $client->request('GET', 'https://api.example.com/users');

// Problems:
// - Different APIs
// - Framework lock-in
// - Hard to swap implementations
// - Testing difficulties

// Solution: PSR-18 (standardized HTTP client)

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class ApiService
{
    public function __construct(private ClientInterface $httpClient) {}

    public function fetchUsers(): array
    {
        $request = new Request('GET', 'https://api.example.com/users');
        $response = $this->httpClient->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }
}

// Benefits:
// ✓ Standard HTTP client interface
// ✓ Framework agnostic
// ✓ Easy to test (mock client)
// ✓ Swappable implementations
```

### Key Interface

```php
<?php
// ClientInterface
// - sendRequest(RequestInterface): ResponseInterface
// Can throw RequestExceptionInterface
```

---

## Core Concepts

### HTTP Client Pattern

```php
<?php
// Basic flow:
// 1. Create request (PSR-7)
// 2. Inject HTTP client
// 3. Send request
// 4. Receive response (PSR-7)
// 5. Process response

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PaymentGateway
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
    ) {}

    public function authorize(float $amount, string $cardToken): bool
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            'https://api.payment.com/authorize'
        );

        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody(/* JSON body */);

        try {
            $response = $this->httpClient->sendRequest($request);
            return $response->getStatusCode() === 200;
        } catch (RequestExceptionInterface $e) {
            // Handle network error
            return false;
        }
    }
}
```

---

## Client Interface

### ClientInterface

```php
<?php
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\RequestExceptionInterface;

interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response
     *
     * @throws RequestExceptionInterface If an error occurs
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
```

### Using HTTP Client

```php
<?php
use Psr\Http\Client\ClientInterface;

class WeatherService
{
    private const API_URL = 'https://api.weather.com';

    public function __construct(private ClientInterface $httpClient) {}

    public function getWeather(string $city): ?array
    {
        $request = new Request(
            'GET',
            self::API_URL . '/forecast?city=' . urlencode($city)
        );

        try {
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            return json_decode(
                $response->getBody()->getContents(),
                true
            );
        } catch (RequestExceptionInterface $e) {
            // Log error
            return null;
        }
    }
}
```

---

## Requests and Responses

### Building Requests

```php
<?php
// GET request
$request = new Request('GET', 'https://api.example.com/users');

// POST with JSON body
$body = json_encode(['name' => 'John', 'email' => 'john@example.com']);
$request = new Request('POST', 'https://api.example.com/users');
$request = $request
    ->withHeader('Content-Type', 'application/json')
    ->withBody(new Stream($body));

// PUT with form data
$body = http_build_query(['name' => 'John']);
$request = new Request('PUT', 'https://api.example.com/users/1');
$request = $request
    ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
    ->withBody(new Stream($body));

// DELETE request
$request = new Request('DELETE', 'https://api.example.com/users/1');

// With authentication
$request = new Request('GET', 'https://api.example.com/protected');
$request = $request->withHeader('Authorization', 'Bearer ' . $token);

// With custom headers
$request = $request
    ->withHeader('User-Agent', 'MyApp/1.0')
    ->withHeader('Accept', 'application/json')
    ->withHeader('X-Custom-Header', 'value');
```

### Handling Responses

```php
<?php
// Get status code
$code = $response->getStatusCode();

if ($code === 200) {
    // Success
} elseif ($code === 404) {
    // Not found
} elseif ($code >= 500) {
    // Server error
}

// Get reason phrase
$reason = $response->getReasonPhrase();

// Get headers
$contentType = $response->getHeaderLine('Content-Type');
$headers = $response->getHeaders();

// Get body
$body = $response->getBody();
$content = $body->getContents();

// Parse JSON response
$data = json_decode($content, true);

// Check response type
if (str_contains($contentType, 'application/json')) {
    $data = json_decode($content, true);
}
```

---

## Implementation

### Simple HTTP Client

```php
<?php
declare(strict_types=1);

namespace App\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class CurlHttpClient implements ClientInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $ch = curl_init();

        try {
            // Set request options
            curl_setopt($ch, CURLOPT_URL, (string)$request->getUri());
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Add headers
            $headers = [];
            foreach ($request->getHeaders() as $name => $values) {
                $headers[] = "$name: " . implode(', ', $values);
            }
            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            // Set body
            if ($request->getBody()->getSize() > 0) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, (string)$request->getBody());
            }

            // Execute request
            $body = curl_exec($ch);

            if ($body === false) {
                throw new RequestException(
                    'HTTP request failed: ' . curl_error($ch),
                    $request
                );
            }

            // Get status code
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Create response
            $stream = $this->streamFactory->createStream($body);
            $response = $this->responseFactory->createResponse($statusCode);

            return $response->withBody($stream);

        } finally {
            curl_close($ch);
        }
    }
}

class RequestException extends \Exception implements RequestExceptionInterface
{
    public function __construct(
        string $message,
        private RequestInterface $request,
    ) {
        parent::__construct($message);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
```

---

## Common Patterns

### Retry Logic

```php
<?php
class RetryHttpClient implements ClientInterface
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY = 1000; // milliseconds

    public function __construct(private ClientInterface $httpClient) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                return $this->httpClient->sendRequest($request);
            } catch (RequestExceptionInterface $e) {
                $lastException = $e;

                if ($attempt < self::MAX_RETRIES) {
                    usleep(self::RETRY_DELAY * 1000);
                }
            }
        }

        throw $lastException;
    }
}
```

### Timeout Handling

```php
<?php
class TimeoutHttpClient implements ClientInterface
{
    private const TIMEOUT = 30;

    public function __construct(private ClientInterface $httpClient) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $startTime = microtime(true);

        try {
            $response = $this->httpClient->sendRequest($request);

            $elapsed = microtime(true) - $startTime;
            if ($elapsed > self::TIMEOUT) {
                throw new TimeoutException(
                    'Request exceeded timeout',
                    $request
                );
            }

            return $response;
        } catch (RequestExceptionInterface $e) {
            throw $e;
        }
    }
}
```

### Request/Response Logging

```php
<?php
class LoggingHttpClient implements ClientInterface
{
    public function __construct(
        private ClientInterface $httpClient,
        private LoggerInterface $logger,
    ) {}

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        $this->logger->debug("HTTP {$method} {$uri}");

        try {
            $response = $this->httpClient->sendRequest($request);

            $this->logger->debug(
                "Response: {$response->getStatusCode()}"
            );

            return $response;
        } catch (RequestExceptionInterface $e) {
            $this->logger->error("Request failed: {$e->getMessage()}");
            throw $e;
        }
    }
}
```

---

## Real-world Examples

### External API Integration

```php
<?php
class GitHubApiClient
{
    private const API_URL = 'https://api.github.com';

    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private string $token = '',
    ) {}

    public function getRepository(string $owner, string $repo): ?array
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            self::API_URL . "/repos/{$owner}/{$repo}"
        );

        if ($this->token) {
            $request = $request->withHeader('Authorization', 'token ' . $this->token);
        }

        try {
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() === 200) {
                return json_decode(
                    $response->getBody()->getContents(),
                    true
                );
            }

            return null;
        } catch (RequestExceptionInterface $e) {
            // Log and handle error
            return null;
        }
    }

    public function listRepositories(string $user, int $page = 1): array
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            self::API_URL . "/users/{$user}/repos?page={$page}&per_page=30"
        );

        try {
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() === 200) {
                return json_decode(
                    $response->getBody()->getContents(),
                    true
                );
            }

            return [];
        } catch (RequestExceptionInterface $e) {
            return [];
        }
    }

    public function getUser(string $username): ?array
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            self::API_URL . "/users/{$username}"
        );

        try {
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() === 200) {
                return json_decode(
                    $response->getBody()->getContents(),
                    true
                );
            }

            return null;
        } catch (RequestExceptionInterface $e) {
            return null;
        }
    }
}
```

---

## Complete Examples

### Full HTTP Client Usage

```php
<?php
declare(strict_types=1);

namespace App;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class DataSyncService
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
    ) {}

    public function syncData(): bool
    {
        $data = ['timestamp' => time(), 'data' => 'example'];

        $body = json_encode($data);

        $request = $this->requestFactory->createRequest('POST', 'https://sync.example.com/api')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Authorization', 'Bearer token123')
            ->withBody(/* stream with body */);

        try {
            $response = $this->httpClient->sendRequest($request);

            return $response->getStatusCode() === 200;
        } catch (RequestExceptionInterface $e) {
            // Handle error
            return false;
        }
    }
}
```

---

## Key Takeaways

**PSR-18 HTTP Client Checklist:**

1. ✅ Inject ClientInterface
2. ✅ Create PSR-7 requests
3. ✅ Handle RequestExceptionInterface
4. ✅ Check response status codes
5. ✅ Parse response bodies appropriately
6. ✅ Set appropriate headers
7. ✅ Implement retry logic if needed
8. ✅ Log requests/responses

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [HTTP Message Interface (PSR-7)](6-http-message-interface.md)
- [HTTP Factories (PSR-17)](13-http-factories.md)
- [HTTP Handlers (PSR-15)](11-http-handlers.md)
