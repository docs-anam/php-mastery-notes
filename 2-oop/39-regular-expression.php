<?php
/**
 * Summary: Regular Expressions in PHP OOP
 *
 * Regular expressions (regex) are patterns used to match character combinations in strings.
 * In PHP, regex is commonly used for validation, searching, and replacing text.
 * PHP supports two main regex engines: POSIX and PCRE (Perl Compatible Regular Expressions).
 * The PCRE extension is more powerful and widely used.
 *
 * OOP Approach:
 * PHP does not have a built-in Regex class, but you can encapsulate regex logic in your own classes.
 * This improves code reusability, readability, and maintainability.
 *
 * Example OOP Regex Class:
 */

class RegexHelper
{
    private string $pattern;
    private array $matches = [];

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    // Check if pattern matches the subject
    public function matches(string $subject): bool
    {
        return preg_match($this->pattern, $subject) === 1;
    }

    // Get all matches in the subject
    public function getAllMatches(string $subject): array
    {
        preg_match_all($this->pattern, $subject, $this->matches);
        return $this->matches[0] ?? [];
    }

    // Replace pattern in subject with replacement
    public function replace(string $subject, string $replacement): string
    {
        return preg_replace($this->pattern, $replacement, $subject);
    }
}

// Usage Example:
$emailRegex = new RegexHelper('/^[\w\.-]+@[\w\.-]+\.\w+$/');
$email = "user@example.com";

if ($emailRegex->matches($email)) {
    echo "Valid email!";
} else {
    echo "Invalid email!";
}

/**
 * Key Points:
 * - Use preg_match(), preg_match_all(), preg_replace() for regex operations.
 * - Always delimit your pattern (e.g., '/pattern/').
 * - Escape special characters as needed.
 * - OOP encapsulation allows for reusable and testable regex logic.
 */
?>