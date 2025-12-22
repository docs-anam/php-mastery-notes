use Nyholm\Psr7\Factory\Psr17Factory;

<?php

/**
 * HTTP Factories in PSR-17
 * 
 * PSR-17 defines interfaces for HTTP factories that create PSR-7 objects.
 * These factories provide a standardized way to instantiate HTTP messages.
 * 
 * ============================================================================
 * KEY INTERFACES:
 * ============================================================================
 * 
 * 1. RequestFactoryInterface
 *    - createRequest(string $method, $uri): RequestInterface
 *    - Creates HTTP requests
 * 
 * 2. ResponseFactoryInterface
 *    - createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
 *    - Creates HTTP responses
 * 
 * 3. ServerRequestFactoryInterface
 *    - createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
 *    - Creates server-side HTTP requests
 * 
 * 4. StreamFactoryInterface
 *    - createStream(string $content = ''): StreamInterface
 *    - createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
 *    - createStreamFromResource($resource): StreamInterface
 *    - Creates streams for message bodies
 * 
 * 5. UploadedFileFactoryInterface
 *    - createUploadedFile(StreamInterface $stream, ...): UploadedFileInterface
 *    - Creates uploaded file representations
 * 
 * 6. UriFactoryInterface
 *    - createUri(string $uri = ''): UriInterface
 *    - Creates URI instances
 * 
 * ============================================================================
 * BENEFITS:
 * ============================================================================
 * 
 * - Decoupling: Applications don't depend on specific PSR-7 implementations
 * - Interoperability: Different libraries can work together seamlessly
 * - Testability: Easy to mock factories in unit tests
 * - Flexibility: Swap implementations without changing application code
 * 
 * ============================================================================
 * USAGE EXAMPLE:
 * ============================================================================
 */

// Example with dependency injection
class ApiClient
{
    private $requestFactory;
    private $streamFactory;
    
    public function __construct(
        \Psr\Http\Message\RequestFactoryInterface $requestFactory,
        \Psr\Http\Message\StreamFactoryInterface $streamFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }
    
    public function sendData(string $url, array $data)
    {
        // Create request using factory
        $request = $this->requestFactory->createRequest('POST', $url);
        
        // Create stream for body
        $body = $this->streamFactory->createStream(json_encode($data));
        
        // Add body to request
        $request = $request->withBody($body)
                           ->withHeader('Content-Type', 'application/json');
        
        return $request;
    }
}

// Example: Creating a response
class ResponseBuilder
{
    private $responseFactory;
    private $streamFactory;
    
    public function __construct(
        \Psr\Http\Message\ResponseFactoryInterface $responseFactory,
        \Psr\Http\Message\StreamFactoryInterface $streamFactory
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }
    
    public function createJsonResponse(array $data, int $statusCode = 200)
    {
        $response = $this->responseFactory->createResponse($statusCode);
        $body = $this->streamFactory->createStream(json_encode($data));
        
        return $response->withBody($body)
                       ->withHeader('Content-Type', 'application/json');
    }
}

/**
 * ============================================================================
 * POPULAR IMPLEMENTATIONS:
 * ============================================================================
 * 
 * - Nyholm/psr7: nyholm/psr7 + nyholm/psr7-server
 * - Guzzle: guzzlehttp/psr7
 * - Laminas: laminas/laminas-diactoros
 * - Slim: slim/psr7
 * 
 * ============================================================================
 * INSTALLATION EXAMPLE (Composer):
 * ============================================================================
 * 
 * composer require nyholm/psr7
 * composer require nyholm/psr7-server
 * 
 * ============================================================================
 * REAL-WORLD USAGE:
 * ============================================================================
 */

// Using Nyholm PSR-7 factories

$psr17Factory = new Psr17Factory();

// Create a simple GET request
$request = $psr17Factory->createRequest('GET', 'https://api.example.com/users');

// Create a response
$response = $psr17Factory->createResponse(200, 'OK');
$body = $psr17Factory->createStream('{"status":"success"}');
$response = $response->withBody($body);

// Create URI
$uri = $psr17Factory->createUri('https://example.com/path?query=value');

// Create server request from globals
$serverRequest = $psr17Factory->createServerRequest(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/',
    $_SERVER
);

/**
 * ============================================================================
 * BEST PRACTICES:
 * ============================================================================
 * 
 * 1. Use dependency injection to pass factories to classes
 * 2. Type-hint against PSR-17 interfaces, not concrete implementations
 * 3. Keep factory creation in bootstrap/container configuration
 * 4. Use the same factory implementation throughout your application
 * 5. Consider using PSR-11 containers to manage factory instances
 * 
 * ============================================================================
 * INTEGRATION WITH PSR-7:
 * ============================================================================
 * 
 * PSR-17 factories create PSR-7 objects (Request, Response, Stream, etc.)
 * They work together to provide a complete HTTP message abstraction layer
 * 
 * PSR-7: Defines the HTTP message interfaces
 * PSR-17: Defines how to create those HTTP messages
 */
