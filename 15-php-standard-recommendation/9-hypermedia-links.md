# PSR-13: Hypermedia Links

## Overview

Learn about PSR-13, the standardized way to represent HTTP links in PHP applications, enabling HATEOAS (Hypermedia As The Engine Of Application State) and RESTful API design.

---

## Table of Contents

1. What is PSR-13
2. Core Concepts
3. Link Objects
4. Link Providers
5. Implementation
6. HATEOAS Patterns
7. Real-world Examples
8. Complete Examples

---

## What is PSR-13

### Purpose

```php
<?php
// Before PSR-13: Ad-hoc link generation

// Manually creating links in responses
$response = [
    'user' => [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ],
    '_links' => [
        'self' => ['href' => '/users/1'],
        'all' => ['href' => '/users'],
        'edit' => ['href' => '/users/1/edit'],
        'delete' => ['href' => '/users/1', 'method' => 'DELETE'],
    ],
];

// Problems:
// - No standard format
// - Manual link construction
// - Inconsistent structure
// - Hard to maintain

// Solution: PSR-13 (standardized hypermedia links)

use Psr\Link\LinkInterface;

$link = new Link('self', '/users/1');
$link2 = new Link('next', '/users?page=2');

// Standard interface across all implementations

// Benefits:
// ✓ Standard link representation
// ✓ Consistent API
// ✓ HATEOAS support
// ✓ Interoperable
```

### Key Interfaces

```php
<?php
// LinkInterface - represents a single link
// - getRels() / withRel()
// - getHref()
// - getAttributes() / withAttribute()
// - isTemplated()

// LinkProviderInterface - manages links
// - getLinks() / withLink() / withoutLink()
// - getLinksByRel()
```

---

## Core Concepts

### Links in REST APIs

```php
<?php
// HATEOAS principle: API responses include links to related resources

// User resource with links
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "_links": {
    "self": {
      "href": "/users/1"
    },
    "all": {
      "href": "/users"
    },
    "articles": {
      "href": "/users/1/articles"
    },
    "profile": {
      "href": "/users/1/profile"
    }
  }
}

// This allows clients to navigate the API without hardcoding URLs!
```

### Relation Types

```php
// Standard link relation types (from RFC 5988):

// Navigation
self        // Link to the resource itself
next        // Link to the next resource in a series
prev        // Link to the previous resource
first       // Link to the first resource
last        // Link to the last resource

// Resource relationships
related     // Link to a related resource
up          // Link to a parent resource

// Actions
create      // Link to create a new resource
edit        // Link to edit the resource
delete      // Link to delete the resource

// Collections
item        // Link to an item in a collection
collection  // Link to a collection

// Content negotiation
alternate   // Link to an alternate representation

// Custom relations
acme:reports, vendor:documentation, etc.
```

---

## Link Objects

### LinkInterface

```php
<?php
use Psr\Link\LinkInterface;

interface LinkInterface extends EvolvableInterface
{
    /**
     * Returns the target URI of this link
     */
    public function getHref(): string;

    /**
     * Returns whether this link is a URI template or not
     */
    public function isTemplated(): bool;

    /**
     * Returns the relation type(s) for this link
     */
    public function getRels(): array;

    /**
     * Returns the specific attribute for this link
     */
    public function getAttributes(): array;
}
```

### Creating Links

```php
<?php
use Psr\Link\Link;

// Simple link
$link = new Link('self', '/users/1');

// Link with multiple relations
$link = new Link(['alternate', 'canonical'], '/users/1');

// Link with attributes
$link = (new Link('edit', '/users/1/edit'))
    ->withAttribute('method', 'PUT')
    ->withAttribute('title', 'Edit User');

// URI template (RFC 6570)
$link = new Link('search', '/users{?page,limit}');

// Multiple attributes
$link = (new Link('item', '/users/1'))
    ->withAttribute('type', 'application/json')
    ->withAttribute('title', 'User Profile')
    ->withAttribute('hreflang', 'en');
```

### Link Attributes

```php
<?php
// Standard attributes (from RFC 5988):

'href'       // The URI (already handled separately)
'rel'        // Relation type (handled by getRels())
'rev'        // Reverse relation (less common)
'type'       // Media type (application/json)
'hreflang'   // Language of linked resource
'title'      // Human-readable title
'title*'     // Encoded title with language
'media'      // Media query (for responsive links)

// Custom attributes
'method'     // HTTP method (GET, POST, PUT, DELETE)
'deprecated' // Mark link as deprecated
'templated'  // Set to true for URI templates
```

---

## Link Providers

### LinkProviderInterface

