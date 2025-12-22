use Psr\Link\LinkInterface;
use Psr\Link\EvolvableLinkInterface;
use Psr\Link\LinkProviderInterface;
use Psr\Link\EvolvableLinkProviderInterface;

<?php

/**
 * PSR-13: Hypermedia Links
 * 
 * PSR-13 defines common interfaces for representing hypermedia links as described
 * in various formats like HAL, JSON-LD, Atom, HTML, and others.
 * 
 * Key Concepts:
 * -------------
 * 1. LinkInterface - Represents a single hypermedia link
 * 2. EvolvableLinkInterface - Immutable link that can be evolved
 * 3. LinkProviderInterface - Object that contains hypermedia links
 * 4. EvolvableLinkProviderInterface - Immutable link provider
 * 
 * Benefits:
 * ---------
 * - Standardizes hypermedia link representation across frameworks
 * - Enables interoperability between different hypermedia formats
 * - Supports REST HATEOAS (Hypermedia as the Engine of Application State)
 * - Provides immutable link objects for better predictability
 */


// ============================================================================
// Example 1: LinkInterface - Basic Hypermedia Link
// ============================================================================

/**
 * LinkInterface represents a readable hypermedia link
 * 
 * Methods:
 * - getHref(): Returns the target URI
 * - isTemplated(): Indicates if the href is a URI template (RFC 6570)
 * - getRels(): Returns relationship types (e.g., 'self', 'next', 'prev')
 * - getAttributes(): Returns attributes like 'hreflang', 'title', 'type'
 */

class SimpleLink implements LinkInterface
{
    private string $href;
    private array $rels;
    private array $attributes;
    private bool $templated;

    public function __construct(string $href, array $rels = [], array $attributes = [], bool $templated = false)
    {
        $this->href = $href;
        $this->rels = $rels;
        $this->attributes = $attributes;
        $this->templated = $templated;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function isTemplated(): bool
    {
        return $this->templated;
    }

    public function getRels(): array
    {
        return $this->rels;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}

// Usage Example
$selfLink = new SimpleLink('/api/users/123', ['self'], ['type' => 'application/json']);
echo "Link href: " . $selfLink->getHref() . "\n";
echo "Link rels: " . implode(', ', $selfLink->getRels()) . "\n";

// ============================================================================
// Example 2: EvolvableLinkInterface - Immutable Link Evolution
// ============================================================================

/**
 * EvolvableLinkInterface extends LinkInterface with methods to create
 * modified copies of links (immutable pattern)
 * 
 * Methods:
 * - withHref(string $href): Returns new instance with updated href
 * - withRel(string $rel): Returns new instance with added relationship
 * - withoutRel(string $rel): Returns new instance without specified relationship
 * - withAttribute(string $attribute, $value): Returns new instance with attribute
 * - withoutAttribute(string $attribute): Returns new instance without attribute
 */

class EvolvableLink extends SimpleLink implements EvolvableLinkInterface
{
    public function withHref(string $href): EvolvableLinkInterface
    {
        $new = clone $this;
        $new->href = $href;
        return $new;
    }

    public function withRel(string $rel): EvolvableLinkInterface
    {
        $new = clone $this;
        if (!in_array($rel, $new->rels)) {
            $new->rels[] = $rel;
        }
        return $new;
    }

    public function withoutRel(string $rel): EvolvableLinkInterface
    {
        $new = clone $this;
        $new->rels = array_values(array_filter($new->rels, fn($r) => $r !== $rel));
        return $new;
    }

    public function withAttribute(string $attribute, $value): EvolvableLinkInterface
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;
        return $new;
    }

    public function withoutAttribute(string $attribute): EvolvableLinkInterface
    {
        $new = clone $this;
        unset($new->attributes[$attribute]);
        return $new;
    }
}

// Usage Example
$link = new EvolvableLink('/api/users/123', ['self']);
$modifiedLink = $link
    ->withRel('canonical')
    ->withAttribute('title', 'User Profile')
    ->withAttribute('type', 'application/hal+json');

echo "\nOriginal rels: " . implode(', ', $link->getRels()) . "\n";
echo "Modified rels: " . implode(', ', $modifiedLink->getRels()) . "\n";

// ============================================================================
// Example 3: LinkProviderInterface - Object with Multiple Links
// ============================================================================

/**
 * LinkProviderInterface represents an object that contains hypermedia links
 * 
 * Methods:
 * - getLinks(): Returns all links
 * - getLinksByRel(string $rel): Returns links with specific relationship
 */

class SimpleLinkProvider implements LinkProviderInterface
{
    private array $links = [];

    public function addLink(LinkInterface $link): void
    {
        $this->links[] = $link;
    }

    public function getLinks(): iterable
    {
        return $this->links;
    }

    public function getLinksByRel(string $rel): iterable
    {
        return array_filter($this->links, function(LinkInterface $link) use ($rel) {
            return in_array($rel, $link->getRels());
        });
    }
}

// Usage Example
$provider = new SimpleLinkProvider();
$provider->addLink(new SimpleLink('/api/users/123', ['self']));
$provider->addLink(new SimpleLink('/api/users', ['collection']));
$provider->addLink(new SimpleLink('/api/users/124', ['next']));

echo "\nAll links:\n";
foreach ($provider->getLinks() as $link) {
    echo "  - " . $link->getHref() . " [" . implode(', ', $link->getRels()) . "]\n";
}

echo "\nLinks with 'self' relation:\n";
foreach ($provider->getLinksByRel('self') as $link) {
    echo "  - " . $link->getHref() . "\n";
}

// ============================================================================
// Example 4: EvolvableLinkProviderInterface - Immutable Link Provider
// ============================================================================

/**
 * EvolvableLinkProviderInterface extends LinkProviderInterface with immutable methods
 * 
 * Methods:
 * - withLink(LinkInterface $link): Returns new instance with added link
 * - withoutLink(LinkInterface $link): Returns new instance without specified link
 */

class EvolvableLinkProvider extends SimpleLinkProvider implements EvolvableLinkProviderInterface
{
    public function withLink(LinkInterface $link): Ev