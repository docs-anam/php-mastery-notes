<?php

/**
 * PSR-7: HTTP Message Interface - Detailed Summary
 * 
 * PSR-7 defines common interfaces for representing HTTP messages as described in RFC 7230 and RFC 7231.
 * This standard provides a set of interfaces for HTTP requests, responses, URIs, streams, and uploaded files.
 * 
 * ============================================
 * KEY INTERFACES
 * ============================================
 * 
 * 1. Psr\Http\Message\MessageInterface
 *    - Base interface for HTTP messages (requests and responses)
 *    - Methods:
 *      - getProtocolVersion() / withProtocolVersion($version)
 *      - getHeaders() / hasHeader($name) / getHeader($name) / getHeaderLine($name)
 *      - withHeader($name, $value) / withAddedHeader($name, $value) / withoutHeader($name)
 *      - getBody() / withBody(StreamInterface $body)
 * 
 * 2. Psr\Http\Message\RequestInterface
 *    - Represents an outgoing, client-side request
 *    - Extends MessageInterface
 *    - Methods:
 *      - getRequestTarget() / withRequestTarget($requestTarget)
 *      - getMethod() / withMethod($method)
 *      - getUri() / withUri(UriInterface $uri, $preserveHost = false)
 * 
 * 3. Psr\Http\Message\ServerRequestInterface
 *    - Represents an incoming, server-side HTTP request
 *    - Extends RequestInterface
 *    - Methods:
 *      - getServerParams()
 *      - getCookieParams() / withCookieParams(array $cookies)
 *      - getQueryParams() / withQueryParams(array $query)
 *      - getUploadedFiles() / withUploadedFiles(array $uploadedFiles)
 *      - getParsedBody() / withParsedBody($data)
 *      - getAttributes() / getAttribute($name, $default = null)
 *      - withAttribute($name, $value) / withoutAttribute($name)
 * 
 * 4. Psr\Http\Message\ResponseInterface
 *    - Represents an outgoing, server-side response
 *    - Extends MessageInterface
 *    - Methods:
 *      - getStatusCode() / withStatus($code, $reasonPhrase = '')
 *      - getReasonPhrase()
 * 
 * 5. Psr\Http\Message\StreamInterface
 *    - Describes a data stream for message bodies
 *    - Methods: read(), write(), seek(), tell(), eof(), getSize(), etc.
 * 
 * 6. Psr\Http\Message\UriInterface
 *    - Represents a URI according to RFC 3986
 *    - Methods: getScheme(), getHost(), getPort(), getPath(), getQuery(), getFragment()
 * 
 * 7. Psr\Http\Message\UploadedFileInterface
 *    - Represents an uploaded file
 *    - Methods: getStream(), moveTo(), getSize(), getError(), getClientFilename(), getClientMediaType()
 * 
 * ============================================
 * KEY CONCEPTS
 * ============================================
 * 
 * IMMUTABILITY:
 * - All PSR-7 objects are IMMUTABLE
 * - Methods starting with "with*" return NEW instances with the modified value
 * - Original objects remain unchanged
 * 
 * EXAMPLE:
 * $newRequest = $request->withHeader('Content-Type', 'application/json');
 * // $request is unchanged, $newRequest has the new header
 * 
 * ============================================
 * BENEFITS
 * ============================================
 * 
 * 1. Interoperability: Different frameworks can work with the same HTTP message objects
 * 2. Testability: Immutable objects are easier to test
 * 3. Thread Safety: Immutability provides thread-safe operations
 * 4. Middleware Support: Perfect for HTTP middleware architectures
 * 5. Standard Compliance: Follows HTTP specifications (RFC 7230, RFC 7231)
 * 
 * ============================================
 * IMPLEMENTATIONS
 * ============================================
 * 
 * Popular implementations include:
 * - Guzzle PSR-7 (guzzlehttp/psr7)
 * - Laminas Diactoros (laminas/laminas-diactoros)
 * - Slim PSR-7 (slim/psr7)
 * - Nyholm PSR-7 (nyholm/psr7)
 * 
 * ============================================
 * USAGE EXAMPLE
 * ============================================
 */

// Example using a PSR-7 implementation (pseudo-code)
// require 'vendor/autoload.php';
// use Psr\Http\Message\ServerRequestInterface;
// use Psr\Http\Message\ResponseInterface;

// Example ServerRequest manipulation
function exampleServerRequest($request) {
    // $request is a ServerRequestInterface instance
    
    // Get query parameters
    $queryParams = $request->getQueryParams();
    
    // Get POST data
    $parsedBody = $request->getParsedBody();
    
    // Get specific header
    $contentType = $request->getHeaderLine('Content-Type');
    
    // Add custom attribute (immutable - returns new instance)
    $newRequest = $request->withAttribute('user_id', 123);
    
    // Get URI
    $uri = $request->getUri();
    $path = $uri->getPath();
    
    return $newRequest;
}

// Example Response creation
function exampleResponse($response) {
    // $response is a ResponseInterface instance
    
    // Set status code
    $response = $response->withStatus(200, 'OK');
    
    // Add headers
    $response = $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('X-Custom-Header', 'value');
    
    // Set body
    $body = $response->getBody();
    $body->write(json_encode(['message' => 'Success']));
    
    return $response;
}

/**
 * ============================================
 * MIDDLEWARE PATTERN WITH PSR-7
 * ============================================
 * 
 * PSR-7 is commonly used with PSR-15 (HTTP Server Request Handlers)
 * to create middleware pipelines:
 * 
 * interface MiddlewareInterface {
 *     public function process(
 *         ServerRequestInterface $request,
 *         RequestHandlerInterface $handler
 *     ): ResponseInterface;
 * }
 * 
 * This allows for:
 * - Request/Response transformation
 * - Authentication/Authorization
 * - Logging
 * - CORS handling
 * - Rate limiting
 * - And more...
 */

// Example Middleware (conceptual)
class ExampleMiddleware {
    public function process($request, $handler) {
        // Modify request
        $request = $request->withAttribute('timestamp', time());
        
        // Call next middleware/handler
        $response = $handler->handle($request);
        
        // Modify response
        $response = $response->withHeader('X-Processed-By', 'ExampleMiddleware');
        
        return $response;
    }
}

/**
 * ============================================
 * IMPORTANT NOTES
 * ============================================
 * 
 * 1. PSR-7 is an INTERFACE specification, not an implementation
 * 2. You need to install a PSR-7 implementation to use it
 * 3. All modifications return NEW instances (immutability)
 * 4. Headers are case-insensitive
 * 5. URI components are automatically encoded/decoded
 * 6. Stream resources should be properly managed
 * 7. Commonly used with PSR-15 for middleware
 * 8. Widely adopted by modern PHP frameworks (Slim, Mezzio, Laravel, Symfony)
 */

?>