```php
<?php
use Psr\Link\LinkProviderInterface;
use Psr\Link\LinkInterface;

interface LinkProviderInterface extends EvolvableInterface
{
    /**
     * Returns all available links
     */
    public function getLinks(): iterable;

    /**
     * Returns the links having a specific relation
     */
    public function getLinksByRel(string $rel): iterable;

    /**
     * Return an instance with an additional link
     */
    public function withLink(LinkInterface $link): static;

    /**
     * Return an instance without a link with a specific relation
     */
    public function withoutLink(LinkInterface $link): static;
}
```

### Using Link Providers

```php
<?php
use Psr\Link\LinkProvider;

// Create provider
$provider = new LinkProvider();

// Add links
$provider = $provider
    ->withLink(new Link('self', '/users/1'))
    ->withLink(new Link('edit', '/users/1/edit'))
    ->withLink(new Link('delete', '/users/1', ['method' => 'DELETE']))
    ->withLink(new Link('next', '/users/2'));

// Get all links
foreach ($provider->getLinks() as $link) {
    echo $link->getHref();
}

// Get links by relation
$selfLinks = $provider->getLinksByRel('self');
foreach ($selfLinks as $link) {
    echo $link->getHref();
}

// Remove link
$provider = $provider->withoutLink(new Link('delete', '/users/1'));
```

---

## Implementation

### Custom Link Class

```php
<?php
declare(strict_types=1);

use Psr\Link\LinkInterface;

class Link implements LinkInterface
{
    private string $href;
    private array $rels;
    private array $attributes = [];
    private bool $templated = false;

    public function __construct(
        string|array $rels,
        string $href,
        array $attributes = []
    ) {
        $this->href = $href;
        $this->rels = is_array($rels) ? $rels : [$rels];
        $this->attributes = $attributes;

        // Check if URI is templated (contains {})
        $this->templated = str_contains($href, '{') && str_contains($href, '}');
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

    public function withRel(string $rel): static
    {
        $new = clone $this;
        $new->rels[] = $rel;
        return $new;
    }

    public function withAttribute(string $name, string $value): static
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute(string $name): static
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }
}

class LinkProvider implements \Psr\Link\LinkProviderInterface
{
    private array $links = [];

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

    public function withLink(LinkInterface $link): static
    {
        $new = clone $this;
        $new->links[] = $link;
        return $new;
    }

    public function withoutLink(LinkInterface $link): static
    {
        $new = clone $this;
        $new->links = array_filter($new->links, function(LinkInterface $existing) use ($link) {
            return $existing->getHref() !== $link->getHref();
        });
        return $new;
    }
}
```

---

## HATEOAS Patterns

### Hypermedia Response

```php
<?php
class UserApi
{
    public function getUserResponse(int $id, LinkProviderInterface $provider): array
    {
        $user = $this->getUser($id);

        // Add links
        $provider = $provider
            ->withLink(new Link('self', "/users/{$id}"))
            ->withLink(new Link('all', "/users"))
            ->withLink(new Link('edit', "/users/{$id}", ['method' => 'PUT']))
            ->withLink(new Link('delete', "/users/{$id}", ['method' => 'DELETE']))
            ->withLink(new Link('articles', "/users/{$id}/articles"));

        return [
            'user' => $user,
            '_links' => $this->linksToArray($provider->getLinks()),
        ];
    }

    public function getListResponse(int $page = 1, LinkProviderInterface $provider): array
    {
        $users = $this->getUsers($page);
        $total = $this->getTotalPages();

        // Add navigation links
        $provider = $provider
            ->withLink(new Link('self', "/users?page={$page}"))
            ->withLink(new Link('first', "/users?page=1"))
            ->withLink(new Link('last', "/users?page={$total}"));

        if ($page > 1) {
            $provider = $provider
                ->withLink(new Link('prev', "/users?page=" . ($page - 1)));
        }

        if ($page < $total) {
            $provider = $provider
                ->withLink(new Link('next', "/users?page=" . ($page + 1)));
        }

        return [
            'users' => $users,
            'page' => $page,
            'total' => $total,
            '_links' => $this->linksToArray($provider->getLinks()),
        ];
    }

    private function linksToArray(iterable $links): array
    {
        $array = [];

        foreach ($links as $link) {
            foreach ($link->getRels() as $rel) {
                $array[$rel] = [
                    'href' => $link->getHref(),
                    'templated' => $link->isTemplated(),
                    ...array_filter($link->getAttributes()),
                ];
            }
        }

        return $array;
    }
}
```

---

## Real-world Examples

### REST API with Links

