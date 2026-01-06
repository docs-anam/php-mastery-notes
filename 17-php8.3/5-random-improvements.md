# Random Class Improvements

## Overview

Learn about the enhanced Random class in PHP 8.3, which provides improved randomization algorithms, better performance, and more flexible random number generation options.

---

## Table of Contents

1. Random Class Overview
2. Random\Randomizer
3. Randomizer Methods
4. Engines
5. Cryptographic Security
6. Performance Improvements
7. Practical Examples
8. Complete Examples

---

## Random Class Overview

### History and Improvements

```php
<?php
// PHP 8.2: Basic Random class introduced
$random = new Random\Randomizer();
$int = $random->nextInt(100);

// PHP 8.3: Improvements and enhancements
// - Better performance
// - More algorithm choices
// - Easier to use
// - Better documentation

// Before PHP 8.2: Using rand() and mt_rand()
$value = rand(1, 100);          // Weak randomization
$value = mt_rand(1, 100);       // Better, but not cryptographic

// PHP 8.3: Using Random class
$random = new Random\Randomizer();
$value = $random->nextInt(0, 100);  // Modern, secure
```

### Key Features

```php
<?php
// Random class features:
// ✓ Multiple randomization engines
// ✓ Cryptographic security options
// ✓ Better performance
// ✓ Modern API design
// ✓ Type-safe methods

$random = new Random\Randomizer();

// Generate various types of random values
$int = $random->nextInt(100);              // Random int
$bytes = $random->getBytes(16);            // Random bytes
$float = $random->getFloat(0.0, 1.0);     // Random float
```

---

## Random\Randomizer Class

### Basic Usage

```php
<?php
// Create randomizer with default engine
$random = new Random\Randomizer();

// Generate random integers
echo $random->nextInt(100);              // 0-99
echo $random->nextInt(1, 100);           // 1-100
echo $random->nextInt(-50, 50);          // -50 to 50

// Generate random bytes (cryptographic)
$bytes = $random->getBytes(16);
echo bin2hex($bytes);                    // 32 hex characters

// Generate random floats
echo $random->getFloat(0.0, 1.0);       // 0.0-1.0
echo $random->getFloat(0.0, 100.0);     // 0.0-100.0

// Shuffle arrays
$array = [1, 2, 3, 4, 5];
$random->shuffleArray($array);
print_r($array);                         // Randomized order
```

### Range Selection

```php
<?php
$random = new Random\Randomizer();

// Integer ranges
echo $random->nextInt();                // Random int (min to max)
echo $random->nextInt(10);              // 0-9
echo $random->nextInt(1, 10);           // 1-10
echo $random->nextInt(-10, 10);         // -10 to 10

// Float ranges
echo $random->getFloat(0, 1);           // 0.0-1.0
echo $random->getFloat(10, 20);         // 10.0-20.0
echo $random->getFloat(-1, 1);          // -1.0 to 1.0

// Inclusive ranges
echo $random->nextInt(1, 100);          // Both boundaries included
```

---

## Randomizer Methods

### Integer Generation

```php
<?php
$random = new Random\Randomizer();

// nextInt() - Generate random integers
$value = $random->nextInt();             // Full range
$value = $random->nextInt(99);           // 0-99
$value = $random->nextInt(1, 100);       // 1-100

// Practical examples
$diceRoll = $random->nextInt(1, 6);     // 1-6
$coinFlip = $random->nextInt(0, 1);     // 0 or 1
$percentage = $random->nextInt(0, 100); // 0-100
```

### Float Generation

```php
<?php
$random = new Random\Randomizer();

// getFloat() - Generate random floats
$value = $random->getFloat(0.0, 1.0);   // 0.0-1.0
$value = $random->getFloat(0.0, 100.0); // 0.0-100.0
$value = $random->getFloat(-1.0, 1.0);  // -1.0-1.0

// Practical examples
$probability = $random->getFloat(0.0, 1.0);  // 0.0-1.0
$percentage = $random->getFloat(0.0, 100.0); // 0.0-100.0
$temperature = $random->getFloat(-40.0, 50.0); // -40°C to 50°C
```

### Bytes Generation

