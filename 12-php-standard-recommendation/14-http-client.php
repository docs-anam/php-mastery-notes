use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;

<?php

/**
 * HTTP Client in PSR (PSR-18)
 * 
 * PSR-18 defines a standardized interface for HTTP clients in PHP.
 * It provides a common way for libraries to send HTTP requests and receive responses.
 * 
 * Key Interfaces:
 * ----------------
 * 
 * 1. ClientInterface
 *    - Main interface for sending HTTP requests
 *    - Method: sendRequest(RequestInterface $request): ResponseInterface
 *    - Must return a ResponseInterface or throw an exception
 * 
 * 2. ClientExceptionInterface
 *    - Base interface for all HTTP client exceptions
 * 
 * 3. RequestExceptionInterface
 *    - Thrown when the request cannot be sent (e.g., network failure)
 *    - Must provide getRequest() method
 * 
 * 4. NetworkExceptionInterface
 *    - Thrown when network-level errors occur
 *    - Must provide getRequest() method
 * 
 * Related PSRs:
 * -------------
 * 
 * PSR-7: HTTP Message Interface (defines RequestInterface and ResponseInterface)
 * PSR-17: HTTP Factories (creates PSR-7 objects)
 * 
 * Benefits:
 * ---------
 * - Interoperability between HTTP clients (Guzzle, Symfony HTTP Client, etc.)
 * - Easy to swap implementations without changing code
 * - Framework-agnostic HTTP communication
 * - Consistent error handling
 * 
 * Popular Implementations:
 * ------------------------
 * - Guzzle
 * - Symfony HTTP Client
 * - Buzz
 * - HTTPlug
 */

// Example: Basic HTTP Client Interface Implementation


/**
 * Example of using a PSR-18 HTTP Client
 */
class HttpClientExample
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Send a GET request to fetch data
     */
    public function fetchData(string $url): ?array
    {
        try {
            // Create PSR-7 request (requires PSR-17 factory)
            $request = new \GuzzleHttp\Psr7\Request('GET', $url);
            
            // Send request using PSR-18 client
            $response = $this->client->sendRequest($request);
            
            // Check status code
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
            
            return null;
            
        } catch (ClientExceptionInterface $e) {
            // Handle client exception
            error_log('HTTP Client Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send a POST request with JSON data
     */
    public function postData(string $url, array $data): ?ResponseInterface
    {
        try {
            $jsonBody = json_encode($data);
            
            $request = new \GuzzleHttp\Psr7\Request(
                'POST',
                $url,
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                $jsonBody
            );
            
            return $this->client->sendRequest($request);
            
        } catch (ClientExceptionInterface $e) {
            error_log('HTTP Client Error: ' . $e->getMessage());
            return null;
        }
    }
}

/**
 * Installation:
 * 
 * composer require psr/http-client
 * composer require psr/http-factory
 * composer require psr/http-message
 * 
 * For actual implementation:
 * composer require guzzlehttp/guzzle
 * OR
 * composer require symfony/http-client
 */

/**
 * Usage Example:
 */

// Using Guzzle as PSR-18 implementation
// $client = new \GuzzleHttp\Client();
// $httpClient = new HttpClientExample($client);
// $data = $httpClient->fetchData('https://api.example.com/users');

/**
 * Exception Handling:
 * 
 * 1. ClientExceptionInterface - Base exception
 * 2. RequestExceptionInterface - Request cannot be sent
 * 3. NetworkExceptionInterface - Network failure (DNS, timeout, etc.)
 * 
 * Example:
 */


function safeHttpRequest(ClientInterface $client, RequestInterface $request): void
{
    try {
        $response = $client->sendRequest($request);
        echo "Status: " . $response->getStatusCode();
        
    } catch (NetworkExceptionInterface $e) {
        // Network error (timeout, DNS failure, connection refused)
        echo "Network error: " . $e->getMessage();
        
    } catch (RequestExceptionInterface $e) {
        // Request could not be sent (invalid request)
        echo "Request error: " . $e->getMessage();
        
    } catch (ClientExceptionInterface $e) {
        // Other client errors
        echo "Client error: " . $e->getMessage();
    }
}

/**
 * Key Points:
 * -----------
 * 
 * 1. PSR-18 only defines the CLIENT interface, not Request/Response
 * 2. Must use PSR-7 for Request and Response objects
 * 3. Synchronous only (no async support in PSR-18)
 * 4. Does not define how to create requests (use PSR-17 factories)
 * 5. Must throw exceptions for errors, not return false/null
 * 6. All exceptions must implement ClientExceptionInterface
 * 7. Must not throw exceptions for HTTP error status codes (4xx, 5xx)
 */

?>