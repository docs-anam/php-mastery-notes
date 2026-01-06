use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

<?php

/**
 * HTTP HANDLERS IN PSR (PSR-15)
 * 
 * PSR-15 defines two interfaces for HTTP server request handlers:
 * 1. RequestHandlerInterface - Handles a server request and produces a response
 * 2. MiddlewareInterface - Processes an incoming request and delegates to the handler
 * 
 * ========================================
 * 1. REQUEST HANDLER INTERFACE
 * ========================================
 * 
 * namespace Psr\Http\Server;
 * use Psr\Http\Message\ResponseInterface;
 * use Psr\Http\Message\ServerRequestInterface;
 * 
 * interface RequestHandlerInterface
 * {
 *     public function handle(ServerRequestInterface $request): ResponseInterface;
 * }
 * 
 * - Takes a PSR-7 ServerRequest
 * - Returns a PSR-7 Response
 * - Represents the final handler that produces a response
 * 
 * ========================================
 * 2. MIDDLEWARE INTERFACE
 * ========================================
 * 
 * namespace Psr\Http\Server;
 * use Psr\Http\Message\ResponseInterface;
 * use Psr\Http\Message\ServerRequestInterface;
 * 
 * interface MiddlewareInterface
 * {
 *     public function process(
 *         ServerRequestInterface $request,
 *         RequestHandlerInterface $handler
 *     ): ResponseInterface;
 * }
 * 
 * - Takes a ServerRequest and a RequestHandler
 * - Can modify request before passing to handler
 * - Can modify response after handler processes
 * - Must call $handler->handle() to continue the chain
 * 
 * ========================================
 * 3. EXAMPLE: BASIC REQUEST HANDLER
 * ========================================
 */


class HomePageHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write('<h1>Welcome Home</h1>');
        return $response->withHeader('Content-Type', 'text/html');
    }
}

/**
 * ========================================
 * 4. EXAMPLE: AUTHENTICATION MIDDLEWARE
 * ========================================
 */


class AuthenticationMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Check authentication
        $token = $request->getHeaderLine('Authorization');
        
        if (empty($token)) {
            $response = new Response(401);
            $response->getBody()->write('Unauthorized');
            return $response;
        }
        
        // Add user to request attributes
        $user = $this->validateToken($token);
        $request = $request->withAttribute('user', $user);
        
        // Continue to next middleware/handler
        return $handler->handle($request);
    }
    
    private function validateToken(string $token): ?object
    {
        // Token validation logic
        return (object)['id' => 1, 'name' => 'John Doe'];
    }
}

/**
 * ========================================
 * 5. EXAMPLE: LOGGING MIDDLEWARE
 * ========================================
 */

class LoggingMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Log before processing
        $startTime = microtime(true);
        $method = $request->getMethod();
        $uri = (string) $request->getUri();
        
        error_log("Request: {$method} {$uri}");
        
        // Process request
        $response = $handler->handle($request);
        
        // Log after processing
        $duration = microtime(true) - $startTime;
        $statusCode = $response->getStatusCode();
        error_log("Response: {$statusCode} ({$duration}s)");
        
        return $response;
    }
}

/**
 * ========================================
 * 6. EXAMPLE: MIDDLEWARE STACK/DISPATCHER
 * ========================================
 */

class MiddlewareStack implements RequestHandlerInterface
{
    private array $middleware = [];
    private RequestHandlerInterface $fallbackHandler;
    
    public function __construct(RequestHandlerInterface $fallbackHandler)
    {
        $this->fallbackHandler = $fallbackHandler;
    }
    
    public function add(MiddlewareInterface $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Create a handler that processes the middleware stack
        $handler = $this->createHandler();
        return $handler->handle($request);
    }
    
    private function createHandler(): RequestHandlerInterface
    {
        $handler = $this->fallbackHandler;
        
        // Build the middleware chain in reverse order
        foreach (array_reverse($this->middleware) as $middleware) {
            $handler = new class($middleware, $handler) implements RequestHandlerInterface {
                private MiddlewareInterface $middleware;
                private RequestHandlerInterface $next;
                
                public function __construct(
                    MiddlewareInterface $middleware,
                    RequestHandlerInterface $next
                ) {
                    $this->middleware = $middleware;
                    $this->next = $next;
                }
                
                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    return $this->middleware->process($request, $this->next);
                }
            };
        }
        
        return $handler;
    }
}

/**
 * ========================================
 * 7. USAGE EXAMPLE
 * ========================================
 */

// Create the main handler
$homeHandler = new HomePageHandler();

// Create middleware stack
$stack = new MiddlewareStack($homeHandler);
$stack->add(new LoggingMiddleware())
      ->add(new AuthenticationMiddleware());

// Process request
// $request = ServerRequest::fromGlobals();
// $response = $stack->handle($request);

/**
 * ========================================
 * 8. KEY BENEFITS
 * ========================================
 * 
 * - Interoperability: Works with any PSR-15 compatible framework
 * - Single Responsibility: Each middleware does one thing
 * - Composability: Stack multiple middleware easily
 * - Testability: Each component can be tested independently
 * - Reusability: Share middleware across projects
 * - Flexibility: Control request/response flow
 * 
 * ========================================
 * 9. COMMON MIDDLEWARE USE CASES
 * ========================================
 * 
 * - Authentication & Authorization
 * - CORS headers
 * - Rate limiting
 * - Request/Response logging
 * - Error handling
 * - Content negotiation
 * - Compression
 * - Caching
 * - Session management
 * - Input validation/sanitization
 * 
 * ========================================
 * 10. EXECUTION FLOW
 * ========================================
 * 
 * Request → Middleware 1 → Middleware 2 → Handler
 *                ↓              ↓            ↓
 * Response ← Middleware 1 ← Middleware 2 ← Response
 * 
 * Each middleware can:
 * - Modify request before passing forward
 * - Short-circuit and return response early
 * - Modify response after handler completes
 */