```php
<?php
$random = new Random\Randomizer();

// getBytes() - Generate random bytes (cryptographic)
$bytes = $random->getBytes(16);         // 16 random bytes
$bytes = $random->getBytes(32);         // 32 random bytes

// Convert to hex for display
$hex = bin2hex($random->getBytes(16));  // 32 hex characters

// Practical examples
$token = bin2hex($random->getBytes(32)); // 64-char token
$uuid = bin2hex($random->getBytes(16));  // UUID-like string
$nonce = bin2hex($random->getBytes(12)); // CSRF token

// Use in security
$sessionToken = bin2hex($random->getBytes(32));
$apiKey = bin2hex($random->getBytes(32));
```

### Array Shuffling

```php
<?php
$random = new Random\Randomizer();

// shuffleArray() - Shuffle array in place
$array = ['a', 'b', 'c', 'd', 'e'];
$random->shuffleArray($array);
print_r($array);  // Randomized order

// Multiple shuffles
$deck = range(1, 52);  // Card deck
$random->shuffleArray($deck);
print_r($deck);

// Shuffle and keep keys
$data = ['a' => 1, 'b' => 2, 'c' => 3];
$array = array_values($data);  // Convert to indexed
$random->shuffleArray($array);
$shuffled = array_combine(array_keys($data), $array);
```

---

## Randomizer Engines

### Engine Selection

```php
<?php
// Create with specific engine
$random = new Random\Randomizer(new Random\Engine\Secure());

// Available engines:
// 1. Secure (default, cryptographic, slower)
// 2. Mt19937 (Mersenne Twister, faster, not cryptographic)
// 3. PcgOneseq128XslRr64 (PCG algorithm)

// Secure engine (recommended for security)
$secureRandom = new Random\Randomizer(
    new Random\Engine\Secure()
);
$token = bin2hex($secureRandom->getBytes(32));

// Mt19937 engine (faster, for non-security purposes)
$fastRandom = new Random\Randomizer(
    new Random\Engine\Mt19937((int)microtime(true))
);
$shuffled = [];
$fastRandom->shuffleArray($shuffled);

// Choose based on use case
// Security-critical: Use Secure engine
// Performance-critical (non-security): Use Mt19937 or PCG
```

### Engine Characteristics

```
Engine               │ Secure? │ Fast?  │ Use Case
─────────────────────┼─────────┼────────┼──────────────────────
Secure()             │ ✓✓✓     │ ✓      │ Tokens, crypto, security
Mt19937()            │ ✓       │ ✓✓✓    │ Games, simulations
PcgOneseq128XslRr64  │ ✓✓      │ ✓✓     │ General purpose
```

---

## Cryptographic Security

### Secure Random Generation

```php
<?php
// For security-sensitive operations
$secureRandom = new Random\Randomizer();

// Generate cryptographically secure tokens
$sessionToken = bin2hex($secureRandom->getBytes(32));    // 64 chars
$csrfToken = bin2hex($secureRandom->getBytes(16));       // 32 chars
$apiKey = bin2hex($secureRandom->getBytes(32));          // 64 chars

// Generate secure passwords
function generatePassword(int $length = 16): string
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $random = new Random\Randomizer();
    $password = '';

    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[$random->nextInt(0, strlen($characters) - 1)];
    }

    return $password;
}

$pwd = generatePassword();
```

### Best Practices

```php
<?php
// ✓ DO: Use Secure engine for sensitive data
$random = new Random\Randomizer(new Random\Engine\Secure());
$token = bin2hex($random->getBytes(32));

// ✓ DO: Use enough bytes (32 bytes = 256 bits)
$token = bin2hex($random->getBytes(32));  // Secure

// ✓ DO: Use for sessions, tokens, security keys
$sessionId = bin2hex($random->getBytes(32));

// ❌ DON'T: Use default Random() for security without thinking
// (Default is usually Secure, but be explicit)

// ❌ DON'T: Use insufficient bytes
// $token = bin2hex($random->getBytes(4));  // Too short!

// ❌ DON'T: Mix engines without understanding
$weakRandom = new Random\Randomizer(new Random\Engine\Mt19937());
// $token = bin2hex($weakRandom->getBytes(16));  // Not secure!
```

---

## Performance Improvements

### Comparison with Old Methods

```php
<?php
// Old method: mt_rand()
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $value = mt_rand(1, 100);
}
$oldTime = (microtime(true) - $start) * 1000;

// New method: Random class (non-secure)
$fastRandom = new Random\Randomizer(new Random\Engine\Mt19937());
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $value = $fastRandom->nextInt(1, 100);
}
$newTime = (microtime(true) - $start) * 1000;

echo "Old mt_rand(): {$oldTime}ms\n";
echo "New Random class: {$newTime}ms\n";
echo "Performance: " . round(($oldTime / $newTime), 2) . "x\n";

// Result: Random class often faster or comparable
```