```php
<?php
class ArticleApi
{
    private PDO $pdo;
    private LinkProvider $linkProvider;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->linkProvider = new LinkProvider();
    }

    public function getArticle(int $id): array
    {
        $article = $this->fetchArticle($id);

        if (!$article) {
            return ['error' => 'Not found'];
        }

        $links = (new LinkProvider())
            ->withLink(new Link('self', "/articles/{$id}"))
            ->withLink(new Link('all', "/articles"))
            ->withLink(new Link('author', "/users/{$article['author_id']}"))
            ->withLink(new Link('edit', "/articles/{$id}", ['method' => 'PUT']))
            ->withLink(new Link('delete', "/articles/{$id}", ['method' => 'DELETE']))
            ->withLink(new Link('comments', "/articles/{$id}/comments"));

        return [
            'article' => $article,
            '_links' => $this->formatLinks($links),
        ];
    }

    public function searchArticles(string $query, int $page = 1): array
    {
        $articles = $this->search($query, $page);
        $total = $this->getSearchTotal($query);

        $links = (new LinkProvider())
            ->withLink(new Link('self', "/articles/search?q={$query}&page={$page}"))
            ->withLink(new Link('first', "/articles/search?q={$query}&page=1"))
            ->withLink(new Link('last', "/articles/search?q={$query}&page={$total}"));

        if ($page > 1) {
            $links = $links->withLink(new Link('prev', "/articles/search?q={$query}&page=" . ($page - 1)));
        }

        if ($page < $total) {
            $links = $links->withLink(new Link('next', "/articles/search?q={$query}&page=" . ($page + 1)));
        }

        return [
            'articles' => $articles,
            'total' => $total,
            'page' => $page,
            '_links' => $this->formatLinks($links),
        ];
    }

    private function formatLinks(LinkProvider $provider): array
    {
        $formatted = [];

        foreach ($provider->getLinks() as $link) {
            $rels = $link->getRels();

            foreach ($rels as $rel) {
                $formatted[$rel] = [
                    'href' => $link->getHref(),
                    ...array_filter($link->getAttributes()),
                ];
            }
        }

        return $formatted;
    }

    private function fetchArticle(int $id): ?array
    {
        // Fetch from database
        return ['id' => $id, 'title' => 'Article', 'author_id' => 1];
    }

    private function search(string $query, int $page): array
    {
        // Search logic
        return [];
    }

    private function getSearchTotal(string $query): int
    {
        // Total pages
        return 1;
    }

    private function getTotal(string $query): int
    {
        return 1;
    }
}
```

---

## Complete Examples

### Full HATEOAS API

```php
<?php
declare(strict_types=1);

namespace App\Api;

use Psr\Link\Link;
use Psr\Link\LinkProvider;

class UserApiResponse
{
    public static function single(array $user): array
    {
        return [
            'data' => $user,
            '_links' => self::userLinks($user['id']),
        ];
    }

    public static function collection(array $users, int $page = 1, int $total = 1): array
    {
        return [
            'data' => $users,
            'pagination' => [
                'page' => $page,
                'total' => $total,
            ],
            '_links' => self::paginationLinks('/users', $page, $total),
        ];
    }

    private static function userLinks(int $id): array
    {
        $provider = (new LinkProvider())
            ->withLink(new Link('self', "/users/{$id}"))
            ->withLink(new Link('all', "/users"))
            ->withLink(new Link('edit', "/users/{$id}", ['method' => 'PUT']))
            ->withLink(new Link('delete', "/users/{$id}", ['method' => 'DELETE']))
            ->withLink(new Link('articles', "/users/{$id}/articles"));

        return self::formatLinks($provider);
    }

    private static function paginationLinks(
        string $path,
        int $page,
        int $total
    ): array {
        $provider = (new LinkProvider())
            ->withLink(new Link('self', "{$path}?page={$page}"))
            ->withLink(new Link('first', "{$path}?page=1"))
            ->withLink(new Link('last', "{$path}?page={$total}"));

        if ($page > 1) {
            $provider = $provider->withLink(
                new Link('prev', "{$path}?page=" . ($page - 1))
            );
        }

        if ($page < $total) {
            $provider = $provider->withLink(
                new Link('next', "{$path}?page=" . ($page + 1))
            );
        }

        return self::formatLinks($provider);
    }

    private static function formatLinks(LinkProvider $provider): array
    {
        $formatted = [];

        foreach ($provider->getLinks() as $link) {
            foreach ($link->getRels() as $rel) {
                $formatted[$rel] = [
                    'href' => $link->getHref(),
                    ...array_filter($link->getAttributes()),
                ];
            }
        }

        return $formatted;
    }
}
```

---

## Key Takeaways

**PSR-13 Hypermedia Links Checklist:**

1. ✅ Use standard link relations (self, next, prev, edit, etc.)
2. ✅ Include links in API responses
3. ✅ Use LinkInterface for consistency
4. ✅ Support URI templates for dynamic links
5. ✅ Add meaningful attributes (method, title, etc.)
6. ✅ Implement HATEOAS principle
7. ✅ Test link generation
8. ✅ Document available link relations

---

## See Also

- [PSR Overview](0-psr-overview.md)
- [HTTP Message Interface (PSR-7)](6-http-message-interface.md)
- [HTTP Client (PSR-18)](14-http-client.md)