---

## Practical Examples

### Token Generation

```php
<?php
class TokenGenerator
{
    private Random\Randomizer $random;

    public function __construct()
    {
        $this->random = new Random\Randomizer();
    }

    public function generateSessionToken(): string
    {
        return bin2hex($this->random->getBytes(32));
    }

    public function generateCsrfToken(): string
    {
        return bin2hex($this->random->getBytes(16));
    }

    public function generateApiKey(): string
    {
        return 'api_' . bin2hex($this->random->getBytes(24));
    }

    public function generateVerificationCode(): string
    {
        return str_pad((string)$this->random->nextInt(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

// Usage
$generator = new TokenGenerator();
$sessionToken = $generator->generateSessionToken();
$verificationCode = $generator->generateVerificationCode();
```

### Game/Simulation

```php
<?php
class GameRandomizer
{
    private Random\Randomizer $random;

    public function __construct()
    {
        // Use fast, non-secure engine for games
        $this->random = new Random\Randomizer(
            new Random\Engine\Mt19937((int)microtime(true))
        );
    }

    public function rollDice(int $sides = 6): int
    {
        return $this->random->nextInt(1, $sides);
    }

    public function dealCard(): int
    {
        return $this->random->nextInt(1, 52);
    }

    public function getRandomProbability(): float
    {
        return $this->random->getFloat(0.0, 1.0);
    }

    public function shuffleDeck(array &$deck): void
    {
        $this->random->shuffleArray($deck);
    }

    public function selectRandomWinner(array $players): string
    {
        $index = $this->random->nextInt(0, count($players) - 1);
        return $players[$index];
    }
}

// Usage
$game = new GameRandomizer();
$roll = $game->rollDice();
$probability = $game->getRandomProbability();
$winner = $game->selectRandomWinner(['Alice', 'Bob', 'Carol']);
```

---

## Complete Example

### Secure Authentication System

```php
<?php
declare(strict_types=1);

namespace App\Security;

class SecureTokenManager
{
    private Random\Randomizer $random;
    private array $tokens = [];

    public function __construct()
    {
        $this->random = new Random\Randomizer(
            new Random\Engine\Secure()
        );
    }

    public function generateSessionToken(): string
    {
        return bin2hex($this->random->getBytes(32));
    }

    public function generateCsrfToken(): string
    {
        return bin2hex($this->random->getBytes(16));
    }

    public function generateRefreshToken(): string
    {
        return bin2hex($this->random->getBytes(32));
    }

    public function generateOtpCode(): string
    {
        // 6-digit OTP
        $code = $this->random->nextInt(0, 999999);
        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }

    public function generateApiKey(string $clientId): string
    {
        $timestamp = time();
        $randomPart = bin2hex($this->random->getBytes(16));
        return "{$clientId}_" . $timestamp . "_" . $randomPart;
    }

    public function validateToken(string $token, string $type): bool
    {
        // Validate stored token
        return isset($this->tokens[$type][$token]);
    }

    public function storeToken(string $token, string $type, int $expirySeconds = 3600): void
    {
        $this->tokens[$type][$token] = time() + $expirySeconds;
    }
}

// Usage in authentication
class AuthenticationService
{
    private SecureTokenManager $tokenManager;

    public function __construct(SecureTokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function login(string $username, string $password): array
    {
        // Validate credentials
        if (!$this->validateCredentials($username, $password)) {
            throw new Exception("Invalid credentials");
        }

        // Generate tokens
        $sessionToken = $this->tokenManager->generateSessionToken();
        $csrfToken = $this->tokenManager->generateCsrfToken();

        // Store tokens
        $this->tokenManager->storeToken($sessionToken, 'session', 3600);
        $this->tokenManager->storeToken($csrfToken, 'csrf', 3600);

        return [
            'session_token' => $sessionToken,
            'csrf_token' => $csrfToken,
            'expires_in' => 3600,
        ];
    }

    private function validateCredentials(string $username, string $password): bool
    {
        // Validate credentials
        return true;
    }
}
```

---

## See Also

- [PHP 8.3 Overview](0-php8.3-overview.md)
- [JSON Validation](4-json-validation.md)
- [Array Functions](7-array-functions.md)
